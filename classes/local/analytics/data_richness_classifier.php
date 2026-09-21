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
 * Classifies the amount of meaningful learning data available for a celebration.
 *
 * The classification is a presentation aid, not an academic score or judgement.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class data_richness_classifier {
    /** No meaningful annual learning signals are available. */
    public const NONE = 'none';

    /** A small amount of learning data is available. */
    public const LOW = 'low';

    /** Enough learning data is available for a standard recap. */
    public const STANDARD = 'standard';

    /** Multiple strong learning signals are available. */
    public const RICH = 'rich';

    /** Celebration experience used when no learning recap can be built. */
    public const EXPERIENCE_CELEBRATION_ONLY = 'celebration_only';

    /** Celebration experience used for recently joined or low-data users. */
    public const EXPERIENCE_BIRTHDAY_WELCOME = 'birthday_welcome';

    /** Celebration experience used for a standard annual learning recap. */
    public const EXPERIENCE_LEARNING_YEAR = 'learning_year';

    /** Celebration experience used when a year-over-year comparison is meaningful. */
    public const EXPERIENCE_LEARNING_YEAR_COMPARISON = 'learning_year_comparison';

    /** Maximum account age treated as recently created for welcome-mode purposes. */
    private const RECENT_ACCOUNT_DAYS = 30;

    /** Minimum activity completions that make a period independently meaningful. */
    private const STANDARD_ACTIVITY_THRESHOLD = 5;

    /** Minimum badge count that independently supports a standard annual recap. */
    private const STANDARD_BADGE_THRESHOLD = 2;

    /** High activity volume that makes a period rich even without other categories. */
    private const RICH_ACTIVITY_THRESHOLD = 25;

    /**
     * Classify one completed period.
     *
     * Active enrolments and recent account age are used only as contextual signals for the
     * current celebration. Pass zero enrolments and a large account age for historical periods.
     *
     * @param period_statistics $statistics Period statistics.
     * @param int $activeenrolments Current active enrolments.
     * @param int $accountagedays Days since account creation.
     * @return string One of the richness constants.
     */
    public function classify(
        period_statistics $statistics,
        int $activeenrolments = 0,
        int $accountagedays = PHP_INT_MAX
    ): string {
        $achievementcategories = 0;

        if ($statistics->get_completed_courses() > 0) {
            $achievementcategories++;
        }
        if ($statistics->get_completed_activities() >= self::STANDARD_ACTIVITY_THRESHOLD) {
            $achievementcategories++;
        }
        if ($statistics->get_earned_badges() > 0) {
            $achievementcategories++;
        }
        if ($statistics->get_graded_completed_courses() > 0) {
            $achievementcategories++;
        }

        if (
            $achievementcategories >= 3
            || $statistics->get_completed_activities() >= self::RICH_ACTIVITY_THRESHOLD
            || ($statistics->get_completed_courses() >= 2 && $statistics->get_completed_activities() >= 10)
        ) {
            return self::RICH;
        }

        if (
            $statistics->get_completed_courses() > 0
            || $statistics->get_earned_badges() >= self::STANDARD_BADGE_THRESHOLD
            || $statistics->get_graded_completed_courses() > 0
            || $statistics->get_completed_activities() >= self::STANDARD_ACTIVITY_THRESHOLD
        ) {
            return self::STANDARD;
        }

        if (
            $statistics->get_completed_activities() > 0
            || $statistics->get_earned_badges() > 0
            || $activeenrolments > 0
            || $accountagedays <= self::RECENT_ACCOUNT_DAYS
        ) {
            return self::LOW;
        }

        return self::NONE;
    }

    /**
     * Choose the future celebration presentation mode from data sufficiency.
     *
     * @param string $currentrichness Current period richness.
     * @param string $previousrichness Previous period richness.
     * @param bool $allowcomparison Whether a year-over-year slide is permitted by site settings.
     * @return string One of the experience constants.
     */
    public function select_experience(
        string $currentrichness,
        string $previousrichness,
        bool $allowcomparison = true
    ): string {
        if ($currentrichness === self::NONE) {
            return self::EXPERIENCE_CELEBRATION_ONLY;
        }

        if ($currentrichness === self::LOW) {
            return self::EXPERIENCE_BIRTHDAY_WELCOME;
        }

        if ($allowcomparison && in_array($previousrichness, [self::STANDARD, self::RICH], true)) {
            return self::EXPERIENCE_LEARNING_YEAR_COMPARISON;
        }

        return self::EXPERIENCE_LEARNING_YEAR;
    }
}
