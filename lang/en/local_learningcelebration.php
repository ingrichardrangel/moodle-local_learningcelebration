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
 * English language strings for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actionnotavailable'] = 'This celebration action is no longer available. Reload a suitable Moodle page and try again.';
$string['analytics_accountage'] = 'Account age';
$string['analytics_accountcreated'] = 'Account created';
$string['analytics_activeenrolments'] = 'Current active course enrolments';
$string['analytics_averagecoursegrade'] = 'Average final grade for completed courses';
$string['analytics_bestcourse'] = 'Best completed-course result';
$string['analytics_bestcoursevalue'] = '{$a->grade} — {$a->course}';
$string['analytics_comparisonavailable'] = 'Year-over-year comparison available';
$string['analytics_completedactivities'] = 'Activities completed';
$string['analytics_completedcourses'] = 'Courses completed';
$string['analytics_currentrichness'] = 'Most recent completed-period data richness';
$string['analytics_days'] = '{$a} days';
$string['analytics_earnedbadges'] = 'Badges earned';
$string['analytics_experience'] = 'Suggested celebration experience';
$string['analytics_experience_birthdaywelcome'] = 'Birthday Welcome';
$string['analytics_experience_celebrationonly'] = 'Celebration only';
$string['analytics_experience_learningyear'] = 'Learning Year';
$string['analytics_experience_learningyearcomparison'] = 'Learning Year + year-over-year comparison';
$string['analytics_gradedcompletedcourses'] = 'Completed courses with a visible numeric grade';
$string['analytics_metric'] = 'Metric';
$string['analytics_previousrichness'] = 'Previous completed-period data richness';
$string['analytics_richness_low'] = 'LOW — welcome-level data';
$string['analytics_richness_none'] = 'NONE — no meaningful learning data for this period';
$string['analytics_richness_rich'] = 'RICH — multiple meaningful learning signals';
$string['analytics_richness_standard'] = 'STANDARD — enough data for an annual recap';
$string['analyticsactivitynote'] = 'Activity completions use Moodle\'s current course_modules_completion record and ' .
    'its timemodified value. If a completion state is changed later, its historical period attribution can also change. ' .
    'Learning Celebration does not infer activity history from logs.';
$string['analyticsgradenote'] = 'Course-grade statistics include only visible numeric course grades for courses ' .
    'completed in the period. The completion date determines the period; the displayed percentage uses the current ' .
    'final grade and does not reconstruct a historical grade snapshot.';
$string['analyticsheading'] = 'Learning analytics engine';
$string['analyticsintro'] = 'This read-only diagnostic summarises Moodle core learning data for the two completed ' .
    'birthday-to-birthday periods calculated above. No analytics snapshot is stored by Learning Celebration.';
$string['analyticssettings'] = 'Learning recap content';
$string['analyticssettings_desc'] = 'Choose which Moodle learning signals may appear in the annual recap. Disabled metrics are ' .
    'removed before data-richness classification, so the experience automatically adapts rather than showing empty ' .
    'placeholders.';
$string['autodisplay_completed'] = 'Celebration completed';
$string['autodisplay_firstviewed'] = 'First recorded display';
$string['autodisplay_preference'] = 'Automatic celebration preference';
$string['autodisplay_recordexists'] = 'Annual view record exists';
$string['autodisplay_reset'] = 'Reset my annual view record for QA';
$string['autodisplay_reset_help'] = 'Deletes only the current administrator’s view record for this celebration year and clears ' .
    'the current-session snooze/completed flags. This is intended for testing the automatic overlay again.';
$string['autodisplay_resetdone'] = 'The annual view record was reset for this administrator.';
$string['autodisplay_timecompleted'] = 'Completion time';
$string['autodisplay_viewcount'] = 'Automatic display count';
$string['autodisplay_year'] = 'Celebration year';
$string['autodisplayheading'] = 'Automatic display state';
$string['autodisplayintro'] = 'Diagnostic state for the current administrator and the most recent observed birthday year. ' .
    'A view record is created only when the real automatic overlay is rendered. Manual replay is read-only and does ' .
    'not increment the display counter.';
$string['birthdayengine'] = 'Birthday engine';
$string['birthdayengine_intro'] = 'The values below are calculated for the currently signed-in administrator using that user\'s ' .
    'Moodle timezone. The displayed periods are the most recently completed birthday-to-birthday learning year and the period ' .
    'immediately before it.';
$string['birthdayfield'] = 'Birthday profile field';
$string['birthdayfield_desc'] = 'Select the Moodle custom profile field that stores each user\'s date of birth. ' .
    'Only Date/Time custom profile fields are listed. Learning Celebration does not copy this value into plugin-owned storage.';
