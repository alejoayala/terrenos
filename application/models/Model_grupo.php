<?php
class Model_grupo extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	//ACCESO AL SISTEMA
	public function m_cargar_grupos($paramPaginate){ 
		//$this->db->select('"grupos"."idgrupo"');
		$this->db->select("g.idgrupo,g.nombre_grupo, g.descripcion, g.estado_g, g.key_group,
			GROUP_CONCAT(DISTINCT r.descripcion_rol ORDER BY descripcion_rol SEPARATOR ',') AS roles, 
			GROUP_CONCAT(DISTINCT r.icono_rol ORDER BY descripcion_rol SEPARATOR ',' ) AS icono_roles, 
			GROUP_CONCAT(DISTINCT r.idrol ORDER BY descripcion_rol SEPARATOR ',') AS idroles, 
			GROUP_CONCAT(DISTINCT gr.idgruporol ORDER BY descripcion_rol SEPARATOR ',') AS idrolesporgrupo", FALSE);
		$this->db->from('grupos g');
		$this->db->join('grupos_roles gr','g.idgrupo = gr.idgrupo AND estado_gr = 1','left');
		$this->db->join('roles r','gr.idrol = r.idrol AND estado_r = 1','left');
		$this->db->where('estado_g', 1);
		$this->db->group_by('g.idgrupo');
		if( $paramPaginate['sortName'] ){ 
			$this->db->order_by('g.'.$paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['pageSize'] ){ 
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_cargar_grupos_cbo()
	{
		$this->db->select("idgrupo, nombre_grupo, descripcion, estado_g", FALSE);
		$this->db->from('grupos');
		$this->db->where('estado_g', 1);
		return $this->db->get()->result_array();
	}
	public function m_count_grupos()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('grupos');
		$this->db->where('estado_g', 1); // activo
		$fila = $this->db->get()->row_array();
		return $fila['contador'];
	}
	public function m_cargar_roles_no_agregados_al_grupo($paramPaginate,$datos)
	{
		$sql = 'SELECT r.idrol, r.descripcion_rol, r.url_rol, r.icono_rol 
		FROM roles r
		LEFT JOIN grupos_roles gr ON r.idrol = gr.idrol AND estado_gr = 1 
		LEFT JOIN grupos g ON gr.idgrupo = g.idgrupo AND estado_g = 1 AND g.idgrupo ='.$datos['id'].'
		WHERE r.idrol NOT IN( 
			SELECT r.idrol FROM roles r JOIN grupos_roles gr ON r.idrol = gr.idrol
			WHERE gr.idgrupo ='.$datos['id'].'  AND r.estado_r = 1 AND gr.estado_gr = 1
		)
		AND estado_r = 1';
		$sql .= ' GROUP BY r.idrol';
		if( $paramPaginate['sortName'] ){
			$sql.= ' ORDER BY '.$paramPaginate['sortName'].' '.$paramPaginate['sort'];
		}
		if($paramPaginate['pageSize'] ){
			' LIMIT '.$paramPaginate['pageSize'].' OFFSET '.$paramPaginate['firstRow'];
		}

		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function m_count_roles_no_agregados_al_grupo($datos)
	{
		$sql = 'SELECT COUNT(*) AS contador 
		FROM roles r
		LEFT JOIN grupos_roles gr ON r.idrol = gr.idrol AND gr.estado_gr = 1 
		LEFT JOIN grupos g ON gr.idgrupo = g.idgrupo AND g.estado_g = 1 AND g.idgrupo ='.$datos['id'].'
		WHERE r.idrol NOT IN( 
			SELECT r.idrol FROM roles r JOIN grupos_roles gr ON r.idrol = gr.idrol
			WHERE gr.idgrupo ='.$datos['id'].' AND r.estado_r = 1 AND gr.estado_gr = 1
		)
		AND r.estado_r = 1 GROUP BY r.idrol';
		$query = $this->db->query($sql);
		$fRol = $query->row_array();
		return $fRol['contador'];
	}
	public function m_agregar_rol_grupo($datos)
	{
		$data = array(
			'idgrupo' => $datos['groupId'],
			'idrol' => $datos['id'],
			'estado_gr' => 1
		);
		return $this->db->insert('grupos_roles', $data);
	}
	public function m_quitar_rol_grupo($id)
	{
		$data = array(
			'estado_gr' => 0,
		);
		$this->db->where('idgruporol',$id);
		if($this->db->update('grupos_roles', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_grupo' => $datos['nombre'],
			'key_group' => $datos['keygrupo'],
			'descripcion' => $datos['descripcion'],
			'estado_g' => 1

		);
		return $this->db->insert('grupos', $data);
	}
	public function m_editar($datos)
	{
		$data = array(
			'nombre_grupo' => $datos['nombre'],
			'descripcion' => $datos['descripcion']
		);
		$this->db->where('idgrupo',$datos['id']);
		return $this->db->update('grupos', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_g' => 0,
		);
		$this->db->where('idgrupo',$id);
		if($this->db->update('grupos', $data)){
			return true;
		}else{
			return false;
		}
	}
}