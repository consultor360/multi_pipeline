<?php
// Caminho: /public_html/modules/multi_pipeline/models/Lead_model.php

defined('BASEPATH') or exit('No direct script access allowed');

class Lead_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_leads_by_pipeline_and_stage($pipeline_id)
    {
        if (!$pipeline_id) {
            return [];
        }
        
        $this->db->select('tblleads.*, tblmulti_pipeline_stages.name as stage_name, tblmulti_pipeline_stages.order as stage_order');
        $this->db->from('tblleads');
        $this->db->join('tblmulti_pipeline_stages', 'tblleads.stage_id = tblmulti_pipeline_stages.id', 'left');
        $this->db->where('tblleads.pipeline_id', $pipeline_id);
        $this->db->where('tblleads.pipeline_id IS NOT NULL');
        $this->db->where('tblleads.pipeline_id !=', '');
        $this->db->where('tblleads.stage_id IS NOT NULL');
        $this->db->order_by('tblmulti_pipeline_stages.order', 'ASC');
        $this->db->order_by('tblleads.dateadded', 'DESC');

        $query = $this->db->get();
        $results = $query->result_array();

        $grouped_leads = [];
        foreach ($results as $lead) {
            $stage_id = $lead['stage_id'];
            if (!isset($grouped_leads[$stage_id])) {
                $grouped_leads[$stage_id] = [
                    'stage_name' => $lead['stage_name'],
                    'stage_order' => $lead['stage_order'],
                    'leads' => []
                ];
            }
            $grouped_leads[$stage_id]['leads'][] = $lead;
        }

        return $grouped_leads;
    }

    public function add_lead($data) {
        // Preparar os dados do lead
        $lead_data = array(
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phonenumber'   => $data['phonenumber'],
            'company'       => $data['company'],
            'title'         => isset($data['title']) ? $data['title'] : null,
            'website'       => isset($data['website']) ? $data['website'] : null,
            'address'       => isset($data['address']) ? $data['address'] : null,
            'city'          => isset($data['city']) ? $data['city'] : null,
            'state'         => isset($data['state']) ? $data['state'] : null,
            'country'       => isset($data['country']) ? $data['country'] : null,
            'zip'           => isset($data['zip']) ? $data['zip'] : null,
            'description'   => isset($data['description']) ? $data['description'] : null,
            'tags'          => isset($data['tags']) ? $data['tags'] : null,
            'source'        => isset($data['source']) ? $data['source'] : null,
            'status'        => isset($data['status']) ? $data['status'] : null,
            'assigned'      => isset($data['assigned']) ? $data['assigned'] : null,
            'pipeline_id'   => isset($data['lead_pipeline_id']) ? $data['lead_pipeline_id'] : null,
            'stage_id'      => isset($data['lead_stage_id']) ? $data['lead_stage_id'] : null,
            'lead_value'    => isset($data['lead_value']) ? $data['lead_value'] : null,
            'dateadded'     => date('Y-m-d H:i:s') // Adiciona a data atual
            // Adicione outros campos conforme necessário
        );

        // Inserir o lead na tabela
        $this->db->insert('tblleads', $lead_data);
        return $this->db->insert_id();
    }

    public function get_sources()
    {
        return $this->db->get('tblleads_sources')->result_array();
    }

    public function get_status()
    {
        return $this->db->get('tblleads_status')->result_array(); // Ajuste o nome da tabela conforme necessário
    }

    public function get_staff()
    {
        return $this->db->get('tblstaff')->result_array(); // Ajuste o nome da tabela conforme necessário
    }

}