<?php
class Usuarios extends CI_Model {
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

 	function encriptar($string){
			$codigo="@#€¬MjhM98&/k~'(_@";
	  		$salt = md5($string.$codigo);
 			$string = md5("$salt$string$salt");
			return $string;
	}
		
	function login($username, $password)
	{
		log_message('info', $username);
		$this -> db -> select('id_usuarios, nombre, clave, id_personas,id_perfiles');
		$this -> db -> from('usuarios');
		$this -> db -> where('nombre', strtolower($username));
		$this -> db -> where('clave', $password);
		$this -> db -> where('habilitado', 1);
		$this -> db -> limit(1);
	
		$query = $this -> db -> get();
		
		if($query -> num_rows() == 1)
		{
			$resultado=$query->result();
			$fecha=date('Y-m-d H:i:s');
			//guardo la fecha del último inicio de sesión
			$row = $query->first_row();
			$data = array(

			'fecha_ultimo_inicio'	=>  $fecha);
			$this->db->WHERE('id_usuarios',$row->id_usuarios);
			$this->db->UPDATE('usuarios',$data);
			
			return $resultado;
		}
		else
		{
			return false;
		}
	}
 
	function alta($datos){
		$fechaalta=date('Y-m-d H:i:s');
		$data = array('nombre' => $datos['email'], 'clave' => $datos['clave'], 'id_perfiles' => $datos['id_perfiles'], 'id_personas' => $datos['id_personas'], 'habilitado' => '1', 'fecha_alta' => $fechaalta);
		$this -> db -> INSERT('usuarios', $data);
		
	}
	
	function modificar($datos){
		
		if ($datos['clave'] <> '******') 
			$data = array('nombre' => $datos['email'], 'clave' => $datos['clave'], 'id_perfiles' => $datos['id_perfiles'], 'id_personas' => $datos['id_personas']);
		else 
			$data = array('nombre' => $datos['email'], 'id_perfiles' => $datos['id_perfiles'], 'id_personas' => $datos['id_personas']);
		$this -> db -> WHERE ('id_usuarios',$datos['id_usuarios']);
		$this -> db -> UPDATE('usuarios', $data);
		
	}
		
	function cambiarClave($claveactual,$clavenueva,$id_usuarios){
		//da de alta un usuario si es que no existe o si no modifica los datos
			
			$claveactual=$this->encriptar($claveactual);
			$clavenueva=$this->encriptar($clavenueva);
			
			$query2 = 'SELECT * FROM usuarios';
			$query2 .= ' where id_usuarios='.$id_usuarios;
			$query2 .= ' and clave=\''.$claveactual.'\'';
			
			//echo $query;
			$query=$this -> db -> query($query2);
			if (!$query) {
				return false;
			}
			else {	
				if($query -> num_rows() == 1)
				{
					$query2 = 'UPDATE usuarios';
					$query2 .= ' SET clave=\''.$clavenueva.'\'';
					$query2 .= ' where id_usuarios='.$id_usuarios;
					
					//echo $query;
					$query=$this -> db -> query($query2);
					return true;
				}
				else
				{
					return false;
				}
			}
			
		}

	function obtenerUsuarios()
	{
		$query = $this->db->GET('usuarios');
		if($query->num_rows()>0)
		{
			return $query->result();
		}
	}
	
	function getDesdePersona($id)
	{
		$this->db->WHERE('id_personas',$id);
		$query = $this->db->GET('usuarios');
		if($query->num_rows()>0)
		{
			return $query->result();
		}
	}
	
	function eliminar($id_usuarios)
	{
		$data = array(

			'habilitado'	=> '0' );
		$this->db->WHERE('id_usuarios',$id_usuarios);
		$this->db->UPDATE('usuarios',$data);
	}
}
?>