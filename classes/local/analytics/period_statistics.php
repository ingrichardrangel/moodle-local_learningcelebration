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
 * Immutable statistics for one completed birthday-to-birthday learning period.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class period_statistics {
    /** @var int Courses completed in the period. */
    private int $completedcourses;

    /** @var int Activities whose completion state changed to complete in the period. */
    private int $completedactivities;

    /** @var int Badges issued in the period. */
    private int $earnedbadges;

    /** @var int Completed courses with a visible numeric course grade. */
    private int $gradedcompletedcourses;

    /** @var float|null Average normalised course grade percentage. */
    private ?float $averagecoursegrade;

    /** @var float|null Best normalised course grade percentage. */
    private ?float $bestcoursegrade;

    /** @var int|null Course id associated with the best course grade. */
    private ?int $bestcourseid;

    /** @var string|null Course name associated with the best course grade. */
    private ?string $bestcoursename;

    /**
     * Constructor.
     *
     * @param int $completedcourses Courses completed.
     * @param int $completedactivities Activities completed.
     * @param int $earnedbadges Badges issued.
     * @param int $gradedcompletedcourses Completed courses with a visible numeric grade.
     * @param float|null $averagecoursegrade Average grade percentage.
     * @param float|null $bestcoursegrade Best grade percentage.
     * @param int|null $bestcourseid Best course id.
     * @param string|null $bestcoursename Best course name.
     */
    public function __construct(
        int $completedcourses,
        int $completedactivities,
        int $earnedbadges,
        int $gradedcompletedcourses,
        ?float $averagecoursegrade,
        ?float $bestcoursegrade,
        ?int $bestcourseid,
        ?string $bestcoursename
    ) {
        foreach ([$completedcourses, $completedactivities, $earnedbadges, $gradedcompletedcourses] as $count) {
            if ($count < 0) {
                throw new \InvalidArgumentException('Analytics counts cannot be negative.');
            }
        }

        $this->completedcourses = $completedcourses;
        $this->completedactivities = $completedactivities;
        $this->earnedbadges = $earnedbadges;
        $this->gradedcompletedcourses = $gradedcompletedcourses;
        $this->averagecoursegrade = $averagecoursegrade;
        $this->bestcoursegrade = $bestcoursegrade;
        $this->bestcourseid = $bestcourseid;
        $this->bestcoursename = $bestcoursename;
    }

    /** @return int Courses completed. */
    public function get_completed_courses(): int {
        return $this->completedcourses;
    }

    /** @return int Activities completed. */
    public function get_completed_activities(): int {
        return $this->completedactivities;
    }

    /** @return int Badges earned. */
    public function get_earned_badges(): int {
        return $this->earnedbadges;
    }

    /** @return int Completed courses with a visible numeric grade. */
    public function get_graded_completed_courses(): int {
        return $this->gradedcompletedcourses;
    }

    /** @return float|null Average normalised course grade percentage. */
    public function get_average_course_grade(): ?float {
        return $this->averagecoursegrade;
    }

    /** @return float|null Best normalised course grade percentage. */
    public function get_best_course_grade(): ?float {
        return $this->bestcoursegrade;
    }

    /** @return int|null Best course id. */
    public function get_best_course_id(): ?int {
        return $this->bestcourseid;
    }

    /** @return string|null Best course name. */
    public function get_best_course_name(): ?string {
        return $this->bestcoursename;
    }

    /**
     * Whether any period achievement signal exists.
     *
     * @return bool
     */
    public function has_any_data(): bool {
        return $this->completedcourses > 0
            || $this->completedactivities > 0
            || $this->earnedbadges > 0
            || $this->gradedcompletedcourses > 0;
    }
}
