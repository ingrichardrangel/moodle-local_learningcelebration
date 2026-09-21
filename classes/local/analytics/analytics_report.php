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
 * Immutable analytics result for the current celebration decision.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class analytics_report {
    /** @var period_statistics Most recently completed learning period. */
    private period_statistics $currentperiod;

    /** @var period_statistics Previous completed learning period. */
    private period_statistics $previousperiod;

    /** @var int Current active course enrolments. */
    private int $activeenrolments;

    /** @var int Account age in calendar days. */
    private int $accountagedays;

    /** @var string Current period richness. */
    private string $currentrichness;

    /** @var string Previous period richness. */
    private string $previousrichness;

    /** @var string Suggested future experience mode. */
    private string $experience;

    /**
     * Constructor.
     *
     * @param period_statistics $currentperiod Current completed period.
     * @param period_statistics $previousperiod Previous completed period.
     * @param int $activeenrolments Current active enrolments.
     * @param int $accountagedays Account age in days.
     * @param string $currentrichness Current richness.
     * @param string $previousrichness Previous richness.
     * @param string $experience Suggested experience.
     */
    public function __construct(
        period_statistics $currentperiod,
        period_statistics $previousperiod,
        int $activeenrolments,
        int $accountagedays,
        string $currentrichness,
        string $previousrichness,
        string $experience
    ) {
        $this->currentperiod = $currentperiod;
        $this->previousperiod = $previousperiod;
        $this->activeenrolments = $activeenrolments;
        $this->accountagedays = $accountagedays;
        $this->currentrichness = $currentrichness;
        $this->previousrichness = $previousrichness;
        $this->experience = $experience;
    }

    /** @return period_statistics Current completed period. */
    public function get_current_period(): period_statistics {
        return $this->currentperiod;
    }

    /** @return period_statistics Previous completed period. */
    public function get_previous_period(): period_statistics {
        return $this->previousperiod;
    }

    /** @return int Current active enrolments. */
    public function get_active_enrolments(): int {
        return $this->activeenrolments;
    }

    /** @return int Account age in calendar days. */
    public function get_account_age_days(): int {
        return $this->accountagedays;
    }

    /** @return string Current richness. */
    public function get_current_richness(): string {
        return $this->currentrichness;
    }

    /** @return string Previous richness. */
    public function get_previous_richness(): string {
        return $this->previousrichness;
    }

    /** @return string Suggested experience mode. */
    public function get_experience(): string {
        return $this->experience;
    }

    /** @return bool Whether a year-over-year comparison is sufficiently supported. */
    public function is_comparison_available(): bool {
        return $this->experience === data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON;
    }
}
