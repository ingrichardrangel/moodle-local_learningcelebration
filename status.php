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
 * Configuration, birthday-engine status and administrator QA simulator.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_learningcelebration_status');
require_capability('local/learningcelebration:manage', context_system::instance());

$PAGE->set_title(get_string('statuspage', 'local_learningcelebration'));
$PAGE->set_heading(get_string('pluginname', 'local_learningcelebration'));

$config = get_config('local_learningcelebration');
$enabled = !empty($config->enabled);
$shortname = $config->birthdayfield ?? '';
$windowdays = isset($config->celebrationwindow) ? max(0, (int) $config->celebrationwindow) : 3;
$leapdaypolicy = $config->leapdaypolicy
    ?? \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_FEBRUARY_28;
$metricpolicy = \local_learningcelebration\local\analytics\metric_policy::from_config();
$presentationoptions = \local_learningcelebration\local\presentation\presentation_options::from_config();

$repository = new \local_learningcelebration\local\profile_field_repository();
$field = $repository->get_datetime_field($shortname);
$timezone = \core_date::get_user_timezone_object($USER);
$timezoneid = $timezone->getName();

$yes = get_string('yes');
$no = get_string('no');
$dateformat = get_string('strftimedaydate', 'core_langconfig');
$formatdate = static function (\DateTimeImmutable $date) use ($dateformat, $timezoneid): string {
    return userdate($date->getTimestamp(), $dateformat, $timezoneid);
};

$statelabels = [
    \local_learningcelebration\local\birthday\birthday_engine::STATE_BIRTHDAY =>
        get_string('engine_state_birthday', 'local_learningcelebration'),
    \local_learningcelebration\local\birthday\birthday_engine::STATE_DELAYED =>
        get_string('engine_state_delayed', 'local_learningcelebration'),
    \local_learningcelebration\local\birthday\birthday_engine::STATE_OUTSIDE =>
        get_string('engine_state_outside', 'local_learningcelebration'),
];

$windowlabels = [
    0 => get_string('window_birthdayonly', 'local_learningcelebration'),
    1 => get_string('window_oneday', 'local_learningcelebration'),
    3 => get_string('window_threedays', 'local_learningcelebration'),
    7 => get_string('window_sevendays', 'local_learningcelebration'),
];

$leapdaylabels = [
    \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_FEBRUARY_28 =>
        get_string('leapday_feb28', 'local_learningcelebration'),
    \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_MARCH_1 =>
        get_string('leapday_mar1', 'local_learningcelebration'),
];

$wraptable = static function (html_table $table): string {
    return html_writer::div(html_writer::table($table), 'table-responsive');
};

$renderpanel = static function (
    string $title,
    string $content,
    ?string $intro = null,
    string $extraclass = ''
) use ($OUTPUT): string {
    $classes = trim('lc-admin-panel ' . $extraclass);
    $panel = html_writer::start_div($classes);
    $panel .= html_writer::start_div('lc-admin-panel__header');
    $panel .= html_writer::tag('h3', $title, ['class' => 'lc-admin-panel__title']);
    if ($intro !== null && $intro !== '') {
        $panel .= html_writer::div($OUTPUT->notification($intro, 'info'), 'lc-admin-panel__intro');
    }
    $panel .= html_writer::end_div();
    $panel .= html_writer::div($content, 'lc-admin-panel__body');
    $panel .= html_writer::end_div();

    return $panel;
};

