<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_learningcelebration\local\analytics;

use local_learningcelebration\local\birthday\evaluation;

/**
 * Coordinates Moodle core learning data into an annual celebration analytics report.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class analytics_engine {
    /** @var analytics_repository Core-data repository. */
    private analytics_repository $repository;

    /** @var data_richness_classifier Data sufficiency classifier. */
    private data_richness_classifier $classifier;

    /** @var metric_policy Administrator-selected metric policy. */
    private metric_policy $policy;

    /**
     * Constructor.
     *
     * @param analytics_repository|null $repository Optional repository for testing.
     * @param data_richness_classifier|null $classifier Optional classifier for testing.
     * @param metric_policy|null $policy Optional metric policy for testing.
     */
    public function __construct(
        ?analytics_repository $repository = null,
        ?data_richness_classifier $classifier = null,
        ?metric_policy $policy = null
    ) {
        $this->repository = $repository ?? new analytics_repository();
        $this->classifier = $classifier ?? new data_richness_classifier();
        $this->policy = $policy ?? metric_policy::from_config();
    }

    /**
     * Build a report using the periods calculated by the birthday engine.
     *
     * @param int $userid User id.
     * @param int $accountcreated User account creation timestamp.
     * @param evaluation $birthdayevaluation Birthday-engine result.
     * @param \DateTimeZone $timezone User timezone.
     * @param int|null $referencetimestamp Instant used to evaluate active enrolments.
     * @return analytics_report
     */
    public function build_report(
        int $userid,
        int $accountcreated,
        evaluation $birthdayevaluation,
        \DateTimeZone $timezone,
        ?int $referencetimestamp = null
    ): analytics_report {
        $current = $this->policy->filter_period($this->repository->get_period_statistics(
            $userid,
            $birthdayevaluation->get_period_start()->getTimestamp(),
            $birthdayevaluation->get_period_end_exclusive()->getTimestamp()
        ));

        $previous = $this->policy->filter_period($this->repository->get_period_statistics(
            $userid,
            $birthdayevaluation->get_previous_period_start()->getTimestamp(),
            $birthdayevaluation->get_previous_period_end_exclusive()->getTimestamp()
        ));

        $referencetimestamp = $referencetimestamp ?? time();
        $activeenrolments = $this->policy->filter_active_enrolments(
            $this->repository->count_active_course_enrolments($userid, $referencetimestamp)
        );
        $accountagedays = $this->calculate_account_age_days(
            $accountcreated,
            $birthdayevaluation->get_today(),
            $timezone
        );

        $currentrichness = $this->classifier->classify($current, $activeenrolments, $accountagedays);
        $previousrichness = $this->classifier->classify($previous);
        $experience = $this->classifier->select_experience(
            $currentrichness,
            $previousrichness,
            $this->policy->allows_comparison()
        );

        return new analytics_report(
            $current,
            $previous,
            $activeenrolments,
            $accountagedays,
            $currentrichness,
            $previousrichness,
            $experience
        );
    }

    /**
     * Calculate account age by local calendar dates instead of raw seconds.
     *
     * @param int $accountcreated Account creation timestamp.
     * @param \DateTimeImmutable $today Current local date.
     * @param \DateTimeZone $timezone User timezone.
     * @return int Whole calendar days, never negative.
     */
    private function calculate_account_age_days(
        int $accountcreated,
        \DateTimeImmutable $today,
        \DateTimeZone $timezone
    ): int {
        if ($accountcreated <= 0) {
            return PHP_INT_MAX;
        }

        $created = (new \DateTimeImmutable('@' . $accountcreated))->setTimezone($timezone)->setTime(0, 0, 0);
        $today = $today->setTime(0, 0, 0);

        if ($created > $today) {
            return 0;
        }

        return (int) $created->diff($today)->days;
    }
}