$string['celebrationwindow'] = 'Celebration window';
$string['celebrationwindow_desc'] = 'Choose how many calendar days after the birthday the celebration remains eligible. ' .
    'The birthday itself is always day 0. This also works when the window crosses into a new calendar year.';
$string['choosebirthdayfield'] = 'Choose a date profile field...';
$string['configurationcheck'] = 'Configuration check';
$string['contentpolicyheading'] = 'Content and presentation policy';
$string['contentpolicyintro'] = 'These site-level choices are applied before Learning Celebration chooses an experience. ' .
    'Turning off a learning signal removes it from the learner-facing recap and from the richness decision used to ' .
    'select that recap.';
$string['currentuservalue'] = 'Your own birthday value';
$string['daterange'] = '{$a->start} → {$a->end}';
$string['enableconfetti'] = 'Enable birthday confetti';
$string['enableconfetti_desc'] = 'Show the decorative confetti animation on the birthday hero slide. Confetti is automatically ' .
    'suppressed when motion is disabled or the user prefers reduced motion.';
$string['enabled'] = 'Enable Learning Celebration';
$string['enabled_desc'] = 'Enables Learning Celebration, including birthday diagnostics, previews and automatic ' .
    'learner-facing celebrations on eligible Moodle pages.';
$string['enablemotion'] = 'Enable motion effects';
$string['enablemotion_desc'] = 'Enable plugin-authored slide and overlay transitions. Users who request reduced motion at ' .
    'operating-system or browser level still receive a reduced-motion experience.';
$string['engine_birthdaytoday'] = 'Birthday today';
$string['engine_currentperiod'] = 'Most recent completed learning period';
$string['engine_daysafter'] = 'Days after most recent birthday';
$string['engine_daysuntil'] = 'Days until next birthday';
$string['engine_eligible'] = 'Inside celebration window';
$string['engine_lastbirthday'] = 'Most recent observed birthday';
$string['engine_nextbirthday'] = 'Next observed birthday';
$string['engine_previousperiod'] = 'Previous learning period';
$string['engine_state'] = 'Celebration state';
$string['engine_state_birthday'] = 'Birthday today';
$string['engine_state_delayed'] = 'Late celebration window';
$string['engine_state_outside'] = 'Outside celebration window';
$string['engine_storedbirthday'] = 'Stored birthday';
$string['engine_timezone'] = 'User timezone';
$string['engine_today'] = 'Today';
$string['fieldtype'] = 'Profile field type';
$string['fieldtype_datetime'] = 'Date/Time';
$string['generalsettings'] = 'General settings';
$string['generalsettings_desc'] = 'Configure the birthday source and the calendar rules used by Learning Celebration.';
$string['leapday_feb28'] = 'Celebrate on 28 February in non-leap years';
$string['leapday_mar1'] = 'Celebrate on 1 March in non-leap years';
$string['leapdaypolicy'] = '29 February policy';
$string['leapdaypolicy_desc'] = 'For users born on 29 February, choose the observed birthday in non-leap years. ' .
    'In leap years, 29 February is always used.';
$string['learningcelebration:manage'] = 'Access Learning Celebration diagnostics and QA tools';
$string['learningcelebration:view'] = 'View and use Learning Celebration';
$string['manageprofilefields'] = 'Manage custom profile fields';
$string['notavailable'] = 'Not available';
$string['openpreview'] = 'Open celebration preview';
$string['opensettings'] = 'Open plugin settings';
$string['overlay_actionerror'] = 'Moodle could not save this choice. Please try again.';
$string['overlay_dialoglabel'] = 'Your annual Learning Celebration';
$string['overlay_finish'] = 'Continue to Moodle';
$string['overlay_notnow'] = 'Remind me later';
$string['pluginname'] = 'Learning Celebration';
$string['policy_activeenrolments'] = 'Active enrolments in Birthday Welcome';
$string['policy_badges'] = 'Badges';
$string['policy_comparison'] = 'Year-over-year comparison';
$string['policy_completedactivities'] = 'Completed activities';
$string['policy_completedcourses'] = 'Completed courses';
$string['policy_confetti'] = 'Birthday confetti';
$string['policy_disabledmetric'] = 'Disabled by site setting';
$string['policy_grades'] = 'Course grades and best-course highlight';
$string['policy_motion'] = 'Motion effects';
$string['pref_autoshow'] = 'Show my annual Learning Celebration automatically';
$string['pref_autoshow_desc'] = 'When enabled, Learning Celebration may open on the first suitable Moodle page you visit ' .
    'during your birthday window. It never opens on administrative pages, login pages, embedded/pop-up layouts, ' .
    'or active quiz attempts.';
