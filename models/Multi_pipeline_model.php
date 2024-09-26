<?php
// Caminho: /public_html/modules/multi_pipeline/models/Multi_pipeline_model.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Multi Pipeline Model
 */
class Multi_pipeline_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
 * Obtém todos os pipelines ou um pipeline específico pelo ID.
 *
 * @param int|null $id O ID do pipeline (opcional).
 * @param array $where Um array de condições WHERE adicionais (opcional).
 * @return array|object Um array de pipelines ou um objeto de pipeline se $id for fornecido.
 */
public function get_pipelines($id = null, $where = [])
{
    if ($id !== null) {
        // Verificar se o parâmetro $id é válido e seguro
        if (!is_numeric($id) || $id < 1) {
            throw new InvalidArgumentException('Invalid pipeline ID');
        }
    }

    $this->db->from(db_prefix() . 'multi_pipeline_pipelines');

    if ($id !== null) {
        $this->db->where('id', $id);
        return $this->db->get()->row(); // Retorna um objeto para um único registro
    } else {
        // Verificar se o parâmetro $where é válido e seguro
        if (!is_array($where) || empty($where)) {
            $where = [];
        }

        $this->db->where($where); // Aplica filtros adicionais se fornecidos
        return $this->db->get()->result_array(); // Retorna um array para múltiplos registros
    }
}

/**
 * Obtém todos os estágios de um pipeline pelo ID do pipeline.
 *
 * @param int $pipeline_id O ID do pipeline.
 * @return array Um array de estágios do pipeline.
 */
public function get_pipeline_stages($pipeline_id)
{
    // Verificar se o parâmetro $pipeline_id é válido e seguro
    if (!is_numeric($pipeline_id) || $pipeline_id < 1) {
        throw new InvalidArgumentException('Invalid pipeline ID');
        
        
    }

    $this->db->select('mps.*')
             ->from(db_prefix(). 'multi_pipeline_stages mps')
             ->join(db_prefix(). 'multi_pipeline_pipelines mpp', 'mpp.id = mps.pipeline_id')
             ->where('mpp.id', $pipeline_id);

    return $this->db->get()->result_array();
}

/**
 * Obtém todos os leads de um pipeline pelo ID do pipeline.
 *
 * @param int $pipeline_id O ID do pipeline.
 * @param array $where Um array de condições WHERE adicionais (opcional).
 * @return array Um array de leads do pipeline.
 */
