<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ControlEmpleado extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security'));
		$this->load->model(array('model_empleado'));
		//cache 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");
	}
	public function lista_contrato_empleados()
	{
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		$paramPaginate = $allInputs['paginate'];

		$lista = $this->model_empleado->m_cargar_contrato_empleado($paramPaginate);
		$totalRows = $this->model_empleado->m_count_contrato_empleado();
		$arrListado = array();
		foreach ($lista as $row) {
			if( $row['estado_co'] == 1 ){
				$estado = 'HABILITADO';
				$clase = 'label-success';
			}
			if( $row['estado_co'] == 2 ){
				$estado = 'DESHABILITADO';
				$clase = 'label-default';
			}
			array_push($arrListado,
				array(
					'id' => $row['idcontrato'],
					'idempleado' => $row['idempleado'],
					'empleado' => $row['empleado'],
					'dni'=> $row['dni'],
					'fecha_inicio' => date('d-m-Y ',strtotime($row['fecha_inicio'])) ,
					'fecha_final' => date('d-m-Y ',strtotime($row['fecha_final'])),
					'idcargo' => $row['idcargo'],
					'cargo' => $row['cargo'],
					'idseccion' => $row['idseccion'],
					'seccion' => $row['seccion'],
					'idafp' => $row['idafp'],
					'afp' => $row['afp'],
					'idbanco' => $row['idbanco'],
					'banco' => $row['banco'],
					'numero_cuenta' => $row['numero_cuenta'],
					'monto_salario' => $row['monto_salario'],
					'observaciones' => $row['observaciones'],
					'estado_asegurado' => ($row['estado_asegurado']==1?'SI':'NO'),
					'idestado_asegurado' => $row['estado_asegurado'],
					'estado_co' => array(
						'string' => $estado,
						'clase' =>$clase,
						'bool' =>$row['estado_co']
					)
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

	public function ver_popup_formulario()
	{
		$this->load->view('control-empleado/contratoEmpleado_formView');
	}

	public function registrar_contrato()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		if($this->model_empleado->m_registrar_contrato($allInputs)){
			$arrData['message'] = 'Se registraron los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function editar_contrato()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
		if($this->model_empleado->m_editar_contrato($allInputs)){
			$arrData['message'] = 'Se editaron los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function anular_contrato()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['message'] = 'No se pudieron anular los datos';
    	$arrData['flag'] = 0;
    	foreach ($allInputs as $row) {
			if( $this->model_empleado->m_anular_contrato($row['id']) ){
				$arrData['message'] = 'Se anularon los datos correctamente';
	    		$arrData['flag'] = 1;
			}
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}


}