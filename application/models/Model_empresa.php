<?php
class Model_empresa extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_empresas($paramPaginate){ 
		$this->db->select("STRING_AGG(sp.nombre, ', ' ORDER BY sp.nombre ) AS especialidades, 
			ARRAY_TO_STRING( ARRAY_AGG(sp.idespecialidad ORDER BY sp.nombre),',' ) AS idespecialidades, 
			ARRAY_TO_STRING(ARRAY_AGG(es.idempresaespecialidad ORDER BY sp.nombre),',' ) AS idempresaespecialidades", FALSE);
		$this->db->select('e.idempresa, s.idsede, MAX(e.descripcion) AS empresa, MAX(e.estado_em), s.descripcion AS sede, MAX(e.descripcion) AS empresa', FALSE);
		$this->db->select('MAX(ruc_empresa) AS ruc_empresa, MAX(domicilio_fiscal) AS domicilio_fiscal, MAX(representante_legal) AS representante_legal, MAX(telefono) AS telefono', FALSE);
		$this->db->from('empresa e');
		// $this->db->join('sede s','e.idsede = s.idsede AND estado_se = 1','left');
		$this->db->join('sede s','e.idsede = s.idsede AND estado_se = 1');
		$this->db->join('empresa_especialidad es','e.idempresa = es.idempresa AND e.idsede = es.idsede AND estado_emes = 1','left');
		$this->db->join('especialidad sp','es.idespecialidad = sp.idespecialidad AND sp.estado = 1','left');
		$this->db->where('estado_em', 1); // activo
		if( $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if( !empty($value) ){
					$this->db->ilike('CAST('.$key.' AS TEXT )', $value);
				}
			}
		}
		$this->db->group_by('e.idempresa, s.idsede');
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
			$this->db->order_by('s.idsede','ASC');
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_empresas($paramPaginate)
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('empresa e');
		$this->db->join('sede s','e.idsede = s.idsede AND estado_se = 1');
		$this->db->where('estado_em', 1); // activo
		if( $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if( !empty($value) ){
					$this->db->ilike('CAST('.$key.' AS TEXT )', $value);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}
	public function m_cargar_especialidades_no_agregados_a_empresa($paramPaginate,$datos)
	{
		$sql = 'SELECT esp.idespecialidad, esp.nombre 
		FROM especialidad esp 
		LEFT JOIN empresa_especialidad emes ON esp.idespecialidad = emes.idespecialidad AND estado_emes = 1 
		LEFT JOIN empresa em ON emes.idempresa = em.idempresa AND estado_em = 1 AND em.idempresa = ? 
		WHERE esp.idespecialidad NOT IN( 
			SELECT c_esp.idespecialidad 
			FROM especialidad c_esp 
			JOIN empresa_especialidad c_emes ON c_esp.idespecialidad = c_emes.idespecialidad 
			WHERE c_emes.idempresa = ? AND c_emes.idsede = ? AND estado = 1 AND estado_emes = 1
		)
		AND estado = 1'; 
		if( $paramPaginate['search'] ){ 
			$sql.= " AND LOWER(".$paramPaginate['searchColumn'].") LIKE '%".strtolower($paramPaginate['searchText'])."%' ESCAPE '!'";
		}
		$sql .= ' GROUP BY esp.idespecialidad';
		if( $paramPaginate['sortName'] ){
			$sql.= ' ORDER BY '.$paramPaginate['sortName'].' '.$paramPaginate['sort'];
		}
		if($paramPaginate['pageSize'] ){
			$sql.= ' LIMIT '.$paramPaginate['pageSize'].' OFFSET '.$paramPaginate['firstRow'];
		}

		//var_dump($paramPaginate); 
		$query = $this->db->query($sql,array($datos['idempresa'],$datos['idempresa'],$datos['idsede'])); 
		return $query->result_array();
	}
	public function m_count_especialidades_no_agregados_a_empresa($datos)
	{
		$sql = 'SELECT COUNT(*) AS contador 
		FROM especialidad esp 
		LEFT JOIN empresa_especialidad emes ON esp.idespecialidad = emes.idespecialidad AND estado_emes = 1 
		LEFT JOIN empresa em ON emes.idempresa = em.idempresa AND estado_em = 1 AND em.idempresa = ? 
		WHERE esp.idespecialidad NOT IN( 
			SELECT c_esp.idespecialidad 
			FROM especialidad c_esp 
			JOIN empresa_especialidad c_emes ON c_esp.idespecialidad = c_emes.idespecialidad 
			WHERE c_emes.idempresa = ? AND c_emes.idsede = ? AND estado = 1 AND estado_emes = 1 
		)
		AND estado = 1 ';
		$query = $this->db->query($sql,array($datos['idempresa'],$datos['idempresa'],$datos['idsede']));
		$fEmpresa = $query->row_array();
		return $fEmpresa['contador'];
	}
	public function m_cargar_empresas_cbo($datos = FALSE){ 
		$this->db->distinct();
		$this->db->select('idempresa, descripcion, estado_em');
		$this->db->from('empresa');
		$this->db->where('estado_em', 1); // activo
		if( $datos ){
			$this->db->ilike($datos['nameColumn'], $datos['search']);
		}else{
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_validar_empresa_sede($datos)
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('empresa e');
		$this->db->join('sede s','e.idsede = s.idsede AND estado_se = 1','left');
		$this->db->where('e.idempresa', $datos['idempresa']);
		$this->db->where('s.idsede', $datos['idsede']);
		$this->db->where('estado_em', 1);
		$fData = $this->db->get()->row_array();
		return (empty($fData['contador']) ? TRUE : FALSE );
	}
	public function m_editar($datos)
	{
		$data = array(
			'descripcion' => strtoupper($datos['empresa']),
			'ruc_empresa' => $datos['ruc_empresa'],
			'domicilio_fiscal' => $datos['domicilio_fiscal'],
			'representante_legal' => $datos['representante_legal'],
			'telefono' => $datos['telefono'],
			'updatedAt' => date('Y-m-d H:i:s')
		);
		$this->db->where('idempresa',$datos['idempresa']);
		return $this->db->update('empresa', $data);
	}
	public function m_registrar($datos)
	{
		return $this->db->insert('empresa', $datos);
	}
	public function m_agregar_especialidad_empresa($datos)
	{
		$data = array(
			'idsede' => $datos['sedeId'],
			'idempresa' => $datos['empresaId'],
			'idespecialidad' => $datos['id'],
			'createdAt' => date('Y-m-d H:i:s'),
			'updatedAt' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('empresa_especialidad', $data);
	}
	public function m_quitar_especialidad_empresa($id)
	{
		$data = array(
			'estado_emes' => 0,
		);
		$this->db->where('idempresaespecialidad',$id);
		if($this->db->update('empresa_especialidad', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_anular($idSede, $idEmpresa)
	{
		$data = array(
			'estado_em' => 0
		);
		$this->db->where('idempresa',$idEmpresa);
		$this->db->where('idsede',$idSede);
		if($this->db->update('empresa', $data)){
			return true;
		}else{
			return false;
		}
	}
}