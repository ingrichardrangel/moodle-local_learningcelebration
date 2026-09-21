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

namespace local_learningcelebration;

use local_learningcelebration\local\analytics\data_richness_classifier;
use local_learningcelebration\local\analytics\period_statistics;

/**
 * Tests for adaptive data-richness classification.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\local\analytics\data_richness_classifier
 */
final class data_richness_classifier_test extends \advanced_testcase {
    /**
     * Build an empty period statistics object.
     *
     * @return period_statistics Empty period helper.
     */
    private function empty_period(): period_statistics {
        return new period_statistics(0, 0, 0, 0, null, null, null, null);
    }

    /**
     * No history and no contextual signal remains NONE.
     */
    public function test_none_without_learning_signals(): void {
        $classifier = new data_richness_classifier();

        $this->assertSame(
            data_richness_classifier::NONE,
            $classifier->classify($this->empty_period(), 0, 120)
        );
    }

    /**
     * A newly created account is LOW rather than an empty annual recap.
     */
    public function test_recent_account_uses_low_richness(): void {
        $classifier = new data_richness_classifier();

        $this->assertSame(
            data_richness_classifier::LOW,
            $classifier->classify($this->empty_period(), 0, 2)
        );
    }

    /**
     * An active enrolment also makes an otherwise empty period useful for welcome mode.
     */
    public function test_active_enrolment_uses_low_richness(): void {
        $classifier = new data_richness_classifier();

        $this->assertSame(
            data_richness_classifier::LOW,
            $classifier->classify($this->empty_period(), 1, 120)
        );
    }

    /**
     * A single badge alone remains LOW so the future UI does not overstate a thin history.
     */
    public function test_single_badge_remains_low_richness(): void {
        $classifier = new data_richness_classifier();
        $statistics = new period_statistics(0, 0, 1, 0, null, null, null, null);

        $this->assertSame(data_richness_classifier::LOW, $classifier->classify($statistics));
    }

    /**
     * Five activity completions provide a meaningful standard recap.
     */
    public function test_activity_threshold_uses_standard_richness(): void {
        $classifier = new data_richness_classifier();
        $statistics = new period_statistics(0, 5, 0, 0, null, null, null, null);

        $this->assertSame(data_richness_classifier::STANDARD, $classifier->classify($statistics));
    }

    /**
     * Multiple achievement categories produce RICH data.
     */
    public function test_multiple_categories_use_rich_richness(): void {
        $classifier = new data_richness_classifier();
        $statistics = new period_statistics(2, 12, 1, 2, 88.0, 95.0, 3, 'Example course');

        $this->assertSame(data_richness_classifier::RICH, $classifier->classify($statistics));
    }

    /**
     * Comparison mode is selected only when both periods are sufficiently meaningful.
     */
    public function test_comparison_experience_requires_previous_standard_data(): void {
        $classifier = new data_richness_classifier();

        $this->assertSame(
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON,
            $classifier->select_experience(data_richness_classifier::RICH, data_richness_classifier::STANDARD)
        );
        $this->assertSame(
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR,
            $classifier->select_experience(data_richness_classifier::STANDARD, data_richness_classifier::LOW)
        );
    }

    /**
     * Comparison can be disabled without removing the annual recap.
     */
    public function test_comparison_can_be_disabled_by_site_policy(): void {
        $classifier = new \local_learningcelebration\local\analytics\data_richness_classifier();
        $experience = $classifier->select_experience(
            \local_learningcelebration\local\analytics\data_richness_classifier::RICH,
            \local_learningcelebration\local\analytics\data_richness_classifier::RICH,
            false
        );

        $this->assertSame(
            \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_LEARNING_YEAR,
            $experience
        );
    }
}
