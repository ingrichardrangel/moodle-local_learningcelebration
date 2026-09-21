# Contributing to Learning Celebration

Thank you for helping improve Learning Celebration.

## Development baseline

The plugin currently declares support for Moodle 4.5 through Moodle 5.2. Changes should use Moodle core APIs and remain database-independent unless a documented platform limitation requires otherwise.

## Before opening a pull request

1. Keep learner data processing minimal and do not add external services without documenting the privacy and security implications.
2. Add or update PHPUnit tests for business logic and persistence changes.
3. Add or update Behat coverage for user-visible workflows when practical.
4. Keep all interface text in Moodle language strings.
5. If AMD source changes, regenerate the corresponding files in `amd/build` with Moodle Grunt before committing.
6. Update `CHANGELOG.md` when behaviour changes.

## Automated checks

The repository includes `.github/workflows/ci.yml`, based on Moodle Plugin CI. Pushes and pull requests are checked against a compatibility matrix covering Moodle 4.5 and 5.2, MariaDB and PostgreSQL, and the relevant PHP generations.

The workflow runs PHP linting, Moodle Code Checker, PHPDoc validation, plugin validation, upgrade-savepoint checks, Mustache linting, JavaScript/AMD validation, PHPUnit, and Behat smoke tests.

Moodle Plugin CI can also be run locally when its dependencies are available. The GitHub workflow is the canonical automated compatibility check for this project.

## Security reports

Please follow `SECURITY.md` and avoid publishing sensitive vulnerability details or real learner data in public issues.
