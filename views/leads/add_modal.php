<!--Caminho: /public_html/modules/multi_pipeline/views/leads/add_modal.php -->

<!-- Adicione este botão no topo da página, à direita -->
<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <!-- Título da página ou outro conteúdo -->
                </div>
                <div class="col-md-4 text-right">
                    <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#addLeadModal">
                        <i class="fa fa-plus"></i> Adicionar novo Lead
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para adicionar novo lead -->
<div class="modal fade" id="addLeadModal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addLeadModalLabel">Adicionar novo Lead</h4>
            </div>
            <div class="modal-body">
            <form action="<?php echo admin_url('multi_pipeline/add_lead'); ?>" method="post">
                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pipeline_stage">Pipeline/Estágio</label>
                                <select id="pipeline_stage" name="pipeline_stage" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">Selecione um Pipeline/Estágio</option>
                                    <?php
                                    $pipelines = $this->db->get('tblmulti_pipeline_pipelines')->result_array();
                                    foreach ($pipelines as $pipeline) {
                                        echo '<optgroup label="' . htmlspecialchars($pipeline['name']) . '">';
                                        $stages = $this->db->get_where('tblmulti_pipeline_stages', ['pipeline_id' => $pipeline['id']])->result_array();
                                        foreach ($stages as $stage) {
                                            echo '<option value="' . $stage['id'] . '" data-pipeline-id="' . $pipeline['id'] . '">' . htmlspecialchars($stage['name']) . '</option>';
                                        }
                                        echo '</optgroup>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="default_status_id">Status Padrão</label>
                                <select name="status" id="default_status_id" class="form-control selectpicker" data-live-search="true" required>
                                    <option value=""><?php echo _l('select_status'); ?></option>
                                    <?php foreach ($statuses as $status) { ?>
                                        <option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                    
                            <div class="form-group">
    <label for="source">Fonte</label>
    <select name="source" id="source" class="form-control selectpicker" data-live-search="true" required>
        <?php
        $sources = $this->db->get('tblleads_sources')->result_array();
        if (!empty($sources)) {
            foreach ($sources as $source) {
                ?>
                <option value="<?php echo $source['id']; ?>"><?php echo $source['name']; ?></option>
                <?php
            }
        } else {
            ?>
            <option value="">Nenhuma fonte encontrada</option>
            <?php
        }
        ?>
    </select>
</div>
<div class="form-group">
    <label for="assigned">Atribuído a</label>
    <select name="assigned" id="assigned" class="form-control selectpicker" data-live-search="true">
        <option value="">Nenhum</option>
        <?php
        $staff = $this->db->get('tblstaff')->result_array();
        if (!empty($staff)) {
            foreach ($staff as $member) {
                ?>
                <option value="<?php echo $member['staffid']; ?>"><?php echo $member['firstname'] . ' ' . $member['lastname']; ?></option>
                <?php
            }
        }
        ?>
    </select>
</div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tags">Tags</label>
                                <input type="text" name="tags" id="tags" class="form-control" data-role="tagsinput">
                            </div>
                            <div class="form-group">
                                <label for="name">Nome *</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="title">Posição</label>
                                <input type="text" name="title" id="title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="email">Endereço de E-mail</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" name="website" id="website" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="phonenumber">Telefone</label>
                                <input type="tel" name="phonenumber" id="phonenumber" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="lead_value">Valor do Lead</label>
                                <input type="number" name="lead_value" id="lead_value" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label for="company">Empresa</label>
                                <input type="text" name="company" id="company" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Endereço</label>
                                <input type="text" name="address" id="address" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="city">Cidade</label>
                                <input type="text" name="city" id="city" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="state">Estado</label>
                                <input type="text" name="state" id="state" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="country">País</label>
                                <select name="country" id="country" class="form-control selectpicker" data-live-search="true">
                                    <?php foreach (get_all_countries() as $country) { ?>
                                        <option value="<?php echo $country['country_id']; ?>"<?php if($country['country_id'] == 30) echo ' selected'; ?>><?php echo $country['short_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="zip">CEP</label>
                                <input type="text" name="zip" id="zip" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Descrição</label>
                                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-info">Adicionar Lead</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar select2 para campos de seleção
    $('.selectpicker').select2({
        theme: 'bootstrap'
    });

    // Inicializar tagsinput para o campo de tags
    $('#tags').tagsinput({
        tagClass: 'label label-info'
    });

    // Carregar estágios baseados no pipeline selecionado
    $('#pipeline').change(function() {
        var pipelineId = $(this).val();
        $.ajax({
            url: admin_url + 'multi_pipeline/get_stages_by_pipeline',
            method: 'POST',
            data: {pipeline_id: pipelineId},
            dataType: 'json',
            success: function(response) {
                var options = '';
                $.each(response, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#status').html(options).selectpicker('refresh');
            }
        });
    });

    // Submeter o formulário via AJAX
    $('form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-Token': $('input[name="csrf_token_name"]').val(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Lead adicionado com sucesso!');
                    $('#addLeadModal').modal('hide');
                    // Recarregar a página ou atualizar a lista de leads
                } else {
                    alert_float('danger', response.message || 'Erro ao adicionar lead. Por favor, tente novamente.');
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Erro ao processar a requisição. Por favor, tente novamente.');
            }
        });
    });

    $('#pipeline_stage').change(function() {
        var selectedStage = $(this).val();
        var pipelineId = $(this).find('option:selected').data('pipeline-id');
        $('#lead_pipeline_id').val(pipelineId);
        $('#lead_stage_id').val(selectedStage);
    });
});
</script>

<input type="hidden" id="lead_pipeline_id" name="lead_pipeline_id" value="">
<input type="hidden" id="lead_stage_id" name="lead_stage_id" value="">