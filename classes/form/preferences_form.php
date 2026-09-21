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

namespace local_learningcelebration\form;

/**
 * User preferences form for Learning Celebration.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class preferences_form extends \moodleform {
    /** Define form fields. */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement(
            'advcheckbox',
            'autoshow',
            get_string('pref_autoshow', 'local_learningcelebration'),
            get_string('pref_autoshow_desc', 'local_learningcelebration')
        );
        $mform->setType('autoshow', PARAM_BOOL);

        $this->add_action_buttons(false, get_string('savechanges'));
    }
}