public function get_pipeline_leads($pipeline_id, $where = [])
{
    // Verificar se o parâmetro $pipeline_id é válido e seguro
    if (!is_numeric($pipeline_id) || $pipeline_id < 1) {
        throw new InvalidArgumentException('Invalid pipeline ID');
    }

    // Verificar se o parâmetro $where é válido e seguro
    if (!is_array($where) || empty($where)) {
        $where = [];
    }

    $this->db->select('mpl.*, l.name, l.email, l.phonenumber, s.name as stage_name, l.pipeline_id')
             ->from(db_prefix(). 'leads mpl')
             ->join(db_prefix(). 'leads l', 'l.id = mpl.perfex_lead_id')
             ->join(db_prefix(). 'multi_pipeline_stages s', 's.id = mpl.stage_id')
             ->where('mpl.pipeline_id', $pipeline_id)
             ->where($where);

    return $this->db->get()->result_array();
}

    /**
     * Add a new pipeline
     * 
     * @param array $data
     * @return int|bool The inserted ID on success, false on failure
     */
    public function add_pipeline($data)
    {
        $this->db->insert(db_prefix() . 'multi_pipeline_pipelines', $data);
        return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : false;
    }

    /**
     * Update a pipeline
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_pipeline($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pipelines', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete a pipeline and handle related data
     * 
     * @param int $id
     * @return bool
     */
    public function delete_pipeline($id)
    {
        $this->db->trans_start();
        
        // Delete pipeline
        $this->db->where('id', $id)->delete(db_prefix() . 'multi_pipeline_pipelines');
        
        // Delete associated stages
        $this->db->where('pipeline_id', $id)->delete(db_prefix() . 'multi_pipeline_stages');
        
        // Update associated leads
        $this->db->where('pipeline_id', $id)
                 ->update(db_prefix() . 'multi_pipeline_leads', ['pipeline_id' => null, 'stage_id' => null]);
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }

    /**
     * Update lead stage
     * 
     * @param int $lead_id
     * @param int $stage_id
     * @return bool
     */
    public function update_lead_stage($lead_id, $stage_id)
    {
        $this->db->where('perfex_lead_id', $lead_id);
        $this->db->update(db_prefix() . 'multi_pipeline_leads', ['stage_id' => $stage_id]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Assign a lead to a pipeline
     * 
     * @param int $lead_id
     * @param int $pipeline_id
     * @return bool
     */
    public function assign_lead_to_pipeline($lead_id, $pipeline_id)
    {
        $first_stage = $this->db->where('pipeline_id', $pipeline_id)
                                ->order_by('order', 'ASC')
                                ->limit(1)
                                ->get(db_prefix() . 'multi_pipeline_stages')
                                ->row();

        if (!$first_stage) {
            return false;
        }

        $data = [
            'perfex_lead_id' => $lead_id,
            'pipeline_id' => $pipeline_id,
            'stage_id' => $first_stage->id
        ];

        $this->db->insert(db_prefix() . 'multi_pipeline_leads', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get lead count by stage
     * 
     * @param int $pipeline_id
     * @return array
     */
    public function get_lead_count_by_stage($pipeline_id)
{
    $this->db->select('s.id, s.name, COUNT(mpl.perfex_lead_id) as lead_count')
             ->from(db_prefix(). 'multi_pipeline_stages s')
             ->join(db_prefix(). 'multi_pipeline_leads mpl', 's.id = mpl.stage_id', 'left')
             ->where('s.pipeline_id', $pipeline_id)
             ->group_by('s.id');
    
    return $this->db->get()->result_array();
}

    /**
     * Move lead between pipelines
     * 
     * @param int $lead_id
     * @param int $new_pipeline_id
     * @return bool
     */
    public function move_lead_to_pipeline($lead_id, $new_pipeline_id)
    {
        $first_stage = $this->db->where('pipeline_id', $new_pipeline_id)
                                ->order_by('order', 'ASC')
                                ->limit(1)
                                ->get(db_prefix() . 'multi_pipeline_stages')
                                ->row();

        if (!$first_stage) {
            return false;
        }

        $this->db->where('perfex_lead_id', $lead_id);
        $this->db->update(db_prefix() . 'multi_pipeline_leads', [
            'pipeline_id' => $new_pipeline_id,
            'stage_id' => $first_stage->id
        ]);

        return $this->db->affected_rows() > 0;
    }
    
    public function get_first_pipeline()
    {
        return $this->db->order_by('id', 'ASC')->limit(1)->get('multi_pipeline_pipelines')->row();
    }
    
    public function create_triggers() {
    $db = CRM_DBManagerFactory::getInstance();
    $db->query("CREATE TRIGGER after_lead_insert_update_stage
                AFTER INSERT ON tblleads
                FOR EACH ROW
                BEGIN
                    UPDATE tblstages
                    SET lead_id = NEW.id
                    WHERE id = NEW.stage_id;
                END;");

    $db->query("CREATE TRIGGER after_stage_update_update_lead
                AFTER UPDATE ON tblstages
                FOR EACH ROW
                BEGIN
                    UPDATE tblleads
                    SET stage_id = NEW.id
                    WHERE id = NEW.lead_id;
                END;");
}

public function add_pipelines($data)
{
    $this->db->insert('tblmulti_pipeline_pipelines', $data);
    return $this->db->insert_id();
}

public function update_pipelines($id, $data)
{
    $this->db->where('id', $id);
    return $this->db->update('tblmulti_pipeline_pipelines', $data);
}

public function delete_pipelines($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('tblmulti_pipeline_pipelines');
}

public function get_all_statuses() {
        $this->db->select('id, name, pipeline_name, pipeline_id, color, order');
        $this->db->from('tblmulti_pipeline_stages');
        $query = $this->db->get();
        return $query->result_array();
    }

public function add_status($data) {
    $pipeline_id = $data['pipeline_id'];
    $pipeline_name = $this->db->select('name')->from('tblmulti_pipeline_pipelines')->where('id', $pipeline_id)->get()->row()->name;

    $data['pipeline_name'] = $pipeline_name; // Adicionado

    $this->db->insert('tblmulti_pipeline_stages', $data);
    return $this->db->insert_id();
}

public function get_kanban_pipeline_stages($pipeline_id)
{
    return $this->db->select('*')->where('pipeline_id', $pipeline_id)->get('tblmulti_pipeline_stages')->result_array();
}

public function get_kanban_pipeline_leads($pipeline_id)
{
    return $this->db->where('pipeline_id', $pipeline_id)->get('tblleads')->result_array();
}

public function update_kanban_lead_stage($lead_id, $stage_id)
{
    return $this->db->where('id', $lead_id)->update('tblleads', ['stage_id' => $stage_id]);
}

public function get_stages($pipeline_id = null)
{
    $this->db->from(db_prefix() . 'multi_pipeline_stages');
    if ($pipeline_id !== null) {
        $this->db->where('pipeline_id', $pipeline_id);
    }
    $this->db->order_by('pipeline_id', 'asc');
    $this->db->order_by('order', 'asc');
    return $this->db->get()->result_array();
}

public function get_leads($id = null, $where = [], $pipeline_id = null) {
    $this->db->select('id, title, pipeline_id, stage_id, created_at');
    $this->db->from(db_prefix() . 'leads'); // Certifique-se de que 'leads' é a tabela correta

    if ($id !== null) {
        // Verificar se o parâmetro $id é válido e seguro
        if (!is_numeric($id) || $id < 1) {
            throw new InvalidArgumentException('Invalid lead ID');
        }
        $this->db->where('id', $id);
    } else {
        // Verificar se o parâmetro $where é válido e seguro
        if (!is_array($where) || empty($where)) {
            $where = [];
        }
        $this->db->where($where);

        // Adicionar filtro de pipeline_id se fornecido
        if ($pipeline_id !== null) {
            $this->db->where('pipeline_id', $pipeline_id);
        }
    }

    $query = $this->db->get();

    if ($id !== null) {
        return $query->row(); // Retorna um objeto para um único registro
    } else {
        return $query->result_array(); // Retorna um array para múltiplos registros
    }
}

}