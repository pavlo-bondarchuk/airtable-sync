<?php

/**
 * Plugin Name: Airtable → WordPress Sync
 * Description: Sync Airtable tables with WordPress post types (core fields, taxonomies, ACF). Includes mapping UI, batch import, safe throttled syncing and logging.
 * Version:     1.0.0
 * Author:      Pavlo Bondarchuk
 * Author URI:  https://bonddesign.top/
 * Text Domain: airtable-sync
 * Domain Path: /languages
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AT_SYNC_PATH', plugin_dir_path(__FILE__));
define('AT_SYNC_URL', plugin_dir_url(__FILE__));

require_once AT_SYNC_PATH . 'includes/class-airtable-client.php';
require_once AT_SYNC_PATH . 'includes/class-field-detector.php';
require_once AT_SYNC_PATH . 'includes/class-mapping-engine.php';
require_once AT_SYNC_PATH . 'includes/class-sync-manager.php';
require_once AT_SYNC_PATH . 'includes/admin-page.php';
require_once AT_SYNC_PATH . 'includes/class-ajax-handlers.php';
require_once AT_SYNC_PATH . 'includes/class-admin-assets.php';
require_once AT_SYNC_PATH . 'includes/class-logger.php';
require_once AT_SYNC_PATH . 'includes/class-ajax-handler-single-import.php';