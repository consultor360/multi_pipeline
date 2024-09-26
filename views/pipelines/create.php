<?php 
// Caminho: /public_html/modules/multi_pipeline/views/pipelines/create.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo _l('create_pipeline'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('multi_pipeline/create_pipeline')); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('name', 'pipeline_name'); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_textarea('description', 'pipeline_description'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary pull-right">
                                    <?php echo _l('submit'); ?>
                                </button>
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
    });
});
</script>