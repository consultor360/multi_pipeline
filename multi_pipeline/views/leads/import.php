<?php 
// Caminho: /public_html/modules/multi_pipeline/views/leads/import.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h4 class="no-margin"><?php echo _l('import_leads'); ?></h4>
        <hr class="hr-panel-heading" />
        <?php echo form_open_multipart(admin_url('multi_pipeline/import_leads')); ?>
        <div class="form-group">
            <label for="file_import"><?php echo _l('choose_file'); ?></label>
            <input type="file" id="file_import" name="file_import" class="form-control" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
        </div>
        <div class="form-group">
            <label for="pipeline_id"><?php echo _l('select_pipeline'); ?></label>
            <select id="pipeline_id" name="pipeline_id" class="form-control" required>
                <?php foreach($pipelines as $pipeline) { ?>
                    <option value="<?php echo $pipeline['id']; ?>"><?php echo $pipeline['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo _l('import'); ?></button>
        <?php echo form_close(); ?>
    </div>
</div>