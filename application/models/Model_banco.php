<?php
class Model_banco extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_banco_cbo($datos=FALSE)
	{
		$this->db->select('idbanco, nombre_banco');
		$this->db->from('bancos');
		if( $datos ){ 
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_cargar_banco($paramPaginate){
		//$this->db->select('idtipoExamen, descripcion, estado_tex');
		$this->db->from('bancos');
		$this->db->where('estado_ba <>', 0);
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
	public function m_count_banco()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('bancos');
		$this->db->where('estado_ba <>', 0);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}

	public function m_editar($datos)
	{
		$data = array(
			'nombre_banco' => strtoupper($datos['nombre_banco'])
		);
		$this->db->where('idbanco',$datos['id']);
		return $this->db->update('bancos', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_banco' => strtoupper($datos['nombre_banco']),
			'estado_ba' => 1,
		);
		return $this->db->insert('bancos', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_ba' => 0
		);
		$this->db->where('idbanco',$id);
		if($this->db->update('bancos', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_habilitar($id)
	{
		$data = array(
			'estado_ba' => 1
		);
		$this->db->where('idbanco',$id);
		if($this->db->update('bancos', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_deshabilitar($id)
	{
		$data = array(
			'estado_ba' => 2
		);
		$this->db->where('idbanco',$id);
		if($this->db->update('bancos', $data)){
			return true;
		}else{
			return false;
		}
	}



}