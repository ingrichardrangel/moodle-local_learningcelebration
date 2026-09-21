# Learning Celebration

[![Moodle Plugin CI](https://github.com/ingrichardrangel/moodle-local_learningcelebration/actions/workflows/ci.yml/badge.svg)](https://github.com/ingrichardrangel/moodle-local_learningcelebration/actions/workflows/ci.yml)

Learning Celebration is a free local Moodle plugin that celebrates a learner's birthday with a privacy-conscious annual learning recap built from Moodle core learning records.

## Version 0.9.0 Beta

Version 0.9.0 is the **publication-preparation beta**. The learner-facing feature set is frozen while compatibility, documentation, testing evidence and Marketplace materials are prepared for the 1.0 review candidate.

There are no new learner-facing features or database-schema changes in this release.

### Compatibility target

- Moodle 4.5 through Moodle 5.3.
- PHP version supported by the selected Moodle branch.
- MariaDB and PostgreSQL are exercised by the public CI matrix.
- Moodle 5.3 testing uses the `MOODLE_503_STABLE` branch during the pre-release period and must remain green before 5.3 support is treated as release-ready.

The CI matrix currently targets:

- Moodle 4.5 / PHP 8.1 / MariaDB;
- Moodle 4.5 / PHP 8.3 / PostgreSQL;
- Moodle 5.2 / PHP 8.3 / MariaDB;
- Moodle 5.2 / PHP 8.4 / PostgreSQL;
- Moodle 5.3 / PHP 8.3 / MariaDB;
- Moodle 5.3 / PHP 8.4 / PostgreSQL.

## What the plugin does

When an authenticated learner enters Moodle during the configured birthday window, Learning Celebration can display an accessible full-screen celebration on the first safe page visited. The experience adapts to the amount of meaningful learning history available.

Possible experiences include:

- a simple birthday celebration when there is no meaningful learning history yet;
- Birthday Welcome for newly arrived learners;
- a completed birthday-to-birthday Learning Year recap;
- an optional neutral year-over-year comparison when both periods contain enough enabled data.

Zero-value metrics are omitted rather than displayed as empty achievements.

## Learning records used

Administrators can independently allow or suppress:

- completed courses;
- completed activities;
- earned badges;
- visible numeric final course grades;
- active enrolments used as welcome context;
- year-over-year comparison.

The plugin calculates these values on demand from Moodle core records. It does not store analytics snapshots.

## Birthday field

Moodle does not provide a universal core date-of-birth field. The administrator selects an existing custom user profile field of type **Date/Time**.

Learning Celebration reads that field to determine birthday eligibility but does not copy the date of birth into plugin-owned storage.

## Installation

1. Place the `learningcelebration` directory in `local/learningcelebration`.
2. Visit **Site administration > Notifications** and complete installation.
3. Create or identify a custom user profile field of type **Date/Time** for date of birth.
4. Open **Site administration > Plugins > Local plugins > Learning Celebration**.
5. Select the birthday field and configure the celebration window and 29 February policy.
6. Configure recap content and optional presentation effects.
7. Enable the plugin.
8. Use **Configuration status** and **Celebration preview** to validate the installation before learner use.

Upgrades from 0.8.1 require no schema migration. The migration introduced in 0.8.1 remains available for older installations that still use the former `local_lc_views` table name.

## Automatic display behaviour

A celebration is considered only when all of the following are true:

- the plugin is enabled;
- the user is authenticated and is not the guest account;
- the user has `local/learningcelebration:view`;
- the configured birthday field contains a valid value;
- the user's local Moodle date is within the configured birthday window;
- the user has not disabled automatic celebrations;
- the annual celebration has not already been completed;
- the current page is considered safe for interruption.

Automatic display is excluded from administration and authentication pages, plugin pages, embedded/pop-up/secure layouts, submitted-form requests and active quiz-attempt flows.

## Learner controls

Learners can:

- choose **Remind me later** without completing the annual celebration;
- complete the celebration and continue to Moodle;
- disable automatic celebrations in their preferences;
- replay their most recently completed Learning Celebration.

Replay is read-only and does not increase the automatic-display count.

## Capabilities

### `local/learningcelebration:view`

Controls learner-facing automatic celebrations, preferences, replay and celebration-state actions. It is granted by default to authenticated users.

### `local/learningcelebration:manage`

Controls Configuration status, Celebration preview and the QA reset tool. It is granted by default to the manager archetype.

Site-wide settings remain protected by `moodle/site:config`.

## Privacy

The plugin stores only the minimum state needed to avoid repeatedly showing the same annual celebration:

- user id;
- observed birthday year;
- first and most recent automatic-display timestamps;
- automatic-display count;
- completion state;
- completion timestamp.

An optional user preference is stored only when a learner disables automatic celebrations. Date of birth, grades, courses, badges and calculated recap statistics are not duplicated into plugin-owned storage.

The Moodle Privacy API declares, exports and deletes both annual-view records and the optional preference. No learner data is transmitted to external services.

See [PRIVACY.md](PRIVACY.md) for the complete data-minimisation model.

## Security

State-changing learner actions use Moodle External Services and `core/ajax`. Parameters, login state, capabilities and the server-derived eligible celebration year are validated before a change is accepted. Administrative reset actions require POST and a valid Moodle sesskey.

See [SECURITY.md](SECURITY.md) for the complete security model.

## Automated tests

The repository contains PHPUnit and Behat tests and a Moodle Plugin CI workflow. The pipeline runs PHP lint, Moodle Code Checker, PHPDoc checks, plugin validation, upgrade-savepoint validation, Mustache linting, Grunt/AMD validation, PHPUnit and Behat.

Version 0.8.1 established a fully green CI baseline for Moodle 4.5 and 5.2 on MariaDB and PostgreSQL. Version 0.9.0 extends that same matrix to Moodle 5.3.

See [docs/TESTING.md](docs/TESTING.md) and [docs/RELEASE_CHECKLIST.md](docs/RELEASE_CHECKLIST.md).

## Administrator QA tools

**Configuration status** exposes read-only diagnostics for the configured birthday field, date engine, current learning periods, enabled content policy and automatic-display state.

The built-in birthday simulator lets an administrator test arbitrary dates, including leap-day and year-boundary cases, without changing the server clock or user profile.

**Celebration preview** provides deterministic demonstration data for every presentation mode without writing learner completion state.

## Public project links

- Source code: https://github.com/ingrichardrangel/moodle-local_learningcelebration
- Issue tracker: https://github.com/ingrichardrangel/moodle-local_learningcelebration/issues
- CI: https://github.com/ingrichardrangel/moodle-local_learningcelebration/actions/workflows/ci.yml

Marketplace-facing copy and screenshot guidance are maintained in [docs/MARKETPLACE.md](docs/MARKETPLACE.md).

## License

GNU GPL v3 or later.

Copyright 2026 Richard Rangel.
