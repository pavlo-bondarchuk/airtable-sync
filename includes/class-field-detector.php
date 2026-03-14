<?php

if (!defined('ABSPATH')) {
    exit;
}

class AT_Sync_Field_Detector
{
    /**
     * Detect all fields & taxonomies for a CPT.
     * Returns structure for mapping UI.
     *
     * [
     *   'core' => [ 'post_title', 'post_excerpt', 'post_content' ],
     *   'meta' => [ 'description', 'conclusion', ... ],
     *   'tax'  => [ 'program-category', 'program-format', ... ]
     * ]
     */
    public static function detect($post_type)
    {
        if (!$post_type) {
            return [];
        }

        $result = [
            'core' => self::get_core_fields(),
            'tax'  => self::get_taxonomies($post_type),
            'meta' => self::get_acf_fields($post_type),
        ];

        return $result;
    }

    /**
     * Default WP core fields.
     */
    private static function get_core_fields()
    {
        return [
            'post_title'   => __('Title', 'airtable-sync'),
            'post_excerpt' => __('Excerpt', 'airtable-sync'),
            'post_content' => __('Content', 'airtable-sync'),
        ];
    }

    /**
     * All taxonomies registered for the CPT.
     */
    private static function get_taxonomies($post_type)
    {
        $taxes = get_object_taxonomies($post_type, 'objects');
        $out = [];

        foreach ($taxes as $tax) {
            $out[$tax->name] = $tax->labels->singular_name;
        }

        return $out;
    }

    /**
     * Detect ACF fields assigned to this CPT.
     * Only single-value text fields for now.
     */
    private static function get_acf_fields($post_type)
    {
        if (!function_exists('acf_get_field_groups')) {
            return [];
        }

        $groups = acf_get_field_groups(['post_type' => $post_type]);
        $fields = [];

        foreach ($groups as $group) {
            $field_objects = acf_get_fields($group['ID']);
            if (!$field_objects) continue;

            foreach ($field_objects as $field) {

                // Only safe simple fields for first version
                $allowed = ['text', 'textarea', 'number', 'email', 'url'];

                if (!in_array($field['type'], $allowed, true)) continue;

                $fields[$field['name']] = $field['label'];
            }
        }

        return $fields;
    }
}
