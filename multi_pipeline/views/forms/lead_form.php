<?php 
// Caminho: /public_html/modules/multi_pipeline/views/forms/lead_form.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="lead_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo _l('add_new_lead'); ?></h4>
            </div>
            <?php echo form_open('admin/multi_pipeline/leads/add', array('id'=>'lead-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','lead_name'); ?>
                        <?php echo render_input('email','lead_email'); ?>
                        <?php echo render_input('phonenumber','lead_phone'); ?>
                        <?php echo render_select('status',$statuses,array('id','name'),'lead_status'); ?>
                        <?php echo render_select('source',$sources,array('id','name'),'lead_source'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>