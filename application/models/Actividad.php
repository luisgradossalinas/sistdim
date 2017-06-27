<?php

class Application_Model_Actividad extends Zend_Db_Table {

    protected $_name = 'actividad';
    protected $_primary = 'id_actividad';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'actividad';

    public function guardar($datos) {
        $id = 0;
        if (!empty($datos["id_actividad"])) {
            $id = (int) $datos["id_actividad"];
        }

        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id_actividad = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }

        return $id;
    }

    public function combo() {
        return $this->getAdapter()->select()->from($this->_name, array('key' => 'id_actividad', 'value' => 'descripcion'))
                        ->where('estado = ?', self::ESTADO_ACTIVO)->query()->fetchAll();
    }

    public function obtenerActividad($proyecto, $proceso, $nivel) {
        return $this->getAdapter()->select()->from($this->_name)
                        ->where('id_proyecto = ?', $proyecto)
                        ->where('id_proceso = ?', $proceso)
                        ->where('nivel = ?', $nivel)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->order('codigo_actividad asc')
                        ->query()->fetchAll();
    }

    public function obtenerMaxPosicion($nivel, $proceso) {

        return $this->getAdapter()->select()->from($this->_name, array('max(codigo_actividad)'))
                        ->where('nivel = ?', $nivel)
                        ->where('id_proceso = ?', $proceso)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->query()->fetchColumn();
    }

    public function cambiarPosicion($nivel, $proceso, $actividad, $anterior, $nueva) {

        //Verificar cuál es el mayor
        $mayor = $anterior;
        $menor = $nueva;
        $tipo = 'suma';

        if ($nueva > $anterior) {
            $mayor = $nueva;
            $menor = $anterior;
            $tipo = 'resta';
        }

        $maximo = $this->obtenerMaxPosicion($nivel, $proceso);
        if ($mayor > $maximo) {
            $mayor = $maximo;
            $nueva = $mayor;
        }

        //Obtener todos los registros que se van a afectar por el cambio de posición
        $dataAct = $this->getAdapter()->select()->from($this->_name)
                        ->where('nivel = ?', $nivel)
                        ->where('id_proceso = ?', $proceso)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->where('codigo_actividad between ' . $menor . " and " . $mayor)
                        ->query()->fetchAll();

        foreach ($dataAct as $value) {

            if ($actividad == $value['id_actividad']) {
                $this->update(array('codigo_actividad' => $nueva), 'id_actividad = ' . $actividad);
            } else {
                if ($tipo == 'resta') {
                    $this->update(array('codigo_actividad' => $value['codigo_actividad'] - 1), 'id_actividad = ' . $value['id_actividad']);
                } else {
                    $this->update(array('codigo_actividad' => $value['codigo_actividad'] + 1), 'id_actividad = ' . $value['id_actividad']);
                }
            }
        }

        return 'Posición actualizada';
    }

    public function obtenerActividadTareaPuesto($puesto) {

        $sqlActividades = $this->getAdapter()->select()->from(array('a' => $this->_name), array('id_actividad', 'codigo_actividad',
                    'descripcion', 'id_proceso', 'nivel', 'id_tarea' => new Zend_Db_Expr("0"), 'tarea' => new Zend_Db_Expr("0"),
                    'id_puesto', 'id_nivel_puesto', 'id_categoria_puesto', 'nombre_puesto', 'codigo_grupo', 'codigo_familia', 'codigo_rol_puesto'))
                ->joinInner(array('p' => Application_Model_Puesto::TABLA), 'p.id_puesto = a.id_puesto', array('puesto' => 'descripcion'))
                ->where('a.id_puesto = ?', $puesto);

        $sqlTarea = $this->getAdapter()->select()->from(array('a' => $this->_name), array('id_actividad', 'codigo_actividad',
                    'descripcion', 'id_proceso', 'nivel'))
                ->joinInner(array('t' => Application_Model_Tarea::TABLA), 't.id_actividad = a.id_actividad', array('id_tarea', 'tarea' => 'descripcion', 'id_puesto', 'id_nivel_puesto', 'id_categoria_puesto', 'nombre_puesto', 'codigo_grupo', 'codigo_familia', 'codigo_rol_puesto'))
                ->joinInner(array('p' => Application_Model_Puesto::TABLA), 'p.id_puesto = t.id_puesto', array('puesto' => 'descripcion'))
                ->where('t.id_puesto = ?', $puesto)
                ->where('a.estado = ?', self::ESTADO_ACTIVO);

        return $this->getAdapter()->select()
                        ->union(array($sqlActividades, $sqlTarea))
                        ->order(array('nivel asc', 'id_proceso asc', 'codigo_actividad asc'))->query()->fetchAll();
    }

    public function obtenerActividadTareaDotacion($puesto) {

        $sqlActividades = $this->getAdapter()->select()->from(array('a' => $this->_name), array('id_actividad', 'codigo_actividad',
                    'descripcion', 'id_proceso', 'nivel', 'id_tarea' => new Zend_Db_Expr("0"), 'tarea' => new Zend_Db_Expr("0"), 'id_periodicidad', 'frecuencia', 'id_tiempo', 'duracion'))
                ->where('a.id_puesto = ?', $puesto)
                ->where('a.estado = ?', self::ESTADO_ACTIVO);

        $sqlTarea = $this->getAdapter()->select()->from(array('a' => $this->_name), array('id_actividad', 'codigo_actividad',
                    'descripcion', 'id_proceso', 'nivel'))
                ->joinInner(array('t' => Application_Model_Tarea::TABLA), 't.id_actividad = a.id_actividad', array('id_tarea', 'tarea' => 'descripcion', 'id_periodicidad', 'frecuencia', 'id_tiempo', 'duracion'))
                ->where('t.id_puesto = ?', $puesto)
                ->where('a.estado = ?', self::ESTADO_ACTIVO);

        return $this->getAdapter()->select()
                        ->union(array($sqlActividades, $sqlTarea))->order(array('nivel asc', 'id_proceso asc', 'codigo_actividad asc'))->query()->fetchAll();
    }

    /* Esta función es para obtener todos los nombres de los niveles de procesos y 
      mostrarlas en el registro de tiempos y frecuencias
     */

    public function obtenerNombreNiveles($nivel, $proceso) {

        if ($nivel == 1) { //Buscar nombre nivel0,nivel1
            $data = $this->getAdapter()->select()->from(array('n0' => Application_Model_Proceso0::TABLA), array('nivel0' => 'descripcion'))
                            ->joinInner(array('n1' => Application_Model_Proceso1::TABLA), 'n1.id_proceso_n0 = n0.id_proceso_n0', array('nivel1' => 'descripcion'))
                            ->where('n1.id_proceso_n1 = ?', $proceso)->query()->fetch();

            $data['nivel2'] = '';
            $data['nivel3'] = '';
            $data['nivel4'] = '';
        } else if ($nivel == 2) { //Buscar nombre nivel0,nivel1,nivel2
            $data = $this->getAdapter()->select()->from(array('n0' => Application_Model_Proceso0::TABLA), array('nivel0' => 'descripcion'))
                            ->joinInner(array('n1' => Application_Model_Proceso1::TABLA), 'n1.id_proceso_n0 = n0.id_proceso_n0', array('nivel1' => 'descripcion'))
                            ->joinInner(array('n2' => Application_Model_Proceso2::TABLA), 'n2.id_proceso_n1 = n1.id_proceso_n1', array('nivel2' => 'descripcion'))
                            ->where('n2.id_proceso_n2 = ?', $proceso)->query()->fetch();

            $data['nivel3'] = '';
            $data['nivel4'] = '';
        } else if ($nivel == 3) { //Buscar nombre nivel0,nivel1,nivel2,nivel3
            $data = $this->getAdapter()->select()->from(array('n0' => Application_Model_Proceso0::TABLA), array('nivel0' => 'descripcion'))
                            ->joinInner(array('n1' => Application_Model_Proceso1::TABLA), 'n1.id_proceso_n0 = n0.id_proceso_n0', array('nivel1' => 'descripcion'))
                            ->joinInner(array('n2' => Application_Model_Proceso2::TABLA), 'n2.id_proceso_n1 = n1.id_proceso_n1', array('nivel2' => 'descripcion'))
                            ->joinInner(array('n3' => Application_Model_Proceso3::TABLA), 'n3.id_proceso_n2 = n2.id_proceso_n2', array('nivel3' => 'descripcion'))
                            ->where('n3.id_proceso_n3 = ?', $proceso)->query()->fetch();

            $data['nivel4'] = '';
        } else if ($nivel == 4) { //Buscar todos los niveles
            $data = $this->getAdapter()->select()->from(array('n0' => Application_Model_Proceso0::TABLA), array('nivel0' => 'descripcion'))
                            ->joinInner(array('n1' => Application_Model_Proceso1::TABLA), 'n1.id_proceso_n0 = n0.id_proceso_n0', array('nivel1' => 'descripcion'))
                            ->joinInner(array('n2' => Application_Model_Proceso2::TABLA), 'n2.id_proceso_n1 = n1.id_proceso_n1', array('nivel2' => 'descripcion'))
                            ->joinInner(array('n3' => Application_Model_Proceso3::TABLA), 'n3.id_proceso_n2 = n2.id_proceso_n2', array('nivel3' => 'descripcion'))
                            ->joinInner(array('n4' => Application_Model_Proceso4::TABLA), 'n4.id_proceso_n3 = n3.id_proceso_n3', array('nivel4' => 'descripcion'))
                            ->where('n4.id_proceso_n4 = ?', $proceso)->query()->fetch();
        }

        return $data;
    }

    /*
     * Función para validar las actividades a la hora de eliminar el proceso de nivel 4
     */

    public function obtenerActividadesVal($proceso4, $nivel) {

        return $this->getAdapter()->select()->from(array('n4' => $this->_name))
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->where('id_proceso = ?', $proceso4)
                        ->where('nivel = ?', $nivel)
                        ->query()->fetchAll();
    }

    public function eliminarActividad($act) {

        $data['estado'] = self::ESTADO_ELIMINADO;
        $this->update($data, $this->_primary . ' = ' . $act);

        $dataActividad = $this->getAdapter()->select()->from($this->_name)->where('id_actividad = ?', $act)->query()->fetchAll();

        //Puesto
        return $dataActividad[0]['id_puesto'];
    }


}
