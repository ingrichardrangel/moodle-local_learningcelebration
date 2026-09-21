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

namespace local_learningcelebration;

use local_learningcelebration\local\celebration\auto_display_service;
use local_learningcelebration\privacy\provider;

/**
 * Privacy regression tests for plugin-owned user preferences.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_learningcelebration\privacy\provider
 */
final class privacy_provider_test extends \advanced_testcase {
    /** A stored opt-out preference alone must make the system context discoverable. */
    public function test_preference_only_user_has_system_context(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_user_preference(auto_display_service::PREF_AUTOSHOW, 0, (int) $user->id);

        $contexts = provider::get_contexts_for_userid((int) $user->id)->get_contextids();
        $this->assertContains(\context_system::instance()->id, $contexts);
    }

    /** Context-wide deletion removes both annual view data and plugin-owned preferences. */
    public function test_context_wide_delete_removes_preferences(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_user_preference(auto_display_service::PREF_AUTOSHOW, 0, (int) $user->id);

        provider::delete_data_for_all_users_in_context(\context_system::instance());

        $this->assertNull(get_user_preferences(
            auto_display_service::PREF_AUTOSHOW,
            null,
            (int) $user->id
        ));
    }

    /**
     * A user with only an annual view row is discoverable through the system context.
     */
    public function test_view_only_user_has_system_context(): void {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $DB->insert_record('local_lc_views', (object) [
            'userid' => (int) $user->id,
            'celebrationyear' => 2026,
            'timefirstviewed' => 1000,
            'timelastviewed' => 1000,
            'viewcount' => 1,
            'completed' => 0,
            'timecompleted' => null,
        ]);

        $contexts = provider::get_contexts_for_userid((int) $user->id)->get_contextids();
        $this->assertContains(\context_system::instance()->id, $contexts);
    }

    /**
     * Context-wide deletion removes annual view rows as well as preferences.
     */
    public function test_context_wide_delete_removes_annual_views(): void {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $DB->insert_record('local_lc_views', (object) [
            'userid' => (int) $user->id,
            'celebrationyear' => 2026,
            'timefirstviewed' => 1000,
            'timelastviewed' => 1000,
            'viewcount' => 1,
            'completed' => 1,
            'timecompleted' => 1200,
        ]);

        provider::delete_data_for_all_users_in_context(\context_system::instance());

        $this->assertFalse($DB->record_exists('local_lc_views', ['userid' => (int) $user->id]));
    }

}
