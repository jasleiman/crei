<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Importaciones extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper(array('url', 'form', 'html', 'language'));
		$this->load->model('menus', '', TRUE);
		$this->load->model('importaciones_model', 'importaciones', TRUE);
		$this->load->library('importador_excel');
	}

	public function plantilla($tipo = '') {
		$this->_requiere_permiso($tipo);
		if (!$this->importador_excel->tipo_valido($tipo)) {
			show_404();
		}
		$this->importador_excel->descargar_plantilla($tipo);
	}

	public function procesar($tipo = '') {
		ob_start();
		$this->_requiere_permiso($tipo);
		if (!$this->importador_excel->tipo_valido($tipo)) {
			show_404();
		}

		$session_data = $this->session->userdata('logged_in');
		$redirect_error = $this->importaciones->url_retorno($tipo);

		if (empty($_FILES['archivo']['name'])) {
			$this->_flash('Seleccione un archivo Excel.', 'warning');
			$this->_redirect_descartando_salida($redirect_error);
		}

		$dir = APPPATH . 'cache/imports/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}

		$config = array(
			'upload_path' => $dir,
			'allowed_types' => 'xlsx|xls',
			'max_size' => 8192,
			'encrypt_name' => TRUE,
		);
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('archivo')) {
			$this->_flash($this->upload->display_errors('', ''), 'error');
			$this->_redirect_descartando_salida($redirect_error);
		}

		$upload = $this->upload->data();
		$lectura = $this->importador_excel->leer_archivo($upload['full_path']);
		@unlink($upload['full_path']);

		if ($lectura['error']) {
			$this->_flash($lectura['error'], 'error');
			$this->_redirect_descartando_salida($redirect_error);
		}

		$preview = $this->importaciones->validar($tipo, $lectura['filas']);
		$validas = 0;
		foreach ($preview as $row) {
			if ($row['valido']) {
				$validas++;
			}
		}

		$this->_guardar_preview_import($tipo, $preview, array(
			'total' => count($preview),
			'validas' => $validas,
		));

		$this->_redirect_descartando_salida('importaciones/vista_previa/' . $tipo);
	}

	/** Muestra la vista previa tras subir el Excel (GET, layout completo). */
	public function vista_previa($tipo = '') {
		$this->_requiere_permiso($tipo);
		if (!$this->importador_excel->tipo_valido($tipo)) {
			show_404();
		}

		$stored = $this->_cargar_preview_import($tipo);
		if ($stored === null) {
			$this->_flash('No hay vista previa pendiente. Suba el archivo nuevamente.', 'warning');
			redirect($this->importaciones->url_retorno($tipo));
		}

		$preview = $stored['preview'];
		$meta = $stored['meta'];
		if (!is_array($meta)) {
			$meta = array('total' => count($preview), 'validas' => 0);
			foreach ($preview as $row) {
				if (!empty($row['valido'])) {
					$meta['validas']++;
				}
			}
		}

		$this->load->helper('form');
		$session_data = $this->session->userdata('logged_in');
		$data['username'] = $session_data['username'];
		$data['menu'] = $this->menus->imprimirMenu($session_data['id']);
		$data['tipo'] = $tipo;
		$data['titulo'] = $this->importaciones->titulo_tipo($tipo);
		$data['preview'] = $preview;
		$data['total'] = (int) $meta['total'];
		$data['validas'] = (int) $meta['validas'];
		$data['url_volver'] = $this->importaciones->url_retorno($tipo);

		$this->load->view('header');
		$this->load->view('menusuperior');
		$this->load->view('importaciones/preview', $data);
		$this->load->view('footer');
	}

	public function confirmar($tipo = '') {
		$this->_requiere_permiso($tipo);
		if (!$this->importador_excel->tipo_valido($tipo)) {
			show_404();
		}

		$redirect = $this->importaciones->url_retorno($tipo);
		$stored = $this->_cargar_preview_import($tipo);

		if ($stored === null) {
			$this->_flash('No hay datos de importación pendientes. Suba el archivo nuevamente.', 'warning');
			redirect($redirect);
		}

		$preview = $stored['preview'];

		$para_insertar = array();
		foreach ($preview as $row) {
			if ($row['valido'] && !empty($row['datos'])) {
				$para_insertar[] = array(
					'fila' => $row['fila'],
					'datos' => $row['datos'],
				);
			}
		}

		if (empty($para_insertar)) {
			$this->_flash('No hay filas válidas para importar.', 'warning');
			redirect($redirect);
		}

		$resultado = $this->importaciones->ejecutar($tipo, $para_insertar);
		$this->_borrar_preview_import($tipo);

		$msg = 'Se importaron ' . $resultado['ok'] . ' registro(s) correctamente.';
		$type = 'success';
		if (!empty($resultado['errores'])) {
			$msg .= ' Errores: ' . implode(' ', $resultado['errores']);
			$type = 'warning';
		}
		if ($resultado['ok'] === 0) {
			$type = 'error';
		}

		$this->_flash($msg, $type);
		redirect($redirect);
	}

	public function cancelar($tipo = '') {
		$this->_requiere_permiso($tipo);
		$this->_borrar_preview_import($tipo);
		redirect($this->importaciones->url_retorno($tipo));
	}

	protected function _requiere_permiso($tipo) {
		if (!$this->importador_excel->tipo_valido($tipo)) {
			show_404();
		}
		$session_data = $this->session->userdata('logged_in');
		$funcion = 'administracion/' . $this->importaciones->funcion_permiso($tipo);
		if (!$this->session->userdata('logged_in') || !$this->menus->estaHabilitado($funcion, $session_data['id'])) {
			redirect('login', 'refresh');
		}
	}

	protected function _redirect_descartando_salida($url) {
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		redirect($url);
	}

	protected function _flash($texto, $type) {
		$msg = str_replace(array('"', "\n", "\r"), array("'", ' ', ' '), $texto);
		$mensaje = '{&quot;text&quot;:&quot;' . $msg . '&quot;,&quot;layout&quot;:&quot;top&quot;,&quot;type&quot;:&quot;' . $type . '&quot;}';
		$this->session->set_flashdata('mensaje', $mensaje);
	}

	/**
	 * La vista previa de alumnos (100+ filas) no entra en ci_sessions (BLOB ~64 KB).
	 * Se guarda en disco; en sesión solo queda el token.
	 */
	protected function _guardar_preview_import($tipo, $preview, $meta) {
		$dir = APPPATH . 'cache/imports/previews/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}

		$this->_borrar_preview_import($tipo);

		$token = bin2hex(random_bytes(16));
		$file = $dir . $this->_preview_filename($tipo, $token);
		$payload = json_encode(
			array('preview' => $preview, 'meta' => $meta),
			JSON_UNESCAPED_UNICODE
		);
		if (@file_put_contents($file, $payload) === false) {
			$this->_flash('No se pudo guardar la vista previa. Verifique permisos en application/cache/imports/.', 'error');
			redirect($this->importaciones->url_retorno($tipo));
			exit;
		}

		$this->session->set_userdata('import_preview_key_' . $tipo, $token);
	}

	/**
	 * @return array{preview: array, meta: array}|null
	 */
	protected function _cargar_preview_import($tipo) {
		$token = $this->session->userdata('import_preview_key_' . $tipo);
		if (!$token) {
			return $this->_cargar_preview_import_legacy($tipo);
		}

		$file = APPPATH . 'cache/imports/previews/' . $this->_preview_filename($tipo, $token);
		if (!is_file($file)) {
			return null;
		}

		$data = json_decode(file_get_contents($file), true);
		if (!is_array($data) || !isset($data['preview']) || !is_array($data['preview'])) {
			return null;
		}

		return $data;
	}

	protected function _cargar_preview_import_legacy($tipo) {
		$preview = $this->session->userdata('import_preview_' . $tipo);
		if (!is_array($preview) || empty($preview)) {
			return null;
		}
		$meta = $this->session->userdata('import_preview_meta_' . $tipo);
		return array('preview' => $preview, 'meta' => is_array($meta) ? $meta : array());
	}

	protected function _borrar_preview_import($tipo) {
		$token = $this->session->userdata('import_preview_key_' . $tipo);
		if ($token) {
			$file = APPPATH . 'cache/imports/previews/' . $this->_preview_filename($tipo, $token);
			if (is_file($file)) {
				@unlink($file);
			}
		}
		$this->session->unset_userdata('import_preview_key_' . $tipo);
		$this->session->unset_userdata('import_preview_' . $tipo);
		$this->session->unset_userdata('import_preview_meta_' . $tipo);
	}

	protected function _preview_filename($tipo, $token) {
		$tipo = preg_replace('/[^a-z0-9_]/', '', (string) $tipo);
		$token = preg_replace('/[^a-f0-9]/', '', (string) $token);
		return 'preview_' . $tipo . '_' . $token . '.json';
	}
}
