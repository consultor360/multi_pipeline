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
                            <div class="col-md-6">
                                <h4><?php echo _l('multi_pipeline_leads'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-filter" aria-hidden="true"></i> <?php echo _l('filter_by_pipeline'); ?>
                                    </button>
                                    <ul class="dropdown-menu width300">
                                        <li>
                                            <a href="#" data-cview="all" onclick="filter_pipeline('all'); return false;">
                                                <?php echo _l('all_pipelines'); ?>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <?php foreach($pipelines as $pipeline){ ?>
                                            <li>
                                                <a href="#" data-cview="pipeline_<?php echo $pipeline['id']; ?>" onclick="filter_pipeline(<?php echo $pipeline['id']; ?>); return false;">
                                                    <?php echo $pipeline['name']; ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php if(has_permission('leads','','create')){ ?>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addLeadModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('new_lead'); ?>
                                </button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                    </div>
                </div>
                <?php foreach ($pipelines as $pipeline) { ?>
                    <div class="pipeline-container" id="pipeline_<?php echo $pipeline['id']; ?>">
                        <div class="pipeline-header">
                            <h3><?php echo e($pipeline['name']); ?></h3>
                            <div class="pipeline-actions">
                                <input type="text" class="form-control search-leads" placeholder="Procurar Leads">
                                <div class="sorting-options">
                                    Classificar por: 
                                    <a href="#" onclick="sort_leads('date_created'); return false;">Data de Criação</a> | 
                                    <a href="#" onclick="sort_leads('kanban'); return false;">Ordenar em modo Kan Ban</a> | 
                                    <a href="#" onclick="sort_leads('last_contact'); return false;">Último contato</a>
                                </div>
                            </div>
                        </div>
                        <div class="kan-ban-wrapper">
                            <div class="kan-ban-row">
                                <?php
                                $pipeline_stages = array_filter($stages, function($stage) use ($pipeline) {
                                    return $stage['pipeline_id'] == $pipeline['id'];
                                });

                                foreach ($pipeline_stages as $stage) {
                                    $stage_leads = isset($leads[$pipeline['id']][$stage['id']]['leads']) ? $leads[$pipeline['id']][$stage['id']]['leads'] : [];
                                    $total_leads = count($stage_leads);
                                ?>
                                    <div class="kan-ban-col" data-col-stage-id="<?php echo e($stage['id']); ?>" data-pipeline-id="<?php echo e($pipeline['id']); ?>" data-total-pages="<?php echo e($total_leads); ?>" data-total="<?php echo e($total_leads); ?>">
                                        <div class="panel panel_s">
                                            <div class="panel-heading" style="background:<?php echo e($stage['color']); ?>;">
                                                <?php echo e($stage['name']); ?> - $0.00 - <?php echo e($total_leads); ?> Leads
                                                <a href="#" class="pull-right"><i class="fa fa-chevron-down"></i></a>
                                            </div>
                                            <div class="kan-ban-content-wrapper">
                                                <div class="kan-ban-content">
                                                    <ul class="stage leads-stage sortable" data-lead-stage-id="<?php echo e($stage['id']); ?>">
                                                        <?php
                                                        if (!empty($stage_leads)) {
                                                            foreach ($stage_leads as $lead) {
                                                                $this->load->view('pipelines/kanban_card', ['lead' => $lead, 'stage' => $stage, 'pipeline' => $pipeline]);
                                                            }
                                                        } else {
                                                            echo "<li class='text-center'>Nenhum Lead encontrado</li>";
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
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

            $.ajax({
                url: admin_url + 'multi_pipeline/change_lead_pipeline_stage',
                type: 'POST',
                data: {
                    lead_id: leadId,
                    pipeline_id: pipelineId,
                    stage_id: stageId
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert_float('success', result.message);
                    } else {
                        alert_float('danger', result.message);
                        // Reverter a posição do item se a atualização falhar
                        $(ui.sender).sortable('cancel');
                    }
                },
                error: function() {
                    alert_float('danger', 'Erro ao atualizar o lead');
                    // Reverter a posição do item se houver um erro
                    $(ui.sender).sortable('cancel');
                }
            });
        }
    }).disableSelection();

    $('.panel-heading').on('click', function() {
        $(this).next('.kan-ban-content-wrapper').slideToggle('fast');
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });

    $('.search-leads').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        var $pipeline = $(this).closest('.pipeline-container');
        $pipeline.find('.kan-ban-content li').each(function() {
            var leadText = $(this).text().toLowerCase();
            $(this).toggle(leadText.indexOf(searchTerm) > -1);
        });
    });
});

function filter_pipeline(pipelineId) {
    if (pipelineId === 'all') {
        $('.pipeline-container').show();
    } else {
        $('.pipeline-container').hide();
        $('#pipeline_' + pipelineId).show();
    }
}

function sort_leads(sortBy) {
    // Implementar a lógica de ordenação aqui
    console.log('Sorting by: ' + sortBy);
}
</script>

<style>
.pipeline-container {
    margin-bottom: 30px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.pipeline-header {
    padding: 15px;
    background-color: #f8f8f8;
    border-bottom: 1px solid #ddd;
}

.pipeline-header h3 {
    margin: 0;
    display: inline-block;
}

.pipeline-actions {
    float: right;
}

.search-leads {
    display: inline-block;
    width: 200px;
    margin-right: 10px;
}

.sorting-options {
    display: inline-block;
}

.kan-ban-wrapper {
    overflow-x: auto;
    white-space: nowrap;
    padding: 15px;
}

.kan-ban-row {
    display: flex;
}

.kan-ban-col {
    flex: 0 0 300px;
    margin-right: 15px;
}

.panel_s {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    margin-bottom: 10px;
}

.panel-heading {
    color: #fff;
    padding: 10px 15px;
    border-bottom: 1px solid transparent;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
}

.kan-ban-content {
    max-height: calc(100vh - 350px);
    overflow-y: auto;
}

.kan-ban-content-wrapper {
    background-color: #f5f5f5;
    padding: 10px;
}

.stage {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.stage li {
    background-color: #fff;
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 3px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .kan-ban-col {
        flex: 0 0 250px;
    }
}
</style>
<?php $this->load->view('leads/add_modal'); ?>