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
public function get_pipelines($where = [], $limit = '', $start = '')
{
    $this->db->select('*');
    $this->db->from('tblmulti_pipeline_pipelines');
    
    // Aplicar filtros adicionais se fornecidos
    if (!empty($where)) {
        $this->db->where($where);
    }
    
    // Aplicar limitação se fornecido
    if ($limit !== '') {
        $this->db->limit($limit, $start);
    }
    
    return $this->db->get()->result_array(); // Retorna um array para múltiplos registros
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
             ->from('tblmulti_pipeline_stages mps')
             ->join('tblmulti_pipeline_pipelines mpp', 'mpp.id = mps.pipeline_id')
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
             ->from('tblleads mpl')
             ->join('tblleads l', 'l.id = mpl.perfex_lead_id')
             ->join('tblmulti_pipeline_stages s', 's.id = mpl.stage_id')
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
        $this->db->insert('tblmulti_pipeline_pipelines', $data);
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
        $this->db->update('tblmulti_pipeline_pipelines', $data);
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
        $this->db->where('id', $id)->delete('tblmulti_pipeline_pipelines');
        
        // Delete associated stages
        $this->db->where('pipeline_id', $id)->delete('tblmulti_pipeline_stages');
        
        // Update associated leads
        $this->db->where('pipeline_id', $id)
                 ->update('tblleads', ['pipeline_id' => null, 'stage_id' => null]);
        
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
        $this->db->update('tblleads', ['stage_id' => $stage_id]);
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
                                ->get('tblmulti_pipeline_stages')
                                ->row();

        if (!$first_stage) {
            return false;
        }

        $data = [
            'perfex_lead_id' => $lead_id,
            'pipeline_id' => $pipeline_id,
            'stage_id' => $first_stage->id
        ];

        $this->db->insert('tblleads', $data);
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
             ->from('tblmulti_pipeline_stages s')
             ->join('tblmulti_pipeline_leads mpl', 's.id = mpl.stage_id', 'left')
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
        $this->db->trans_start();

        // Obter o estágio inicial do novo pipeline
        $first_stage = $this->db->where('pipeline_id', $new_pipeline_id)
                                ->order_by('order', 'ASC')
                                ->limit(1)
                                ->get('multi_pipeline_stages')
                                ->row();

        if (!$first_stage) {
            $this->db->trans_complete();
            return false;
        }

        // Atualizar o lead
        $this->db->where('id', $lead_id);
        $this->db->update('tblleads', [
            'pipeline_id' => $new_pipeline_id,
            'stage_id' => $first_stage->id
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
    
    public function get_first_pipeline()
    {
        return $this->db->order_by('id', 'ASC')->limit(1)->get('tblmulti_pipeline_pipelines')->row();
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
    $this->db->select('*');
    $this->db->from('tblmulti_pipeline_stages');
    
    if ($pipeline_id !== null) {
        $this->db->where('pipeline_id', $pipeline_id);
    }
    
    $this->db->order_by('order', 'ASC');
    return $this->db->get()->result_array();
}

public function get_leads($where = [])
{
    $this->db->select('*');
    $this->db->from('tblleads');
    
    if (!empty($where)) {
        $this->db->where($where);
    }
    
    return $this->db->get()->result_array();
}

public function get_leads_grouped()
{
    $this->db->select('tblleads.*, tblmulti_pipeline_stages.name as stage_name, tblmulti_pipeline_stages.color as stage_color, tblmulti_pipeline_pipelines.id as pipeline_id, tblmulti_pipeline_pipelines.name as pipeline_name');
    $this->db->from('tblleads');
    $this->db->join('tblmulti_pipeline_stages', 'tblmulti_pipeline_stages.id = tblleads.stage_id', 'left');
    $this->db->join('tblmulti_pipeline_pipelines', 'tblmulti_pipeline_pipelines.id = tblleads.pipeline_id', 'left');
    $leads = $this->db->get()->result_array();

    $grouped_leads = [];
    foreach ($leads as $lead) {
        $pipeline_id = $lead['pipeline_id'];
        $stage_id = $lead['stage_id'];
        if (!isset($grouped_leads[$pipeline_id])) {
            $grouped_leads[$pipeline_id] = [];
        }
        if (!isset($grouped_leads[$pipeline_id][$stage_id])) {
            $grouped_leads[$pipeline_id][$stage_id] = [
                'stage_id' => $stage_id,
                'stage_name' => $lead['stage_name'],
                'stage_color' => $lead['stage_color'],
                'leads' => []
            ];
        }
        $grouped_leads[$pipeline_id][$stage_id]['leads'][] = $lead;
    }

    return $grouped_leads;
}

public function get_all_stages()
{
    $this->db->select('tblmulti_pipeline_stages.*, tblmulti_pipelines.name as pipeline_name');
    $this->db->join('tblmulti_pipelines', 'tblmulti_pipelines.id = tblmulti_pipeline_stages.pipeline_id');
    return $this->db->get('tblmulti_pipeline_stages')->result_array();
}

public function get_stages_by_pipeline($pipeline_id)
{
    $this->db->select('id, name');
    $this->db->from('tblmulti_pipeline_stages');
    $this->db->where('pipeline_id', $pipeline_id);
    $this->db->order_by('order', 'ASC');
    return $this->db->get()->result_array();
}

/**
 * Adiciona um novo lead na tabela tblleads
 *
 * @param array $data Dados do lead
 * @return int|bool ID do lead inserido ou false em falha
 */
public function add_lead($data)
{
    // Preparar os dados para inserção
    $insert_data = [
        'pipeline_id'   => $data['pipeline_id'],
        'stage_id'      => $data['stage_id'],
        'status'        => $data['status'],
        'source'        => $data['source'],
        'assigned'      => !empty($data['assigned']) ? $data['assigned'] : null,
        'name'          => $data['name'],
        'title'         => $data['title'],
        'email'         => $data['email'],
        'tags'          => $data['tags'],
        'website'       => $data['website'],
        'phonenumber'   => $data['phonenumber'],
        'lead_value'    => $data['lead_value'],
        'company'       => $data['company'],
        'address'       => $data['address'],
        'city'          => $data['city'],
        'state'         => $data['state'],
        'country'       => $data['country'],
        'zip'           => $data['zip'],
        'description'   => $data['description'],
        'dateadded'     => date('Y-m-d H:i:s'),
    ];

    // Inserir os dados na tabela tblleads
    $this->db->insert('tblleads', $insert_data);
    return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : false;
}

public function get_pipeline($id)
{
    $this->db->where('id', $id);
    return $this->db->get('tblmulti_pipeline_pipelines')->row_array();
}

public function update_status($id, $data)
{
    $this->db->where('id', $id);
    return $this->db->update('tblmulti_pipeline_stages', $data);
}

public function get_status($id)
{
    $this->db->where('id', $id);
    return $this->db->get('tblmulti_pipeline_stages')->row_array();
}

public function delete_status($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('tblmulti_pipeline_stages');
}

public function get_all_statuses_with_lead_count()
{
    $this->db->select('tblmulti_pipeline_stages.*, tblmulti_pipeline_pipelines.name as pipeline_name, COUNT(tblleads.id) as lead_count');
    $this->db->from('tblmulti_pipeline_stages');
    $this->db->join('tblmulti_pipeline_pipelines', 'tblmulti_pipeline_pipelines.id = tblmulti_pipeline_stages.pipeline_id', 'left');
    $this->db->join('tblleads', 'tblleads.stage_id = tblmulti_pipeline_stages.id', 'left');
    $this->db->group_by('tblmulti_pipeline_stages.id');
    $this->db->order_by('tblmulti_pipeline_stages.pipeline_id, tblmulti_pipeline_stages.order');
    return $this->db->get()->result_array();
}

public function get_pipelines_with_lead_count()
{
    $this->db->select('p.*, COUNT(l.id) as lead_count');
    $this->db->from('tblmulti_pipeline_pipelines p');
    $this->db->join('tblleads l', 'l.pipeline_id = p.id', 'left');
    $this->db->group_by('p.id');
    return $this->db->get()->result_array();
}

public function get_all_leads()
{
    $this->db->select('tblleads.*, tblmulti_pipeline_pipelines.name as pipeline_name, tblmulti_pipeline_stages.name as stage_name');
    $this->db->from('tblleads');
    $this->db->join('tblmulti_pipeline_pipelines', 'tblleads.pipeline_id = tblmulti_pipeline_pipelines.id', 'left');
    $this->db->join('tblmulti_pipeline_stages', 'tblleads.stage_id = tblmulti_pipeline_stages.id', 'left');
    return $this->db->get()->result_array();
}

public function get_pipelines_with_stages_and_lead_count()
{
    $pipelines = $this->get_pipelines();
    foreach ($pipelines as &$pipeline) {
        $pipeline['stages'] = $this->get_pipeline_stages($pipeline['id']);
        $pipeline['lead_count'] = $this->db->where('pipeline_id', $pipeline['id'])->count_all_results('tblleads');
    }
    return $pipelines;
}

public function update_lead_pipeline_stage($lead_id, $pipeline_id, $stage_id)
{
    $this->db->where('id', $lead_id);
    $result = $this->db->update('tblleads', [
        'pipeline_id' => $pipeline_id,
        'stage_id' => $stage_id
    ]);

    return $this->db->affected_rows() > 0;
}

    /**
     * Adiciona uma nova associação de formulário
     * 
     * @param array $data
     * @return int|bool ID da associação inserida ou false em falha
     */
    public function add_form_association($data)
    {
        $this->db->insert('multi_pipeline_form_associations', $data);
        return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : false;
    }

    /**
     * Atualiza uma associação de formulário
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_form_association($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('multi_pipeline_form_associations', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Exclui uma associação de formulário
     * 
     * @param int $id
     * @return bool
     */
    public function delete_form_association($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('multi_pipeline_form_associations');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Obtém as associações de formulários
     *
     * @return array
     */
    public function get_form_associations()
    {
        $this->db->select('fa.id, f.name as form_name, p.name as pipeline_name, s.name as stage_name');
        $this->db->from('multi_pipeline_form_associations fa');
        $this->db->join('tblweb_to_lead f', 'fa.form_id = f.id', 'left');
        $this->db->join('multi_pipeline_pipelines p', 'fa.pipeline_id = p.id', 'left');
        $this->db->join('multi_pipeline_stages s', 'fa.stage_id = s.id', 'left');
        return $this->db->get()->result_array();
    }

    /**
     * Obtém os pipelines com seus estágios
     *
     * @return array
     */
    public function get_pipelines_with_stages()
    {
        $this->db->select('p.id as pipeline_id, p.name as pipeline_name, s.id as stage_id, s.name as stage_name');
        $this->db->from('multi_pipeline_pipelines p');
        $this->db->join('multi_pipeline_stages s', 'p.id = s.pipeline_id', 'left');
        $this->db->order_by('p.id, s.order', 'ASC');
        $pipelines = $this->db->get()->result_array();

        $result = [];
        foreach ($pipelines as $pipeline) {
            $result[$pipeline['pipeline_id']]['pipeline_name'] = $pipeline['pipeline_name'];
            $result[$pipeline['pipeline_id']]['stages'][] = [
                'id' => $pipeline['stage_id'],
                'name' => $pipeline['stage_name']
            ];
        }
        return $result;
    }
}