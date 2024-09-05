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
                                        <?php
                                        if (isset($stages) && is_array($stages)) {
                                            foreach ($stages as $stage) {
                                                if ($stage['pipeline_id'] != $pipeline['id']) continue;

                                                $kanBan = new \app\services\leads\LeadsKanban($stage['id']);
                                                $kanBan->search($this->input->get('search'))
                                                    ->sortBy($this->input->get('sort_by'), $this->input->get('sort'));
                                                if ($this->input->get('refresh')) {
                                                    $kanBan->refresh($this->input->get('refresh')[$stage['id']] ?? null);
                                                }
                                                $leads = $kanBan->get();
                                                $total_leads = count($leads);
                                                $total_pages = $kanBan->totalPages();

                                                $settings = '';
                                                foreach (get_system_favourite_colors() as $color) {
                                                    $color_selected_class = 'cpicker-small';
                                                    if ($color == $stage['color']) {
                                                        $color_selected_class = 'cpicker-big';
                                                    }
                                                    $settings .= "<div class='kanban-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ';border:1px solid ' . $color . "'></div>";
                                                }
                                                ?>
                                                <div class="kan-ban-col" data-col-stage-id="<?php echo e($stage['id']); ?>" data-pipeline-id="<?php echo e($pipeline['id']); ?>" data-total-pages="<?php echo e($total_pages); ?>" data-total="<?php echo e($total_leads); ?>">
                                                    <div class="panel panel_s">
                                                        <?php
                                                        $stage_color = '';
                                                        if (!empty($stage['color'])) {
                                                            $stage_color = 'style="background:' . $stage['color'] . ';border:1px solid ' . $stage['color'] . '"';
                                                        }
                                                        ?>
                                                        <div class="panel-heading tw-bg-neutral-700 tw-text-white" <?php echo $stage_color; ?> data-stage-id="<?php echo e($stage['id']); ?>">
                                                            <i class="fa fa-reorder pointer"></i>
                                                            <span class="heading pointer tw-ml-1" <?php if ($is_admin) { ?>
                                                                data-order="<?php echo e($stage['order']); ?>" data-color="<?php echo e($stage['color']); ?>"
                                                                data-name="<?php echo e($stage['name']); ?>"
                                                                onclick="edit_stage(this,<?php echo e($stage['id']); ?>); return false;"
                                                                <?php } ?>><?php echo e($stage['name']); ?>
                                                            </span>
                                                            <a href="#" onclick="return false;" class="pull-right color-white kanban-color-picker kanban-stage-color-picker" data-placement="bottom" data-toggle="popover" data-content="
                                                                <div class='text-center'>
                                                                  <button type='button' return false;' class='btn btn-primary btn-block mtop10 new-lead-from-stage' data-pipeline-id='<?php echo e($pipeline['id']); ?>' data-stage-id='<?php echo e($stage['id']); ?>'>
                                                                    <?php echo _l('new_lead'); ?>
                                                                  </button>
                                                                </div>
                                                                <?php if (is_admin()) {?>
                                                                <hr />
                                                                <div class='kan-ban-settings cpicker-wrapper'>
                                                                  <?php echo $settings; ?>
                                                                </div><?php } ?>" data-html="true" data-trigger="focus">
                                                                <i class="fa fa-angle-down"></i>
                                                            </a>
                                                        </div>
                                                        <div class="kan-ban-content-wrapper">
                                                            <div class="kan-ban-content">
                                                                <ul class="stage leads-stage sortable" data-lead-stage-id="<?php echo e($stage['id']); ?>">
                                                                    <?php
                                                                    foreach ($leads as $lead) {
                                                                        if ($lead['pipeline_id'] == $pipeline['id'] && $lead['stage_id'] == $stage['id']) {
                                                                            $this->load->view('pipelines/kanban_card', ['lead' => $lead, 'stage' => $stage, 'pipeline' => $pipeline]);
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <?php if ($total_leads > 0) { ?>
                                                                    <li class="text-center not-sortable kanban-load-more" data-load-stage="<?php echo e($stage['id']); ?>">
                                                                        <a href="#" class="btn btn-default btn-block<?php if ($total_pages <= 1 || $kanBan->getPage() === $total_pages) {
                                                                            echo ' disabled';
                                                                        } ?>" data-page="<?php echo $kanBan->getPage(); ?>"
                                                                            onclick="kanban_load_more(<?php echo e($stage['id']); ?>, this, 'leads/leads_kanban_load_more', 315, 360); return false;">
                                                                            <?php echo _l('load_more'); ?>
                                                                        </a>
                                                                    </li>
                                                                    <?php } ?>
                                                                    <li class="text-center not-sortable mtop30 kanban-empty<?php if ($total_leads > 0) {
                                                                        echo ' hide';
                                                                    } ?>">
                                                                        <h4>
                                                                            <i class="fa-solid fa-circle-notch" aria-hidden="true"></i><br /><br />
                                                                            <?php echo _l('no_leads_found'); ?>
                                                                        </h4>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                        } else {
                                            echo "Nenhum est��gio encontrado.";
                                        }
                                        ?>
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
                // Atualizar a UI conforme necess��rio
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
            var $innerContainer = $container.find('.kan-ban-inner-container');
            var containerWidth = $container.width();
            var totalWidth = 0;

            $innerContainer.find('.kan-ban-col').each(function() {
                totalWidth += $(this).outerWidth(true);
            });

            if (totalWidth > containerWidth) {
                $container.css('overflow-x', 'scroll');
                $innerContainer.width(totalWidth);
            } else {
                $container.css('overflow-x', 'hidden');
                $innerContainer.width('100%');
            }
        });
    }

    makeKanbanResponsive();
    $(window).resize(makeKanbanResponsive);
});

function openAddLeadModal(pipelineId = null, stageId = null) {
    var modalUrl = '<?php echo site_url('multi_pipeline/get_stages_by_pipeline'); ?>';
    if (pipelineId) {
        modalUrl += '/' + pipelineId;
    }
    if (stageId) {
        modalUrl += '/' + stageId;
    }
    
    $('#lead_modal').remove();
    $('<div id="lead_modal" class="modal fade" role="dialog"></div>').appendTo('body');
    $('#lead_modal').load(modalUrl, function() {
        $('#lead_modal').modal({show: true, backdrop: 'static'});
    });
}
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