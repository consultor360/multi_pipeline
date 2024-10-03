<?php 
// Caminho: /public_html/modules/multi_pipeline/views/forms/form_associations.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('form_associations'); ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <!-- Formulário de Associação -->
                        <form action="<?php echo admin_url('multi_pipeline/save_form_association'); ?>" method="post">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form_id"><?php echo _l('select_form'); ?></label>
                                        <select name="form_id" id="form_id" class="form-control selectpicker" data-live-search="true" required>
                                            <option value=""><?php echo _l('select_form'); ?></option>
                                            <?php foreach($forms as $form): ?>
                                                <option value="<?php echo $form['id']; ?>" <?php echo (isset($association['form_id']) && $association['form_id'] == $form['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($form['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pipeline_stage"><?php echo _l('select_pipeline_stage'); ?></label>
                                        <select name="pipeline_stage" id="pipeline_stage" class="form-control selectpicker" data-live-search="true" required>
                                            <option value=""><?php echo _l('select_pipeline_stage'); ?></option>
                                            <?php foreach($pipelines as $pipeline_id => $pipeline): ?>
                                                <optgroup label="<?php echo htmlspecialchars($pipeline['pipeline_name']); ?>" style="font-weight: bold;">
                                                    <?php foreach($pipeline['stages'] as $stage): ?>
                                                        <option value="<?php echo $pipeline_id . ',' . $stage['id']; ?>" <?php echo (isset($association['pipeline_id']) && $association['pipeline_id'] == $pipeline_id && isset($association['stage_id']) && $association['stage_id'] == $stage['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($stage['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-info btn-block"><?php echo _l('save'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <?php if(isset($association)): ?>
                                <input type="hidden" name="association_id" value="<?php echo htmlspecialchars($association['id']); ?>">
                            <?php endif; ?>
                        </form>

                        <hr class="hr-panel-heading" />

                        <!-- Tabela de Associações -->
                        <table class="table dt-table table-form-associations">
                            <thead>
                                <tr>
                                    <th><?php echo _l('form_name'); ?></th>
                                    <th><?php echo _l('pipeline_name'); ?></th>
                                    <th><?php echo _l('stage_name'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($associations as $assoc): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assoc['form_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assoc['pipeline_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assoc['stage_name']); ?></td>
                                        <td>
                                            <a href="<?php echo admin_url('multi_pipeline/edit_form_association/'.$assoc['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>
                                            <a href="<?php echo admin_url('multi_pipeline/delete_form_association/'.$assoc['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
$(function() {
    initDataTable('.table-form-associations');
});
</script>