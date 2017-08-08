<?php
class Model_afp extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_afp_cbo($datos=FALSE)
	{
		$this->db->select('idafp, nombre_afp');
		$this->db->from('afp');
		if( $datos ){ 
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_cargar_afp($paramPaginate){
		//$this->db->select('idtipoExamen, descripcion, estado_tex');
		$this->db->from('afp');
		$this->db->where('estado_afp <>', 0);
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
	public function m_count_afp()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('afp');
		$this->db->where('estado_afp <>', 0);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}

	public function m_editar($datos)
	{
		$data = array(
			'nombre_afp' => strtoupper($datos['nombre_afp'])
		);
		$this->db->where('idafp',$datos['id']);
		return $this->db->update('afp', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_afp' => strtoupper($datos['nombre_afp']),
			'estado_afp' => 1,
		);
		return $this->db->insert('afp', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_afp' => 0
		);
		$this->db->where('idafp',$id);
		if($this->db->update('afp', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_habilitar($id)
	{
		$data = array(
			'estado_afp' => 1
		);
		$this->db->where('idafp',$id);
		if($this->db->update('afp', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_deshabilitar($id)
	{
		$data = array(
			'estado_afp' => 2
		);
		$this->db->where('idafp',$id);
		if($this->db->update('afp', $data)){
			return true;
		}else{
			return false;
		}
	}



}