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

namespace local_learningcelebration\local\birthday;

/**
 * Immutable result returned by the birthday engine.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class evaluation {
    /** @var \DateTimeImmutable Local date represented by the configured birthday value. */
    private \DateTimeImmutable $dateofbirth;

    /** @var \DateTimeImmutable Current local date used for the evaluation. */
    private \DateTimeImmutable $today;

    /** @var \DateTimeImmutable Most recent observed birthday. */
    private \DateTimeImmutable $lastbirthday;

    /** @var \DateTimeImmutable Next observed birthday. */
    private \DateTimeImmutable $nextbirthday;

    /** @var \DateTimeImmutable Start of the most recently completed learning period. */
    private \DateTimeImmutable $periodstart;

    /** @var \DateTimeImmutable Exclusive end of the most recently completed learning period. */
    private \DateTimeImmutable $periodendexclusive;

    /** @var \DateTimeImmutable Start of the preceding completed learning period. */
    private \DateTimeImmutable $previousperiodstart;

    /** @var \DateTimeImmutable Exclusive end of the preceding completed learning period. */
    private \DateTimeImmutable $previousperiodendexclusive;

    /** @var bool Whether today falls inside the configured celebration window. */
    private bool $eligible;

    /** @var bool Whether today is the observed birthday itself. */
    private bool $birthdaytoday;

    /** @var int Whole calendar days elapsed since the most recent observed birthday. */
    private int $daysafterbirthday;

    /** @var string Evaluation state. */
    private string $state;

    /**
     * Constructor.
     *
     * @param \DateTimeImmutable $dateofbirth Local birth date.
     * @param \DateTimeImmutable $today Current local date.
     * @param \DateTimeImmutable $lastbirthday Most recent observed birthday.
     * @param \DateTimeImmutable $nextbirthday Next observed birthday.
     * @param \DateTimeImmutable $periodstart Start of completed period.
     * @param \DateTimeImmutable $periodendexclusive Exclusive end of completed period.
     * @param \DateTimeImmutable $previousperiodstart Start of preceding period.
     * @param \DateTimeImmutable $previousperiodendexclusive Exclusive end of preceding period.
     * @param bool $eligible Whether the celebration window is active.
     * @param bool $birthdaytoday Whether today is the observed birthday.
     * @param int $daysafterbirthday Days since the most recent observed birthday.
     * @param string $state Evaluation state.
     */
    public function __construct(
        \DateTimeImmutable $dateofbirth,
        \DateTimeImmutable $today,
        \DateTimeImmutable $lastbirthday,
        \DateTimeImmutable $nextbirthday,
        \DateTimeImmutable $periodstart,
        \DateTimeImmutable $periodendexclusive,
        \DateTimeImmutable $previousperiodstart,
        \DateTimeImmutable $previousperiodendexclusive,
        bool $eligible,
        bool $birthdaytoday,
        int $daysafterbirthday,
        string $state
    ) {
        $this->dateofbirth = $dateofbirth;
        $this->today = $today;
        $this->lastbirthday = $lastbirthday;
        $this->nextbirthday = $nextbirthday;
        $this->periodstart = $periodstart;
        $this->periodendexclusive = $periodendexclusive;
        $this->previousperiodstart = $previousperiodstart;
        $this->previousperiodendexclusive = $previousperiodendexclusive;
        $this->eligible = $eligible;
        $this->birthdaytoday = $birthdaytoday;
        $this->daysafterbirthday = $daysafterbirthday;
        $this->state = $state;
    }

    /** @return \DateTimeImmutable Local birth date. */
    public function get_date_of_birth(): \DateTimeImmutable {
        return $this->dateofbirth;
    }

    /** @return \DateTimeImmutable Current local date. */
    public function get_today(): \DateTimeImmutable {
        return $this->today;
    }

    /** @return \DateTimeImmutable Most recent observed birthday. */
    public function get_last_birthday(): \DateTimeImmutable {
        return $this->lastbirthday;
    }

    /** @return \DateTimeImmutable Next observed birthday. */
    public function get_next_birthday(): \DateTimeImmutable {
        return $this->nextbirthday;
    }

    /** @return \DateTimeImmutable Start of the completed learning period. */
    public function get_period_start(): \DateTimeImmutable {
        return $this->periodstart;
    }

    /**
     * Return the exclusive period end.
     *
     * This timestamp is intended for future database queries using
     * start <= eventtime < endexclusive.
     *
     * @return \DateTimeImmutable Exclusive end of the completed learning period.
     */
    public function get_period_end_exclusive(): \DateTimeImmutable {
        return $this->periodendexclusive;
    }

    /** @return \DateTimeImmutable Inclusive display end of the completed learning period. */
    public function get_period_end_display(): \DateTimeImmutable {
        return $this->periodendexclusive->modify('-1 day');
    }

    /** @return \DateTimeImmutable Start of the preceding completed learning period. */
    public function get_previous_period_start(): \DateTimeImmutable {
        return $this->previousperiodstart;
    }

    /** @return \DateTimeImmutable Exclusive end of the preceding completed learning period. */
    public function get_previous_period_end_exclusive(): \DateTimeImmutable {
        return $this->previousperiodendexclusive;
    }

    /** @return \DateTimeImmutable Inclusive display end of the preceding completed learning period. */
    public function get_previous_period_end_display(): \DateTimeImmutable {
        return $this->previousperiodendexclusive->modify('-1 day');
    }

    /** @return bool Whether the configured celebration window is active. */
    public function is_eligible(): bool {
        return $this->eligible;
    }

    /** @return bool Whether today is the observed birthday itself. */
    public function is_birthday_today(): bool {
        return $this->birthdaytoday;
    }

    /** @return int Calendar days since the most recent observed birthday. */
    public function get_days_after_birthday(): int {
        return $this->daysafterbirthday;
    }

    /** @return int Calendar days until the next observed birthday. */
    public function get_days_until_next_birthday(): int {
        return (int) $this->today->diff($this->nextbirthday)->days;
    }

    /** @return string Evaluation state. */
    public function get_state(): string {
        return $this->state;
    }
}
