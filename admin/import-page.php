<?php
if (!defined('ABSPATH')) exit;

$post_type = get_option('airtable_sync_post_type', '');
$mapping   = get_option('airtable_sync_mapping', []);
?>

<div class="wrap airtable-sync-wrap">

    <h1><?php echo __('Airtable Sync — Import / Sync', 'airtable-sync'); ?></h1>

    <?php if (!$post_type): ?>
        <div class="notice notice-warning">
            <p><?php echo __('Please select a target Post Type in Settings before running Import.', 'airtable-sync'); ?></p>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <?php if (empty($mapping)): ?>
        <div class="notice notice-warning">
            <p><?php echo __('Please configure the field mapping before importing.', 'airtable-sync'); ?></p>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <p class="description">
        <?php echo __('When you click the button below, the plugin will fetch Airtable records, map them to WordPress fields and import/update posts.', 'airtable-sync'); ?>
    </p>

    <button id="at-sync-run" class="button button-primary">
        <?php echo __('Sync Now', 'airtable-sync'); ?>
    </button>

    <div id="at-sync-loading" style="display:none; margin-top:20px;">
        <span class="spinner is-active"></span>
        <?php echo __('Sync in progress…', 'airtable-sync'); ?>
    </div>

    <div id="at-sync-result" style="display:none; margin-top:30px;" class="notice notice-info">
        <!-- JS fills results -->
    </div>

</div>