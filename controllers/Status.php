<?php
// Caminho: /public_html/modules/multi_pipeline/controllers/Status.php

defined('BASEPATH') or exit('No direct script access allowed');

class Status extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('multi_pipeline/multi_pipeline_model');
    }

    public function index()
    {
        // L��gica para listar todos os status
        $data['statuses'] = $this->multi_pipeline_model->get_all_statuses();
        $data['title'] = _l('lead_statuses');
        $this->load->view('multi_pipeline/status/list', $data);
    }

    public function create()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            // Valida�0�4�0�0o dos dados
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Nome do Status', 'required|trim');
            $this->form_validation->set_rules('color', 'Cor', 'required|trim');
            $this->form_validation->set_rules('pipeline_id', 'Pipeline', 'required|numeric');
            $this->form_validation->set_rules('order', 'Ordem', 'required|numeric');

            if ($this->form_validation->run() === TRUE) {
                $id = $this->multi_pipeline_model->add_status($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('lead_status')));
                    redirect(admin_url('multi_pipeline/status/create/' . $pipeline_id));
                } else {
                    set_alert('danger', _l('something_went_wrong'));
                }
            }
        }

        $data['pipelines'] = $this->multi_pipeline_model->get_pipelines();
        $data['title'] = _l('create_lead_status');
        $this->load->view('multi_pipeline/status/create', $data);
    }
}