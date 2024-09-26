<?php 
// Caminho: /public_html/modules/multi_pipeline/views/leads/add.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h4 class="no-margin"><?php echo _l('add_new_lead'); ?></h4>
        <hr class="hr-panel-heading" />
        <?php echo form_open(admin_url('multi_pipeline/add_lead')); ?>
        <div class="form-group">
            <label for="name"><?php echo _l('lead_name'); ?></label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email"><?php echo _l('lead_email'); ?></label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="phone"><?php echo _l('lead_phone'); ?></label>
            <input type="tel" id="phone" name="phone" class="form-control">
        </div>
        <div class="form-group">
            <label for="company"><?php echo _l('lead_company'); ?></label>
            <input type="text" id="company" name="company" class="form-control">
        </div>
        <div class="form-group">
            <label for="pipeline_id"><?php echo _l('lead_pipeline'); ?></label>
            <select id="pipeline_id" name="pipeline_id" class="form-control" required>
                <?php foreach($pipelines as $pipeline) { ?>
                    <option value="<?php echo $pipeline['id']; ?>"><?php echo $pipeline['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
        <?php echo form_close(); ?>
    </div>
</div>