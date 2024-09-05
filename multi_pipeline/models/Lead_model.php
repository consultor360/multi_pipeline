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

}