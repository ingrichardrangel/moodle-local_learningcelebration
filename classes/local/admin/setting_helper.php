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

/**
 * Helpers used by the Learning Celebration administration settings.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_helper {
    /**
     * Return datetime custom profile fields for the birthday field selector.
     *
     * The method returns the choices consumed directly by admin_setting_configselect.
     *
     * @return array<string, string> Available choices keyed by profile field shortname.
     */
    public static function get_datetime_profile_fields(): array {
        global $DB;

        $choices = ['' => get_string('choosebirthdayfield', 'local_learningcelebration')];

        $sql = "SELECT f.id, f.shortname, f.name, f.categoryid, f.sortorder, c.name AS categoryname
                  FROM {user_info_field} f
                  JOIN {user_info_category} c ON c.id = f.categoryid
                 WHERE f.datatype = :datatype
              ORDER BY c.id ASC, f.sortorder ASC, f.id ASC";

        $fields = $DB->get_records_sql($sql, ['datatype' => 'datetime']);
        $context = \context_system::instance();

        foreach ($fields as $field) {
            $categoryname = format_string($field->categoryname, true, ['context' => $context]);
            $fieldname = format_string($field->name, true, ['context' => $context]);
            $choices[$field->shortname] = get_string(
                'profilefieldchoice',
                'local_learningcelebration',
                (object) [
                    'category' => $categoryname,
                    'name' => $fieldname,
                    'shortname' => $field->shortname,
                ]
            );
        }

        return $choices;
    }
}
