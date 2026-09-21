# Changelog

## 1.0.0 - 2026-09-21

### Stable release

- First stable release of Learning Celebration.
- Declares support for Moodle 4.5 through Moodle 5.3.
- Promotes the fully tested 0.9.0 feature set to `MATURITY_STABLE` without learner-facing runtime changes.
- Applies final interface polish to the birthday overlay and reorganises the Configuration status experience for clearer spacing and administrator review.
- Keeps the privacy-minimised birthday-to-birthday recap, adaptive presentation modes, safe-page automatic display, replay, learner opt-out, administrator simulation/preview tools and configurable recap policy.
- Retains full Moodle Privacy API coverage and no external-service dependencies.
- Retains PHPUnit and Behat coverage with Moodle Plugin CI across Moodle 4.5, 5.2 and the Moodle 5.3 code line, MariaDB and PostgreSQL.

### Release validation

- PHP lint, Moodle Code Checker, PHPDoc Checker, plugin validation, upgrade savepoints, Mustache, Grunt/AMD/CSS, PHPUnit and Behat are green in the public CI matrix used for the stable release.
- Moodle 5.3 compatibility is explicitly declared for Marketplace publication and Early bird 5.3 eligibility.

## 0.9.0 - 2026-09-21

### Added

- Moodle 5.3 to the declared support range for beta compatibility validation.
- Moodle 5.3 / PHP 8.3 / MariaDB and Moodle 5.3 / PHP 8.4 / PostgreSQL jobs to Moodle Plugin CI.
- Marketplace-facing description, privacy/testing statements, suggested tags, reviewer test path and screenshot checklist in `docs/MARKETPLACE.md`.
- Public testing documentation in `docs/TESTING.md`.
- A 1.0 release-candidate acceptance gate in `docs/RELEASE_CHECKLIST.md`.
- Public repository, issue-tracker and CI links in the README.

### Changed

- Plugin maturity changed from alpha to beta.
- README refocused from development milestones to installation, behaviour, privacy, security, testing and administrator QA.
- Security reporting guidance now points users to the public issue tracker for non-sensitive defects and private contact/reporting for vulnerabilities.
- Feature set frozen for the 1.0 review-candidate phase.

### Notes

- This release adds no learner-facing features and makes no database-schema changes.
- Moodle 5.3 remains a beta compatibility target until the new CI jobs and manual checks are green.

## 0.8.1 - 2026-09-21

- CI remediation release with no new learner-facing features.
- Renames the annual view table to `local_learningcelebration_vw` for Moodle plugin validation while preserving existing data during upgrade.
- Corrects PHPUnit privacy-context handling, page-policy testability, Behat navigation steps, PHPDoc, Moodle coding style, Mustache example contexts and CSS lint findings.
- Normalises Privacy API context identifiers before strict comparison so export and deletion remain reliable across database drivers.
- Adds `PRIVACY.md` to document data minimisation, ownership, external-service use, export and deletion behaviour.
- Keeps the 0.8.0 automated quality workflow and compatibility matrix unchanged.

## 0.8.0 - 2026-09-18

### Added

- GitHub Actions workflow powered by Moodle Plugin CI.
- Compatibility matrix covering Moodle 4.5 and 5.2, MariaDB and PostgreSQL, and relevant PHP versions.
- Automated PHP lint, Moodle Code Checker, PHPDoc, plugin validation, upgrade-savepoint, Mustache, Grunt/AMD, PHPUnit and Behat checks.
- Behat smoke coverage for administrator diagnostics/preview, learner preferences and unauthenticated access protection.
- Capability regression tests for learner-facing and manager-facing defaults.
- Additional annual-view repository tests for idempotent completion, latest-completed lookup and targeted deletion.
- Additional Privacy API regression tests for view-only users and context-wide annual-view deletion.
- `.editorconfig` and `CONTRIBUTING.md` for public repository consistency.

### Changed

