<?php
// Caminho: /public_html/modules/multi_pipeline/install.php

defined('BASEPATH') or exit('No direct script access allowed');

function multi_pipeline_install()
{
    $CI = &get_instance();
    $CI->load->dbforge();
    
    $CI->db->query('SET FOREIGN_KEY_CHECKS=0');
    
    $permissions = [
        ['name' => 'Multi Pipeline View', 'hortname' => 'multi_pipeline_view'],
        ['name' => 'Multi Pipeline Create', 'hortname' => 'multi_pipeline_create'],
        ['name' => 'Multi Pipeline Edit', 'hortname' => 'multi_pipeline_edit'],
        ['name' => 'Multi Pipeline Delete', 'hortname' => 'multi_pipeline_delete'],
        ['name' => 'Multi Pipeline Manage Stages', 'hortname' => 'multi_pipeline_manage_stages'],
        ['name' => 'Multi Pipeline Manage Pipelines', 'hortname' => 'multi_pipeline_manage_pipelines'],
    ];

    foreach ($permissions as $permission) {
        if (!$CI->db->where('hortname', $permission['hortname'])->get(db_prefix() . 'permissions')->num_rows()) {
            $CI->db->insert(db_prefix() . 'permissions', $permission);
        }
    }

    create_pipelines_table($CI);
    create_stages_table($CI);
    add_stage_and_pipeline_id_columns_to_leads_table($CI);
    create_lead_activities_table($CI);
    create_lead_notes_table($CI);
    create_stage_transitions_table($CI);
    create_categories_table($CI);
    create_statuses_table($CI);
    
    // Adicionar colunas extras
    add_order_column_to_stages_table($CI);

    $CI->db->query('SET FOREIGN_KEY_CHECKS=1');
    
    add_menu_options($CI);
}

function create_pipelines_table($CI)
{
    $CI->dbforge->add_field([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ],
        'name' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE
        ],
        'description' => [
            'type' => 'TEXT',
            'null' => TRUE
        ],
        'status' => [
            'type' => 'TINYINT',
            'constraint' => 1,
            'default' => 1
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => FALSE,
            'default' => date('Y-m-d H:i:s')
        ],
        'updated_at' => [
            'type' => 'DATETIME',
            'null' => TRUE
        ]
    ]);
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->create_table('multi_pipeline_pipelines', TRUE);
}

function create_stages_table($CI)
{
    $fields = array(
        'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'name' => array(
            'type' => 'VARCHAR',
            'constraint' => 255
        ),
        'pipeline_name' => array(
            'type' => 'VARCHAR',
            'constraint' => 255
        ),
        'pipeline_id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        ),
        'color' => array( // Adicione essa linha
            'type' => 'VARCHAR',
            'constraint' => 255
        ),
        // ...
    );

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->create_table('multi_pipeline_stages', TRUE); // Create table if it doesn't exist
}

function add_order_column_to_stages_table($CI)
{
    $fields = $CI->db->list_fields('multi_pipeline_stages');
    if (!in_array('order', $fields)) {
        $CI->dbforge->add_column('multi_pipeline_stages', array(
            'order' => array(
                'type' => 'INT',
                'constraint' => 11
            )
        ));
    }
}

function add_stage_and_pipeline_id_columns_to_leads_table($CI)
{
    $fields = array(
        'stage_id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        ),
        'pipeline_id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        )
    );

    // Check if the columns already exist
    $columns = $CI->db->list_fields('leads');

    foreach ($fields as $field => $attributes) {
        if (!in_array($field, $columns)) {
            $CI->dbforge->add_column('leads', array($field => $attributes));
        }
    }
}

function create_lead_activities_table($CI)
{
    $fields = array(
        'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'lead_id' => array( // Adicione essa linha
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        ),
        // ...
    );

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('lead_id');
    $CI->dbforge->create_table('multi_pipeline_lead_activities', TRUE); // Create table if it doesn't exist
}

function create_lead_notes_table($CI)
{
    $fields = array(
        'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'lead_id' => array( // Adicione essa linha
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        ),
        // ...
    );

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('lead_id');
    $CI->dbforge->create_table('multi_pipeline_lead_notes', TRUE); // Create table if it doesn't exist
}

function create_categories_table($CI)
{
    $fields = array(
        'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'name' => array(
            'type' => 'VARCHAR',
            'constraint' => 255
        ),
        'description' => array(
            'type' => 'TEXT'
        )
    );

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->create_table('multi_pipeline_categories', TRUE); // Create table if it doesn't exist
}

function create_stage_transitions_table($CI)
{
    $fields = array(
        'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'lead_id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        ),
        'from_stage_id' => array( // Adicione essa linha
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        ),
        'to_stage_id' => array( // Adicione essa linha
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE
        ),
        'created_at' => array(
            'type' => 'DATETIME'
        ),
        // ...
    );

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key(['lead_id', 'from_stage_id', 'to_stage_id']);
    $CI->dbforge->create_table('multi_pipeline_stage_transitions', TRUE); // Create table if it doesn't exist
}

function create_statuses_table($CI)
{
    $fields = array(
        'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        ),
        'name' => array(
            'type' => 'VARCHAR',
            'constraint' => 255
        ),
        'color' => array(
            'type' => 'VARCHAR',
            'constraint' => 255
        )
    );

    $CI->dbforge->add_field($fields);
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->create_table('multi_pipeline_statuses', TRUE);
}

function add_menu_options($CI)
{
    $menuOptions = [
        [
            'name' => 'multi_pipeline_pipelines',
            'url' => 'multi_pipeline/pipelines',
            'permission' => 'pipelines',
            'icon' => 'fa fa-bars',
        ],
        [
            'name' => 'multi_pipeline_stages',
            'url' => 'multi_pipeline/stages',
            'permission' => 'stages',
            'icon' => 'fa fa-step-forward',
        ],
        [
            'name' => 'multi_pipeline_leads',
            'url' => 'multi_pipeline/leads',
            'permission' => 'leads',
            'icon' => 'fa fa-lead',
        ],
    ];

    foreach ($menuOptions as $option) {
        add_option($option['name'], json_encode($option));
    }
    
}
function add_color_column_to_stages_table($CI)
{
    $CI->dbforge->add_column('multi_pipeline_stages', array(
        'color' => array(
            'type' => 'VARCHAR',
            'constraint' => 255
        )
    ));
}