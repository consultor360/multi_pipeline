<?php 
// Caminho: /public_html/modules/multi_pipeline/views/leads/details.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h4 class="no-margin"><?php echo _l('lead_details'); ?></h4>
        <hr class="hr-panel-heading" />
        
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><?php echo _l('lead_name'); ?>:</strong> <?php echo $lead->name; ?></p>
                        <p><strong><?php echo _l('lead_email'); ?>:</strong> <?php echo $lead->email; ?></p>
                        <p><strong><?php echo _l('lead_phone'); ?>:</strong> <?php echo $lead->phone; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><?php echo _l('lead_company'); ?>:</strong> <?php echo $lead->company; ?></p>
                        <p><strong><?php echo _l('lead_pipeline'); ?>:</strong> <?php echo get_pipeline_name($lead->pipeline_id); ?></p>
                        <p><strong><?php echo _l('lead_stage'); ?>:</strong> <?php echo $lead->stage_name; ?></p>
                    </div>
                </div>
                <hr />
                <h5><?php echo _l('lead_activities'); ?></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo _l('date'); ?></th>
                            <th><?php echo _l('activity_type'); ?></th>
                            <th><?php echo _l('description'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lead->activities as $activity) : ?>
                            <tr>
                                <td><?php echo _d($activity->date); ?></td>
                                <td><?php echo $activity->type; ?></td>
                                <td><?php echo $activity->description; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>