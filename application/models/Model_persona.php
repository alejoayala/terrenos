<?php
class Model_persona extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_persona_cbo($datos=FALSE)
	{
		$this->db->select('idpersona, CONCAT(nombres," ",apellido_paterno," ",apellido_materno) as cliente');
		$this->db->from('persona');
		$this->db->where('activo <>',0);
		if( $datos ){ 
			//$this->db->like('LOWER('.$datos['nameColumn'].')', strtolower($datos['search']));
			$this->db->like('LOWER(CONCAT(nombres," ",apellido_paterno," ",apellido_materno))', strtolower($datos['search']));
		}else{ 
			$this->db->limit(100);
		}
		return $this->db->get()->result_array();
	}
	public function m_cargar_persona($paramPaginate){
		$this->db->select('p.*,ec.nombre_estadocivil as estado_civil,ni.nombre_nivelinstruccion as nivel_instruccion,u.nombre_usuario as usuario');
		$this->db->from('persona p');
		$this->db->join('estado_civil ec','ec.idestadocivil = p.idestadocivil','left'); 
		$this->db->join('nivel_instruccion ni','ni.idnivelinstruccion = p.idnivelinstruccion','left'); 
		$this->db->join('usuarios u','u.idusuario = p.idusuario'); 
		$this->db->where('p.activo <>', 0);
		$this->db->where('p.condicion', 0);
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
	public function m_count_persona($paramPaginate)
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('persona p');
		$this->db->join('estado_civil ec','ec.idestadocivil = p.idestadocivil','left'); 
		$this->db->join('nivel_instruccion i','i.idnivelinstruccion = p.idnivelinstruccion','left'); 
		$this->db->join('usuarios u','u.idusuario = p.idusuario'); 
		$this->db->where('p.activo <>', 0);
		$this->db->where('p.condicion', 0);
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

	public function m_editar($datos)
	{
		$data = array(
			'idpersona' => $datos['idpersona'],
			'credencial' => strtoupper($datos['credencial']),
			'nombres' => strtoupper($datos['nombres']),
			'apellido_paterno' => strtoupper($datos['apellido_paterno']),
			'apellido_materno' => strtoupper($datos['apellido_materno']),
			'nombre_completo' => strtoupper($datos['nombres'].' '.$datos['apellido_paterno'].' '.$datos['apellido_materno']),
			'domicilio_actual' =>(empty($datos['domicilio_actual']) ? NULL : $datos['domicilio_actual']), 
			'dni' =>(empty($datos['dni']) ? NULL : $datos['dni']),
			'telefono' => (empty($datos['telefono']) ? NULL : $datos['telefono']),
			'celular' => (empty($datos['celular']) ? NULL : $datos['celular']),
			'fec_nacimiento' => date('Y-m-d H:i:s',strtotime($datos['fec_nacimiento'])),
			'lugar_nacimiento' => (empty($datos['lugar_nacimiento']) ? NULL : $datos['lugar_nacimiento']),
			'idubigeo' => $datos['idubigeo'],
			'ocupacion' => $datos['ocupacion'],
			'grado_instruccion' => $datos['grado_instruccion'],
			'estado_civil' => $datos['estado_civil'],
			'sexo' => $datos['sexo'],
			'profesion' => $datos['profesion'],
			'foto' => $datos['foto'],
			'condicion' => $datos['condicion'],
			'idtitular' => $datos['idtitular'],
			'idusuario' => $datos['idusuario'],
		);
		$this->db->where('idpersona',$datos['id']);
		return $this->db->update('persona', $data);
	}
	public function m_registrar($datos)
	{
		$data = array(
			//'idusuario' => $datos['idusuario'],
			'credencial' => strtoupper($datos['credencial']),
			'nombres' => strtoupper($datos['nombres']),
			'apellido_paterno' => strtoupper($datos['apellido_paterno']),
			'apellido_materno' => strtoupper($datos['apellido_materno']),
			'nombre_completo' => strtoupper($datos['nombres'].' '.$datos['apellido_paterno'].' '.$datos['apellido_materno']),
			'domicilio_actual' =>(empty($datos['domicilio_actual']) ? NULL : $datos['domicilio_actual']), 
			'dni' =>(empty($datos['dni']) ? NULL : $datos['dni']),
			'telefono' => (empty($datos['telefono']) ? NULL : $datos['telefono']),
			'celular' => (empty($datos['celular']) ? NULL : $datos['celular']),
			'fec_nacimiento' => date('Y-m-d H:i:s',strtotime($datos['fec_nacimiento'])),
			'lugar_nacimiento' => (empty($datos['lugar_nacimiento']) ? NULL : $datos['lugar_nacimiento']),
			'idubigeo' => $datos['idubigeo'],
			'ocupacion' => $datos['ocupacion'],
			'grado_instruccion' => $datos['grado_instruccion'],
			'estado_civil' => $datos['estado_civil'],
			'sexo' => $datos['sexo'],
			'profesion' => $datos['profesion'],
			'foto' => $datos['foto'],
			'condicion' => $datos['condicion'],
			'idtitular' => $datos['idtitular'],
			'idusuario' => $datos['idusuario'],
			'activo' => 1
		);
		return $this->db->insert('persona', $data);
	}
	public function m_anular($id)
	{
		$data = array(
			'activo' => 0
		);
		$this->db->where('idpersona',$id);
		if($this->db->update('persona', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_habilitar($id)
	{
		$data = array(
			'activo' => 1
		);
		$this->db->where('idpersona',$id);
		if($this->db->update('persona', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_deshabilitar($id)
	{
		$data = array(
			'activo' => 2
		);
		$this->db->where('idpersona',$id);
		if($this->db->update('persona', $data)){
			return true;
		}else{
			return false;
		}
	}
	public function m_cargar_familiares_no_agregados_a_persona($paramPaginate,$datos)
	{
		$sql = 'SELECT f.idfamiliar,CONCAT(f.nombres," ",f.apellido_paterno," ",f.apellido_materno) AS familiar 
		FROM familiares f 
		LEFT JOIN familiares_empleados fe on fe.idfamiliar=f.idfamiliar 
		LEFT JOIN persona p on fe.idpersona=p.idpersona
		WHERE f.estado_fa = 1 and fe.idfamiliar is null and f.idfamiliar NOT in (select f.idfamiliar from familiares f 
		INNER JOIN familiares_empleados fe on fe.idfamiliar=f.idfamiliar 
		INNER JOIN empleados e on e.idempleado=fe.idempleado where e.idempleado='.$datos['id'].')';
		if( $paramPaginate['search'] ){ 
			$sql.= " AND CONCAT(f.nombres,' ',f.apellido_paterno,' ',f.apellido_materno) LIKE '%".strtoupper($paramPaginate['searchText'])."%'";
		}
		//$sql .= ' GROUP BY emes.idempresaespecialidad, esp.idespecialidad,s.idsede,s.descripcion,em.idempresa,em.descripcion';
		if( $paramPaginate['sortName'] ){
			$sql.= ' ORDER BY '.$paramPaginate['sortName'].' '.$paramPaginate['sort'];
		}
		if($paramPaginate['pageSize'] ){
			$sql.= ' LIMIT '.$paramPaginate['pageSize'].' OFFSET '.$paramPaginate['firstRow'];
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function m_count_familiares_no_agregados_a_empleado($datos)
	{
		$sql = 'SELECT COUNT(*) AS contador 
		FROM familiares f 
		LEFT JOIN familiares_empleados fe on fe.idfamiliar=f.idfamiliar 
		LEFT JOIN empleados e on fe.idempleado=e.idempleado
		WHERE f.estado_fa = 1 and fe.idfamiliar is null and f.idfamiliar NOT in (select f.idfamiliar from familiares f 
		INNER JOIN familiares_empleados fe on fe.idfamiliar=f.idfamiliar 
		INNER JOIN empleados e on e.idempleado=fe.idempleado where e.idempleado='.$datos['id'].')';
		$query = $this->db->query($sql);
		$fEmpresa = $query->row_array();
		return $fEmpresa['contador'];
	}

	public function m_cargar_familiares_del_empleado($paramPaginate,$datos)
	{
		$sql = 'SELECT f.idfamiliar,CONCAT(f.nombres," ",f.apellido_paterno," ",f.apellido_materno) AS familiar ,fe.tipo_consanguineo 
		FROM familiares f 
		INNER JOIN familiares_empleados fe on fe.idfamiliar=f.idfamiliar 
		INNER JOIN empleados e on fe.idempleado=e.idempleado
		WHERE f.estado_fa = 1 and e.idempleado='.$datos['id'].' AND fe.estado_faem=1';
		if( $paramPaginate['search'] ){ 
			$sql.= " AND CONCAT(f.nombres,' ',f.apellido_paterno,' ',f.apellido_materno) LIKE '%".strtoupper($paramPaginate['searchText'])."%'";
		}
		//$sql .= ' GROUP BY emes.idempresaespecialidad, esp.idespecialidad,s.idsede,s.descripcion,em.idempresa,em.descripcion';
		if( $paramPaginate['sortName'] ){
			$sql.= ' ORDER BY '.$paramPaginate['sortName'].' '.$paramPaginate['sort'];
		}
		if($paramPaginate['pageSize'] ){
			$sql.= ' LIMIT '.$paramPaginate['pageSize'].' OFFSET '.$paramPaginate['firstRow'];
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function m_count_familiares_del_empleado($datos)
	{
		$sql = 'SELECT COUNT(*) AS contador 
		FROM familiares f 
		INNER JOIN familiares_empleados fe on fe.idfamiliar=f.idfamiliar 
		INNER JOIN empleados e on fe.idempleado=e.idempleado
		WHERE f.estado_fa = 1 and e.idempleado='.$datos['id'];
		$query = $this->db->query($sql);
		$fEmpresa = $query->row_array();
		return $fEmpresa['contador'];
	}
	public function m_agregar_familiar_empleado($datos,$ParamFam)
	{
		$data = array(
			'idempleado' => $datos['idempleado'],
			'idfamiliar' => $ParamFam[0]['id'],
			'tipo_consanguineo' => $datos['tipo_consanguineo'],
			'estado_faem' => 1,
		);
		return $this->db->insert('familiares_empleados', $data);
	}
	public function m_anular_familiar_empleado($id)
	{
		$data = array(
			'estado_faem' => 0
		);
		$this->db->where('idfamiliarempleado',$id);
		if($this->db->update('familiares_empleados', $data)){
			return true;
		}else{
			return false;
		}
	}

/*************** CONTRATOS ********************/

	public function m_cargar_contrato_empleado($paramPaginate){
		$this->db->select("co.*,e.dni as dni,CONCAT(e.nombres,' ',e.apellido_paterno,' ',e.apellido_materno) as empleado, b.nombre_banco as banco,a.nombre_afp as afp,ca.nombre_cargo as cargo,se.nombre_seccion as seccion");
		$this->db->from('contratos co');
		$this->db->join('bancos b','b.idbanco = co.idbanco'); 
		$this->db->join('afp a','a.idafp = co.idafp'); 
		$this->db->join('cargos ca','co.idcargo = ca.idcargo'); 
		$this->db->join('secciones se','se.idseccion = co.idseccion'); 
		$this->db->join('empleados e','e.idempleado = co.idempleado'); 
		$this->db->where('co.estado_co <>', 0);
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
	public function m_count_contrato_empleado()
	{
		$this->db->select('COUNT(*) AS contador',FALSE);
		$this->db->from('contratos');
		$this->db->where('estado_co <>', 0);
		$fData = $this->db->get()->row_array();
		return $fData['contador'];
	}
	public function m_cargar_empleado_por_dni($datos)
	{
		$this->db->select("* , CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno)as empleado");
		$this->db->from('empleados');
		$this->db->where('estado_em <>', 0);
		$this->db->where('dni',$datos['dni']);

		return $this->db->get()->result_array();

	}
	public function m_registrar_contrato($datos)
	{
		$data = array(
			'idempleado' => $datos['idempleado'],
			'fecha_inicio' => date('Y-m-d H:i:s',strtotime($datos['fecha_inicio'])),
			'fecha_final' => date('Y-m-d H:i:s',strtotime($datos['fecha_final'])),
			'idcargo' => strtoupper($datos['idcargo']),
			'idseccion' => strtoupper($datos['idseccion']),
			'idafp' => strtoupper($datos['idafp']),
			'idbanco' =>$datos['idbanco'],
			'numero_cuenta' => $datos['numero_cuenta'] ,
			'monto_salario' => $datos['monto_salario'] ,
			'observaciones' => (empty($datos['observaciones']) ? null : $datos['observaciones']) ,
			'estado_asegurado' =>$datos['estado_asegurado'] , 
			'estado_co' => 1,
		);
		return $this->db->insert('contratos', $data);

	}
	public function m_editar_contrato($datos)
	{
		$data = array(
			'idempleado' => $datos['idempleado'],
			'fecha_inicio' => date('Y-m-d H:i:s',strtotime($datos['fecha_inicio'])),
			'fecha_final' => date('Y-m-d H:i:s',strtotime($datos['fecha_final'])),
			'idcargo' => strtoupper($datos['idcargo']),
			'idseccion' => strtoupper($datos['idseccion']),
			'idafp' => strtoupper($datos['idafp']),
			'idbanco' =>$datos['idbanco'],
			'numero_cuenta' => $datos['numero_cuenta'] ,
			'monto_salario' => $datos['monto_salario'] ,
			'observaciones' => (empty($datos['observaciones']) ? null : $datos['observaciones']) ,
			'estado_asegurado' =>$datos['estado_asegurado'] , 
			'estado_co' => 1,
		);
		//return $this->db->insert('contratos', $data);
		$this->db->where('idcontrato',$datos['id']);
		return $this->db->update('contratos', $data);

	}
	public function m_anular_contrato($id)
	{
		$data = array(
			'estado_co' => 0
		);
		$this->db->where('idcontrato',$id);
		if($this->db->update('contratos', $data)){
			return true;
		}else{
			return false;
		}
	}


	public function m_cargar_este_empleado_por_codigo($datos)
	{
		$this->db->select("idempleado, dni, CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno) AS empleado");
		$this->db->from('empleados');
		$this->db->where('idempleado', $datos['id']);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}


}