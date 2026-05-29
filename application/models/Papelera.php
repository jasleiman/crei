<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Registros con baja lógica (habilitado = 0): listar, restaurar y eliminar definitivamente.
 */
class Papelera extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('alcance', '', TRUE);
		$this->load->model('usuarios', '', TRUE);
	}

	public function tipos() {
		return array('personas', 'alumnos', 'obrassociales', 'planes');
	}

	public function tipo_valido($tipo) {
		return in_array($tipo, $this->tipos(), true);
	}

	public function columnas($tipo) {
		$map = array(
			'personas' => array(
				array('campo' => 'nombre', 'label' => 'Nombre'),
				array('campo' => 'email', 'label' => 'Email'),
				array('campo' => 'telefono', 'label' => 'Teléfono'),
				array('campo' => 'partido', 'label' => 'Partido'),
			),
			'alumnos' => array(
				array('campo' => 'nombre', 'label' => 'Alumno'),
				array('campo' => 'dni', 'label' => 'DNI'),
				array('campo' => 'telefono', 'label' => 'Teléfono'),
				array('campo' => 'obra_social', 'label' => 'Obra social'),
			),
			'obrassociales' => array(
				array('campo' => 'descripcion', 'label' => 'Nombre'),
				array('campo' => 'telefono', 'label' => 'Teléfono'),
				array('campo' => 'email', 'label' => 'Email'),
				array('campo' => 'localidad', 'label' => 'Localidad'),
			),
			'planes' => array(
				array('campo' => 'alumno', 'label' => 'Alumno'),
				array('campo' => 'escuela', 'label' => 'Escuela'),
				array('campo' => 'acta_acuerdo', 'label' => 'Acta / acuerdo'),
			),
		);
		return isset($map[$tipo]) ? $map[$tipo] : array();
	}

	public function titulo($tipo) {
		$map = array(
			'personas' => 'Papelera — Usuarios / personas',
			'alumnos' => 'Papelera — Alumnos',
			'obrassociales' => 'Papelera — Obras sociales',
			'planes' => 'Papelera — Planes',
		);
		return isset($map[$tipo]) ? $map[$tipo] : 'Papelera';
	}

	/**
	 * @return object[]
	 */
	public function listar($tipo) {
		switch ($tipo) {
			case 'personas':
				return $this->listar_personas();
			case 'alumnos':
				return $this->listar_alumnos();
			case 'obrassociales':
				return $this->listar_obras_sociales();
			case 'planes':
				return $this->listar_planes();
			default:
				return array();
		}
	}

	protected function listar_personas() {
		$this->db->select('id_personas AS id, id_personas, UPPER(nombre) AS nombre, email, telefono, partido, id_profesionales');
		$this->db->from('personas');
		$this->db->where('habilitado', 0);
		$this->alcance->aplicar_filtro_personas_listado();
		$this->db->order_by('nombre', 'asc');
		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->result() : array();
	}

	protected function listar_alumnos() {
		$this->db->select('alumnos.id_alumnos AS id, alumnos.id_alumnos, UPPER(alumnos.nombre) AS nombre, alumnos.dni, alumnos.telefono, obras_sociales.descripcion AS obra_social');
		$this->db->from('alumnos');
		$this->db->join('obras_sociales', 'alumnos.id_obras_sociales = obras_sociales.id_obras_sociales', 'left');
		$this->db->where('alumnos.habilitado', 0);
		$this->alcance->aplicar_filtro_alumnos('alumnos');
		$this->db->order_by('alumnos.nombre', 'asc');
		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->result() : array();
	}

	protected function listar_obras_sociales() {
		$this->db->select('id_obras_sociales AS id, id_obras_sociales, descripcion, telefono, email, localidad, partido');
		$this->db->from('obras_sociales');
		$this->db->where('habilitado', 0);
		$this->db->order_by('descripcion', 'asc');
		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->result() : array();
	}

	protected function listar_planes() {
		$this->db->select('planes.id_planes AS id, planes.id_planes, planes.acta_acuerdo, planes.escuela, alumnos.nombre AS alumno');
		$this->db->from('planes');
		$this->db->join('alumnos', 'planes.id_alumnos = alumnos.id_alumnos');
		$this->db->where('planes.habilitado', 0);
		$this->alcance->aplicar_filtro_alumnos('alumnos');
		$this->db->order_by('planes.id_planes', 'desc');
		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->result() : array();
	}

	/**
	 * @param string $tipo
	 * @param int[] $ids
	 * @return array{ok: int, errores: string[]}
	 */
	public function restaurar($tipo, $ids) {
		return $this->procesar_ids($tipo, $ids, 'restaurar');
	}

	/**
	 * @param string $tipo
	 * @param int[] $ids
	 * @return array{ok: int, errores: string[]}
	 */
	public function eliminar_definitivo($tipo, $ids) {
		return $this->procesar_ids($tipo, $ids, 'eliminar');
	}

	protected function procesar_ids($tipo, $ids, $accion) {
		$ids = $this->normalizar_ids($ids);
		if (empty($ids)) {
			return array('ok' => 0, 'errores' => array('No se recibieron registros.'));
		}

		$ok = 0;
		$errores = array();
		foreach ($ids as $id) {
			$resultado = ($accion === 'restaurar')
				? $this->restaurar_uno($tipo, $id)
				: $this->eliminar_uno($tipo, $id);
			if ($resultado === true) {
				$ok++;
			} else {
				$errores[] = $resultado;
			}
		}
		return array('ok' => $ok, 'errores' => $errores);
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

	protected function restaurar_uno($tipo, $id) {
		if (!$this->puede_gestionar($tipo, $id)) {
			return '#' . $id . ': sin permiso.';
		}
		if (!$this->esta_en_papelera($tipo, $id)) {
			return '#' . $id . ': no está en la papelera.';
		}

		switch ($tipo) {
			case 'personas':
				$this->db->where('id_personas', $id);
				$this->db->update('personas', array('habilitado' => 1));
				$this->restaurar_usuarios_persona($id);
				return true;
			case 'alumnos':
				$this->db->where('id_alumnos', $id);
				$this->db->update('alumnos', array('habilitado' => 1));
				return true;
			case 'obrassociales':
				$this->db->where('id_obras_sociales', $id);
				$this->db->update('obras_sociales', array('habilitado' => 1));
				return true;
			case 'planes':
				$this->db->where('id_planes', $id);
				$this->db->update('planes', array('habilitado' => 1));
				return true;
			default:
				return 'Tipo no válido.';
		}
	}

	protected function restaurar_usuarios_persona($id_personas) {
		$this->db->where('id_personas', (int) $id_personas);
		$this->db->update('usuarios', array('habilitado' => 1));
	}

	protected function eliminar_uno($tipo, $id) {
		if (!$this->puede_gestionar($tipo, $id)) {
			return '#' . $id . ': sin permiso.';
		}
		if (!$this->esta_en_papelera($tipo, $id)) {
			return '#' . $id . ': no está en la papelera.';
		}

		switch ($tipo) {
			case 'personas':
				return $this->eliminar_persona_definitivo($id);
			case 'alumnos':
				return $this->eliminar_alumno_definitivo($id);
			case 'obrassociales':
				return $this->eliminar_obra_social_definitivo($id);
			case 'planes':
				return $this->eliminar_plan_definitivo($id);
			default:
				return 'Tipo no válido.';
		}
	}

	protected function eliminar_persona_definitivo($id) {
		if ($this->cuenta_alumnos_activos_persona($id) > 0) {
			return 'Persona #' . $id . ': tiene alumnos activos asignados.';
		}
		$this->db->where('id_personas', $id);
		$this->db->delete('personas_padre');
		$this->db->where('id_padre', $id);
		$this->db->delete('personas_padre');
		$this->db->where('id_personas', $id);
		$this->db->delete('usuarios');
		$this->db->where('id_personas', $id);
		$this->db->delete('personas');
		return true;
	}

	protected function cuenta_alumnos_activos_persona($id_personas) {
		$this->db->from('alumnos');
		$this->db->group_start();
		$this->db->where('id_personas', (int) $id_personas);
		$this->db->or_where('id_coordinador', (int) $id_personas);
		$this->db->group_end();
		$this->db->where('habilitado', 1);
		return (int) $this->db->count_all_results();
	}

	protected function eliminar_alumno_definitivo($id) {
		$ids_planes = $this->ids_planes_alumno($id);
		foreach ($ids_planes as $id_plan) {
			$this->eliminar_plan_definitivo($id_plan, false);
		}
		$this->db->where('id_alumnos', $id);
		$this->db->delete('actividades');
		$this->db->where('id_alumnos', $id);
		$this->db->delete('alumnos');
		return true;
	}

	protected function ids_planes_alumno($id_alumnos) {
		$this->db->select('id_planes');
		$this->db->from('planes');
		$this->db->where('id_alumnos', (int) $id_alumnos);
		$query = $this->db->get();
		$ids = array();
		foreach ($query->result() as $row) {
			$ids[] = (int) $row->id_planes;
		}
		return $ids;
	}

	protected function eliminar_obra_social_definitivo($id) {
		if ($this->cuenta_alumnos_activos_con_obra_social($id) > 0) {
			return 'Obra social #' . $id . ': tiene alumnos activos asignados.';
		}

		$this->db->from('alumnos');
		$this->db->where('id_obras_sociales', (int) $id);
		$this->db->where('habilitado', 0);
		$alumnos_papelera = (int) $this->db->count_all_results();
		if ($alumnos_papelera > 0) {
			$id_reemplazo = $this->id_obra_social_reemplazo($id);
			if ($id_reemplazo <= 0) {
				return 'Obra social #' . $id . ': tiene alumnos en papelera y no hay otra obra activa para reasignarlos.';
			}
			$this->db->where('id_obras_sociales', (int) $id);
			$this->db->where('habilitado', 0);
			$this->db->update('alumnos', array('id_obras_sociales' => $id_reemplazo));
		}

		$this->db->where('id_obras_sociales', $id);
		$this->db->delete('obras_sociales');
		return true;
	}

	protected function cuenta_alumnos_activos_con_obra_social($id_obras_sociales) {
		$this->db->from('alumnos');
		$this->db->where('id_obras_sociales', (int) $id_obras_sociales);
		$this->db->where('habilitado', 1);
		return (int) $this->db->count_all_results();
	}

	/** Obra activa para reasignar alumnos en papelera antes de borrar la obra. */
	protected function id_obra_social_reemplazo($excluir_id) {
		$this->db->select('id_obras_sociales, descripcion');
		$this->db->from('obras_sociales');
		$this->db->where('habilitado', 1);
		$this->db->where('id_obras_sociales <>', (int) $excluir_id);
		$this->db->order_by('descripcion', 'asc');
		$query = $this->db->get();
		$fallback = 0;
		foreach ($query->result() as $row) {
			if (stripos(trim((string) $row->descripcion), 'PARTICULAR') !== false) {
				return (int) $row->id_obras_sociales;
			}
			if ($fallback <= 0) {
				$fallback = (int) $row->id_obras_sociales;
			}
		}
		return $fallback;
	}

	protected function eliminar_plan_definitivo($id, $verificar_papelera = true) {
		if ($verificar_papelera && !$this->esta_en_papelera('planes', $id)) {
			return '#' . $id . ': no está en la papelera.';
		}
		$this->db->where('id_planes', $id);
		$this->db->delete('planes_dias');
		$this->db->where('id_planes', $id);
		$this->db->delete('planes');
		return true;
	}

	protected function esta_en_papelera($tipo, $id) {
		$tabla = $this->tabla_tipo($tipo);
		if (!$tabla) {
			return false;
		}
		$pk = $this->pk_tipo($tipo);
		$this->db->from($tabla);
		$this->db->where($pk, (int) $id);
		$this->db->where('habilitado', 0);
		return (int) $this->db->count_all_results() > 0;
	}

	protected function tabla_tipo($tipo) {
		$map = array(
			'personas' => 'personas',
			'alumnos' => 'alumnos',
			'obrassociales' => 'obras_sociales',
			'planes' => 'planes',
		);
		return isset($map[$tipo]) ? $map[$tipo] : null;
	}

	protected function pk_tipo($tipo) {
		$map = array(
			'personas' => 'id_personas',
			'alumnos' => 'id_alumnos',
			'obrassociales' => 'id_obras_sociales',
			'planes' => 'id_planes',
		);
		return isset($map[$tipo]) ? $map[$tipo] : 'id';
	}

	protected function puede_gestionar($tipo, $id) {
		if ($this->alcance->ve_todo()) {
			return true;
		}
		switch ($tipo) {
			case 'personas':
				$row = $this->fila_persona($id);
				if (!$row) {
					return false;
				}
				if ((int) $row->id_profesionales === 2) {
					return $this->alcance->puede_ver_maestra_papelera((int) $row->id_personas);
				}
				return $this->alcance->es_coordinador() && (int) $row->id_personas === $this->alcance->id_personas_sesion();
			case 'alumnos':
				$row = $this->fila_alumno($id);
				return $row && $this->alcance->puede_ver_maestra_papelera((int) $row->id_personas);
			case 'obrassociales':
				return $this->alcance->ve_todo();
			case 'planes':
				$this->db->select('alumnos.id_personas');
				$this->db->from('planes');
				$this->db->join('alumnos', 'planes.id_alumnos = alumnos.id_alumnos');
				$this->db->where('planes.id_planes', (int) $id);
				$this->db->limit(1);
				$query = $this->db->get();
				if ($query->num_rows() === 0) {
					return false;
				}
				return $this->alcance->puede_ver_maestra_papelera((int) $query->row()->id_personas);
			default:
				return false;
		}
	}

	protected function fila_persona($id) {
		$this->db->from('personas');
		$this->db->where('id_personas', (int) $id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->row() : null;
	}

	protected function fila_alumno($id) {
		$this->db->select('id_alumnos, id_personas');
		$this->db->from('alumnos');
		$this->db->where('id_alumnos', (int) $id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->row() : null;
	}
}
