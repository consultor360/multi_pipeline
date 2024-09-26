<?php 
// Caminho: /public_html/modules/multi_pipeline/views/pipelines/kanban.php

defined('BASEPATH') or exit('No direct script access allowed');
$is_admin = is_admin();
?>

<div class="kan-ban-wrapper" id="kan-ban-wrapper">
    <?php foreach ($pipelines as $pipeline) { ?>
        <div class="pipeline-section">
            <h3><?php echo e($pipeline['name']); ?></h3>
            <div class="kan-ban-row">
                <?php
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
                    <ul class="kan-ban-col" data-col-stage-id="<?php echo e($stage['id']); ?>" data-pipeline-id="<?php echo e($pipeline['id']); ?>" data-total-pages="<?php echo e($total_pages); ?>" data-total="<?php echo e($total_leads); ?>">
                        <li class="kan-ban-col-wrapper">
                            <div class="border-right panel_s">
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
                                                    $this->load->view('admin/leads/_kan_ban_card', ['lead' => $lead, 'stage' => $stage, 'pipeline' => $pipeline]);
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
                        </li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<script>
$(function() {
    init_kanban('leads/leads_kanban', $('.kan-ban-wrapper'), 'leads/leads_kanban_load_more');

    // Adicionar funcionalidade de arrastar e soltar
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

            // Atualizar o status do lead via AJAX
            $.post(admin_url + 'leads/update_lead_status', {
                lead_id: leadId,
                status_id: stageId,
                pipeline_id: pipelineId
            }).done(function(response) {
                // Atualizar a UI conforme necessário
            });
        }
    }).disableSelection();

    // Adicionar novo lead
    $(document).on('click', '.new-lead-from-stage', function() {
        var stageId = $(this).data('stage-id');
        var pipelineId = $(this).data('pipeline-id');
        // Abrir modal ou redirecionar para o formulário de novo lead
        // com os parâmetros stageId e pipelineId
    });

    // Expandir/recolher detalhes do lead
    $(document).on('click', '.kan-ban-expand-top', function(e) {
        e.preventDefault();
        var leadId = $(this).closest('li').data('lead-id');
        $('#kan-ban-expand-' + leadId).slideToggle();
    });
});
</script>