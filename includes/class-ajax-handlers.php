<?php
if (!defined('ABSPATH')) exit;

class AT_Sync_Ajax_Handlers
{
    public function __construct()
    {
        // Mapping UI
        add_action('wp_ajax_airtable_get_columns',      [$this, 'get_columns']);
        add_action('wp_ajax_airtable_get_wp_fields',    [$this, 'get_wp_fields']);
        add_action('wp_ajax_airtable_save_mapping',     [$this, 'save_mapping']);

        // Sync execution
        add_action('wp_ajax_airtable_sync_run',         [$this, 'run_sync']);

        // Optional "old import" endpoints
        add_action('wp_ajax_airtable_pull',             [$this, 'pull']);
        add_action('wp_ajax_airtable_import',           [$this, 'import']);
    }

    // ----------------------------
    // HELPERS
    // ----------------------------

    private function verify_nonce()
    {
        if (!isset($_POST['_ajax_nonce']) && !isset($_POST['nonce'])) {
            wp_send_json_error(['message' => 'Missing nonce'], 400);
        }

        $nonce = $_POST['_ajax_nonce'] ?? $_POST['nonce'];

        if (!wp_verify_nonce($nonce, 'at_sync_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce'], 400);
        }
    }

    // ----------------------------
    // LOAD AIRTABLE COLUMNS
    // ----------------------------
    public function get_columns()
    {
        $this->verify_nonce();

        $client = new AT_Sync_Airtable_Client();
        $fields = $client->detect_columns();
        if (is_wp_error($fields)) {
            wp_send_json_error(['message' => $fields->get_error_message()]);
        }

        wp_send_json_success([
            'columns' => $fields
        ]);
    }

    // ----------------------------
    // LOAD WP FIELDS (core/meta/tax)
    // ----------------------------
    public function get_wp_fields()
    {
        $this->verify_nonce();

        $post_type = get_option('airtable_sync_post_type', '');

        if (!$post_type) {
            wp_send_json_error(['message' => 'No post type selected']);
        }

        $fields = AT_Sync_Field_Detector::detect($post_type);

        $flat = [];
        foreach ($fields as $group => $items) {
            foreach ($items as $key => $label) {
                $flat[] = [
                    'key'   => $key,
                    'label' => $label,
                    'type'  => $group
                ];
            }
        }

        wp_send_json_success($flat);
    }

    // ----------------------------
    // SAVE MAPPING
    // ----------------------------
    public function save_mapping()
    {
        $this->verify_nonce();

        if (!isset($_POST['mapping'])) {
            wp_send_json_error(['message' => 'Missing mapping']);
        }

        $mapping = $_POST['mapping'];

        update_option('airtable_sync_mapping', $mapping);

        wp_send_json_success(['saved' => true]);
    }

    // ----------------------------
    // RUN SYNC
    // ----------------------------
    public function run_sync()
    {
        $this->verify_nonce();

        $post_type = get_option('airtable_sync_post_type');
        $mapping   = get_option('airtable_sync_mapping');

        if (!$post_type || empty($mapping)) {
            error_log('[AT Sync] Mapping or post type not set');
            wp_send_json_error(['message' => 'Mapping or post type not set']);
        }
        error_log('[AT Sync] AJAX run_sync start, post_type=' . $post_type);

        $sync = new AT_Sync_Manager();
        $result = $sync->run_sync($post_type, $mapping);
        error_log('[AT Sync] AJAX run_sync finished');

        wp_send_json($result);
    }

    // ----------------------------
    // OLD: airtable_pull
    // ----------------------------
    public function pull()
    {
        $this->verify_nonce();

        $client = new AT_Sync_Airtable_Client();
        $records = $client->fetch_rows();

        if (is_wp_error($records)) {
            wp_send_json_error(['message' => $records->get_error_message()]);
        }

        wp_send_json([
            'records' => $records
        ]);
    }

    // ----------------------------
    // OLD: airtable_import
    // ----------------------------
    public function import()
    {
        $this->verify_nonce();

        $records = $_POST['records'] ?? [];

        if (!$records) {
            wp_send_json_error(['message' => 'No records for import']);
        }

        $sync = new AT_Sync_Manager();
        $result = $sync->import_records($records);

        wp_send_json($result);
    }
}

new AT_Sync_Ajax_Handlers();