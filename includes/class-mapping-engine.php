<?php
if (!defined('ABSPATH')) exit;

class AT_Sync_Mapping_Engine
{
    public static function import_records($records, $post_type, $mapping)
    {
        atsync_log("=== Mapping Engine START ===");
        atsync_log("Total Airtable records: ".count($records));

        $results = [];
        $index = 0;

        foreach ($records as $record) {
            $air_id = $record['id'] ?? 'undefined';

            atsync_log("---- Record #{$index}  AirtableID={$air_id} ----");

            $result = self::import_single($record, $post_type, $mapping);

            $results[] = [
                'record_id' => $air_id,
                'status'    => $result['status'],
                'post_id'   => $result['post_id'],
                'message'   => $result['message']
            ];

            $index++;
        }

        atsync_log("=== Mapping Engine END ===");

        return $results;
    }

    /**
     * Imports a single row
     */
    public static function import_single($record, $post_type, $mapping)
    {
        $fields = $record['fields'] ?? [];

        // Extract title
        $title = self::extract_field($fields, $mapping, 'post_title');

        if (!$title) {
            return [
                'status'  => 'skipped',
                'post_id' => null,
                'message' => 'Missing title'
            ];
        }

        // Try find existing by title
        $existing = get_page_by_title($title, OBJECT, $post_type);

        if ($existing) {
            $post_id = $existing->ID;
            self::update_post($post_id, $fields, $mapping);

            atsync_log("Updated post ID={$post_id} title={$title}");

            return [
                'status'  => 'updated',
                'post_id' => $post_id,
                'message' => 'Updated existing post'
            ];
        }

        // Create new post
        $post_id = wp_insert_post([
            'post_type'   => $post_type,
            'post_status' => 'publish',
            'post_title'  => $title,
        ]);

        if (is_wp_error($post_id)) {
            return [
                'status'  => 'error',
                'post_id' => null,
                'message' => $post_id->get_error_message()
            ];
        }

        self::update_post($post_id, $fields, $mapping);

        atsync_log("Created post ID={$post_id} title={$title}");

        return [
            'status'  => 'created',
            'post_id' => $post_id,
            'message' => 'Post created'
        ];
    }
    public static function import_records_fast($records, $post_type, $mapping)
    {
        global $wpdb;

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $results = [];

        foreach ($records as $i => $record) {

            $fields = $record['fields'] ?? [];
            $title  = self::extract_field($fields, $mapping, 'post_title');

            if (!$title) {
                $skipped++;
                continue;
            }

            // Fast lookup by title
            $existing = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} 
                 WHERE post_title = %s AND post_type = %s LIMIT 1",
                    $title, $post_type
                )
            );

            if ($existing) {
                $post_id = intval($existing);
                self::update_post_fast($post_id, $post_type, $fields, $mapping);
                $updated++;
            } else {
                // Ultra-fast insert (no wp_insert_post)
                $wpdb->insert($wpdb->posts, [
                    'post_title'   => $title,
                    'post_status'  => 'publish',
                    'post_type'    => $post_type,
                    'post_date'    => current_time('mysql'),
                    'post_date_gmt'=> gmdate('Y-m-d H:i:s'),
                ]);

                $post_id = $wpdb->insert_id;

                self::update_post_fast($post_id, $post_type, $fields, $mapping);
                $created++;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped
        ];
    }

    /**
     * Update core/meta/tax fields
     */
    private static function update_post($post_id, $fields, $mapping)
    {
        $core = [];
        $tax  = [];
        $meta = [];

        foreach ($mapping as $air_col => $wp_field) {
            if (!isset($fields[$air_col])) continue;

            $value = $fields[$air_col];

            // core fields
            if (in_array($wp_field, ['post_title', 'post_excerpt', 'post_content'], true)) {
                $core[$wp_field] = $value;
                continue;
            }

            // taxonomy
            if (taxonomy_exists($wp_field)) {
                $tax[$wp_field] = (array)$value;
                continue;
            }

            // meta
            $meta[$wp_field] = $value;
        }

        if (!empty($core)) {
            $core['ID'] = $post_id;
            wp_update_post($core);
        }

        foreach ($tax as $taxonomy => $terms) {
            wp_set_object_terms($post_id, $terms, $taxonomy, false);
        }

        foreach ($meta as $key => $val) {
            update_post_meta($post_id, $key, $val);
        }
    }
    private static function update_post_fast($post_id, $post_type, $fields, $mapping)
    {
        global $wpdb;

        $meta_updates = [];
        $tax_updates  = [];

        foreach ($mapping as $air_col => $wp_field) {

            if (!isset($fields[$air_col])) continue;

            $value = $fields[$air_col];

            // CORE FIELDS
            if (in_array($wp_field, ['post_title','post_excerpt','post_content'], true)) {

                $wpdb->update(
                    $wpdb->posts,
                    [ $wp_field => $value ],
                    ['ID' => $post_id]
                );
                continue;
            }

            // TAXONOMIES
            if (taxonomy_exists($wp_field)) {
                $tax_updates[$wp_field] = (array)$value;
                continue;
            }

            // META
            $meta_updates[$wp_field] = $value;
        }

        // apply taxonomies
        foreach ($tax_updates as $tax => $terms) {
            wp_set_object_terms($post_id, $terms, $tax, false);
        }

        // apply meta
        foreach ($meta_updates as $key => $val) {
            update_post_meta($post_id, $key, $val);
        }
    }
    /**
     * Extract one core field from mapping
     */
    private static function extract_field($fields, $mapping, $target)
    {
        foreach ($mapping as $air_col => $wp_field) {
            if ($wp_field === $target && isset($fields[$air_col])) {
                return $fields[$air_col];
            }
        }
        return null;
    }
}