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
