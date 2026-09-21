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

use local_learningcelebration\local\celebration\view_repository;

/**
 * Tests for annual celebration view persistence.
 *
 * @package   local_learningcelebration
 */
final class view_repository_test extends \advanced_testcase {
    /** Display and completion update one annual record. */
    public function test_display_and_completion_flow(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $repository = new view_repository();

        $repository->record_displayed((int) $user->id, 2026, 1000);
        $repository->record_displayed((int) $user->id, 2026, 1100);
        $record = $repository->get_record((int) $user->id, 2026);

        $this->assertSame(2, (int) $record->viewcount);
        $this->assertSame(1000, (int) $record->timefirstviewed);
        $this->assertSame(1100, (int) $record->timelastviewed);
        $this->assertFalse((bool) $record->completed);

        $repository->mark_completed((int) $user->id, 2026, 1200);
        $this->assertTrue($repository->is_completed((int) $user->id, 2026));
        $record = $repository->get_record((int) $user->id, 2026);
        $this->assertSame(1200, (int) $record->timecompleted);
    }

    /**
     * Completion is idempotent and preserves the first completion timestamp.
     */
    public function test_completion_is_idempotent(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $repository = new view_repository();

        $repository->record_displayed((int) $user->id, 2026, 1000);
        $repository->mark_completed((int) $user->id, 2026, 1200);
        $repository->mark_completed((int) $user->id, 2026, 1500);

        $record = $repository->get_record((int) $user->id, 2026);
        $this->assertNotNull($record);
        $this->assertSame(1200, (int) $record->timecompleted);
        $this->assertSame(1500, (int) $record->timelastviewed);
        $this->assertSame(1, (int) $record->viewcount);
    }

    /**
     * Latest completed lookup ignores incomplete years and returns the newest completed year.
     */
    public function test_latest_completed_record(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $repository = new view_repository();

        $repository->record_displayed((int) $user->id, 2024, 1000);
        $repository->mark_completed((int) $user->id, 2024, 1100);
        $repository->record_displayed((int) $user->id, 2025, 1200);
        $repository->record_displayed((int) $user->id, 2026, 1300);
        $repository->mark_completed((int) $user->id, 2026, 1400);

        $latest = $repository->get_latest_completed_record((int) $user->id);
        $this->assertNotNull($latest);
        $this->assertSame(2026, (int) $latest->celebrationyear);
        $this->assertTrue($repository->has_completed_celebration((int) $user->id));
    }

    /**
     * Deleting one year leaves other annual records untouched.
     */
    public function test_delete_record_removes_only_requested_year(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $repository = new view_repository();

        $repository->record_displayed((int) $user->id, 2025, 1000);
        $repository->record_displayed((int) $user->id, 2026, 1100);
        $repository->delete_record((int) $user->id, 2025);

        $this->assertNull($repository->get_record((int) $user->id, 2025));
        $this->assertNotNull($repository->get_record((int) $user->id, 2026));
    }

}
