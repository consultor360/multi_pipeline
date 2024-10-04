<?php
// Caminho: /public_html/modules/multi_pipeline/models/Lead_model.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Modelo para gerenciamento de leads no sistema de múltiplos pipelines
 */
class Lead_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('date');
        
        // Inicializar variáveis de configuração
        $this->table_leads = 'tblleads';
        $this->table_pipeline_stages = 'tblmulti_pipeline_stages';
        $this->table_pipeline_pipelines = 'tblmulti_pipeline_pipelines';
        $this->table_form_associations = 'tblmulti_pipeline_form_associations';
        $this->table_web_to_lead = 'tblweb_to_lead';
        $this->table_leads_sources = 'tblleads_sources';
        $this->table_leads_status = 'tblleads_status';
        $this->table_staff = 'tblstaff';
    }

    /**
     * Obtém leads agrupados por estágio para um pipeline específico
     *
     * @param int $pipeline_id ID do pipeline
     * @return array Leads agrupados por estágio
     */
    public function get_leads_by_pipeline_and_stage($pipeline_id)
    {
        if (!$pipeline_id) {
            return [];
        }
        
        // Seleciona os dados necessários dos leads e estágios
        $this->db->select('tblleads.*, tblmulti_pipeline_stages.name as stage_name, tblmulti_pipeline_stages.order as stage_order');
        $this->db->from('tblleads');
        $this->db->join('tblmulti_pipeline_stages', 'tblleads.stage_id = tblmulti_pipeline_stages.id', 'left');
        
        // Aplica filtros para garantir dados válidos
        $this->db->where('tblleads.pipeline_id', $pipeline_id);
        $this->db->where('tblleads.pipeline_id IS NOT NULL');
        $this->db->where('tblleads.pipeline_id !=', '');
        $this->db->where('tblleads.stage_id IS NOT NULL');
        
        // Ordena os resultados
        $this->db->order_by('tblmulti_pipeline_stages.order', 'ASC');
        $this->db->order_by('tblleads.dateadded', 'DESC');

        $query = $this->db->get();
        $results = $query->result_array();

        // Agrupa os leads por estágio
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

    /**
     * Adiciona um novo lead na tabela tblleads
     *
     * @param array $data Dados do lead
     * @return int|bool ID do lead inserido ou false em falha
     */
    public function add_lead($data)
    {
        // Prepara os dados do lead para inserção
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
        );

        // Insere o lead na tabela
        $this->db->insert('tblleads', $lead_data);
        return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : false;
    }

    /**
     * Obtém todas as fontes de leads
     *
     * @return array Lista de fontes de leads
     */
    public function get_sources()
    {
        return $this->db->get('tblleads_sources')->result_array();
    }

    /**
     * Obtém todos os status de leads
     *
     * @return array Lista de status de leads
     */
    public function get_status()
    {
        return $this->db->get('tblleads_status')->result_array();
    }

    /**
     * Obtém todos os membros da equipe
     *
     * @return array Lista de membros da equipe
     */
    public function get_staff()
    {
        return $this->db->get_staff('tblstaff')->result_array();
    }
}