<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grupo extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security'));
		$this->load->model(array('model_grupo'));
		//cache 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function lista_grupos()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$listaGrupos = $this->model_grupo->m_cargar_grupos($paramPaginate);
		$totalRows = $this->model_grupo->m_count_grupos();
		$arrListado = array(); //var_dump($listaGrupos); exit();
		foreach ($listaGrupos as $row) {
			$arrIdRolesPorGrupo = explode(",",$row['idrolesporgrupo']);
			$arrIdRoles = explode(",",$row['idroles']);
			$arrRoles = explode(",",$row['roles']);
			$arrIconoRoles = explode(",",$row['icono_roles']);
			$arrDetalle = array();
			foreach ($arrIdRoles as $key => $value) { 
				if(!empty($arrIdRolesPorGrupo[$key])){
					array_push($arrDetalle, 
						array(
							'idRolPorGrupo' => $arrIdRolesPorGrupo[$key],
							'idRol' => $arrIdRoles[$key],
							//'iconoRol' => $arrIconoRoles[$key],
							'rol' => $arrRoles[$key],
						)
					);
				}
			}
			array_push($arrListado, 
				array(
					'id' => $row['idgrupo'],
					'nombre' => $row['nombre_grupo'],
					'descripcion' => $row['descripcion'],
					'keygrupo'=>$row['key_group'],
					'roles' => $arrDetalle
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $totalRows;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($listaGrupos)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function lista_grupos_cbo()
	{
		$listaGrupos = $this->model_grupo->m_cargar_grupos_cbo();
		$arrListado = array(); //var_dump($listaGrupos); exit();
		foreach ($listaGrupos as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idgrupo'],
					'descripcion' => $row['nombre_grupo']
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($listaGrupos)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function lista_roles_no_agregados_al_grupo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$datos = $allInputs['datos'];
		$listaRolesNoAgregados = $this->model_grupo->m_cargar_roles_no_agregados_al_grupo($paramPaginate,$datos);
		$totalRows = $this->model_grupo->m_count_roles_no_agregados_al_grupo($datos);
		$arrListado = array();
		foreach ($listaRolesNoAgregados as $row) {
			array_push($arrListado, 
				array(
					'id' => $row['idrol'],
					'descripcion' => $row['descripcion_rol'],
					'url' => $row['url_rol'],
					'icono' => $row['icono_rol'],
				)
			);
		}
		$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $totalRows;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($listaRolesNoAgregados)){ 
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function ver_popup_formulario()
	{
		$this->load->view('seguridad/group_formView');
	}
	public function ver_popup_agregar_rol()
	{
		$this->load->view('seguridad/popupAgregarRolView');
	}
	public function agregar_rol()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	foreach ($allInputs['roles'] as $row) { 
    		$row['groupId'] = $allInputs['groupId'];
    		if($this->model_grupo->m_agregar_rol_grupo($row)){ 
				$arrData['message'] = 'Se registraron los datos correctamente';
	    		$arrData['flag'] = 1;
			}
    	}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function quitar_rol_de_grupo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al anular los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	//foreach ($allInputs['roles'] as $row) { 
		//$row['groupId'] = $allInputs['idRolPorGrupo'];
		if($this->model_grupo->m_quitar_rol_grupo($allInputs['idRolPorGrupo'])){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
    	//}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		if($this->model_grupo->m_registrar($allInputs)){
			$arrData['message'] = 'Se registraron los datos correctamente';
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
		if($this->model_grupo->m_editar($allInputs)) { 
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
			if( $this->model_grupo->m_anular($row['id']) ){
				$arrData['message'] = 'Se anularon los datos correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}