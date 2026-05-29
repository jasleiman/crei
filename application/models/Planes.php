<?php
class Planes extends CI_Model {
	function __construct() {
		// Call the Model constructor
		parent::__construct();

	}

	function get($id = 0,$alumno=0) {
		$this -> load -> model('Datos', 'datos');
		$this -> db -> reset_query();
		$this -> db -> WHERE('planes.habilitado', 1);
		if ($id > 0) {
			$this -> db -> WHERE('planes.id_planes', $id);
		}
		
		if ($alumno > 0) {
			$this -> db -> WHERE('planes.id_alumnos', $alumno);
		}
		$this -> db -> select('planes.id_planes,planes.id_alumnos as id_alumnos,alumnos.nombre as alumno,fecha_inicio,fecha_fin,acta_acuerdo,escuela,orientacion,\'0\' as horastotales');
		$this -> db -> from('planes');
		$this -> db -> join('alumnos', 'planes.id_alumnos = alumnos.id_alumnos');

		$query = $this -> db -> get();
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();
			foreach ($resultado as $row) {
				$row -> fecha_inicio = $this -> datos -> fechapg2php($row -> fecha_inicio);
				$row -> fecha_fin = $this -> datos -> fechapg2php($row -> fecha_fin);
				$row -> horastotales = $this -> calcularHoras($row -> id_planes,7,2015);
			}
			return $resultado;
		}
	}

	function alta($data) {
		$this -> load -> model('Datos', 'datos');
		$fecha_inicio = $this -> datos -> fechaphp2pg($data['fecha_inicio']);
		$fecha_fin = $this -> datos -> fechaphp2pg($data['fecha_fin']);
		$datai = array('id_alumnos' => $data['id_alumnos'], 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'acta_acuerdo' => $data['acta_acuerdo'], 'escuela' => $data['escuela'], 'orientacion' => $data['orientacion'], 'habilitado' => 1);

		$this -> db -> INSERT('planes', $datai);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	function modificar($id_planes, $data) {
		$this -> load -> model('Datos', 'datos');
		$fecha_inicio = $this -> datos -> fechaphp2pg($data['fecha_inicio']);
		$fecha_fin = $this -> datos -> fechaphp2pg($data['fecha_fin']);
		$datai = array('id_alumnos' => $data['id_alumnos'], 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'acta_acuerdo' => $data['acta_acuerdo'], 'escuela' => $data['escuela'], 'orientacion' => $data['orientacion'], 'habilitado' => 1);
		$this -> db -> WHERE('id_planes', $id_planes);
		$this -> db -> UPDATE('planes', $datai);
	}

	function baja($id_planes) {
		$this -> db -> reset_query();
		//verifico si no hay ninguna actividad con este tipo de actividades asignada
		try {
			$data = array('habilitado' => 0);
			$this -> db -> WHERE('id_planes', $id_planes);
			$this -> db -> UPDATE('planes', $data);
			return true;
		} catch (Exception $e) {
			log_message('error', $e -> getMessage());
		}

	}

	function calcularHoras($id_planes,$mes,$anio) {
		$this -> load -> model('Datos', 'datos');
		$this -> db -> WHERE('planes.habilitado', 1);
		$this -> db -> WHERE('planes.id_planes', $id_planes);
		$this -> db -> select('planes.id_planes,planes.id_alumnos as id_alumnos,planes_dias.id_clases_profesionales,planes_dias.cantidad_horas');
		$this -> db -> from('planes');
		$this -> db -> join('planes_dias', 'planes.id_planes = planes_dias.id_planes');

		$horastotales = '0:0';
		$horas = 0;
		$query = $this -> db -> get();
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();
			foreach($resultado as $row)
				$horastotales = $this -> datos -> sumarHoras($horastotales, $row -> cantidad_horas);

		}
		
		return $horastotales;

	}
	
	function diasEntre($start_date, $end_date, $weekDay) {
		$first_date = strtotime($start_date." -1 days");
		$first_date = strtotime(date("M d Y",$first_date)." next ".$weekDay);
		
		$last_date = strtotime($end_date." +1 days");
		$last_date = strtotime(date("M d Y",$last_date)." last ".$weekDay);
		
		return  floor(($last_date - $first_date)/(7*86400)) + 1;
	}

}
