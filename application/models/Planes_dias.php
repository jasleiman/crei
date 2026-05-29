<?php
class Planes_dias extends CI_Model {
	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function get($id = 0) {
		$this -> db -> reset_query();

		if ($id > 0) {
			$this -> db -> WHERE('planes.id_planes', $id);
		}
		$this -> db -> select('id_planes_dias,planes.id_planes,clases_profesionales.descripcion as clases_profesionales,planes_dias.id_clases_profesionales,cantidad_horas,planes.id_alumnos as id_alumnos,alumnos.nombre as alumno,fecha_inicio,fecha_fin,acta_acuerdo,escuela,orientacion');
		$this -> db -> from('planes_dias');
		$this -> db -> join('planes', 'planes.id_planes = planes_dias.id_planes');
		$this -> db -> join('alumnos', 'planes.id_alumnos = alumnos.id_alumnos');
		$this -> db -> join('clases_profesionales', 'planes_dias.id_clases_profesionales = clases_profesionales.id_clases_profesionales');
		$this -> db -> WHERE('planes.habilitado', '1');
		$this -> db -> order_by('id_clases_profesionales', 'ASC');

		$query = $this -> db -> get();
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();
			return $resultado;
		} else
			return false;
	}

	function getDia($id = 0) {
		$this -> db -> reset_query();
		if ($id > 0) {
			$this -> db -> WHERE('id_planes_dias', $id);
		}
		$this -> db -> select('id_planes_dias,id_planes,id_clases_profesionales,cantidad_horas');
		$this -> db -> from('planes_dias');
		$query = $this -> db -> get();
		
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();
			return $resultado;
		} else
			return false;
	}


	function alta($datos) {

		$data = array('id_planes' => $datos['id_planes'], 'id_clases_profesionales' => $datos['id_clases_profesionales'], 'cantidad_horas' => $datos['cantidad_horas']);

		$this -> db -> INSERT('planes_dias', $data);
	}

	function modificar($id_planes_dias, $datos) {

		$data = array('id_planes' => $datos['id_planes'], 'id_clases_profesionales' => $datos['id_clases_profesionales'], 'cantidad_horas' => $datos['cantidad_horas']);
		$this -> db -> WHERE('$id_planes_dias', $id_planes_dias);
		$this -> db -> UPDATE('planes_dias', $data);
	}

	function baja($id_planes_dias) {
		$this -> db -> reset_query();
		//verifico si no hay ninguna actividad con este tipo de actividades asignada
		$this -> db -> WHERE('id_planes_dias', $id_planes_dias);
		$this -> db -> delete('planes_dias', $data);
		return true;

	}

}
