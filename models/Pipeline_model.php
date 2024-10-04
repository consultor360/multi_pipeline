<?php
// Caminho: /public_html/modules/multi_pipeline/models/Pipeline_model.php

defined('BASEPATH') or exit('No direct script access allowed');

class Pipeline_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('date');
        
        // Inicializar variáveis de configuração
        $this->table_pipelines = 'tblmulti_pipeline_pipelines';
        $this->table_stages = 'tblmulti_pipeline_stages';
    }

    /**
     * Obtém um pipeline específico ou todos os pipelines
     *
     * @param string|int $id ID do pipeline (opcional)
     * @return object|array Pipeline específico ou lista de todos os pipelines
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblmulti_pipeline_pipelines')->row();
        }
        return $this->db->get('tblmulti_pipeline_pipelines')->result_array();
    }

    /**
     * Adiciona um novo pipeline
     *
     * @param array $data Dados do pipeline a ser adicionado
     * @return int ID do pipeline recém-inserido
     */
    public function add($data)
    {
        $this->db->insert('tblmulti_pipeline_pipelines', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Novo Pipeline Adicionado [ID: ' . $insert_id . ']');
        }
        return $insert_id;
    }

    /**
     * Atualiza um pipeline existente
     *
     * @param int $id ID do pipeline a ser atualizado
     * @param array $data Novos dados do pipeline
     * @return int Número de linhas afetadas pela atualização
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblmulti_pipeline_pipelines', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Pipeline Atualizado [ID: ' . $id . ']');
        }
        return $this->db->affected_rows();
    }

    /**
     * Exclui um pipeline
     *
     * @param int $id ID do pipeline a ser excluído
     * @return int Número de linhas afetadas pela exclusão
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblmulti_pipeline_pipelines');
        if ($this->db->affected_rows() > 0) {
            log_activity('Pipeline Excluído [ID: ' . $id . ']');
        }
        return $this->db->affected_rows();
    }
    
    /**
     * Obtém todos os pipelines
     *
     * @return array Lista de todos os pipelines
     */
    public function get_pipelines()
    {
        return $this->db->get('tblmulti_pipeline_pipeliness')->result_array();
    }

    /**
     * Obtém um pipeline específico
     *
     * @param int $id ID do pipeline
     * @return array Dados do pipeline
     */
    public function get_pipeline($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblmulti_pipeline_pipelines')->row_array();
    }

    /**
     * Adiciona um novo pipeline (método alternativo)
     *
     * @param array $data Dados do pipeline a ser adicionado
     * @return int ID do pipeline recém-inserido
     */
    public function add_pipeline($data)
    {
        $this->db->insert('tblmulti_pipeline_pipelines', $data);
        return $this->db->insert_id();
    }

    /**
     * Atualiza um pipeline existente (método alternativo)
     *
     * @param int $id ID do pipeline a ser atualizado
     * @param array $data Novos dados do pipeline
     * @return bool Verdadeiro se atualizado com sucesso, falso caso contrário
     */
    public function update_pipeline($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblmulti_pipeline_pipelines', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Exclui um pipeline (método alternativo)
     *
     * @param int $id ID do pipeline a ser excluído
     * @return bool Verdadeiro se excluído com sucesso, falso caso contrário
     */
    public function delete_pipeline($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblmulti_pipeline_pipelines');
        return $this->db->affected_rows() > 0;
    }
}