// Build a diagnostic table for a birthday-engine evaluation.
$makeevaluationtable = static function (
    \local_learningcelebration\local\birthday\evaluation $evaluation,
    bool $simulationmode = false
) use (
    $yes,
    $no,
    $timezoneid,
    $formatdate,
    $statelabels
): html_table {
    $table = new html_table();
    $table->attributes['class'] = 'generaltable';
    $table->head = [
        get_string('statusitem', 'local_learningcelebration'),
        get_string('statusvalue', 'local_learningcelebration'),
    ];

    $table->data[] = [get_string('engine_timezone', 'local_learningcelebration'), s($timezoneid)];

    $birthdaylabel = $simulationmode
        ? get_string('simulatedbirthday', 'local_learningcelebration')
        : get_string('engine_storedbirthday', 'local_learningcelebration');
    $todaylabel = $simulationmode
        ? get_string('simulatedtoday', 'local_learningcelebration')
        : get_string('engine_today', 'local_learningcelebration');

    $table->data = array_merge($table->data, [
        [$birthdaylabel, $formatdate($evaluation->get_date_of_birth())],
        [$todaylabel, $formatdate($evaluation->get_today())],
        [get_string('engine_lastbirthday', 'local_learningcelebration'), $formatdate($evaluation->get_last_birthday())],
        [get_string('engine_nextbirthday', 'local_learningcelebration'), $formatdate($evaluation->get_next_birthday())],
        [get_string('engine_birthdaytoday', 'local_learningcelebration'), $evaluation->is_birthday_today() ? $yes : $no],
        [get_string('engine_eligible', 'local_learningcelebration'), $evaluation->is_eligible() ? $yes : $no],
        [get_string('engine_daysafter', 'local_learningcelebration'), $evaluation->get_days_after_birthday()],
        [get_string('engine_daysuntil', 'local_learningcelebration'), $evaluation->get_days_until_next_birthday()],
        [get_string('engine_state', 'local_learningcelebration'), $statelabels[$evaluation->get_state()]],
        [
            get_string('engine_currentperiod', 'local_learningcelebration'),
            get_string('daterange', 'local_learningcelebration', (object) [
                'start' => $formatdate($evaluation->get_period_start()),
                'end' => $formatdate($evaluation->get_period_end_display()),
            ]),
        ],
        [
            get_string('engine_previousperiod', 'local_learningcelebration'),
            get_string('daterange', 'local_learningcelebration', (object) [
                'start' => $formatdate($evaluation->get_previous_period_start()),
                'end' => $formatdate($evaluation->get_previous_period_end_display()),
            ]),
        ],
    ]);

    return $table;
};

$configtable = new html_table();
$configtable->attributes['class'] = 'generaltable';
$configtable->head = [
    get_string('statusitem', 'local_learningcelebration'),
    get_string('statusvalue', 'local_learningcelebration'),
];
$configtable->data[] = [
    get_string('enabled', 'local_learningcelebration'),
    $enabled ? $yes : $no,
];

$birthtimestamp = null;

if ($field) {
    $context = context_system::instance();
    $displayname = format_string($field->name, true, ['context' => $context]);
    $configtable->data[] = [
        get_string('birthdayfield', 'local_learningcelebration'),
        s($displayname) . ' (' . s($field->shortname) . ')',
    ];
    $configtable->data[] = [
        get_string('fieldtype', 'local_learningcelebration'),
        get_string('fieldtype_datetime', 'local_learningcelebration'),
    ];
    $configtable->data[] = [
        get_string('userswithbirthday', 'local_learningcelebration'),
        $repository->count_populated_values((int) $field->id),
    ];

    $birthtimestamp = $repository->get_user_value((int) $USER->id, (int) $field->id);
    $configtable->data[] = [
        get_string('currentuservalue', 'local_learningcelebration'),
        $birthtimestamp !== null
            ? get_string('valueavailable', 'local_learningcelebration')
            : get_string('valuemissing', 'local_learningcelebration'),
    ];
}

