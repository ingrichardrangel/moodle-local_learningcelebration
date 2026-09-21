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

namespace local_learningcelebration\local\presentation;

use local_learningcelebration\local\analytics\analytics_report;
use local_learningcelebration\local\analytics\data_richness_classifier;
use local_learningcelebration\local\analytics\period_statistics;
use local_learningcelebration\local\birthday\evaluation;

/**
 * Builds template-safe data for the Learning Celebration visual experience.
 *
 * This class deliberately contains presentation decisions only. It does not query
 * Moodle data or persist anything.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class celebration_view_builder {
    /** @var presentation_options Site-level presentation options. */
    private presentation_options $options;

    /**
     * Constructor.
     *
     * @param presentation_options|null $options Optional presentation options for testing.
     */
    public function __construct(?presentation_options $options = null) {
        $this->options = $options ?? presentation_options::from_config();
    }

    /**
     * Build the live celebration preview from validated engine results.
     *
     * @param evaluation $evaluation Birthday evaluation.
     * @param analytics_report $report Learning analytics report.
     * @param string $firstname User first name.
     * @param string $sitename Moodle site name.
     * @param string $timezoneid User timezone id.
     * @return array Mustache context.
     */
    public function build(
        evaluation $evaluation,
        analytics_report $report,
        string $firstname,
        string $sitename,
        string $timezoneid
    ): array {
        $experience = $report->get_experience();
        $current = $report->get_current_period();
        $previous = $report->get_previous_period();
        $slides = [];

        $slides[] = $this->hero_slide($firstname, $experience);

        if ($experience === data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY) {
            $slides[] = $this->quiet_year_slide();
        } else if ($experience === data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME) {
            $slides[] = $this->welcome_slide($report, $current);
        } else {
            $slides[] = $this->period_slide($evaluation, $timezoneid);
            $slides[] = $this->metrics_slide($current);

            if ($current->get_best_course_grade() !== null && $current->get_best_course_name() !== null) {
                $slides[] = $this->highlight_slide($current);
            }

            if ($experience === data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON) {
                $slides[] = $this->comparison_slide($current, $previous);
            }
        }

        $slides[] = $this->final_slide();

        return $this->wrap_context($slides, $firstname, $sitename, $experience, false);
    }

    /**
     * Build one of the deterministic demonstration experiences used by administrators and reviewers.
     *
     * @param string $experience Experience constant.
     * @param string $firstname Display first name.
     * @param string $sitename Site name.
     * @return array Mustache context.
     */
    public function build_demo(string $experience, string $firstname, string $sitename): array {
        $supported = [
            data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY,
            data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME,
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR,
            data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON,
        ];
        if (!in_array($experience, $supported, true)) {
            throw new \InvalidArgumentException('Unsupported celebration preview experience.');
        }

        $slides = [$this->hero_slide($firstname, $experience)];

        if ($experience === data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY) {
            $slides[] = $this->quiet_year_slide();
        } else if ($experience === data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME) {
            $slides[] = [
                'kind' => 'welcome',
                'eyebrow' => get_string('visual_firststeps_eyebrow', 'local_learningcelebration'),
                'title' => get_string('visual_firststeps_title', 'local_learningcelebration'),
                'body' => get_string('visual_firststeps_body', 'local_learningcelebration'),
                'hasmetrics' => true,
                'metrics' => [
                    $this->metric('2', get_string('visual_metric_coursesenrolled', 'local_learningcelebration')),
                    $this->metric('3', get_string('visual_metric_activities', 'local_learningcelebration')),
                ],
            ];
        } else {
            $slides[] = [
                'kind' => 'period',
                'eyebrow' => get_string('visual_learningyear_eyebrow', 'local_learningcelebration'),
                'title' => get_string('visual_learningyear_title', 'local_learningcelebration'),
                'body' => get_string('visual_demo_period', 'local_learningcelebration'),
                'hasperiod' => true,
                'periodstart' => '18 Sep 2025',
                'periodend' => '17 Sep 2026',
            ];
            $slides[] = [
                'kind' => 'metrics',
                'eyebrow' => get_string('visual_yearinnumbers_eyebrow', 'local_learningcelebration'),
                'title' => get_string('visual_yearinnumbers_title', 'local_learningcelebration'),
                'hasmetrics' => true,
                'metrics' => [
                    $this->metric('12', get_string('visual_metric_courses', 'local_learningcelebration')),
                    $this->metric('184', get_string('visual_metric_activities', 'local_learningcelebration')),
                    $this->metric('6', get_string('visual_metric_badges', 'local_learningcelebration')),
                    $this->metric('87.4%', get_string('visual_metric_averagegrade', 'local_learningcelebration')),
                ],
            ];
            $slides[] = [
                'kind' => 'highlight',
                'eyebrow' => get_string('visual_highlight_eyebrow', 'local_learningcelebration'),
                'title' => get_string('visual_highlight_title', 'local_learningcelebration'),
                'hashighlight' => true,
                'coursename' => get_string('visual_demo_course', 'local_learningcelebration'),
                'coursegrade' => '96.0%',
            ];

            if ($experience === data_richness_classifier::EXPERIENCE_LEARNING_YEAR_COMPARISON) {
                $slides[] = [
                    'kind' => 'comparison',
                    'eyebrow' => get_string('visual_comparison_eyebrow', 'local_learningcelebration'),
                    'title' => get_string('visual_comparison_title', 'local_learningcelebration'),
                    'hascomparison' => true,
                    'comparisons' => [
                        $this->comparison(
                            get_string('visual_metric_courses', 'local_learningcelebration'),
                            '12',
                            '8'
                        ),
                        $this->comparison(
                            get_string('visual_metric_activities', 'local_learningcelebration'),
                            '184',
                            '136'
                        ),
                        $this->comparison(
                            get_string('visual_metric_badges', 'local_learningcelebration'),
                            '6',
                            '4'
                        ),
                    ],
                ];
            }
        }

        $slides[] = $this->final_slide();
        return $this->wrap_context($slides, $firstname, $sitename, $experience, true);
    }

    /**
     * Build the birthday hero slide.
     *
     * @param string $firstname Learner first name.
     * @param string $experience Selected experience mode.
     * @return array Hero slide.
     */
    private function hero_slide(string $firstname, string $experience): array {
        if ($experience === data_richness_classifier::EXPERIENCE_BIRTHDAY_WELCOME) {
            $body = get_string('visual_hero_body_welcome', 'local_learningcelebration');
        } else if ($experience === data_richness_classifier::EXPERIENCE_CELEBRATION_ONLY) {
            $body = get_string('visual_hero_body_simple', 'local_learningcelebration');
        } else {
            $body = get_string('visual_hero_body_learning', 'local_learningcelebration');
        }

        return [
            'kind' => 'hero',
            'ishero' => true,
            'eyebrow' => get_string('visual_hero_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_hero_title', 'local_learningcelebration', $firstname),
            'body' => $body,
        ];
    }

    /**
     * Build the quiet-year slide.
     *
     * @return array Quiet-year slide.
     */
    private function quiet_year_slide(): array {
        return [
            'kind' => 'quiet',
            'eyebrow' => get_string('visual_quiet_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_quiet_title', 'local_learningcelebration'),
            'body' => get_string('visual_quiet_body', 'local_learningcelebration'),
        ];
    }

    /**
     * Build the low-data welcome slide.
     *
     * @param analytics_report $report Analytics report.
     * @param period_statistics $statistics Period statistics.
     * @return array Welcome slide using only meaningful non-zero information.
     */
    private function welcome_slide(analytics_report $report, period_statistics $statistics): array {
        $metrics = [];
        if ($report->get_active_enrolments() > 0) {
            $metrics[] = $this->metric(
                (string) $report->get_active_enrolments(),
                get_string('visual_metric_coursesenrolled', 'local_learningcelebration')
            );
        }
        if ($statistics->get_completed_activities() > 0) {
            $metrics[] = $this->metric(
                (string) $statistics->get_completed_activities(),
                get_string('visual_metric_activities', 'local_learningcelebration')
            );
        }
        if ($statistics->get_earned_badges() > 0) {
            $metrics[] = $this->metric(
                (string) $statistics->get_earned_badges(),
                get_string('visual_metric_badges', 'local_learningcelebration')
            );
        }

        return [
            'kind' => 'welcome',
            'eyebrow' => get_string('visual_firststeps_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_firststeps_title', 'local_learningcelebration'),
            'body' => get_string('visual_firststeps_body', 'local_learningcelebration'),
            'hasmetrics' => !empty($metrics),
            'metrics' => $metrics,
        ];
    }

    /**
     * Build the birthday-to-birthday period slide.
     *
     * @param evaluation $evaluation Birthday evaluation.
     * @param string $timezoneid User timezone identifier.
     * @return array Learning period slide.
     */
    private function period_slide(evaluation $evaluation, string $timezoneid): array {
        $dateformat = get_string('strftimedate', 'core_langconfig');
        return [
            'kind' => 'period',
            'eyebrow' => get_string('visual_learningyear_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_learningyear_title', 'local_learningcelebration'),
            'body' => get_string('visual_learningyear_body', 'local_learningcelebration'),
            'hasperiod' => true,
            'periodstart' => userdate($evaluation->get_period_start()->getTimestamp(), $dateformat, $timezoneid),
            'periodend' => userdate($evaluation->get_period_end_display()->getTimestamp(), $dateformat, $timezoneid),
        ];
    }

    /**
     * Build the annual metrics slide.
     *
     * @param period_statistics $statistics Period statistics.
     * @return array Metrics slide with zero-value metrics omitted.
     */
    private function metrics_slide(period_statistics $statistics): array {
        $metrics = [];
        if ($statistics->get_completed_courses() > 0) {
            $metrics[] = $this->metric(
                (string) $statistics->get_completed_courses(),
                get_string('visual_metric_courses', 'local_learningcelebration')
            );
        }
        if ($statistics->get_completed_activities() > 0) {
            $metrics[] = $this->metric(
                (string) $statistics->get_completed_activities(),
                get_string('visual_metric_activities', 'local_learningcelebration')
            );
        }
        if ($statistics->get_earned_badges() > 0) {
            $metrics[] = $this->metric(
                (string) $statistics->get_earned_badges(),
                get_string('visual_metric_badges', 'local_learningcelebration')
            );
        }
        if ($statistics->get_average_course_grade() !== null) {
            $metrics[] = $this->metric(
                format_float($statistics->get_average_course_grade(), 1) . '%',
                get_string('visual_metric_averagegrade', 'local_learningcelebration')
            );
        }

        return [
            'kind' => 'metrics',
            'eyebrow' => get_string('visual_yearinnumbers_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_yearinnumbers_title', 'local_learningcelebration'),
            'hasmetrics' => !empty($metrics),
            'metrics' => $metrics,
        ];
    }

    /**
     * Build the best-course highlight slide.
     *
     * @param period_statistics $statistics Period statistics.
     * @return array Best-course highlight.
     */
    private function highlight_slide(period_statistics $statistics): array {
        return [
            'kind' => 'highlight',
            'eyebrow' => get_string('visual_highlight_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_highlight_title', 'local_learningcelebration'),
            'hashighlight' => true,
            'coursename' => strip_tags(format_string((string) $statistics->get_best_course_name())),
            'coursegrade' => format_float((float) $statistics->get_best_course_grade(), 1) . '%',
        ];
    }

    /**
     * Build the neutral year-over-year comparison slide.
     *
     * @param period_statistics $current Current period statistics.
     * @param period_statistics $previous Previous period statistics.
     * @return array Comparison slide using direct counts, without judgemental winner language.
     */
    private function comparison_slide(period_statistics $current, period_statistics $previous): array {
        $comparisons = [];
        if ($current->get_completed_courses() > 0 || $previous->get_completed_courses() > 0) {
            $comparisons[] = $this->comparison(
                get_string('visual_metric_courses', 'local_learningcelebration'),
                (string) $current->get_completed_courses(),
                (string) $previous->get_completed_courses()
            );
        }
        if ($current->get_completed_activities() > 0 || $previous->get_completed_activities() > 0) {
            $comparisons[] = $this->comparison(
                get_string('visual_metric_activities', 'local_learningcelebration'),
                (string) $current->get_completed_activities(),
                (string) $previous->get_completed_activities()
            );
        }
        if ($current->get_earned_badges() > 0 || $previous->get_earned_badges() > 0) {
            $comparisons[] = $this->comparison(
                get_string('visual_metric_badges', 'local_learningcelebration'),
                (string) $current->get_earned_badges(),
                (string) $previous->get_earned_badges()
            );
        }
        if ($current->get_average_course_grade() !== null || $previous->get_average_course_grade() !== null) {
            $comparisons[] = $this->comparison(
                get_string('visual_metric_averagegrade', 'local_learningcelebration'),
                $current->get_average_course_grade() === null
                    ? get_string('notavailable', 'local_learningcelebration')
                    : format_float($current->get_average_course_grade(), 1) . '%',
                $previous->get_average_course_grade() === null
                    ? get_string('notavailable', 'local_learningcelebration')
                    : format_float($previous->get_average_course_grade(), 1) . '%'
            );
        }

        return [
            'kind' => 'comparison',
            'eyebrow' => get_string('visual_comparison_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_comparison_title', 'local_learningcelebration'),
            'body' => get_string('visual_comparison_body', 'local_learningcelebration'),
            'hascomparison' => !empty($comparisons),
            'comparisons' => $comparisons,
        ];
    }

    /**
     * Build the closing slide.
     *
     * @return array Final slide.
     */
    private function final_slide(): array {
        return [
            'kind' => 'final',
            'isfinal' => true,
            'eyebrow' => get_string('visual_final_eyebrow', 'local_learningcelebration'),
            'title' => get_string('visual_final_title', 'local_learningcelebration'),
            'body' => get_string('visual_final_body', 'local_learningcelebration'),
        ];
    }

    /**
     * Build one metric presentation item.
     *
     * @param string $value Metric value.
     * @param string $label Display label.
     * @return array Metric item.
     */
    private function metric(string $value, string $label): array {
        return ['value' => $value, 'label' => $label];
    }

    /**
     * Build one comparison presentation item.
     *
     * @param string $label Display label.
     * @param string $current Current-period value.
     * @param string $previous Previous-period value.
     * @return array Comparison item.
     */
    private function comparison(string $label, string $current, string $previous): array {
        return ['label' => $label, 'current' => $current, 'previous' => $previous];
    }

    /**
     * Add slide numbering and outer template data.
     *
     * @param array $slides Slides.
     * @param string $firstname User first name.
     * @param string $sitename Site name.
     * @param string $experience Experience key.
     * @param bool $isdemo Demo indicator.
     * @return array
     */
    private function wrap_context(
        array $slides,
        string $firstname,
        string $sitename,
        string $experience,
        bool $isdemo
    ): array {
        $count = count($slides);
        foreach ($slides as $index => &$slide) {
            $slide['index'] = $index;
            $slide['number'] = $index + 1;
            $slide['isfirst'] = $index === 0;
            $slide['count'] = $count;
            $slide['slidelabel'] = get_string('visual_slide', 'local_learningcelebration', $index + 1);
        }
        unset($slide);

        return [
            'firstname' => $firstname,
            'sitename' => $sitename,
            'experience' => $experience,
            'isdemo' => $isdemo,
            'motionenabled' => $this->options->motion_enabled(),
            'confettienabled' => $this->options->confetti_enabled(),
            'slidecount' => $count,
            'slides' => $slides,
            'previouslabel' => get_string('visual_previous', 'local_learningcelebration'),
            'nextlabel' => get_string('visual_next', 'local_learningcelebration'),
            'restartlabel' => get_string('visual_restart', 'local_learningcelebration'),
            'brandlabel' => get_string('visual_brand', 'local_learningcelebration', $sitename),
        ];
    }
}
