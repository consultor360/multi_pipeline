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
        $data['statuses'] = $this->multi_pipeline_model->get_all_statuses_with_lead_count();
        $data['title'] = _l('lead_statuses');
        $this->load->view('multi_pipeline/status/list', $data);
    }

    public function create()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            // Valida0400o dos dados
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

    public function edit($id)
    {
        if (!has_permission('multi_pipeline', '', 'edit')) {
            access_denied('edit_lead_status');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Nome do Status', 'required|trim');
            $this->form_validation->set_rules('color', 'Cor', 'required|trim');
            $this->form_validation->set_rules('pipeline_id', 'Pipeline', 'required|numeric');
            $this->form_validation->set_rules('order', 'Ordem', 'required|numeric');

            if ($this->form_validation->run() === TRUE) {
                $success = $this->multi_pipeline_model->update_status($id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('lead_status')));
                    redirect(admin_url('multi_pipeline/status'));
                } else {
                    set_alert('danger', _l('something_went_wrong'));
                }
            }
        }

        $data['status'] = $this->multi_pipeline_model->get_status($id);
        if (!$data['status']) {
            show_404();
        }

        $data['pipelines'] = $this->multi_pipeline_model->get_pipelines();
        $data['title'] = _l('edit_lead_status');
        $this->load->view('multi_pipeline/status/edit', $data);
    }

    public function delete($id)
    {
        if (!has_permission('multi_pipeline', '', 'delete')) {
            access_denied('delete_lead_status');
        }

        if ($this->multi_pipeline_model->delete_status($id)) {
            set_alert('success', _l('deleted_successfully', _l('lead_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_status')));
        }

        redirect(admin_url('multi_pipeline/status'));
    }
}