$configtable->data[] = [
    get_string('celebrationwindow', 'local_learningcelebration'),
    $windowlabels[$windowdays] ?? get_string('window_custom', 'local_learningcelebration', $windowdays),
];
$defaultleapdaykey = \local_learningcelebration\local\birthday\birthday_engine::LEAPDAY_FEBRUARY_28;
$defaultleapdaylabel = $leapdaylabels[$defaultleapdaykey];
$configtable->data[] = [
    get_string('leapdaypolicy', 'local_learningcelebration'),
    $leapdaylabels[$leapdaypolicy] ?? $defaultleapdaylabel,
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('statuspage', 'local_learningcelebration'));
echo $OUTPUT->notification(get_string('statusintro', 'local_learningcelebration'), 'info');

echo html_writer::start_div('lc-admin-status');

if ($shortname === '') {
    echo $OUTPUT->notification(get_string('warningnofieldconfigured', 'local_learningcelebration'), 'warning');
} else if (!$field) {
    echo $OUTPUT->notification(get_string('warninginvalidfield', 'local_learningcelebration'), 'error');
}

if (!$enabled) {
    echo $OUTPUT->notification(get_string('warningdisabled', 'local_learningcelebration'), 'warning');
}

echo $renderpanel(
    get_string('configurationcheck', 'local_learningcelebration'),
    $wraptable($configtable)
);

$policytable = new html_table();
$policytable->attributes['class'] = 'generaltable';
$policytable->head = [
    get_string('statusitem', 'local_learningcelebration'),
    get_string('statusvalue', 'local_learningcelebration'),
];
$policytable->data = [
    [
        get_string('policy_completedcourses', 'local_learningcelebration'),
        $metricpolicy->includes_completed_courses() ? $yes : $no,
    ],
    [
        get_string('policy_completedactivities', 'local_learningcelebration'),
        $metricpolicy->includes_completed_activities() ? $yes : $no,
    ],
    [get_string('policy_badges', 'local_learningcelebration'), $metricpolicy->includes_badges() ? $yes : $no],
    [get_string('policy_grades', 'local_learningcelebration'), $metricpolicy->includes_grades() ? $yes : $no],
    [get_string('policy_activeenrolments', 'local_learningcelebration'), $metricpolicy->includes_active_enrolments() ? $yes : $no],
    [get_string('policy_comparison', 'local_learningcelebration'), $metricpolicy->allows_comparison() ? $yes : $no],
    [get_string('policy_motion', 'local_learningcelebration'), $presentationoptions->motion_enabled() ? $yes : $no],
    [get_string('policy_confetti', 'local_learningcelebration'), $presentationoptions->confetti_enabled() ? $yes : $no],
];
echo $renderpanel(
    get_string('contentpolicyheading', 'local_learningcelebration'),
    $wraptable($policytable),
    get_string('contentpolicyintro', 'local_learningcelebration')
);

$engine = new \local_learningcelebration\local\birthday\birthday_engine();
$liveevaluation = null;

if ($field && $birthtimestamp !== null) {
    $liveevaluation = $engine->evaluate(
        $birthtimestamp,
        $timezone,
        $windowdays,
        $leapdaypolicy
    );

    echo $renderpanel(
        get_string('birthdayengine', 'local_learningcelebration'),
        $wraptable($makeevaluationtable($liveevaluation)),
        get_string('birthdayengine_intro', 'local_learningcelebration')
    );
}

