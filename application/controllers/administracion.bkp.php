<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administracion extends CI_Controller {

 function __construct()
 {
 	
   parent::__construct();
   $this->load->model('menus','',TRUE);
   $this->load->model('perfiles','',TRUE);
   $this->load->model('usuarios','',TRUE);
   $this->load->model('personas','',TRUE);
   $this->load->model('obrassociales','',TRUE);
   $this->load->model('alumnos','',TRUE);
 }
 

 function usuarios()
 {
   $this->load->helper(array('form'));
   $this->load->helper('html');
   $this->load->helper('url');
   $this->load->helper('language');
   $this->load->library('user_agent');
   
	
	if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
		redirect('/exploradornosoportado');
	else {
		$session_data = $this->session->userdata('logged_in');
     $data['username'] = $session_data['username'];
	   $data['menu']=$this->menus->imprimirMenu($session_data['id']);
	   $data['usuarios']=$this->usuarios->obtenerUsuarios();
	   $data['personas']=$this->personas->obtenerPersonas();
	   $this->load->view('header');
	   $this->load->view('menusuperior');
	   $this->load->view('adminusuarios',$data);
	   $this->load->view('footer');
   }
 }

 function altausuarios()
 {
   $this->load->helper(array('form'));
   $this->load->helper('html');
   $this->load->helper('url');
   $this->load->helper('language');
   $this->load->library('user_agent');
   
	
	if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
		redirect('/exploradornosoportado');
	else {
		$session_data = $this->session->userdata('logged_in');
     $data['username'] = $session_data['username'];
	   $data['menu']=$this->menus->imprimirMenu($session_data['id']);
	   $this->load->view('header');
	   $this->load->view('menusuperior');
	   $this->load->view('altausuarios',$data);
	   $this->load->view('footer');
   }
 }

 function obrassociales()
 {
   $this->load->helper(array('form'));
   $this->load->helper('html');
   $this->load->helper('url');
   $this->load->helper('language');
   $this->load->library('user_agent');
   
	
	if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
		redirect('/exploradornosoportado');
	else {
		$session_data = $this->session->userdata('logged_in');
     $data['username'] = $session_data['username'];
	   $data['menu']=$this->menus->imprimirMenu($session_data['id']);
	   $data['obrassociales']=$this->obrassociales->obtenerObrasSociales();
	   $this->load->view('header');
	   $this->load->view('menusuperior');
	   $this->load->view('adminobrassociales',$data);
	   $this->load->view('footer');
   }
 }

 function altaobrassociales()
 {
   $this->load->helper(array('form'));
   $this->load->helper('html');
   $this->load->helper('url');
   $this->load->helper('language');
   $this->load->library('user_agent');
   
	
	if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
		redirect('/exploradornosoportado');
	else {
		$session_data = $this->session->userdata('logged_in');
     $data['username'] = $session_data['username'];
	   $data['menu']=$this->menus->imprimirMenu($session_data['id']);
	   $this->load->view('header');
	   $this->load->view('menusuperior');
	   $this->load->view('altaobrassociales',$data);
	   $this->load->view('footer');
   }
 }

    function bajaobrassocial()
    {
        
        
        if ($this->input->post('id_obras_sociales'))
        {
 
          $id = $this->input->post('id_obras_sociales');
 
          $this->obrassociales->bajaObrasSociales($id_obras_sociales);
 
        }
 
    }

     function amobrassociales()
    {

           $this->load->helper(array('form'));
           $this->load->helper('html');
           $this->load->helper('url');
           $this->load->helper('language');
           $this->load->library('user_agent');
           $this->load->library(array('form_validation'));
           
          
          if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
            redirect('/exploradornosoportado');
          else {
             $session_data = $this->session->userdata('logged_in');
               $data['username'] = $session_data['username'];
             $data['menu']=$this->menus->imprimirMenu($session_data['id']);
             $data['obrassociales']=$this->obrassociales->obtenerObrasSociales();
             $this->load->view('header');
             $this->load->view('menusuperior');
             $this->load->view('altaobrassociales',$data);
             //$this->load->view('footer');

            
            $descripcion = $this->input->post('descripcion');
            $email = $this->input->post('email');
            $direccion = $this->input->post('direccion');
           $localidad = $this->input->post('localidad');
            $partido = $this->input->post('partido');
            $codigo_postal = $this->input->post('codigo_postal');
            $provincia = $this->input->post('provincia');
            $telefono = $this->input->post('telefono');
            $contacto = $this->input->post('contacto');
 
                         //si estamos editando
             if($this->input->post('id_obras_sociales'))
             {
 
                         $id = $this->input->post('id_obras_sociales');
                         $this->obrassociales->modificarObraSOcial($id_obras_sociales,$descripcion,$telefono,$contacto,$partido,$codigo_postal,$provincia,$direccion,$telefono,$email,$localidad);
 
             //si estamos agregando un usuario
             }else{


 
                         $this->obrassociales->altaObraSocial($descripcion,$telefono,$contacto,$partido,$codigo_postal,$provincia,$direccion,$telefono,$email,$localidad);
 
             }
                        
                         //en cualquier caso damos ok porque todo ha salido bien
                         //habría que hacer la comprobación de la respuesta del modelo

                        

 
            
        }
        
    }

 function alumnos()
 {
   $this->load->helper(array('form'));
   $this->load->helper('html');
   $this->load->helper('url');
   $this->load->helper('language');
   $this->load->library('user_agent');
   
	
	if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
		redirect('/exploradornosoportado');
	else {
		$session_data = $this->session->userdata('logged_in');
     $data['username'] = $session_data['username'];
	   $data['menu']=$this->menus->imprimirMenu($session_data['id']);
	   $data['alumnos']=$this->alumnos->obtenerAlumnos();
	   $this->load->view('header');
	   $this->load->view('menusuperior');
	   $this->load->view('adminalumnos',$data);
	   $this->load->view('footer');
   }
 }

 function altaalumnos()
 {
   $this->load->helper(array('form'));
   $this->load->helper('html');
   $this->load->helper('url');
   $this->load->helper('language');
   $this->load->library('user_agent');
   
	
	if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
		redirect('/exploradornosoportado');
	else {
	   $session_data = $this->session->userdata('logged_in');
       $data['username'] = $session_data['username'];
	   $data['menu']=$this->menus->imprimirMenu($session_data['id']);
	   $data['obrassociales']=$this->obrassociales->obtenerObrasSociales();
	   $this->load->view('header');
	   $this->load->view('menusuperior');
	   $this->load->view('altaalumnos',$data);
	   $this->load->view('footer');
   }
 }

    function amAlumnos()
    {

           $this->load->helper(array('form'));
           $this->load->helper('html');
           $this->load->helper('url');
           $this->load->helper('language');
           $this->load->library('user_agent');
           
          
          if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8)
            redirect('/exploradornosoportado');
          else {
             $session_data = $this->session->userdata('logged_in');
               $data['username'] = $session_data['username'];
             $data['menu']=$this->menus->imprimirMenu($session_data['id']);
             $data['obrassociales']=$this->obrassociales->obtenerObrasSociales();
             $this->load->view('header');
             $this->load->view('menusuperior');
             $this->load->view('altaalumnos',$data);
             $this->load->view('footer');
       

 

 
            $nombre = $this->input->post('nombre');
            $email = $this->input->post('email');
			$direccion = $this->input->post('direccion');
			$localidad = $this->input->post('localidad');
			$partido = $this->input->post('partido');
			$codigo_postal = $this->input->post('codigo_postal');
			$provincia = $this->input->post('provincia');
			$padre = $this->input->post('padre');
			$telefono = $this->input->post('telefono');
			$dni = $this->input->post('dni');
			$diagnostico = $this->input->post('diagnostico');
			$id_obras_sociales = $this->input->post('id_obras_sociales');
 
                         //si estamos editando
             if($this->input->post('id_alumnos'))
             {
 
                         $id = $this->input->post('id_alumnos');
                         $this->alumnos->modificarAlumno($id_alumnos,$nombre,$direccion,$localidad,$partido,
                         	                $codigo_postal,$provincia,$padre,$telefono,$email,$dni,$diagnostico,$id_obras_sociales);
 
             //si estamos agregando un usuario
             }else{
 
                         $this->alumnos->altaAlumno($nombre,$direccion,$localidad,$partido,$codigo_postal,$provincia,$padre,$telefono,$email,$dni,$id_obras_sociales,$diagnostico);
 
             }
                        
                         //en cualquier caso damos ok porque todo ha salido bien
                         //habría que hacer la comprobación de la respuesta del modelo

                        

 
            
        }
        
    }
}
 


?>