<?php
if (!defined('ABSPATH')) exit;

$option_posttype = get_option('airtable_sync_post_type', '');
$existing_mapping = get_option('airtable_sync_mapping', []);
?>

<div class="wrap airtable-sync-wrap">

    <h1><?php echo __('Airtable Sync — Field Mapping', 'airtable-sync'); ?></h1>

    <?php if (!$option_posttype): ?>
        <div class="notice notice-warning">
            <p><?php echo __('Please select a Post Type in Settings before creating a mapping.', 'airtable-sync'); ?></p>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <div id="airtable-mapping-root"
         data-post-type="<?php echo esc_attr($option_posttype); ?>"
         data-mapping='<?php echo json_encode($existing_mapping); ?>'>

        <p class="description">
            <?php echo __('Click the button below to load Airtable columns and available WordPress fields.', 'airtable-sync'); ?>
        </p>

        <button class="button button-primary" id="at-load-columns">
            <?php echo __('Load Columns', 'airtable-sync'); ?>
        </button>

        <div id="at-mapping-loading" style="display:none; margin-top:20px;">
            <span class="spinner is-active"></span>
            <?php echo __('Loading…', 'airtable-sync'); ?>
        </div>

        <form id="at-mapping-form" style="display:none; margin-top:30px;">

            <h2><?php echo __('Column Mapping', 'airtable-sync'); ?></h2>

            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo __('Airtable Column', 'airtable-sync'); ?></th>
                        <th><?php echo __('Map To WordPress Field', 'airtable-sync'); ?></th>
                    </tr>
                </thead>

                <tbody id="at-mapping-table-body">
                    <!-- JS inserts mapping rows here -->
                </tbody>
            </table>

            <p style="margin-top:20px;">
                <button type="submit" class="button button-primary">
                    <?php echo __('Save Mapping', 'airtable-sync'); ?>
                </button>
            </p>

        </form>

        <div id="at-mapping-message" style="margin-top:20px;"></div>

    </div>

</div>