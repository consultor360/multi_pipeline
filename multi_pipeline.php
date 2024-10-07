<?php
// Caminho: /public_html/modules/multi_pipeline/multi_pipeline.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Name: Multi Pipeline
 * Description: Adiciona funcionalidades de múltiplos pipelines ao Perfex CRM
 * Version: 1.0.0
 * Requires at least: 2.3.*
 */

// Define o nome do módulo
define('MULTI_PIPELINE_MODULE_NAME', 'multi_pipeline');

$CI =& get_instance();
$CI->config->load('multi_pipeline/multi_pipeline', TRUE, TRUE);

// Adiciona a ação admin_init para inicializar itens de menu e permissões
hooks()->add_action('admin_init', 'init_multi_pipeline');

// Registrar gancho de ativação do módulo
register_activation_hook(MULTI_PIPELINE_MODULE_NAME, 'multi_pipeline_activation_hook');

function multi_pipeline_activation_hook()
{
    $CI = &get_instance();
    $CI->load->config('multi_pipeline', true);
    
    require_once(__DIR__ . '/install.php');
    multi_pipeline_install();
}

// Registrar gancho de desativação do módulo
register_deactivation_hook(MULTI_PIPELINE_MODULE_NAME, 'multi_pipeline_deactivation_hook');

function multi_pipeline_deactivation_hook()
{
    $uninstall_file = __DIR__ . '/uninstall.php';
    if (file_exists($uninstall_file)) {
        require_once($uninstall_file);
    }
}

function get_edit_pipeline_url($id) {
    return site_url('multi_pipeline/pipelines/edit/'. $id);
}

/**
 * Função para inicializar o módulo Multi Pipeline
 */
function init_multi_pipeline() {
    multi_pipeline_init_menu_items();
    multi_pipeline_permissions();
}

/**
 * Inicializa itens de menu para o multi pipeline
 */
function multi_pipeline_init_menu_items() {
    $CI = &get_instance();
    
    if (!has_permission('multi_pipeline', '', 'view')) {
        return;
    }

    // Get the pipeline ID from somewhere (e.g. database, session, etc.)
    $id = 1; // Replace with the actual pipeline ID

    // Menu principal
    $CI->app_menu->add_sidebar_menu_item('multi-pipeline', [
        'name'     => _l('multi_pipeline'),
        'href'     => '#',
        'icon'     => 'fa fa-sitemap',
        'position' => 5,
    ]);

    // Submenus
    $submenus = [
        [
            'slug'     => 'multi-pipeline-overview',
            'name'     => _l('Multi Pipeline'),
            'href'     => admin_url('multi_pipeline'),
            'icon'     => 'fa fa-tasks',
        ],
        [
            'slug'     => 'create-pipeline',
            'name'     => _l('Create Pipeline'),
            'href'     => admin_url('multi_pipeline/create_pipeline'),
            'icon'     => 'fa fa-plus',
        ],
        [
            'slug'     => 'view-pipelines',
            'name'     => _l('View Pipelines'),
            'href'     => admin_url('multi_pipeline/pipelines'),
            'icon'     => 'fa fa-eye',
        ],
        [
            'slug'     => 'multi_pipeline_summary',
            'name'     => _l('lead summary'),
            'href'     => admin_url('multi_pipeline/summary'),
            'icon'     => 'fa fa-chart-bar',
        ],
        /*
        [
            'slug'     => 'multi_pipeline_add_lead',
            'name'     => _l('add_new_lead'),
            'href'     => admin_url('multi_pipeline/add_lead'),
            'icon'     => 'fa fa-plus',
        ],
        [
            'slug'     => 'form-associations',
            'name'     => _l('Form Associations'),
            'href'     => admin_url('multi_pipeline/form_associations'),
            'icon'     => 'fa fa-link',
        ],
        */
        [
            'slug'     => 'create-status',
            'name'     => _l('Create Status'),
            'href'     => admin_url('multi_pipeline/status/create'),
            'icon'     => 'fa fa-plus-circle',
        ],
        [
            'slug'     => 'view-statuses',
            'name'     => _l('View Statuses'),
            'href'     => admin_url('multi_pipeline/status'),
            'icon'     => 'fa fa-list',
        ],
    ];

    foreach ($submenus as $index => $submenu) {
        $submenu['position'] = $index + 1;
        $CI->app_menu->add_sidebar_children_item('multi-pipeline', $submenu);
    }
}

/**
 * Registra permissões para o multi pipeline
 */
function multi_pipeline_permissions() {
    $capabilities = [
        'capabilities' => [
            'view'   => _l('permission_view') . ' (' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ],
    ];

    register_staff_capabilities('multi_pipeline', $capabilities, _l('multi_pipeline'));
}

// Nota: A chamada direta para init_multi_pipeline() foi removida para evitar problemas de segurança