<?php
class Profesionales extends CI_Model {
	function __construct() {
		// Call the Model constructor
		parent::__construct();

	}

	function get($id = 0) {
		$this -> db -> reset_query();
		$this -> db -> select('id_profesionales, descripcion,id_padre,id_clases_profesionales');
		$this -> db -> from('profesionales');
		$this -> db -> where('habilitado','1');
		if ($id > 0)
			$this -> db -> where('id_profesionales', $id);

		$query = $this -> db -> get();
		//echo $this->db->last_query();
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();

			return $resultado;
		}
	}

	function alta($data) {
		$datai = array('id_profesionales' => $data['id_profesionales'], 'descripcion' => $data['descripcion'], 'id_padre' => ($data['id_padre']==0) ? NULL : $data['id_padre'],'id_clases_profesionales' => $data['id_clases_profesionales'], 'habilitado' => 1);

		$this -> db -> INSERT('profesionales', $datai);
	}

	function modificar($id, $data) {
		$datai = array('descripcion' => $data['descripcion'], 'id_padre' => $data['id_padre'],'id_clases_profesionales' => $data['id_clases_profesionales'], 'habilitado' => isset($data['habilitado']) ? $data['habilitado']:1);

		$this -> db -> WHERE('id_profesionales', $id);
		$this -> db -> UPDATE('profesionales', $datai);
	}

	function baja($id) {
		$this -> db -> reset_query();
		//verifico si no hay ninguna actividad con este tipo de actividades asignada
		try {
			$data = array('habilitado' => 0);
			$this -> db -> WHERE('id_profesionales', $id);
			$this -> db -> UPDATE('profesionales', $data);
			return true;
		} catch (Exception $e) {
			log_message('error', $e -> getMessage());
		}

	}

}
