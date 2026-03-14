<?php
if (!defined('ABSPATH')) exit;

class AT_Sync_Ajax_Single_Import {

    public function __construct() {
        add_action('wp_ajax_airtable_sync_import_single', [$this, 'import_single']);
    }

    public function import_single() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'at_sync_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $record = isset($_POST['record']) ? $_POST['record'] : null;
        if (!$record || !isset($record['fields'])) {
            wp_send_json_error(['message' => 'Missing record data']);
        }

        $post_type = get_option('airtable_sync_post_type');
        $mapping   = get_option('airtable_sync_mapping');

        if (!$post_type || empty($mapping)) {
            wp_send_json_error(['message' => 'Mapping or post type missing']);
        }

        $result = AT_Sync_Mapping_Engine::import_single($record, $post_type, $mapping);

        wp_send_json_success($result);
    }
}

new AT_Sync_Ajax_Single_Import();