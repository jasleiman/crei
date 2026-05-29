<?php

class Personas extends CI_Model {

    function get($id) {
        $this->db->select('personas.id_personas as id,personas.id_personas, UPPER(nombre) as nombre,email,telefono,direccion,localidad,partido,codigo_postal,provincia,id_profesionales,habilitado');
        $this->db->from('personas');
        $this->db->where('personas.id_personas', $id);
        $this->db->where('habilitado', '1');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    function getMI($id = 0) {
        $this->load->model('Alcance', 'alcance', TRUE);
        $ids = $this->alcance->ids_maestras_visibles();

        $this->db->SELECT('id_personas,UPPER(nombre) AS nombre');
        $this->db->from('personas');
        if ($ids !== null) {
            if (empty($ids)) {
                return false;
            }
            $this->db->where_in('id_personas', $ids);
        }
        $this->db->where('id_profesionales', '2');
        $this->db->where('habilitado', '1');
        $this->db->order_by('nombre asc');

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        if ($this->alcance->es_maestra()) {
            $row = new stdClass();
            $row->id_personas = $this->alcance->id_personas_sesion();
            $p = $this->get($row->id_personas);
            $row->nombre = $p ? $p[0]->nombre : '';
            return array($row);
        }
        return false;
    }

    function getMITodos($id = 0) {
        return $this->getMI($id);
    }

    function getCoordinador($id = 0) {
        $this->load->model('Alcance', 'alcance', TRUE);
        $tipos = $this->alcance->ids_profesionales_clase_equipo();
        if (empty($tipos)) {
            return false;
        }

        $this->db->reset_query();
        $this->db->SELECT('id_personas,UPPER(nombre) AS nombre');
        $this->db->from('personas');
        $this->db->where_in('id_profesionales', $tipos);
        $this->db->where('habilitado', '1');
        $this->db->order_by('nombre asc');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function bajaPersonas($datos) {
        $id = (int) $datos['id_personas'];
        $data = array('habilitado' => 0);
        $this->db->WHERE('id_personas', $id);
        $this->db->UPDATE('personas', $data);
        $this->load->model('usuarios', '', TRUE);
        $usuarios = $this->usuarios->getDesdePersona($id);
        if ($usuarios) {
            foreach ($usuarios as $u) {
                $this->usuarios->eliminar($u->id_usuarios);
            }
        }
    }

    function altaPersonas($datos) {
        $data = array('nombre' => $datos['nombre'], 'direccion' => $datos['direccion'], 'localidad' => $datos['localidad'], 'partido' => $datos['partido'], 'codigo_postal' => $datos['codigo_postal'], 'provincia' => $datos['provincia'], 'telefono' => $datos['telefono'], 'id_profesionales' => $datos['id_profesionales'], 'email' => $datos['email'], 'id_padre' => $datos['id_padre'][0], 'habilitado' => 1);
        $this->db->INSERT('personas', $data);
        $id_personas = $this->db->insert_id();
        $datos['id_personas'] = $id_personas;
        $this->agregarPersonasPadre($datos);
        return $datos['id_personas'];
    }

    function modificarPersonas($datos) {
        $data = array('nombre' => $datos['nombre'], 'direccion' => $datos['direccion'], 'localidad' => $datos['localidad'], 'partido' => $datos['partido'], 'codigo_postal' => $datos['codigo_postal'], 'provincia' => $datos['provincia'], 'telefono' => $datos['telefono'], 'id_profesionales' => $datos['id_profesionales'], 'email' => $datos['email']);
        $this->db->WHERE('id_personas', $datos['id_personas']);
        $this->db->UPDATE('personas', $data);
        $this->agregarPersonasPadre($datos);
        return $datos['id_personas'];
    }

    function agregarPersonasPadre($datos) {
        if (sizeof($datos['id_padre']) > 0) {
            $this->db->where('id_personas', $datos['id_personas']);
            $this->db->delete('personas_padre');
            if (!is_array($datos['id_padre'])) {
                $arr = array($datos['id_padre']);
                $datos['id_padre'] = $arr;
            }
            foreach ($datos['id_padre'] as $padre) {
                $data = array('id_personas' => $datos['id_personas'], 'id_padre' => $padre);
                $this->db->INSERT('personas_padre', $data);
            }
        }
    }

    function obtenerPersonas() {
        $this->load->model('Alcance', 'alcance', TRUE);
        $this->db->where('habilitado', '1');
        $this->alcance->aplicar_filtro_personas_listado();
        $query = $this->db->GET('personas');
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    function obtenerPersonasPadre($id_profesionales) {
        $id_profesionales = (int) $id_profesionales;
        if ($id_profesionales <= 0) {
            return false;
        }

        $this->load->model('profesionales', '', TRUE);
        $this->load->model('Alcance', 'alcance', TRUE);

        // Maestra integradora: cualquier profesional de clase Equipo puede ser coordinador
        if ($id_profesionales === Alcance::PROF_MAESTRA) {
            $tipos = $this->alcance->ids_profesionales_clase_equipo();
        } else {
            // Demás tipos: superiores según organigrama (cadena id_padre del tipo seleccionado)
            $tipos = $this->tipos_superiores_organigrama($id_profesionales);
        }

        return $this->personas_activas_por_tipos_profesionales($tipos);
    }

    /**
     * Tipos profesionales superiores inmediatos en el organigrama (id_padre).
     *
     * @return int[]
     */
    protected function tipos_superiores_organigrama($id_profesionales) {
        $tipos = array();
        $visitados = array();
        $id = (int) $id_profesionales;

        while ($id > 0 && !in_array($id, $visitados, true)) {
            $visitados[] = $id;
            $prof = $this->profesionales->get($id);
            if (!$prof || !isset($prof[0]->id_padre) || $prof[0]->id_padre === null || $prof[0]->id_padre === '') {
                break;
            }
            $id_padre = (int) $prof[0]->id_padre;
            $tipos[] = $id_padre;
            $id = $id_padre;
        }

        return array_values(array_unique($tipos));
    }

    /**
     * @param int[] $tipos id_profesionales
     * @return array<int,string>|false
     */
    protected function personas_activas_por_tipos_profesionales($tipos) {
        if (empty($tipos)) {
            return false;
        }

        $this->db->reset_query();
        $this->db->select('id_personas, UPPER(nombre) AS nombre');
        $this->db->from('personas');
        $this->db->where_in('id_profesionales', $tipos);
        $this->db->where('habilitado', '1');
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get();

        $resultado = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $resultado[(int) $row['id_personas']] = $row['nombre'];
            }
        }

        return !empty($resultado) ? $resultado : false;
    }

    function obtenerIdPadre($id_personas) {
        $resultado = array();
        $id_personas = (int) $id_personas;
        if ($id_personas <= 0) {
            return $resultado;
        }

        $this->db->reset_query();
        $this->db->where('id_personas', $id_personas);
        $this->db->select('id_padre');
        $this->db->from('personas_padre');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $item) {
                $resultado[] = (int) $item['id_padre'];
            }
        }

        return $resultado;
    }

    function getNoCargadas() {
        $resultado = array();
        $fecha = date('Y-m-d', strtotime('last Saturday'));
        $this->db->WHERE('id_profesionales <>', 9);
        $this->db->WHERE('personas.habilitado', 1);
        $this->db->where('`id_personas` NOT IN (SELECt id_personas from actividades where hora_carga > \'' . $fecha . '\' and id_personas is not null group by id_personas)', NULL, false);
        $this->db->select('email');
        $this->db->from('personas');
        $this->db->group_by('email');
        $query = $this->db->get();
        //echo $this->db->last_query();

        if ($query->num_rows() > 0) {

            $qr = $query->result_array();
            foreach ($qr as $item) {
                $resultado[] = $item['email'];
            }
        }



        if (sizeof($qr) > 0)
            return $resultado;
        else
            return false;
    }

    function getEnvioInforme() {

        $this->db->WHERE('id_profesionales <>', 9);
        $this->db->WHERE('habilitado', 1);

        $this->db->select('id_personas,email');
        $this->db->from('personas');

        $query = $this->db->get();
        //echo $this->db->last_query();

        if ($query->num_rows() > 0) {

            return $query->result();
        } else
            return false;
    }
}

?>
