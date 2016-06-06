<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acceso extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security'));
		$this->load->model(array('model_acceso'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0"); 
		$this->output->set_header("Pragma: no-cache");
		date_default_timezone_set("America/Lima");
	}
	public function index()
	{
		// var_dump($this->session); exit(); 
		$allInputs = json_decode(trim(file_get_contents('php://input')),true);
		if($allInputs){ 
			$loggedUser = $this->model_acceso->m_logging_user($allInputs);
			if( isset($loggedUser['logged']) && $loggedUser['logged'] == 1 ){ // var_dump('2'); exit();
				$arrData['flag'] = 1;
				$arrPerfilUsuario = $this->model_acceso->m_listar_perfil_usuario($loggedUser['id']); // var_dump($arrPerfilUsuario); exit(); 
				$arrPerfilUsuario['username'] = ucwords($arrPerfilUsuario['nombre_usuario']);
				$arrData['message'] = 'Usuario inició sesión correctamente';
				if( isset($arrPerfilUsuario['idusuario']) ){ 
					$this->session->set_userdata('sess_vs_'.substr(base_url(),-8,7),$arrPerfilUsuario);
				}else{
					$arrData['flag'] = 0;
    				$arrData['message'] = 'El usuario no tiene sede y/o empleado asignada';
				}
			}else{ 
    			$arrData['flag'] = 0;
    			$arrData['message'] = 'Usuario o contraseña invalida. Inténtelo nuevamente.';
    		}
			
			// Validar usuario y clave 
		}else{
			$arrData['flag'] = 0;
    		$arrData['message'] = 'No se encontraron datos.';
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function getSessionCI()
	{
		$arrData['flag'] = 0;
		$arrData['datos'] = array();
		if( $this->session->has_userdata( 'sess_vs_'.substr(base_url(),-8,7) ) ){
			$arrData['flag'] = 1;
			$arrData['datos'] = $_SESSION['sess_vs_'.substr(base_url(),-8,7) ];
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function logoutSessionCI()
	{
		$this->session->unset_userdata('sess_vs_'.substr(base_url(),-8,7));
        $this->cache->clean();
	}
}
