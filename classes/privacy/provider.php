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

namespace local_learningcelebration\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use local_learningcelebration\local\celebration\auto_display_service;
use local_learningcelebration\local\celebration\view_repository;

/**
 * Privacy provider for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\user_preference_provider {

    /** @var string Component name. */
    private const COMPONENT = 'local_learningcelebration';

    /**
     * Describe stored personal data.
     *
     * @param collection $collection Metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            view_repository::TABLE,
            [
                'userid' => 'privacy:metadata:views:userid',
                'celebrationyear' => 'privacy:metadata:views:celebrationyear',
                'timefirstviewed' => 'privacy:metadata:views:timefirstviewed',
                'timelastviewed' => 'privacy:metadata:views:timelastviewed',
                'viewcount' => 'privacy:metadata:views:viewcount',
                'completed' => 'privacy:metadata:views:completed',
                'timecompleted' => 'privacy:metadata:views:timecompleted',
            ],
            'privacy:metadata:views'
        );

        $collection->add_user_preference(
            auto_display_service::PREF_AUTOSHOW,
            'privacy:metadata:preference:autoshow'
        );

        return $collection;
    }

    /**
     * Get contexts containing plugin-owned data for a user.
     *
     * @param int $userid User id.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $contextlist = new contextlist();
        $hasviewdata = $DB->record_exists(view_repository::TABLE, ['userid' => $userid]);
        $haspreference = get_user_preferences(auto_display_service::PREF_AUTOSHOW, null, $userid) !== null;
        if ($hasviewdata || $haspreference) {
            $contextlist->add_system_context();
        }
        return $contextlist;
    }

    /**
     * Export plugin-owned data.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $systemcontext = \context_system::instance();
        if (!in_array($systemcontext->id, $contextlist->get_contextids(), true)) {
            return;
        }

        $records = $DB->get_records(view_repository::TABLE, [
            'userid' => $contextlist->get_user()->id,
        ], 'celebrationyear ASC');

        $export = [];
        foreach ($records as $record) {
            $export[] = (object) [
                'celebrationyear' => (int) $record->celebrationyear,
                'timefirstviewed' => transform::datetime((int) $record->timefirstviewed),
                'timelastviewed' => transform::datetime((int) $record->timelastviewed),
                'viewcount' => (int) $record->viewcount,
                'completed' => transform::yesno((int) $record->completed),
                'timecompleted' => empty($record->timecompleted)
                    ? null
                    : transform::datetime((int) $record->timecompleted),
            ];
        }

        writer::with_context($systemcontext)->export_data(
            [get_string('privacy:path:views', 'local_learningcelebration')],
            (object) ['views' => $export]
        );
    }

    /**
     * Delete all plugin-owned user data in a context.
     *
     * @param \context $context Context.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if (!$context instanceof \context_system) {
            return;
        }
        $DB->delete_records(view_repository::TABLE);
        $DB->delete_records('user_preferences', [
            'name' => auto_display_service::PREF_AUTOSHOW,
        ]);
    }

    /**
     * Delete plugin-owned data for one user.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $systemcontext = \context_system::instance();
        if (!in_array($systemcontext->id, $contextlist->get_contextids(), true)) {
            return;
        }

        $userid = (int) $contextlist->get_user()->id;
        $DB->delete_records(view_repository::TABLE, ['userid' => $userid]);
        unset_user_preference(auto_display_service::PREF_AUTOSHOW, $userid);
    }

    /**
     * List users with plugin-owned data in a context.
     *
     * @param userlist $userlist User list.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        if (!$userlist->get_context() instanceof \context_system) {
            return;
        }

        $userlist->add_from_sql('userid', 'SELECT userid FROM {' . view_repository::TABLE . '}', []);
        $userlist->add_from_sql(
            'userid',
            'SELECT userid FROM {user_preferences} WHERE name = :preferencename',
            ['preferencename' => auto_display_service::PREF_AUTOSHOW]
        );
    }

    /**
     * Delete plugin-owned data for an approved list of users.
     *
     * @param approved_userlist $userlist Approved users.
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        if (!$userlist->get_context() instanceof \context_system) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $DB->delete_records_select(view_repository::TABLE, "userid {$insql}", $params);
        foreach ($userids as $userid) {
            unset_user_preference(auto_display_service::PREF_AUTOSHOW, (int) $userid);
        }
    }

    /**
     * Export site-wide user preferences owned by this plugin.
     *
     * @param int $userid User id.
     * @return void
     */
    public static function export_user_preferences(int $userid): void {
        $value = get_user_preferences(auto_display_service::PREF_AUTOSHOW, null, $userid);
        if ($value === null) {
            return;
        }

        writer::export_user_preference(
            self::COMPONENT,
            auto_display_service::PREF_AUTOSHOW,
            transform::yesno((int) $value),
            get_string('privacy:metadata:preference:autoshow', 'local_learningcelebration')
        );
    }
}
