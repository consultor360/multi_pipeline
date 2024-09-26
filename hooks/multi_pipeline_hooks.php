<?php
// Caminho: /public_html/modules/multi_pipeline/hooks/multi_pipeline_hooks.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Multi Pipeline Module Hooks
 */

/**
 * Register necessary hooks for the Multi Pipeline module
 */
hooks()->add_action('admin_init', 'multi_pipeline_module_init_menu_items');
hooks()->add_action('leads_status_changed', 'multi_pipeline_update_lead_status');
hooks()->add_action('lead_created', 'multi_pipeline_assign_lead_to_pipeline');

/**
 * Initialize Multi Pipeline menu items
 *
 * @return void
 */
function multi_pipeline_module_init_menu_items()
{
    $CI = &get_instance();
    
    if (has_permission('multi_pipeline', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('multi_pipeline', [
            'name'     => _l('multi_pipeline'),
            'href'     => admin_url('multi_pipeline'),
            'position' => 30,
            'icon'     => 'fa fa-sitemap',
        ]);
    }
}

$config['hooks'] = array(
    // ...
    'post_module_install' => array(
        // ...
        'multi_pipeline:multi_pipeline_activation_hook'
    )
);

/**
 * Update lead status in Multi Pipeline when changed in Perfex CRM
 *
 * @param int $lead_id
 * @param array $data
 * @return void
 */
function multi_pipeline_update_lead_status($lead_id, $data)
{
    $CI = &get_instance();
    $CI->load->model('multi_pipeline_model');
    $CI->multi_pipeline_model->update_lead_status($lead_id, $data['status']);
}

/**
 * Assign new lead to appropriate pipeline based on form ID
 *
 * @param int $lead_id
 * @return void
 */
function multi_pipeline_assign_lead_to_pipeline($lead_id)
{
    $CI = &get_instance();
    $CI->load->model('multi_pipeline_model');
    $CI->multi_pipeline_model->assign_lead_to_pipeline($lead_id);
}