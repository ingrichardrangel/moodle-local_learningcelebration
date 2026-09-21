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
 * User preferences for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

require_login();
require_capability('local/learningcelebration:view', context_system::instance());

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/learningcelebration/preferences.php'));
$PAGE->set_title(get_string('preferencespage', 'local_learningcelebration'));
$PAGE->set_heading(fullname($USER));

$form = new \local_learningcelebration\form\preferences_form();
$form->set_data((object) [
    'autoshow' => (int) get_user_preferences(
        \local_learningcelebration\local\celebration\auto_display_service::PREF_AUTOSHOW,
        1,
        $USER->id
    ),
]);

if ($data = $form->get_data()) {
    $enabled = !empty($data->autoshow) ? 1 : 0;

    if ($enabled === 1) {
        // Avoid storing the default value unnecessarily.
        unset_user_preference(
            \local_learningcelebration\local\celebration\auto_display_service::PREF_AUTOSHOW,
            $USER->id
        );
    } else {
        set_user_preference(
            \local_learningcelebration\local\celebration\auto_display_service::PREF_AUTOSHOW,
            0,
            $USER->id
        );
    }

    redirect(
        new moodle_url('/local/learningcelebration/preferences.php'),
        get_string('changessaved'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('preferencespage', 'local_learningcelebration'));
echo $OUTPUT->notification(get_string('preferencesintro', 'local_learningcelebration'), 'info');
$form->display();

echo html_writer::div(
    html_writer::link(
        new moodle_url('/local/learningcelebration/replay.php'),
        get_string('replaylink', 'local_learningcelebration'),
        ['class' => 'btn btn-secondary']
    ),
    'mt-4'
);

echo $OUTPUT->footer();
