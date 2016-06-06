<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rol extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security'));
		$this->load->model(array('model_rol'));
		//cache 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function lista_roles()
	{
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_rol->m_cargar_roles($paramPaginate);
		$totalRows = $this->model_rol->m_count_roles();
		$arrListado = array();
		foreach ($lista as $row) {
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
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function lista_roles_session()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$idgroup = $allInputs['idgrupo'];
		$idespecialidad = isset($allInputs['idespecialidad']);
		$lista = $this->model_rol->m_cargar_roles_session($idgroup);
		$arrListado = array(
				array(
					'label' => 'Explorar',
				    'iconClasses' => '',
				    'separator'=> true,
				)
			);
		foreach ($lista as $row) {
			if($children = $this->model_rol->m_cargar_roles_children($row['idrol'],$allInputs)){

				array_push($arrListado, 
					array(
						// 'id' => $row['idrol'],
						'label' => $row['descripcion_rol'],
						'url' => $row['url_rol'],
						'iconClasses' => $row['icono_rol'],
						'children' => $children
					)
				);
			}else{
				array_push($arrListado, 
					array(
						// 'id' => $row['idrol'],
						'label' => $row['descripcion_rol'],
						'url' => $row['url_rol'],
						'iconClasses' => $row['icono_rol']
						
					)
				);
			}
		}

		// var_dump($arrListado); exit();
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


	public function ver_popup_formulario()
	{
		$this->load->view('seguridad/rol_formView');
	}	
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		if($this->model_rol->m_registrar($allInputs)){
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
		if($this->model_rol->m_editar($allInputs)){
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
			if( $this->model_rol->m_anular($row['id']) ){
				$arrData['message'] = 'Se anularon los datos correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}