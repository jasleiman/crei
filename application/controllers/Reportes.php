<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reportes extends CI_Controller
{

	private $excel;

	/** Año del filtro: siempre el año en curso. */
	protected function anio_en_curso()
	{
		return (int) date('Y');
	}

	function __construct()
	{
		parent::__construct();

		$this->load->model('menus', '', TRUE);
		$this->load->model('perfiles', '', TRUE);
		$this->load->model('actividades', '', TRUE);
		$this->load->model('tipo_actividades', '', TRUE);
		$this->load->model('personas', '', TRUE);
		$this->load->model('datos', '', TRUE);
		$this->excel = new Spreadsheet();
	}

	function horas()
	{
		$session_data = $this->session->userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this->session->userdata('logged_in') and $this->menus->estaHabilitado($funcion, $session_data['id'])) {

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
			$this->load->model('alumnos', '', TRUE);

			if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8) {
				redirect('/exploradornosoportado');
			} else {
				$session_data = $this->session->userdata('logged_in');
				$data['username'] = $session_data['username'];
				$data['menu'] = $this->menus->imprimirMenu($session_data['id']);
				$data['alumnos'] = $this->alumnos->getHabilitados();
				$data['tipo_actividades'] = $this->tipo_actividades->get();
				$mes = $this->input->get('mes');
				$anio = $this->anio_en_curso();
				$servicio = $this->input->get('servicio');
				if (isset($mes) == false) {
					$mes = date('m');
				}
				if (isset($servicio) == false) {
					$servicio = 'EP';
				}
				$data['datos'] = $this->actividades->getActividades($mes, $anio, $servicio);

				$data['mes'] = $mes;
				$data['anio'] = $anio;
				$data['servicio'] = $servicio;
				$this->load->view('header');
				$this->load->view('menusuperior');
				$this->load->view('reportes/alumnosplanes', $data);
				$this->load->view('footer');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function detalle()
	{
		$session_data = $this->session->userdata('logged_in');
		$data['username'] = $session_data['username'];
		$data['menu'] = $this->menus->imprimirMenu($session_data['id']);
		$alumno = $this->input->get('alumno');
		$mes = $this->input->get('mes');
		$anio = $this->anio_en_curso();
		$servicio = $this->input->get('servicio');
		$data['actividades'] = $this->actividades->getDetalle($alumno, $mes, $anio, 0, false, $servicio);
		$this->load->view('header');
		$this->load->view('menusuperior');
		$this->load->view('reportes/detallealumno', $data);
		$this->load->view('footer');
		unset($_POST);
	}

	function detallepersonas()
	{
		$session_data = $this->session->userdata('logged_in');
		$data['username'] = $session_data['username'];
		$data['menu'] = $this->menus->imprimirMenu($session_data['id']);
		$id_personas = $this->input->get('id_personas');
		$mes = $this->input->get('mes');
		$anio = $this->anio_en_curso();

		$data['datos'] = $this->actividades->getDetallePersona($mes, $anio, $id_personas);
		$data['id_personas'] = $id_personas;
		$data['mes'] = $mes;
		$data['anio'] = $anio;
		$this->load->view('header');
		$this->load->view('menusuperior');
		$this->load->view('reportes/detallepersonas', $data);
		$this->load->view('footer');
		unset($_POST);
	}

	function porPersona()
	{
		$session_data = $this->session->userdata('logged_in');
		$funcion = strtolower(__CLASS__) . '/' . __FUNCTION__;
		if ($this->session->userdata('logged_in')) {

			$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['menu'] = $this->menus->imprimirMenu($session_data['id']);


			$this->load->helper(array('form'));
			$this->load->helper('html');
			$this->load->helper('url');
			$this->load->helper('language');
			$this->load->library('user_agent');
			$this->load->model('alumnos', '', TRUE);

			if ($this->agent->browser() == 'Internet Explorer' and $this->agent->version() <= 8) {
				redirect('/exploradornosoportado');
			} else {
				$session_data = $this->session->userdata('logged_in');
				$data['username'] = $session_data['username'];
				$data['menu'] = $this->menus->imprimirMenu($session_data['id']);
				$data['personas'] = $this->personas->obtenerPersonas();
				$mes = $this->input->get('mes');
				$anio = $this->anio_en_curso();
				$id_personas = $this->input->get('id_personas');

				if (isset($mes) == false) {
					$mes = date('m');
				}
				if (isset($id_personas) == false) {
					$id_personas = 0;
				}

				$data['datos'] = $this->actividades->getActividadesPorPersona($mes, $anio, $id_personas);

				$data['mes'] = $mes;
				$data['anio'] = $anio;
				$data['id_personas'] = $id_personas;
				$this->load->view('header');
				$this->load->view('menusuperior');
				$this->load->view('reportes/personas', $data);
				$this->load->view('footer');
			}
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function exportarExcel()
	{
		//load our new PHPExcel library

		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Actividades');

		// load database
		$this->load->database();

		// load model
		$mes = $this->input->get('mes');
		$anio = $this->anio_en_curso();
		$id_personas = $this->input->get('id_personas');

		if (isset($mes) == false) {
			$mes = date('m');
		}
		if (isset($id_personas) == false) {
			$id_personas = 0;
		}

		$datos = $this->actividades->getActividadesPorPersona($mes, $anio, $id_personas, true);
		$salida = array();
		$i = 0;
		foreach ($datos as $data) {
			$salida[$i]['mes'] = $mes;
			$salida[$i]['anio'] = $anio;
			$salida[$i]['nombre'] = $data['nombre'];
			$salida[$i]['horas'] = $data['horas'];
			$i++;
		}
		$header = array('Mes', 'Año', 'Persona', 'Horas trabajadas');

		$this->excel->getActiveSheet()->fromArray($header, NULL, 'A1');
		// read data to active sheet
		$this->excel->getActiveSheet()->fromArray($salida, null, 'A2');
		$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);


		$filename = 'actividades'; //save our workbook as this file name

		$writer = new Xlsx($this->excel); // instantiate Xlsx
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // generate excel file
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');	// download file

	}

	function exportarExcelDetalle()
	{
		$datos['mes'] = $this->input->get('mes');
		$datos['anio'] = $this->anio_en_curso();
		$datos['id_personas'] = $this->input->get('id_personas');
		$this->actividades->exportarExcelDetalle($datos, 'no');
	}

	function exportarExcelDetalleAlumno()
	{
		//load our new PHPExcel library

		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Actividades');

		// load database
		$this->load->database();

		// load model
		$mes = $this->input->get('mes');
		$anio = $this->anio_en_curso();
		$id_personas = $this->input->get('id_personas');
		$alumno = $this->input->get('alumno');
		$servicio = $this->input->get('servicio');
		if (isset($mes) == false) {
			$mes = date('m');
		}
		if (isset($id_personas) == false) {
			$id_personas = 0;
		}
		if (isset($servicio) == false) {
			$servicio = 'EP';
		}

		$datos = $this->actividades->getDetalle($alumno, $mes, $anio, 0, true, $servicio);
		$salida = array();
		$i = 0;
		foreach ($datos as $data) {
			$salida[$i]['persona'] = $data['persona'];
			$salida[$i]['alumno'] = $data['alumno'];
			$salida[$i]['fecha'] = date('d-m-Y', strtotime($data['fecha_inicio']));
			$salida[$i]['hora_inicio'] = date('H:i', strtotime($data['fecha_inicio']));
			$salida[$i]['hora_fin'] = date('H:i', strtotime($data['fecha_fin']));

			$salida[$i]['tipo_actividades'] = $data['tipo_actividades'];
			$salida[$i]['observaciones'] = $data['observaciones'];
			$i++;
		}
		$header = array('Persona', 'Alumno', 'Fecha', 'Hora Inicio', 'Hora Fin', 'Actividad', 'Observaciones');

		$this->excel->getActiveSheet()->fromArray($header, NULL, 'A1');
		// read data to active sheet
		$this->excel->getActiveSheet()->fromArray($salida, null, 'A2');
		$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);


		$filename = 'actividadesdetalle'; // set filename for excel file to be exported
		$writer = new Xlsx($this->excel); // instantiate Xlsx
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // generate excel file
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');	// download file

	}

	function AddPlayTime(...$times)
	{
		$minutes = 0;
		foreach ($times as $time) {
			if ($time === null || $time === '') {
				$time = '00:00';
			}
			$partes = explode(':', (string) $time);
			$hour = isset($partes[0]) ? (int) $partes[0] : 0;
			$minute = isset($partes[1]) ? (int) $partes[1] : 0;
			$minutes += $hour * 60 + $minute;
		}

		$hours = floor($minutes / 60);
		$minutes = $minutes % 60;

		return sprintf('%02d:%02d', $hours, $minutes);
	}
	function exportarExcelDetalleTodosAlumno()
	{
		//load our new PHPExcel library

		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Actividades');

		// load database
		$this->load->database();

		// load model
		$mes = $this->input->get('mes');
		$anio = $this->anio_en_curso();
		$id_personas = $this->input->get('id_personas');
		$servicio = $this->input->get('servicio');

		if (isset($mes) == false) {
			$mes = date('m');
		}
		if (isset($servicio) == false) {
			$servicio = 'PRIMARIO';
		}

		if (isset($id_personas) == false) {
			$id_personas = 0;
		}
		$datos = $this->actividades->getTotales(0, $mes, $anio, 0, true, $servicio);
		$salida = array();
		$temporal = array();
		$hor = array();
		$i = 0;
		foreach ($datos as $data) {
			$temporal[$data['alumno']]['alumno'] = $data['alumno'];
			if ((int) $data['id_tipo_actividades'] === 8) {
				if (!isset($temporal[$data['alumno']]['horas_ma'])) {
					$temporal[$data['alumno']]['horas_ma'] = '00:00';
				}
				$temporal[$data['alumno']]['horas_ma'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_ma']);
				continue;
			}
			switch ($data['id_clases_profesionales']) {
				case 1:
					if (!isset($temporal[$data['alumno']]['horas_mi'])) $temporal[$data['alumno']]['horas_mi'] = '00:00';
					$temporal[$data['alumno']]['horas_mi'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_mi']);

					break;
				case 2:
					if (!isset($temporal[$data['alumno']]['horas_eq'])) $temporal[$data['alumno']]['horas_eq'] = '00:00';
					$temporal[$data['alumno']]['horas_eq'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_eq']);

					break;
				case 3:
					if (!isset($temporal[$data['alumno']]['horas_ma'])) $temporal[$data['alumno']]['horas_ma'] = '00:00';
					$temporal[$data['alumno']]['horas_ma'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_ma']);

					break;

				default:

					break;
			}
			switch ($data['id_profesionales']) {
				case 1:
					if (!isset($temporal[$data['alumno']]['horas_ae'])) $temporal[$data['alumno']]['horas_ae'] = '00:00';
					if (!isset($temporal[$data['alumno']]['horas_ae_sup'])) $temporal[$data['alumno']]['horas_ae_sup'] = '00:00';
					if ($data['id_tipo_actividades'] <> 7)
						$temporal[$data['alumno']]['horas_ae'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_ae']);
					else $temporal[$data['alumno']]['horas_ae_sup'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_ae_sup']);
					break;
				case 9:

					if (!isset($temporal[$data['alumno']]['horas_dir'])) $temporal[$data['alumno']]['horas_dir'] = '00:00';

					if (!isset($temporal[$data['alumno']]['horas_dir_sup'])) $temporal[$data['alumno']]['horas_dir_sup'] = '00:00';
					if ($data['id_tipo_actividades'] <> 7)
						$temporal[$data['alumno']]['horas_dir'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_dir']);
					else $temporal[$data['alumno']]['horas_dir_sup'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_dir_sup']);
					break;
				case 8:
					if (!isset($temporal[$data['alumno']]['horas_psi'])) $temporal[$data['alumno']]['horas_psi'] = '00:00';
					if (!isset($temporal[$data['alumno']]['horas_psi_sup'])) $temporal[$data['alumno']]['horas_psi_sup'] = '00:00';
					if ($data['id_tipo_actividades'] <> 7)
						$temporal[$data['alumno']]['horas_psi'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_psi']);
					else $temporal[$data['alumno']]['horas_psi_sup'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_psi_sup']);
					break;
				case 12:
					if (!isset($temporal[$data['alumno']]['horas_fon'])) $temporal[$data['alumno']]['horas_fon'] = '00:00';
					if (!isset($temporal[$data['alumno']]['horas_fon_sup'])) $temporal[$data['alumno']]['horas_fon_sup'] = '00:00';
					if ($data['id_tipo_actividades'] <> 7)
						$temporal[$data['alumno']]['horas_fon'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_fon']);
					else $temporal[$data['alumno']]['horas_fon_sup'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_fon_sup']);
					break;
				case 13:
					if (!isset($temporal[$data['alumno']]['horas_as'])) $temporal[$data['alumno']]['horas_as'] = '00:00';
					if (!isset($temporal[$data['alumno']]['horas_as_sup'])) $temporal[$data['alumno']]['horas_as_sup'] = '00:00';
					if ($data['id_tipo_actividades'] <> 7)
						$temporal[$data['alumno']]['horas_as'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_as']);
					else $temporal[$data['alumno']]['horas_as_sup'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_as_sup']);
					break;
				case 14:
					if (!isset($temporal[$data['alumno']]['horas_psico'])) $temporal[$data['alumno']]['horas_psico'] = '00:00';
					if (!isset($temporal[$data['alumno']]['horas_psico_sup'])) $temporal[$data['alumno']]['horas_psico_sup'] = '00:00';
					if ($data['id_tipo_actividades'] <> 7)
						$temporal[$data['alumno']]['horas_psico'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_psico']);
					else $temporal[$data['alumno']]['horas_psico_sup'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_psico_sup']);
					break;
				case 14:
					if (!isset($temporal[$data['alumno']]['horas_vice'])) $temporal[$data['alumno']]['horas_vice'] = '00:00';
					if (!isset($temporal[$data['alumno']]['horas_vice_sup'])) $temporal[$data['alumno']]['horas_vice_sup'] = '00:00';
					if ($data['id_tipo_actividades'] <> 7)
						$temporal[$data['alumno']]['horas_vice'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_vice']);
					else $temporal[$data['alumno']]['horas_vice_sup'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_vice_sup']);
					break;

				default:

					break;
			}
			switch ($data['id_tipo_actividades']) {
				case 7:
					if ($data['id_clases_profesionales'] == 1) {
						if (!isset($temporal[$data['alumno']]['horas_misupervision'])) $temporal[$data['alumno']]['horas_misupervision'] = '00:00';
						$temporal[$data['alumno']]['horas_misupervision'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_misupervision']);
					}


					break;


				default:
					if ($data['id_clases_profesionales'] == 1) {
						if (!isset($temporal[$data['alumno']]['horas_micomun'])) $temporal[$data['alumno']]['horas_micomun'] = '00:00';
						$temporal[$data['alumno']]['horas_micomun'] = $this->datos->sumarHoras($data['horas'], $temporal[$data['alumno']]['horas_micomun']);
					}

					break;
			}
			$temporal[$data['alumno']]['servicio'] = $data['servicio'];
		}

		$horasDefault = '00:00';
		foreach ($temporal as $data) {
			$h = function ($key) use ($data, $horasDefault) {
				return isset($data[$key]) && $data[$key] !== '' ? $data[$key] : $horasDefault;
			};

			$salida[$i]['alumno'] = $data['alumno'];
			$salida[$i]['servicio'] = isset($data['servicio']) ? $data['servicio'] : '';
			$salida[$i]['horas_micomun'] = $h('horas_micomun');
			$salida[$i]['horas_misupervision'] = $h('horas_misupervision');
			$salida[$i]['horas_ma'] = $h('horas_ma');
			$salida[$i]['horas_ae'] = $h('horas_ae');
			$salida[$i]['horas_ae_sup'] = $h('horas_ae_sup');
			$salida[$i]['horas_dir'] = $h('horas_dir');
			$salida[$i]['horas_dir_sup'] = $h('horas_dir_sup');
			$salida[$i]['horas_psi'] = $h('horas_psi');
			$salida[$i]['horas_psi_sup'] = $h('horas_psi_sup');
			$salida[$i]['horas_fon'] = $h('horas_fon');
			$salida[$i]['horas_fon_sup'] = $h('horas_fon_sup');
			$salida[$i]['horas_as'] = $h('horas_as');
			$salida[$i]['horas_as_sup'] = $h('horas_as_sup');
			$salida[$i]['horas_psico'] = $h('horas_psico');
			$salida[$i]['horas_psico_sup'] = $h('horas_psico_sup');
			$salida[$i]['horas_vice'] = $h('horas_vice');
			$salida[$i]['horas_vice_sup'] = $h('horas_vice_sup');
			$salida[$i]['horas_eq'] = $h('horas_eq');
			$salida[$i]['total'] = $this->AddPlayTime(
				$salida[$i]['horas_micomun'],
				$salida[$i]['horas_misupervision'],
				$salida[$i]['horas_ae'],
				$salida[$i]['horas_ae_sup'],
				$salida[$i]['horas_ma'],
				$salida[$i]['horas_dir'],
				$salida[$i]['horas_dir_sup'],
				$salida[$i]['horas_psi'],
				$salida[$i]['horas_psi_sup'],
				$salida[$i]['horas_fon'],
				$salida[$i]['horas_fon_sup'],
				$salida[$i]['horas_as'],
				$salida[$i]['horas_as_sup'],
				$salida[$i]['horas_psico'],
				$salida[$i]['horas_psico_sup'],
				$salida[$i]['horas_vice'],
				$salida[$i]['horas_vice_sup'],
				$salida[$i]['horas_eq']
			);
			$i++;
		}

		$header = array('Alumno', 'Servicio', 'Horas MI generales', 'Horas MI Supervisión', 'Horas Maestra de apoyo', 'Horas Psicopedagoga', 'Horas Psicopedagoga Supervisión', 'Psicóloga', 'Psicóloga Supervisión', 'Directivo', 'Directivo Supervisión', 'Fonoaudiólogo', 'Fonoaudiólogo Supervisión', 'Trabajadora Social', 'Trabajadora Social Supervisión', 'Psicólogo', 'Psicólogo Supervisión', 'Vicedirección', 'Vicedirección Supervisión', 'Total Equipo', 'Total');

		$this->excel->getActiveSheet()->fromArray($header, NULL, 'A1');
		// read data to active sheet
		$this->excel->getActiveSheet()->fromArray($salida, null, 'A2');
		$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

		$filename = 'actividadesdetalle'; // set filename for excel file to be exported
                $filename = $filename."_{$mes}_{$anio}_{$servicio}";
		$writer = new Xlsx($this->excel); // instantiate Xlsx
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // generate excel file
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');	// download file

	}

	function exportarExcelHoras()
	{
		//load our new PHPExcel library

		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Actividades');

		// load database
		$this->load->database();

		// load model
		$mes = $this->input->get('mes');
		$anio = $this->anio_en_curso();
		$servicio = $this->input->get('servicio');
		if (isset($mes) == false) {
			$mes = date('m');
		}
		if (isset($servicio) == false) {
			$servicio = 'EP';
		}


		$datos = $this->actividades->getActividades($mes, $anio, $servicio);
		$salida = array();
		$i = 0;

		foreach ($datos as $data) {

			$salida[$i]['alumno'] = $data->nombre;
			$salida[$i]['horas'] = $data->horas;
			$salida[$i]['planificadas'] = $data->planificadas;
			$salida[$i]['diferencia'] = $data->diferencia;

			$i++;
		}
		$header = array('Alumno', 'Horas Cumplidas', 'Horas Planificadas', 'Diferencia');

		$this->excel->getActiveSheet()->fromArray($header, NULL, 'A1');
		// read data to active sheet
		$this->excel->getActiveSheet()->fromArray($salida, null, 'A2');
		$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);



		$filename = 'actividadeshoras'; //save our workbook as this file name

		$writer = new Xlsx($this->excel); // instantiate Xlsx
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // generate excel file
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');	// download file

	}
}
