<?php
if (!defined('ABSPATH')) exit;

$option_api_key  = get_option('airtable_api_key', '');
$option_base_id  = get_option('airtable_base_id', '');
$option_table    = get_option('airtable_table', '');
$option_posttype = get_option('airtable_sync_post_type', '');
$option_autodetect = get_option('airtable_autodetect_fields', 1);

$post_types = get_post_types(['public' => true], 'objects');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_admin_referer('airtable_sync_settings_save', 'airtable_sync_nonce');

    update_option('airtable_api_key', sanitize_text_field($_POST['airtable_api_key'] ?? ''));
    update_option('airtable_base_id', sanitize_text_field($_POST['airtable_base_id'] ?? ''));
    update_option('airtable_table', sanitize_text_field($_POST['airtable_table'] ?? ''));
    update_option('airtable_sync_post_type', sanitize_text_field($_POST['airtable_sync_post_type'] ?? ''));
    update_option('airtable_autodetect_fields', isset($_POST['airtable_autodetect_fields']) ? 1 : 0);

    echo '<div class="updated"><p>Settings saved.</p></div>';
}
?>

<div class="wrap airtable-sync-wrap">

    <h1><?php echo __('Airtable Sync — Settings', 'airtable-sync'); ?></h1>

    <form method="post">
        <?php wp_nonce_field('airtable_sync_settings_save', 'airtable_sync_nonce'); ?>

        <table class="form-table">

            <tr>
                <th scope="row">
                    <label for="airtable_api_key"><?php echo __('API Key', 'airtable-sync'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="airtable_api_key"
                        name="airtable_api_key"
                        value="<?php echo esc_attr($option_api_key); ?>"
                        class="regular-text"
                        autocomplete="off">
                    <p class="description">
                        <?php echo __('Found in your Airtable account settings.', 'airtable-sync'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="airtable_base_id"><?php echo __('Base ID', 'airtable-sync'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="airtable_base_id"
                        name="airtable_base_id"
                        value="<?php echo esc_attr($option_base_id); ?>"
                        class="regular-text"
                        autocomplete="off">
                    <p class="description">
                        <?php echo __('Found in Airtable “API Docs → Authentication → Base ID”.', 'airtable-sync'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="airtable_table"><?php echo __('Table Name', 'airtable-sync'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="airtable_table"
                        name="airtable_table"
                        value="<?php echo esc_attr($option_table); ?>"
                        class="regular-text"
                        autocomplete="off">
                    <p class="description">
                        <?php echo __('Exact table name in Airtable.', 'airtable-sync'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="airtable_sync_post_type"><?php echo __('Target Post Type', 'airtable-sync'); ?></label>
                </th>
                <td>
                    <select id="airtable_sync_post_type" name="airtable_sync_post_type">
                        <option value=""><?php echo __('— Select —', 'airtable-sync'); ?></option>

                        <?php foreach ($post_types as $pt): ?>
                            <option value="<?php echo esc_attr($pt->name); ?>"
                                <?php selected($option_posttype, $pt->name); ?>>
                                <?php echo esc_html($pt->label . " ({$pt->name})"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <p class="description">
                        <?php echo __('Records will import into this post type.', 'airtable-sync'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th>
                    <?php echo __('Auto-detect fields', 'airtable-sync'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox"
                            name="airtable_autodetect_fields"
                            value="1"
                            <?php checked($option_autodetect, 1); ?>>
                        <?php echo __('Enable automatic field detection', 'airtable-sync'); ?>
                    </label>
                    <p class="description">
                        <?php echo __('If enabled, plugin detects taxonomies & meta-fields of selected post type.', 'airtable-sync'); ?>
                    </p>
                </td>
            </tr>

        </table>

        <?php submit_button(__('Save Settings', 'airtable-sync')); ?>
    </form>

</div>