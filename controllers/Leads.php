<?php
// Caminho: /public_html/modules/multi_pipeline/controllers/Leads.php

defined('BASEPATH') or exit('No direct script access allowed');

class Leads extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Multi_pipeline_model');
        $this->load->model('Lead_model');
    }

    public function add()
    {
        // Lógica para adicionar lead
        $this->load->view('multi_pipeline/leads/add');
    }

    public function import()
    {
        // Lógica para importar leads
        $this->load->view('multi_pipeline/leads/import');
    }

    public function summary()
    {
        // Lógica para exibir resumo de leads
        $this->load->view('multi_pipeline/leads/summary');
    }

    public function details($id)
    {
        // Lógica para exibir detalhes de um lead
        $data['lead'] = $this->Multi_pipeline_model->get_lead($id);
        $this->load->view('multi_pipeline/leads/details', $data);
    }
}