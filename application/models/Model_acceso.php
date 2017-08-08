<?php
class Model_acceso extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	//ACCESO AL SISTEMA
	public function m_logging_user($data){ 
		//$this->db->select(' (SELECT key_group FROM "grupo" g WHERE estado_g = 1 LIMIT 1) AS key_grupo',FALSE);
		$this->db->select('COUNT(*) AS logged, idusuario AS id',FALSE);
		$this->db->from('usuarios');
		$this->db->where('nombre_usuario', $data['usuario']);
		$this->db->where('password', do_hash($data['clave'] , 'md5'));
		$this->db->where('estado_u <>', '0');
		$this->db->group_by('idusuario');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_listar_perfil_usuario($userId, $sedeId = FALSE ) 
	{
		$this->db->select('u.idusuario,u.nombre_usuario, u.fec_creacion, 
			g.nombre_grupo AS grupo, g.idgrupo, 
			g.key_group',FALSE);
		$this->db->from('usuarios u');
		//$this->db->join('empresa e','s.idsede = e.idsede'); 
		$this->db->join('grupos g','u.idgrupo = g.idgrupo');
		$this->db->where('u.idusuario', $userId);
		$this->db->where('u.estado_u <>', '0');
		// $this->db->where('estado_em <>', '0');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}

	/* 
		AL LOGEARTE COMO USUARIO SALUD 
		LA VARIABLE SESSION "idsedeempresaadmin" sirve para vincular la atencion a la SEDE-EMPRESA 
		LA VARIABLE SESSION "idempresamedico" sirve para vincular la atencion a la EMPRESA-ESPECIALIDAD 
	*/ 
	public function m_listar_perfil_usuario_salud( $userId, $empresaMedico = FALSE )
	{
		$this->db->select('u.idusers, ip_address, username, email, 
			s.idsede, e.idempresa, s.descripcion AS sede, e.descripcion AS empresa, "g"."name" AS grupo, "g"."idgroup", 
			es.idespecialidad, es.nombre AS especialidad, te.descripcion AS tipoEspecialidad, 
			ea.idempresaadmin, razon_social, nombre_legal, ruc, nombre_logo, nombre_foto, sea.idsedeempresaadmin, 
			em.nombres, apellido_paterno, apellido_materno, key_group, med.idmedico, med_nombres, med_apellido_paterno, med_apellido_materno, 
			ee.idempresaespecialidad, eme.idempresamedico',FALSE);
		$this->db->from('users u');
		$this->db->join('users_por_sede ups','u.idusers = ups.idusers');
		$this->db->join('sede s','ups.idsede = s.idsede');
		
		/* SOLO USUARIO SALUD */ 
		$this->db->join('medico med','u.idusers = med.iduser');
		$this->db->join('empresa_medico eme','med.idmedico = eme.idmedico');
		$this->db->join('empresa_especialidad ee','eme.idempresaespecialidad = ee.idempresaespecialidad 
			AND eme.idsede = ee.idsede AND eme.idempresa = ee.idempresa AND eme.idespecialidad = ee.idespecialidad'); 
		$this->db->join('empresa e','ee.idempresa = e.idempresa AND ee.idsede = e.idsede'); 
		$this->db->join('especialidad es','ee.idespecialidad = es.idespecialidad AND es.estado <> 0');
		$this->db->join('tipo_especialidad te','es.idtipoespecialidad = te.idtipoespecialidad');

		$this->db->join('rh_empleado em','u.idusers = em.iduser');
		$this->db->join('sede_empresa_admin sea','s.idsede = sea.idsede');
		$this->db->join('empresa_admin ea','sea.idempresaadmin = ea.idempresaadmin');
		$this->db->join('users_groups ug','u.idusers = ug.idusers');
		$this->db->join('group g','ug.idgroup = g.idgroup');
		$this->db->where('u.idusers', $userId);
		$this->db->where('sea.estado_sea', 1); // empresa por sede activa 
		if( $empresaMedico ){
			$this->db->where('eme.idempresamedico', $empresaMedico);
		}
		$this->db->where('estado_ups <>', '0');
		$this->db->where('estado_usuario <>', '0');
		$this->db->where('estado_se <>', '0');
		$this->db->where('estado_em <>', '0');
		//$this->db->where('es.estado <>', '0');
		//$this->db->where('ea.estado <>', '0');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
}
?>