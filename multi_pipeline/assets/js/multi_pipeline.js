
// Caminho: public_html/modules/multi_pipeline/assets/js/multi_pipeline.js
$(function() {
    'use strict';

    // Inicialização do módulo Multi Pipeline
    var MultiPipeline = {
        init: function() {
            this.bindEvents();
            this.initDragAndDrop();
        },

        bindEvents: function() {
            // Evento para adicionar novo lead
            $('#add_lead_btn').on('click', this.addLead);

            // Evento para importar leads
            $('#import_leads_btn').on('click', this.importLeads);

            // Evento para exibir resumo de leads
            $('#lead_summary_btn').on('click', this.showLeadSummary);
        },

        initDragAndDrop: function() {
            // Implementar funcionalidade de arrastar e soltar para mover leads entre status
            $('.pipeline-stage').sortable({
                connectWith: '.pipeline-stage',
                update: function(event, ui) {
                    var leadId = ui.item.data('lead-id');
                    var newStageId = ui.item.closest('.pipeline-stage').data('stage-id');
                    MultiPipeline.updateLeadStage(leadId, newStageId);
                }
            });
        },

        addLead: function() {
            // Lógica para adicionar novo lead
        },

        importLeads: function() {
            // Lógica para importar leads
        },

        showLeadSummary: function() {
            // Lógica para exibir resumo de leads
        },

        updateLeadStage: function(leadId, newStageId) {
            // Lógica para atualizar o estágio do lead via AJAX
            $.ajax({
                url: admin_url + 'multi_pipeline/update_lead_stage',
                type: 'POST',
                data: {
                    lead_id: leadId,
                    stage_id: newStageId
                },
                success: function(response) {
                    if (response.success) {
                        alert_float('success', 'Lead atualizado com sucesso');
                    } else {
                        alert_float('danger', 'Erro ao atualizar lead');
                    }
                }
            });
        }
    };

    // Inicializar o módulo
    MultiPipeline.init();
});