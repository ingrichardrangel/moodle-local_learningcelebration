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
 * Decides whether an automatic celebration may safely appear on a page.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class page_policy {
    /** Page layouts where an interruption is inappropriate. */
    private const EXCLUDED_LAYOUTS = [
        'login',
        'popup',
        'embedded',
        'frametop',
        'maintenance',
        'redirect',
        'print',
        'secure',
    ];

    /** Exact or prefix paths which should never trigger an automatic celebration. */
    private const EXCLUDED_PATH_PREFIXES = [
        '/admin/',
        '/login/',
        '/auth/',
        '/local/learningcelebration/',
        '/mod/quiz/attempt.php',
        '/mod/quiz/startattempt.php',
        '/mod/quiz/summary.php',
        '/mod/quiz/processattempt.php',
    ];

    /**
     * Check the current page.
     *
     * @param \moodle_page $page Moodle page.
     * @return bool
     */
    public function is_safe(\moodle_page $page): bool {
        if ((defined('CLI_SCRIPT') && CLI_SCRIPT)
                || (defined('AJAX_SCRIPT') && AJAX_SCRIPT)
                || (defined('WS_SERVER') && WS_SERVER)) {
            return false;
        }

        // Never interrupt a page while submitted form data is being processed.
        if (data_submitted()) {
            return false;
        }

        if (in_array((string) $page->pagelayout, self::EXCLUDED_LAYOUTS, true)) {
            return false;
        }

        $pagetype = (string) $page->pagetype;
        if (str_starts_with($pagetype, 'admin-') || str_starts_with($pagetype, 'mod-quiz-attempt')) {
            return false;
        }

        $path = '';
        if ($page->url) {
            $path = (string) parse_url($page->url->out(false), PHP_URL_PATH);
        }

        foreach (self::EXCLUDED_PATH_PREFIXES as $excluded) {
            if ($path === $excluded || str_starts_with($path, $excluded)) {
                return false;
            }
        }

        return true;
    }
}
