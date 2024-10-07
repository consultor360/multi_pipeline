<?php 
// Caminho: /public_html/modules/multi_pipeline/views/pipelines/view.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin"><?php echo _l('pipelines_list'); ?></h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?php echo admin_url('multi_pipeline/create_pipeline'); ?>" class="btn btn-info pull-right">
                                    <i class="fa fa-plus"></i> <?php echo _l('create_pipeline'); ?>
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('id'); ?></th>
                                        <th><?php echo _l('pipeline_name'); ?></th>
                                        <th><?php echo _l('description'); ?></th>
                                        <th><?php echo _l('leads_count'); ?></th>
                                        <th><?php echo _l('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pipelines as $pipeline): ?>
                                    <tr>
                                        <td><?php echo $pipeline->id; ?></td>
                                        <td><?php echo $pipeline->name; ?></td>
                                        <td><?php echo $pipeline->description; ?></td>
                                        <td>
                                            <?php
                                            $leads_count = $this->db->where('pipeline_id', $pipeline->id)->count_all_results('tblleads');
                                            echo $leads_count;
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('multi_pipeline/pipelines/edit/' . $pipeline->id); ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i></a>
                                            <a href="<?php echo admin_url('multi_pipeline/pipelines/delete/' . $pipeline->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo _l('are_you_sure'); ?>');"><i class="fa fa-trash"></i></a>
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
</div>
<?php init_tail(); ?>
