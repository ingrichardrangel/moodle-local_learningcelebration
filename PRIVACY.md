# Privacy design

Learning Celebration is designed around data minimisation. It reads learning and profile data from Moodle only when needed to build a birthday celebration and does not create a separate analytics history.

## Data read from Moodle

Depending on administrator settings, Learning Celebration can read:

- the configured Date/Time custom profile field used as the user's date of birth;
- course completion records;
- activity completion records;
- badge awards;
- visible numeric final course grades for completed courses;
- active course enrolments;
- basic user information required to render the experience, such as the user's first name and timezone.

These values remain owned by Moodle and are not copied into plugin-owned analytics tables.

## Data stored by Learning Celebration

The plugin-owned `local_learningcelebration_vw` table stores only the minimum annual state needed to prevent repeated automatic celebrations:

- user id;
- observed birthday year;
- first automatic-display timestamp;
- most recent automatic-display/completion timestamp;
- automatic-display count;
- completion state;
- completion timestamp.

The user's automatic-display opt-out is stored through Moodle's user preference API only when the user changes the default behaviour.

Learning Celebration does **not** store a copy of the user's date of birth, grades, badges, enrolments, course/activity completion data, or generated recap statistics.

## External services

Learning Celebration sends no personal data to external services. It has no analytics SDK, CDN, remote font, advertising integration, or AI service dependency.

The browser action used to snooze or complete a celebration is handled by Moodle's own External Services/AJAX infrastructure on the same Moodle site.

## Privacy API

Learning Celebration implements Moodle's Privacy API for plugin-owned data and preferences. The provider supports:

- metadata declaration;
- discovery of users/contexts containing plugin-owned data;
- export of annual celebration records and the auto-show preference;
- deletion for one approved user;
- deletion for approved user lists;
- context-wide deletion.

The configured date-of-birth field and Moodle learning records are not exported by this plugin because Learning Celebration does not own those records; they are handled by the Moodle components that store them.

## Retention

Learning Celebration does not impose an independent retention schedule. Plugin-owned records remain until removed by Moodle privacy operations, uninstall/administrative processes, or future retention features explicitly introduced by the site/plugin.

## Design principle

New features should avoid storing additional personal data unless persistence is required for the feature to work. When new plugin-owned personal data is introduced, the Privacy API metadata, export, deletion and automated tests must be updated in the same release.
