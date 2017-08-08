<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpresaAdmin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper'));
		$this->load->model(array('model_empresa_admin'));
		//cache 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function lista_empresa_admin_cbo()
	{
		$lista = $this->model_empresa_admin->m_cargar_empresas_admin_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idempresaadmin'],
					'descripcion' => $row['razon_social']
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
	public function lista_sede_empresa_admin_cbo()
	{
		$lista = $this->model_empresa_admin->m_cargar_sede_empresas_admin_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idsedeempresaadmin'],
					'descripcion' => strtoupper($row['razon_social']).' - '.strtoupper($row['descripcion'])
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

	// ==========================================
	// LISTADO DE MEDIOS DE PAGO
	// ==========================================
	public function lista_empresa_admin()
	{
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_empresa_admin->m_cargar_empresas_admin($paramPaginate);
		$totalRows = $this->model_empresa_admin->m_count_empresas_admin();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idempresaadmin'],
					'razon_social' => $row['razon_social'],
					'nombre_legal' => $row['nombre_legal'],
					'domicilio_fiscal' => $row['domicilio_fiscal'],
					'direccion' => $row['direccion'],
					'ruc' => $row['ruc'],
					'nombre_logo' => $row['nombre_logo'],
					'rs_facebook' => $row['rs_facebook'],
					'rs_twitter' => $row['rs_twitter'],
					'rs_youtube' => $row['rs_youtube'],
					'redes_sociales' => array(
						'facebook' => $row['rs_facebook'],
						'twitter' => $row['rs_twitter'],
						'youtube' => $row['rs_youtube']
					),
					
					'estado' => $row['estado_emp']
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
	// ==========================================
	// CRUD
	// ==========================================
	public function ver_popup_formulario()
	{
		$this->load->view('empresaAdmin/empresaadmin_formView');
	}	
	public function registrar()
	{
		// var_dump(  ); exit(); 
		$allInputs['razon_social'] = $this->input->post('razon_social');
		$allInputs['nombre_legal'] = $this->input->post('nombre_legal');
		$allInputs['domicilio_fiscal'] = $this->input->post('domicilio_fiscal');
		$allInputs['direccion'] = $this->input->post('direccion');
		$allInputs['ruc'] = $this->input->post('ruc');
		$allInputs['rs_facebook'] = $this->input->post('rs_facebook');
		$allInputs['rs_twitter'] = $this->input->post('rs_twitter');
		$allInputs['rs_youtube'] = $this->input->post('rs_youtube');
				
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente'; 
    	$arrData['flag'] = 0; 
    	$this->db->trans_start();
    	if( empty($_FILES) ){
			$allInputs['nombre_logo'] = 'noimage.png'; 
			if($this->model_empresa_admin->m_registrar($allInputs)){ 
				
				$arrData['message'] = 'Se registraron los datos correctamente'; 
    			$arrData['flag'] = 1; 
			}
		}else{
			if( subir_fichero('assets/img/dinamic/empresa','fotoEmpresa') ){ 
				$allInputs['nombre_logo'] = $_FILES['fotoEmpresa']['name']; 
				if($this->model_empresa_admin->m_registrar($allInputs)){ 
					
					$arrData['message'] = 'Se registraron los datos correctamente'; 
	    			$arrData['flag'] = 1; 
				}
			}
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function editar()
	{	
		$allInputs['id'] = $this->input->post('id');
		$allInputs['razon_social'] = $this->input->post('razon_social');
		$allInputs['nombre_legal'] = $this->input->post('nombre_legal');
		$allInputs['domicilio_fiscal'] = $this->input->post('domicilio_fiscal');
		$allInputs['direccion'] = $this->input->post('direccion');
		$allInputs['ruc'] = $this->input->post('ruc');
		$allInputs['rs_facebook'] = $this->input->post('rs_facebook');
		$allInputs['rs_twitter'] = $this->input->post('rs_twitter');
		$allInputs['rs_youtube'] = $this->input->post('rs_youtube');
				
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente'; 
    	$arrData['flag'] = 0; 
    	//$this->db->trans_start();
    	if( empty($_FILES) ){
			$allInputs['nombre_logo'] = $this->input->post('nombre_logo');
			if($this->model_empresa_admin->m_editar($allInputs)){ 
				
				$arrData['message'] = 'Se registraron los datos correctamente'; 
    			$arrData['flag'] = 1; 
			}
		}else{
			if( subir_fichero('assets/img/dinamic/empresa','fotoEmpresa') ){ 
				$allInputs['nombre_logo'] = $_FILES['fotoEmpresa']['name']; 
				if($this->model_empresa_admin->m_editar($allInputs)){ 
					
					$arrData['message'] = 'Se registraron los datos correctamente'; 
	    			$arrData['flag'] = 1; 
				}
			}
		}
		//$this->db->trans_complete();
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
			if( $this->model_empresa_admin->m_anular($row['id']) ){
				$arrData['message'] = 'Se anularon los datos correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}