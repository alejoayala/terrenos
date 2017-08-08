<?php
class Model_familiar extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_familiar_cbo($datos=FALSE)
	{
		$this->db->select('idfamiliar, CONCAT(nombres," ",apellido_paterno," ",apellido_materno');
		$this->db->from('familiares');
		if( $datos ){ 
			$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_cargar_familiar($paramPaginate){
		$this->db->select('f.idfamiliar,f.dni,f.nombres,f.apellido_paterno,f.apellido_materno,f.fecha_nacimiento,e.nombre_estadocivil as estado_civil,i.nombre_nivelinstruccion as nivel_instruccion,f.idestadocivil,f.idnivelinstruccion,f.estado_fa');
		$this->db->from('familiares f');
		$this->db->join('estado_civil e','f.idestadocivil = e.idestadocivil'); 
		$this->db->join('nivel_instruccion i','i.idnivelinstruccion = f.idnivelinstruccion'); 
		$this->db->where('estado_fa <>', 0);
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
	public function m_count_familiar()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('familiares');
		$this->db->where('estado_fa <>', 0);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}

	public function m_editar($datos)
	{
		$data = array(
			'nombres' => strtoupper($datos['nombres']),
			'apellido_paterno' => strtoupper($datos['apellido_paterno']),
			'apellido_materno' => strtoupper($datos['apellido_materno']),
			'dni' => $datos['dni'],
			'fecha_nacimiento' => date('Y-m-d H:i:s',strtotime($datos['fecha_nacimiento'])),
			'idestadocivil' => $datos['idestadocivil'],
			'idnivelinstruccion' => $datos['idnivelinstruccion'],
			'estado_fa' => 1,
		);
		$this->db->where('idfamiliar',$datos['id']);
		return $this->db->update('familiares', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombres' => strtoupper($datos['nombres']),
			'apellido_paterno' => strtoupper($datos['apellido_paterno']),
			'apellido_materno' => strtoupper($datos['apellido_materno']),
			'dni' => $datos['dni'],
			'fecha_nacimiento' => date('Y-m-d H:i:s',strtotime($datos['fecha_nacimiento'])),
			'idestadocivil' => $datos['idestadocivil'],
			'idnivelinstruccion' => $datos['idnivelinstruccion'],
			'estado_fa' => 1,
		);
		return $this->db->insert('familiares', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_fa' => 0
		);
		$this->db->where('idfamiliar',$id);
		if($this->db->update('familiares', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_habilitar($id)
	{
		$data = array(
			'estado_fa' => 1
		);
		$this->db->where('idfamiliar',$id);
		if($this->db->update('familiares', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_deshabilitar($id)
	{
		$data = array(
			'estado_fa' => 2
		);
		$this->db->where('idfamiliar',$id);
		if($this->db->update('familiares', $data)){
			return true;
		}else{
			return false;
		}
	}



}