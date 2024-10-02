<?php 
// Caminho: /public_html/modules/multi_pipeline/views/status/edit.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('edit_lead_status'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('multi_pipeline/status/edit/' . $status['id'])); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('name', 'leadstatus_name', $status['name']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_color_picker('color', 'leadstatus_color', $status['color']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_select('pipeline_id', $pipelines, array('id', 'name'), 'pipeline', $status['pipeline_id']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input('order', 'order', $status['order'], 'number'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    appValidateForm($('form'), {
        name: 'required',
        color: 'required',
        pipeline_id: 'required',
        order: 'required'
    });
});
</script>