<?php
// Caminho: /public_html/modules/multi_pipeline/uninstall.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Uninstalls the multi-pipeline system
 *
 * @return void
 */
function multi_pipeline_uninstall()
{
    $CI = &get_instance();
    $CI->load->dbforge();

    remove_tables($CI);
    remove_trigger($CI);
    remove_options();
    remove_permissions($CI);
    remove_categories_table($CI);
}

/**
 * Removes all tables related to the multi-pipeline system
 *
 * @param CI_DB_driver $CI CodeIgniter database instance
 * @return void
 */
function remove_tables($CI)
{
    $tables = [
        'multi_pipeline_pipelines',
        'multi_pipeline_stages',
        'multi_pipeline_leads',
        'multi_pipeline_lead_activities',
        'multi_pipeline_lead_notes',
        'multi_pipeline_stage_transitions',
        'multi_pipeline_categories'
    ];

    foreach ($tables as $table) {
        $CI->dbforge->drop_table(db_prefix() . $table, TRUE);
    }
}

/**
 * Removes the after_lead_insert trigger
 *
 * @param CI_DB_driver $CI CodeIgniter database instance
 * @return void
 */
function remove_trigger($CI)
{
    $CI->db->query("DROP TRIGGER IF EXISTS `after_lead_insert`");
    $CI->db->query("DROP TRIGGER IF EXISTS `after_stage_update`");
}

/**
 * Removes options related to the multi-pipeline system
 *
 * @return void
 */
function remove_options()
{
    delete_option('multi_pipeline_enabled');
    delete_option('multi_pipeline_version');
}

/**
 * Removes permissions related to the multi-pipeline system
 *
 * @param CI_DB_driver $CI CodeIgniter database instance
 * @return void
 */
function remove_permissions($CI)
{
    $CI->load->model('roles_model');
    $permissions = [
        'multi_pipeline_view',
        'multi_pipeline_create',
        'multi_pipeline_edit',
        'multi_pipeline_delete',
        'multi_pipeline_manage_stages',
        'multi_pipeline_manage_pipelines'
    ];

    foreach ($permissions as $permission) {
        $CI->roles_model->remove_permission('multi_pipeline', $permission);
    }
}

/**
 * Removes the categories table
 *
 * @param CI_DB_driver $CI CodeIgniter database instance
 * @return void
 */
function remove_categories_table($CI)
{
    $CI->dbforge->drop_table(db_prefix() . 'multi_pipeline_categories', TRUE);
}

?>