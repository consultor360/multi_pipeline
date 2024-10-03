<?php
// Caminho: /public_html/modules/multi_pipeline/controllers/Multi_pipeline.php

defined('BASEPATH') or exit('No direct script access allowed');

class Multi_pipelines extends CI_Controller {

    public function pipelines() {
        // ...
    }

    public function edit($id) {
        // Load the edit pipeline view here
        $this->load->view('modules/multi_pipeline/views/pipelines/edit', array('id' => $id));
    }

}

/**
 * Multi Pipeline Controller
 */
class Multi_pipeline extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Multi_pipeline_model');
        $this->load->library('form_validation');
        $this->load->model('Pipeline_model');
        $this->load->model('currencies_model');
        $this->load->model('Lead_model'); // Adicionado
    }

    /**
     * Index function - List all pipelines
     */
    public function index()
    {
        if (!has_permission('multi_pipeline', '', 'view')) {
            access_denied('multi_pipeline');
        }

        $data['title'] = _l('multi_pipeline');
        $data['pipelines'] = $this->Multi_pipeline_model->get_pipelines();
        $data['summary'] = [];
        

        // Adicione os dados do pipeline nativo
        $this->load->model('Leads_model');
        $data['statuses'] = $this->Leads_model->get_status();

        // Obtenha o resumo dos leads
        foreach ($data['statuses'] as $status) {
            $total_leads = total_rows(db_prefix() . 'leads', ['status' => $status['id']]);

            // Calcular o valor total dos leads para este status
            $this->db->select_sum('lead_value');
            $this->db->where('status', $status['id']);
            $value_result = $this->db->get(db_prefix() . 'leads')->row();
            $total_value = $value_result ? $value_result->lead_value : 0;

            $data['summary'][] = [
                'pipeline_id' => 0, // 0 para o pipeline nativo
                'status_id' => $status['id'],
                'name' => $status['name'],
                'color' => $status['color'],
                'total' => $total_leads,
                'value' => $total_value
            ];
        }
        
        // Recuperar novamente os pipelines se necessário
        $data['pipelines'] = $this->Multi_pipeline_model->get_pipelines();
        $data['stages'] = $this->Multi_pipeline_model->get_stages();
        
        // Recuperar leads agrupados por pipeline e estágio
        $data['leads'] = $this->Multi_pipeline_model->get_leads_grouped();
        
        $data['bodyclass'] = 'kan-ban-body';
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['base_currency'] = $base_currency ? $base_currency : (object)['symbol' => '$'];

        $this->load->view('pipelines/list', $data);
    }

    public function list()
    {
        $this->load->model('Multi_pipeline_model');
        $pipelines = $this->Multi_pipeline_model->get_pipelines();

        $data['pipelines'] = [];
        foreach ($pipelines as $pipeline) {
            $stages = $this->Multi_pipeline_model->get_kanban_pipeline_stages($pipeline['id']);
            $leads = $this->Multi_pipeline_model->get_kanban_pipeline_leads($pipeline['id']);

            $pipeline_data = [
                'id' => $pipeline['id'],
                'name' => $pipeline['name'],
                'stages' => $stages,
                'leads' => $leads
            ];

            $data['pipelines'][] = $pipeline_data;
        }

        $data['title'] = _l('pipelines');
        $data['bodyclass'] = 'kan-ban-body';

        $this->load->view('multi_pipeline/pipelines/kanban', $data);
    }
    
    public function table()
    {
        if (!has_permission('multi_pipeline', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('pipelines');
    }
    

    /**
     * Create pipeline function
     */
    public function create_pipeline()
    {
        if (!has_permission('multi_pipeline', '', 'create')) {
            access_denied('create_pipeline');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('name', _l('pipeline_name'), 'required|max_length[255]|is_unique[' . db_prefix() . 'multi_pipeline_pipelines.name]');
            $this->form_validation->set_rules('description', _l('pipeline_description'), 'trim');

            if ($this->form_validation->run() === TRUE) {
                $data = $this->input->post();
                $this->load->model('multi_pipeline_model');
                $pipeline_id = $this->multi_pipeline_model->add_pipeline($data);
                if ($pipeline_id) {
                    log_activity('New Pipeline Created [ID: ' . $pipeline_id . ', Name: ' . $data['name'] . ']');
                    set_alert('success', _l('pipeline_created_successfully'));
                    redirect(admin_url('multi_pipeline/status/create/' . $pipeline_id));
                } else {
                    set_alert('danger', _l('pipeline_creation_failed'));
                }
            }
        }

        $data['title'] = _l('create_pipeline');
        $this->load->view('multi_pipeline/pipelines/create', $data);
    }

    /**
     * Edit pipeline function
     * 
     * @param int $id
     */
    public function edit_pipeline($id)
    {
        if (!has_permission('multi_pipeline', '', 'edit')) {
            access_denied('edit_pipeline');
        }

        $pipeline = $this->multi_pipeline_model->get_pipeline($id);
        if (!$pipeline) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('name', _l('pipeline_name'), 'required|max_length[255]|edit_unique[' . db_prefix() . 'pipelines.name.' . $id . ']');
            $this->form_validation->set_rules('description', _l('pipeline_description'), 'trim');

            if ($this->form_validation->run() === TRUE) {
                $data = $this->input->post();
                if ($this->multi_pipeline_model->update_pipeline($id, $data)) {
                    log_activity('Pipeline Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
                    set_alert('success', _l('pipeline_updated_successfully'));
                    redirect(admin_url('multi_pipeline/view_pipeline/' . $id));
                } else {
                    set_alert('danger', _l('pipeline_update_failed'));
                }
            }
        }

        $data['pipeline'] = $pipeline;
        $data['title'] = _l('edit_pipeline');
        $this->load->view('multi_pipeline/edit_pipeline', $data);
    }

    /**
     * Delete pipeline function
     * 
     * @param int $id
     */
    public function delete_pipeline($id)
    {
        if (!has_permission('multi_pipeline', '', 'delete')) {
            access_denied('delete_pipeline');
        }

        $pipeline = $this->multi_pipeline_model->get_pipeline($id);
        if (!$pipeline) {
            show_404();
        }

        if ($this->multi_pipeline_model->delete_pipeline($id)) {
            log_activity('Pipeline Deleted [ID: ' . $id . ', Name: ' . $pipeline->name . ']');
            set_alert('success', _l('pipeline_deleted_successfully'));
        } else {
            set_alert('danger', _l('pipeline_deletion_failed'));
        }
        redirect(admin_url('multi_pipeline'));
    }

    /**
 * View pipeline function
 * 
 * @param int $id (opcional)
 */
public function view_pipeline($id = null)
{
    if (!has_permission('multi_pipeline', '', 'view')) {
        access_denied('multi_pipeline');
    }

    if ($id === null) {
        $first_pipeline = $this->multi_pipeline_model->get_first_pipeline();
        
        if ($first_pipeline) {
            $id = $first_pipeline->id;
        } else {
            set_alert('warning', _l('no_pipelines_found'));
            redirect(admin_url('multi_pipeline'));
        }
    }
    $this->load->model('multi_pipeline_model');

    $data['pipeline'] = $this->multi_pipeline_model->get_pipeline($id);
    if (!$data['pipeline']) {
        show_404();
    }
    $data['multi_pipeline_stages'] = $this->multi_pipeline_model->get_pipeline_stages($id);
    $data['leads'] = $this->multi_pipeline_model->get_pipeline_leads($id);
    $data['lead_count_by_stage'] = $this->multi_pipeline_model->get_lead_count_by_stage($id);
    $data['title'] = $data['pipeline']->name;
    $this->load->view('pipelines/list', $data);
}

    /**
     * Update lead stage (AJAX)
     */
    public function update_lead_stage()
    {
        if (!has_permission('multi_pipeline', '', 'edit')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            die();
        }

        $lead_id = $this->input->post('lead_id');
        $stage_id = $this->input->post('stage_id');

        if ($this->multi_pipeline_model->update_lead_stage($lead_id, $stage_id)) {
            $lead = $this->multi_pipeline_model->get_pipeline_leads($stage_id, ['perfex_lead_id' => $lead_id])[0];
            log_activity('Lead Stage Updated [Lead ID: ' . $lead_id . ', New Stage: ' . $lead['stage_name'] . ']');
            echo json_encode(['success' => true, 'message' => _l('lead_stage_updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('lead_stage_update_failed')]);
        }
    }

    /**
     * Move lead to another pipeline (AJAX)
     */
    public function move_lead_to_pipeline()
    {
        if (!has_permission('multi_pipeline', '', 'edit')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            die();
        }

        $lead_id = $this->input->post('lead_id');
        $pipeline_id = $this->input->post('pipeline_id');

        if ($this->multi_pipeline_model->move_lead_to_pipeline($lead_id, $pipeline_id)) {
            $pipeline = $this->multi_pipeline_model->get_pipeline($pipeline_id);
            log_activity('Lead Moved to New Pipeline [Lead ID: ' . $lead_id . ', New Pipeline: ' . $pipeline->name . ']');
            echo json_encode(['success' => true, 'message' => _l('lead_moved_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('lead_move_failed')]);
        }
    }
    
    public function view_pipelines() {
    $pipeline = // retrieve the pipeline data from the database or model
    $this->load->view('pipelines/view', array('pipeline' => $pipeline));
}

public function update_kanban_lead_stage()
{
    $lead_id = $this->input->post('lead_id');
    $stage_id = $this->input->post('stage_id');
    
    $result = $this->multi_pipeline_model->update_kanban_lead_stage($lead_id, $stage_id);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

public function add_modal($pipeline_id = null, $stage_id = null)
{
    // Carregar os pipelines
    $data['pipelines'] = $this->Multi_pipeline_model->get_pipelines();

    // Carregar os estágios agrupados por pipeline
    $all_stages = $this->Multi_pipeline_model->get_stages();
    $data['stages'] = [];
    foreach ($all_stages as $stage) {
        $data['stages'][$stage['pipeline_id']][] = $stage;
    }

    // Carregar status
    $data['statuses'] = $this->Lead_model->get_status();

    // Carregar fontes (sources)
    $data['sources'] = $this->Lead_model->get_sources();

    // Carregar staff
    $this->load->model('Lead_model');
    $data['staff'] = $this->Lead_model->get_staff();

    // Passar os IDs do pipeline e estágio, se fornecidos
    $data['pipeline_id'] = $pipeline_id;
    $data['stage_id'] = $stage_id;

    $this->load->view('modules/multi_pipeline/views/leads/add_modal', $data);
}
    
    public function add_lead()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            // Validar e processar os dados conforme necessário

            // Salvar o lead
            $lead_id = $this->Multi_pipeline_model->add_lead($data);

            if ($lead_id) {
                echo json_encode(['success' => true, 'message' => 'Lead adicionado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao adicionar lead.']);
            }
            return;
        }

        // Carregar dados necessários para a view
        $data['pipelines'] = $this->Multi_pipeline_model->get_pipelines();
        $data['stages'] = $this->Multi_pipeline_model->get_stages();
        $data['statuses'] = $this->Lead_model->get_status(); // Atualizado para Lead_model
        $data['title'] = _l('add_new_lead');
        $this->load->view('multi_pipeline/leads/add', $data);
        
        // Carregar o modelo Lead_model se ainda não estiver carregado
        if (!$this->load->is_loaded('Lead_model')) {
            $this->load->model('Lead_model');
        }
        $data['sources'] = $this->Lead_model->get_sources(); // Atualizado para Lead_model
        
        // Carregar o modelo Lead_model_model se ainda não estiver carregado
        if (!$this->load->is_loaded('Lead_model')) {
            $this->load->model('Lead_model');
        }
        $data['staff'] = $this->Lead_model->get_staff(); // Função para obter staff

        $this->load->view('modules/multi_pipeline/views/leads/add_modal', $data);
    }

    public function get_stages_by_pipeline()
    {
        $pipeline_id = $this->input->post('pipeline_id');
        $stages = $this->multi_pipeline_model->get_stages_by_pipeline($pipeline_id);
        echo json_encode($stages);
    }

    public function summary()
    {
        if (!$this->Multi_pipeline_model) {
            $this->load->model('Multi_pipeline_model');
        }
        $data['pipelines'] = $this->Multi_pipeline_model->get_pipelines_with_stages_and_lead_count();
        $data['leads'] = $this->Multi_pipeline_model->get_all_leads();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title'] = _l('lead_summary');
        $this->load->view('multi_pipeline/leads/summary', $data);
    }

    public function change_lead_pipeline_stage()
{
    $lead_id = $this->input->post('lead_id');
    $pipeline_id = $this->input->post('pipeline_id');
    $stage_id = $this->input->post('stage_id');

    $result = $this->Multi_pipeline_model->update_lead_pipeline_stage($lead_id, $pipeline_id, $stage_id);

    if ($result) {
        echo json_encode(['success' => true, 'message' => _l('lead_pipeline_stage_updated_successfully')]);
    } else {
        echo json_encode(['success' => false, 'message' => _l('lead_pipeline_stage_update_failed')]);
    }
}

    /**
     * Salva uma associação de formulário
     */
    public function save_form_association()
    {
        if (!has_permission('multi_pipeline', '', 'create') && !has_permission('multi_pipeline', '', 'edit')) {
            access_denied('multi_pipeline');
        }

        $this->form_validation->set_rules('form_id', 'Formulário', 'required|integer');
        $this->form_validation->set_rules('pipeline_stage', 'Pipeline e Estágio', 'required');

        if ($this->form_validation->run() === FALSE) {
            set_alert('danger', validation_errors());
            redirect(admin_url('multi_pipeline'));
        }

        $pipeline_stage = explode(',', $this->input->post('pipeline_stage'));
        $pipeline_id = $pipeline_stage[0];
        $stage_id = $pipeline_stage[1];
        $form_id = $this->input->post('form_id');

        // Verifica se já existe associação para o formulário
        $existing = $this->db->where('form_id', $form_id)->get('multi_pipeline_form_associations')->row();
        if ($existing) {
            // Atualiza a associação existente
            $data = [
                'pipeline_id' => $pipeline_id,
                'stage_id' => $stage_id,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->Multi_pipeline_model->update_form_association($existing->id, $data);
            set_alert('success', 'Associação atualizada com sucesso.');
        } else {
            // Cria uma nova associação
            $data = [
                'form_id' => $form_id,
                'pipeline_id' => $pipeline_id,
                'stage_id' => $stage_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $new_id = $this->Multi_pipeline_model->add_form_association($data);
            if ($new_id) {
                set_alert('success', 'Associação criada com sucesso.');
            } else {
                set_alert('danger', 'Falha ao criar a associação.');
            }
        }

        redirect(admin_url('multi_pipeline'));
    }

    /**
     * Edita uma associação de formulário
     * 
     * @param int $id
     */
    public function edit_form_association($id)
    {
        if (!has_permission('multi_pipeline', '', 'edit')) {
            access_denied('multi_pipeline');
        }

        $association = $this->Multi_pipeline_model->get_form_associations();
        $association = array_filter($association, function($assoc) use ($id) {
            return $assoc['id'] == $id;
        });

        if (empty($association)) {
            show_404();
        }

        $data['association'] = array_shift($association);
        $data['forms'] = $this->db->get('tblweb_to_lead')->result_array();
        $data['pipelines'] = $this->Multi_pipeline_model->get_pipelines();
        $data['title'] = 'Editar Associação de Formulário';

        $this->load->view('forms/edit_form_association', $data);
    }

    /**
     * Deleta uma associação de formulário
     * 
     * @param int $id
     */
    public function delete_form_association($id)
    {
        if (!has_permission('multi_pipeline', '', 'delete')) {
            access_denied('multi_pipeline');
        }

        if ($this->Multi_pipeline_model->delete_form_association($id)) {
            set_alert('success', 'Associação deletada com sucesso.');
        } else {
            set_alert('danger', 'Falha ao deletar a associação.');
        }

        redirect(admin_url('multi_pipeline'));
    }

    public function form_associations()
    {
        if (!has_permission('multi_pipeline', '', 'view')) {
            access_denied('multi_pipeline');
        }

        // Carregar dados necessários
        $data['forms'] = $this->db->get('tblweb_to_lead')->result_array();
        $data['pipelines'] = $this->Multi_pipeline_model->get_pipelines_with_stages();
        $data['associations'] = $this->Multi_pipeline_model->get_form_associations();
        $data['title'] = _l('form_associations');

        // Carregar a view
        $this->load->view('forms/form_associations', $data);
    }
}