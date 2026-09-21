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
use local_learningcelebration\local\presentation\celebration_view_builder;

/**
 * Tests for visual celebration view-data generation.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\local\presentation\celebration_view_builder
 */
final class celebration_view_builder_test extends \advanced_testcase {
    /** All deterministic preview modes produce an interactive deck with at least two slides. */
    public function test_all_demo_modes_build_complete_decks(): void {
        $builder = new celebration_view_builder();
        $modes = [
            data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY,
            data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME,
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR,
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON,
        ];

        foreach ($modes as $mode) {
            $context = $builder->build_demo($mode, 'Alex', 'Example Moodle');
            $this->assertSame($mode, $context['experience']);
            $this->assertTrue($context['isdemo']);
            $this->assertGreaterThanOrEqual(2, $context['slidecount']);
            $this->assertCount($context['slidecount'], $context['slides']);
            $this->assertTrue($context['slides'][0]['isfirst']);
            $this->assertTrue($context['slides'][$context['slidecount'] - 1]['isfinal']);
        }
    }

    /** Comparison demo contains a dedicated comparison slide. */
    public function test_comparison_demo_contains_comparison_slide(): void {
        $context = (new celebration_view_builder())->build_demo(
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON,
            'Alex',
            'Example Moodle'
        );

        $comparisons = array_filter($context['slides'], static fn(array $slide): bool => !empty($slide['hascomparison']));
        $this->assertCount(1, $comparisons);
    }

    /** Unsupported preview modes are rejected rather than silently normalised. */
    public function test_unsupported_demo_mode_is_rejected(): void {
        $this->expectException(\InvalidArgumentException::class);
        (new celebration_view_builder())->build_demo('unknown_mode', 'Alex', 'Example Moodle');
    }
}
