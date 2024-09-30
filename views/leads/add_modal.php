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
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addLeadModalLabel">Adicionar novo Lead</h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo admin_url('multi_pipeline/add_lead'); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Pipeline -->
                            <div class="form-group">
                                <label for="pipeline_id">Pipeline</label>
                                <select name="pipeline_id" id="pipeline_id" class="form-control selectpicker" data-live-search="true" required>
                                    <option value=""><?php echo _l('select_pipeline'); ?></option>
                                    <?php foreach ($pipelines as $pipeline) { ?>
                                        <option value="<?php echo $pipeline['id']; ?>"><?php echo $pipeline['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Estágio do Lead -->
                            <div class="form-group">
                                <label for="stage_id">Estágio do Lead</label>
                                <select name="stage_id" id="stage_id" class="form-control selectpicker" data-live-search="true" required>
                                    <option value=""><?php echo _l('select_stage'); ?></option>
                                    <?php foreach ($stages as $stage) { ?>
                                        <option value="<?php echo $stage['id']; ?>"><?php echo $stage['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Status Padrão -->
                            <div class="form-group">
                                <label for="status">Status Padrão</label>
                                <select name="status" id="status" class="form-control selectpicker" data-live-search="true" required>
                                    <option value=""><?php echo _l('select_status'); ?></option>
                                    <?php foreach ($statuses as $status) { ?>
                                        <option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Fonte -->
                            <div class="form-group">
                                <label for="source">Fonte</label>
                                <select name="source" id="source" class="form-control selectpicker" data-live-search="true" required>
                                    <option value=""><?php echo _l('select_source'); ?></option>
                                    <?php foreach ($sources as $source) { ?>
                                        <option value="<?php echo $source['id']; ?>"><?php echo $source['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Atribuído a -->
                            <div class="form-group">
                                <label for="assigned">Atribuído a</label>
                                <select name="assigned" id="assigned" class="form-control selectpicker" data-live-search="true">
                                    <option value="">Nenhum</option>
                                    <?php foreach ($staff as $member) { ?>
                                        <option value="<?php echo $member['staffid']; ?>"><?php echo $member['firstname'] . ' ' . $member['lastname']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Tags -->
                            <div class="form-group">
                                <label for="tags">Tags</label>
                                <input type="text" name="tags" id="tags" class="form-control" data-role="tagsinput">
                            </div>
                            <!-- Nome -->
                            <div class="form-group">
                                <label for="name">Nome *</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <!-- Posição -->
                            <div class="form-group">
                                <label for="title">Posição</label>
                                <input type="text" name="title" id="title" class="form-control">
                            </div>
                            <!-- Endereço de E-mail -->
                            <div class="form-group">
                                <label for="email">Endereço de E-mail</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Website -->
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" name="website" id="website" class="form-control">
                            </div>
                            <!-- Telefone -->
                            <div class="form-group">
                                <label for="phonenumber">Telefone</label>
                                <input type="tel" name="phonenumber" id="phonenumber" class="form-control">
                            </div>
                            <!-- Valor do Lead -->
                            <div class="form-group">
                                <label for="lead_value">Valor do Lead</label>
                                <input type="number" name="lead_value" id="lead_value" class="form-control" step="0.01">
                            </div>
                            <!-- Empresa -->
                            <div class="form-group">
                                <label for="company">Empresa</label>
                                <input type="text" name="company" id="company" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Endereço -->
                            <div class="form-group">
                                <label for="address">Endereço</label>
                                <input type="text" name="address" id="address" class="form-control">
                            </div>
                            <!-- Cidade -->
                            <div class="form-group">
                                <label for="city">Cidade</label>
                                <input type="text" name="city" id="city" class="form-control">
                            </div>
                            <!-- Estado -->
                            <div class="form-group">
                                <label for="state">Estado</label>
                                <input type="text" name="state" id="state" class="form-control">
                            </div>
                            <!-- País -->
                            <div class="form-group">
                                <label for="country">País</label>
                                <select name="country" id="country" class="form-control selectpicker" data-live-search="true">
                                    <option value=""><?php echo _l('select_country'); ?></option>
                                    <?php foreach (get_all_countries() as $country) { ?>
                                        <option value="<?php echo $country['country_id']; ?>"<?php if($country['country_id'] == 30) echo ' selected'; ?>><?php echo $country['short_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- CEP -->
                            <div class="form-group">
                                <label for="zip">CEP</label>
                                <input type="text" name="zip" id="zip" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!-- Descrição -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Descrição</label>
                                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Rodapé do Modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-info">Adicionar Lead</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts JavaScript -->
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
    $('#pipeline_id').change(function() {
        var pipelineId = $(this).val();
        if(pipelineId != ''){
            $.ajax({
                url: '<?php echo admin_url('multi_pipeline/get_stages_by_pipeline'); ?>',
                method: 'POST',
                data: {pipeline_id: pipelineId},
                dataType: 'json',
                success: function(response) {
                    var options = '<option value=""><?php echo _l('select_stage'); ?></option>';
                    $.each(response, function(key, value) {
                        options += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $('#stage_id').html(options).selectpicker('refresh');
                }
            });
        } else {
            $('#stage_id').html('<option value=""><?php echo _l('select_stage'); ?></option>').selectpicker('refresh');
        }
    });

    // Submeter o formulário via AJAX
    $('#addLeadModal form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    $('#addLeadModal').modal('hide');
                    // Recarregar a página ou atualizar a lista de leads
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    });
});
</script>