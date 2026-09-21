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

namespace local_learningcelebration\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_learningcelebration\local\celebration\action_service;

/**
 * AJAX external function for updating the current annual celebration state.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class update_celebration_state extends external_api {
    /**
     * Describe input parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'action' => new external_value(PARAM_ALPHA, 'Action: complete or snooze.'),
            'celebrationyear' => new external_value(PARAM_INT, 'Observed birthday year.'),
        ]);
    }

    /**
     * Update the current user's celebration state.
     *
     * @param string $action Action to perform.
     * @param int $celebrationyear Observed birthday year supplied by the rendered experience.
     * @return array Success payload.
     */
    public static function execute(string $action, int $celebrationyear): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'action' => $action,
            'celebrationyear' => $celebrationyear,
        ]);

        $usercontext = \context_user::instance((int) $USER->id);
        self::validate_context($usercontext);
        require_capability('local/learningcelebration:view', \context_system::instance());

        (new action_service())->handle(
            $USER,
            (string) $params['action'],
            (int) $params['celebrationyear']
        );

        return ['success' => true];
    }

    /**
     * Describe return value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the action completed successfully.'),
        ]);
    }
}
