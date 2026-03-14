<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Menu + Page Loader
 */
class AT_Sync_Admin_Page
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
    }

    /**
     * Register plugin pages in WP Admin
     */
    public function register_menu()
    {
        add_menu_page(
            __('Airtable Sync', 'airtable-sync'),
            __('Airtable Sync', 'airtable-sync'),
            'manage_options',
            'airtable-sync',
            [$this, 'render_main_page'],
            'dashicons-database-import',
            58
        );

        add_submenu_page(
            'airtable-sync',
            __('Settings', 'airtable-sync'),
            __('Settings', 'airtable-sync'),
            'manage_options',
            'airtable-sync-settings',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'airtable-sync',
            __('Field Mapping', 'airtable-sync'),
            __('Field Mapping', 'airtable-sync'),
            'manage_options',
            'airtable-sync-mapping',
            [$this, 'render_mapping_page']
        );

        add_submenu_page(
            'airtable-sync',
            __('Import / Sync', 'airtable-sync'),
            __('Import / Sync', 'airtable-sync'),
            'manage_options',
            'airtable-sync-import',
            [$this, 'render_import_page']
        );

        add_submenu_page(
            'airtable-sync',
            __('Logs', 'airtable-sync'),
            __('Logs', 'airtable-sync'),
            'manage_options',
            'airtable-sync-logs',
            [$this, 'render_logs_page']
        );
    }

    /**
     * Page loaders
     */
    public function render_main_page()
    {
        require plugin_dir_path(__FILE__) . '../admin/page-template.php';
    }

    public function render_settings_page()
    {
        require plugin_dir_path(__FILE__) . '../admin/settings-page.php';
    }

    public function render_mapping_page()
    {
        require plugin_dir_path(__FILE__) . '../admin/mapping-page.php';
    }

    public function render_import_page()
    {
        require plugin_dir_path(__FILE__) . '../admin/import-page.php';
    }

    public function render_logs_page()
    {
        $log = AT_Sync_Logger::read();

        if (isset($_POST['clear_log'])) {
            AT_Sync_Logger::clear();
            $log = AT_Sync_Logger::read();
            echo '<div class="updated"><p>Log cleared.</p></div>';
        }

        ?>
        <div class="wrap">
            <h1>Airtable Sync Logs</h1>

            <form method="post">
                <button class="button button-secondary" name="clear_log" value="1">Clear Log</button>
            </form>

            <textarea style="width:100%; height:500px; margin-top:20px; font-family:monospace;"><?php
                echo esc_textarea($log);
                ?></textarea>
        </div>
        <?php
    }
}

// init
new AT_Sync_Admin_Page();