// Read-only learning analytics diagnostics for the completed birthday-to-birthday periods.
if ($liveevaluation) {
    $analyticsengine = new \local_learningcelebration\local\analytics\analytics_engine();
    $analyticsreport = $analyticsengine->build_report(
        (int) $USER->id,
        (int) $USER->timecreated,
        $liveevaluation,
        $timezone
    );

    $richnesslabels = [
        \local_learningcelebration\local\analytics\data_richness_classifier::NONE =>
            get_string('analytics_richness_none', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::LOW =>
            get_string('analytics_richness_low', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::STANDARD =>
            get_string('analytics_richness_standard', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::RICH =>
            get_string('analytics_richness_rich', 'local_learningcelebration'),
    ];
    $experiencelabels = [
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY =>
            get_string('analytics_experience_celebrationonly', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME =>
            get_string('analytics_experience_birthdaywelcome', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_LEARNING_YEAR =>
            get_string('analytics_experience_learningyear', 'local_learningcelebration'),
        \local_learningcelebration\local\analytics\data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON =>
            get_string('analytics_experience_learningyearcomparison', 'local_learningcelebration'),
    ];

    $formatpercentage = static function (?float $value): string {
        if ($value === null) {
            return get_string('notavailable', 'local_learningcelebration');
        }
        return format_float($value, 1) . '%';
    };
    $formatbestcourse = static function (
        \local_learningcelebration\local\analytics\period_statistics $statistics
    ) use ($formatpercentage): string {
        if ($statistics->get_best_course_grade() === null || $statistics->get_best_course_name() === null) {
            return get_string('notavailable', 'local_learningcelebration');
        }

        $name = $statistics->get_best_course_name();
        $courseid = $statistics->get_best_course_id();
        if ($courseid !== null) {
            $context = context_course::instance($courseid, IGNORE_MISSING);
            if ($context) {
                $name = format_string($name, true, ['context' => $context]);
            }
        }

        return get_string('analytics_bestcoursevalue', 'local_learningcelebration', (object) [
            'grade' => $formatpercentage($statistics->get_best_course_grade()),
            'course' => $name,
        ]);
    };

    $analyticscontent = '';

    $analyticsoverview = new html_table();
    $analyticsoverview->attributes['class'] = 'generaltable';
    $analyticsoverview->head = [
        get_string('statusitem', 'local_learningcelebration'),
        get_string('statusvalue', 'local_learningcelebration'),
    ];
    $analyticsoverview->data = [
        [
            get_string('analytics_accountcreated', 'local_learningcelebration'),
            !empty($USER->timecreated)
                ? userdate((int) $USER->timecreated, $dateformat, $timezoneid)
                : get_string('notavailable', 'local_learningcelebration'),
        ],
        [
            get_string('analytics_accountage', 'local_learningcelebration'),
            $analyticsreport->get_account_age_days() === PHP_INT_MAX
                ? get_string('notavailable', 'local_learningcelebration')
                : get_string('analytics_days', 'local_learningcelebration', $analyticsreport->get_account_age_days()),
        ],
    ];
    if ($metricpolicy->includes_active_enrolments()) {
        $analyticsoverview->data[] = [
            get_string('analytics_activeenrolments', 'local_learningcelebration'),
            $analyticsreport->get_active_enrolments(),
        ];
    }
    $analyticsoverview->data = array_merge($analyticsoverview->data, [
        [
            get_string('analytics_currentrichness', 'local_learningcelebration'),
            $richnesslabels[$analyticsreport->get_current_richness()],
        ],
        [
            get_string('analytics_previousrichness', 'local_learningcelebration'),
            $richnesslabels[$analyticsreport->get_previous_richness()],
        ],
        [
            get_string('analytics_experience', 'local_learningcelebration'),
            $experiencelabels[$analyticsreport->get_experience()],
        ],
        [
            get_string('analytics_comparisonavailable', 'local_learningcelebration'),
            $analyticsreport->is_comparison_available() ? $yes : $no,
        ],
    ]);
    $analyticscontent .= $wraptable($analyticsoverview);

    $currentstatistics = $analyticsreport->get_current_period();
    $previousstatistics = $analyticsreport->get_previous_period();
    $currentperiodlabel = get_string('daterange', 'local_learningcelebration', (object) [
        'start' => $formatdate($liveevaluation->get_period_start()),
        'end' => $formatdate($liveevaluation->get_period_end_display()),
    ]);
    $previousperiodlabel = get_string('daterange', 'local_learningcelebration', (object) [
        'start' => $formatdate($liveevaluation->get_previous_period_start()),
        'end' => $formatdate($liveevaluation->get_previous_period_end_display()),
    ]);

    $analyticstable = new html_table();
    $analyticstable->attributes['class'] = 'generaltable';
    $analyticstable->head = [
        get_string('analytics_metric', 'local_learningcelebration'),
        $currentperiodlabel,
        $previousperiodlabel,
    ];
    $analyticstable->data = [];
    if ($metricpolicy->includes_completed_courses()) {
        $analyticstable->data[] = [
            get_string('analytics_completedcourses', 'local_learningcelebration'),
            $currentstatistics->get_completed_courses(),
            $previousstatistics->get_completed_courses(),
        ];
    }
    if ($metricpolicy->includes_completed_activities()) {
        $analyticstable->data[] = [
            get_string('analytics_completedactivities', 'local_learningcelebration'),
            $currentstatistics->get_completed_activities(),
            $previousstatistics->get_completed_activities(),
        ];
    }
    if ($metricpolicy->includes_badges()) {
        $analyticstable->data[] = [
            get_string('analytics_earnedbadges', 'local_learningcelebration'),
            $currentstatistics->get_earned_badges(),
            $previousstatistics->get_earned_badges(),
        ];
    }
    if ($metricpolicy->includes_grades()) {
        $analyticstable->data[] = [
            get_string('analytics_gradedcompletedcourses', 'local_learningcelebration'),
            $currentstatistics->get_graded_completed_courses(),
            $previousstatistics->get_graded_completed_courses(),
        ];
        $analyticstable->data[] = [
            get_string('analytics_averagecoursegrade', 'local_learningcelebration'),
            $formatpercentage($currentstatistics->get_average_course_grade()),
            $formatpercentage($previousstatistics->get_average_course_grade()),
        ];
        $analyticstable->data[] = [
            get_string('analytics_bestcourse', 'local_learningcelebration'),
            $formatbestcourse($currentstatistics),
            $formatbestcourse($previousstatistics),
        ];
    }
    if (empty($analyticstable->data)) {
        $analyticstable->data[] = [
            get_string('analytics_metric', 'local_learningcelebration'),
            get_string('policy_disabledmetric', 'local_learningcelebration'),
            get_string('policy_disabledmetric', 'local_learningcelebration'),
        ];
    }
    $analyticscontent .= $wraptable($analyticstable);
    if ($metricpolicy->includes_completed_activities()) {
        $analyticscontent .= $OUTPUT->notification(get_string('analyticsactivitynote', 'local_learningcelebration'), 'info');
    }
    if ($metricpolicy->includes_grades()) {
        $analyticscontent .= $OUTPUT->notification(get_string('analyticsgradenote', 'local_learningcelebration'), 'info');
    }

    echo $renderpanel(
        get_string('analyticsheading', 'local_learningcelebration'),
        $analyticscontent,
        get_string('analyticsintro', 'local_learningcelebration')
    );
}

// Automatic-display state for the current administrator.
if ($liveevaluation) {
    $celebrationyear = (int) $liveevaluation->get_last_birthday()->format('Y');
    $viewrepository = new \local_learningcelebration\local\celebration\view_repository();
    $viewrecord = $viewrepository->get_record((int) $USER->id, $celebrationyear);
    $autoshow = (bool) get_user_preferences(
        \local_learningcelebration\local\celebration\auto_display_service::PREF_AUTOSHOW,
        1,
        $USER->id
    );

    $autocontent = '';

    $autotable = new html_table();
    $autotable->attributes['class'] = 'generaltable';
    $autotable->head = [
        get_string('statusitem', 'local_learningcelebration'),
        get_string('statusvalue', 'local_learningcelebration'),
    ];
    $autotable->data = [
        [get_string('autodisplay_preference', 'local_learningcelebration'), $autoshow ? $yes : $no],
        [get_string('autodisplay_year', 'local_learningcelebration'), $celebrationyear],
        [get_string('autodisplay_recordexists', 'local_learningcelebration'), $viewrecord ? $yes : $no],
        [
            get_string('autodisplay_completed', 'local_learningcelebration'),
            $viewrecord && !empty($viewrecord->completed) ? $yes : $no,
        ],
        [
            get_string('autodisplay_viewcount', 'local_learningcelebration'),
            $viewrecord ? (int) $viewrecord->viewcount : 0,
        ],
        [
            get_string('autodisplay_firstviewed', 'local_learningcelebration'),
            $viewrecord
                ? userdate((int) $viewrecord->timefirstviewed, $dateformat, $timezoneid)
                : get_string('notavailable', 'local_learningcelebration'),
        ],
        [
            get_string('autodisplay_timecompleted', 'local_learningcelebration'),
            $viewrecord && !empty($viewrecord->timecompleted)
                ? userdate((int) $viewrecord->timecompleted, $dateformat, $timezoneid)
                : get_string('notavailable', 'local_learningcelebration'),
        ],
    ];
    $autocontent .= $wraptable($autotable);

    if ($viewrecord) {
        $reseturl = new moodle_url('/local/learningcelebration/reset.php', [
            'celebrationyear' => $celebrationyear,
            'sesskey' => sesskey(),
        ]);
        $autocontent .= html_writer::div(
            $OUTPUT->single_button(
                $reseturl,
                get_string('autodisplay_reset', 'local_learningcelebration'),
                'post',
                ['class' => 'btn-secondary']
            ) . html_writer::div(
                get_string('autodisplay_reset_help', 'local_learningcelebration'),
                'text-muted small lc-admin-resethelp'
            ),
            'lc-admin-reset'
        );
    }

    echo $renderpanel(
        get_string('autodisplayheading', 'local_learningcelebration'),
        $autocontent,
        get_string('autodisplayintro', 'local_learningcelebration')
    );
}

// Administrator-only, non-persistent birthday-engine simulator.
$runsimulation = optional_param('runsimulation', 0, PARAM_BOOL);
$defaultbirthday = $liveevaluation ? $liveevaluation->get_date_of_birth()->format('Y-m-d') : '';
$defaulttoday = (new \DateTimeImmutable('now', $timezone))->format('Y-m-d');
$simbirthday = optional_param('simbirthday', $defaultbirthday, PARAM_TEXT);
$simtoday = optional_param('simtoday', $defaulttoday, PARAM_TEXT);
$simwindow = optional_param('simwindow', $windowdays, PARAM_INT);
$simleapdaypolicy = optional_param('simleapdaypolicy', $leapdaypolicy, PARAM_ALPHANUMEXT);

if (!array_key_exists($simwindow, $windowlabels)) {
    $simwindow = $windowdays;
}
if (!array_key_exists($simleapdaypolicy, $leapdaylabels)) {
    $simleapdaypolicy = $leapdaypolicy;
}

$form = html_writer::start_tag('form', [
    'method' => 'post',
    'action' => new moodle_url('/local/learningcelebration/status.php'),
    'class' => 'lc-admin-simulator',
]);
$form .= html_writer::empty_tag('input', [
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey(),
]);
$form .= html_writer::empty_tag('input', [
    'type' => 'hidden',
    'name' => 'runsimulation',
    'value' => 1,
]);

$form .= html_writer::start_div('row');
$form .= html_writer::start_div('col-md-6 mb-3');
$form .= html_writer::tag('label', get_string('simulatedbirthday', 'local_learningcelebration'), [
    'for' => 'id_simbirthday',
    'class' => 'font-weight-bold',
]);
$form .= html_writer::empty_tag('input', [
    'type' => 'date',
    'id' => 'id_simbirthday',
    'name' => 'simbirthday',
    'value' => $simbirthday,
    'class' => 'form-control',
    'required' => 'required',
]);
$form .= html_writer::end_div();

$form .= html_writer::start_div('col-md-6 mb-3');
$form .= html_writer::tag('label', get_string('simulatedtoday', 'local_learningcelebration'), [
    'for' => 'id_simtoday',
    'class' => 'font-weight-bold',
]);
$form .= html_writer::empty_tag('input', [
    'type' => 'date',
    'id' => 'id_simtoday',
    'name' => 'simtoday',
    'value' => $simtoday,
    'class' => 'form-control',
    'required' => 'required',
]);
$form .= html_writer::end_div();
$form .= html_writer::end_div();

$form .= html_writer::start_div('row');
$form .= html_writer::start_div('col-md-6 mb-3');
$form .= html_writer::tag('label', get_string('simulationwindow', 'local_learningcelebration'), [
    'for' => 'id_simwindow',
    'class' => 'font-weight-bold',
]);
$form .= html_writer::select($windowlabels, 'simwindow', $simwindow, false, [
    'id' => 'id_simwindow',
    'class' => 'form-control',
]);
$form .= html_writer::end_div();

$form .= html_writer::start_div('col-md-6 mb-3');
$form .= html_writer::tag('label', get_string('simulationleapdaypolicy', 'local_learningcelebration'), [
    'for' => 'id_simleapdaypolicy',
    'class' => 'font-weight-bold',
]);
$form .= html_writer::select($leapdaylabels, 'simleapdaypolicy', $simleapdaypolicy, false, [
    'id' => 'id_simleapdaypolicy',
    'class' => 'form-control',
]);
$form .= html_writer::end_div();
$form .= html_writer::end_div();

$form .= html_writer::start_div('lc-admin-simulator__actions');
$form .= html_writer::tag('button', get_string('runsimulation', 'local_learningcelebration'), [
    'type' => 'submit',
    'class' => 'btn btn-primary',
]);
$form .= html_writer::link(
    new moodle_url('/local/learningcelebration/status.php'),
    get_string('resetsimulation', 'local_learningcelebration'),
    ['class' => 'btn btn-secondary']
);
$form .= html_writer::end_div();
$form .= html_writer::end_tag('form');

$simulatorcontent = $form;

if ($runsimulation) {
    require_sesskey();

    try {
        $simulationservice = new \local_learningcelebration\local\admin\simulation_service($engine);
        $simulation = $simulationservice->evaluate(
            $simbirthday,
            $simtoday,
            $timezone,
            $simwindow,
            $simleapdaypolicy
        );

        $simulatorcontent .= html_writer::tag(
            'h4',
            get_string('simulationresult', 'local_learningcelebration'),
            ['class' => 'lc-admin-panel__subtitle']
        );
        $simulatorcontent .= $wraptable($makeevaluationtable($simulation, true));
    } catch (\InvalidArgumentException $exception) {
        $simulatorcontent .= $OUTPUT->notification(get_string('simulationinvalid', 'local_learningcelebration'), 'error');
    }
}

echo $renderpanel(
    get_string('simulatorheading', 'local_learningcelebration'),
    $simulatorcontent,
    get_string('simulatorintro', 'local_learningcelebration')
);

$actions = html_writer::div(
    html_writer::link(
        new moodle_url('/admin/settings.php', ['section' => 'local_learningcelebration']),
        get_string('opensettings', 'local_learningcelebration'),
        ['class' => 'btn btn-primary']
    ) .
    html_writer::link(
        new moodle_url('/local/learningcelebration/preview.php'),
        get_string('openpreview', 'local_learningcelebration'),
        ['class' => 'btn btn-secondary']
    ) .
    html_writer::link(
        new moodle_url('/user/profile/index.php'),
        get_string('manageprofilefields', 'local_learningcelebration'),
        ['class' => 'btn btn-secondary']
    ),
    'lc-admin-actions'
);

echo html_writer::div($actions, 'lc-admin-panel lc-admin-panel--actions');
echo html_writer::end_div();
echo $OUTPUT->footer();