$string['preferencesintro'] = 'Choose whether Moodle may automatically show your annual Learning Celebration when you are ' .
    'inside the configured birthday window. Manual replay remains available when a completed celebration exists.';
$string['preferencespage'] = 'Learning Celebration preferences';
$string['presentationsettings'] = 'Presentation';
$string['presentationsettings_desc'] = 'Control optional visual effects. Browser-level reduced-motion preferences are always ' .
    'respected even when motion is enabled here.';
$string['previewapply'] = 'Load preview';
$string['previewintro'] = 'Administrator-only visual QA. Preview the celebration with the current administrator\'s real data ' .
    'or switch to deterministic demonstration datasets. Live preview applies the configured learning-content policy; ' .
    'demonstration datasets remain available to inspect each visual layout. Site-level motion and confetti settings ' .
    'apply to all previews. This page never marks a celebration as viewed.';
$string['previewliveresult'] = 'The analytics engine selected this experience for the current administrator: {$a}';
$string['previewliveunavailable'] = 'A live preview cannot be built for this administrator yet. Configure a valid birthday ' .
    'field and store a birthday value for this user, or select one of the demonstration datasets above.';
$string['previewmode'] = 'Preview dataset';
$string['previewmode_live'] = 'Current administrator — real Moodle data';
$string['previewopenstatus'] = 'Open diagnostics';
$string['previewpage'] = 'Celebration preview';
$string['privacy:metadata:preference:autoshow'] = 'Whether automatic annual Learning Celebrations are enabled for the user.';
$string['privacy:metadata:views'] = 'Records that an annual Learning Celebration was displayed and whether the user completed it.';
$string['privacy:metadata:views:celebrationyear'] = 'The observed birthday year associated with the celebration.';
$string['privacy:metadata:views:completed'] = 'Whether the annual celebration was completed.';
$string['privacy:metadata:views:timecompleted'] = 'When the annual celebration was completed.';
$string['privacy:metadata:views:timefirstviewed'] = 'The first time this annual celebration was displayed.';
$string['privacy:metadata:views:timelastviewed'] = 'The most recent time this annual celebration was displayed or completed.';
$string['privacy:metadata:views:userid'] = 'The user who received the celebration.';
$string['privacy:metadata:views:viewcount'] = 'The number of times the automatic annual celebration overlay was rendered.';
$string['privacy:path:views'] = 'Learning Celebration views';
$string['profilefieldchoice'] = '{$a->category} — {$a->name} ({$a->shortname})';
$string['replayintro'] = 'A replay is generated from the same birthday-to-birthday period using the learning records ' .
    'currently available in Moodle. Learning Celebration does not store a separate analytics snapshot.';
$string['replaylink'] = 'Replay my Learning Celebration';
$string['replaynone'] = 'There is no completed Learning Celebration available to replay yet.';
$string['replaypage'] = 'My Learning Celebration';
$string['replayunavailable'] = 'Learning Celebration is not currently configured with a valid birthday field.';
$string['resetsimulation'] = 'Reset to current values';
$string['runsimulation'] = 'Run simulation';
$string['settings'] = 'Learning Celebration';
$string['showactiveenrolments'] = 'Include active enrolments in Birthday Welcome';
$string['showactiveenrolments_desc'] = 'Allow the number of currently active course enrolments to appear as contextual ' .
    'information for learners with little annual history.';
$string['showbadges'] = 'Include earned badges';
$string['showbadges_desc'] = 'Allow Moodle badge awards to contribute to the recap and to data-richness classification.';
$string['showcomparison'] = 'Allow year-over-year comparison';
$string['showcomparison_desc'] = 'When both completed learning periods contain enough enabled data, allow a neutral side-by-side ' .
    'comparison. Disabling this never removes the annual recap itself.';
$string['showcompletedactivities'] = 'Include completed activities';
$string['showcompletedactivities_desc'] = 'Allow activity-completion counts to contribute to the recap and to data-richness ' .
    'classification.';
$string['showcompletedcourses'] = 'Include completed courses';
$string['showcompletedcourses_desc'] = 'Allow course-completion counts to contribute to the recap and to data-richness ' .
    'classification.';
$string['showgrades'] = 'Include course grades';
$string['showgrades_desc'] = 'Allow visible numeric final course grades to appear as an average and best-course highlight. ' .
    'Disable this if the institution does not want grades shown in celebrations.';
$string['simulatedbirthday'] = 'Simulated birthday';
$string['simulatedtoday'] = 'Simulated current date';
$string['simulationinvalid'] = 'The simulation could not run. Check that both dates are valid calendar dates, that ' .
    'the birthday is not later than the simulated current date, and then try again.';
