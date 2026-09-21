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

namespace local_learningcelebration\local;

/**
 * Data access for the configured birthday custom profile field.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_repository {
    /**
     * Return a supported datetime profile field by shortname.
     *
     * @param string $shortname Profile field shortname.
     * @return \stdClass|null The field record or null when it is not valid.
     */
    public function get_datetime_field(string $shortname): ?\stdClass {
        global $DB;

        if ($shortname === '') {
            return null;
        }

        $field = $DB->get_record('user_info_field', [
            'shortname' => $shortname,
            'datatype' => 'datetime',
        ]);

        return $field ?: null;
    }

    /**
     * Count users who currently have a non-empty value for the field.
     *
     * @param int $fieldid Custom profile field id.
     * @return int Number of stored non-empty values.
     */
    public function count_populated_values(int $fieldid): int {
        global $DB;

        $sql = "SELECT COUNT(1)
                  FROM {user_info_data}
                 WHERE fieldid = :fieldid
                   AND data <> :emptyvalue
                   AND data <> :zerovalue";

        return (int) $DB->count_records_sql($sql, [
            'fieldid' => $fieldid,
            'emptyvalue' => '',
            'zerovalue' => '0',
        ]);
    }

    /**
     * Return the user's raw timestamp value for the field when available.
     *
     * @param int $userid User id.
     * @param int $fieldid Custom profile field id.
     * @return int|null Timestamp or null when no birthday is stored.
     */
    public function get_user_value(int $userid, int $fieldid): ?int {
        global $DB;

        $value = $DB->get_field('user_info_data', 'data', [
            'userid' => $userid,
            'fieldid' => $fieldid,
        ]);

        if ($value === false || $value === '' || (string) $value === '0') {
            return null;
        }

        return (int) $value;
    }
}
