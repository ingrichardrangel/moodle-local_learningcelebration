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

use local_learningcelebration\local\admin\simulation_service;
use local_learningcelebration\local\birthday\birthday_engine;

/**
 * Tests for the administrator birthday-engine simulator service.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\local\admin\simulation_service
 */
final class simulation_service_test extends \advanced_testcase {
    /**
     * The simulator can test 29 February with a 28 February policy without changing profile data.
     */
    public function test_simulates_february_29_with_february_28_policy(): void {
        $service = new simulation_service();
        $result = $service->evaluate(
            '2000-02-29',
            '2027-02-28',
            new \DateTimeZone('UTC'),
            0,
            birthday_engine::LEAPDAY_FEBRUARY_28
        );

        $this->assertTrue($result->is_birthday_today());
        $this->assertSame('2027-02-28', $result->get_last_birthday()->format('Y-m-d'));
    }

    /**
     * The simulator exposes how a different leap-day policy changes the same date.
     */
    public function test_simulates_february_29_with_march_1_policy(): void {
        $service = new simulation_service();
        $result = $service->evaluate(
            '2000-02-29',
            '2027-02-28',
            new \DateTimeZone('UTC'),
            0,
            birthday_engine::LEAPDAY_MARCH_1
        );

        $this->assertFalse($result->is_birthday_today());
        $this->assertSame('2027-03-01', $result->get_next_birthday()->format('Y-m-d'));
        $this->assertSame(1, $result->get_days_until_next_birthday());
    }

    /**
     * Invalid calendar dates are rejected instead of being normalised by PHP.
     */
    public function test_rejects_invalid_calendar_date(): void {
        $this->expectException(\InvalidArgumentException::class);

        (new simulation_service())->evaluate(
            '2001-02-29',
            '2027-02-28',
            new \DateTimeZone('UTC'),
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28
        );
    }

    /**
     * A simulated birth date cannot occur after the simulated current date.
     */
    public function test_rejects_birthday_after_simulated_today(): void {
        $this->expectException(\InvalidArgumentException::class);

        (new simulation_service())->evaluate(
            '2030-01-01',
            '2027-01-01',
            new \DateTimeZone('UTC'),
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28
        );
    }

    /**
     * Simulator windows mirror the values exposed by plugin administration.
     */
    public function test_rejects_unsupported_window(): void {
        $this->expectException(\InvalidArgumentException::class);

        (new simulation_service())->evaluate(
            '1990-12-31',
            '2027-01-02',
            new \DateTimeZone('UTC'),
            5,
            birthday_engine::LEAPDAY_FEBRUARY_28
        );
    }
}
