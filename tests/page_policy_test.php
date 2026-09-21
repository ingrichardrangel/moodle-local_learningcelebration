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

namespace local_learningcelebration;

use local_learningcelebration\local\celebration\page_policy;

/**
 * Tests for automatic-display page safety policy.
 *
 * @package   local_learningcelebration
 */
final class page_policy_test extends \advanced_testcase {
    /** Normal course pages are eligible. */
    public function test_normal_course_page_is_safe(): void {
        $page = new \moodle_page();
        $page->set_url(new \moodle_url('/course/view.php', ['id' => 2]));
        $page->set_pagelayout('incourse');
        $page->set_pagetype('course-view-topics');

        $this->assertTrue((new page_policy())->is_safe($page));
    }

    /** Active quiz attempts are excluded. */
    public function test_quiz_attempt_is_not_safe(): void {
        $page = new \moodle_page();
        $page->set_url(new \moodle_url('/mod/quiz/attempt.php', ['attempt' => 4]));
        $page->set_pagelayout('incourse');
        $page->set_pagetype('mod-quiz-attempt');

        $this->assertFalse((new page_policy())->is_safe($page));
    }

    /** Administration pages are excluded. */
    public function test_admin_page_is_not_safe(): void {
        $page = new \moodle_page();
        $page->set_url(new \moodle_url('/admin/index.php'));
        $page->set_pagelayout('admin');
        $page->set_pagetype('admin-index');

        $this->assertFalse((new page_policy())->is_safe($page));
    }
}
