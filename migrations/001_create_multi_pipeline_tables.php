<?php
// Caminho: /public_html/modules/multi_pipeline/migrations/001_create_multi_pipeline_tables.php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_multi_pipeline_tables {
    protected $ci;
    protected $dbforge;

    public function __construct() {
        $this->ci =& get_instance();
        $this->dbforge = $this->ci->dbforge;
    }

    public function up()
    {
        $this->dbforge->drop_table('tblstages', TRUE);
        $this->dbforge->drop_table('tblpipelines', TRUE);

        // Criação da tabela de pipelines
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'status' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
                'default' => date('Y-m-d H:i:s')
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('name');
        $this->dbforge->create_table('tblpipelines', TRUE);

        // Criação da tabela de estágios
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'pipeline_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'order' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'default' => 0
            ),
            'color' => array(
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => FALSE,
                'default' => '#000000'
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
                'default' => date('Y-m-d H:i:s')
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('pipeline_id');
        $this->dbforge->add_key('order');
        $this->dbforge->create_table('tblstages', TRUE);

        // Adição de chave estrangeira
        $this->ci->db->query('ALTER TABLE tblstages ADD CONSTRAINT fk_stage_pipeline FOREIGN KEY (pipeline_id) REFERENCES tblpipelines(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Remoção da chave estrangeira
        $this->ci->db->query('ALTER TABLE tblstages DROP FOREIGN KEY fk_stage_pipeline');

        // Remoção das tabelas
        $this->dbforge->drop_table('tblstages', TRUE);
        $this->dbforge->drop_table('tblpipelines', TRUE);
    }
}