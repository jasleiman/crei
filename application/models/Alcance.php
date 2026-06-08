<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Alcance de datos: administrador y equipo (todo), coordinador (sus maestras),
 * maestra integradora (sus alumnos vía id_personas de la maestra).
 */
class Alcance extends CI_Model {

	const PERFIL_ADMINISTRADOR = 1;
	const PERFIL_EQUIPO = 3;
	const PERFIL_APOYO_MAESTRA = 4;
	const PROF_COORDINADOR = 1;
	const PROF_MAESTRA = 2;
	/** Tipo organigrama MAESTRA DE APOYO (profesionales.id_profesionales = 15 en datos actuales). */
	const PROF_MAESTRA_APOYO = 15;
	/** Clase profesional Maestra de apoyo (clases_profesionales.id = 3). */
	const CLASE_PROFESIONAL_APOYO = 3;
	/** Clase profesional Equipo: psicopedagoga, directivo, fonoaudiólogo, etc. */
	const CLASE_PROFESIONAL_EQUIPO = 2;

	/** @var int[]|null */
	protected $_cache_ids_profesionales_clase_equipo = null;

	/** @var bool|null */
	protected $_cache_es_maestra_apoyo = null;

	protected $session_data = array();

	public function __construct() {
		parent::__construct();
		$logged = $this->session->userdata('logged_in');
		$this->session_data = is_array($logged) ? $logged : array();
	}

	public function es_administrador() {
		return isset($this->session_data['id_perfiles'])
			&& (int) $this->session_data['id_perfiles'] === self::PERFIL_ADMINISTRADOR;
	}

	public function es_equipo() {
		return isset($this->session_data['id_perfiles'])
			&& (int) $this->session_data['id_perfiles'] === self::PERFIL_EQUIPO;
	}

	/** Administrador y Equipo: ven y consultan todo el universo de datos. */
	public function ve_todo() {
		return $this->es_administrador() || $this->es_equipo();
	}

	/** Puede asignar varios coordinadores a una maestra (admin o equipo). */
	public function puede_gestionar_coordinadores_maestra() {
		return $this->ve_todo();
	}

	public function id_personas_sesion() {
		return isset($this->session_data['id_personas']) ? (int) $this->session_data['id_personas'] : 0;
	}

	public function id_profesionales_sesion() {
		return isset($this->session_data['id_profesionales']) ? (int) $this->session_data['id_profesionales'] : 0;
	}

	public function es_maestra() {
		return $this->id_profesionales_sesion() === self::PROF_MAESTRA;
	}

	public function es_coordinador() {
		return $this->id_profesionales_sesion() === self::PROF_COORDINADOR;
	}

	/** Usuario con tipo profesional Maestra de apoyo (clase 3). */
	public function es_maestra_apoyo() {
		if ($this->_cache_es_maestra_apoyo !== null) {
			return $this->_cache_es_maestra_apoyo;
		}
		$id = $this->id_profesionales_sesion();
		if ($id === self::PROF_MAESTRA_APOYO) {
			$this->_cache_es_maestra_apoyo = true;
			return true;
		}
		$this->_cache_es_maestra_apoyo = $this->id_clase_profesional($id) === self::CLASE_PROFESIONAL_APOYO;
		return $this->_cache_es_maestra_apoyo;
	}

	/** Perfil de menú «Apoyo + Maestra» (carga horas normal, no equipo). */
	public function es_perfil_apoyo_maestra() {
		return isset($this->session_data['id_perfiles'])
			&& (int) $this->session_data['id_perfiles'] === self::PERFIL_APOYO_MAESTRA;
	}

	/** Solo perfil Equipo usa la pantalla de carga grupal (ateneo) como pantalla inicial al ingresar. */
	public function usa_carga_horas_equipo() {
		return isset($this->session_data['id_perfiles'])
			&& (int) $this->session_data['id_perfiles'] === self::PERFIL_EQUIPO;
	}

	/** Puede abrir y guardar carga grupal (ateneo): administrador y equipo. */
	public function puede_acceder_carga_horas_equipo() {
		return $this->ve_todo();
	}

	/** Ve todos los alumnos activos (admin, equipo o maestra de apoyo). */
	public function ve_todos_los_alumnos() {
		return $this->ve_todo() || $this->es_maestra_apoyo();
	}

