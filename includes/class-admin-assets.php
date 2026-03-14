<?php
if (!defined('ABSPATH')) exit;

class AT_Sync_Admin_Assets
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue($hook)
    {

        $allowed = [
            'toplevel_page_airtable-sync',
            'airtable-sync_page_airtable-sync-settings',
            'airtable-sync_page_airtable-sync-mapping',
            'airtable-sync_page_airtable-sync-import',
        ];

        if (!in_array($hook, $allowed, true)) {
            return;
        }

        wp_enqueue_style(
            'at-sync-admin',
            plugins_url('../admin/assets/sync.css', __FILE__),
            [],
            filemtime(plugin_dir_path(__FILE__) . '../admin/assets/sync.css')
        );

        wp_enqueue_script(
            'at-sync-admin',
            plugins_url('../admin/assets/sync.js', __FILE__),
            ['jquery'],
            filemtime(plugin_dir_path(__FILE__) . '../admin/assets/sync.js'),
            true
        );

        wp_localize_script(
            'at-sync-admin',
            'ATSync',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('at_sync_nonce'),
            ]
        );
    }
}
new AT_Sync_Admin_Assets();
