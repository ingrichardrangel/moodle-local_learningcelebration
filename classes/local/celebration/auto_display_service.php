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

namespace local_learningcelebration\local\celebration;

use local_learningcelebration\local\analytics\analytics_engine;
use local_learningcelebration\local\presentation\celebration_view_builder;

/**
 * Resolves whether the current user should receive an automatic celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class auto_display_service {
    /** User preference controlling automatic celebrations. */
    public const PREF_AUTOSHOW = 'local_learningcelebration_autoshow';

    /** @var page_policy Page safety policy. */
    private page_policy $pagepolicy;

    /** @var view_repository View persistence. */
    private view_repository $views;

    /** @var birthday_context_resolver Birthday context resolver. */
    private birthday_context_resolver $resolver;

    /**
     * Constructor.
     *
     * @param page_policy|null $pagepolicy Page policy.
     * @param view_repository|null $views View repository.
     * @param birthday_context_resolver|null $resolver Birthday context resolver.
     */
    public function __construct(
        ?page_policy $pagepolicy = null,
        ?view_repository $views = null,
        ?birthday_context_resolver $resolver = null
    ) {
        $this->pagepolicy = $pagepolicy ?? new page_policy();
        $this->views = $views ?? new view_repository();
        $this->resolver = $resolver ?? new birthday_context_resolver();
    }

    /**
     * Build automatic overlay data for the current user, or return null.
     *
     * @param \moodle_page $page Current page.
     * @param \stdClass $user Current Moodle user.
     * @param \stdClass $site Site record.
     * @return array|null Contains viewdata and celebrationyear.
     */
    public function build(\moodle_page $page, \stdClass $user, \stdClass $site): ?array {
        global $SESSION;

        if (!$this->pagepolicy->is_safe($page)) {
            return null;
        }

        $systemcontext = \context_system::instance();
        if (!has_capability('local/learningcelebration:view', $systemcontext, $user)) {
            return null;
        }

        if (!(bool) get_user_preferences(self::PREF_AUTOSHOW, 1, $user->id)) {
            return null;
        }

        $birthdaycontext = $this->resolver->resolve($user);
        if ($birthdaycontext === null) {
            return null;
        }

        $evaluation = $birthdaycontext->get_evaluation();
        $timezone = $birthdaycontext->get_timezone();
        $celebrationyear = $birthdaycontext->get_celebration_year();

        if (
            !empty($SESSION->local_learningcelebration_snoozed[$celebrationyear])
            || !empty($SESSION->local_learningcelebration_completed[$celebrationyear])
        ) {
            return null;
        }

        if ($this->views->is_completed((int) $user->id, $celebrationyear)) {
            $SESSION->local_learningcelebration_completed[$celebrationyear] = true;
            return null;
        }

        $report = (new analytics_engine())->build_report(
            (int) $user->id,
            (int) $user->timecreated,
            $evaluation,
            $timezone
        );

        $sitename = strip_tags(format_string($site->shortname ?: $site->fullname));
        $viewdata = (new celebration_view_builder())->build(
            $evaluation,
            $report,
            (string) $user->firstname,
            $sitename,
            $timezone->getName()
        );

        return [
            'viewdata' => $viewdata,
            'celebrationyear' => $celebrationyear,
        ];
    }
}
