<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('add_new_lead'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <form action="<?php echo admin_url('multi_pipeline/add_lead'); ?>" method="post" id="add-lead-form">
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
                                                    echo '<option value="' . $pipeline['id'] . ',' . $stage['id'] . '">' . htmlspecialchars($stage['name']) . '</option>';
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
                                            <?php
                                            $statuses = $this->db->get('tblleads_status')->result_array();
                                            foreach ($statuses as $status) { ?>
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
                                    <div class="form-group">
                                        <label for="name">Nome *</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Endereço de E-mail *</label>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phonenumber">Telefone</label>
                                        <input type="tel" name="phonenumber" id="phonenumber" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website">Website</label>
                                        <input type="url" name="website" id="website" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="lead_value">Valor do Lead</label>
                                        <input type="number" name="lead_value" id="lead_value" class="form-control" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="company">Empresa</label>
                                        <input type="text" name="company" id="company" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="title">Posição</label>
                                        <input type="text" name="title" id="title" class="form-control">
                                    </div>
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
                                                <option value="<?php echo $country['country_id']; ?>"<?php if($country['country_id'] == 236) echo ' selected'; ?>><?php echo $country['short_name']; ?></option>
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
                            <input type="hidden" id="pipeline_id" name="pipeline_id" value="0">
                            <input type="hidden" id="stage_id" name="stage_id" value="0">
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-info">Adicionar Lead</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    $('.selectpicker').selectpicker();

    $('#pipeline_stage').change(function() {
        var selectedValue = $(this).val();
        console.log("Valor selecionado:", selectedValue);

        if (selectedValue) {
            var ids = selectedValue.split(',');
            if(ids.length === 2){
                var pipelineId = parseInt(ids[0]);
                var stageId = parseInt(ids[1]);

                console.log("Pipeline ID:", pipelineId);
                console.log("Stage ID:", stageId);

                if(!isNaN(pipelineId) && !isNaN(stageId)){
                    $('#pipeline_id').val(pipelineId);
                    $('#stage_id').val(stageId);

                    console.log("Hidden pipeline_id:", $('#pipeline_id').val());
                    console.log("Hidden stage_id:", $('#stage_id').val());
                } else {
                    console.error("Erro: Valores de pipeline_id ou stage_id inválidos.");
                    $('#pipeline_id').val('');
                    $('#stage_id').val('');
                }
            } else {
                console.error("Erro: O valor selecionado não está no formato esperado 'pipeline_id,stage_id'.");
                $('#pipeline_id').val('');
                $('#stage_id').val('');
            }
        } else {
            $('#pipeline_id').val('');
            $('#stage_id').val('');
            console.log("Pipeline e Stage IDs foram limpos.");
        }
    });

    $('#add-lead-form').on('submit', function(e) {
        e.preventDefault();

        console.log("Antes da submissão:");
        console.log("pipeline_id:", $('#pipeline_id').val());
        console.log("stage_id:", $('#stage_id').val());

        if(!$('#pipeline_id').val() || !$('#stage_id').val()){
            alert('Por favor, selecione um Pipeline e Estágio válidos.');
            return;
        }

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log("Resposta do servidor:", response);
                if (response && response.success) {
                    alert_float('success', 'Lead adicionado com sucesso!');
                    window.location.href = '<?php echo admin_url('multi_pipeline'); ?>';
                } else {
                    alert_float('warning', response.message || 'Lead adicionado, mas houve um problema na resposta do servidor.');
                    window.location.href = '<?php echo admin_url('multi_pipeline'); ?>';
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro AJAX:", status, error);
                console.log("Resposta do servidor:", xhr.responseText);
                alert_float('warning', 'Lead possivelmente adicionado, mas houve um problema na comunicação com o servidor.');
                window.location.href = '<?php echo admin_url('multi_pipeline'); ?>';
            }
        });
    });
});
</script>