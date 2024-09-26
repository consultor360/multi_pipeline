<?php
// Caminho: /public_html/modules/multi_pipeline/helpers/multi_pipeline_helper.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Retorna o nome do pipeline
 *
 * @param int $pipeline_id
 * @return string
 */
function get_pipeline_name($pipeline_id)
{
    $CI =& get_instance();
    $CI->load->model('multi_pipeline_model');
    $pipeline = $CI->multi_pipeline_model->get_pipeline($pipeline_id);
    return $pipeline ? $pipeline->name : '';
}

/**
 * Retorna a lista de estágios de um pipeline
 *
 * @param int $pipeline_id
 * @return array
 */
function get_pipeline_stages($pipeline_id)
{
    $CI =& get_instance();
    $CI->load->model('multi_pipeline_model');
    return $CI->multi_pipeline_model->get_pipeline_stages($pipeline_id);
}

/**
 * Verifica se o usuário tem permissão para acessar o pipeline
 *
 * @param int $pipeline_id
 * @return bool
 */
function has_pipeline_permission($pipeline_id)
{
    $CI =& get_instance();
    $CI->load->model('staff_model');
    $staff = $CI->staff_model->get(get_staff_user_id());
    
    // Implementar lógica de verificação de permissão
    // Por exemplo, verificar se o usuário tem a role adequada ou está associado ao pipeline
    
    return true; // Temporariamente retornando true para todos os usuários
}

// Fim do arquivo