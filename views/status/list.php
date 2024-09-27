// Caminho: /public_html/modules/multi_pipeline/views/status/list.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('lead_statuses'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('lead_status_name'); ?></th>
                                        <th><?php echo _l('pipeline_name'); ?></th>
                                        <th><?php echo _l('lead_status_color'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($statuses as $status): ?>
                                    <tr>
                                        <td><?php echo $status['name']; ?></td>
                                        <td><?php echo $status['pipeline_name']; ?></td>
                                        <td><span class="label" style="background-color: <?php echo $status['color']; ?>"><?php echo $status['color']; ?></span></td>
                                        <td>
                                            <a href="<?php echo admin_url('multi_pipeline/status/edit/' . $status['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                            <a href="<?php echo admin_url('multi_pipeline/status/delete/' . $status['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
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
