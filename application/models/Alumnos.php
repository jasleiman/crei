<?php
class Alumnos extends CI_Model {
	function __construct()
    {
        parent::__construct();
    }

    function get($id = 0) {
		$this -> load -> model('Datos', 'datos');
		$this -> load -> model('Alcance', 'alcance');
		$this -> db -> reset_query();
		$this -> db -> WHERE('alumnos.habilitado', 1);
		if ($id > 0) {
			$this -> db -> WHERE('alumnos.id_alumnos', $id);
		}
		$this -> alcance -> aplicar_filtro_alumnos('alumnos');
		$this -> db -> select('id_alumnos,UPPER(alumnos.nombre) as nombre,alumnos.direccion,alumnos.localidad,alumnos.partido,alumnos.codigo_postal,alumnos.provincia,alumnos.padre,alumnos.telefono,alumnos.email,alumnos.dni,alumnos.diagnostico,alumnos.id_personas,obras_sociales.id_obras_sociales,obras_sociales.descripcion,alumnos.id_coordinador,alumnos.servicio');
		$this -> db -> from('alumnos');
		$this -> db -> join('obras_sociales', 'alumnos.id_obras_sociales = obras_sociales.id_obras_sociales');
                $this->db->order_by('alumnos.nombre asc');

		$query = $this -> db -> get();
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();
			
			return $resultado;
		}
	}
	
	function getDesdePersona($id = 0) {
		$this -> load -> model('Alcance', 'alcance');
		$this -> db -> reset_query();
		$this -> db -> WHERE('alumnos.habilitado', 1);
		if ($id > 0) {
			if (!$this -> alcance -> puede_ver_maestra($id)) {
				return false;
			}
			$this -> db -> WHERE('alumnos.id_personas', (int) $id);
		} else {
			$this -> alcance -> aplicar_filtro_alumnos('alumnos');
		}
		$this -> db -> select('id_alumnos,alumnos.nombre,alumnos.direccion,alumnos.localidad,alumnos.partido,alumnos.codigo_postal,alumnos.provincia,alumnos.padre,alumnos.telefono,alumnos.email,alumnos.dni,alumnos.diagnostico,alumnos.id_personas,obras_sociales.id_obras_sociales,obras_sociales.descripcion,alumnos.id_coordinador,alumnos.servicio');
		$this -> db -> from('alumnos');
		$this -> db -> join('obras_sociales', 'alumnos.id_obras_sociales = obras_sociales.id_obras_sociales');
		$this->db->order_by('alumnos.nombre asc');

		$query = $this -> db -> get();
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();
			
			return $resultado;
		}
		else return false;
	}
	
	function getHabilitados($id = 0) {
		$this -> load -> model('Alcance', 'alcance');
		$this -> db -> reset_query();
		$this -> db -> WHERE('alumnos.habilitado', 1);
		$this -> alcance -> aplicar_filtro_alumnos('alumnos');
		$this -> db -> select('id_alumnos,alumnos.nombre,alumnos.direccion,alumnos.localidad,alumnos.partido,alumnos.codigo_postal,alumnos.provincia,alumnos.padre,alumnos.telefono,alumnos.email,alumnos.dni,alumnos.diagnostico,alumnos.id_personas,obras_sociales.id_obras_sociales,obras_sociales.descripcion,alumnos.id_coordinador,alumnos.servicio');
		$this -> db -> from('alumnos');
		$this -> db -> join('obras_sociales', 'alumnos.id_obras_sociales = obras_sociales.id_obras_sociales');
		$this->db->order_by('alumnos.nombre asc');

		$query = $this -> db -> get();
	
		if ($query -> num_rows() > 0) {
			$resultado = $query -> result();
			
			return $resultado;
		}
		else return false;
	}

	function alta($datos)
	{
		$data = array(

			'nombre'			=> $datos['nombre'],
			'direccion'			=> $datos['direccion'],
			'localidad'			=> $datos['localidad'],
			'partido'			=> $datos['partido'],
			'codigo_postal'		=> $datos['codigo_postal'],
			'provincia'			=> $datos['provincia'],
			'padre'				=> $datos['padre'],
			'telefono'			=> $datos['telefono'],
			'email'				=> $datos['email'],
			'dni' 				=> $datos['dni'],
			'diagnostico'		=> $datos['diagnostico'],
			'id_obras_sociales' => $datos['id_obras_sociales'],
			'id_coordinador' => $datos['id_coordinador'],
			'servicio' => $datos['servicio'],
			
			'id_personas' => $datos['id_personas']

			);

		$this->db->INSERT('alumnos',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	function modificar($datos)
	{
		
		$data = array(

			'nombre'			=> $datos['nombre'],
			'direccion'			=> $datos['direccion'],
			'localidad'			=> $datos['localidad'],
			'partido'			=> $datos['partido'],
			'codigo_postal'		=> $datos['codigo_postal'],
			'provincia'			=> $datos['provincia'],
			'padre'				=> $datos['padre'],
			'telefono'			=> $datos['telefono'],
			'email'				=> $datos['email'],
			'dni' 				=> $datos['dni'],
			'diagnostico'		=> $datos['diagnostico'],
			'id_obras_sociales' => $datos['id_obras_sociales'],
			'id_coordinador' => $datos['id_coordinador'],
			'servicio' => $datos['servicio'],
			'id_personas' => $datos['id_personas']

			);
		
		$this->db->WHERE('id_alumnos',$datos['id_alumnos']);
		$this->db->UPDATE('alumnos',$data);
		
	}
	
	function baja($id_alumnos) {
		$this -> db -> reset_query();
		try {
			$data = array('habilitado' => 0);
			$this -> db -> WHERE('id_alumnos', $id_alumnos);
			$this -> db -> UPDATE('alumnos', $data);
			return true;
		} catch (Exception $e) {
			log_message('error', $e -> getMessage());
		}

	}
	
	function getAlumnosPlanes(){
		$this->load->model('Alcance', 'alcance');
		$this->db->reset_query();
		$this->db->select('alumnos.nombre,obras_sociales.descripcion,planes.escuela,planes.acta_acuerdo,planes.fecha_inicio,planes.fecha_fin');
		$this->db->from('alumnos');
		$this->db->join('obras_sociales','alumnos.id_obras_sociales=obras_sociales.id_obras_sociales','left');
		$this->db->join('planes','alumnos.id_alumnos=planes.id_planes','left');
		$this->alcance->aplicar_filtro_alumnos('alumnos');
		$this->db->where('planes.fecha_fin > ',date('Y-m-d',now()));
		$this->db->where('planes.habilitado','1');
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
			return $query->result();
		}
	}

}
