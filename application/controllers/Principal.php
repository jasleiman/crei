<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Principal extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('menus', '', TRUE);
        $this->load->model('perfiles', '', TRUE);
        $this->load->model('actividades', '', TRUE);
        $this->load->model('tipo_actividades', '', TRUE);
        $this->load->model('personas', '', TRUE);
        $this->load->model('alumnos', '', TRUE);
        $this->load->model('horas', '', TRUE);
        $this->load->model('alcance', '', TRUE);
    }

    function index() {
        $session_data = $this->session->userdata('logged_in');
        $funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
        if ($this->session->userdata('logged_in') and $this->menus->estaHabilitado($funcion, $session_data['id'])) {

            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $data['menu'] = $this->menus->imprimirMenu($session_data['id']);
            $id_personas = $session_data['id_personas'];
            $data['actividades'] = $this->actividades->obtenerActividades($id_personas);
            $this->load->helper(array('form'));
            $this->load->helper('html');
            $this->load->helper('url');
            $this->load->helper('language');
            $this->load->library('user_agent');

            if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8) {
                redirect('/exploradornosoportado');
            } else {
                $session_data = $this->session->userdata('logged_in');
                $data['username'] = $session_data['username'];
                $data['menu'] = $this->menus->imprimirMenu($session_data['id']);
                $data['tipo_actividades'] = $this->tipo_actividades->get();
                $data['personas'] = $this->personas->getMI();
                if (!$data['personas']) {
                    $data['personas'] = array();
                }
                $data['es_maestra_apoyo'] = $this->alcance->es_maestra_apoyo();

                if ($this->input->get('id_actividades') == true) {
                    $id_actividades = $this->input->get('id_actividades');
                    $data['horas'] = $this->horas->get($id_actividades);
                    if (!$data['horas']) {
                        redirect('principal', 'refresh');
                    }
                    $persona = $this->personas->get($data['horas'][0]->id_personas);
                    $personaAR = (object)['id_personas' => $persona[0]->id_personas, 'nombre' => $persona[0]->nombre];
                    array_unshift($data['personas'], $personaAR);
                    $data['alumnos'] = $this->alumnos->getDesdePersona($data['horas'][0]->id_personas);
                    if ($data['alumnos'] === false) {
                        $datosAlumnoActivida = [
                            'id_alumnos' => $data['horas'][0]->id_alumnos,
                            'nombre' => $data['horas'][0]->alumno];
                        $data['alumnos'] = [(object)$datosAlumnoActivida];
                    }
                } else {
                    if ($data['es_maestra_apoyo']) {
                        $data['alumnos'] = $this->alumnos->getHabilitados();
                        if ($data['alumnos'] === false) {
                            $data['alumnos'] = array();
                        }
                    } elseif (empty($data['personas']) || !isset($data['personas'][0]->id_personas)) {
                        $id_personas_filtro = $id_personas;
                        $data['alumnos'] = $this->alumnos->getDesdePersona($id_personas_filtro);
                    } else {
                        $id_personas_filtro = $data['personas'][0]->id_personas;
                        $data['alumnos'] = $this->alumnos->getDesdePersona($id_personas_filtro);
                    }
                }
                $this->load->view('header');
                $this->load->view('menusuperior');
                $this->load->view('cargahoras', $data);
                $this->load->view('footer');
            }
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    function cargaActividades() {
        $this->load->helper(array('form'));
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->helper('language');
        $this->load->library('user_agent');
        $session_data = $this->session->userdata('logged_in');
        $funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
        if ($this->session->userdata('logged_in') and $this->menus->estaHabilitado($funcion, $session_data['id'])) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $post = $this->input->post();
            if ($this->alcance->es_maestra_apoyo()) {
                $post['id_personas'] = (int) $session_data['id_personas'];
            }

            $resultado = $this->actividades->cargaActividades($post);
            if ($resultado) {
                $msg = "Tu carga ha sido exitosa. Muchas gracias!";
                $type = "success";
                $mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
            } else {
                $msg = "Ya existen información cargada para ese alumno en esa fecha y hora y para el mismo docente";
                $type = "error";
                $mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}"';
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect('principal', 'refresh');
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    function cargarAteneo() {
        $session_data = $this->session->userdata('logged_in');
        $funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
        if (!$this->session->userdata('logged_in') || !$this->alcance->usa_carga_horas_equipo()
            || !$this->menus->estaHabilitado($funcion, $session_data['id'])) {
            redirect('login', 'refresh');
        }

            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $data['menu'] = $this->menus->imprimirMenu($session_data['id']);
            $id_personas = $session_data['id'];
            $data['actividades'] = $this->actividades->obtenerActividades($id_personas);
            $this->load->helper(array('form'));
            $this->load->helper('html');
            $this->load->helper('url');
            $this->load->helper('language');
            $this->load->library('user_agent');

            if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8) {
                redirect('/exploradornosoportado');
            } else {
                $session_data = $this->session->userdata('logged_in');
                $data['username'] = $session_data['username'];
                $data['menu'] = $this->menus->imprimirMenu($session_data['id']);

                $data['tipo_actividades'] = $this->tipo_actividades->get();
                $data['personas'] = $this->personas->getMI();
                if (empty($data['personas'])) {
                    $data['personas'] = array();
                }

                if (!empty($data['personas'])) {
                    $data['alumnos'] = $this->alumnos->getDesdePersona($data['personas'][0]->id_personas);
                } else {
                    $data['alumnos'] = array();
                }
                $data['alumnostodos'] = $this->alumnos->get();
                if ($data['alumnostodos'] === false) {
                    $data['alumnostodos'] = array();
                }
                $this->load->view('header');
                $this->load->view('menusuperior');
                $this->load->view('cargarhorasateneo', $data);
                $this->load->view('footer');
            }
    }

    function grabarAteneo() {
        $this->load->helper(array('form'));
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->helper('language');
        $this->load->library('user_agent');
        $session_data = $this->session->userdata('logged_in');
        $funcion_carga = strtolower(__CLASS__) . '/cargarateneo';
        if (!$this->session->userdata('logged_in') || !$this->alcance->usa_carga_horas_equipo()
            || !$this->menus->estaHabilitado($funcion_carga, $session_data['id'])) {
            redirect('login', 'refresh');
        }

            $session_data = $this->session->userdata('logged_in');
            $id_personas_carga = (int) $session_data['id_personas'];
            if ($id_personas_carga <= 0) {
                redirect('principal/cargarateneo', 'refresh');
            }

            $ids_alumnos = $this->input->post('id_alumnos_grupo');
            if (!is_array($ids_alumnos) || empty($ids_alumnos)) {
                $this->_flash_grabar_ateneo('Seleccione al menos un alumno.', 'error');
                redirect('principal/cargarateneo', 'refresh');
            }

            $fecha = trim((string) $this->input->post('fecha'));
            $hora_inicio = trim((string) $this->input->post('hora_inicio'));
            $hora_fin = trim((string) $this->input->post('hora_fin'));
            $id_tipo_actividades = (int) $this->input->post('id_tipo_actividades');
            if ($fecha === '' || $hora_inicio === '' || $hora_fin === '' || $id_tipo_actividades <= 0) {
                $this->_flash_grabar_ateneo('Complete fecha, hora de inicio, hora de fin y actividad.', 'error');
                redirect('principal/cargarateneo', 'refresh');
            }

            $act_base = array(
                'fecha' => $fecha,
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin,
                'id_tipo_actividades' => $id_tipo_actividades,
                'observaciones' => $this->input->post('observaciones'),
            );

            $guardadas = 0;
            $rechazadas = 0;
            foreach ($ids_alumnos as $id_alumnos) {
                $id_alumnos = (int) $id_alumnos;
                if ($id_alumnos <= 0) {
                    continue;
                }
                if (!$this->alcance->puede_ver_alumno($id_alumnos)) {
                    $rechazadas++;
                    continue;
                }

                $act = $act_base;
                $act['id_alumnos'] = $id_alumnos;
                $act['id_personas'] = $id_personas_carga;
                if ($this->actividades->cargaActividades($act)) {
                    $guardadas++;
                } else {
                    $rechazadas++;
                }
            }

            if ($guardadas > 0) {
                $msg = 'Se registraron ' . $guardadas . ' carga(s) a tu nombre.';
                if ($rechazadas > 0) {
                    $msg .= ' ' . $rechazadas . ' fila(s) no se guardaron (duplicado o alumno no válido).';
                }
                $type = 'success';
            } else {
                $msg = 'No se guardó ninguna carga. Verifique fecha, hora y alumnos seleccionados.';
                $type = 'error';
            }
            $mensaje = '{&quot;text&quot;:&quot;' . str_replace('"', "'", $msg) . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}';
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect('principal/cargarateneo', 'refresh');
    }

    protected function _flash_grabar_ateneo($texto, $type) {
        $msg = str_replace(array('"', "\n", "\r"), array("'", ' ', ' '), $texto);
        $mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}';
        $this->session->set_flashdata('mensaje', $mensaje);
    }

    public function selectAlumno() {
        //set selected country id from POST
        $id_personas = $this->input->post('id', TRUE);
        //run the query for the cities we specified earlier
        $data['alumnos'] = $this->alumnos->getDesdePersona($id_personas);
        $output = null;

        if ($data['alumnos'] <> false) {
            foreach ($data['alumnos'] as $row) {

                $output .= "<option value='" . $row->id_alumnos . "'>" . $row->nombre . "</option>";
                next($data['alumnos']);
            }
            echo $output;
        } else {
            $output = "<option value='NULL'>Ninguno</option>";
            echo $output;
        }
    }

    public function enviarMailRecordatorio() {
        $this->load->library('email');
        $sincarga = $this->personas->getNoCargadas();

        foreach ($sincarga as $email_a) {
            if (filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
                $mensaje = '<p>Hola, recordá por favor de cargar las novedades de la semana en el sistema <a href="http://www.crei.edu.ar/integracion">http://www.crei.edu.ar/integracion</a></p><p>
Muchas gracias</p>';
                $asunto = 'Recordatorio de carga de datos en el sistema ';

                $mensaje_html = $this->email->full_html($asunto, $mensaje);
                $result = $this->email
                        ->from('avisosintegracion@crei.edu.ar', 'Sistema de gestión de integración escolar')
                        ->reply_to('avisosintegracion@crei.edu.ar')    // Optional, an account where a human being reads.
                        ->to($email_a)
                        ->bcc('juan@setiar.com.ar')
                        ->bcc('eliana.puerta@escuela.crei.edu.ar')
                        ->bcc('gabriela.dropulich@escuela.crei.edu.ar')
                        ->subject($asunto)
                        ->message($mensaje_html)
                        ->send();
            }
        }
    }

    public function enviarMailRecordatorioTest() {
        $this->load->library('email');
        $sincarga = $this->personas->getNoCargadas();

        foreach ($sincarga as $email_a) {
            if (filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
                $mensaje = '<p>Hola, recordá por favor de cargar las novedades de la semana en el sistema <a href="http://www.crei.edu.ar/integracion">http://www.crei.edu.ar/integracion</a></p><p>
Muchas gracias</p>';
                $asunto = 'Recordatorio de carga de datos en el sistema ';

                $mensaje_html = $this->email->full_html($asunto, $mensaje);
                $result = $this->email
                        ->from('avisosintegracion@crei.edu.ar', 'Sistema de gestión de integración escolar')
                        ->reply_to('avisosintegracion@crei.edu.ar')    // Optional, an account where a human being reads.
                        ->bcc('juan@setiar.com.ar')
                        ->subject($asunto)
                        ->message($mensaje_html)
                        ->send();
            }
        }
    }

    public function enviarMailRecordatorioUltimoDia() {
        $this->load->library('email');
        $sincarga = $this->personas->getNoCargadas();

        foreach ($sincarga as $email_a) {
            if (filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
                $mensaje = '<p>Hola, recordá que hoy es el último día para realizar las cargas de horas del mes. <a href="http://www.crei.edu.ar/integracion">http://www.crei.edu.ar/integracion</a></p><p>
Muchas gracias</p>';
                $asunto = 'Recordatorio de carga de datos en el sistema ';

                $mensaje_html = $this->email->full_html($asunto, $mensaje);
                $result = $this->email
                        ->from('avisosintegracion@crei.edu.ar', 'Sistema de gestión de integración escolar')
                        ->reply_to('avisosintegracion@crei.edu.ar')    // Optional, an account where a human being reads.
                        ->to($email_a)
                        ->bcc('juan@setiar.com.ar')
                        ->bcc('eliana.puerta@escuela.crei.edu.ar')
                        ->bcc('gabriela.dropulich@escuela.crei.edu.ar')
                        ->subject($asunto)
                        ->message($mensaje_html)
                        ->send();
            }
        }
    }

    public function enviarMailMensual() {
        $this->load->library('email');
        $perso = $this->personas->getEnvioInforme();
        //var_dump($perso);
        foreach ($perso as $per) {
            if (filter_var($per->email, FILTER_VALIDATE_EMAIL)) {

                $datos['mes'] = date('m', strtotime('first day of last month'));
                $datos['anio'] = date('Y', strtotime('first day of last month'));
                ;
                $datos['id_personas'] = $per->id_personas;

                $this->actividades->exportarExcelDetalle($datos, 'si');
                $mensaje = '<p>Hola, te adjuntamos el informe de lo que cargaste durante el último mes</p>';
                $asunto = 'Informe mensual de carga de datos';
                $mensaje_html = $this->email->full_html($asunto, $mensaje);
                $result = $this->email
                        ->from('avisosintegracion@crei.edu.ar', 'Sistema de gestión de integración escolar')
                        ->reply_to('avisosintegracion@crei.edu.ar')    // Optional, an account where a human being reads.
                        // ->to($per->email)
                        ->bcc('juan@setiar.com.ar')
                        ->subject($asunto)
                        ->message($mensaje_html)
                        ->attach('actividadesdetalle' . $datos['anio'] . $datos['mes'] . '_' . $datos['id_personas'] . '.xlsx')
                        ->send();
                $this->email->clear(true);
            }
        }
    }
}
