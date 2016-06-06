<?php
class Model_cargo extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cargo_cbo($datos=FALSE)
	{
		$this->db->select('idcargo, nombre_cargo');
		$this->db->from('cargos');
		if( $datos ){ 
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_cargar_cargo($paramPaginate){
		//$this->db->select('idtipoExamen, descripcion, estado_tex');
		$this->db->from('cargos');
		$this->db->where('estado_ca <>', 0);
		if( $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if( !empty($value) ){
					$this->db->ilike('CAST('.$key.' AS TEXT )', $value);
				}
			}
		}
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_cargo()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('cargos');
		$this->db->where('estado_ca <>', 0);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}

	public function m_editar($datos)
	{
		$data = array(
			'nombre_cargo' => strtoupper($datos['nombre_cargo'])
		);
		$this->db->where('idcargo',$datos['id']);
		return $this->db->update('cargos', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_cargo' => strtoupper($datos['nombre_cargo']),
			'estado_ca' => 1,
		);
		return $this->db->insert('cargos', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_ca' => 0
		);
		$this->db->where('idcargo',$id);
		if($this->db->update('cargos', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_habilitar($id)
	{
		$data = array(
			'estado_ca' => 1
		);
		$this->db->where('idcargo',$id);
		if($this->db->update('cargos', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_deshabilitar($id)
	{
		$data = array(
			'estado_ca' => 2
		);
		$this->db->where('idcargo',$id);
		if($this->db->update('cargos', $data)){
			return true;
		}else{
			return false;
		}
	}



}