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
 * Library callbacks for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add Learning Celebration links to the current user's settings navigation.
 *
 * @param navigation_node $navigation User navigation node.
 * @param stdClass $user User whose navigation is being built.
 * @param context_user $usercontext User context.
 * @param stdClass $course Current course.
 * @param context|null $coursecontext Current course context.
 * @return void
 */
function local_learningcelebration_extend_navigation_user_settings(
    navigation_node $navigation,
    stdClass $user,
    context_user $usercontext,
    stdClass $course,
    ?context $coursecontext
): void {
    global $USER;

    if (empty($USER->id) || (int) $USER->id !== (int) $user->id || isguestuser()) {
        return;
    }

    if (!has_capability('local/learningcelebration:view', context_system::instance())) {
        return;
    }

    $navigation->add(
        get_string('preferencespage', 'local_learningcelebration'),
        new moodle_url('/local/learningcelebration/preferences.php'),
        navigation_node::TYPE_SETTING,
        null,
        'local_learningcelebration_preferences'
    );

    $navigation->add(
        get_string('replaylink', 'local_learningcelebration'),
        new moodle_url('/local/learningcelebration/replay.php'),
        navigation_node::TYPE_SETTING,
        null,
        'local_learningcelebration_replay'
    );
}
