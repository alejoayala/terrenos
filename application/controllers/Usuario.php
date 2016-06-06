<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','otros_helper'));
		$this->load->model(array('model_usuario'));
		//cache 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		$this->sessionHospital = @$this->session->userdata('sess_vs_'.substr(base_url(),-8,7));
		date_default_timezone_set("America/Lima"); //var_dump($this->user);
	}
	public function lista_usuarios()
	{ 
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_usuario->m_cargar_usuarios($paramPaginate);
		$totalRows = $this->model_usuario->m_count_usuarios($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			$estadoUsuario = ($row['estado_u'] == 1 ? 'HABILITADO':'DESHABILITADO');
			$claseEstado = ($row['estado_u'] == 1 ? 'label-success':'label-default');
			$arrDetalle = array();
			array_push($arrListado, 
				array(
					'idusuario' => $row['idusuario'],
					'iddetalle' => $row['idgrupousuario'],
					'groupId' => $row['idgrupo'],
					'usuario' => $row['nombre_usuario'],
					'fec_creacion' => $row['fec_creacion'],
					'grupo' => $row['name'],
					'estado' => array(
						'string' => $estadoUsuario,
						'clase' =>$claseEstado,
						'bool' =>$row['estado_u']
					),
					'sedes' => $arrDetalle
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
	public function lista_usuario_cbo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		if( isset($allInputs['search']) ){
			$listaCbo = $this->model_usuario->m_cargar_usuarios_cbo($allInputs);
		}else{
			$listaCbo = $this->model_usuario->m_cargar_usuarios_cbo();
		}
		$arrListado = array();
		foreach ($listaCbo as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idusuario'],
					'descripcion' => $row['nombre_usuario']
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($listaCbo)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function ver_popup_formulario()
	{
		$this->load->view('seguridad/usuario_formView');
	}	
	public function ver_popup_agregar_sede()
	{
		$this->load->view('seguridad/popupAgregarSedeView');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$this->db->trans_start();
		if($this->model_usuario->m_registrar($allInputs)){
			$allInputs['id'] = GetLastId('idusuario','usuarios');
    		if($this->model_usuario->m_registrar_detalle($allInputs)){ 
				$arrData['message'] = 'Se registraron los datos correctamente';
    			$arrData['flag'] = 1;
    			$arrData['idusuario'] = $allInputs['id'];
    			$arrData['usuario'] = $allInputs['usuario'];
			}
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$this->db->trans_start();
		if($this->model_usuario->m_editar($allInputs)){
			if($this->model_usuario->m_editar_detalle($allInputs)){
				$arrData['message'] = 'Se editaron los datos correctamente';
    			$arrData['flag'] = 1;
			} 
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function deshabilitar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['message'] = 'No se pudo deshabilitar al usuario';
    	$arrData['flag'] = 0;
    	foreach ($allInputs as $row) {
			if( $this->model_usuario->m_deshabilitar($row['idusuario']) ){
				$arrData['message'] = 'Se deshabilitó al usuario correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function habilitar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['message'] = 'No se pudo habilitar al usuario';
    	$arrData['flag'] = 0;
    	foreach ($allInputs as $row) {
			if( $this->model_usuario->m_habilitar($row['idusuario']) ){
				$arrData['message'] = 'Se habilitó al usuario correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}