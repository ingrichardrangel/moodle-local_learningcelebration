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

use local_learningcelebration\local\birthday\birthday_engine;
use local_learningcelebration\local\profile_field_repository;

/**
 * Resolves the current user's validated birthday context from site configuration.
 *
 * The resolver centralises configuration normalisation so automatic display and
 * state-changing actions cannot disagree about which annual celebration is valid.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class birthday_context_resolver {
    /** Supported post-birthday windows. */
    private const ALLOWED_WINDOWS = [0, 1, 3, 7];

    /**
     * Resolve the user's currently eligible celebration.
     *
     * @param \stdClass $user Moodle user record.
     * @return birthday_context|null Null when the plugin is disabled, data is unavailable, or the user is outside the window.
     */
    public function resolve(\stdClass $user): ?birthday_context {
        global $SESSION;

        $config = get_config('local_learningcelebration');
        if (empty($config->enabled) || empty($config->birthdayfield)) {
            return null;
        }

        $windowdays = isset($config->celebrationwindow) ? (int) $config->celebrationwindow : 3;
        if (!in_array($windowdays, self::ALLOWED_WINDOWS, true)) {
            $windowdays = 3;
        }

        $leapdaypolicy = $config->leapdaypolicy ?? birthday_engine::LEAPDAY_FEBRUARY_28;
        if (
            !in_array(
                $leapdaypolicy,
                [birthday_engine::LEAPDAY_FEBRUARY_28, birthday_engine::LEAPDAY_MARCH_1],
                true
            )
        ) {
            $leapdaypolicy = birthday_engine::LEAPDAY_FEBRUARY_28;
        }

        $signature = sha1(implode('|', [
            (string) $user->id,
            (string) ($user->timemodified ?? 0),
            (string) $config->birthdayfield,
            (string) $windowdays,
            (string) $leapdaypolicy,
        ]));

        $birthtimestamp = null;
        $usecache = !is_siteadmin($user);
        if (
            $usecache
            && isset($SESSION->local_learningcelebration_birthcache)
            && ($SESSION->local_learningcelebration_birthcache['signature'] ?? '') === $signature
        ) {
            $cached = $SESSION->local_learningcelebration_birthcache['birthtimestamp'] ?? null;
            $birthtimestamp = is_int($cached) ? $cached : null;
        } else {
            $repository = new profile_field_repository();
            $field = $repository->get_datetime_field((string) $config->birthdayfield);
            if ($field) {
                $birthtimestamp = $repository->get_user_value((int) $user->id, (int) $field->id);
            }

            if ($usecache) {
                $SESSION->local_learningcelebration_birthcache = [
                    'signature' => $signature,
                    'birthtimestamp' => $birthtimestamp,
                ];
            }
        }

        if ($birthtimestamp === null) {
            return null;
        }

        $timezone = \core_date::get_user_timezone_object($user);
        $evaluation = (new birthday_engine())->evaluate(
            $birthtimestamp,
            $timezone,
            $windowdays,
            $leapdaypolicy
        );

        if (!$evaluation->is_eligible()) {
            return null;
        }

        return new birthday_context(
            $evaluation,
            $timezone,
            (int) $evaluation->get_last_birthday()->format('Y')
        );
    }
}
