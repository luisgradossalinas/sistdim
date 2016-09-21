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
    
    public function obtenerActividadPuesto($puesto) {
        return $this->getAdapter()->select()->from(array('a' => $this->_name))
                ->joinInner(array('p' => Application_Model_Puesto::TABLA), 'p.id_puesto = a.id_puesto',
                        array('id_puesto','puesto' => 'descripcion','codigo_grupo','codigo_familia'))
                        ->where('a.id_puesto = ?', $puesto)
                        ->where('a.estado = ?', self::ESTADO_ACTIVO)
                        //->order('codigo_actividad asc')
                        ->query()->fetchAll();
    }

}
