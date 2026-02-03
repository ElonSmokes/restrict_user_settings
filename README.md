# Restrict user settings

Nextcloud app that hides sections in **Settings → User** (personal settings) for non-admin users. Admins control what is visible via **Administration → Restrict user settings**.

## Features

- **Admin panel**: Checkboxes to choose which sections are hidden for non-admin users:
  - Personal info
  - Notifications
  - Sharing
  - Appearance & accessibility
  - Availability flow
  - Privacy
- **Security section**: Option to show only "Devices & sessions" (hide password change, two-factor auth, etc.) for non-admins.
- Admins always see all sections.

## Installation

1. Copy the app into your Nextcloud `apps` directory:
   ```
   cp -r restrict_user_settings /path/to/nextcloud/apps/
   ```
2. Enable the app in **Administration → Apps** (search for "Restrict user settings").

## Configuration

1. Go to **Administration → Restrict user settings**.
2. Check the sections you want to **hide** for non-admin users.
3. Optionally check **Show only "Devices & sessions" in Security**.
4. Click **Save**.

## Compatibility

- Nextcloud 28–33 (community).
- If some sections are not hidden on your version, the Settings app may use different DOM structure; you can adjust selectors in `js/settings-restrict.js` (see `SECTION_SELECTORS`).

## License

AGPL-3.0.
