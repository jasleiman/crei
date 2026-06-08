<?php

Class Menus extends CI_Model {

	function agregarPermitido($idMenu) {
		//agrega al array $this->menuPermitido el idMenu pasado por parametros

		$this -> menuPermitido[$idMenu] = $idMenu;
	}

	function estaPermitido($idMenu) {
		//indica si el idMenu pasado por parametro esta dentro de la lista de permitidos para el usuario actual
		if (isset($this -> menuPermitido[$idMenu])) {
			return true;
		} else
			return false;
	}

	function cargarPrimerNivel($id) {
		$query2 = 'SELECT m.id_menus,m.descripcion, m.orden, m.accion';
		$query2 .= ' FROM menus as m';
		$query2 .= ' INNER JOIN perfiles_menus as p on p.id_menus=m.id_menus';
		$query2 .= ' WHERE m.id_padre IS NULL';
		$query2 .= ' AND id_perfiles in (SELECT id_perfiles FROM usuarios WHERE id_usuarios = ' . $id . ')';
		$query2 .= ' AND habilitado=1';
		$query2 .= ' AND imprimir=1';
		$query2 .= ' ORDER BY orden';

		//echo $query2;
		$query = $this -> db -> query($query2);

		if (!$query) {
			return false;
		} else {
			if ($query -> num_rows() >= 1) {
				$resultado = $query -> result();

				return $resultado;
			} else {
				return false;
			}
		}
	}

	function cargarHijos($id, $id_menu) {

		$query2 = 'SELECT m.id_menus,m.descripcion, m.orden,m.accion';
		$query2 .= ' FROM menus as m';
		$query2 .= ' INNER JOIN perfiles_menus as p on p.id_menus=m.id_menus';
		$query2 .= ' WHERE m.id_padre = ' . $id_menu;
		$query2 .= ' AND id_perfiles in (SELECT id_perfiles FROM usuarios WHERE id_usuarios = ' . $id . ')';
		$query2 .= ' AND habilitado=1';
		$query2 .= ' AND imprimir=1';
		$query2 .= ' ORDER BY orden';

		//echo $query2;
		$query = $this -> db -> query($query2);

		if (!$query) {
			return false;
		} else {
			if ($query -> num_rows() >= 1) {
				$resultado = $query -> result();

				return $resultado;
			} else {
				return false;
			}
		}
	}

	function estaHabilitado($menu, $id_usuarios) {
		$this -> db -> where('m.accion', $menu);
		$this -> db -> where('u.id_usuarios', $id_usuarios);
		$this -> db -> select('m.id_menus');
		$this -> db -> from('menus as m');
		$this -> db -> join('perfiles_menus as p', 'p.id_menus=m.id_menus');
		$this -> db -> join('usuarios as u', 'u.id_perfiles=p.id_perfiles');
		$query = $this -> db -> get();
		//echo $this->db->last_query();
		if ($query -> num_rows() > 0) {
			return true;
		} else
			return false;
	}

	function imprimirMenu($id) {
		//precondiciones: se debe haber ejecutado el método cargarMenu y el menu debe estar correctamente cargado en el array $this->menu
		//postcondiciones: el menu queda impreso en pantalla
		$varmenu = '';
		$varmenu2 = $this -> cargarPrimerNivel($id);
                $session_data = $this -> session -> userdata('logged_in');
		foreach ($varmenu2 as $elem) {
                    /**
                     * Equipo: no muestra «Cargar horas» individual (usa carga de equipo).
                     * Apoyo + Maestra: no muestra «Cargar horas equipo».
                     */
                    if ((int) $session_data['id_perfiles'] === 3 && (int) $elem->id_menus === 1) {
                        continue;
                    }
                    if ((int) $session_data['id_perfiles'] === 4 && (int) $elem->id_menus === 43) {
                        continue;
                    }
			$hijos = $this -> cargarHijos($id, $elem -> id_menus);
			if ($hijos <> false) {
				$varmenu .= '<li class="accordion">
                            <a href="' . $elem -> accion . '"><i class="glyphicon glyphicon-plus"></i><span>' . $elem -> descripcion . '</span></a>
                            <ul class="nav nav-pills nav-stacked"> ';
				foreach ($hijos as $item) {
					$varmenu .= '<li><a href="' . base_url() . 'index.php/' . $item -> accion . '">' . $item -> descripcion . '</a></li>';
				}
				$varmenu .= '</ul></li> ';
			} else
				$varmenu .= '<li><a class="ajax-link" href="' . base_url() . 'index.php/' . $elem -> accion . '"><span>' . $elem -> descripcion . '</span></a></li>';
		}
		return $varmenu;
	}

}
?>