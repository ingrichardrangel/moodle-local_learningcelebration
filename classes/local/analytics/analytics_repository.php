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
 * Read-only access to Moodle core learning data used by Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analytics_repository {
    /**
     * Build statistics for one half-open period: start <= event < end.
     *
     * @param int $userid User id.
     * @param int $starttimestamp Inclusive start timestamp.
     * @param int $endtimestamp Exclusive end timestamp.
     * @return period_statistics
     */
    public function get_period_statistics(int $userid, int $starttimestamp, int $endtimestamp): period_statistics {
        $grades = $this->get_completed_course_grade_summary($userid, $starttimestamp, $endtimestamp);

        return new period_statistics(
            $this->count_completed_courses($userid, $starttimestamp, $endtimestamp),
            $this->count_completed_activities($userid, $starttimestamp, $endtimestamp),
            $this->count_earned_badges($userid, $starttimestamp, $endtimestamp),
            $grades->get_count(),
            $grades->get_average(),
            $grades->get_best(),
            $grades->get_best_course_id(),
            $grades->get_best_course_name()
        );
    }

    /**
     * Count course-completion records completed during the period.
     *
     * @param int $userid User id.
     * @param int $starttimestamp Inclusive start timestamp.
     * @param int $endtimestamp Exclusive end timestamp.
     * @return int
     */
    protected function count_completed_courses(int $userid, int $starttimestamp, int $endtimestamp): int {
        global $DB;

        $sql = "SELECT COUNT(1)
                  FROM {course_completions}
                 WHERE userid = :userid
                   AND timecompleted IS NOT NULL
                   AND timecompleted >= :periodstart
                   AND timecompleted < :periodend";

        return (int) $DB->count_records_sql($sql, [
            'userid' => $userid,
            'periodstart' => $starttimestamp,
            'periodend' => $endtimestamp,
        ]);
    }

    /**
     * Count completed activity records whose completion state last changed during the period.
     *
     * Moodle core stores one current completion row per user/activity. Therefore this metric
     * intentionally uses course_modules_completion.timemodified and does not claim to be an
     * immutable event-history count.
     *
     * @param int $userid User id.
     * @param int $starttimestamp Inclusive start timestamp.
     * @param int $endtimestamp Exclusive end timestamp.
     * @return int
     */
    protected function count_completed_activities(int $userid, int $starttimestamp, int $endtimestamp): int {
        global $DB;

        $sql = "SELECT COUNT(1)
                  FROM {course_modules_completion}
                 WHERE userid = :userid
                   AND completionstate > :incomplete
                   AND timemodified >= :periodstart
                   AND timemodified < :periodend";

        return (int) $DB->count_records_sql($sql, [
            'userid' => $userid,
            'incomplete' => 0,
            'periodstart' => $starttimestamp,
            'periodend' => $endtimestamp,
        ]);
    }

    /**
     * Count badges issued during the period.
     *
     * @param int $userid User id.
     * @param int $starttimestamp Inclusive start timestamp.
     * @param int $endtimestamp Exclusive end timestamp.
     * @return int
     */
    protected function count_earned_badges(int $userid, int $starttimestamp, int $endtimestamp): int {
        global $DB;

        $sql = "SELECT COUNT(1)
                  FROM {badge_issued}
                 WHERE userid = :userid
                   AND dateissued >= :periodstart
                   AND dateissued < :periodend";

        return (int) $DB->count_records_sql($sql, [
            'userid' => $userid,
            'periodstart' => $starttimestamp,
            'periodend' => $endtimestamp,
        ]);
    }

    /**
     * Return a normalised grade summary for courses completed during the period.
     *
     * Only visible course grade items and visible user grade records are used. This prevents
     * the celebration from exposing a grade which Moodle currently marks as hidden. The period
     * is determined by course completion time; the grade value is the current final course grade.
     *
     * @param int $userid User id.
     * @param int $starttimestamp Inclusive start timestamp.
     * @param int $endtimestamp Exclusive end timestamp.
     * @return course_grade_summary
     */
    protected function get_completed_course_grade_summary(
        int $userid,
        int $starttimestamp,
        int $endtimestamp
    ): course_grade_summary {
        global $DB;

        $sql = "SELECT c.id AS courseid,
                       c.fullname,
                       gg.finalgrade,
                       gi.grademin,
                       gi.grademax
                  FROM {course_completions} cc
                  JOIN {course} c
                    ON c.id = cc.course
                  JOIN {grade_items} gi
                    ON gi.courseid = cc.course
                   AND gi.itemtype = :itemtype
                  JOIN {grade_grades} gg
                    ON gg.itemid = gi.id
                   AND gg.userid = cc.userid
                 WHERE cc.userid = :userid
                   AND cc.timecompleted IS NOT NULL
                   AND cc.timecompleted >= :periodstart
                   AND cc.timecompleted < :periodend
                   AND gg.finalgrade IS NOT NULL
                   AND gi.hidden = :itemvisible
                   AND gg.hidden = :gradevisible
                   AND c.visible = :coursevisible
                   AND gi.grademax > gi.grademin";

        $records = $DB->get_records_sql($sql, [
            'itemtype' => 'course',
            'userid' => $userid,
            'periodstart' => $starttimestamp,
            'periodend' => $endtimestamp,
            'itemvisible' => 0,
            'gradevisible' => 0,
            'coursevisible' => 1,
        ]);

        if (!$records) {
            return new course_grade_summary(0, null, null, null, null);
        }

        $total = 0.0;
        $count = 0;
        $best = null;
        $bestcourseid = null;
        $bestcoursename = null;

        foreach ($records as $record) {
            $minimum = (float) $record->grademin;
            $maximum = (float) $record->grademax;
            if ($maximum <= $minimum) {
                continue;
            }

            $percentage = (((float) $record->finalgrade - $minimum) / ($maximum - $minimum)) * 100;
            $total += $percentage;
            $count++;

            if ($best === null || $percentage > $best) {
                $best = $percentage;
                $bestcourseid = (int) $record->courseid;
                $bestcoursename = (string) $record->fullname;
            }
        }

        if ($count === 0) {
            return new course_grade_summary(0, null, null, null, null);
        }

        return new course_grade_summary(
            $count,
            $total / $count,
            $best,
            $bestcourseid,
            $bestcoursename
        );
    }

    /**
     * Count currently active course enrolments at the supplied instant.
     *
     * @param int $userid User id.
     * @param int $attimestamp Reference timestamp.
     * @return int Number of distinct active courses.
     */
    public function count_active_course_enrolments(int $userid, int $attimestamp): int {
        global $DB;

        $sql = "SELECT COUNT(DISTINCT e.courseid)
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE ue.userid = :userid
                   AND ue.status = :useractive
                   AND e.status = :instanceenabled
                   AND ue.timestart <= :timestart
                   AND (ue.timeend = :notimeend OR ue.timeend > :timeend)
                   AND e.courseid <> :siteid";

        return (int) $DB->count_records_sql($sql, [
            'userid' => $userid,
            'useractive' => 0,
            'instanceenabled' => 0,
            'timestart' => $attimestamp,
            'notimeend' => 0,
            'timeend' => $attimestamp,
            'siteid' => SITEID,
        ]);
    }
}
