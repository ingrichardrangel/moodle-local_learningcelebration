# Testing Learning Celebration

## Continuous integration

The repository uses Moodle Plugin CI from `.github/workflows/ci.yml`.

Every push and pull request runs:

- PHP lint;
- Moodle Code Checker with zero warnings allowed;
- Moodle PHPDoc Checker with zero warnings allowed;
- plugin validation;
- upgrade-savepoint validation;
- Mustache lint;
- Grunt/AMD and CSS validation;
- PHPUnit;
- Behat smoke tests with Chrome.

## CI matrix for 1.0.0 Stable

- Moodle 4.5 / PHP 8.1 / MariaDB
- Moodle 4.5 / PHP 8.3 / PostgreSQL
- Moodle 5.2 / PHP 8.3 / MariaDB
- Moodle 5.2 / PHP 8.4 / PostgreSQL
- Moodle 5.3 / PHP 8.3 / MariaDB
- Moodle 5.3 / PHP 8.4 / PostgreSQL

The 1.0.0 stable release declares Moodle 5.3 support after the Moodle 5.3 code line passed the same automated quality gate used for the other supported branches.

## PHPUnit scope

The PHPUnit suite covers, among other areas:

- birthday date calculation;
- birthday-to-birthday periods;
- leap-day policies;
- delayed celebration windows across year boundaries;
- simulation input handling;
- analytics orchestration;
- data-richness classification;
- metric policy filtering;
- presentation options;
- annual view persistence;
- capability defaults;
- safe-page policy;
- Privacy API discovery and deletion.

## Behat scope

The Behat suite provides browser-level smoke coverage for administrator diagnostics/preview, authenticated learner preferences and guest authentication enforcement. Calendar and analytics edge cases are intentionally covered primarily by PHPUnit and the deterministic administrator simulator.

## Manual stable-release regression

Before publishing a stable release, test with developer debugging enabled:

1. Install from a clean ZIP.
2. Upgrade from 0.8.1.
3. Confirm Configuration status and simulator.
4. Confirm all Celebration preview variants.
5. Trigger an automatic birthday overlay on a normal learner page.
6. Use Remind me later and confirm it does not reopen during the same session.
7. Start a new session and complete the celebration.
8. Confirm the completed celebration no longer opens automatically.
9. Replay the completed celebration and confirm the automatic-display count does not change.
10. Disable automatic celebrations in learner preferences and confirm the overlay does not open.
11. Confirm an active quiz attempt is not interrupted.
12. Reset the annual QA record as an administrator and repeat the automatic-display flow.
13. Verify responsive behaviour and keyboard navigation.
14. Verify reduced-motion behaviour.

## Database checks

The runtime code uses Moodle DML/XMLDB APIs rather than database-specific SQL. CI covers both MariaDB and PostgreSQL to catch portability issues.
