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

/**
 * Persistence for celebration views and completion state.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class view_repository {
    /** Database table name. */
    public const TABLE = 'local_learningcelebration_vw';

    /**
     * Return one annual view record.
     *
     * @param int $userid User id.
     * @param int $celebrationyear Observed birthday year.
     * @return \stdClass|null
     */
    public function get_record(int $userid, int $celebrationyear): ?\stdClass {
        global $DB;

        $record = $DB->get_record(self::TABLE, [
            'userid' => $userid,
            'celebrationyear' => $celebrationyear,
        ]);

        return $record ?: null;
    }

    /**
     * Whether the annual celebration is already completed.
     *
     * @param int $userid User id.
     * @param int $celebrationyear Observed birthday year.
     * @return bool
     */
    public function is_completed(int $userid, int $celebrationyear): bool {
        global $DB;

        return $DB->record_exists(self::TABLE, [
            'userid' => $userid,
            'celebrationyear' => $celebrationyear,
            'completed' => 1,
        ]);
    }

    /**
     * Record that the celebration was actually rendered to the user.
     *
     * The unique userid/year key can be hit concurrently when multiple browser tabs
     * load at once. A duplicate insert is therefore re-read and converted into the
     * normal update path rather than surfacing a database error to the user.
     *
     * @param int $userid User id.
     * @param int $celebrationyear Observed birthday year.
     * @param int|null $timestamp Timestamp override for tests.
     * @return \stdClass Stored record.
     */
    public function record_displayed(int $userid, int $celebrationyear, ?int $timestamp = null): \stdClass {
        global $DB;

        $timestamp = $timestamp ?? time();
        $record = $this->get_record($userid, $celebrationyear);

        if ($record) {
            return $this->increment_display($record, $timestamp);
        }

        $record = (object) [
            'userid' => $userid,
            'celebrationyear' => $celebrationyear,
            'timefirstviewed' => $timestamp,
            'timelastviewed' => $timestamp,
            'viewcount' => 1,
            'completed' => 0,
            'timecompleted' => null,
        ];

        try {
            $record->id = $DB->insert_record(self::TABLE, $record);
            return $record;
        } catch (\dml_write_exception $exception) {
            // If another request won the unique-key race, continue with that record.
            $concurrent = $this->get_record($userid, $celebrationyear);
            if ($concurrent === null) {
                throw $exception;
            }
            return $this->increment_display($concurrent, $timestamp);
        }
    }

    /**
     * Mark an annual celebration as completed.
     *
     * @param int $userid User id.
     * @param int $celebrationyear Observed birthday year.
     * @param int|null $timestamp Timestamp override for tests.
     * @return void
     */
    public function mark_completed(int $userid, int $celebrationyear, ?int $timestamp = null): void {
        global $DB;

        $timestamp = $timestamp ?? time();
        $record = $this->get_record($userid, $celebrationyear);

        if (!$record) {
            $record = (object) [
                'userid' => $userid,
                'celebrationyear' => $celebrationyear,
                'timefirstviewed' => $timestamp,
                'timelastviewed' => $timestamp,
                'viewcount' => 1,
                'completed' => 1,
                'timecompleted' => $timestamp,
            ];

            try {
                $DB->insert_record(self::TABLE, $record);
                return;
            } catch (\dml_write_exception $exception) {
                $record = $this->get_record($userid, $celebrationyear);
                if ($record === null) {
                    throw $exception;
                }
            }
        }

        if (empty($record->completed)) {
            $record->completed = 1;
            $record->timecompleted = $timestamp;
        }
        $record->timelastviewed = $timestamp;
        $DB->update_record(self::TABLE, $record);
    }

    /**
     * Delete one annual celebration record.
     *
     * @param int $userid User id.
     * @param int $celebrationyear Observed birthday year.
     * @return void
     */
    public function delete_record(int $userid, int $celebrationyear): void {
        global $DB;
        $DB->delete_records(self::TABLE, [
            'userid' => $userid,
            'celebrationyear' => $celebrationyear,
        ]);
    }

    /**
     * Return the most recent completed annual celebration for a user.
     *
     * @param int $userid User id.
     * @return \stdClass|null
     */
    public function get_latest_completed_record(int $userid): ?\stdClass {
        global $DB;

        $records = $DB->get_records(
            self::TABLE,
            ['userid' => $userid, 'completed' => 1],
            'celebrationyear DESC',
            '*',
            0,
            1
        );
        $record = reset($records);

        return $record ?: null;
    }

    /**
     * Whether a user has at least one completed celebration.
     *
     * @param int $userid User id.
     * @return bool
     */
    public function has_completed_celebration(int $userid): bool {
        global $DB;
        return $DB->record_exists(self::TABLE, ['userid' => $userid, 'completed' => 1]);
    }

    /**
     * Increment an existing automatic-display record.
     *
     * @param \stdClass $record Existing record.
     * @param int $timestamp Display timestamp.
     * @return \stdClass Updated record.
     */
    private function increment_display(\stdClass $record, int $timestamp): \stdClass {
        global $DB;

        $record->timelastviewed = $timestamp;
        $record->viewcount = ((int) $record->viewcount) + 1;
        $DB->update_record(self::TABLE, $record);
        return $record;
    }
}
