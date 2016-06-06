<?php
class Model_estadocivil extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_estado_civil_cbo($datos=FALSE)
	{
		$this->db->select('idestadocivil, nombre_estadocivil');
		$this->db->from('estado_civil');
		if( $datos ){ 
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}



}