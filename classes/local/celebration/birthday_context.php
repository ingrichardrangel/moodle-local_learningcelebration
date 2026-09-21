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

namespace local_learningcelebration\local\celebration;

use local_learningcelebration\local\birthday\evaluation;

/**
 * Immutable resolved birthday context for the current annual celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class birthday_context {
    /** @var evaluation */
    private evaluation $evaluation;

    /** @var \DateTimeZone */
    private \DateTimeZone $timezone;

    /** @var int */
    private int $celebrationyear;

    /**
     * Constructor.
     *
     * @param evaluation $evaluation Birthday evaluation.
     * @param \DateTimeZone $timezone User timezone.
     * @param int $celebrationyear Observed birthday year.
     */
    public function __construct(evaluation $evaluation, \DateTimeZone $timezone, int $celebrationyear) {
        $this->evaluation = $evaluation;
        $this->timezone = $timezone;
        $this->celebrationyear = $celebrationyear;
    }

    /** @return evaluation */
    public function get_evaluation(): evaluation {
        return $this->evaluation;
    }

    /** @return \DateTimeZone */
    public function get_timezone(): \DateTimeZone {
        return $this->timezone;
    }

    /** @return int */
    public function get_celebration_year(): int {
        return $this->celebrationyear;
    }
}
