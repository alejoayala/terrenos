<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresa extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security'));
		$this->load->model(array('model_empresa'));
		//cache 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function lista_empresas()
	{
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_empresa->m_cargar_empresas($paramPaginate);
		$totalRows = $this->model_empresa->m_count_empresas($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) {
			$arrIdEmpresaPorEspec = explode(",",$row['idempresaespecialidades']);
			$arrIdEspecialidad = explode(",",$row['idespecialidades']);
			$arrEspecialidades = explode(",",$row['especialidades']);
			$arrDetalle = array();
			foreach ($arrIdEmpresaPorEspec as $key => $value) { 
				if(!empty($arrIdEmpresaPorEspec[$key])){
					array_push($arrDetalle, 
						array(
							'idEmpresaPorEspec' => $arrIdEmpresaPorEspec[$key], // problema no jala el ID verdadero aqui me quede
							'idEspecialidad' => $arrIdEspecialidad[$key],
							'especialidad' => strtoupper($arrEspecialidades[$key])
						)
					);
				}
			}
			array_push($arrListado, 
				array(
					'idsede' => $row['idsede'],
					'idempresa' => $row['idempresa'],
					'sede' => $row['sede'],
					'empresa' => $row['empresa'],
					'ruc_empresa' => $row['ruc_empresa'],
					'domicilio_fiscal' => $row['domicilio_fiscal'],
					'representante_legal' => $row['representante_legal'],
					'telefono' => $row['telefono'],
					'especialidades' => $arrDetalle
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $totalRows;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function lista_empresas_cbo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$allInputs['nameColumn'] = (empty($allInputs['nameColumn']) ? 'descripcion' : $allInputs['nameColumn'] );
		if( isset($allInputs['search']) ){
			$lista = $this->model_empresa->m_cargar_empresas_cbo($allInputs);
		}else{
			$lista = $this->model_empresa->m_cargar_empresas_cbo();
		}
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idempresa'],
					'descripcion' => $row['descripcion']
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function lista_especialidades_no_agregados_a_empresa()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$datos = $allInputs['datos'];
		$listaEspecialidadesNoAgregados = $this->model_empresa->m_cargar_especialidades_no_agregados_a_empresa($paramPaginate,$datos);
		$totalRows = $this->model_empresa->m_count_especialidades_no_agregados_a_empresa($datos);
		$arrListado = array();
		foreach ($listaEspecialidadesNoAgregados as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idespecialidad'],
					'nombre' => $row['nombre']
				)
			);
		}
		$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $totalRows;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($listaEspecialidadesNoAgregados)){ 
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function ver_popup_formulario()
	{
		$this->load->view('empresa/empresa_formView');
	}
	public function ver_popup_agregar_especialidad()
	{
		$this->load->view('empresa/popupAgregarEspecialidadView');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// BUSCAR EMPRESAS CON EL MISMO NOMBRE 
		$allInputs['search'] = $allInputs['empresa'];
		$allInputs['nameColumn'] = 'descripcion';
		$listaEmpresa = $this->model_empresa->m_cargar_empresas_cbo($allInputs);
		if( !empty($listaEmpresa) ){
			$data['idempresa'] = $listaEmpresa[0]['idempresa'];
		}

		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;

    	$data['idsede'] = $allInputs['idsede'];
		$data['descripcion'] = strtoupper($allInputs['empresa']);
		$data['ruc_empresa'] = $allInputs['ruc_empresa'];
		$data['domicilio_fiscal'] = $allInputs['domicilio_fiscal'];
		$data['representante_legal'] = $allInputs['representante_legal'];
		$data['telefono'] = $allInputs['telefono'];
		$data['createdAt'] = date('Y-m-d H:i:s');
		$data['updatedAt'] = date('Y-m-d H:i:s');

		// BUSCAR EMPRESA Y SEDE QUE COINCIDAN 
		if( !empty($data['idempresa']) ){
			if( !$this->model_empresa->m_validar_empresa_sede($data) ){
				$arrData['message'] = 'Sede y/o empresa ya existentes.';
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
			}
		}
		
		if($this->model_empresa->m_registrar($data)){
			$arrData['message'] = 'Se registraron los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function agregar_especialidad()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$this->db->trans_start();
    	foreach ($allInputs['especialidades'] as $row) { 
    		$row['empresaId'] = $allInputs['empresaId'];
    		$row['sedeId'] = $allInputs['sedeId'];
    		if($this->model_empresa->m_agregar_especialidad_empresa($row)){ 
				$arrData['message'] = 'Se registraron los datos correctamente';
	    		$arrData['flag'] = 1;
			}
    	}
    	$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function quitar_especialidad_de_empresa()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al anular los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		if($this->model_empresa->m_quitar_especialidad_empresa($allInputs['idEmpresaPorEspec'])) { 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		if($this->model_empresa->m_editar($allInputs)){
			$arrData['message'] = 'Se editaron los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo anular los datos';
    	$arrData['flag'] = 0;
    	foreach ($allInputs as $row) {
			if( $this->model_empresa->m_anular($row['idsede'],$row['idempresa']) ){
				$arrData['message'] = 'Se anularon los datos correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}