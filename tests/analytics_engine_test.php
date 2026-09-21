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

use local_learningcelebration\local\analytics\analytics_engine;
use local_learningcelebration\local\analytics\analytics_repository;
use local_learningcelebration\local\analytics\data_richness_classifier;
use local_learningcelebration\local\analytics\period_statistics;
use local_learningcelebration\local\birthday\birthday_engine;

/**
 * Tests for analytics-engine orchestration without querying Moodle tables.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\local\analytics\analytics_engine
 */
final class analytics_engine_test extends \advanced_testcase {
    /** The engine combines period statistics, enrolments and account age into one report. */
    public function test_builds_adaptive_report(): void {
        $current = new period_statistics(2, 14, 1, 2, 89.5, 96.0, 11, 'Current course');
        $previous = new period_statistics(1, 7, 0, 1, 82.0, 82.0, 12, 'Previous course');

        $repository = new class($current, $previous) extends analytics_repository {
            /** @var period_statistics[] */
            private array $periods;

            /** @param period_statistics $current Current period. @param period_statistics $previous Previous period. */
            public function __construct(period_statistics $current, period_statistics $previous) {
                $this->periods = [$current, $previous];
            }

            /** @inheritDoc */
            public function get_period_statistics(int $userid, int $starttimestamp, int $endtimestamp): period_statistics {
                return array_shift($this->periods);
            }

            /** @inheritDoc */
            public function count_active_course_enrolments(int $userid, int $attimestamp): int {
                return 3;
            }
        };

        $timezone = new \DateTimeZone('UTC');
        $birthday = new birthday_engine();
        $evaluation = $birthday->evaluate(
            (new \DateTimeImmutable('1990-09-18 12:00:00', $timezone))->getTimestamp(),
            $timezone,
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            (new \DateTimeImmutable('2026-09-18 12:00:00', $timezone))->getTimestamp()
        );

        $accountcreated = (new \DateTimeImmutable('2020-01-01 12:00:00', $timezone))->getTimestamp();
        $report = (new analytics_engine($repository))->build_report(
            7,
            $accountcreated,
            $evaluation,
            $timezone,
            (new \DateTimeImmutable('2026-09-18 12:00:00', $timezone))->getTimestamp()
        );

        $this->assertSame(3, $report->get_active_enrolments());
        $this->assertSame(data_richness_classifier::RICH, $report->get_current_richness());
        $this->assertSame(data_richness_classifier::STANDARD, $report->get_previous_richness());
        $this->assertTrue($report->is_comparison_available());
        $this->assertSame(2, $report->get_current_period()->get_completed_courses());
    }
}
