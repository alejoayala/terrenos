<?php
class Model_config extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_empresa_activa(){ 
		$this->db->select('idempresaadmin, razon_social, nombre_legal, domicilio_fiscal, 
			direccion, ruc, nombre_logo, estado_emp, rs_facebook, rs_twitter, rs_youtube',FALSE);
		$this->db->from('empresa_admin');
		$this->db->where('estado_emp', 1); // activo
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_empresa_sede_activa(){ 
		$this->db->select('idsedeempresaadmin, s.idsede,',FALSE);
		$this->db->from('empresa_admin ea');
		$this->db->join('sede_empresa_admin sea','ea.idempresaadmin = sea.idempresaadmin');
		$this->db->join('sede s','sea.idsede = s.idsede');
		$this->db->where('estado_sea', 1); // activo
		$this->db->where('sea.idsede', $this->sessionHospital['idsede']); // activo 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
}