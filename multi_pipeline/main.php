<?php
// Caminho: /public_html/modules/multi_pipeline/main.php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Multi Pipeline Module - Main entry point
 *
 * This file handles the installation, configuration, and initialization
 * of the Multi Pipeline module.
 */

/**
 * Register activation module hook
 */
register_activation_hook('multi_pipeline', 'multi_pipeline_activation_hook');

/**
 * Register deactivation module hook
 */
register_deactivation_hook('multi_pipeline', 'multi_pipeline_deactivation_hook');

/**
 * Register uninstall module hook
 */
register_uninstall_hook('multi_pipeline', 'multi_pipeline_uninstall_hook');

/**
 * Multi Pipeline module activation function
 *
 * This function is called when the module is activated
 *
 * @return void
 */
function multi_pipeline_activation_hook()
{
    require_once(__DIR__ . '/install.php');
    multi_pipeline_install();
    create_triggers();
    if (check_triggers_exist()) {
        log_message('info', 'Multi Pipeline module activated and triggers created successfully');
    } else {
        log_message('error', 'Multi Pipeline module activated but triggers creation failed');
    }
}

register_activation_hook(MODULE_PATH . 'multi_pipeline', 'multi_pipeline_activation_hook');

/**
 * Create triggers function
 *
 * This function creates the necessary triggers for the module
 *
 * @return void
 */
function create_triggers()
{
    $CI =& get_instance();
    create_lead_insert_trigger($CI);
    create_lead_status_update_trigger($CI);
}

/**
 * Create lead insert trigger function
 *
 * This function creates the after_lead_insert trigger
 *
 * @param object $CI CodeIgniter instance
 * @return void
 */
function create_lead_insert_trigger($CI)
{
    $sql = "CREATE TRIGGER after_lead_insert AFTER INSERT ON " . db_prefix() . "leads
            FOR EACH ROW
            BEGIN
                DECLARE default_pipeline_id INT;
                DECLARE default_stage_id INT;
                SET default_pipeline_id = (SELECT id FROM " . db_prefix() . "multi_pipeline_pipelines ORDER BY id ASC LIMIT 1);
                SET default_stage_id = (SELECT id FROM " . db_prefix() . "multi_pipeline_stages WHERE pipeline_id = default_pipeline_id ORDER BY `order` ASC LIMIT 1);
                INSERT INTO " . db_prefix() . "multi_pipeline_leads (perfex_lead_id, pipeline_id, stage_id)
                VALUES (NEW.id, default_pipeline_id, default_stage_id);
            END;";
    if ($CI->db->query($sql) === FALSE) {
        log_activity('Failed to create after_lead_insert trigger: ' . $CI->db->error()['message'], null, 'error');
    } else {
        log_activity('after_lead_insert trigger created or updated successfully');
    }
}

/**
 * Create lead status update trigger function
 *
 * This function creates the after_stage_update trigger
 *
 * @param object $CI CodeIgniter instance
 * @return void
 */
function create_lead_status_update_trigger($CI)
{
    $CI->db->query("DROP TRIGGER IF EXISTS after_stage_update");
    $sql = "CREATE TRIGGER after_stage_update AFTER UPDATE ON " . db_prefix() . "leads
            FOR EACH ROW
            BEGIN
                IF OLD.status != NEW.status THEN
                    INSERT INTO " . db_prefix() . "multi_pipeline_stage_transitions 
                    (lead_id, from_stage_id, to_stage_id, created_at)
                    VALUES (NEW.id, OLD.status, NEW.status, NOW());
                    
                    UPDATE " . db_prefix() . "multi_pipeline_leads
                    SET stage_id = NEW.status
                    WHERE perfex_lead_id = NEW.id;
                END IF;
            END;";
    
    if ($CI->db->query($sql) === FALSE) {
        log_activity('Failed to create after_stage_update trigger: ' . $CI->db->error()['message'], null, 'error');
    } else {
        log_activity('after_stage_update trigger created successfully');
    }
}

/**
 * Check if triggers exist
 *
 * This function checks if the required triggers exist in the database
 *
 * @return bool
 */
function check_triggers_exist()
{
    $CI =& get_instance();
    $triggers = ['after_lead_insert', 'after_stage_update'];
    foreach ($triggers as $trigger) {
        $result = $CI->db->query("SHOW TRIGGERS WHERE `Trigger` = '$trigger'")->result();
        if (empty($result)) {
            log_activity("Trigger $trigger does not exist", null, 'error');
            return false;
        }
    }
    return true;
}

/**
 * Multi Pipeline module deactivation function
 *
 * This function is called when the module is deactivated
 *
 * @return void
 */
function multi_pipeline_deactivation_hook()
{
    // Add any deactivation logic here, if needed
}

/**
 * Multi Pipeline module uninstall function
 *
 * This function is called when the module is uninstalled
 *
 * @return void
 */
function multi_pipeline_uninstall_hook()
{
    require_once(__DIR__ . '/uninstall.php');
}