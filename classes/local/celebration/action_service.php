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

/**
 * Applies a validated user action to the current annual celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class action_service {
    /** Complete the annual experience. */
    public const ACTION_COMPLETE = 'complete';

    /** Defer the annual experience for the rest of the current Moodle session. */
    public const ACTION_SNOOZE = 'snooze';

    /** @var birthday_context_resolver */
    private birthday_context_resolver $resolver;

    /** @var view_repository */
    private view_repository $views;

    /**
     * Constructor.
     *
     * @param birthday_context_resolver|null $resolver Birthday context resolver.
     * @param view_repository|null $views View repository.
     */
    public function __construct(?birthday_context_resolver $resolver = null, ?view_repository $views = null) {
        $this->resolver = $resolver ?? new birthday_context_resolver();
        $this->views = $views ?? new view_repository();
    }

    /**
     * Handle a state-changing action for the current user's eligible celebration.
     *
     * The client-provided year is treated only as a consistency token. The authoritative
     * year is independently derived from the configured profile field and Birthday Engine.
     *
     * @param \stdClass $user Current Moodle user.
     * @param string $action Action name.
     * @param int $celebrationyear Client-provided observed birthday year.
     * @return void
     */
    public function handle(\stdClass $user, string $action, int $celebrationyear): void {
        global $SESSION;

        if (!in_array($action, [self::ACTION_COMPLETE, self::ACTION_SNOOZE], true)) {
            throw new \invalid_parameter_exception('Unsupported celebration action.');
        }
        if ($celebrationyear < 1900 || $celebrationyear > 9999) {
            throw new \invalid_parameter_exception('Invalid celebration year.');
        }

        $birthdaycontext = $this->resolver->resolve($user);
        if ($birthdaycontext === null || $birthdaycontext->get_celebration_year() !== $celebrationyear) {
            throw new \moodle_exception('actionnotavailable', 'local_learningcelebration');
        }

        // A legitimate action must follow a server-rendered automatic celebration.
        // This blocks arbitrary authenticated requests from fabricating annual view records.
        $record = $this->views->get_record((int) $user->id, $celebrationyear);
        if ($record === null) {
            throw new \moodle_exception('actionnotavailable', 'local_learningcelebration');
        }

        if ($action === self::ACTION_COMPLETE) {
            $this->views->mark_completed((int) $user->id, $celebrationyear);
            $SESSION->local_learningcelebration_completed[$celebrationyear] = true;
            unset($SESSION->local_learningcelebration_snoozed[$celebrationyear]);
            return;
        }

        // Snooze is deliberately session-scoped and creates no additional persistent personal data.
        $SESSION->local_learningcelebration_snoozed[$celebrationyear] = true;
    }
}
