<?php
// Caminho: /public_html/modules/multi_pipeline/views/pipelines_table.php


defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'description',
    'status',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'pipelines';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'status') {
            $_data = (_l($aRow['status']));
        } elseif ($aColumns[$i] == 'name') {
            $_data = '<a href="' . admin_url('multi_pipeline/view/' . $aRow['id']) . '">' . $_data . '</a>';
        }
        $row[] = $_data;
    }

    $options = icon_btn('multi_pipeline/edit/' . $aRow['id'], 'pencil-square-o', 'btn-default', ['title' => _l('edit')]);
    $options .= icon_btn('multi_pipeline/delete/' . $aRow['id'], 'remove', 'btn-danger _delete', ['title' => _l('delete')]);
    
    $row[] = $options;

    $output['aaData'][] = $row;
}