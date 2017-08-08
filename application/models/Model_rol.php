<?php
class Model_rol extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	} 
	public function m_cargar_roles($paramPaginate){ 
		$this->db->select('idrol, descripcion_rol, url_rol, icono_rol, estado_r');
		$this->db->from('roles');
		$this->db->where('estado_r', 1); // activo
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_roles()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('roles');
		$this->db->where('estado_r', 1); // activo
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}
	public function m_cargar_roles_session($idgroup){ 
		$this->db->select('r.idrol, r.descripcion_rol, r.url_rol, r.icono_rol,r.idparent,r.orden');
		$this->db->from('roles r');
		$this->db->join('grupos_roles gr', 'r.idrol = gr.idrol');
		$this->db->join('grupos g', 'gr.idgrupo = g.idgrupo');
		$this->db->where('r.estado_r', 1); // activo
		$this->db->where('gr.estado_gr', 1); // activo
		$this->db->where('r.idparent', null);
		$this->db->where('gr.idgrupo', $idgroup); // activo
		$this->db->order_by('r.idrol', 'ASC');
		return $this->db->get()->result_array();
	}
	public function m_cargar_roles_children($idrol,$datos){
		$idgroup = $datos['idgrupo'];
		
		$this->db->select('descripcion_rol as label, url_rol as url');
		$this->db->from('roles');
		$this->db->join('grupos_roles gr', 'roles.idrol = gr.idrol');
		$this->db->join('grupos', 'gr.idgrupo = grupos.idgrupo');
		$this->db->where('roles.estado_r', 1); // activo
		$this->db->where('gr.estado_gr', 1); // activo
		// $this->db->where_not_in('idparent', null);
		$this->db->where('gr.idgrupo', $idgroup); // activo
		$this->db->where('idparent', $idrol);

		if(isset($datos['idespecialidad'])){
			if ($datos['idespecialidad'] == 28){
				$this->db->where_not_in('rol.idrol', 52);
			}else{
				$this->db->where_not_in('rol.idrol', 53);
			}
		} ;

		$this->db->order_by('roles.idrol', 'ASC');
		return $this->db->get()->result_array();
	}


	public function m_editar($datos)
	{
		$data = array(
			'descripcion_rol' => $datos['descripcion'],
			'url_rol' => $datos['url'],
			'icono_rol' => $datos['icono']
		);
		$this->db->where('idrol',$datos['id']);
		return $this->db->update('roles', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			'descripcion_rol' => $datos['descripcion'],
			'url_rol' => $datos['url'],
			'icono_rol' => $datos['icono'],
			'estado_r' => 1
		);
		return $this->db->insert('roles', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'estado_r' => 0,
		);
		$this->db->where('idrol',$id);
		if($this->db->update('roles', $data)){
			return true;
		}else{
			return false;
		}
	}
}