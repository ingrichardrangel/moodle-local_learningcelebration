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

namespace local_learningcelebration\local\presentation;

/**
 * Site-level presentation options for the celebration experience.
 *
 * @package   local_learningcelebration
 * @copyright 2026 Richard Rangel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class presentation_options {
    /** @var bool */
    private bool $motion;
    /** @var bool */
    private bool $confetti;

    /**
     * Constructor.
     *
     * @param bool $motion Enable plugin-authored motion effects.
     * @param bool $confetti Enable hero confetti when motion is enabled.
     */
    public function __construct(bool $motion = true, bool $confetti = true) {
        $this->motion = $motion;
        $this->confetti = $confetti;
    }

    /**
     * Build presentation options from plugin configuration.
     *
     * @return self
     */
    public static function from_config(): self {
        $config = get_config('local_learningcelebration');
        return new self(
            !isset($config->enablemotion) || (bool) $config->enablemotion,
            !isset($config->enableconfetti) || (bool) $config->enableconfetti
        );
    }

    /**
     * Check whether plugin-authored motion is enabled.
     *
     * @return bool
     */
    public function motion_enabled(): bool {
        return $this->motion;
    }

    /**
     * Confetti is meaningful only when plugin-authored motion is enabled.
     * The browser's prefers-reduced-motion setting can still suppress it in CSS.
     *
     * @return bool
     */
    public function confetti_enabled(): bool {
        return $this->motion && $this->confetti;
    }
}
