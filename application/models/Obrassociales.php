<?php
class ObrasSociales extends CI_Model {
		function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    	function obtenerObrasSociales()
	{
		$this->db->where('habilitado', 1);
		$query = $this->db->GET('obras_sociales');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return array();
	}

	 function bajaObrasSociales($id_obras_sociales)
	{
		$data = array('habilitado' => 0);
		$this->db->where('id_obras_sociales', $id_obras_sociales);
		return $this->db->update('obras_sociales', $data);
	}

		function altaObraSocial($descripcion,$telefono,$contacto,$partido,$codigo_postal,$provincia,$direccion,$email,$localidad)
	{
		$data = array(

			'descripcion'	=> $descripcion,
			'direccion'		=> $direccion,
			'localidad'		=> $localidad,
			'partido'		=> $partido,
			'codigo_postal'	=> $codigo_postal,
			'provincia'		=> $provincia,
			'telefono'		=> $telefono,
			'contacto'		=> $contacto,
			'email'			=> $email

			);

		$this->db->INSERT('obras_sociales',$data);
	}

	function modificarObraSOcial($id_obras_sociales,$descripcion,$telefono,$contacto,$partido,$codigo_postal,$provincia,$direccion,$email,$localidad)
	{
		$data = array(

			'descripcion'	=> $descripcion,
			'direccion'		=> $direccion,
			'localidad'		=> $localidad,
			'partido'		=> $partido,
			'codigo_postal'	=> $codigo_postal,
			'provincia'		=> $provincia,
			'telefono'		=> $telefono,
			'contacto'		=> $contacto,
			'email'			=> $email

			);
		$this->db->WHERE('id_obras_sociales',$id_obras_sociales);
		$this->db->UPDATE('obras_sociales',$data);
	}

}