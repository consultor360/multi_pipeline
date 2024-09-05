<?php 
// Caminho: /public_html/modules/multi_pipeline/views/leads/summary.php

defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <h4 class="no-margin"><?php echo _l('lead_summary'); ?></h4>
        <hr class="hr-panel-heading" />
        
        <?php foreach ($pipelines as $pipeline) : ?>
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5><?php echo $pipeline['name']; ?></h5>
                        <hr class="hr-panel-heading" />
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo _l('stage'); ?></th>
                                    <th><?php echo _l('lead_count'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pipeline['stages'] as $stage) : ?>
                                    <tr>
                                        <td><?php echo $stage['name']; ?></td>
                                        <td><?php echo $stage['lead_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>