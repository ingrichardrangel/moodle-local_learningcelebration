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
 * Administrator-only visual preview for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_learningcelebration_preview');
require_capability('local/learningcelebration:manage', context_system::instance());

$PAGE->set_title(get_string('previewpage', 'local_learningcelebration'));
$PAGE->set_heading(get_string('pluginname', 'local_learningcelebration'));

$mode = optional_param('mode', 'live', PARAM_ALPHANUMEXT);
$experiences = [
    'live' => get_string('previewmode_live', 'local_learningcelebration'),
    \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY =>
        get_string('analytics_experience_celebrationonly', 'local_learningcelebration'),
    \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME =>
        get_string('analytics_experience_birthdaywelcome', 'local_learningcelebration'),
    \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_LEARNING_YEAR =>
        get_string('analytics_experience_learningyear', 'local_learningcelebration'),
    \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON =>
        get_string('analytics_experience_learningyearcomparison', 'local_learningcelebration'),
];
if (!array_key_exists($mode, $experiences)) {
    $mode = 'live';
}

$builder = new \local_learningcelebration\local\presentation\celebration_view_builder();
$firstname = (string) $USER->firstname;
$sitename = strip_tags(format_string($SITE->shortname ?: $SITE->fullname));
$viewdata = null;
$liveexperience = null;

if ($mode === 'live') {
    $config = get_config('local_learningcelebration');
    $shortname = $config->birthdayfield ?? '';
    $windowdays = isset($config->celebrationwindow) ? max(0, (int) $config->celebrationwindow) : 3;
    $leapdaypolicy = $config->leapdaypolicy
        ?? \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_FEBRUARY_28;

    $repository = new \local_learningcelebration\local\profile_field_repository();
    $field = $repository->get_datetime_field($shortname);

    if ($field) {
        $birthtimestamp = $repository->get_user_value((int) $USER->id, (int) $field->id);
        if ($birthtimestamp !== null) {
            $timezone = \core_date::get_user_timezone_object($USER);
            $evaluation = (new \local_learningcelebration\local\birthday\birthday_engine())->evaluate(
                $birthtimestamp,
                $timezone,
                $windowdays,
                $leapdaypolicy
            );
            $report = (new \local_learningcelebration\local\analytics\analytics_engine())->build_report(
                (int) $USER->id,
                (int) $USER->timecreated,
                $evaluation,
                $timezone
            );
            $liveexperience = $report->get_experience();
            $viewdata = $builder->build(
                $evaluation,
                $report,
                $firstname,
                $sitename,
                $timezone->getName()
            );
        }
    }
} else {
    $viewdata = $builder->build_demo($mode, $firstname, $sitename);
}

$rootid = 'local-learningcelebration-preview-root';
if ($viewdata !== null) {
    $PAGE->requires->js_call_amd('local_learningcelebration/celebration', 'init', [$rootid]);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('previewpage', 'local_learningcelebration'));
echo $OUTPUT->notification(get_string('previewintro', 'local_learningcelebration'), 'info');

echo html_writer::start_div('local-learningcelebration-preview');
echo html_writer::start_div('lc-preview-toolbar');
echo html_writer::start_div('lc-preview-toolbar__field');
echo html_writer::label(
    get_string('previewmode', 'local_learningcelebration'),
    'lc-preview-mode',
    false,
    ['class' => 'font-weight-bold d-block mb-1']
);
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => (new moodle_url('/local/learningcelebration/preview.php'))->out(false),
    'class' => 'd-flex',
]);
echo html_writer::select($experiences, 'mode', $mode, false, [
    'id' => 'lc-preview-mode',
    'class' => 'custom-select mr-2',
]);
echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'class' => 'btn btn-secondary',
    'value' => get_string('previewapply', 'local_learningcelebration'),
]);
echo html_writer::end_tag('form');
echo html_writer::end_div();

echo html_writer::start_div('lc-preview-toolbar__actions');
echo html_writer::link(
    new moodle_url('/local/learningcelebration/status.php'),
    get_string('previewopenstatus', 'local_learningcelebration'),
    ['class' => 'btn btn-secondary']
);
echo html_writer::link(
    new moodle_url('/admin/settings.php', ['section' => 'local_learningcelebration']),
    get_string('opensettings', 'local_learningcelebration'),
    ['class' => 'btn btn-secondary']
);
echo html_writer::end_div();
echo html_writer::end_div();

if ($mode === 'live' && $viewdata === null) {
    echo $OUTPUT->notification(get_string('previewliveunavailable', 'local_learningcelebration'), 'warning');
} else if ($mode === 'live' && $liveexperience !== null) {
    $labels = [
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY =>
            get_string('analytics_experience_celebrationonly', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME =>
            get_string('analytics_experience_birthdaywelcome', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_LEARNING_YEAR =>
            get_string('analytics_experience_learningyear', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON =>
            get_string('analytics_experience_learningyearcomparison', 'local_learningcelebration'),
    ];
    echo $OUTPUT->notification(
        get_string('previewliveresult', 'local_learningcelebration', $labels[$liveexperience]),
        'success'
    );
}

if ($viewdata !== null) {
    echo html_writer::start_div('', ['id' => $rootid]);
    echo $OUTPUT->render_from_template('local_learningcelebration/celebration', $viewdata);
    echo html_writer::end_div();
}

echo html_writer::end_div();
echo $OUTPUT->footer();
