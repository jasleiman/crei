<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		if ($this -> agent -> browser() == 'Internet Explorer' and $this -> agent -> version() <= 8)
			redirect('/exploradornosoportado');
		else {
			$this -> load -> view('header');
			$this -> load -> view('login');
			$this -> load -> view('footer');
		}
	}

	function verificarlogin() {
		//This method will have the credentials validation
		$this -> load -> library('form_validation');
		$this -> form_validation -> set_rules('username', 'Username', 'trim|required');
		$this -> form_validation -> set_rules('clave', 'Clave', 'trim|required|callback_check_database');

		if ($this -> form_validation -> run() == FALSE) {
			//Field validation failed.  User redirected to login page
			$error = 'Usuarios o clave incorrectos';
			$this -> session -> set_flashdata('error', $error);
			redirect('login', 'refresh');

		} else {
			//Go to private area
			$session_data = $this -> session -> userdata('logged_in');
			$this->load->model('alcance', '', TRUE);
			if ($this->alcance->usa_carga_horas_equipo()) {
				redirect('principal/cargarateneo', 'refresh');
			}
			redirect('principal', 'refresh');
		}

	}

	function check_database($password) {
		log_message('info', $password);
		$this -> load -> model('usuarios', '', TRUE);
		$this -> load -> model('personas', '', TRUE);
		//Field validation succeeded.  Validate against database
		$username = $this -> input -> post('username');
		//query the database
		log_message('info', $username);
		$result = $this -> usuarios -> login($username, $password);
		if ($result) {
			$sess_array = array();
			foreach ($result as $row) {

				$persona = $this -> personas -> get($row -> id_personas);

				$sess_array = array('id' => $row -> id_usuarios, 'username' => $row -> nombre, 'persona' => $persona[0] -> nombre, 'id_personas' => $persona[0]->id_personas, 'id_profesionales' => $persona[0]->id_profesionales,'id_perfiles'=>$row->id_perfiles);
				$this -> session -> set_userdata('logged_in', $sess_array);
			}
			return TRUE;
		} else {
			$this -> form_validation -> set_message('check_database', $this -> lang -> line('mensaje_errorlogin'));
			return false;
		}

	}

	function logout() {
		$this -> session -> unset_userdata('logged_in');
		session_destroy();
		redirect('login', 'refresh');
	}

}
?>