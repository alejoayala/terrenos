<?php
class Model_sede extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	//ACCESO AL SISTEMA
	public function m_cargar_sedes($paramPaginate){ 
		$this->db->select('idsede, descripcion, estado_se');
		$this->db->from('sede');
		$this->db->where('estado_se', 1); // activo
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_sedes()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('sede');
		$this->db->where('estado_se', 1); // activo
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}
	public function m_cargar_sedes_cbo($datos = FALSE){ 
		$this->db->select('idsede, descripcion, estado_se');
		$this->db->from('sede');
		$this->db->where('estado_se', 1); // activo
		if( $datos ){
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_editar($datos)
	{
		$data = array(
			'descripcion' => strtoupper($datos['descripcion']),
			'updatedAt' => date('Y-m-d H:i:s')
		);
		$this->db->where('idsede',$datos['id']);
		return $this->db->update('sede', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'descripcion' => strtoupper($datos['descripcion']),
			'createdAt' => date('Y-m-d H:i:s'),
			'updatedAt' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('sede', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_se' => 0
		);
		$this->db->where('idsede',$id);
		if($this->db->update('sede', $data)){
			return true;
		}else{
			return false;
		}
	}
}