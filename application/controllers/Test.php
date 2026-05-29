<?php
error_reporting(0);
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Test extends CI_Controller {

  private $excel;
	function __construct() {
		parent::__construct();

		$this -> load -> model('menus', '', TRUE);
		$this -> load -> model('perfiles', '', TRUE);
		$this -> load -> model('actividades', '', TRUE);
		$this -> load -> model('tipo_actividades', '', TRUE);
		$this -> load -> model('personas', '', TRUE);

	}

	function AddPlayTime($mi,$misup,$ma,$eq1,$eq2,$eq3,$eq4,$eq5,$eq6) {
		$times=array();
		$times[]=$mi;
		$times[]=$misup;
		$times[]=$ma;
		$times[]=$eq1;
		$times[]=$eq2;
		$times[]=$eq3;
		$times[]=$eq4;
		$times[]=$eq5;
		$times[]=$eq6;
	    // loop throught all the times

	    foreach ($times as $time) {
	        list($hour, $minute) = explode(':', $time);
	        $minutes += $hour * 60;
	        $minutes += $minute;
	    }

	    $hours = floor($minutes / 60);
	    $minutes -= $hours * 60;

	    // returns the time already formatted
	    return sprintf('%02d:%02d', $hours, $minutes);
	}

	function exportarExcel() {
		       //load our new PHPExcel library
        $this->excel=new Spreadsheet();
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Actividades');

        // load database
        $this->load->database();

        // load model
        $mes='10';
		$anio='2020';
		$id_personas=279;

		if (isset($mes) ==false) {$mes=date('m'); }
		if (isset($anio) ==false) {$anio=date('Y'); }
		if (isset($id_personas) ==false) {$id_personas=0; }

		$datos=$this->actividades->getActividadesPorPersona($mes,$anio,$id_personas,true);
		$salida=array();
		$i=0;
		foreach ($datos as $data) {
			$salida[$i]['mes']=$mes;
			$salida[$i]['anio']=$anio;
			$salida[$i]['nombre']=$data['nombre'];
			$salida[$i]['horas']=$data['horas'];
			$i++;

		}
		$header=array('Mes','Año','Persona','Horas trabajadas');

 		$this->excel->getActiveSheet()->fromArray($header,NULL,'A1');
        // read data to active sheet
        $this->excel->getActiveSheet()->fromArray($salida,null,'A2');
		$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);




				$writer = new Xlsx($spreadsheet); // instantiate Xlsx

				$filename = 'actividades'; // set filename for excel file to be exported

				header('Content-Type: application/vnd.ms-excel'); // generate excel file
				header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
				header('Cache-Control: max-age=0');

				$writer->save('php://output');	// download file

	}


	function exportarExcelDetalleTodosAlumno() {
		       //load our new PHPExcel library
      $this->excel=new Spreadsheet();
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Actividades');

        // load database
        $this->load->database();

        // load model
        $mes=10;
		$anio=2020;
		$id_personas=$this -> input -> get('id_personas');
		$servicio='INICIAL';

		if (isset($mes) ==false) {$mes=date('m'); }
		if (isset($anio) ==false) {$anio=date('Y'); }
		if (isset($servicio) ==false) {$servicio='PRIMARIO'; }
		if (isset($id_personas) ==false) {$id_personas=0; }


		$datos=$this->actividades->getTotales(0,$mes,$anio,0,true,$servicio);
		$salida=array();
		$temporal=array();
		$hor=array();
		$i=0;
		foreach ($datos as $data) {
			$temporal[$data['alumno']]['alumno']=$data['alumno'];

			switch ($data['id_clases_profesionales']) {
				case 1:
					if (!isset($temporal[$data['alumno']]['horas_mi'])) $temporal[$data['alumno']]['horas_mi']='00:00';
					$temporal[$data['alumno']]['horas_mi']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_mi']);

					break;
				case 2:
					if (!isset($temporal[$data['alumno']]['horas_eq'])) $temporal[$data['alumno']]['horas_eq']='00:00';
					$temporal[$data['alumno']]['horas_eq']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_eq']);

					break;
				case 3:
					if (!isset($temporal[$data['alumno']]['horas_ma'])) $temporal[$data['alumno']]['horas_ma']='00:00';
					$temporal[$data['alumno']]['horas_ma']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_ma']);

					break;

				default:

					break;
			}
			switch ($data['id_profesionales']) {
				case 1:
					if (!isset($temporal[$data['alumno']]['horas_ae'])) $temporal[$data['alumno']]['horas_ae']='00:00';
					if (!isset($temporal[$data['alumno']]['horas_ae_sup'])) $temporal[$data['alumno']]['horas_ae_sup']='00:00';
					if ($data['id_tipo_actividades'] <> 7)
					$temporal[$data['alumno']]['horas_ae']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_ae']);
					else $temporal[$data['alumno']]['horas_ae_sup']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_ae_sup']);
					break;
				case 9:

					if (!isset($temporal[$data['alumno']]['horas_dir'])) $temporal[$data['alumno']]['horas_dir']='00:00';

					if (!isset($temporal[$data['alumno']]['horas_dir_sup'])) $temporal[$data['alumno']]['horas_dir_sup']='00:00';
					if ($data['id_tipo_actividades'] <> 7)
					$temporal[$data['alumno']]['horas_dir']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_dir']);
					else $temporal[$data['alumno']]['horas_dir_sup']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_dir_sup']);
					break;
				case 8:
					if (!isset($temporal[$data['alumno']]['horas_psi'])) $temporal[$data['alumno']]['horas_psi']='00:00';
					if (!isset($temporal[$data['alumno']]['horas_psi_sup'])) $temporal[$data['alumno']]['horas_psi_sup']='00:00';
					if ($data['id_tipo_actividades'] <> 7)
					$temporal[$data['alumno']]['horas_psi']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_psi']);
					else $temporal[$data['alumno']]['horas_psi_sup']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_psi_sup']);
					break;
				case 12:
					if (!isset($temporal[$data['alumno']]['horas_fon'])) $temporal[$data['alumno']]['horas_fon']='00:00';
					if (!isset($temporal[$data['alumno']]['horas_fon_sup'])) $temporal[$data['alumno']]['horas_fon_sup']='00:00';
					if ($data['id_tipo_actividades'] <> 7)
					$temporal[$data['alumno']]['horas_fon']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_fon']);
					else $temporal[$data['alumno']]['horas_fon_sup']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_fon_sup']);
					break;
				case 13:
					if (!isset($temporal[$data['alumno']]['horas_as'])) $temporal[$data['alumno']]['horas_as']='00:00';
					if (!isset($temporal[$data['alumno']]['horas_as_sup'])) $temporal[$data['alumno']]['horas_as_sup']='00:00';
					if ($data['id_tipo_actividades'] <> 7)
					$temporal[$data['alumno']]['horas_as']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_as']);
					else $temporal[$data['alumno']]['horas_as_sup']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_as_sup']);
					break;
				case 14:
					if (!isset($temporal[$data['alumno']]['horas_psico'])) $temporal[$data['alumno']]['horas_psico']='00:00';
					if (!isset($temporal[$data['alumno']]['horas_psico_sup'])) $temporal[$data['alumno']]['horas_psico_sup']='00:00';
					if ($data['id_tipo_actividades'] <> 7)
					$temporal[$data['alumno']]['horas_psico']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_psico']);
					else $temporal[$data['alumno']]['horas_psico_sup']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_psico_sup']);
					break;
				case 14:
					if (!isset($temporal[$data['alumno']]['horas_vice'])) $temporal[$data['alumno']]['horas_vice']='00:00';
					if (!isset($temporal[$data['alumno']]['horas_vice_sup'])) $temporal[$data['alumno']]['horas_vice_sup']='00:00';
					if ($data['id_tipo_actividades'] <> 7)
					$temporal[$data['alumno']]['horas_vice']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_vice']);
					else $temporal[$data['alumno']]['horas_vice_sup']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_vice_sup']);
					break;

				default:

					break;
			}
			switch ($data['id_tipo_actividades']) {
				case 7:
					if ($data['id_clases_profesionales']==1) {
						if (!isset($temporal[$data['alumno']]['horas_misupervision'])) $temporal[$data['alumno']]['horas_misupervision']='00:00';
						$temporal[$data['alumno']]['horas_misupervision']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_misupervision']);
					}


					break;


				default:
					if ($data['id_clases_profesionales']==1) {
						if (!isset($temporal[$data['alumno']]['horas_micomun'])) $temporal[$data['alumno']]['horas_micomun']='00:00';
						$temporal[$data['alumno']]['horas_micomun']=$this->datos->sumarHoras($data['horas'],$temporal[$data['alumno']]['horas_micomun']);
					}

					break;
			}
			$temporal[$data['alumno']]['servicio']=$data['servicio'];

		}

		foreach ($temporal as $data) {

			$salida[$i]['alumno']=$data['alumno'];
			$salida[$i]['servicio']=$data['servicio'];
			$salida[$i]['horas_micomun']=$data['horas_micomun'];
			$salida[$i]['horas_misupervision']=$data['horas_misupervision'];
			$salida[$i]['horas_ma']=$data['horas_ma'];
			$salida[$i]['horas_ae']=$data['horas_ae'];
			$salida[$i]['horas_ae_sup']=$data['horas_ae_sup'];
			$salida[$i]['horas_dir']=$data['horas_dir'];
			$salida[$i]['horas_dir_sup']=$data['horas_dir_sup'];
			$salida[$i]['horas_psi']=$data['horas_psi'];
			$salida[$i]['horas_psi_sup']=$data['horas_psi_sup'];
			$salida[$i]['horas_fon']=$data['horas_fon'];
			$salida[$i]['horas_fon_sup']=$data['horas_fon_sup'];
			$salida[$i]['horas_as']=$data['horas_as'];
			$salida[$i]['horas_as_sup']=$data['horas_as_sup'];
			$salida[$i]['horas_psico']=$data['horas_psico'];
			$salida[$i]['horas_psico_sup']=$data['horas_psico_sup'];
			$salida[$i]['horas_vice']=$data['horas_vice'];
			$salida[$i]['horas_vice_sup']=$data['horas_vice_sup'];
			$salida[$i]['horas_eq']=$data['horas_eq'];
			$fila=$i+1;
			$salida[$i]['total']=$this->AddPlayTime($data['horas_micomun'],$data['horas_misupervision'],$data['horas_ae'],$data['horas_ae_sup'],$data['horas_ma'],$data['horas_dir'],$data['horas_dir_sup'],$data['horas_psi'],$data['horas_psi_sup'],$data['horas_fon'],$data['horas_fon_sup'],$data['horas_as'],$data['horas_as_sup'],$data['horas_psico'],$data['horas_psico_sup'],$data['horas_vice'],$data['horas_vice_sup']);
			$i++;

		}
		$header=array('Alumno','Servicio','Horas MI generales','Horas MI Supervisión','Horas Maestra de apoyo','Horas Psicopedagoga','Horas Psicopedagoga Supervisión','Psicóloga','Psicóloga Supervisión','Directivo','Directivo Supervisión','Fonoaudiólogo','Fonoaudiólogo Supervisión','Trabajadora Social','Trabajadora Social Supervisión','Psicólogo','Psicólogo Supervisión', 'Vicedirección','Vicedirección Supervisión','Total Equipo','Total');

 		$this->excel->getActiveSheet()->fromArray($header,NULL,'A1');
        // read data to active sheet
        $this->excel->getActiveSheet()->fromArray($salida,null,'A2');
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

		$writer = new Xlsx($this->excel); // instantiate Xlsx

		$filename = 'actividadesdetalle'; // set filename for excel file to be exported

		header('Content-Type: application/vnd.ms-excel'); // generate excel file
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');	// download file

	}
  public function index() {

          $spreadsheet = new Spreadsheet(); // instantiate Spreadsheet

          $sheet = $spreadsheet->getActiveSheet();

          // manually set table data value
          $sheet->setCellValue('A1', 'Gipsy Danger');
          $sheet->setCellValue('A2', 'Gipsy Avenger');
          $sheet->setCellValue('A3', 'Striker Eureka');

          $writer = new Xlsx($spreadsheet); // instantiate Xlsx

          $filename = 'list-of-jaegers'; // set filename for excel file to be exported

          header('Content-Type: application/vnd.ms-excel'); // generate excel file
          header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
          header('Cache-Control: max-age=0');

          $writer->save('php://output');	// download file
      }
}
