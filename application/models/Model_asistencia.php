<?php
class Model_asistencia extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_asistencia($paramPaginate){
		$this->db->select('co.*,CONCAT(e.nombres," ",e.apellido_paterno," ",e.apellido_materno) as empleado');
		$this->db->from('control_asistencia co');
		$this->db->join('empleados e','co.idempleado=e.idempleado');
		$this->db->where('estado_co <>', 0);
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
	public function m_cargar_asistencia_por_empleado($datos,$paramPaginate){
		$this->db->select('co.*,CONCAT(nombres," ",apellido_paterno," ",apellido_materno) as empleado');
		$this->db->from('control_asistencia');
		$this->db->where('idempleado',$datos['idempleado']);
		$this->db->where('estado_co <>', 0);
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
	public function m_count_asistencia()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('control_asistencia');
		$this->db->where('estado_co <>', 0);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}
	public function m_count_asistencia_por_empleado($datos)
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('secciones');
		$this->db->where('estado_se <>', 0);
		$this->db->where('idempleado',$datos['idempleado']);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}

	public function m_registrar($datos)
	{
		$data = array(
			'idempleado' => $datos['idempleado'],
			'fecha' => date('Y-m-d H:i:s',strtotime($datos['fecha'])),
			'hora' => $datos['hora'],
			'tipo' => $datos['tipo'],
			'estado_co' => 1,
		);
		return $this->db->insert('control_asistencia', $data);
	}



}