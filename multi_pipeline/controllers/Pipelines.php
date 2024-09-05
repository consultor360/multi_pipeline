<?php
// Caminho: /public_html/modules/multi_pipeline/controllers/Pipelines.php

defined('BASEPATH') or exit('No direct script access allowed');

class Pipelines extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('multi_pipeline_model');
        $this->load->model('Lead_model');
    }

    public function index()
    {
        $data['title'] = _l('pipelines');

        // Consultar a tabela "leads" e filtrar por pipeline_id válido
        $leads = $this->db->query("SELECT * FROM leads WHERE pipeline_id IS NOT NULL");

        // Passar os leads filtrados para a view
        $data['leads'] = $leads->result_array();

        $data['multi_pipeline_pipelines'] = $this->multi_pipeline_model->get_pipelines();
        $this->load->view('multi_pipeline/pipelines/list', $data);
    }
    
    public function view($id)
{
    if (!has_permission('multi_pipeline', '', 'view')) {
        access_denied('multi_pipeline');
    }

    $pipeline = $this->Multi_pipeline_model->get($id);
    if (!$pipeline) {
        show_404();
    }

    $data['pipeline'] = $pipeline;
    $data['stages'] = $this->Multi_pipeline_model->get_pipeline_stages($id);
    $data['grouped_leads'] = $this->Lead_model->get_leads_by_pipeline_and_stage($id);

    $this->load->view('pipelines/view', $data);
}

    public function create()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $this->multi_pipeline_model->add_pipeline($data);
            if ($id) {
                set_alert('success', _l('pipeline_added_successfully'));
                redirect(admin_url('multi_pipeline/pipelines'));
            }
        }
        $data['title'] = _l('new_pipeline');
        $this->load->view('multi_pipeline/pipelines/create', $data);
    }

    public function edit($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->multi_pipeline_model->update_pipeline($id, $data);
            if ($success) {
                set_alert('success', _l('pipeline_updated_successfully'));
                redirect(admin_url('multi_pipeline/pipelines'));
            }
        }
        $data['pipeline'] = $this->multi_pipeline_model->get_pipelines($id);
        $data['title'] = _l('edit_pipeline');
        $this->load->view('multi_pipeline/pipelines/edit', $data);
    }

    public function delete($id)
    {
        if ($this->multi_pipeline_model->delete_pipeline($id)) {
            set_alert('success', _l('pipeline_deleted_successfully'));
        } else {
            set_alert('warning', _l('problem_deleting_pipeline'));
        }
        redirect(admin_url('multi_pipeline/pipelines'));
    }
    
    public function edits($id)
{
    if (!has_permission('multi_pipeline', '', 'edit')) {
        access_denied('multi_pipeline');
    }

    if ($this->input->post()) {
        $data = $this->input->post();
        $success = $this->multi_pipeline_model->update_pipeline($id, $data);
        if ($success) {
            set_alert('success', _l('updated_successfully', _l('pipeline')));
            redirect(admin_url('multi_pipeline/pipelines'));
        }
    }

    $pipeline = $this->multi_pipeline_model->get_pipelines($id);
    if (!$pipeline) {
        show_404();
    }

    $data['pipeline'] = $pipeline;
    $data['title'] = _l('edit_pipeline');
    
    $this->load->view('multi_pipeline/pipelines/edit', $data);
}
    
}