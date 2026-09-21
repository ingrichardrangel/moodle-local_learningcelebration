# Moodle Marketplace preparation

This file contains draft Marketplace-facing material for Learning Celebration. It is intentionally kept in the repository so the public description can stay aligned with the shipped plugin.

## Plugin identity

- Name: Learning Celebration
- Frankenstyle component: `local_learningcelebration`
- Plugin type: Local plugin
- Licence: GNU GPL v3 or later
- Distribution: Free
- Maintainer: Richard Rangel
- Source: https://github.com/ingrichardrangel/moodle-local_learningcelebration
- Issues: https://github.com/ingrichardrangel/moodle-local_learningcelebration/issues
- Dependencies: None outside Moodle core
- External services: None

## Short description

Celebrate learners on their birthday with a privacy-conscious annual recap of course completions, activities, badges and optional grades.

## Full description

Learning Celebration adds an optional birthday experience to Moodle. During a configurable birthday window, an eligible learner can receive a full-screen celebration containing an annual learning recap calculated from Moodle core records.

The recap adapts to the amount of meaningful history available. A newly registered learner is welcomed without being shown a wall of zero-value statistics, while learners with richer history can see completed courses, completed activities, badges and optional visible final course grades. When sufficient information exists for two completed birthday-to-birthday periods, the site may also enable a neutral year-over-year comparison.

Administrators choose which Date/Time custom profile field contains the date of birth and may independently disable grades, badges, activity counts, course counts, active-enrolment context or annual comparisons. Motion and decorative confetti can also be disabled. Browser-level reduced-motion preferences are respected.

The plugin avoids interrupting sensitive pages such as active quiz attempts and administration/authentication flows. Learners can defer the celebration, complete it, replay the most recently completed celebration or opt out of automatic display.

Learning Celebration follows a data-minimisation model. It does not duplicate dates of birth, grades, courses, badges or calculated analytics. Plugin-owned storage contains only the annual display/completion state required to avoid repeatedly showing the same celebration, plus an optional opt-out preference. The Moodle Privacy API covers export and deletion of this data. No external services, analytics platforms, CDNs, AI services or third-party libraries are required.

The public repository includes PHPUnit and Behat coverage with Moodle Plugin CI across supported Moodle/PHP/database combinations.

## Suggested tags

- birthday
- learner engagement
- learner experience
- learning analytics
- recognition
- student experience

## Supported versions for 1.0.0

Learning Celebration 1.0.0 declares Moodle 4.5 through Moodle 5.3 as its supported range. Moodle 5.3 compatibility was exercised through the public automated-test matrix before the stable release.

## Privacy statement for the listing

Learning Celebration reads an administrator-selected Date/Time profile field and selected Moodle core learning records only to build the birthday experience. Dates of birth and learning analytics are not copied into plugin-owned storage. The plugin stores only annual display/completion state and an optional automatic-display preference. No personal data is sent to external services.

## Early bird 5.3 release note

Version 1.0.0 explicitly declares Moodle 5.3 support after automated compatibility testing. Publish the 5.3-compatible Marketplace version before the Early bird 5.3 deadline. Do not claim the badge until Moodle grants it.

## Automated testing statement for the listing

The repository includes PHPUnit and Behat tests. GitHub Actions runs Moodle Plugin CI with linting, coding-style checks, PHPDoc validation, plugin validation, Mustache/AMD checks, PHPUnit and Behat across multiple Moodle, PHP and database combinations.

Do not describe the plugin as holding Moodle awards until Moodle has actually granted them. The implementation is being prepared to support review for Privacy friendly and Automated testing support recognition.

## Screenshot set to capture before submission

Capture screenshots from a clean test site with non-sensitive demonstration data. Recommended set:

1. Birthday hero / first celebration screen.
2. Learning Year metrics screen.
3. Year-over-year comparison screen.
4. Administrator plugin settings.
5. Configuration status / birthday simulator.
6. Optional mobile view showing responsive behaviour.

Avoid real learner names, real dates of birth, grades or institution-only information. Use the deterministic Celebration preview where possible.

## Reviewer test path

1. Install the ZIP from Moodle's plugin installer.
2. Create a Date/Time custom user profile field named, for example, `Date of birth`.
3. Select that field in Learning Celebration settings.
4. Use Configuration status to confirm the field is recognised.
5. Use the simulator to test a birthday without changing the server clock.
6. Use Celebration preview to inspect all four adaptive presentation modes.
7. For automatic-display testing, set a test user's birthday inside the configured window and open a normal non-administrative page.
8. Verify Remind me later, completion, replay and user opt-out behaviour.
9. Verify no automatic overlay appears during an active quiz attempt.
