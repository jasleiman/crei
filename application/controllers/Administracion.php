<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Administracion extends CI_Controller {

	function __construct() {

		parent::__construct();
		$this -> load -> model('menus', '', TRUE);
		$this -> load -> model('perfiles', '', TRUE);
		$this -> load -> model('usuarios', '', TRUE);
		$this -> load -> model('personas', '', TRUE);
		$this -> load -> model('obrassociales', '', TRUE);
		$this -> load -> model('alumnos', '', TRUE);
		$this -> load -> model('tipo_actividades', '', TRUE);
		$this -> load -> model('planes', '', TRUE);
		$this -> load -> model('planes_dias', '', TRUE);
		$this -> load -> model('profesionales', '', TRUE);
		$this -> load -> model('clases_profesionales', '', TRUE);
		$this -> load -> model('horas', '', TRUE);
		$this -> load -> model('alcance', '', TRUE);
	}

	function personas() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {

			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['usuarios'] = $this -> usuarios -> obtenerUsuarios();
			$data['personas'] = $this -> personas -> obtenerPersonas();

			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminusuarios', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	//revisar usuario / persona
	function altapersonas() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['perfiles'] = $this -> perfiles -> get();
			$data['profesionales'] = $this -> profesionales -> get();
			if ($this -> input -> post('id_personas') <> false) {
				$id_edit = (int) $this -> input -> post('id_personas');
				$persona_edit = $this -> personas -> get($id_edit);
				if (!$persona_edit) {
					redirect('administracion/personas', 'refresh');
				}
				if ((int) $persona_edit[0] -> id_profesionales === 2 && !$this -> alcance -> puede_ver_maestra($id_edit)) {
					redirect('administracion/personas', 'refresh');
				}
				$data['personas'] = $persona_edit;
				$data['padre'] = $this->personas->obtenerPersonasPadre($data['personas'][0]->id_profesionales);
				$data['coordinador']=$this->personas->obtenerIdPadre($this -> input -> post('id_personas'));
				$data['usuarios'] = $this -> usuarios -> getDesdePersona($this -> input -> post('id_personas'));
			}
			$this -> load -> view('altausuarios', $data);

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function bajapersonas() {
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['personas'] = $this -> personas -> obtenerPersonas();
			$id_personas = (int) $this -> input -> post('id_personas');
			if ($this -> input -> post('id_personas') == true) {
				$p = $this -> personas -> get($id_personas);
				if ($p && (int) $p[0] -> id_profesionales === 2 && !$this -> alcance -> puede_ver_maestra($id_personas)) {
					redirect('login', 'refresh');
				}
				$datos['id_personas'] = $id_personas;
				$this -> personas -> bajaPersonas($datos);
				$msg = "El Usuario ha sido eliminado";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/personas');
			} else {

				$msg = "El Usuario no se pudo eliminar";
				$type = "warning";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/personas');

			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function amPersonas() {
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$this -> load -> helper(array('form'));
			$this -> load -> helper('html');
			$this -> load -> helper('url');
			$this -> load -> helper('language');
			$this -> load -> library('user_agent');
			$this -> load -> library(array('form_validation'));

			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$datos = $this -> input -> post();
			$validacion = $this -> alcance -> validar_datos_persona($datos);
			if ($validacion !== true) {
				$this -> session -> set_flashdata('mensaje', '{&quot;text&quot;:&quot;' . $validacion . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;error&quot;}');
				redirect('administracion/personas', 'refresh');
			}

			//si estamos editando
			if ($this -> input -> post('id_personas_carga')) {

				$datos['id_personas'] = $this -> personas -> modificarPersonas($datos);
				$this -> usuarios -> modificar ($datos);
				$msg = "Los datos han sido modificados";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/personas');

				//si estamos agregando un usuario
			} else {

				$datos['id_personas'] = $this -> personas -> altaPersonas($datos);
				$this -> usuarios -> alta($datos);
				$msg = "Se ha agregado exitosamente";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/personas');

			}

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	public function selectCoordinadoresAlumno() {
		$id_maestra = (int) $this -> input -> post('id', TRUE);
		$output = '<option value="">Seleccione coordinador</option>';
		if ($id_maestra > 0 && $this -> alcance -> puede_ver_maestra($id_maestra)) {
			$coordinadores = $this -> alcance -> coordinadores_de_maestra($id_maestra);
			if ($coordinadores) {
				foreach ($coordinadores as $row) {
					$output .= '<option value="' . (int) $row -> id_personas . '">' . htmlspecialchars($row -> nombre, ENT_QUOTES, 'UTF-8') . '</option>';
				}
			}
		}
		echo $output;
	}

	public function selectPadre() {
		$id_profesionales = $this->input->post('id', TRUE);
		$id_personas = $this->input->post('id_personas', TRUE);
		$data['padres'] = $this->personas->obtenerPersonasPadre($id_profesionales);
		$padres = $this->personas->obtenerIdPadre($id_personas);
		if (!is_array($padres)) {
			$padres = array();
		}

		$output = '';
		if (is_array($data['padres']) && !empty($data['padres'])) {
			foreach ($data['padres'] as $id_padre => $nombre) {
				$sel = in_array((int) $id_padre, array_map('intval', $padres), true) ? ' selected' : '';
				$output .= '<option value="' . (int) $id_padre . '"' . $sel . '>' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</option>';
			}
		} else {
			$output = "<option value=''>Ninguno</option>";
		}
		echo $output;
	}

	function usuarios() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['usuarios'] = $this -> usuarios -> obtenerUsuarios();
			$data['personas'] = $this -> personas -> obtenerPersonas();

			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminusuarios', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function altausuarios() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['perfiles'] = $this -> perfiles -> get();

			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('altausuarios', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function obrassociales() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['obrassociales'] = $this -> obrassociales -> obtenerObrasSociales();
			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminobrassociales', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function altaobrassociales() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('altaobrassociales', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function bajaobrassocial() {
		$session_data = $this -> session -> userdata('logged_in');
		$this->load->model('bajas_masivas', '', TRUE);
		if ($this -> session -> userdata('logged_in') and $this -> bajas_masivas -> usuario_puede_eliminar('obrassociales', $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['obrassociales'] = $this -> obrassociales -> obtenerObrasSociales();

			if ($this -> input -> post('id_obras_sociales') == true) {
				$id_obras_sociales = $this -> input -> post('id_obras_sociales');
				$this->db->from('alumnos');
				$this->db->where('id_obras_sociales', (int) $id_obras_sociales);
				$this->db->where('habilitado', 1);
				$alumnosconos = (int) $this->db->count_all_results();

				if ($alumnosconos === 0) {
					$this -> obrassociales -> bajaObrasSociales($id_obras_sociales);
					$msg = "La obra social ha sido eliminada";
					$type = "success";
					$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
					$this -> session -> set_flashdata('mensaje', $mensaje);
					redirect('administracion/obrassociales');
				}
				//esta obra social no se puede eliminar
				$msg = "La operación no se pudo realizar, la obra social tiene personas asignadas";
				$type = "error";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/obrassociales');

			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function amobrassociales() {

		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');
		$this -> load -> library(array('form_validation'));

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['obrassociales'] = $this -> obrassociales -> obtenerObrasSociales();

			$descripcion = $this -> input -> post('descripcion');
			$email = $this -> input -> post('email');
			$direccion = $this -> input -> post('direccion');
			$localidad = $this -> input -> post('localidad');
			$partido = $this -> input -> post('partido');
			$codigo_postal = $this -> input -> post('codigo_postal');
			$provincia = $this -> input -> post('provincia');
			$telefono = $this -> input -> post('telefono');
			$contacto = $this -> input -> post('contacto');

			//si estamos editando
			if ($this -> input -> post('id_obras_sociales') == true) {

				$id_obras_sociales = $this -> input -> post('id_obras_sociales');
				$this -> obrassociales -> modificarObraSOcial($id_obras_sociales, $descripcion, $telefono, $contacto, $partido, $codigo_postal, $provincia, $direccion, $telefono, $email, $localidad);
				$msg = "Los datos han sido modificados";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/obrassociales');

				//si estamos agregando un usuario
			} else {

				$this -> obrassociales -> altaObraSocial($descripcion, $telefono, $contacto, $partido, $codigo_postal, $provincia, $direccion, $telefono, $email, $localidad);
				$msg = "Obra social agregada exitosamente";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/obrassociales');

			}

			//en cualquier caso damos ok porque todo ha salido bien
			//habría que hacer la comprobación de la respuesta del modelo

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function alumnos() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['alumnos'] = $this -> alumnos -> get();

			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminalumnos', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function altaalumnos() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['obrassociales'] = $this -> obrassociales -> obtenerObrasSociales();
			$data['personas'] = $this -> personas -> getMI();
			if (!$data['personas']) {
				$data['personas'] = array();
			}
			$data['coordinadores'] = array();
			if ($this -> input -> post('id_alumnos')) {
				$id_alumnos = (int) $this -> input -> post('id_alumnos');
				if (!$this -> alcance -> puede_ver_alumno($id_alumnos)) {
					redirect('administracion/alumnos', 'refresh');
				}
				$data['alumnos'] = $this -> alumnos -> get($id_alumnos);
				$data['planes'] = $this -> planes -> get(0, $id_alumnos);
				if ($data['alumnos'] && isset($data['alumnos'][0])) {
					$coords = $this -> alcance -> coordinadores_de_maestra($data['alumnos'][0] -> id_personas);
					$data['coordinadores'] = $coords ? $coords : array();
				}
			} else {
				$coords = $this -> alcance -> listar_coordinadores_para_alumno(0);
				$data['coordinadores'] = $coords ? $coords : array();
			}

			$this -> load -> view('altaalumnos', $data);

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function bajaalumnos() {

		$session_data = $this -> session -> userdata('logged_in');
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$data['username'] = $session_data['username'];

			$id_alumnos = (int) $this -> input -> post('id_alumnos');
			if (!$this -> alcance -> puede_ver_alumno($id_alumnos)) {
				redirect('login', 'refresh');
			}
			$resultado = $this -> alumnos -> baja($id_alumnos);
			if ($resultado) {
				$msg = "El alumno ha sido eliminado";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/alumnos');
			} else {
				//esta obra social no se puede eliminar
				$msg = "La operación no se pudo realizar";
				$type = "error";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/alumnos');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}//fin bajaalumnos

	function amAlumnos() {

		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];

			$datos = $this -> input -> post();
			$validacion = $this -> alcance -> validar_datos_alumno($datos);
			if ($validacion !== true) {
				$this -> session -> set_flashdata('mensaje', '{&quot;text&quot;:&quot;' . $validacion . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;error&quot;}');
				redirect('administracion/alumnos', 'refresh');
			}

			//si estamos editando
			if ($this -> input -> post('id_alumnos')) {

				$id = $this -> input -> post('id_alumnos');
				$id_planes = $this -> input -> post('id_planes');
				$this -> alumnos -> modificar($datos);
				
				//$this -> planes -> modificar($id_planes, $datos);
				//si estamos agregando un usuario
			} else {

				$id_alumnos = $this -> alumnos -> alta($datos);
				$datos['id_alumnos'] = $id_alumnos;
				$id_planes = $this -> planes -> alta($datos);
				$dias_maestra = array('id_planes' => $id_planes, 'id_clases_profesionales' => '1', 'cantidad_horas' => '20');
				$this -> planes_dias -> alta($dias_maestra);
				$dias_equipo = array('id_planes' => $id_planes, 'id_clases_profesionales' => '3', 'cantidad_horas' => '8');
				$this -> planes_dias -> alta($dias_equipo);
				$dias_equipo = array('id_planes' => $id_planes, 'id_clases_profesionales' => '2', 'cantidad_horas' => '4');
				$this -> planes_dias -> alta($dias_equipo);

			}
			redirect('administracion/alumnos', 'refresh');
			//en cualquier caso damos ok porque todo ha salido bien
			//habría que hacer la comprobación de la respuesta del modelo

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function actividades() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['tipo_actividades'] = $this -> tipo_actividades -> get();
			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminactividades', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function altaactividades() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);

			if ($this -> input -> post('id_tipo_actividades')) {
				$data['tipo_actividades'] = $this -> tipo_actividades -> get($this -> input -> post('id_tipo_actividades'));
			}
			$this -> load -> view('altaactividades', $data);

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function amActividades() {

		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);

			$descripcion = $this -> input -> post('descripcion');
			$directa = $this -> input -> post('directa');

			//si estamos editando
			if ($this -> input -> post('id_tipo_actividades')) {

				$id = $this -> input -> post('id_tipo_actividades');
				$this -> tipo_actividades -> modificar($id, $descripcion, $directa);

				//si estamos agregando un usuario
			} else {

				$this -> tipo_actividades -> alta($descripcion, $directa);

			}
			redirect('administracion/actividades', 'refresh');
			//en cualquier caso damos ok porque todo ha salido bien
			//habría que hacer la comprobación de la respuesta del modelo

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function bajatipoactividades() {
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['tipo_actividades'] = $this -> tipo_actividades -> get();

			$id_tipo_actividades = $this -> input -> post('id_tipo_actividades');
			$resultado = $this -> tipo_actividades -> baja($id_tipo_actividades);
			if ($resultado) {
				$msg = "El tipo de actividad ha sido eliminada";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/actividades');
			} else {
				//esta obra social no se puede eliminar
				$msg = "La operación no se pudo realizar, el tipo de actividades tiene actividades asignadas";
				$type = "error";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/actividades');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}//fin bajatipoactividades

	function planes() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['planes'] = $this -> planes -> get();
			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminplanes', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function altaplanes() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['alumnos'] = $this -> alumnos -> get();

			if ($this -> input -> post('id_planes')) {
				$data['planes'] = $this -> planes -> get($this -> input -> post('id_planes'));
			}
			$this -> load -> view('altaplanes', $data);

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function amPlanes() {

		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);

			$datos = $this -> input -> post();

			//si estamos editando
			if ($this -> input -> post('id_planes')) {

				$id = $this -> input -> post('id_planes');
				$this -> planes -> modificar($id, $datos);

				//si estamos agregando un usuario
			} else {

				$this -> planes -> alta($datos);

			}
			redirect('administracion/planes', 'refresh');
			//en cualquier caso damos ok porque todo ha salido bien
			//habría que hacer la comprobación de la respuesta del modelo

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function bajaplanes() {

		$session_data = $this -> session -> userdata('logged_in');
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['tipo_actividades'] = $this -> tipo_actividades -> get();

			$id_planes = $this -> input -> post('id_planes');
			$resultado = $this -> planes -> baja($id_planes);
			if ($resultado) {
				$msg = "El plan ha sido eliminado";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/planes');
			} else {
				//esta obra social no se puede eliminar
				$msg = "La operación no se pudo realizar";
				$type = "error";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/planes');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}//fin bajaplanes

	function planesdias() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['planes_dias'] = $this -> planes_dias -> get($this -> input -> post('id_planes'));
			$data['id_planes'] = $this -> input -> post('id_planes');
			$this -> load -> view('adminplanesdias', $data);

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}// fin planes dias

	function altaplanesdias() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['alumnos'] = $this -> alumnos -> obtenerAlumnos();
			$data['personas'] = $this -> personas -> obtenerPersonas();

			if ($this -> input -> post('id_planes_dias')) {
				$data['planes_dias'] = $this -> planes_dias -> getDia($this -> input -> post('id_planes'));

			}
			$data['id_planes'] = $this -> input -> post('id_planes');
			$this -> load -> view('altaplanesdias', $data);

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}// fin altaplanesdias

	function amPlanesDias() {

		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);

			$datos = $this -> input -> post();

			//si estamos editando
			if ($this -> input -> post('id_planes_dias')) {

				$id = $this -> input -> post('id_planes_dias');
				$this -> planes_dias -> modificar($id, $datos);

				//si estamos agregando un usuario
			} else {

				$this -> planes_dias -> alta($datos);

			}
			//redirect('administracion/planesdias', 'refresh');
			//en cualquier caso damos ok porque todo ha salido bien
			//habría que hacer la comprobación de la respuesta del modelo

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function bajaplanesdias() {
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);

			$id_planes_dias = $this -> input -> post('id_planes_dias');
			$resultado = $this -> planes_dias -> baja($id_planes_dias);
			if ($resultado) {
				$msg = "El día se ha eliminado";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				//redirect('administracion/actividades');
			} else {
				//esta obra social no se puede eliminar
				$msg = "La operación no se pudo realizar";
				$type = "error";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				//redirect('administracion/actividades');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}//fin bajaplanes

	function perfiles() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['perfiles'] = $this -> perfiles -> get();
			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminperfiles', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function bajaperfiles() {
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['perfiles'] = $this -> perfiles -> obtenerPerfiles();

			if ($this -> input -> post('id_perfiles') == true) {
				$id_perfiles = $this -> input -> post('id_perfiles');
				$this -> perfiles -> bajaperfiles($id_perfiles);
				$msg = "El Perfil ha sido eliminado";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/perfiles');
			} else {

				$msg = "El Perfil no se pudo eliminar";
				$type = "warning";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/perfiles');

			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function amPerfiles() {

		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');
		$this -> load -> library(array('form_validation'));

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$data['perfiles'] = $this -> perfiles -> get();

			$descripcion = $this -> input -> post('descripcion');
			$id_perfiles = $this -> input -> post('id_perfiles');

			//si estamos editando
			if ($this -> input -> post('id_perfiles') == true) {

				$id_perfiles = $this -> input -> post('id_perfiles');
				$this -> perfiles -> modificarPerfiles($id_perfiles, $descripcion);
				$msg = "Los datos han sido modificados";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/perfiles');

				//si estamos agregando un usuario
			} else {

				$this -> perfiles -> altaPerfiles($descripcion);
				$msg = "Se ha agregado exitosamente";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/perfiles');

			}

			//en cualquier caso damos ok porque todo ha salido bien
			//habría que hacer la comprobación de la respuesta del modelo

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function profesionales() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);

			$data['profesionales'] = $this -> profesionales -> get();
			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminprofesionales', $data);
			$this -> load -> view('footer');

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}// fin profesionales

	function altaprofesionales() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];

			$data['profesionales_todos'] = $this -> profesionales -> get();
			$data['clases_profesionales'] = $this -> clases_profesionales -> get();
			if ($this -> input -> post('id_profesionales')) {
				$data['profesionales'] = $this -> profesionales -> get($this -> input -> post('id_profesionales'));
			}
			$this -> load -> view('altaprofesionales', $data);

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function amProfesionales() {

		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');

		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {

			$data['username'] = $session_data['username'];

			$datos = $this -> input -> post();

			//si estamos editando
			if ($this -> input -> post('id_profesionales')) {

				$id = $this -> input -> post('id_profesionales');
				$this -> profesionales -> modificar($id, $datos);
				redirect('administracion/profesionales');

				//si estamos agregando un usuario
			} else {

				$this -> profesionales -> alta($datos);
				redirect('administracion/profesionales');

			}
			//redirect('administracion/planesdias', 'refresh');
			//en cualquier caso damos ok porque todo ha salido bien
			//habría que hacer la comprobación de la respuesta del modelo

		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function bajaprofesionales() {
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);

			$id = $this -> input -> post('id_profesionales');
			$resultado = $this -> profesionales -> baja($id);
			if ($resultado) {
				$msg = "El tipo de profesional se ha eliminado";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/profesionales');
			} else {
				//esta obra social no se puede eliminar
				$msg = "La operación no se pudo realizar";
				$type = "error";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/profesionales');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}//fin bajaplanes
	
	function horas() {
		$this -> load -> helper(array('form'));
		$this -> load -> helper('html');
		$this -> load -> helper('url');
		$this -> load -> helper('language');
		$this -> load -> library('user_agent');
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {

			$data['username'] = $session_data['username'];
			$data['menu'] = $this -> menus -> imprimirMenu($session_data['id']);
			$mes=$this -> input -> get('mes');
			$anio = (int) date('Y');
			if ($mes ==false) {$mes=date('m'); }
				$data['mes']=$mes;
				$data['anio']=$anio;
			$data['horas'] = $this -> horas -> get(0,$mes,$anio);

			$this -> load -> view('header');
			$this -> load -> view('menusuperior');
			$this -> load -> view('adminhoras', $data);
			$this -> load -> view('footer');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	

	function bajahoras() {
		$session_data = $this -> session -> userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this -> session -> userdata('logged_in') and $this -> menus -> estaHabilitado($funcion, $session_data['id'])) {
			$session_data = $this -> session -> userdata('logged_in');
			$data['username'] = $session_data['username'];
			

			if ($this -> input -> post('id_actividades') == true) {
				$datos['id_actividades'] = $this -> input -> post('id_actividades');
				$this -> horas -> baja($datos);
				$msg = "La actividad ha sido eliminada";
				$type = "success";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/horas');
			} else {

				$msg = "La actividad no se pudo eliminar";
				$type = "warning";
				$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
				$this -> session -> set_flashdata('mensaje', $mensaje);
				redirect('administracion/horas');

			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}

	}

	function papelera($tipo = '') {
		$this->load->model('papelera', '', TRUE);
		$this->load->model('bajas_masivas', '', TRUE);

		if (!$this->papelera->tipo_valido($tipo)) {
			show_404();
		}

		$session_data = $this->session->userdata('logged_in');
		if (!$this->session->userdata('logged_in') || !$this->bajas_masivas->usuario_puede_eliminar($tipo, $session_data['id'])) {
			redirect('login', 'refresh');
		}

		$this->load->helper(array('form', 'html', 'url', 'language'));
		$data['username'] = $session_data['username'];
		$data['menu'] = $this->menus->imprimirMenu($session_data['id']);
		$data['tipo'] = $tipo;
		$data['titulo'] = $this->papelera->titulo($tipo);
		$data['columnas'] = $this->papelera->columnas($tipo);
		$data['registros'] = $this->papelera->listar($tipo);
		$data['url_volver'] = $this->bajas_masivas->url_retorno($tipo);

		$this->load->view('header');
		$this->load->view('menusuperior');
		$this->load->view('administracion/papelera', $data);
		$this->load->view('footer');
	}

	function papelera_accion($tipo = '') {
		$this->load->model('papelera', '', TRUE);
		$this->load->model('bajas_masivas', '', TRUE);

		if (!$this->papelera->tipo_valido($tipo)) {
			show_404();
		}

		$session_data = $this->session->userdata('logged_in');
		if (!$this->session->userdata('logged_in') || !$this->bajas_masivas->usuario_puede_eliminar($tipo, $session_data['id'])) {
			redirect('login', 'refresh');
		}

		$accion = $this->input->post('accion');
		$ids = $this->input->post('ids');

		if ($accion === 'restaurar') {
			$resultado = $this->papelera->restaurar($tipo, $ids);
			$verbo = 'restauraron';
		} elseif ($accion === 'eliminar_definitivo') {
			$resultado = $this->papelera->eliminar_definitivo($tipo, $ids);
			$verbo = 'eliminaron definitivamente';
		} else {
			$resultado = array('ok' => 0, 'errores' => array('Acción no válida.'));
			$verbo = 'procesaron';
		}

		$redirect = site_url('administracion/papelera/' . $tipo);

		if ($resultado['ok'] > 0) {
			$msg = 'Se ' . $verbo . ' ' . $resultado['ok'] . ' registro(s).';
			$type = 'success';
			if (!empty($resultado['errores'])) {
				$msg .= ' Advertencias: ' . implode(' ', array_slice($resultado['errores'], 0, 5));
				$type = 'warning';
			}
		} else {
			$msg = !empty($resultado['errores'])
				? implode(' ', array_slice($resultado['errores'], 0, 8))
				: 'No se procesó ningún registro.';
			$type = 'error';
		}

		$mensaje = '{&quot;text&quot;:&quot;' . str_replace(array('"', "\n", "\r"), array("'", ' ', ' '), $msg) . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
		$this->session->set_flashdata('mensaje', $mensaje);
		redirect($redirect);
	}

	function bajamasiva($tipo = '') {
		$this->load->model('bajas_masivas', '', TRUE);

		if (!$this->bajas_masivas->tipo_valido($tipo)) {
			show_404();
		}

		$session_data = $this->session->userdata('logged_in');

		if (!$this->session->userdata('logged_in') || !$this->bajas_masivas->usuario_puede_eliminar($tipo, $session_data['id'])) {
			redirect('login', 'refresh');
		}

		$ids = $this->input->post('ids');
		$resultado = $this->bajas_masivas->procesar($tipo, $ids);
		$redirect = $this->bajas_masivas->url_retorno($tipo);

		if ($resultado['ok'] > 0) {
			$msg = 'Se eliminaron ' . $resultado['ok'] . ' registro(s).';
			$type = 'success';
			if (!empty($resultado['errores'])) {
				$msg .= ' No se pudieron eliminar algunos: ' . implode(' ', array_slice($resultado['errores'], 0, 5));
				if (count($resultado['errores']) > 5) {
					$msg .= ' (y ' . (count($resultado['errores']) - 5) . ' más)';
				}
				$type = 'warning';
			}
		} else {
			$msg = !empty($resultado['errores'])
				? implode(' ', array_slice($resultado['errores'], 0, 8))
				: 'No se eliminó ningún registro.';
			$type = 'error';
		}

		$mensaje = '{&quot;text&quot;:&quot;' . str_replace(array('"', "\n", "\r"), array("'", ' ', ' '), $msg) . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
		$this->session->set_flashdata('mensaje', $mensaje);
		redirect(site_url($redirect));
	}

	function perfil3menu1() {
		$datai = array('id_perfiles' => 3, id_menus => 1);
                $this->db->INSERT('perfiles_menus',$datai);
                $insert_id = $this->db->insert_id();
		//$this -> db -> INSERT('planes', $datai);
		///$insert_id = $this->db->insert_id();
		return $insert_id;
	}	
	

} //fin de la clase
?>