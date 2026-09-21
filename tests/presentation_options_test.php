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
use local_learningcelebration\local\presentation\presentation_options;

/**
 * Tests for site-level presentation controls.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\local\presentation\presentation_options
 */
final class presentation_options_test extends \advanced_testcase {
    /**
     * Disabling motion also suppresses animated confetti.
     */
    public function test_motion_off_suppresses_confetti(): void {
        $options = new presentation_options(false, true);
        $this->assertFalse($options->motion_enabled());
        $this->assertFalse($options->confetti_enabled());

        $context = (new celebration_view_builder($options))->build_demo(
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR,
            'Alex',
            'Example Moodle'
        );
        $this->assertFalse($context['motionenabled']);
        $this->assertFalse($context['confettienabled']);
    }
}
