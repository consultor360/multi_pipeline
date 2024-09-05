<?php
// Caminho: /public_html/modules/multi_pipeline/models/Pipeline_model.php

defined('BASEPATH') or exit('No direct script access allowed');

class Pipeline_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblmulti_pipeline_pipelines')->row();
        }
        return $this->db->get('tblmulti_pipeline_pipelines')->result_array();
    }

    public function add($data)
    {
        $this->db->insert('tblmulti_pipeline_pipelines', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Novo Pipeline Adicionado [ID: ' . $insert_id . ']');
        }
        return $insert_id;
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblmulti_pipeline_pipelines', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Pipeline Atualizado [ID: ' . $id . ']');
        }
        return $this->db->affected_rows();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblmulti_pipeline_pipelines');
        if ($this->db->affected_rows() > 0) {
            log_activity('Pipeline Excluído [ID: ' . $id . ']');
        }
        return $this->db->affected_rows();
    }
    
    public function get_pipelines()
    {
        return $this->db->get('tblmulti_pipeline_pipeliness')->result_array();
    }

    public function get_pipeline($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblmulti_pipeline_pipelines')->row_array();
    }

    public function add_pipeline($data)
    {
        $this->db->insert('tblmulti_pipeline_pipelines', $data);
        return $this->db->insert_id();
    }

    public function update_pipeline($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblmulti_pipeline_pipelines', $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete_pipeline($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblmulti_pipeline_pipelines');
        return $this->db->affected_rows() > 0;
    }
}