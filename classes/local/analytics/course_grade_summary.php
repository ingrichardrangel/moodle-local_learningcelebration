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
 * Summary of visible numeric final grades for completed courses.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_grade_summary {
    /** @var int Number of usable completed-course grades. */
    private int $count;

    /** @var float|null Average normalised percentage. */
    private ?float $average;

    /** @var float|null Best normalised percentage. */
    private ?float $best;

    /** @var int|null Best course id. */
    private ?int $bestcourseid;

    /** @var string|null Best course name. */
    private ?string $bestcoursename;

    /**
     * Constructor.
     *
     * @param int $count Number of grades.
     * @param float|null $average Average percentage.
     * @param float|null $best Best percentage.
     * @param int|null $bestcourseid Best course id.
     * @param string|null $bestcoursename Best course name.
     */
    public function __construct(
        int $count,
        ?float $average,
        ?float $best,
        ?int $bestcourseid,
        ?string $bestcoursename
    ) {
        $this->count = $count;
        $this->average = $average;
        $this->best = $best;
        $this->bestcourseid = $bestcourseid;
        $this->bestcoursename = $bestcoursename;
    }

    /**
     * Get the number of usable course grades.
     *
     * @return int Number of usable course grades.
     */
    public function get_count(): int {
        return $this->count;
    }

    /**
     * Get the average normalised course grade.
     *
     * @return float|null Average percentage.
     */
    public function get_average(): ?float {
        return $this->average;
    }

    /**
     * Get the best normalised course grade.
     *
     * @return float|null Best percentage.
     */
    public function get_best(): ?float {
        return $this->best;
    }

    /**
     * Get the best course id.
     *
     * @return int|null Best course id.
     */
    public function get_best_course_id(): ?int {
        return $this->bestcourseid;
    }

    /**
     * Get the best course name.
     *
     * @return string|null Best course name.
     */
    public function get_best_course_name(): ?string {
        return $this->bestcoursename;
    }
}
