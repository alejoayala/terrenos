<?php
class Model_usuario extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	//ACCESO AL SISTEMA
	public function m_cargar_usuarios($paramPaginate){ 
		//$this->db->distinct();
		$this->db->select('u.idusuario, u.nombre_usuario, u.password, u.fec_creacion, MAX(idgrupousuario) AS idgrupousuario, MAX(g.idgrupo) AS idgrupo, u.estado_u ,(g.nombre_grupo) as name');
		$this->db->from('usuarios u');
		$this->db->join('grupos_usuarios gu','u.idusuario = gu.idusuario','left');
		$this->db->join('grupos g','u.idgrupo = g.idgrupo','left');
		$this->db->where('estado_g', 1); // activo
		$this->db->group_by('u.idusuario');
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
		if( $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow']);
		}
		return $this->db->get()->result_array();
	}
	public function m_count_usuarios($paramPaginate)
	{

		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('usuarios u');
		//$this->db->join('users_groups ug','u.idusers = ug.idusers','left');
		$this->db->join('grupos g','u.idgrupo = g.idgrupo','left');
		$this->db->where('estado_g', 1); // activo
		$this->db->group_by('u.idusuario');
		if( $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if( !empty($value) ){
					$this->db->ilike('CAST('.$key.' AS TEXT )', $value);
				}
			}
		}
		
		$filas = $this->db->get()->num_rows();
		return $filas;
	}
	public function m_cargar_usuarios_cbo($datos = FALSE) // SOLO USUARIOS QUE FALTAN ASIGNAR 
	{
		$this->db->select('u.idusuario, nombre_usuario');
		$this->db->from('usuarios u');
		$this->db->join('empleados e','u.idusuario = e.idusuario','left');
		$this->db->where('estado_u', 1);
		$this->db->where('idempleado IS NULL');
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
			'nombre_usuario' => $datos['usuario']
		);
		$this->db->where('idusuario',$datos['idusuario']);
		return $this->db->update('usuarios', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_usuario' => $datos['usuario'], 
			'fec_creacion' => date('Y-m-d H:i:s'), 
			'password' => do_hash($datos['clave'],'md5'), 
			'idgrupo' => $datos['grupoId'],
			'estado_u' => 1
		);

		return $this->db->insert('usuarios', $data);
		//$this->db->insert('users', $data);
		//return $this->db->insert_id();
	}
	public function m_editar_detalle($datos)
	{
		$data = array(
			'idgrupo' => $datos['grupoId']
		);
		$this->db->where('idgrupousuario',$datos['iddetalle']);
		return $this->db->update('grupos_usuarios', $data);
	}
	public function m_registrar_detalle($datos)
	{
		$data = array(
			'idusuario' => $datos['id'],
			'idgrupo' => $datos['grupoId'],
			'estado_gu' => 1
		);
		return $this->db->insert('grupos_usuarios', $data);
	}
	public function m_deshabilitar($id)
	{
		$data = array(
			'estado_u' => 0,
		);
		$this->db->where('idusuario',$id);
		if($this->db->update('usuarios', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_habilitar($id)
	{
		$data = array(
			'estado_u' => 1,
		);
		$this->db->where('idusuario',$id);
		if($this->db->update('usuarios', $data)){
			return true;
		}else{
			return false;
		}
	}
}