<?php
defined('BASEPATH') or exit('No direct script access allowed');
$lead_already_client_tooltip = '';
$lead_is_client = isset($lead['is_lead_client']) ? $lead['is_lead_client'] !== '0' : false;
if ($lead_is_client) {
    $lead_already_client_tooltip = 'data-toggle="tooltip" title="' . _l('lead_have_client_profile') . '"';
}
?>
<li data-lead-id="<?php echo isset($lead['id']) ? html_escape($lead['id']) : ''; ?>" 
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
        <div class="tw-flex lead-name">
            <?php if (isset($lead['assigned']) && $lead['assigned'] != 0) { ?>
            <a href="<?php echo admin_url('profile/' . $lead['assigned']); ?>" data-placement="right" data-toggle="tooltip" title="<?php echo get_staff_full_name($lead['assigned']); ?>">
                <?php echo staff_profile_image($lead['assigned'], ['staff-profile-image-small']); ?>
            </a>
            <?php } ?>
            <a href="<?php echo admin_url('leads/index/' . $lead['id']); ?>" onclick="init_lead(<?php echo $lead['id']; ?>); return false;" class="pull-left">
                <span class="inline-block tw-truncate tw-w-40 tw-align-middle" data-toggle="tooltip" title="<?php echo html_escape($lead['name']); ?>">
                    <?php echo html_escape($lead['name']); ?>
                </span>
            </a>
        </div>
    </div>
</li>