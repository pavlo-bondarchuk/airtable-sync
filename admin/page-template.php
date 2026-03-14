<?php
if (!defined('ABSPATH')) exit;

$api_key   = get_option('airtable_api_key', '');
$base_id   = get_option('airtable_base_id', '');
$table     = get_option('airtable_table', '');
$post_type = get_option('airtable_sync_post_type', '');
$mapping   = get_option('airtable_sync_mapping', []);
?>

<div class="wrap airtable-sync-wrap">

    <h1><?php echo __('Airtable Sync – Dashboard', 'airtable-sync'); ?></h1>

    <p class="description">
        <?php echo __('This plugin allows you to import and synchronize Airtable records into WordPress custom post types.', 'airtable-sync'); ?>
    </p>

    <hr>

    <h2><?php echo __('Configuration Status', 'airtable-sync'); ?></h2>

    <table class="widefat striped" style="max-width:700px;">

        <tr>
            <th><?php echo __('API Key', 'airtable-sync'); ?></th>
            <td>
                <?php if ($api_key): ?>
                    <span class="at-ok"><?php echo __('Configured', 'airtable-sync'); ?></span>
                <?php else: ?>
                    <span class="at-missing"><?php echo __('Not set', 'airtable-sync'); ?></span>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <th><?php echo __('Base ID', 'airtable-sync'); ?></th>
            <td>
                <?php if ($base_id): ?>
                    <span class="at-ok"><?php echo __('Configured', 'airtable-sync'); ?></span>
                <?php else: ?>
                    <span class="at-missing"><?php echo __('Not set', 'airtable-sync'); ?></span>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <th><?php echo __('Table Name', 'airtable-sync'); ?></th>
            <td>
                <?php if ($table): ?>
                    <span class="at-ok"><?php echo __('Configured', 'airtable-sync'); ?></span>
                <?php else: ?>
                    <span class="at-missing"><?php echo __('Not set', 'airtable-sync'); ?></span>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <th><?php echo __('Target Post Type', 'airtable-sync'); ?></th>
            <td>
                <?php if ($post_type): ?>
                    <span class="at-ok"><?php echo esc_html($post_type); ?></span>
                <?php else: ?>
                    <span class="at-missing"><?php echo __('Not selected', 'airtable-sync'); ?></span>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <th><?php echo __('Field Mapping', 'airtable-sync'); ?></th>
            <td>
                <?php if (!empty($mapping)): ?>
                    <span class="at-ok"><?php echo __('Mapping configured', 'airtable-sync'); ?></span>
                <?php else: ?>
                    <span class="at-missing"><?php echo __('Not configured', 'airtable-sync'); ?></span>
                <?php endif; ?>
            </td>
        </tr>

    </table>

    <hr style="margin:30px 0;">

    <h2><?php echo __('Quick Navigation', 'airtable-sync'); ?></h2>

    <ul class="at-quick-links">
        <li><a href="admin.php?page=airtable-sync-settings" class="button button-secondary">
                <?php echo __('Settings', 'airtable-sync'); ?></a></li>

        <li><a href="admin.php?page=airtable-sync-mapping" class="button button-secondary">
                <?php echo __('Field Mapping', 'airtable-sync'); ?></a></li>

        <li><a href="admin.php?page=airtable-sync-import" class="button button-primary">
                <?php echo __('Import / Sync', 'airtable-sync'); ?></a></li>
    </ul>

</div>