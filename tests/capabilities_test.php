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

/**
 * Capability regression tests.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
final class capabilities_test extends \advanced_testcase {
    /**
     * Authenticated users receive learner-facing access but not management access by default.
     */
    public function test_authenticated_user_default_capabilities(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $context = \context_system::instance();

        $this->assertTrue(has_capability('local/learningcelebration:view', $context));
        $this->assertFalse(has_capability('local/learningcelebration:manage', $context));
    }

    /**
     * A system manager receives the plugin management capability from its archetype.
     */
    public function test_manager_default_management_capability(): void {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $context = \context_system::instance();
        $managerroleid = (int) $DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST);
        role_assign($managerroleid, (int) $user->id, $context->id);
        $this->setUser($user);

        $this->assertTrue(has_capability('local/learningcelebration:view', $context));
        $this->assertTrue(has_capability('local/learningcelebration:manage', $context));
    }
}
