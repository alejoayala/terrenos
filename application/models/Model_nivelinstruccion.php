<?php
class Model_nivelinstruccion extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_nivel_instruccion_cbo($datos=FALSE)
	{
		$this->db->select('idnivelinstruccion, nombre_nivelinstruccion');
		$this->db->from('nivel_instruccion');
		if( $datos ){ 
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}



}