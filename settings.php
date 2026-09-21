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
 * Administrative settings for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_learningcelebration',
        get_string('settings', 'local_learningcelebration')
    );

    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading(
        'local_learningcelebration/generalheading',
        get_string('generalsettings', 'local_learningcelebration'),
        get_string('generalsettings_desc', 'local_learningcelebration')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/enabled',
        get_string('enabled', 'local_learningcelebration'),
        get_string('enabled_desc', 'local_learningcelebration'),
        0
    ));

    $settings->add(new admin_setting_configselect(
        'local_learningcelebration/birthdayfield',
        get_string('birthdayfield', 'local_learningcelebration'),
        get_string('birthdayfield_desc', 'local_learningcelebration'),
        '',
        \local_learningcelebration\local\admin\setting_helper::get_datetime_profile_fields()
    ));

    $settings->add(new admin_setting_configselect(
        'local_learningcelebration/celebrationwindow',
        get_string('celebrationwindow', 'local_learningcelebration'),
        get_string('celebrationwindow_desc', 'local_learningcelebration'),
        '3',
        [
            '0' => get_string('window_birthdayonly', 'local_learningcelebration'),
            '1' => get_string('window_oneday', 'local_learningcelebration'),
            '3' => get_string('window_threedays', 'local_learningcelebration'),
            '7' => get_string('window_sevendays', 'local_learningcelebration'),
        ]
    ));

    $settings->add(new admin_setting_configselect(
        'local_learningcelebration/leapdaypolicy',
        get_string('leapdaypolicy', 'local_learningcelebration'),
        get_string('leapdaypolicy_desc', 'local_learningcelebration'),
        \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_FEBRUARY_28,
        [
            \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_FEBRUARY_28 =>
                get_string('leapday_feb28', 'local_learningcelebration'),
            \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_MARCH_1 =>
                get_string('leapday_mar1', 'local_learningcelebration'),
        ]
    ));

    $settings->add(new admin_setting_heading(
        'local_learningcelebration/analyticsheading',
        get_string('analyticssettings', 'local_learningcelebration'),
        get_string('analyticssettings_desc', 'local_learningcelebration')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/showcompletedcourses',
        get_string('showcompletedcourses', 'local_learningcelebration'),
        get_string('showcompletedcourses_desc', 'local_learningcelebration'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/showcompletedactivities',
        get_string('showcompletedactivities', 'local_learningcelebration'),
        get_string('showcompletedactivities_desc', 'local_learningcelebration'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/showbadges',
        get_string('showbadges', 'local_learningcelebration'),
        get_string('showbadges_desc', 'local_learningcelebration'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/showgrades',
        get_string('showgrades', 'local_learningcelebration'),
        get_string('showgrades_desc', 'local_learningcelebration'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/showactiveenrolments',
        get_string('showactiveenrolments', 'local_learningcelebration'),
        get_string('showactiveenrolments_desc', 'local_learningcelebration'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/showcomparison',
        get_string('showcomparison', 'local_learningcelebration'),
        get_string('showcomparison_desc', 'local_learningcelebration'),
        1
    ));

    $settings->add(new admin_setting_heading(
        'local_learningcelebration/presentationheading',
        get_string('presentationsettings', 'local_learningcelebration'),
        get_string('presentationsettings_desc', 'local_learningcelebration')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/enablemotion',
        get_string('enablemotion', 'local_learningcelebration'),
        get_string('enablemotion_desc', 'local_learningcelebration'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningcelebration/enableconfetti',
        get_string('enableconfetti', 'local_learningcelebration'),
        get_string('enableconfetti_desc', 'local_learningcelebration'),
        1
    ));

}

$ADMIN->add('localplugins', new admin_externalpage(
    'local_learningcelebration_status',
    get_string('statuspage', 'local_learningcelebration'),
    new moodle_url('/local/learningcelebration/status.php'),
    'local/learningcelebration:manage'
));

$ADMIN->add('localplugins', new admin_externalpage(
    'local_learningcelebration_preview',
    get_string('previewpage', 'local_learningcelebration'),
    new moodle_url('/local/learningcelebration/preview.php'),
    'local/learningcelebration:manage'
));
