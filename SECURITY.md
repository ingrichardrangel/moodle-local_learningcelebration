# Security policy

Learning Celebration is designed to operate entirely inside Moodle and does not transmit learner data to external services.

## Security model

- Learner-facing features require `local/learningcelebration:view`.
- Administrative diagnostics and QA tools require `local/learningcelebration:manage`.
- Site-wide configuration continues to require Moodle's `moodle/site:config` capability.
- State-changing overlay actions use Moodle's External Services API and `core/ajax` rather than a plugin-owned AJAX endpoint.
- External-function parameters are type-validated, the user context is validated, and the server independently resolves the currently eligible celebration year before accepting an action.
- A completion/snooze request is accepted only after a server-rendered annual view record exists for that user and year.
- Administrator QA reset is POST-only and protected by Moodle's session key.
- Database access uses Moodle DML with placeholders and XMLDB schema definitions.
- Templates use Moodle Mustache rendering; no external scripts, styles, fonts, images, analytics services or AI services are loaded.

## Data handled

The configured birthday remains in Moodle's custom profile-field storage. Learning Celebration reads it to determine birthday eligibility but does not copy it into plugin-owned storage.

The plugin-owned `local_learningcelebration_vw` table stores only annual display/completion state. Learning metrics are calculated on demand from Moodle core data and are not stored as analytics snapshots.

The Privacy API covers both annual view records and the optional auto-show opt-out preference, including users who have only the preference and no annual view record.

## Reporting a vulnerability

Do not publish credentials, personal data, exploit payloads against a live site or other sensitive vulnerability details in a public issue.

For non-sensitive defects, use the public issue tracker:
https://github.com/ingrichardrangel/moodle-local_learningcelebration/issues

For a security-sensitive report, contact the maintainer privately through the GitHub repository/profile or use GitHub private vulnerability reporting if it is enabled for the repository. Include the affected version, reproduction steps, expected behaviour and observed behaviour, and allow reasonable time for investigation before public disclosure.