$string['simulationleapdaypolicy'] = 'Simulation 29 February policy';
$string['simulationresult'] = 'Simulation result';
$string['simulationwindow'] = 'Simulation celebration window';
$string['simulatorheading'] = 'Birthday engine simulator';
$string['simulatorintro'] = 'Administrator-only QA tool. Enter a birthday and a simulated current date to test the ' .
    'engine without changing the user profile, server clock or Moodle configuration. Simulation values are used only ' .
    'for this request and are not stored.';
$string['statusintro'] = 'This diagnostic page validates the plugin configuration, site-level content policy and ' .
    'birthday engine. It also includes a non-persistent administrator simulator and read-only learning analytics ' .
    'It does not trigger the visual birthday experience or store analytics snapshots.';
$string['statusitem'] = 'Check';
$string['statuspage'] = 'Configuration status';
$string['statusvalue'] = 'Value';
$string['userswithbirthday'] = 'Users with a stored value';
$string['valueavailable'] = 'A value is available';
$string['valuemissing'] = 'No value is stored';
$string['visual_brand'] = '{$a} · Learning Celebration';
$string['visual_comparison_body'] = 'These values compare the two completed birthday-to-birthday periods without ranking ' .
    'or judging your performance.';
$string['visual_comparison_eyebrow'] = 'Two learning years';
$string['visual_comparison_title'] = 'Your recent history, side by side';
$string['visual_demo_course'] = 'Artificial Intelligence for Educators';
$string['visual_demo_period'] = 'A demonstration of the birthday-to-birthday period used by Learning Celebration.';
$string['visual_final_body'] = 'Keep learning at your own pace, one meaningful step at a time.';
$string['visual_final_eyebrow'] = 'A new year begins';
$string['visual_final_title'] = 'That was your year. Now comes the next one.';
$string['visual_firststeps_body'] = 'You are still building your history here. We will celebrate the meaningful milestones ' .
    'that are already available without filling the experience with empty statistics.';
$string['visual_firststeps_eyebrow'] = 'Your first steps';
$string['visual_firststeps_title'] = 'Your journey has already begun';
$string['visual_hero_body_learning'] = 'Today we are celebrating you — and taking a look at the learning journey that ' .
    'filled your last year.';
$string['visual_hero_body_simple'] = 'Today is about celebrating another year of you. A new learning year begins from here.';
$string['visual_hero_body_welcome'] = 'Your learning journey here is just getting started, and today is already a good ' .
    'reason to celebrate.';
$string['visual_hero_eyebrow'] = 'A day worth celebrating';
$string['visual_hero_title'] = 'Happy birthday, {$a}!';
$string['visual_highlight_eyebrow'] = 'A standout result';
$string['visual_highlight_title'] = 'One course stood out this year';
$string['visual_learningyear_body'] = 'This recap covers the completed learning period immediately before your birthday.';
$string['visual_learningyear_eyebrow'] = 'Your learning year';
$string['visual_learningyear_title'] = 'One year, viewed from birthday to birthday';
$string['visual_metric_activities'] = 'activities completed';
$string['visual_metric_averagegrade'] = 'average final grade';
$string['visual_metric_badges'] = 'badges earned';
$string['visual_metric_courses'] = 'courses completed';
$string['visual_metric_coursesenrolled'] = 'active course enrolments';
$string['visual_next'] = 'Next';
$string['visual_previous'] = 'Previous';
$string['visual_previousyear'] = 'Previous year';
$string['visual_progress'] = 'Celebration progress';
$string['visual_quiet_body'] = 'There is not enough activity yet for an annual recap, so we are keeping the focus on ' .
    'your birthday and what comes next.';
$string['visual_quiet_eyebrow'] = 'Your next chapter';
$string['visual_quiet_title'] = 'A new learning year starts today';
$string['visual_restart'] = 'Replay';
$string['visual_slide'] = 'Go to slide {$a}';
$string['visual_thisyear'] = 'Recent year';
$string['visual_yearinnumbers_eyebrow'] = 'Your year in numbers';
$string['visual_yearinnumbers_title'] = 'A few things you accomplished';
$string['warningdisabled'] = 'Learning Celebration is currently disabled.';
$string['warninginvalidfield'] = 'The configured birthday field no longer exists or is not a Date/Time profile field. ' .
    'Select another field in the plugin settings.';
$string['warningnofieldconfigured'] = 'No birthday profile field has been selected yet.';
$string['window_birthdayonly'] = 'Birthday only';
$string['window_custom'] = 'Birthday + {$a} days';
$string['window_oneday'] = 'Birthday + 1 day';
$string['window_sevendays'] = 'Birthday + 7 days';
$string['window_threedays'] = 'Birthday + 3 days';
