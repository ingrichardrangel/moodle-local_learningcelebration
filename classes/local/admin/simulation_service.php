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

namespace local_learningcelebration\local\admin;

use local_learningcelebration\local\birthday\birthday_engine;
use local_learningcelebration\local\birthday\evaluation;

/**
 * Validates administrator QA inputs and evaluates the birthday engine with simulated dates.
 *
 * Simulation values are never persisted by this service.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class simulation_service {
    /** @var int[] Celebration windows exposed by the administrator simulator. */
    private const ALLOWED_WINDOWS = [0, 1, 3, 7];

    /** @var birthday_engine Birthday calculation engine. */
    private birthday_engine $engine;

    /**
     * Constructor.
     *
     * @param birthday_engine|null $engine Optional engine instance for testing.
     */
    public function __construct(?birthday_engine $engine = null) {
        $this->engine = $engine ?? new birthday_engine();
    }

    /**
     * Evaluate an arbitrary birthday and current date in a specified timezone.
     *
     * @param string $birthdate ISO local date (Y-m-d).
     * @param string $today ISO local date (Y-m-d).
     * @param \DateTimeZone $timezone User timezone.
     * @param int $windowdays Celebration window.
     * @param string $leapdaypolicy Leap-day policy.
     * @return evaluation Birthday-engine result.
     */
    public function evaluate(
        string $birthdate,
        string $today,
        \DateTimeZone $timezone,
        int $windowdays,
        string $leapdaypolicy
    ): evaluation {
        if (!in_array($windowdays, self::ALLOWED_WINDOWS, true)) {
            throw new \InvalidArgumentException('Unsupported celebration window.');
        }

        $birthdateobject = $this->parse_date($birthdate, $timezone);
        $todayobject = $this->parse_date($today, $timezone);

        if ($birthdateobject > $todayobject) {
            throw new \InvalidArgumentException('The simulated birthday cannot be later than the simulated current date.');
        }

        return $this->engine->evaluate(
            $birthdateobject->setTime(12, 0, 0)->getTimestamp(),
            $timezone,
            $windowdays,
            $leapdaypolicy,
            $todayobject->setTime(12, 0, 0)->getTimestamp()
        );
    }

    /**
     * Parse an exact ISO calendar date without accepting normalised invalid values.
     *
     * @param string $value ISO date.
     * @param \DateTimeZone $timezone Timezone.
     * @return \DateTimeImmutable Parsed local date at midnight.
     */
    private function parse_date(string $value, \DateTimeZone $timezone): \DateTimeImmutable {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, $timezone);
        $errors = \DateTimeImmutable::getLastErrors();

        $haserrors = is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0);
        if (!$date || $haserrors || $date->format('Y-m-d') !== $value) {
            throw new \InvalidArgumentException('Invalid simulated date. Use a real calendar date in YYYY-MM-DD format.');
        }

        return $date;
    }
}
