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
                        <h4 class="no-margin"><?php echo $pipeline->name; ?></h4>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo _l('pipeline_details'); ?></h5>
                                <p><strong><?php echo _l('pipeline_name'); ?>:</strong> <?php echo $pipeline->name; ?></p>
                                <p><strong><?php echo _l('pipeline_description'); ?>:</strong> <?php echo $pipeline->description; ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5><?php echo _l('pipeline_stages'); ?></h5>
                                <?php foreach($pipeline->stages as $stage): ?>
                                    <p><?php echo $stage->name; ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>