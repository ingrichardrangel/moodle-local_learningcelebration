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
 * Upgrade steps for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade Learning Celebration.
 *
 * @param int $oldversion Installed version.
 * @return bool
 */
function xmldb_local_learningcelebration_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026091807) {
        $table = new xmldb_table('local_lc_views');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('celebrationyear', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timefirstviewed', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timelastviewed', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('viewcount', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userfk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('useryear', XMLDB_KEY_UNIQUE, ['userid', 'celebrationyear']);
        $table->add_index('completedidx', XMLDB_INDEX_NOTUNIQUE, ['completed']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026091807, 'local', 'learningcelebration');
    }

    if ($oldversion < 2026091809) {
        $table = new xmldb_table('local_lc_views');
        $oldindex = new xmldb_index('completedidx', XMLDB_INDEX_NOTUNIQUE, ['completed']);
        $newindex = new xmldb_index('usercompletedyear', XMLDB_INDEX_NOTUNIQUE, [
            'userid',
            'completed',
            'celebrationyear',
        ]);

        if ($dbman->index_exists($table, $oldindex)) {
            $dbman->drop_index($table, $oldindex);
        }
        if (!$dbman->index_exists($table, $newindex)) {
            $dbman->add_index($table, $newindex);
        }

        upgrade_plugin_savepoint(true, 2026091809, 'local', 'learningcelebration');
    }

    if ($oldversion < 2026092100) {
        $oldtable = new xmldb_table('local_lc_views');
        $newtable = new xmldb_table('local_learningcelebration_vw');

        if ($dbman->table_exists($oldtable) && !$dbman->table_exists($newtable)) {
            $dbman->rename_table($oldtable, 'local_learningcelebration_vw');
        }

        upgrade_plugin_savepoint(true, 2026092100, 'local', 'learningcelebration');
    }

    return true;
}
