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

use local_learningcelebration\local\birthday\birthday_engine;

/**
 * Tests for the birthday calculation engine.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\local\birthday\birthday_engine
 */
final class birthday_engine_test extends \advanced_testcase {
    /**
     * Exact birthdays are eligible and produce the expected learning periods.
     */
    public function test_exact_birthday_is_eligible(): void {
        $timezone = new \DateTimeZone('America/Caracas');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('1990-09-18', $timezone),
            $timezone,
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            $this->timestamp('2026-09-18 13:00:00', $timezone)
        );

        $this->assertTrue($result->is_eligible());
        $this->assertTrue($result->is_birthday_today());
        $this->assertSame(birthday_engine::STATE_BIRTHDAY, $result->get_state());
        $this->assertSame(0, $result->get_days_after_birthday());
        $this->assertSame('2026-09-18', $result->get_last_birthday()->format('Y-m-d'));
        $this->assertSame('2027-09-18', $result->get_next_birthday()->format('Y-m-d'));
        $this->assertSame(365, $result->get_days_until_next_birthday());
        $this->assertSame('2025-09-18', $result->get_period_start()->format('Y-m-d'));
        $this->assertSame('2026-09-17', $result->get_period_end_display()->format('Y-m-d'));
        $this->assertSame('2024-09-18', $result->get_previous_period_start()->format('Y-m-d'));
        $this->assertSame('2025-09-17', $result->get_previous_period_end_display()->format('Y-m-d'));
    }

    /**
     * A date inside the configured post-birthday window is marked as delayed.
     */
    public function test_delayed_window_is_eligible(): void {
        $timezone = new \DateTimeZone('America/Caracas');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('1990-09-18', $timezone),
            $timezone,
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            $this->timestamp('2026-09-20 09:00:00', $timezone)
        );

        $this->assertTrue($result->is_eligible());
        $this->assertFalse($result->is_birthday_today());
        $this->assertSame(2, $result->get_days_after_birthday());
        $this->assertSame(birthday_engine::STATE_DELAYED, $result->get_state());
    }

    /**
     * Dates beyond the configured window are not eligible.
     */
    public function test_outside_window_is_not_eligible(): void {
        $timezone = new \DateTimeZone('America/Caracas');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('1990-09-18', $timezone),
            $timezone,
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            $this->timestamp('2026-09-22 09:00:00', $timezone)
        );

        $this->assertFalse($result->is_eligible());
        $this->assertSame(4, $result->get_days_after_birthday());
        $this->assertSame(birthday_engine::STATE_OUTSIDE, $result->get_state());
    }

    /**
     * Before this year's birthday, the engine uses the previous year's birthday as the last birthday.
     */
    public function test_before_birthday_uses_previous_year(): void {
        $timezone = new \DateTimeZone('Europe/Madrid');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('1990-09-18', $timezone),
            $timezone,
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            $this->timestamp('2026-06-01 12:00:00', $timezone)
        );

        $this->assertSame('2025-09-18', $result->get_last_birthday()->format('Y-m-d'));
        $this->assertSame('2026-09-18', $result->get_next_birthday()->format('Y-m-d'));
        $this->assertSame(109, $result->get_days_until_next_birthday());
        $this->assertFalse($result->is_eligible());
    }

    /**
     * Celebration windows can cross from December into January.
     */
    public function test_window_can_cross_calendar_year(): void {
        $timezone = new \DateTimeZone('America/New_York');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('1990-12-31', $timezone),
            $timezone,
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            $this->timestamp('2026-01-02 10:00:00', $timezone)
        );

        $this->assertTrue($result->is_eligible());
        $this->assertSame(2, $result->get_days_after_birthday());
        $this->assertSame(birthday_engine::STATE_DELAYED, $result->get_state());
        $this->assertSame('2025-12-31', $result->get_last_birthday()->format('Y-m-d'));
        $this->assertSame('2026-12-31', $result->get_next_birthday()->format('Y-m-d'));
        $this->assertSame('2024-12-31', $result->get_period_start()->format('Y-m-d'));
        $this->assertSame('2025-12-30', $result->get_period_end_display()->format('Y-m-d'));
    }

    /**
     * A 29 February birthday can be observed on 28 February in a non-leap year.
     */
    public function test_february_29_can_use_february_28_policy(): void {
        $timezone = new \DateTimeZone('UTC');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('2000-02-29', $timezone),
            $timezone,
            0,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            $this->timestamp('2026-02-28 08:00:00', $timezone)
        );

        $this->assertTrue($result->is_birthday_today());
        $this->assertTrue($result->is_eligible());
        $this->assertSame('2026-02-28', $result->get_last_birthday()->format('Y-m-d'));
        $this->assertSame('2027-02-28', $result->get_next_birthday()->format('Y-m-d'));
    }

    /**
     * A 29 February birthday can be observed on 1 March in a non-leap year.
     */
    public function test_february_29_can_use_march_1_policy(): void {
        $timezone = new \DateTimeZone('UTC');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('2000-02-29', $timezone),
            $timezone,
            0,
            birthday_engine::LEAPDAY_MARCH_1,
            $this->timestamp('2026-03-01 08:00:00', $timezone)
        );

        $this->assertTrue($result->is_birthday_today());
        $this->assertSame('2026-03-01', $result->get_last_birthday()->format('Y-m-d'));
        $this->assertSame('2027-03-01', $result->get_next_birthday()->format('Y-m-d'));
    }

    /**
     * Leap years always use 29 February regardless of the non-leap-year policy.
     */
    public function test_february_29_uses_actual_date_in_leap_year(): void {
        $timezone = new \DateTimeZone('UTC');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('2000-02-29', $timezone),
            $timezone,
            0,
            birthday_engine::LEAPDAY_MARCH_1,
            $this->timestamp('2028-02-29 08:00:00', $timezone)
        );

        $this->assertTrue($result->is_birthday_today());
        $this->assertSame('2028-02-29', $result->get_last_birthday()->format('Y-m-d'));
    }


    /**
     * Birth dates before the Unix epoch remain valid on supported 64-bit Moodle environments.
     */
    public function test_pre_1970_birthday_is_supported(): void {
        $timezone = new \DateTimeZone('UTC');
        $engine = new birthday_engine();

        $result = $engine->evaluate(
            $this->timestamp('1955-09-18', $timezone),
            $timezone,
            3,
            birthday_engine::LEAPDAY_FEBRUARY_28,
            $this->timestamp('2026-09-18 12:00:00', $timezone)
        );

        $this->assertTrue($result->is_birthday_today());
        $this->assertSame('1955-09-18', $result->get_date_of_birth()->format('Y-m-d'));
        $this->assertSame('2026-09-18', $result->get_last_birthday()->format('Y-m-d'));
    }

    /**
     * Convert a local date/time to a timestamp for deterministic tests.
     *
     * @param string $value Date/time string.
     * @param \DateTimeZone $timezone Timezone.
     * @return int Timestamp.
     */
    private function timestamp(string $value, \DateTimeZone $timezone): int {
        return (new \DateTimeImmutable($value, $timezone))->getTimestamp();
    }
}
