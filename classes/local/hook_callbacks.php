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

namespace local_learningcelebration\local;

use core\hook\output\before_footer_html_generation;
use local_learningcelebration\local\celebration\auto_display_service;
use local_learningcelebration\local\celebration\view_repository;

/**
 * Core hook callbacks for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class hook_callbacks {
    /**
     * Inject an eligible celebration immediately before Moodle finalises footer JavaScript.
     *
     * @param before_footer_html_generation $hook Output hook.
     * @return void
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $PAGE, $SITE, $USER;

        if (during_initial_install()
                || !get_config('local_learningcelebration', 'version')
                || !isloggedin()
                || isguestuser()
                || empty($USER->id)) {
            return;
        }

        try {
            $payload = (new auto_display_service())->build($PAGE, $USER, $SITE);
            if ($payload === null) {
                return;
            }

            $year = (int) $payload['celebrationyear'];
            $viewdata = $payload['viewdata'];
            $rootid = 'local-learningcelebration-auto-' . $year;

            $viewdata['rootid'] = $rootid;
            $viewdata['isoverlay'] = true;
            $viewdata['dialoglabel'] = get_string('overlay_dialoglabel', 'local_learningcelebration');
            $viewdata['notnowlabel'] = get_string('overlay_notnow', 'local_learningcelebration');
            $viewdata['finishlabel'] = get_string('overlay_finish', 'local_learningcelebration');
            $viewdata['actionerror'] = get_string('overlay_actionerror', 'local_learningcelebration');

            $html = $hook->renderer->render_from_template('local_learningcelebration/overlay', $viewdata);

            // Record only after the experience has been rendered successfully.
            (new view_repository())->record_displayed((int) $USER->id, $year);

            $PAGE->requires->js_call_amd('local_learningcelebration/celebration', 'initOverlay', [
                $rootid,
                $year,
                !empty($viewdata['motionenabled']),
            ]);

            $hook->add_html($html);
        } catch (\Throwable $exception) {
            debugging(
                'Learning Celebration automatic display failed: ' . $exception->getMessage(),
                DEBUG_DEVELOPER
            );
        }
    }
}
