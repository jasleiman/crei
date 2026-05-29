<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Eliminación masiva reutilizando las mismas reglas que las bajas individuales.
 */
class Bajas_masivas extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('obrassociales', '', TRUE);
		$this->load->model('alumnos', '', TRUE);
		$this->load->model('personas', '', TRUE);
		$this->load->model('planes', '', TRUE);
		$this->load->model('profesionales', '', TRUE);
		$this->load->model('perfiles', '', TRUE);
		$this->load->model('tipo_actividades', '', TRUE);
		$this->load->model('alcance', '', TRUE);
	}

	/**
	 * @param string $tipo
	 * @param int[] $ids
	 * @return array{ok: int, errores: string[]}
	 */
	public function procesar($tipo, $ids) {
		$ids = $this->normalizar_ids($ids);
		if (empty($ids)) {
			return array('ok' => 0, 'errores' => array('No se recibieron registros para eliminar.'));
		}

		$ok = 0;
		$errores = array();

		foreach ($ids as $id) {
			$resultado = $this->eliminar_uno($tipo, $id);
			if ($resultado === true) {
				$ok++;
			} else {
				$errores[] = $resultado;
			}
		}

		return array('ok' => $ok, 'errores' => $errores);
	}

	public function tipo_valido($tipo) {
		return in_array($tipo, $this->tipos(), true);
	}

	public function tipos() {
		return array(
			'obrassociales',
			'alumnos',
			'personas',
			'planes',
			'profesionales',
			'perfiles',
			'tipo_actividades',
		);
	}

	/**
	 * Acciones de menú que autorizan eliminar (según tabla menus + variantes históricas).
	 *
	 * @return string[]
	 */
	public function acciones_permiso($tipo) {
		$map = array(
			'obrassociales' => array(
				'administracion/bajaobrasocial',
				'administracion/bajaobrassocial',
				'administracion/obrassociales',
			),
			'alumnos' => array(
				'administracion/bajaalumnos',
				'administracion/alumnos',
			),
			'personas' => array(
				'administracion/bajapersonas',
				'administracion/personas',
			),
			'planes' => array(
				'administracion/bajaplanes',
				'administracion/planes',
			),
			'profesionales' => array(
				'administracion/bajaprofesionales',
				'administracion/profesionales',
			),
			'perfiles' => array(
				'administracion/bajaperfiles',
				'administracion/perfiles',
			),
			'tipo_actividades' => array(
				'administracion/bajatipoactividades',
				'administracion/actividades',
			),
		);
		return isset($map[$tipo]) ? $map[$tipo] : array();
	}

	public function usuario_puede_eliminar($tipo, $id_usuario) {
		$this->load->model('menus', '', TRUE);
		foreach ($this->acciones_permiso($tipo) as $accion) {
			if ($this->menus->estaHabilitado($accion, $id_usuario)) {
				return true;
			}
		}
		return false;
	}

	public function url_retorno($tipo) {
		$map = array(
			'obrassociales' => 'administracion/obrassociales',
			'alumnos' => 'administracion/alumnos',
			'personas' => 'administracion/personas',
			'planes' => 'administracion/planes',
			'profesionales' => 'administracion/profesionales',
			'perfiles' => 'administracion/perfiles',
			'tipo_actividades' => 'administracion/actividades',
		);
		return isset($map[$tipo]) ? $map[$tipo] : 'administracion';
	}

	protected function normalizar_ids($ids) {
		if (!is_array($ids)) {
			return array();
		}
		$out = array();
		foreach ($ids as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$out[] = $id;
			}
		}
		return array_values(array_unique($out));
	}

	/**
	 * @return true|string mensaje de error
	 */
	protected function eliminar_uno($tipo, $id) {
		switch ($tipo) {
			case 'obrassociales':
				return $this->eliminar_obra_social($id);
			case 'alumnos':
				return $this->eliminar_alumno($id);
			case 'personas':
				return $this->eliminar_persona($id);
			case 'planes':
				return $this->eliminar_plan($id);
			case 'profesionales':
				return $this->eliminar_profesional($id);
			case 'perfiles':
				return $this->eliminar_perfil($id);
			case 'tipo_actividades':
				return $this->eliminar_tipo_actividad($id);
			default:
				return 'Tipo de entidad no válido.';
		}
	}

	protected function eliminar_obra_social($id) {
		if ($this->cuenta_alumnos_con_obra_social($id) > 0) {
			return 'Obra social #' . $id . ': tiene alumnos asignados.';
		}
		$this->obrassociales->bajaObrasSociales($id);
		return true;
	}

	protected function cuenta_alumnos_con_obra_social($id_obras_sociales) {
		$this->db->from('alumnos');
		$this->db->where('id_obras_sociales', (int) $id_obras_sociales);
		$this->db->where('habilitado', 1);
		return (int) $this->db->count_all_results();
	}

	protected function eliminar_alumno($id) {
		if (!$this->alcance->puede_ver_alumno($id)) {
			return 'Alumno #' . $id . ': sin permiso.';
		}
		if ($this->alumnos->baja($id)) {
			return true;
		}
		return 'Alumno #' . $id . ': no se pudo eliminar.';
	}

	protected function eliminar_persona($id) {
		$p = $this->personas->get($id);
		if ($p && (int) $p[0]->id_profesionales === 2 && !$this->alcance->puede_ver_maestra($id)) {
			return 'Usuario #' . $id . ': sin permiso.';
		}
		$this->personas->bajaPersonas(array('id_personas' => $id));
		return true;
	}

	protected function eliminar_plan($id) {
		if ($this->planes->baja($id)) {
			return true;
		}
		return 'Plan #' . $id . ': no se pudo eliminar.';
	}

	protected function eliminar_profesional($id) {
		if ($this->profesionales->baja($id)) {
			return true;
		}
		return 'Tipo de profesional #' . $id . ': no se pudo eliminar.';
	}

	protected function eliminar_perfil($id) {
		$this->perfiles->bajaPerfiles($id);
		return true;
	}

	protected function eliminar_tipo_actividad($id) {
		$resultado = $this->tipo_actividades->baja($id);
		if ($resultado) {
			return true;
		}
		return 'Tipo de actividad #' . $id . ': no se pudo eliminar.';
	}
}
