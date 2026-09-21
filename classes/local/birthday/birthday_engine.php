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
 * Determines birthday eligibility and birthday-to-birthday learning periods.
 *
 * All calculations are performed as calendar dates in the supplied user timezone.
 * Timestamp arithmetic is deliberately avoided so daylight-saving transitions do
 * not alter date boundaries.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class birthday_engine {
    /** Celebrate 29 February birthdays on 28 February in non-leap years. */
    public const LEAPDAY_FEBRUARY_28 = 'feb28';

    /** Celebrate 29 February birthdays on 1 March in non-leap years. */
    public const LEAPDAY_MARCH_1 = 'mar1';

    /** Evaluation state: today is the observed birthday. */
    public const STATE_BIRTHDAY = 'birthday';

    /** Evaluation state: today is after the birthday but still inside the allowed window. */
    public const STATE_DELAYED = 'delayed';

    /** Evaluation state: today is outside the allowed celebration window. */
    public const STATE_OUTSIDE = 'outside';

    /**
     * Evaluate a stored Moodle datetime profile value.
     *
     * @param int $birthtimestamp Stored custom-profile timestamp.
     * @param \DateTimeZone $timezone User timezone used for all calendar-date calculations.
     * @param int $windowdays Number of days after the birthday for which celebration remains eligible.
     * @param string $leapdaypolicy Behaviour for 29 February birthdays in non-leap years.
     * @param int|null $nowtimestamp Current timestamp. Primarily injectable for deterministic tests.
     * @return evaluation Birthday evaluation.
     */
    public function evaluate(
        int $birthtimestamp,
        \DateTimeZone $timezone,
        int $windowdays = 3,
        string $leapdaypolicy = self::LEAPDAY_FEBRUARY_28,
        ?int $nowtimestamp = null
    ): evaluation {
        if ($windowdays < 0) {
            throw new \InvalidArgumentException('The celebration window cannot be negative.');
        }

        if (!in_array($leapdaypolicy, [self::LEAPDAY_FEBRUARY_28, self::LEAPDAY_MARCH_1], true)) {
            throw new \InvalidArgumentException('Unsupported leap-day policy.');
        }

        $nowtimestamp = $nowtimestamp ?? time();
        $dateofbirth = $this->local_date_from_timestamp($birthtimestamp, $timezone);
        $today = $this->local_date_from_timestamp($nowtimestamp, $timezone);

        $birthmonth = (int) $dateofbirth->format('n');
        $birthday = (int) $dateofbirth->format('j');
        $currentyear = (int) $today->format('Y');

        $birthdaythisyear = $this->observed_birthday(
            $currentyear,
            $birthmonth,
            $birthday,
            $timezone,
            $leapdaypolicy
        );

        if ($today >= $birthdaythisyear) {
            $lastbirthday = $birthdaythisyear;
            $nextbirthday = $this->observed_birthday(
                $currentyear + 1,
                $birthmonth,
                $birthday,
                $timezone,
                $leapdaypolicy
            );
        } else {
            $lastbirthday = $this->observed_birthday(
                $currentyear - 1,
                $birthmonth,
                $birthday,
                $timezone,
                $leapdaypolicy
            );
            $nextbirthday = $birthdaythisyear;
        }

        $daysafterbirthday = (int) $lastbirthday->diff($today)->days;
        $birthdaytoday = $daysafterbirthday === 0;
        $eligible = $daysafterbirthday <= $windowdays;

        if ($birthdaytoday) {
            $state = self::STATE_BIRTHDAY;
        } else if ($eligible) {
            $state = self::STATE_DELAYED;
        } else {
            $state = self::STATE_OUTSIDE;
        }

        $lastbirthdayyear = (int) $lastbirthday->format('Y');
        $periodstart = $this->observed_birthday(
            $lastbirthdayyear - 1,
            $birthmonth,
            $birthday,
            $timezone,
            $leapdaypolicy
        );
        $previousperiodstart = $this->observed_birthday(
            $lastbirthdayyear - 2,
            $birthmonth,
            $birthday,
            $timezone,
            $leapdaypolicy
        );

        return new evaluation(
            $dateofbirth,
            $today,
            $lastbirthday,
            $nextbirthday,
            $periodstart,
            $lastbirthday,
            $previousperiodstart,
            $periodstart,
            $eligible,
            $birthdaytoday,
            $daysafterbirthday,
            $state
        );
    }

    /**
     * Convert a timestamp into a local calendar date at midnight.
     *
     * @param int $timestamp Timestamp to convert.
     * @param \DateTimeZone $timezone Target timezone.
     * @return \DateTimeImmutable Local calendar date.
     */
    private function local_date_from_timestamp(int $timestamp, \DateTimeZone $timezone): \DateTimeImmutable {
        return (new \DateTimeImmutable('@' . $timestamp))
            ->setTimezone($timezone)
            ->setTime(0, 0, 0);
    }

    /**
     * Return the observed birthday date for a year.
     *
     * @param int $year Target year.
     * @param int $month Birth month.
     * @param int $day Birth day.
     * @param \DateTimeZone $timezone User timezone.
     * @param string $leapdaypolicy Behaviour for non-leap years.
     * @return \DateTimeImmutable Observed birthday at local midnight.
     */
    private function observed_birthday(
        int $year,
        int $month,
        int $day,
        \DateTimeZone $timezone,
        string $leapdaypolicy
    ): \DateTimeImmutable {
        if ($month === 2 && $day === 29 && !checkdate(2, 29, $year)) {
            if ($leapdaypolicy === self::LEAPDAY_MARCH_1) {
                $month = 3;
                $day = 1;
            } else {
                $month = 2;
                $day = 28;
            }
        }

        return (new \DateTimeImmutable('now', $timezone))
            ->setDate($year, $month, $day)
            ->setTime(0, 0, 0);
    }
}