- Normalised abbreviated PHP GPL headers in older source/test files for cleaner Moodle coding-style review.
- No learner-facing behaviour, database schema, analytics rules or stored personal-data fields were changed in this release.

## 0.7.0 - 2026-09-18

### Added

- Learner-facing `local/learningcelebration:view` capability with authenticated-user archetype default.
- Administrative `local/learningcelebration:manage` capability for diagnostics, preview and QA reset.
- Moodle External Services function `local_learningcelebration_update_celebration_state` for overlay completion/snooze actions.
- `core/ajax` repository module for state-changing browser requests.
- Server-side action validation that derives the authoritative celebration year from the configured birthday and current birthday window.
- `SECURITY.md` documenting the plugin security and data-handling model.
- Privacy regression tests for users who store only the auto-show preference.

### Changed

- Removed the plugin-owned `action.php` AJAX endpoint in favour of Moodle's standard External Services/AJAX stack.
- Automatic display, preferences and replay now enforce the learner-facing capability.
- Configuration status, visual preview and QA reset now enforce the plugin management capability.
- QA reset is explicitly POST-only in addition to sesskey validation.
- Manual replay is read-only and no longer increments the automatic-display counter.
- Birthday session-cache signature now includes the user id and `timemodified` value to reduce stale profile data.
- Page safety uses Moodle submitted-data detection instead of direct request-superglobal inspection.
- Replaced the single-column completed index with a composite `(userid, completed, celebrationyear)` lookup index.

### Fixed

- Privacy context discovery now includes users who have only the auto-show preference and no annual view row.
- Context-wide Privacy API deletion now removes plugin-owned auto-show preferences as well as annual view records.
- Privacy user-list discovery now includes preference-only users.
- View-record creation handles concurrent browser-tab insert races without surfacing the annual unique-key collision to the learner.

## 0.6.0 - 2026-09-18

### Added

- Site-level Learning recap content settings for completed courses, completed activities, badges, visible final grades, active-enrolment welcome context, and year-over-year comparison.
- Metric policy applied before data-richness classification and experience selection.
- Site-level motion and confetti controls while preserving browser/OS reduced-motion behaviour.
- Configuration-status diagnostics for the effective content and presentation policy.
- Automated tests for metric filtering, comparison suppression and presentation options.

### Changed

- Live preview, automatic display and replay now all use the same configured metric and presentation policy.
- Disabling grades also removes the best-course highlight and prevents grades from contributing to richness.
- Disabling comparison falls back to Learning Year without removing the annual recap.
- No database schema changes are required for this release.


## 0.5.0 - 2026-09-18

### Added
- Automatic full-screen Learning Celebration overlay on eligible, safe Moodle pages.
- Moodle 4.5 output hook registration using `core\\hook\\output\\before_footer_html_generation`.
- Safe-page policy excluding administration, authentication, embedded/popup/secure layouts, non-GET requests, and active quiz attempts.
- Annual `local_lc_views` persistence table for display/completion tracking.
- Session-scoped **Remind me later** action.
- Persistent completion action so a finished celebration is not auto-shown again in the same birthday year.
- User preference to opt out of automatic celebrations.
- User-facing replay page and profile/settings navigation links.
- Focus management, focus trapping, Escape-to-snooze and body scroll locking for the modal overlay.
- Full Privacy API support for view records and the auto-show user preference.
- PHPUnit coverage for page-safety and persistence flows.
- Uninstall cleanup for the plugin-owned auto-show user preference.

### Changed
- Visual controller now supports both static preview/replay mode and automatic overlay mode.
- The plugin now stores minimal personal data required to remember annual display/completion state; analytics and birth dates remain read-only and are not duplicated.

## 0.4.0 - 2026-09-18

### Added

