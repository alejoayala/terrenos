<?php
class Model_empresa_admin extends CI_Model { 
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_empresas_admin_cbo(){ 
		$this->db->select('idempresaadmin, razon_social, nombre_legal, direccion');
		$this->db->from('empresa_admin');
		$this->db->where('estado_emp <>', 0);
		return $this->db->get()->result_array();
	}
	public function m_cargar_sede_empresas_admin_cbo()
	{
		$this->db->select('idsedeempresaadmin, razon_social, s.descripcion');
		$this->db->from('empresa_admin ea');
		$this->db->join('sede_empresa_admin sea','ea.idempresaadmin = sea.idempresaadmin');
		$this->db->join('sede s','sea.idsede = s.idsede');
		$this->db->where('estado_emp <>', 0);
		$this->db->where('estado_sea <>', 0);
		$this->db->where('estado_se', 1);
		return $this->db->get()->result_array();
	}
	public function m_cargar_esta_sede_empresa_admin($id)
	{
		$this->db->select('idsedeempresaadmin, ea.idempresaadmin, razon_social, s.descripcion');
		$this->db->from('empresa_admin ea');
		$this->db->join('sede_empresa_admin sea','ea.idempresaadmin = sea.idempresaadmin');
		$this->db->join('sede s','sea.idsede = s.idsede');
		$this->db->where('idsedeempresaadmin', $id);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	// ==========================================
	// OBTENER TODAS LAS EMPRESAS ADMIN
	// ==========================================
	public function m_cargar_empresas_admin($paramPaginate){ 
		//$this->db->select('idprecio, nombre, descripcion, porcentaje, tipo_precio, estado_emp');
		$this->db->from('empresa_admin');
		$this->db->where('estado_emp', 1); // activo
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	// ==========================================
	// CANTIDAD DE REGISTROS EN EMPRESAS ADMIN
	// ==========================================
	public function m_count_empresas_admin()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('empresa_admin');
		$this->db->where('estado_emp', 1); // activo
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}
	// ==========================================
	// CRUD
	// ==========================================
	public function m_editar($datos)
	{
		$data = array(
			'razon_social' => $datos['razon_social'],
			'nombre_legal' => $datos['nombre_legal'],
			'domicilio_fiscal' => $datos['domicilio_fiscal'],
			'direccion' => $datos['direccion'],
			'ruc' => $datos['ruc'],
			'nombre_logo' => $datos['nombre_logo'],
			'rs_facebook' => $datos['rs_facebook'],
			'rs_twitter' => $datos['rs_twitter'],
			'rs_youtube' => $datos['rs_youtube']
		);
		$this->db->where('idempresaadmin',$datos['id']);
		return $this->db->update('empresa_admin', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'razon_social' => $datos['razon_social'],
			'nombre_legal' => $datos['nombre_legal'],
			'domicilio_fiscal' => $datos['domicilio_fiscal'],
			'direccion' => $datos['direccion'],
			'ruc' => $datos['ruc'],
			'nombre_logo' => $datos['nombre_logo'],
			'rs_facebook' => $datos['rs_facebook'],
			'rs_twitter' => $datos['rs_twitter'],
			'rs_youtube' => $datos['rs_youtube']
		);
		return $this->db->insert('empresa_admin', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_emp' => 0
		);
		$this->db->where('idempresaadmin',$id);
		if($this->db->update('empresa_admin', $data)){
			return true;
		}else{
			return false;
		}
	}
}