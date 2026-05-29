<?php
Class Perfiles extends CI_Model
{

 function get()
 {
   $this -> db -> select('id_perfiles, descripcion');
   $this -> db -> from('perfiles');
   $this -> db -> where('habilitado', '1');


   $query = $this -> db -> get();

   if($query -> num_rows() >= 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }
 }
 
 function bajaPerfiles($id_perfiles)
  {
 
    $this->db->where('id_perfiles',$id_perfiles);
    return $this->db->delete('perfiles');
  }

  function altaPerfiles($descripcion)
  {
    $data = array(

      'descripcion'        => $descripcion,

      );

    $this->db->INSERT('perfiles',$data);
  }

  function modificarPerfiles($id_perfiles,$descripcion)
  {
    $data = array(

      'id_perfiles'     => $id_perfiles,
      'descripcion'     => $descripcion

      );
    $this->db->WHERE('id_perfiles',$id_perfiles);
    $this->db->UPDATE('perfiles',$data);
  }
 
 
}
?>