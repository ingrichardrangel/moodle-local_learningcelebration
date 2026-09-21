# Release checklist

This checklist records the acceptance gate for the Learning Celebration 1.0.0 stable release.

## Source and metadata

- [ ] `version.php` version, release, maturity and supported range are correct.
- [ ] `CHANGELOG.md` contains the release entry.
- [ ] README requirements and installation instructions match the code.
- [ ] Repository, issue tracker and documentation links are public and correct.
- [ ] English language strings contain no hard-coded institution branding.
- [ ] GPL headers and licence files are present.

## Installation and upgrade

- [ ] Clean ZIP installation succeeds with developer debugging enabled.
- [ ] Upgrade from 0.8.1 succeeds without warnings or data loss.
- [ ] Upgrade from a pre-0.8.1 installation still executes the table rename safely.
- [ ] Uninstall removes plugin tables/config through Moodle and removes the plugin-owned user preference.
- [ ] Reinstallation succeeds after uninstall.

## Security and privacy

- [ ] Learner features require `local/learningcelebration:view`.
- [ ] Administrator diagnostics/QA require `local/learningcelebration:manage`.
- [ ] Site configuration requires `moodle/site:config`.
- [ ] State changes validate login, capability, parameters and eligible celebration year.
- [ ] Administrative state changes require POST/sesskey where applicable.
- [ ] Privacy metadata matches actual stored data.
- [ ] Privacy export/delete/user-list tests pass.
- [ ] No external service receives learner data.
- [ ] `PRIVACY.md` and `SECURITY.md` match the implementation.

## Accessibility and presentation

- [ ] Keyboard navigation works through every slide.
- [ ] Focus is trapped while the automatic overlay is open and restored after close.
- [ ] Escape safely defers the celebration.
- [ ] `prefers-reduced-motion` is respected.
- [ ] Site-level motion/confetti controls work.
- [ ] Desktop and mobile layouts remain usable.

## Automated quality gate

- [ ] Moodle 4.5 / PHP 8.1 / MariaDB green.
- [ ] Moodle 4.5 / PHP 8.3 / PostgreSQL green.
- [ ] Moodle 5.2 / PHP 8.3 / MariaDB green.
- [ ] Moodle 5.2 / PHP 8.4 / PostgreSQL green.
- [ ] Moodle 5.3 / PHP 8.3 / MariaDB green.
- [ ] Moodle 5.3 / PHP 8.4 / PostgreSQL green.
- [ ] PHP lint green.
- [ ] Moodle Code Checker green with zero warnings.
- [ ] PHPDoc Checker green with zero warnings.
- [ ] Plugin validation green.
- [ ] Upgrade savepoints green.
- [ ] Mustache lint green.
- [ ] Grunt/AMD/CSS validation green.
- [ ] PHPUnit green.
- [ ] Behat green.

## Marketplace materials

- [ ] Short description finalised.
- [ ] Full description finalised.
- [ ] Screenshot set contains no personal or institution-sensitive data.
- [ ] Reviewer test path is documented.
- [ ] Supported Moodle versions match proven CI/manual results.
- [ ] Free/GPL distribution information is correct.
- [ ] No award is claimed before Moodle grants it.
