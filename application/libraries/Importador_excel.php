<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Lectura y generación de plantillas Excel para importaciones masivas.
 */
class Importador_excel {

	const MAX_FILAS = 500;

	/** @var CI_Controller */
	protected $CI;

	public function __construct() {
		$this->CI =& get_instance();
	}

	/**
	 * Definición de columnas por tipo de importación.
	 *
	 * @return array<string, array{headers: string[], ejemplo: string[]}>
	 */
	public function definiciones() {
		return array(
			'obrassociales' => array(
				'headers' => array(
					'descripcion', 'telefono', 'contacto', 'direccion', 'localidad',
					'partido', 'codigo_postal', 'provincia', 'email',
				),
				'ejemplo' => array(
					'OSDE 210', '011-4444-5555', 'María Pérez', 'Av. Siempre Viva 123',
					'CABA', 'CABA', '1425', 'Buenos Aires', 'contacto@osde.com.ar',
				),
			),
			'maestras' => array(
				'headers' => array(
					'nombre', 'email', 'clave', 'perfil', 'tipo_profesional', 'coordinador', 'coordinador2',
					'telefono', 'direccion', 'localidad', 'partido', 'codigo_postal', 'provincia',
				),
				'ejemplo' => array(
					'JUAN PEREZ', 'juan.perez@ejemplo.com', 'Cambiar123', 'Maestra integradora', 'Maestra integradora',
					'eliana.puerta@escuela.crei.edu.ar; Lic.andreaccabrera@gmail.com', '',
					'011-1111-2222', 'Calle 1', 'CABA', 'CABA', '1425', 'Buenos Aires',
				),
			),
			'alumnos' => array(
				'headers' => array(
					'nombre', 'dni', 'servicio', 'email', 'telefono', 'direccion', 'localidad',
					'partido', 'codigo_postal', 'provincia', 'padre', 'diagnostico',
					'obra_social', 'maestra_integradora', 'coordinador',
					'fecha_inicio', 'fecha_fin', 'escuela', 'orientacion', 'acta_acuerdo',
				),
				'ejemplo' => array(
					'NIÑO EJEMPLO', '12345678', 'PRIMARIO', 'familia@ejemplo.com', '011-3333-4444',
					'Calle 2', 'CABA', 'CABA', '1425', 'Buenos Aires', 'Padre Ejemplo', '',
					'OSDE 210', 'JUAN PEREZ', 'coord.ejemplo@crei.edu.ar',
					'01/03/2025', '30/11/2025', 'Escuela 1', 'Orientación', 'ACTA-001',
				),
			),
			'planes' => array(
				'headers' => array(
					'alumno_dni', 'fecha_inicio', 'fecha_fin', 'escuela', 'orientacion', 'acta_acuerdo',
				),
				'ejemplo' => array(
					'12345678', '01/03/2025', '30/11/2025', 'Escuela 1', 'Orientación', 'ACTA-001',
				),
			),
		);
	}

	public function tipo_valido($tipo) {
		$def = $this->definiciones();
		return isset($def[$tipo]);
	}

	/**
	 * Envía plantilla .xlsx al navegador.
	 */
	public function descargar_plantilla($tipo) {
		$def = $this->definiciones();
		if (!isset($def[$tipo])) {
			show_404();
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle(substr($tipo, 0, 31));
		$sheet->fromArray($def[$tipo]['headers'], NULL, 'A1');
		$sheet->fromArray($def[$tipo]['ejemplo'], NULL, 'A2');

		$nombres = array(
			'obrassociales' => 'plantilla_obras_sociales',
			'maestras' => 'plantilla_maestras_integradoras',
			'alumnos' => 'plantilla_alumnos',
			'planes' => 'plantilla_planes',
		);
		$filename = $nombres[$tipo] . '.xlsx';

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	/**
	 * Lee archivo subido y devuelve filas asociativas (clave = encabezado normalizado).
	 *
	 * @return array{filas: array[], error: string|null}
	 */
	public function leer_archivo($ruta) {
		try {
			$spreadsheet = IOFactory::load($ruta);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray(null, true, true, false);
		} catch (Exception $e) {
			return array('filas' => array(), 'error' => 'No se pudo leer el archivo Excel: ' . $e->getMessage());
		}

		if (count($rows) < 2) {
			return array('filas' => array(), 'error' => 'El archivo no tiene filas de datos (solo encabezados o está vacío).');
		}

		$headers = array_shift($rows);
		$map = array();
		foreach ($headers as $i => $h) {
			if ($h === null || trim((string) $h) === '') {
				continue;
			}
			$map[$i] = $this->normalizar_clave($h);
		}

		if (empty($map)) {
			return array('filas' => array(), 'error' => 'No se encontraron encabezados en la primera fila.');
		}

		$filas = array();
		$num_fila = 1;
		foreach ($rows as $row) {
			$num_fila++;
			if ($num_fila > self::MAX_FILAS + 1) {
				return array('filas' => array(), 'error' => 'El archivo supera el máximo de ' . self::MAX_FILAS . ' filas.');
			}

			$asoc = array();
			$vacio = true;
			foreach ($map as $i => $clave) {
				$col = (int) $i + 1;
				$val = $this->leer_valor_celda($sheet->getCellByColumnAndRow($col, $num_fila));
				if ($val !== '') {
					$vacio = false;
				}
				$asoc[$clave] = $val;
			}
			if (!$vacio) {
				$asoc['_fila'] = $num_fila;
				$filas[] = $asoc;
			}
		}

		if (empty($filas)) {
			return array('filas' => array(), 'error' => 'No hay filas con datos para importar.');
		}

		return array('filas' => $filas, 'error' => null);
	}

	/**
	 * Lee el valor visible de una celda; convierte fechas Excel a dd/mm/aaaa.
	 */
	protected function leer_valor_celda($cell) {
		if ($cell === null) {
			return '';
		}

		try {
			if (class_exists('\PhpOffice\PhpSpreadsheet\Shared\Date')
				&& \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
				$raw = $cell->getValue();
				if (is_numeric($raw)) {
					$dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $raw);
					return $dt->format('d/m/Y');
				}
			}
		} catch (Exception $e) {
			// seguir con valor formateado
		}

		$formatted = $cell->getFormattedValue();
		if ($formatted !== null && $formatted !== '') {
			return $this->normalizar_celda((string) $formatted);
		}

		return $this->normalizar_celda((string) $cell->getValue());
	}

	/** Quita espacios al inicio/fin (incl. espacios de Excel) y caracteres de control. */
	protected function normalizar_celda($texto) {
		$texto = (string) $texto;
		$texto = preg_replace('/^\x{FEFF}/u', '', $texto);
		$texto = str_replace(array("\xc2\xa0", "\x00"), array(' ', ''), $texto);
		$texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $texto);
		return trim($texto);
	}

	public function normalizar_clave($texto) {
		$texto = (string) $texto;
		$texto = preg_replace('/^\x{FEFF}/u', '', $texto);
		$texto = mb_strtolower(trim($texto), 'UTF-8');
		$reemplazos = array(
			'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
			' ' => '_', '-' => '_', '.' => '',
		);
		return strtr($texto, $reemplazos);
	}
}
