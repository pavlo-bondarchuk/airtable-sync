=== Airtable → CPT Sync ===
Contributors: bonddesign
Tags: airtable, sync, import, mapping, acf
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sync Airtable with WordPress posts and custom fields using a clean mapping UI and safe batch import.

== Description ==

Airtable → WordPress Sync is a lightweight, developer-friendly tool for importing and updating WordPress posts using Airtable data.

It allows you to:

* fetch Airtable records using your API key
* map Airtable columns to WordPress fields (title, content, excerpt)
* map to taxonomies (categories, custom taxonomies)
* map to meta fields (including ACF fields)
* run batch imports with throttling to avoid host limits
* track the entire process in a built-in log viewer
* safely re-import updated items
* temporarily disable heavy WordPress hooks and plugins during sync for maximum performance

No bloated UI. No background daemons. Works even on shared hosting.

### ✨ Features

- Visual field mapping interface
- Core + taxonomy + ACF support
- Secure AJAX-based syncing
- Real-time progress reporting
- Logging with rotation
- Slow-host protection (GoDaddy, Bluehost, SiteGround)
- Compatible with WPML and Polylang
- Supports JSON, arrays, and multi-select Airtable fields
- Zero external dependencies

### 🔧 For Developers

- Extendable import engine
- Clean OOP architecture
- Actions/filters before & after mapping
- Optional WP-CLI commands (coming soon)

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/airtable-sync`
2. Activate the plugin in WordPress admin
3. Go to **Airtable Sync → Settings**
4. Enter API key, Base ID, Table name, and select your target post type
5. Go to **Field Mapping** and configure mappings
6. Go to **Import / Sync** and click **Sync Now**

== Screenshots ==

1. Field Mapping screen
2. Import / Sync progress
3. Log viewer
4. Settings panel

== Frequently Asked Questions ==

= Does this plugin require ACF? =
No, ACF fields work automatically like regular meta fields.

= Is this a one-way sync? =
Yes. Airtable → WordPress only.

= Is automatic sync supported? =
Not yet. Cron-based syncing is planned for version 1.2.

= Does the plugin support image import? =
Image import is planned for version 1.1.

== Changelog ==

= 1.0.0 =
* Initial release
* Airtable fetch
* Mapping UI
* Batch import
* Progress reporting
* Logging system
* Safe mode (disables heavy hooks during sync)

== Upgrade Notice ==
Version 1.0.0 is the initial release. No breaking changes.