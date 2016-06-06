<?php
class Model_seccion extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_seccion_cbo($datos=FALSE)
	{
		$this->db->select('idseccion, nombre_seccion');
		$this->db->from('secciones');
		if( $datos ){ 
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_cargar_seccion($paramPaginate){
		//$this->db->select('idtipoExamen, descripcion, estado_tex');
		$this->db->from('secciones');
		$this->db->where('estado_se <>', 0);
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
	public function m_count_seccion()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('secciones');
		$this->db->where('estado_se <>', 0);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}

	public function m_editar($datos)
	{
		$data = array(
			'nombre_seccion' => strtoupper($datos['nombre_seccion'])
		);
		$this->db->where('idseccion',$datos['id']);
		return $this->db->update('secciones', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_seccion' => strtoupper($datos['nombre_seccion']),
			'estado_se' => 1,
		);
		return $this->db->insert('secciones', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_se' => 0
		);
		$this->db->where('idseccion',$id);
		if($this->db->update('secciones', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_habilitar($id)
	{
		$data = array(
			'estado_se' => 1
		);
		$this->db->where('idseccion',$id);
		if($this->db->update('secciones', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_deshabilitar($id)
	{
		$data = array(
			'estado_se' => 2
		);
		$this->db->where('idseccion',$id);
		if($this->db->update('secciones', $data)){
			return true;
		}else{
			return false;
		}
	}



}