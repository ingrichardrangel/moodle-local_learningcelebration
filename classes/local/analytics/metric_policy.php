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

/**
 * Controls which learning signals may be used by Learning Celebration.
 *
 * Disabled metrics are removed before data-richness classification and presentation.
 * This keeps the adaptive experience consistent with the administrator's choices.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class metric_policy {
    /** @var bool */
    private bool $completedcourses;
    /** @var bool */
    private bool $completedactivities;
    /** @var bool */
    private bool $badges;
    /** @var bool */
    private bool $grades;
    /** @var bool */
    private bool $activeenrolments;
    /** @var bool */
    private bool $comparison;

    /**
     * Constructor.
     *
     * @param bool $completedcourses Include completed courses.
     * @param bool $completedactivities Include completed activities.
     * @param bool $badges Include badges.
     * @param bool $grades Include grades and best-course highlights.
     * @param bool $activeenrolments Include active enrolments in welcome mode.
     * @param bool $comparison Allow year-over-year comparison.
     */
    public function __construct(
        bool $completedcourses = true,
        bool $completedactivities = true,
        bool $badges = true,
        bool $grades = true,
        bool $activeenrolments = true,
        bool $comparison = true
    ) {
        $this->completedcourses = $completedcourses;
        $this->completedactivities = $completedactivities;
        $this->badges = $badges;
        $this->grades = $grades;
        $this->activeenrolments = $activeenrolments;
        $this->comparison = $comparison;
    }

    /**
     * Build a policy from plugin settings. Missing settings preserve the v0.5 behaviour.
     *
     * @return self
     */
    public static function from_config(): self {
        $config = get_config('local_learningcelebration');

        return new self(
            !isset($config->showcompletedcourses) || (bool) $config->showcompletedcourses,
            !isset($config->showcompletedactivities) || (bool) $config->showcompletedactivities,
            !isset($config->showbadges) || (bool) $config->showbadges,
            !isset($config->showgrades) || (bool) $config->showgrades,
            !isset($config->showactiveenrolments) || (bool) $config->showactiveenrolments,
            !isset($config->showcomparison) || (bool) $config->showcomparison
        );
    }

    /**
     * Check whether completed courses are enabled.
     *
     * @return bool
     */
    public function includes_completed_courses(): bool {
        return $this->completedcourses;
    }

    /**
     * Check whether completed activities are enabled.
     *
     * @return bool
     */
    public function includes_completed_activities(): bool {
        return $this->completedactivities;
    }

    /**
     * Check whether badges are enabled.
     *
     * @return bool
     */
    public function includes_badges(): bool {
        return $this->badges;
    }

    /**
     * Check whether course grades are enabled.
     *
     * @return bool
     */
    public function includes_grades(): bool {
        return $this->grades;
    }

    /**
     * Check whether active enrolments are enabled.
     *
     * @return bool
     */
    public function includes_active_enrolments(): bool {
        return $this->activeenrolments;
    }

    /**
     * Check whether year-over-year comparison is enabled.
     *
     * @return bool
     */
    public function allows_comparison(): bool {
        return $this->comparison;
    }

    /**
     * Remove disabled metrics from a period result.
     *
     * @param period_statistics $statistics Unfiltered statistics.
     * @return period_statistics Filtered immutable statistics.
     */
    public function filter_period(period_statistics $statistics): period_statistics {
        return new period_statistics(
            $this->completedcourses ? $statistics->get_completed_courses() : 0,
            $this->completedactivities ? $statistics->get_completed_activities() : 0,
            $this->badges ? $statistics->get_earned_badges() : 0,
            $this->grades ? $statistics->get_graded_completed_courses() : 0,
            $this->grades ? $statistics->get_average_course_grade() : null,
            $this->grades ? $statistics->get_best_course_grade() : null,
            $this->grades ? $statistics->get_best_course_id() : null,
            $this->grades ? $statistics->get_best_course_name() : null
        );
    }

    /**
     * Filter the active-enrolment contextual signal.
     *
     * @param int $activeenrolments Raw active enrolment count.
     * @return int
     */
    public function filter_active_enrolments(int $activeenrolments): int {
        return $this->activeenrolments ? max(0, $activeenrolments) : 0;
    }
}
