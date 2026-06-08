<?php
class Horas extends CI_Model {

	function get($id=0,$mes=0,$anio=0) {
		$this -> load -> model('Alcance', 'alcance');

		$es_maestra_apoyo = $this->alcance->es_maestra_apoyo();
		$id_personas_sesion = $this->alcance->id_personas_sesion();
		$ids_maestras = $this->alcance->ids_maestras_visibles();

		$this->db->reset_query();
		if ($mes > 0 and $anio > 0 ) {
			$fecha_inicio = $anio.'-'.$mes.'-'.'1';
			$fecha_fin = date('Y-m-t',strtotime($fecha_inicio));

			$this -> db -> WHERE('actividades.fecha_inicio >=', $fecha_inicio);
			$this -> db -> WHERE('actividades.fecha_fin <=',  $fecha_fin.' 23:59:59');
		}
			$this -> db -> WHERE('alumnos.habilitado',  '1');
			$this -> db -> WHERE('actividades.habilitado',  '1');
			if ($es_maestra_apoyo) {
				$this->db->where('actividades.id_personas', $id_personas_sesion);
			} elseif ($ids_maestras !== null) {
				if (empty($ids_maestras)) {
					$this->db->where('alumnos.id_personas', 0);
					$this->db->where('actividades.id_personas', 0);
				} else {
					$this->db->where_in('alumnos.id_personas', $ids_maestras);
					$this->db->where_in('actividades.id_personas', $ids_maestras);
				}
			}
			$this -> db -> select('id_actividades, tipo_actividades.id_tipo_actividades,tipo_actividades.descripcion,fecha_inicio,fecha_fin,alumnos.id_alumnos,alumnos.nombre as alumno,observaciones,actividades.habilitado,actividades.id_personas');
			$this -> db -> from('actividades');
			$this -> db -> join('tipo_actividades','tipo_actividades.id_tipo_actividades=actividades.id_tipo_actividades','inner');
			$this -> db -> join('alumnos','alumnos.id_alumnos=actividades.id_alumnos','inner');

			if ($id > 0)
			$this -> db -> where('id_actividades', $id);

			$this->db->order_by('alumnos.nombre,fecha_inicio');
			$query = $this -> db -> get();
			//echo $this->db->last_query();
			if ($query -> num_rows() > 0) {
				return $query -> result();
			} else {
				return false;
			}

	}

	function baja($datos) {

		$data = array('habilitado' => 0, );
		$this -> db -> WHERE('id_actividades', $datos['id_actividades']);
		$this -> db -> UPDATE('actividades', $data);
	}

	function alta($datos) {
		$data = array('id_tipo_actividades' => $datos['id_tipo_actividades'], 'fecha_inicio' => $datos['fecha_inicio'], 'fecha_fin' => $datos['fecha_fin'], 'id_alumnos' => $datos['id_alumnos'], 'id_personas' => $datos['id_personas'], 'observaciones' => $datos['observaciones'], 'habilitado' => 1);
		$this -> db -> INSERT('acividades', $data);
		return $this->db->insert_id();
	}

	function modificacion($datos) {
		$data = array('id_tipo_actividades' => $datos['id_tipo_actividades'], 'fecha_inicio' => $datos['fecha_inicio'], 'fecha_fin' => $datos['fecha_fin'], 'id_alumnos' => $datos['id_alumnos'], 'id_personas' => $datos['id_personas'], 'observaciones' => $datos['observaciones']);
		$this -> db -> WHERE('id_actividades', $datos['id_actividades']);
		$this -> db -> UPDATE('actividades', $data);
		return $datos['id_actividades'];
	}

}
?>
