// Caminho: /public_html/modules/multi_pipeline/views/status/list.php

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
                                <h4 class="no-margin"><?php echo _l('lead_statuses'); ?></h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?php echo admin_url('multi_pipeline/status/create'); ?>" class="btn btn-info pull-right">
                                    <i class="fa fa-plus"></i> <?php echo _l('create_lead_status'); ?>
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('id'); ?></th>
                                        <th><?php echo _l('lead_status_name'); ?></th>
                                        <th><?php echo _l('pipeline_name'); ?></th>
                                        <th><?php echo _l('lead_count'); ?></th>
                                        <th><?php echo _l('lead_status_color'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($statuses as $status): ?>
                                    <tr>
                                         <td><?php echo $status['id']; ?></td>
                                        <td><?php echo $status['name']; ?></td>
                                        <td><?php echo $status['pipeline_name']; ?></td>
                                        <td><?php echo $status['lead_count']; ?></td>
                                        <td><span class="label" style="background-color: <?php echo $status['color']; ?>"><?php echo $status['color']; ?></span></td>
                                        <td>
                                            <a href="<?php echo admin_url('multi_pipeline/status/edit/' . $status['id']); ?>" class="btn btn-default btn-icon" title="<?php echo _l('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                            <a href="<?php echo admin_url('multi_pipeline/status/delete/' . $status['id']); ?>" class="btn btn-danger btn-icon _delete" data-toggle="tooltip" data-placement="top" title="<?php echo _l('delete'); ?>"><i class="fa fa-trash"></i></a>
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