	protected function id_clase_profesional($id_profesionales) {
		$id_profesionales = (int) $id_profesionales;
		if ($id_profesionales <= 0) {
			return 0;
		}
		$this->db->reset_query();
		$this->db->select('id_clases_profesionales');
		$this->db->from('profesionales');
		$this->db->where('id_profesionales', $id_profesionales);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 0) {
			return 0;
		}
		return (int) $query->row()->id_clases_profesionales;
	}

	/**
	 * Tipos profesionales que pueden ser coordinador/superior de una maestra.
	 *
	 * @return int[]
	 */
	public function ids_profesionales_clase_equipo() {
		if ($this->_cache_ids_profesionales_clase_equipo !== null) {
			return $this->_cache_ids_profesionales_clase_equipo;
		}
		$this->db->select('id_profesionales');
		$this->db->from('profesionales');
		$this->db->where('id_clases_profesionales', self::CLASE_PROFESIONAL_EQUIPO);
		$this->db->where('habilitado', 1);
		$ids = array();
		foreach ($this->db->get()->result() as $row) {
			$ids[] = (int) $row->id_profesionales;
		}
		$this->db->reset_query();
		$this->_cache_ids_profesionales_clase_equipo = array_values(array_unique($ids));
		return $this->_cache_ids_profesionales_clase_equipo;
	}

	/**
	 * IDs de maestras integradoras visibles. null = sin filtro (administrador).
	 *
	 * @return int[]|null
	 */
	public function ids_maestras_visibles() {
		if ($this->ve_todo()) {
			return null;
		}

		$id = $this->id_personas_sesion();
		if ($id <= 0) {
			return array();
		}

		if ($this->es_maestra()) {
			return array($id);
		}

		$this->load->model('datos', '', TRUE);
		$descendientes = $this->datos->getChildrenAlternativo('personas', $id);
		if (!is_array($descendientes) || empty($descendientes)) {
			return array();
		}

		$this->db->select('id_personas');
		$this->db->from('personas');
		$this->db->where_in('id_personas', $descendientes);
		$this->db->where('id_profesionales', self::PROF_MAESTRA);
		$this->db->where('habilitado', 1);
		$query = $this->db->get();

		$ids = array();
		foreach ($query->result() as $row) {
			$ids[] = (int) $row->id_personas;
		}
		return array_values(array_unique($ids));
	}

	/**
	 * Aplica filtro de alumnos por maestra integradora asignada.
	 */
	public function aplicar_filtro_alumnos($alias = 'alumnos') {
		if ($this->ve_todos_los_alumnos()) {
			return;
		}
		$ids = $this->ids_maestras_visibles();
		if ($ids === null) {
			return;
		}
		$campo = $alias . '.id_personas';
		if (empty($ids)) {
			$this->db->where($campo, 0);
		} else {
			$this->db->where_in($campo, $ids);
		}
	}

	public function puede_ver_maestra($id_maestra) {
		if ($this->ve_todo() || $this->es_maestra_apoyo()) {
			return true;
		}
		$ids = $this->ids_maestras_visibles();
		if ($ids === null) {
			return true;
		}
		return in_array((int) $id_maestra, $ids, true);
	}

	/** Alcance sobre maestra aunque esté en papelera (habilitado = 0). */
	public function puede_ver_maestra_papelera($id_maestra) {
		$id_maestra = (int) $id_maestra;
		if ($this->ve_todo()) {
			return true;
		}
		if ($this->es_maestra()) {
			return $id_maestra === $this->id_personas_sesion();
		}
		if ($this->es_coordinador()) {
			$this->load->model('datos', '', TRUE);
			$descendientes = $this->datos->getChildrenAlternativo('personas', $this->id_personas_sesion());
			if (!is_array($descendientes)) {
				return false;
			}
			return in_array($id_maestra, array_map('intval', $descendientes), true);
		}
		return false;
	}

	public function puede_ver_alumno($id_alumnos) {
		if ($this->ve_todos_los_alumnos()) {
			$this->db->from('alumnos');
			$this->db->where('id_alumnos', (int) $id_alumnos);
			$this->db->where('habilitado', 1);
			return (int) $this->db->count_all_results() > 0;
		}
		$this->db->select('id_personas');
		$this->db->from('alumnos');
		$this->db->where('id_alumnos', (int) $id_alumnos);
		$this->db->where('habilitado', 1);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 0) {
			return false;
		}
		return $this->puede_ver_maestra((int) $query->row()->id_personas);
	}

	public function coordinador_pertenece_a_maestra($id_coordinador, $id_maestra) {
		$this->db->from('personas_padre');
		$this->db->where('id_personas', (int) $id_maestra);
		$this->db->where('id_padre', (int) $id_coordinador);
		return $this->db->count_all_results() > 0;
	}

	/**
	 * Coordinadores asignados a una maestra (personas_padre).
	 *
	 * @return object[]|false
	 */
	public function coordinadores_de_maestra($id_maestra) {
		$tipos = $this->ids_profesionales_clase_equipo();
		if (empty($tipos)) {
			return false;
		}
		$this->db->select('personas.id_personas, UPPER(personas.nombre) AS nombre');
		$this->db->from('personas_padre');
		$this->db->join('personas', 'personas.id_personas = personas_padre.id_padre');
		$this->db->where('personas_padre.id_personas', (int) $id_maestra);
		$this->db->where_in('personas.id_profesionales', $tipos);
		$this->db->where('personas.habilitado', 1);
		$this->db->order_by('personas.nombre', 'asc');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	/**
	 * Lista para combo de coordinador en alta de alumno.
	 */
	public function listar_coordinadores_para_alumno($id_maestra = 0) {
		if ($this->ve_todo()) {
			$tipos = $this->ids_profesionales_clase_equipo();
			if (empty($tipos)) {
				return false;
			}
			$this->db->select('id_personas, UPPER(nombre) AS nombre');
			$this->db->from('personas');
			$this->db->where_in('id_profesionales', $tipos);
			$this->db->where('habilitado', 1);
			$this->db->order_by('nombre', 'asc');
			$query = $this->db->get();
			return $query->num_rows() > 0 ? $query->result() : false;
		}

		if ($id_maestra > 0) {
			return $this->coordinadores_de_maestra($id_maestra);
		}

		if ($this->es_coordinador()) {
			$this->db->select('id_personas, UPPER(nombre) AS nombre');
			$this->db->from('personas');
			$this->db->where('id_personas', $this->id_personas_sesion());
			$this->db->where('habilitado', 1);
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->num_rows() > 0 ? $query->result() : false;
		}

		return false;
	}

	/**
	 * @param array $datos POST de alumno (id_personas, id_coordinador, id_alumnos opcional)
	 * @return true|string
	 */
	public function validar_datos_alumno($datos) {
		$id_maestra = isset($datos['id_personas']) ? (int) $datos['id_personas'] : 0;
		$id_coordinador = isset($datos['id_coordinador']) ? (int) $datos['id_coordinador'] : 0;

		if ($id_maestra <= 0) {
			return 'Debe seleccionar una maestra integradora.';
		}
		if ($id_coordinador <= 0) {
			return 'Debe seleccionar un coordinador.';
		}
		if (!$this->puede_ver_maestra($id_maestra)) {
			return 'No tiene permiso para usar esa maestra integradora.';
		}
		if (!$this->coordinador_pertenece_a_maestra($id_coordinador, $id_maestra)) {
			return 'El coordinador no está asignado a la maestra integradora seleccionada.';
		}
		if (!empty($datos['id_alumnos']) && !$this->puede_ver_alumno($datos['id_alumnos'])) {
			return 'No tiene permiso para modificar ese alumno.';
		}
		return true;
	}

	/**
	 * @param array $datos POST de persona/usuario
	 * @return true|string
	 */
	public function validar_datos_persona($datos) {
		$id_profesionales = isset($datos['id_profesionales']) ? (int) $datos['id_profesionales'] : 0;
		$id_personas_edit = 0;
		if (!empty($datos['id_personas_carga'])) {
			$id_personas_edit = (int) $datos['id_personas_carga'];
		} elseif (!empty($datos['id_personas'])) {
			$id_personas_edit = (int) $datos['id_personas'];
		}

		if ($id_personas_edit > 0 && (int) $id_profesionales === self::PROF_MAESTRA) {
			if (!$this->puede_ver_maestra($id_personas_edit)) {
				return 'No tiene permiso para modificar esa maestra integradora.';
			}
		}

		if ((int) $id_profesionales !== self::PROF_MAESTRA) {
			return true;
		}

		$padres = isset($datos['id_padre']) ? $datos['id_padre'] : array();
		if (!is_array($padres)) {
			$padres = array($padres);
		}
		$padres = array_filter(array_map('intval', $padres));

		if (empty($padres)) {
			return 'Debe asignar al menos un coordinador a la maestra integradora.';
		}

		$tipos_coord = $this->ids_profesionales_clase_equipo();
		if (empty($tipos_coord)) {
			return 'No hay tipos profesionales de equipo configurados para asignar coordinadores.';
		}
		foreach ($padres as $id_padre) {
			$this->db->select('id_personas');
			$this->db->from('personas');
			$this->db->where('id_personas', $id_padre);
			$this->db->where_in('id_profesionales', $tipos_coord);
			$this->db->where('habilitado', 1);
			$this->db->limit(1);
			if ($this->db->get()->num_rows() === 0) {
				return 'Uno de los coordinadores seleccionados no es válido (debe ser un profesional de clase Equipo).';
			}
		}

		if (!$this->puede_gestionar_coordinadores_maestra() && $this->es_coordinador()) {
			if (!in_array($this->id_personas_sesion(), $padres, true)) {
				return 'Debe incluirse como coordinador de la maestra integradora.';
			}
			if (count($padres) > 1) {
				return 'Solo administrador o equipo pueden asignar más de un coordinador.';
			}
		}

		return true;
	}

	public function aplicar_filtro_personas_listado() {
		$ids = $this->ids_maestras_visibles();
		if ($ids === null) {
			return;
		}
		$incluir = $ids;
		$incluir[] = $this->id_personas_sesion();
		$incluir = array_values(array_unique($incluir));
		if (empty($incluir)) {
			$this->db->where('personas.id_personas', 0);
		} else {
			$this->db->where_in('personas.id_personas', $incluir);
		}
	}

	/** IDs para IN (...) en consultas SQL crudas */
	public function ids_maestras_visibles_sql_in() {
		$ids = $this->ids_maestras_visibles();
		if ($ids === null) {
			return null;
		}
		if (empty($ids)) {
			return '(0)';
		}
		return '(' . implode(',', array_map('intval', $ids)) . ')';
	}
}
