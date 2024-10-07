<?php
defined('BASEPATH') or exit('No direct script access allowed');
$lead_already_client_tooltip = '';
$lead_is_client = isset($lead['is_lead_client']) ? $lead['is_lead_client'] !== '0' : false;
if ($lead_is_client) {
    $lead_already_client_tooltip = ' data-toggle="tooltip" title="' . _l('lead_have_client_profile') . '"';
}
?>
<li data-lead-id="<?php echo e($lead['id']); ?>" 
    data-pipeline-id="<?php echo isset($pipeline['id']) ? html_escape($pipeline['id']) : ''; ?>" 
    <?php echo $lead_already_client_tooltip; ?> 
    class="lead-kan-ban<?php 
        if (isset($lead['assigned']) && $lead['assigned'] == get_staff_user_id()) {
            echo ' current-user-lead';
        }
        if ($lead_is_client && get_option('lead_lock_after_convert_to_customer') == 1 && !is_admin()) {
            echo ' not-sortable';
        }
    ?>">
    <div class="panel-body lead-body">
        <div class="lead-content">
            <div class="lead-header">
                <?php if (isset($lead['assigned']) && $lead['assigned'] != 0) { ?>
                <a href="<?php echo admin_url('profile/' . $lead['assigned']); ?>" data-placement="right"
                    data-toggle="tooltip" title="<?php echo e(get_staff_full_name($lead['assigned'])); ?>"
                    class="assigned-user-avatar">
                    <?php echo staff_profile_image($lead['assigned'], ['staff-profile-image-xs']); ?>
                </a>
                <?php } ?>
                <a href="<?php echo admin_url('leads/index/' . $lead['id']); ?>" onclick="init_lead(<?php echo $lead['id']; ?>); return false;" class="lead-name">
                    <span>#<?php echo e($lead['id']) . ' - ' . e($lead['name']); ?></span>
                </a>
            </div>
            <div class="lead-info">
                <p><strong><?php echo _l('Email'); ?>:</strong> <?php echo e($lead['email'] ?? '-'); ?></p>
                <p><strong><?php echo _l('lead_add_edit_phonenumber'); ?>:</strong> <?php echo e($lead['phonenumber'] ?? '-'); ?></p>
                <p><strong><?php echo _l('lead_city'); ?>:</strong> <?php echo e($lead['city'] ?? '-'); ?></p>
                <p><strong><?php echo _l('leads_canban_source'); ?>:</strong> <?php echo e($lead['source_name'] ?? '-'); ?></p>
                <?php $lead_value = isset($lead['lead_value']) && $lead['lead_value'] != 0 ? app_format_money($lead['lead_value'], $base_currency->symbol) : '--'; ?>
                <p><strong><?php echo _l('leads_canban_lead_value'); ?>:</strong> <?php echo e($lead_value); ?></p>
                <p><strong><?php echo _l('lead_title'); ?>:</strong> <?php echo e($lead['title'] ?? '-'); ?></p>
            </div>
            <div class="lead-footer">
            <?php if (isset($lead['lastcontact']) && is_date($lead['lastcontact']) && $lead['lastcontact'] != '0000-00-00 00:00:00') { ?>
                <small class="text-muted">
                    <?php echo _l('leads_dt_last_contact'); ?>: 
                    <span data-toggle="tooltip" data-title="<?php echo e(_dt($lead['lastcontact'])); ?>">
                        <?php echo e(time_ago($lead['lastcontact'])); ?>
                    </span>
                </small><br>
                <?php } ?>
                <small class="text-muted">
                    <?php echo _l('lead_created'); ?>: 
                    <span data-toggle="tooltip" data-title="<?php echo e(_dt($lead['dateadded'])); ?>">
                        <?php echo e(time_ago($lead['dateadded'])); ?>
                    </span>
                </small>
            </div>
            <?php if (isset($lead['tags']) && $lead['tags']) { ?>
            <div class="kanban-tags">
                <?php echo render_tags($lead['tags']); ?>
            </div>
            <?php } ?>
        </div>
    </div>
</li>