- Administrator-only **Celebration preview** page.
- First reusable visual Learning Celebration experience built with a Moodle Mustache template.
- Adaptive slide decks for Celebration only, Birthday Welcome, Learning Year, and Learning Year + comparison.
- Real-data preview powered by the already validated Birthday Engine and Learning Analytics Engine.
- Deterministic demonstration datasets so administrators and Marketplace reviewers can inspect every experience without editing user records.
- Keyboard-accessible previous/next/replay navigation and direct slide controls.
- Responsive layout, reduced-motion support, and non-blocking decorative confetti.
- Zero-value learning metrics are omitted from learner-facing presentation data.
- Presentation builder PHPUnit coverage.

### Behaviour

- Automatic learner-facing celebrations remain intentionally disabled in 0.4.0.
- Opening the preview never records a celebration as viewed and does not create plugin-owned personal-data records.
- No external JavaScript, CSS, fonts, images, CDNs or services are used.

## 0.3.0 - 2026-09-18

### Added

- Read-only Learning Analytics Engine for the two completed birthday-to-birthday periods.
- Completed-course statistics from Moodle course completion records.
- Completed-activity statistics from Moodle activity completion records.
- Badge issuance statistics.
- Visible numeric final-course-grade average and best-result summary for completed courses.
- Current active-course-enrolment context.
- Account-age context for newly created users.
- Adaptive `NONE`, `LOW`, `STANDARD` and `RICH` data sufficiency classification.
- Suggested celebration modes for Celebration only, Birthday Welcome, Learning Year and Learning Year + comparison.
- Learning Analytics diagnostics on the Configuration status page.
- PHPUnit coverage for data-richness classification and analytics-engine orchestration.

### Privacy and behaviour

- Analytics are calculated on demand from Moodle core tables and are not persisted by the plugin.
- Hidden course grades and hidden courses are excluded from grade highlights.
- Activity completion uses the current completion row timestamp and explicitly documents that later completion-state changes can alter historical attribution.
- Course grade statistics use completion time for period membership and the current visible final course grade for the displayed result.

## 0.2.1 - 2026-09-18

### Added

- Administrator-only birthday-engine simulator on the Configuration status page.
- Independent simulated birthday and current-date inputs without modifying user profile data or the server clock.
- Simulation overrides for celebration window and 29 February policy.
- Days-until-next-birthday diagnostic value.
- PHPUnit coverage for simulator date validation and leap-day simulation.

### Changed

- Refactored the status page so live and simulated engine results use the same diagnostic-table rendering path.
- Simulator form submits by POST with Moodle sesskey validation and does not persist test values.

### Fixed

- Birthday timestamps before 1 January 1970 are no longer treated as missing or invalid, allowing older learners' birth dates to be evaluated correctly.

## 0.2.0 - 2026-09-18

### Added

- User-timezone-aware birthday calculation engine.
- Configurable celebration window: birthday only, or birthday plus 1, 3 or 7 days.
- Configurable 29 February policy for non-leap years.
- Most recent and next observed birthday calculations.
- Most recently completed birthday-to-birthday learning period.
- Previous learning period for later year-over-year comparisons.
- Correct delayed-window handling across the December-to-January year boundary.
- Birthday-engine diagnostics on the administrator Configuration status page.
- PHPUnit coverage for core calendar scenarios and leap-day behaviour.

### Changed

- Expanded the Configuration status page to display the birthday engine result for the current administrator when a birthday value is available.
- Updated Privacy API wording to cover birthday eligibility and period calculations without plugin-owned personal-data storage.

## 0.1.2 - 2026-09-18

### Fixed

- Fixed the administrator Configuration status page by explicitly loading Moodle's `lib/adminlib.php` before calling `admin_externalpage_setup()`.

## 0.1.1 - 2026-09-18

### Fixed

- Fixed the Birthday profile field selector so it displays actual Date/Time custom profile fields instead of the PHP callback class and method names.

## 0.1.0 - 2026-09-18

### Added

- Initial local plugin skeleton.
- Moodle 4.5 baseline and support metadata through Moodle 5.2.
- Enable/disable setting.
- Birthday Date/Time profile-field selector.
- Administrator configuration status page.
- Profile-field repository abstraction for later birthday detection.
- Initial Privacy API declaration.
- Installation and configuration documentation.
