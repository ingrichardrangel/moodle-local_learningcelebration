# Learning Celebration

Learning Celebration is a local Moodle plugin that celebrates a learner's birthday with a personal annual learning recap based on Moodle core learning records.

## Version 0.8.1

Version 0.8.1 is the **CI remediation milestone**. It preserves the validated learner experience while addressing the first full Moodle Plugin CI findings from version 0.8.0.

### Quality infrastructure

- Added a GitHub Actions workflow powered by Moodle Plugin CI.
- Added an explicit compatibility matrix for Moodle 4.5 and Moodle 5.2.
- The matrix exercises both MariaDB and PostgreSQL and uses PHP versions appropriate to each Moodle branch.
- CI runs PHP lint, Moodle Code Checker, PHPDoc checks, plugin validation, upgrade-savepoint validation, Mustache linting, Grunt/AMD checks, PHPUnit, and Behat.
- Added Behat smoke scenarios for administrator diagnostics/preview, authenticated learner preferences, and guest authentication enforcement.
- Expanded PHPUnit regression coverage for annual-view persistence, capability defaults, and Privacy API data discovery/deletion.
- Normalised PHP licence headers in older files to reduce Marketplace/coding-style review noise.
- Added `.editorconfig` and `CONTRIBUTING.md` for consistent public contributions.

The workflow is committed with the plugin but only runs when the repository is hosted on GitHub and receives a push or pull request. Local ZIP installation does not invoke GitHub Actions and does not send Moodle data anywhere.

## Requirements

- Moodle 4.5 or later (declared support in this development release: 4.5 through 5.2).
- PHP version supported by the target Moodle release.
- At least one custom user profile field of type **Date/Time** to use as the date-of-birth field.

## Installation or upgrade

1. Copy the `learningcelebration` directory to `local/learningcelebration`.
2. Visit **Site administration > Notifications** and complete the installation/upgrade.
3. Open **Site administration > Plugins > Local plugins > Learning Celebration**.
4. Confirm the birthday field, celebration window and 29 February policy.
5. Configure **Learning recap content** and **Presentation** as required by the institution.
6. Enable the plugin.
7. Use **Configuration status** to validate the effective policy and current account.
8. Use **Celebration preview** to inspect visual variants without creating learner-facing completion state.

Upgrading from 0.8.0 to 0.8.1 renames the existing `local_lc_views` table to `local_learningcelebration_vw` without deleting its records. Fresh installations create the new table name directly.

## Capabilities

### `local/learningcelebration:view`

Controls learner-facing access to automatic celebrations, preferences, replay and the state-changing AJAX service. It is granted by default to the authenticated-user archetype so normal logged-in users keep the 0.6.0 behaviour after upgrade. Administrators can override it through Moodle roles.

### `local/learningcelebration:manage`

Controls **Configuration status**, **Celebration preview**, and the QA reset action. It is granted by default to the manager archetype. Site administrators retain access as usual.

Site-wide configuration remains protected separately by `moodle/site:config`.

## Learning recap content policy

Administrators can independently allow or suppress:

- completed courses;
- completed activities;
- badges;
- final course grades;
- active enrolments used in Birthday Welcome;
- year-over-year comparison.

The policy is applied before data-richness classification, so disabled metrics cannot influence the experience selection and do not leave empty placeholders.

## Automatic display behaviour

A celebration is considered for automatic display only when all of the following are true:

- the plugin is enabled;
- the user is authenticated and is not the guest account;
- the user has `local/learningcelebration:view`;
- the user has a valid value in the configured Date/Time profile field;
- the current local date in the user's Moodle timezone is within the configured birthday window;
- the user has not disabled automatic celebrations;
- the celebration has not already been completed for the observed birthday year;
- the current page is considered safe for an interruption.

The overlay does not automatically open on administration/authentication/plugin pages, embedded/popup/secure layouts, submitted-form requests, or active quiz-attempt flows.

## State-changing actions

The learner's **Remind me later** and **Continue to Moodle** actions are sent through Moodle's standard `core/ajax` module to the registered external function `local_learningcelebration_update_celebration_state`.

The service validates:

1. external-function parameter types;
2. the current Moodle user context;
3. `local/learningcelebration:view`;
4. the configured birthday field and current birthday-window eligibility;
5. that the supplied celebration year matches the server-derived year;
6. that the annual view record already exists because the overlay was actually rendered.

This prevents a logged-in browser from fabricating arbitrary annual completion records by altering client parameters.

## Learning periods and analytics

The most recently completed learning year is defined as:

`previous observed birthday <= activity < most recent observed birthday`

Learning Celebration reads Moodle core records for course completions, activity completions, badge awards, visible numeric final course grades and active enrolments. Analytics are calculated on demand and are not persisted as snapshots.

Moodle stores one current activity-completion row per user/activity, so later completion-state changes can alter historical attribution. Likewise, course completion determines the recap period while the grade shown is the current visible final course grade.

## Privacy

The date of birth remains in Moodle's custom profile-field storage and is not copied into plugin-owned storage.

`local_learningcelebration_vw` stores only:

- user id;
- observed birthday year;
- first and most recent automatic-display timestamps;
- automatic-display count;
- completion status;
- completion timestamp.

An optional user preference is stored only when a learner disables automatic celebrations. The Privacy API declares, exports and deletes both kinds of plugin-owned data. Version 0.7.0 also correctly discovers and removes preference-only records even when that user has never generated an annual view row.

See `PRIVACY.md` for the data-minimisation model and `SECURITY.md` for the security model.

## Testing 0.8.1

The learner-facing runtime remains functionally unchanged, so the established manual regression sequence should still pass: automatic display, snooze, completion persistence, replay, QA reset, page exclusions, capability restrictions, and privacy behaviour.

For repository validation, push the plugin source to GitHub and inspect the **Moodle Plugin CI** workflow. All matrix jobs should pass before treating a commit as release-ready. The workflow covers Moodle 4.5/5.2, MariaDB/PostgreSQL, PHPUnit, Behat, Moodle coding checks, Mustache, and AMD/Grunt validation.

The Behat suite currently provides smoke coverage rather than a complete browser-level simulation of every birthday scenario. Calendar and analytics edge cases continue to be covered primarily by PHPUnit and the administrator simulation tools.

## Roadmap

The next milestone is 0.9.0 beta after the 0.8.1 CI matrix is fully green: perform explicit Moodle 4.5/5.2 compatibility passes, add Moodle 5.3 validation, prepare Marketplace-facing documentation and freeze the feature set for the 1.0 review candidate.

## License

GNU GPL v3 or later.

Copyright 2026 Richard Rangel.
