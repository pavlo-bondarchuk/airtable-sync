<?php
if (!defined('ABSPATH')) exit;

class AT_Sync_Manager
{
    private $client;

    public function __construct()
    {
        $this->client = new AT_Sync_Airtable_Client();
    }

    /**
     * Main sync runner
     */
    public function run_sync($post_type, $mapping)
    {
        atsync_log("TurboSync: START for post_type={$post_type}");

        // -----------------------------
        // TURBO MODE: Disable heavy WP hooks
        // -----------------------------
        atsync_log("TurboSync: disabling all heavy WP hooks");

        remove_all_actions('save_post');
        remove_all_actions('save_post_' . $post_type);
        remove_all_actions('wp_after_insert_post');
        remove_all_actions('transition_post_status');
        remove_all_actions('clean_post_cache');
        remove_all_actions('post_updated');
        remove_all_actions('future_to_publish');

        remove_all_actions('acf/save_post');
        remove_all_actions('acf/update_value');

        // Stop term + comment recount
        wp_defer_term_counting(true);
        wp_defer_comment_counting(true);

        // Disable cache invalidation (CRITICAL speed boost)
        if (function_exists('wp_suspend_cache_invalidation')) {
            wp_suspend_cache_invalidation(true);
        }

        // Disable revisions (huge speed boost on GoDaddy)
        remove_action('post_updated', 'wp_save_post_revision');
        add_filter('wp_revisions_to_keep', '__return_zero');

        // -----------------------------
        // STEP 1 — Fetch all records
        // -----------------------------
        $records = $this->client->fetch_rows();

        if (empty($records) || is_wp_error($records)) {
            return [
                'success' => false,
                'message' => 'Failed to fetch Airtable records'
            ];
        }

        atsync_log("TurboSync: fetched ".count($records)." Airtable records");

        // -----------------------------
        // STEP 2 — Process records FAST
        // -----------------------------
        require_once AT_SYNC_PATH . 'includes/class-mapping-engine.php';

        $results = AT_Sync_Mapping_Engine::import_records_fast(
            $records,
            $post_type,
            $mapping
        );

        // -----------------------------
        // RESTORE NORMAL WORDPRESS MODE
        // -----------------------------
        atsync_log("TurboSync: restoring WordPress hooks");

        wp_defer_term_counting(false);
        wp_defer_comment_counting(false);

        if (function_exists('wp_suspend_cache_invalidation')) {
            wp_suspend_cache_invalidation(false);
        }

        return [
            'success' => true,
            'message' => 'Turbo sync completed',
            'imported' => $results['created'],
            'updated' => $results['updated'],
            'skipped' => $results['skipped']
        ];
    }
}