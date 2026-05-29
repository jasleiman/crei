<?php
class Clases_profesionales extends CI_Model {
	function __construct() {
		// Call the Model constructor
		parent::__construct();

	}

	function get($id = 0) {
		$this -> db -> reset_query();
		$this -> db -> select('id_clases_profesionales, descripcion');
		$this -> db -> from('clases_profesionales');
		
		if ($id > 0)
			$this -> db -> where('id_clases_profesionales', $id);

		$query = $this -> db -> get();
		//echo $this->db->last_query();
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();

			return $resultado;
		}
	}

	

}
