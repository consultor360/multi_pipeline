// Caminho: /public_html/modules/multi_pipeline/views/leads/summary.php

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('lead_summary'); ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <div class="row">
                            <div class="col-md-12">
                                <a href="#" class="btn btn-info pull-left display-block" onclick="new_lead(); return false;">
                                    <?php echo _l('new_lead'); ?>
                                </a>
                                <?php if(has_permission('leads','','create')){ ?>
                                <a href="<?php echo admin_url('leads/import'); ?>" class="btn btn-info pull-left display-block mleft5">
                                    <?php echo _l('import_leads'); ?>
                                </a>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="clearfix mtop20"></div>
                        
                        <table class="table dt-table table-leads" data-order-col="2" data-order-type="desc">
                            <thead>
                                <tr>
                                    <th><?php echo _l('options'); ?></th>
                                    <th><?php echo _l('lead_name'); ?></th>
                                    <th><?php echo _l('lead_company'); ?></th>
                                    <th><?php echo _l('lead_email'); ?></th>
                                    <th><?php echo _l('lead_phonenumber'); ?></th>
                                    <th><?php echo _l('pipeline_stage'); ?></th>
                                    <th><?php echo _l('lead_value'); ?></th>
                                    <th><?php echo _l('leads_dt_assigned'); ?></th>
                                    <th><?php echo _l('leads_dt_last_contact'); ?></th>
                                    <th><?php echo _l('leads_dt_datecreated'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($leads as $lead){ ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo admin_url('leads/index/'.$lead['id']); ?>" class="btn btn-default btn-icon">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <?php if(has_permission('leads','','delete')){ ?>
                                        <a href="<?php echo admin_url('leads/delete/'.$lead['id']); ?>" class="btn btn-danger btn-icon delete-lead">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td><a href="<?php echo admin_url('leads/index/'.$lead['id']); ?>"><?php echo $lead['name']; ?></a></td>
                                    <td><?php echo $lead['company']; ?></td>
                                    <td><?php echo $lead['email']; ?></td>
                                    <td><?php echo $lead['phonenumber']; ?></td>
                                    <td>
                                        <select onchange="change_lead_pipeline_stage(this.value, <?php echo $lead['id']; ?>)">
                                            <?php foreach($pipelines as $pipeline){ ?>
                                                <optgroup label="<?php echo $pipeline['name']; ?>">
                                                    <?php foreach($pipeline['stages'] as $stage){ ?>
                                                        <option value="<?php echo $pipeline['id'] . ',' . $stage['id']; ?>" <?php if($lead['pipeline_id'] == $pipeline['id'] && $lead['stage_id'] == $stage['id']){echo 'selected';} ?>><?php echo $pipeline['name'] . ' - ' . $stage['name']; ?></option>
                                                    <?php } ?>
                                                </optgroup>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td><?php echo app_format_money($lead['lead_value'], $base_currency); ?></td>
                                    <td><?php echo get_staff_full_name($lead['assigned']); ?></td>
                                    <td><?php echo ($lead['lastcontact'] ? _dt($lead['lastcontact']) : '-') ?></td>
                                    <td><?php echo _dt($lead['dateadded']); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
function change_lead_pipeline_stage(value, lead_id) {
    var [pipeline_id, stage_id] = value.split(',');
    $.post(admin_url + 'multi_pipeline/change_lead_pipeline_stage', {
        pipeline_id: pipeline_id,
        stage_id: stage_id,
        lead_id: lead_id
    }).done(function(response) {
        // Atualizar a UI conforme necessário
    });
}

$(function() {
    initDataTable('.table-leads', admin_url + 'multi_pipeline/leads_table');
});
</script>
