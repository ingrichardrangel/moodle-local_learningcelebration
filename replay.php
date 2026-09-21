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
 * Replay the current user's most recent completed annual celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();
require_capability('local/learningcelebration:view', context_system::instance());

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/learningcelebration/replay.php'));
$PAGE->set_title(get_string('replaypage', 'local_learningcelebration'));
$PAGE->set_heading(fullname($USER));

$config = get_config('local_learningcelebration');
$viewdata = null;
$message = null;

if (empty($config->enabled) || empty($config->birthdayfield)) {
    $message = get_string('replayunavailable', 'local_learningcelebration');
} else {
    $views = new \local_learningcelebration\local\celebration\view_repository();
    $latestview = $views->get_latest_completed_record((int) $USER->id);

    if (!$latestview) {
        $message = get_string('replaynone', 'local_learningcelebration');
    } else {
        $profilerepository = new \local_learningcelebration\local\profile_field_repository();
        $field = $profilerepository->get_datetime_field((string) $config->birthdayfield);

        if ($field) {
            $birthtimestamp = $profilerepository->get_user_value((int) $USER->id, (int) $field->id);
            if ($birthtimestamp !== null) {
                $timezone = \core_date::get_user_timezone_object($USER);
                $windowdays = isset($config->celebrationwindow) ? max(0, (int) $config->celebrationwindow) : 3;
                $leapdaypolicy = $config->leapdaypolicy
                    ?? \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_FEBRUARY_28;

                $birthdate = (new DateTimeImmutable('@' . $birthtimestamp))->setTimezone($timezone);
                $month = (int) $birthdate->format('n');
                $day = (int) $birthdate->format('j');
                $year = (int) $latestview->celebrationyear;

                if ($month === 2 && $day === 29 && !checkdate(2, 29, $year)) {
                    if ($leapdaypolicy === \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_MARCH_1) {
                        $month = 3;
                        $day = 1;
                    } else {
                        $day = 28;
                    }
                }

                // Midday avoids any edge case around local midnight transitions; the engine normalises to a local date.
                $reference = (new DateTimeImmutable('now', $timezone))
                    ->setDate($year, $month, $day)
                    ->setTime(12, 0, 0);

                $evaluation = (new \local_learningcelebration\local\birthday\birthday_engine())->evaluate(
                    $birthtimestamp,
                    $timezone,
                    $windowdays,
                    $leapdaypolicy,
                    $reference->getTimestamp()
                );

                $report = (new \local_learningcelebration\local\analytics\analytics_engine())->build_report(
                    (int) $USER->id,
                    (int) $USER->timecreated,
                    $evaluation,
                    $timezone,
                    $reference->getTimestamp()
                );
                $sitename = strip_tags(format_string($SITE->shortname ?: $SITE->fullname));
                $viewdata = (new \local_learningcelebration\local\presentation\celebration_view_builder())->build(
                    $evaluation,
                    $report,
                    (string) $USER->firstname,
                    $sitename,
                    $timezone->getName()
                );
            }
        }

        if ($viewdata === null && $message === null) {
            $message = get_string('replayunavailable', 'local_learningcelebration');
        }
    }
}

$rootid = 'local-learningcelebration-replay-root';
if ($viewdata !== null) {
    $PAGE->requires->js_call_amd('local_learningcelebration/celebration', 'init', [$rootid]);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('replaypage', 'local_learningcelebration'));
echo $OUTPUT->notification(get_string('replayintro', 'local_learningcelebration'), 'info');

if ($message !== null) {
    echo $OUTPUT->notification($message, 'warning');
} else {
    echo html_writer::start_div('local-learningcelebration-preview');
    echo html_writer::start_div('', ['id' => $rootid]);
    echo $OUTPUT->render_from_template('local_learningcelebration/celebration', $viewdata);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo $OUTPUT->footer();
