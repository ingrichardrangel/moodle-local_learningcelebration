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
 * Administrator-only QA reset for an annual celebration view record.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();
require_capability('local/learningcelebration:manage', context_system::instance());
if (!data_submitted()) {
    throw new moodle_exception('invalidrequest', 'error');
}
require_sesskey();

$celebrationyear = required_param('celebrationyear', PARAM_INT);
if ($celebrationyear < 1900 || $celebrationyear > 9999) {
    throw new invalid_parameter_exception('Invalid celebration year.');
}

(new \local_learningcelebration\local\celebration\view_repository())->delete_record(
    (int) $USER->id,
    $celebrationyear
);

unset($SESSION->local_learningcelebration_completed[$celebrationyear]);
unset($SESSION->local_learningcelebration_snoozed[$celebrationyear]);

redirect(
    new moodle_url('/local/learningcelebration/status.php'),
    get_string('autodisplay_resetdone', 'local_learningcelebration'),
    null,
    \core\output\notification::NOTIFY_SUCCESS
);
