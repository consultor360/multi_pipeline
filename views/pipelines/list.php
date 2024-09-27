<?php
// Caminho: /public_html/modules/multi_pipeline/views/pipelines/list.php

defined('BASEPATH') or exit('No direct script access allowed');
$is_admin = is_admin();
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4><?php echo _l('multi_pipeline_leads'); ?></h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-info pull-right" onclick="openAddLeadModal()">
                                    <i class="fa fa-plus"></i> Adicionar Lead
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kan-ban-wrapper" id="kan-ban-wrapper">
                    <?php foreach ($pipelines as $pipeline) { ?>
                        <div class="pipeline-section">
                            <h3><?php echo e($pipeline['name']); ?></h3>
                            <div class="kan-ban-row">
                                <div class="kan-ban-outer-container">
                                    <div class="kan-ban-inner-container">
                                        <?php if (isset($leads[$pipeline['id']])) {
                                            foreach ($leads[$pipeline['id']] as $stage) { ?>
                                                <div class="kan-ban-col" data-col-stage-id="<?php echo e($stage['stage_id']); ?>" data-pipeline-id="<?php echo e($pipeline['id']); ?>" data-total-pages="<?php echo e(count($stage['leads'])); ?>" data-total="<?php echo e(count($stage['leads'])); ?>">
                                                    <div class="panel panel_s">
                                                        <?php
                                                        $stage_color = '';
                                                        if (!empty($stage['stage_color'])) {
                                                            $stage_color = 'style="background:' . $stage['stage_color'] . ';border:1px solid ' . $stage['stage_color'] . '"';
                                                        }
                                                        ?>
                                                        <div class="panel-heading tw-bg-neutral-700 tw-text-white" <?php echo $stage_color; ?> data-stage-id="<?php echo e($stage['stage_id']); ?>">
                                                            <?php echo e($stage['stage_name']); ?>
                                                        </div>
                                                        <div class="kan-ban-content-wrapper">
                                                            <div class="kan-ban-content">
                                                                <ul class="stage leads-stage sortable" data-lead-stage-id="<?php echo e($stage['stage_id']); ?>">
                                                                    <?php foreach ($stage['leads'] as $lead) { ?>
                                                                        <?php $this->load->view('pipelines/kanban_card', ['lead' => $lead, 'stage' => $stage, 'pipeline' => $pipeline]); ?>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else {
                                            echo "<p>Nenhum lead encontrado para este pipeline.</p>";
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    init_kanban('leads/leads_kanban', $('.kan-ban-wrapper'), 'leads/leads_kanban_load_more');

    $(".sortable").sortable({
        connectWith: ".sortable",
        helper: 'clone',
        placeholder: 'kan-ban-item-placeholder',
        start: function(event, ui) {
            $('body').css('cursor', 'move');
        },
        stop: function(event, ui) {
            $('body').css('cursor', 'auto');
            var item = $(ui.item);
            var stageId = item.closest('.kan-ban-col').data('col-stage-id');
            var leadId = item.data('lead-id');
            var pipelineId = item.closest('.kan-ban-col').data('pipeline-id');

            $.post(admin_url + 'leads/update_lead_status', {
                lead_id: leadId,
                status_id: stageId,
                pipeline_id: pipelineId
            }).done(function(response) {
                // Atualizar a UI conforme necessário
            });
        }
    }).disableSelection();

    $(document).on('click', '.new-lead-from-stage', function() {
        var stageId = $(this).data('stage-id');
        var pipelineId = $(this).data('pipeline-id');
        openAddLeadModal(pipelineId, stageId);
    });

    $(document).on('click', '.kan-ban-expand-top', function(e) {
        e.preventDefault();
        var leadId = $(this).closest('li').data('lead-id');
        $('#kan-ban-expand-' + leadId).slideToggle();
    });

    function makeKanbanResponsive() {
        $('.kan-ban-outer-container').each(function() {
            var $container = $(this);
            // Implementar lógica adicional se necessário
        });
    }
});
</script>

<style>
.kan-ban-wrapper {
    overflow: hidden;
}

.pipeline-section {
    margin-bottom: 20px;
}

.kan-ban-row {
    white-space: nowrap;
}

.kan-ban-outer-container {
    width: 100%;
    overflow-x: auto;
}

.kan-ban-inner-container {
    display: inline-block;
    white-space: nowrap;
}

.kan-ban-col {
    display: inline-block;
    vertical-align: top;
    width: 300px;
    margin-right: 10px;
}

.panel_s {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
}

.kan-ban-content {
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

@media (max-width: 768px) {
    .kan-ban-col {
        width: 250px;
    }
}
</style>