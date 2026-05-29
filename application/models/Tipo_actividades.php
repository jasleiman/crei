<?php
class Tipo_actividades extends CI_Model {
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function get($id=0)
	{
		$this->db->reset_query();
		$this->db->WHERE('habilitado',1);
		if ($id > 0) {
			$this->db->WHERE('id_tipo_actividades',$id);
		}
		$this->db->order_by('descripcion');
		$query = $this->db->get('tipo_actividades');
		if($query->num_rows()>0)
		{
			$resultado=$query->result();
			if ($id==0) {
				foreach ($resultado as $row) {
					if ($row->directa==1) $row->directa='DIRECTA';
					else $row->directa='INDIRECTA';
				}
			}
			return $resultado;
		}
	}

	function alta($descripcion,$directa)
	{
		$data = array(

			'descripcion'			=> $descripcion,
			'directa'			=> $directa
			

			);

		$this->db->INSERT('tipo_actividades',$data);
	}

	function modificar($id_tipo_actividades,$descripcion,$directa)
	{
		$data = array(

			'descripcion'			=> $descripcion,
			'directa'			=> $directa
			
			
			);
		$this->db->WHERE('id_tipo_actividades',$id_tipo_actividades);
		$this->db->UPDATE('tipo_actividades',$data);
	}
	
	function baja($id_tipo_actividades)
	{
		$this->db->reset_query();
		//verifico si no hay ninguna actividad con este tipo de actividades asignada
		$this->db->WHERE('id_tipo_actividades',$id_tipo_actividades);
		$query = $this->db->get('actividades');
		if($query->num_rows()>0)
		{
				return false;
		}
		else {
			$data = array(
	
				'habilitado'			=> 0
			
				);
			$this->db->WHERE('id_tipo_actividades',$id_tipo_actividades);
			$this->db->UPDATE('tipo_actividades',$data);
			return true;
		}
	}

}