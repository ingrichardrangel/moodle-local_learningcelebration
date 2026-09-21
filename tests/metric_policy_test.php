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

use local_learningcelebration\local\analytics\metric_policy;
use local_learningcelebration\local\analytics\period_statistics;

/**
 * Tests for administrator metric filtering.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\local\analytics\metric_policy
 */
final class metric_policy_test extends \advanced_testcase {
    /** Disabled metrics are removed rather than exposed as learner-facing data. */
    public function test_filters_disabled_metrics(): void {
        $source = new period_statistics(3, 18, 4, 2, 88.5, 96.0, 17, 'Example course');
        $policy = new metric_policy(false, true, false, false, false, false);
        $filtered = $policy->filter_period($source);

        $this->assertSame(0, $filtered->get_completed_courses());
        $this->assertSame(18, $filtered->get_completed_activities());
        $this->assertSame(0, $filtered->get_earned_badges());
        $this->assertSame(0, $filtered->get_graded_completed_courses());
        $this->assertNull($filtered->get_average_course_grade());
        $this->assertNull($filtered->get_best_course_grade());
        $this->assertSame(0, $policy->filter_active_enrolments(5));
        $this->assertFalse($policy->allows_comparison());
    }
}
