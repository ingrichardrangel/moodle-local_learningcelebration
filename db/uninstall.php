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

/**
 * Uninstall cleanup for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Remove plugin-owned data stored outside plugin tables.
 *
 * Plugin tables and config are removed by Moodle's standard uninstall process.
 *
 * @return bool
 */
function xmldb_local_learningcelebration_uninstall(): bool {
    global $DB;

    $DB->delete_records('user_preferences', [
        'name' => \local_learningcelebration\local\celebration\auto_display_service::PREF_AUTOSHOW,
    ]);

    return true;
}
