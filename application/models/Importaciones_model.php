<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Importaciones_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('alcance', '', TRUE);
		$this->load->model('obrassociales', '', TRUE);
		$this->load->model('alumnos', '', TRUE);
		$this->load->model('personas', '', TRUE);
		$this->load->model('usuarios', '', TRUE);
		$this->load->model('planes', '', TRUE);
		$this->load->model('planes_dias', '', TRUE);
		$this->load->model('perfiles', '', TRUE);
		$this->load->model('datos', '', TRUE);
	}

	/**
	 * @param string $tipo
	 * @param array[] $filas
	 * @return array{fila: int, valido: bool, mensaje: string, datos: array}[]
	 */
	public function validar($tipo, $filas) {
		foreach ($filas as $i => $fila) {
			$filas[$i] = $this->normalizar_fila_importacion($fila);
		}

		switch ($tipo) {
			case 'obrassociales':
				return $this->validar_obras_sociales($filas);
			case 'maestras':
				return $this->validar_maestras($filas);
			case 'alumnos':
				return $this->validar_alumnos($filas);
			case 'planes':
				return $this->validar_planes($filas);
			default:
				return array();
		}
	}

	/**
	 * @param string $tipo
	 * @param array[] $filas_validas datos listos para insertar
	 * @return array{ok: int, errores: string[]}
	 */
	public function ejecutar($tipo, $filas_validas) {
		$ok = 0;
		$errores = array();

		$this->db->trans_start();

		foreach ($filas_validas as $item) {
			switch ($tipo) {
				case 'obrassociales':
					$this->insertar_obra_social($item['datos']);
					break;
				case 'maestras':
					$this->insertar_maestra($item['datos']);
					break;
				case 'alumnos':
					$this->insertar_alumno_con_plan($item['datos']);
					break;
				case 'planes':
					$this->insertar_plan($item['datos']);
					break;
			}
			$ok++;
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return array('ok' => 0, 'errores' => array('Error de transacción en la base de datos. No se guardó ningún registro.'));
		}

		return array('ok' => $ok, 'errores' => $errores);
	}

	protected function validar_obras_sociales($filas) {
		$resultado = array();

		foreach ($filas as $fila) {
			$num = $fila['_fila'];
			$errores = array();

			if (empty($fila['descripcion'])) {
				$errores[] = 'descripcion es obligatoria';
			}
			$err_email = $this->validar_campo_email(isset($fila['email']) ? $fila['email'] : '', false);
			if ($err_email) {
				$errores[] = $err_email;
			}
			if (empty($errores) && !empty($fila['descripcion']) && $this->obra_social_existe($fila['descripcion'])) {
				$errores[] = 'ya existe una obra social activa con ese nombre';
			}
			if (empty($errores) && !empty($fila['descripcion']) && $this->obra_social_en_papelera($fila['descripcion'])) {
				$errores[] = 'esa obra social está en la papelera; restáurela o elimínela definitivamente (Obras sociales → Ver papelera)';
			}

			$valido = empty($errores);
			$resultado[] = array(
				'fila' => $num,
				'valido' => $valido,
				'mensaje' => $valido ? 'OK' : implode('; ', $errores),
				'resumen' => $fila['descripcion'],
				'datos' => $valido ? $fila : null,
			);
		}

		return $resultado;
	}

	/**
	 * Lista de coordinadores/superiores para una maestra (varios permitidos).
	 * En la columna coordinador: separar con punto y coma (;) o barra (|).
	 * También columnas opcionales coordinador2, coordinador3.
	 *
	 * @return string[]
	 */
	protected function lista_identificadores_coordinadores_maestra($fila) {
		$claves = array(
			'coordinador', 'coordinadora', 'superior', 'psicopedagoga', 'psicopedagogo',
			'coordinador2', 'coordinador_2', 'coordinador3', 'coordinador_3',
		);
		$tokens = array();
		foreach ($claves as $clave) {
			if (empty($fila[$clave])) {
				continue;
			}
			$texto = $this->limpiar_texto_celda((string) $fila[$clave]);
			$partes = preg_split('/\s*[;|]\s*/', $texto);
			foreach ($partes as $parte) {
				$parte = $this->limpiar_texto_celda($parte);
				if ($parte !== '') {
					$tokens[] = $parte;
				}
			}
		}
		$unicos = array();
		foreach ($tokens as $t) {
			if (!in_array($t, $unicos, true)) {
				$unicos[] = $t;
			}
		}
		return $unicos;
	}

	protected function validar_maestras($filas) {
		$resultado = array();

		foreach ($filas as $fila) {
			$num = $fila['_fila'];
			$errores = array();

			if (empty($fila['nombre'])) {
				$errores[] = 'nombre es obligatorio';
			}
			$modo = 'alta';
			$id_personas_existente = null;
			$id_usuarios_existente = null;

			$err_email = $this->validar_campo_email(isset($fila['email']) ? $fila['email'] : '', true);
			if ($err_email) {
				$errores[] = $err_email;
			} elseif ($this->usuario_en_papelera($fila['email'])) {
				$errores[] = 'ya existe ese email en la papelera; restáure el usuario o elimínelo definitivamente (Usuarios → Ver papelera)';
			} else {
				$usuario_activo = $this->buscar_usuario_activo_por_email($fila['email']);
				if ($usuario_activo) {
					$modo = 'actualizar';
					$id_personas_existente = (int) $usuario_activo['id_personas'];
					$id_usuarios_existente = (int) $usuario_activo['id_usuarios'];
				}
			}
			if ($modo === 'alta' && empty($fila['clave'])) {
				$errores[] = 'clave es obligatoria';
			}

			$id_perfiles = $this->resolver_perfil(isset($fila['perfil']) ? $fila['perfil'] : '');
			if (!$id_perfiles) {
				$errores[] = 'perfil no encontrado (permisos de menú: Administrador, Equipo, Maestra integradora, etc.)';
			}

			$tipo_prof_txt = $this->valor_tipo_profesional_fila($fila);
			$id_profesionales = $this->resolver_tipo_profesional($tipo_prof_txt, 'Maestra integradora');
			if (!$id_profesionales) {
				$errores[] = 'tipo_profesional no encontrado'
					. ($tipo_prof_txt !== '' ? ': "' . $tipo_prof_txt . '"' : '')
					. ' (use la descripción del organigrama: Maestra integradora, Psicopedagoga, Directivo, etc.)';
			}

			$coordinadores_txt = $this->lista_identificadores_coordinadores_maestra($fila);
			$id_padres = array();
			$requiere_coordinadores = $id_profesionales
				&& $this->tipo_profesional_puede_tener_coordinadores($id_profesionales);

			if ($requiere_coordinadores) {
				if (empty($coordinadores_txt)) {
					$errores[] = 'falta al menos un coordinador/superior (columna coordinador; varios separados con ; )';
				} else {
					foreach ($coordinadores_txt as $coordinador_txt) {
						$err_coord_email = $this->validar_identificador_con_email($coordinador_txt, 'coordinador');
						if ($err_coord_email) {
							$errores[] = $err_coord_email . ' (' . $coordinador_txt . ')';
							continue;
						}
						$id_padre = $this->resolver_superior_maestra($coordinador_txt);
						if (!$id_padre) {
							$errores[] = $this->mensaje_superior_maestra_no_encontrado($coordinador_txt);
							continue;
						}
						$id_padres[] = $id_padre;
					}
					$id_padres = array_values(array_unique(array_map('intval', $id_padres)));
				}
			} elseif (!empty($coordinadores_txt)) {
				foreach ($coordinadores_txt as $coordinador_txt) {
					$err_coord_email = $this->validar_identificador_con_email($coordinador_txt, 'coordinador');
					if ($err_coord_email) {
						$errores[] = $err_coord_email . ' (' . $coordinador_txt . ')';
						continue;
					}
					$id_padre = $this->resolver_superior_maestra($coordinador_txt);
					if (!$id_padre) {
						$errores[] = $this->mensaje_superior_maestra_no_encontrado($coordinador_txt);
						continue;
					}
					$id_padres[] = $id_padre;
				}
				$id_padres = array_values(array_unique(array_map('intval', $id_padres)));
			}

			if (empty($errores) && $modo === 'actualizar' && $id_personas_existente > 0
				&& (int) $id_profesionales === Alcance::PROF_MAESTRA
				&& !$this->alcance->puede_ver_maestra($id_personas_existente)) {
				$errores[] = 'sin permiso para modificar esa maestra integradora';
			}

			if (empty($errores) && !empty($id_padres)) {
				if (!$this->alcance->ve_todo() && $this->alcance->es_coordinador()) {
					if (!in_array($this->alcance->id_personas_sesion(), $id_padres, true)) {
						$errores[] = 'solo puede importar maestras donde usted figure como uno de los coordinadores';
					}
				}
			}

			$valido = empty($errores);
			$datos = null;
			if ($valido) {
				$datos = $fila;
				$datos['modo'] = $modo;
				$datos['id_profesionales'] = $id_profesionales;
				$datos['id_perfiles'] = $id_perfiles;
				$datos['id_padre'] = $id_padres;
				$datos['tipo_profesional'] = $tipo_prof_txt;
				if ($modo === 'actualizar') {
					$datos['id_personas'] = $id_personas_existente;
					$datos['id_usuarios'] = $id_usuarios_existente;
					if (empty($datos['clave'])) {
						$datos['clave'] = '******';
					}
				}
			}

			$mensaje_ok = ($modo === 'actualizar') ? 'OK (actualizar datos existentes)' : 'OK (alta nueva)';

			$resultado[] = array(
				'fila' => $num,
				'valido' => $valido,
				'mensaje' => $valido ? $mensaje_ok : implode('; ', $errores),
				'resumen' => trim($fila['nombre'] . ' / ' . $fila['email'] . ' (' . ($tipo_prof_txt !== '' ? $tipo_prof_txt : 'Maestra integradora') . ')'),
				'datos' => $datos,
			);
		}

		return $resultado;
	}

	protected function validar_alumnos($filas) {
		$resultado = array();
		$servicios = array('INICIAL', 'PRIMARIO', 'SECUNDARIO');

		foreach ($filas as $fila) {
			$num = $fila['_fila'];
			$errores = array();
			$id_mi = null;
			$id_coord = null;
			$id_os = null;

			if (empty($fila['nombre'])) {
				$errores[] = 'nombre es obligatorio';
			}
			if (empty($fila['dni'])) {
				$errores[] = 'dni es obligatorio';
			} elseif ($this->alumno_dni_existe($fila['dni'])) {
				$errores[] = 'ya existe un alumno con ese DNI';
			}

			$err_email = $this->validar_campo_email(isset($fila['email']) ? $fila['email'] : '', false);
			if ($err_email) {
				$errores[] = $err_email;
			}

			$servicio = strtoupper(isset($fila['servicio']) ? $fila['servicio'] : '');
			if ($servicio === '' || !in_array($servicio, $servicios, true)) {
				$errores[] = 'servicio debe ser INICIAL, PRIMARIO o SECUNDARIO';
			}

			if (empty($fila['obra_social'])) {
				$errores[] = 'obra_social es obligatoria';
			} else {
				$id_os = $this->resolver_obra_social($fila['obra_social']);
				if (!$id_os) {
					$errores[] = 'obra social no encontrada';
				}
			}

			if (empty($fila['maestra_integradora'])) {
				$errores[] = 'maestra_integradora es obligatoria';
			} else {
				$err_mi_email = $this->validar_identificador_con_email($fila['maestra_integradora'], 'maestra_integradora');
				if ($err_mi_email) {
					$errores[] = $err_mi_email;
				} else {
					$id_mi = $this->resolver_maestra($fila['maestra_integradora']);
					if (!$id_mi) {
						$errores[] = $this->mensaje_persona_no_encontrada('maestra integradora', $fila['maestra_integradora'], 2);
					}
				}
			}

			if (empty($fila['coordinador'])) {
				$errores[] = 'coordinador es obligatorio';
			} else {
				$err_coord_email = $this->validar_identificador_con_email($fila['coordinador'], 'coordinador');
				if ($err_coord_email) {
					$errores[] = $err_coord_email;
				} else {
					$id_coord = $this->resolver_superior_maestra($fila['coordinador']);
					if (!$id_coord) {
						$errores[] = $this->mensaje_superior_maestra_no_encontrado($fila['coordinador']);
					}
				}
			}

			$raw_inicio = isset($fila['fecha_inicio']) ? $fila['fecha_inicio'] : '';
			$raw_fin = isset($fila['fecha_fin']) ? $fila['fecha_fin'] : '';
			$fecha_inicio = $this->normalizar_fecha_importacion($raw_inicio);
			$fecha_fin = $this->normalizar_fecha_importacion($raw_fin);
			$fila['fecha_inicio'] = $fecha_inicio;
			$fila['fecha_fin'] = $fecha_fin;

			if ($fecha_inicio === '' || !$this->fecha_valida($fecha_inicio)) {
				$errores[] = 'fecha_inicio inválida (use dd/mm/aaaa)'
					. ($raw_inicio !== '' && $raw_inicio !== $fecha_inicio ? '; valor leído: "' . $raw_inicio . '"' : '');
			}
			if ($fecha_fin === '' || !$this->fecha_valida($fecha_fin)) {
				$errores[] = 'fecha_fin inválida (use dd/mm/aaaa)'
					. ($raw_fin !== '' && $raw_fin !== $fecha_fin ? '; valor leído: "' . $raw_fin . '"' : '');
			}

			if (empty($errores) && !empty($id_mi) && !empty($id_coord)) {
				if (!$this->alcance->coordinador_pertenece_a_maestra($id_coord, $id_mi)) {
					$errores[] = 'el coordinador no está asignado a esa maestra integradora';
				}
				if (!$this->alcance->puede_ver_maestra($id_mi)) {
					$errores[] = 'sin permiso para importar alumnos de esa maestra';
				}
			}

			$valido = empty($errores);
			$datos = null;
			if ($valido) {
				$datos = $fila;
				$datos['servicio'] = $servicio;
				$datos['id_obras_sociales'] = $id_os;
				$datos['id_personas'] = $id_mi;
				$datos['id_coordinador'] = $id_coord;
			}

			$resultado[] = array(
				'fila' => $num,
				'valido' => $valido,
				'mensaje' => $valido ? 'OK' : implode('; ', $errores),
				'resumen' => trim($fila['nombre'] . ' (DNI ' . $fila['dni'] . ')'),
				'datos' => $datos,
			);
		}

		return $resultado;
	}

	protected function validar_planes($filas) {
		$resultado = array();

		foreach ($filas as $fila) {
			$num = $fila['_fila'];
			$errores = array();

			if (empty($fila['alumno_dni'])) {
				$errores[] = 'alumno_dni es obligatorio';
			} else {
				$id_alumno = $this->resolver_alumno_por_dni($fila['alumno_dni']);
				if (!$id_alumno) {
					$errores[] = 'alumno no encontrado por DNI';
				}
			}

			if (empty($fila['fecha_inicio']) || !$this->fecha_valida($fila['fecha_inicio'])) {
				$errores[] = 'fecha_inicio inválida';
			}
			if (empty($fila['fecha_fin']) || !$this->fecha_valida($fila['fecha_fin'])) {
				$errores[] = 'fecha_fin inválida';
			}

			$valido = empty($errores);
			$datos = null;
			if ($valido) {
				$datos = $fila;
				$datos['id_alumnos'] = $id_alumno;
			}

			$resultado[] = array(
				'fila' => $num,
				'valido' => $valido,
				'mensaje' => $valido ? 'OK' : implode('; ', $errores),
				'resumen' => 'DNI ' . $fila['alumno_dni'],
				'datos' => $datos,
			);
		}

		return $resultado;
	}

	protected function insertar_obra_social($fila) {
		$this->obrassociales->altaObraSocial(
			$fila['descripcion'],
			isset($fila['telefono']) ? $fila['telefono'] : '',
			isset($fila['contacto']) ? $fila['contacto'] : '',
			isset($fila['partido']) ? $fila['partido'] : '',
			isset($fila['codigo_postal']) ? $fila['codigo_postal'] : '',
			isset($fila['provincia']) ? $fila['provincia'] : '',
			isset($fila['direccion']) ? $fila['direccion'] : '',
			isset($fila['email']) ? $fila['email'] : '',
			isset($fila['localidad']) ? $fila['localidad'] : ''
		);
	}

	protected function insertar_maestra($fila) {
		if (!empty($fila['modo']) && $fila['modo'] === 'actualizar') {
			$this->actualizar_persona_importada($fila);
			return;
		}

		$datos = $this->datos_persona_desde_fila_import($fila);
		$datos['id_personas'] = $this->personas->altaPersonas($datos);
		$this->usuarios->alta($datos);
	}

	protected function actualizar_persona_importada($fila) {
		$datos = $this->datos_persona_desde_fila_import($fila);
		$datos['id_personas'] = (int) $fila['id_personas'];
		$datos['id_usuarios'] = (int) $fila['id_usuarios'];
		if (empty($datos['clave'])) {
			$datos['clave'] = '******';
		}

		$this->personas->modificarPersonas($datos);
		$this->usuarios->modificar($datos);
	}

	protected function datos_persona_desde_fila_import($fila) {
		return array(
			'nombre' => $fila['nombre'],
			'direccion' => isset($fila['direccion']) ? $fila['direccion'] : '',
			'localidad' => isset($fila['localidad']) ? $fila['localidad'] : '',
			'partido' => isset($fila['partido']) ? $fila['partido'] : '',
			'codigo_postal' => isset($fila['codigo_postal']) ? $fila['codigo_postal'] : '',
			'provincia' => isset($fila['provincia']) ? $fila['provincia'] : '',
			'telefono' => isset($fila['telefono']) ? $fila['telefono'] : '',
			'id_profesionales' => (int) $fila['id_profesionales'],
			'email' => $fila['email'],
			'id_padre' => isset($fila['id_padre']) ? $fila['id_padre'] : array(),
			'clave' => isset($fila['clave']) ? $fila['clave'] : '',
			'id_perfiles' => $fila['id_perfiles'],
		);
	}

	protected function insertar_alumno_con_plan($fila) {
		$datos = array(
			'nombre' => $fila['nombre'],
			'direccion' => isset($fila['direccion']) ? $fila['direccion'] : '',
			'localidad' => isset($fila['localidad']) ? $fila['localidad'] : '',
			'partido' => isset($fila['partido']) ? $fila['partido'] : '',
			'codigo_postal' => isset($fila['codigo_postal']) ? $fila['codigo_postal'] : '',
			'provincia' => isset($fila['provincia']) ? $fila['provincia'] : '',
			'padre' => isset($fila['padre']) ? $fila['padre'] : '',
			'telefono' => isset($fila['telefono']) ? $fila['telefono'] : '',
			'email' => isset($fila['email']) ? $fila['email'] : '',
			'dni' => $fila['dni'],
			'diagnostico' => isset($fila['diagnostico']) ? $fila['diagnostico'] : '',
			'id_obras_sociales' => $fila['id_obras_sociales'],
			'id_coordinador' => $fila['id_coordinador'],
			'servicio' => $fila['servicio'],
			'id_personas' => $fila['id_personas'],
			'fecha_inicio' => $fila['fecha_inicio'],
			'fecha_fin' => $fila['fecha_fin'],
			'acta_acuerdo' => isset($fila['acta_acuerdo']) ? $fila['acta_acuerdo'] : '',
			'escuela' => isset($fila['escuela']) ? $fila['escuela'] : '',
			'orientacion' => isset($fila['orientacion']) ? $fila['orientacion'] : '',
		);

		$id_alumnos = $this->alumnos->alta($datos);
		$datos['id_alumnos'] = $id_alumnos;
		$id_planes = $this->planes->alta($datos);

		$this->planes_dias->alta(array(
			'id_planes' => $id_planes,
			'id_clases_profesionales' => '1',
			'cantidad_horas' => '20',
		));
		$this->planes_dias->alta(array(
			'id_planes' => $id_planes,
			'id_clases_profesionales' => '3',
			'cantidad_horas' => '8',
		));
		$this->planes_dias->alta(array(
			'id_planes' => $id_planes,
			'id_clases_profesionales' => '2',
			'cantidad_horas' => '4',
		));
	}

	protected function insertar_plan($fila) {
		$datos = array(
			'id_alumnos' => $fila['id_alumnos'],
			'fecha_inicio' => $fila['fecha_inicio'],
			'fecha_fin' => $fila['fecha_fin'],
			'acta_acuerdo' => isset($fila['acta_acuerdo']) ? $fila['acta_acuerdo'] : '',
			'escuela' => isset($fila['escuela']) ? $fila['escuela'] : '',
			'orientacion' => isset($fila['orientacion']) ? $fila['orientacion'] : '',
		);
		$this->planes->alta($datos);
	}

	protected function obra_social_existe($descripcion) {
		return $this->buscar_obra_social_por_descripcion($descripcion, 1) !== null;
	}

	protected function obra_social_en_papelera($descripcion) {
		return $this->buscar_obra_social_por_descripcion($descripcion, 0) !== null;
	}

	protected function buscar_obra_social_por_descripcion($descripcion, $habilitado) {
		$this->db->select('id_obras_sociales');
		$this->db->from('obras_sociales');
		$this->db->where('LOWER(descripcion)', mb_strtolower(trim($descripcion), 'UTF-8'));
		$this->db->where('habilitado', (int) $habilitado);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_obras_sociales;
		}
		return null;
	}

	protected function resolver_obra_social($descripcion) {
		$this->db->select('id_obras_sociales');
		$this->db->from('obras_sociales');
		$this->db->where('LOWER(descripcion)', mb_strtolower(trim($descripcion), 'UTF-8'));
		$this->db->where('habilitado', 1);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_obras_sociales;
		}
		return null;
	}

	protected function usuario_existe($email) {
		return $this->buscar_usuario_activo_por_email($email) !== null;
	}

	protected function usuario_en_papelera($email) {
		return $this->buscar_usuario_por_login($email, 0) !== null;
	}

	/**
	 * @return array{id_usuarios: int, id_personas: int}|null
	 */
	protected function buscar_usuario_activo_por_email($email) {
		$login = mb_strtolower(trim((string) $email), 'UTF-8');
		if ($login === '') {
			return null;
		}

		$this->db->reset_query();
		$this->db->select('usuarios.id_usuarios, usuarios.id_personas');
		$this->db->from('usuarios');
		$this->db->join('personas', 'personas.id_personas = usuarios.id_personas');
		$this->db->where('LOWER(usuarios.nombre)', $login);
		$this->db->where('usuarios.habilitado', 1);
		$this->db->where('personas.habilitado', 1);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return array(
				'id_usuarios' => (int) $q->row()->id_usuarios,
				'id_personas' => (int) $q->row()->id_personas,
			);
		}
		return null;
	}

	protected function buscar_usuario_por_login($email, $habilitado) {
		$this->db->reset_query();
		$this->db->select('id_usuarios');
		$this->db->from('usuarios');
		$this->db->where('LOWER(nombre)', mb_strtolower(trim($email), 'UTF-8'));
		$this->db->where('habilitado', (int) $habilitado);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_usuarios;
		}
		return null;
	}

	/**
	 * Tipos profesionales que pueden ser coordinador/superior (clase Equipo, id_clases_profesionales = 2).
	 *
	 * @return int[]
	 */
	protected function ids_tipos_superiores_maestra() {
		$this->load->model('Alcance', 'alcance', TRUE);
		return $this->alcance->ids_profesionales_clase_equipo();
	}

	/** Coordinador/superior válido: cualquier persona con tipo de clase Equipo. */
	protected function resolver_superior_maestra($identificador) {
		$tipos = $this->ids_tipos_superiores_maestra();
		if (empty($tipos)) {
			return null;
		}

		$id = $this->resolver_persona_superior_por_login_usuario($identificador, $tipos, 1);
		if ($id) {
			return $id;
		}

		$id = $this->resolver_persona_superior_por_identificador($identificador, $tipos, 1);
		if ($id) {
			return $id;
		}

		foreach ($tipos as $id_tipo) {
			$id = $this->resolver_persona_por_nombre_o_email($identificador, $id_tipo, 1);
			if ($id) {
				return $id;
			}
		}
		return null;
	}

	/**
	 * Resuelve por login de usuario (perfil Equipo/Administrador no cambia el tipo profesional de la persona).
	 *
	 * @param int[] $tipos_superiores id_profesionales válidos
	 */
	protected function resolver_persona_superior_por_login_usuario($identificador, $tipos_superiores, $habilitado = 1) {
		if (strpos($identificador, '@') === false) {
			return null;
		}
		$login = mb_strtolower(trim($identificador), 'UTF-8');
		$this->db->select('personas.id_personas');
		$this->db->from('usuarios');
		$this->db->join('personas', 'personas.id_personas = usuarios.id_personas');
		$this->db->where('LOWER(usuarios.nombre)', $login);
		$this->db->where('usuarios.habilitado', (int) $habilitado);
		$this->db->where('personas.habilitado', (int) $habilitado);
		$this->db->where_in('personas.id_profesionales', $tipos_superiores);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_personas;
		}
		return null;
	}

	/**
	 * @param int[] $tipos_superiores
	 */
	protected function resolver_persona_superior_por_identificador($identificador, $tipos_superiores, $habilitado = 1) {
		$identificador = trim((string) $identificador);
		if ($identificador === '' || empty($tipos_superiores)) {
			return null;
		}
		$habilitado = (int) $habilitado;

		$this->db->select('personas.id_personas');
		$this->db->from('personas');
		$this->db->where_in('personas.id_profesionales', $tipos_superiores);
		$this->db->where('personas.habilitado', $habilitado);
		if (strpos($identificador, '@') !== false) {
			$this->db->where('LOWER(personas.email)', mb_strtolower($identificador, 'UTF-8'));
		} else {
			$this->db->where('UPPER(personas.nombre)', mb_strtoupper($identificador, 'UTF-8'));
		}
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_personas;
		}
		return null;
	}

	protected function mensaje_superior_maestra_no_encontrado($identificador) {
		$identificador = trim((string) $identificador);
		$tipos = $this->ids_tipos_superiores_maestra();
		foreach ($tipos as $id_tipo) {
			if ($this->resolver_persona_por_nombre_o_email($identificador, $id_tipo, 0) !== null) {
				return 'el superior/coordinador "' . $identificador . '" está en la papelera; restáurelo en Administración → Usuarios → Ver papelera antes de importar';
			}
		}
		$otra = $this->buscar_persona_activa_cualquier_tipo($identificador);
		if ($otra) {
			$tipos_nombres = $this->nombres_tipos_profesionales_superiores();
			if (in_array((int) $otra['id_profesionales'], $tipos, true)) {
				return 'existe "' . $identificador . '" como ' . $otra['tipo'] . ' pero no se pudo vincular; verifique que el login de usuario apunte a esa persona';
			}
			return 'existe "' . $identificador . '" con tipo profesional "' . $otra['tipo'] . '". '
				. 'El perfil Equipo/Administrador es distinto: en Usuarios debe tener tipo profesional '
				. $tipos_nombres . ' (no Maestra integradora) para poder ser coordinador en la importación';
		}
		return 'superior/coordinador no encontrado: "' . $identificador . '" (nombre o email de un profesional de clase Equipo activo ya cargado)';
	}

	protected function nombres_tipos_profesionales_superiores() {
		$tipos = $this->ids_tipos_superiores_maestra();
		if (empty($tipos)) {
			return 'profesional de clase Equipo';
		}
		$this->db->select('descripcion');
		$this->db->from('profesionales');
		$this->db->where_in('id_profesionales', $tipos);
		$this->db->where('habilitado', 1);
		$this->db->order_by('descripcion', 'asc');
		$nombres = array();
		foreach ($this->db->get()->result() as $row) {
			$nombres[] = $row->descripcion;
		}
		return empty($nombres) ? 'profesional de clase Equipo' : implode(', ', $nombres);
	}

	/**
	 * Mensaje de error si la persona no está activa: distingue papelera vs inexistente.
	 */
	protected function mensaje_persona_no_encontrada($rol, $identificador, $id_profesionales) {
		$identificador = trim((string) $identificador);
		if ($this->resolver_persona_por_nombre_o_email($identificador, $id_profesionales, 0) !== null) {
			return 'el ' . $rol . ' "' . $identificador . '" está en la papelera; restáurelo en Administración → Usuarios → Ver papelera antes de importar';
		}
		$otra = $this->buscar_persona_activa_cualquier_tipo($identificador);
		if ($otra) {
			return 'existe "' . $identificador . '" como ' . $otra['tipo'] . ', no como ' . $rol . ' válido para esta importación';
		}
		return $rol . ' no encontrado: "' . $identificador . '" (use nombre o email de un registro activo ya cargado)';
	}

	/**
	 * @return array{id_personas: int, id_profesionales: int, tipo: string}|null
	 */
	protected function buscar_persona_activa_cualquier_tipo($identificador) {
		$identificador = trim((string) $identificador);
		if ($identificador === '') {
			return null;
		}

		if (strpos($identificador, '@') !== false) {
			$login = mb_strtolower($identificador, 'UTF-8');
			$this->db->select('personas.id_personas, personas.id_profesionales, profesionales.descripcion AS tipo');
			$this->db->from('usuarios');
			$this->db->join('personas', 'personas.id_personas = usuarios.id_personas');
			$this->db->join('profesionales', 'profesionales.id_profesionales = personas.id_profesionales');
			$this->db->where('usuarios.habilitado', 1);
			$this->db->where('personas.habilitado', 1);
			$this->db->where('LOWER(usuarios.nombre)', $login);
			$this->db->limit(1);
			$q = $this->db->get();
			if ($q->num_rows() > 0) {
				$row = $q->row();
				return array(
					'id_personas' => (int) $row->id_personas,
					'id_profesionales' => (int) $row->id_profesionales,
					'tipo' => $row->tipo,
				);
			}
		}

		$this->db->select('personas.id_personas, personas.id_profesionales, profesionales.descripcion AS tipo');
		$this->db->from('personas');
		$this->db->join('profesionales', 'profesionales.id_profesionales = personas.id_profesionales');
		$this->db->where('personas.habilitado', 1);
		if (strpos($identificador, '@') !== false) {
			$this->db->where('LOWER(personas.email)', mb_strtolower($identificador, 'UTF-8'));
		} else {
			$this->db->where('UPPER(personas.nombre)', mb_strtoupper($identificador, 'UTF-8'));
		}
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() === 0) {
			return null;
		}
		$row = $q->row();
		return array(
			'id_personas' => (int) $row->id_personas,
			'id_profesionales' => (int) $row->id_profesionales,
			'tipo' => $row->tipo,
		);
	}

	protected function alumno_dni_existe($dni) {
		$this->db->from('alumnos');
		$this->db->where('dni', trim($dni));
		$this->db->where('habilitado', 1);
		return $this->db->count_all_results() > 0;
	}

	protected function resolver_alumno_por_dni($dni) {
		$this->db->select('id_alumnos');
		$this->db->from('alumnos');
		$this->db->where('dni', trim($dni));
		$this->db->where('habilitado', 1);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_alumnos;
		}
		return null;
	}

	/**
	 * Busca persona por nombre, email en personas o login en usuarios (si el valor contiene @).
	 *
	 * @param int $habilitado 1 activos, 0 papelera
	 */
	protected function resolver_persona_por_nombre_o_email($identificador, $id_profesionales, $habilitado = 1) {
		$identificador = trim($identificador);
		if ($identificador === '') {
			return null;
		}
		$habilitado = (int) $habilitado;

		$this->db->select('personas.id_personas');
		$this->db->from('personas');
		$this->db->where('personas.id_profesionales', (int) $id_profesionales);
		$this->db->where('personas.habilitado', $habilitado);
		if (strpos($identificador, '@') !== false) {
			$this->db->where('LOWER(personas.email)', mb_strtolower($identificador, 'UTF-8'));
		} else {
			$this->db->where('UPPER(personas.nombre)', mb_strtoupper($identificador, 'UTF-8'));
		}
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_personas;
		}

		if (strpos($identificador, '@') === false) {
			return null;
		}

		$this->db->select('personas.id_personas');
		$this->db->from('usuarios');
		$this->db->join('personas', 'personas.id_personas = usuarios.id_personas');
		$this->db->where('personas.id_profesionales', (int) $id_profesionales);
		$this->db->where('personas.habilitado', $habilitado);
		$this->db->where('LOWER(usuarios.nombre)', mb_strtolower($identificador, 'UTF-8'));
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (int) $q->row()->id_personas;
		}

		return null;
	}

	protected function resolver_maestra($identificador) {
		return $this->resolver_persona_por_nombre_o_email($identificador, 2);
	}

	/** Lee tipo profesional del Excel (organigrama, no confundir con perfil de usuario). */
	protected function valor_tipo_profesional_fila($fila) {
		$claves = array('tipo_profesional', 'profesional', 'id_profesionales');
		foreach ($claves as $clave) {
			if (!empty($fila[$clave])) {
				return $this->limpiar_texto_celda((string) $fila[$clave]);
			}
		}
		return '';
	}

	/**
	 * @param string $descripcion descripción en tabla profesionales
	 * @param string|null $default_si_vacio valor por defecto si la celda está vacía
	 */
	protected function resolver_tipo_profesional($descripcion, $default_si_vacio = null) {
		$descripcion = $this->limpiar_texto_celda((string) $descripcion);
		if ($descripcion === '' && $default_si_vacio !== null) {
			$descripcion = $this->limpiar_texto_celda((string) $default_si_vacio);
		}
		if ($descripcion === '') {
			return null;
		}
		if (ctype_digit($descripcion)) {
			$id = (int) $descripcion;
			$this->db->from('profesionales');
			$this->db->where('id_profesionales', $id);
			$this->db->where('habilitado', 1);
			return $this->db->count_all_results() > 0 ? $id : null;
		}
		$this->load->model('profesionales', '', TRUE);
		$lista = $this->profesionales->get();
		if (!$lista) {
			return null;
		}
		$buscar = mb_strtolower($descripcion, 'UTF-8');
		foreach ($lista as $p) {
			if (mb_strtolower($p->descripcion, 'UTF-8') === $buscar) {
				return (int) $p->id_profesionales;
			}
		}
		return null;
	}

	/** Solo tipos que en el organigrama pueden tener superiores en personas_padre (ej. maestra integradora). */
	protected function tipo_profesional_puede_tener_coordinadores($id_profesionales) {
		$this->load->model('profesionales', '', TRUE);
		$id = (int) $id_profesionales;
		$prof = $this->profesionales->get($id);
		if (!$prof || !isset($prof[0]->id_padre) || $prof[0]->id_padre === null || $prof[0]->id_padre === '') {
			return false;
		}
		return true;
	}

	protected function resolver_perfil($descripcion) {
		if (trim($descripcion) === '') {
			return null;
		}
		$perfiles = $this->perfiles->get();
		if (!$perfiles) {
			return null;
		}
		$buscar = mb_strtolower(trim($descripcion), 'UTF-8');
		foreach ($perfiles as $p) {
			if (mb_strtolower($p->descripcion, 'UTF-8') === $buscar) {
				return (int) $p->id_perfiles;
			}
		}
		return null;
	}

	protected function fecha_valida($fecha) {
		return $this->parsear_fecha_a_dmY($fecha) !== null;
	}

	/** Año de dos dígitos de Excel (26 → 2026). */
	protected function expandir_anio_importacion($anio) {
		$anio = (int) $anio;
		if ($anio >= 100) {
			return $anio;
		}
		return 2000 + $anio;
	}

	/**
	 * Interpreta fechas de importación (texto, Excel serial, dd/mm/aa).
	 *
	 * @return string|null fecha en dd/mm/aaaa
	 */
	protected function parsear_fecha_a_dmY($fecha) {
		if ($fecha instanceof \DateTimeInterface) {
			return $fecha->format('d/m/Y');
		}

		$fecha = $this->limpiar_texto_celda((string) $fecha);
		if ($fecha === '') {
			return null;
		}

		// Serial Excel (número puro o con decimales)
		if (preg_match('/^\d+(\.\d+)?$/', $fecha)) {
			$serial = (float) $fecha;
			if ($serial > 25000 && $serial < 80000) {
				$convertida = $this->excel_serial_a_dmY($serial);
				if ($convertida !== null) {
					return $convertida;
				}
			}
		}

		// ISO aaaa-mm-dd[ hh:mm:ss]
		if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $fecha, $m)) {
			$dia = (int) $m[3];
			$mes = (int) $m[2];
			$anio = (int) $m[1];
			if (checkdate($mes, $dia, $anio)) {
				return sprintf('%02d/%02d/%04d', $dia, $mes, $anio);
			}
		}

		// Unificar guiones Unicode a ASCII
		$fecha = str_replace(
			array("\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x88\x92"),
			'-',
			$fecha
		);

		$sep = strpos($fecha, '/') !== false ? '/' : (strpos($fecha, '-') !== false ? '-' : null);
		if ($sep === null) {
			return null;
		}

		$partes = explode($sep, $fecha);
		if (count($partes) !== 3) {
			return null;
		}

		$p1 = (int) $partes[0];
		$p2 = (int) $partes[1];
		$anio = $this->expandir_anio_importacion((int) $partes[2]);

		// dd/mm/aaaa (prioridad Argentina)
		if (checkdate($p2, $p1, $anio)) {
			return sprintf('%02d/%02d/%04d', $p1, $p2, $anio);
		}
		// mm/dd/aaaa (Excel en locale US: 12/20/26)
		if (checkdate($p1, $p2, $anio)) {
			return sprintf('%02d/%02d/%04d', $p2, $p1, $anio);
		}

		return null;
	}

	protected function excel_serial_a_dmY($serial) {
		if (class_exists('\PhpOffice\PhpSpreadsheet\Shared\Date')) {
			try {
				$dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $serial);
				return $dt->format('d/m/Y');
			} catch (Exception $e) {
				// fallback manual
			}
		}
		$unix = (int) round(((float) $serial - 25569) * 86400);
		if ($unix <= 0) {
			return null;
		}
		return gmdate('d/m/Y', $unix);
	}

	/** URL de retorno tras importar */
	public function url_retorno($tipo) {
		$map = array(
			'obrassociales' => 'administracion/obrassociales',
			'maestras' => 'administracion/personas',
			'alumnos' => 'administracion/alumnos',
			'planes' => 'administracion/planes',
		);
		return isset($map[$tipo]) ? $map[$tipo] : 'administracion';
	}

	/** Función de menú para permisos */
	public function funcion_permiso($tipo) {
		$map = array(
			'obrassociales' => 'obrassociales',
			'maestras' => 'personas',
			'alumnos' => 'alumnos',
			'planes' => 'planes',
		);
		return isset($map[$tipo]) ? $map[$tipo] : 'personas';
	}

	/**
	 * Normaliza cada fila leída del Excel antes de validar.
	 */
	protected function normalizar_fila_importacion($fila) {
		$claves_fecha = array('fecha_inicio', 'fecha_fin');
		foreach ($fila as $clave => $valor) {
			if ($clave === '_fila' || !is_scalar($valor)) {
				continue;
			}
			$valor = $this->limpiar_texto_celda((string) $valor);
			if (in_array($clave, $claves_fecha, true)) {
				$valor = $this->normalizar_fecha_importacion($valor);
			} elseif (strpos($valor, '@') !== false) {
				$valor = mb_strtolower($valor, 'UTF-8');
			}
			$fila[$clave] = $valor;
		}
		return $fila;
	}

	/** Convierte fechas de Excel (serial, ISO, dd/mm/aa) al formato dd/mm/aaaa. */
	protected function normalizar_fecha_importacion($fecha) {
		$parsed = $this->parsear_fecha_a_dmY($fecha);
		return $parsed !== null ? $parsed : $this->limpiar_texto_celda((string) $fecha);
	}

	protected function limpiar_texto_celda($texto) {
		$texto = preg_replace('/^\x{FEFF}/u', '', $texto);
		$texto = str_replace(array("\xc2\xa0", "\x00"), array(' ', ''), $texto);
		$texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $texto);
		return trim($texto);
	}

	protected function email_valido($email) {
		$email = $this->limpiar_texto_celda((string) $email);
		if ($email === '') {
			return false;
		}
		return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * @param bool $obligatorio
	 * @return string|null mensaje de error
	 */
	protected function validar_campo_email($email, $obligatorio = true) {
		$email = $this->limpiar_texto_celda((string) $email);
		if ($email === '') {
			return $obligatorio ? 'email es obligatorio' : null;
		}
		if (!$this->email_valido($email)) {
			return 'email con formato inválido';
		}
		return null;
	}

	/**
	 * Si el valor parece un email (contiene @), valida el formato.
	 *
	 * @return string|null mensaje de error
	 */
	protected function validar_identificador_con_email($identificador, $nombre_campo) {
		$identificador = $this->limpiar_texto_celda((string) $identificador);
		if ($identificador === '' || strpos($identificador, '@') === false) {
			return null;
		}
		if (!$this->email_valido($identificador)) {
			return $nombre_campo . ' con formato de email inválido';
		}
		return null;
	}

	public function titulo_tipo($tipo) {
		$titulos = array(
			'obrassociales' => 'Obras sociales',
			'maestras' => 'Maestras integradoras',
			'alumnos' => 'Alumnos',
			'planes' => 'Planes',
		);
		return isset($titulos[$tipo]) ? $titulos[$tipo] : $tipo;
	}
}
