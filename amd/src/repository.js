// This file is part of Moodle - https://moodle.org/
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
 * AJAX repository for Learning Celebration.
 *
 * @module     local_learningcelebration/repository
 * @copyright  2026 Richard Rangel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';

/**
 * Persist a user action for the currently eligible annual celebration.
 *
 * @param {String} action Action name: complete or snooze.
 * @param {Number} celebrationYear Observed birthday year.
 * @return {Promise}
 */
export const updateCelebrationState = (action, celebrationYear) => fetchMany([{
    methodname: 'local_learningcelebration_update_celebration_state',
    args: {
        action,
        celebrationyear: celebrationYear,
    },
}])[0];
