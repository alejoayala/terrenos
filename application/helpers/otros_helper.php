<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function GetLastId($campoId,$table){
    $ci2 =& get_instance();
    $ci2->db->select('MAX('.$campoId.') AS id',FALSE);
    $ci2->db->from($table);
    $fData = $ci2->db->get()->row_array();
    return $fData['id'];
}