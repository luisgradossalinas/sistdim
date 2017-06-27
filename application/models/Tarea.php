<?php

class Application_Model_Tarea extends Zend_Db_Table {

    protected $_name = 'tarea';
    protected $_primary = 'id_tarea';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'tarea';

    public function guardar($datos) {
        $id = 0;
        if (!empty($datos["id_tarea"])) {
            $id = (int) $datos["id_tarea"];
        }

        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id_tarea = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }
        return $id;
    }

    public function combo() {
        return $this->getAdapter()->select()->from($this->_name, array('key' => 'id_tarea', 'value' => 'descripcion'))
                        ->where('estado = ?', self::ESTADO_ACTIVO)->query()->fetchAll();
    }

    public function obtenerTarea($proyecto, $actividad) {
        return $this->getAdapter()->select()->from($this->_name)
                        ->where('id_proyecto = ?', $proyecto)
                        ->where('id_actividad = ?', $actividad)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->order('codigo_tarea asc')
                        ->query()->fetchAll();
    }

    public function obtenerMaxPosicion($actividad) {

        return $this->getAdapter()->select()->from($this->_name, array('max(codigo_tarea)'))
                        ->where('id_actividad = ?', $actividad)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->query()->fetchColumn();
    }

    public function cambiarPosicion($actividad, $tarea, $anterior, $nueva) {

        //Verificar cuál es el mayor
        $mayor = $anterior;
        $menor = $nueva;
        $tipo = 'suma';

        if ($nueva > $anterior) {
            $mayor = $nueva;
            $menor = $anterior;
            $tipo = 'resta';
        }

        $maximo = $this->obtenerMaxPosicion($actividad);
        if ($mayor > $maximo) {
            $mayor = $maximo;
            $nueva = $mayor;
        }

        //Obtener todos los registros que se van a afectar por el cambio de posición
        $dataTarea = $this->getAdapter()->select()->from($this->_name)
                        ->where('id_actividad = ?', $actividad)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->where('codigo_tarea between ' . $menor . " and " . $mayor)
                        ->query()->fetchAll();

        foreach ($dataTarea as $value) {

            if ($tarea == $value['id_tarea']) {
                $this->update(array('codigo_tarea' => $nueva), 'id_tarea = ' . $tarea);
            } else {
                if ($tipo == 'resta') {
                    $this->update(array('codigo_tarea' => $value['codigo_tarea'] - 1), 'id_tarea = ' . $value['id_tarea']);
                } else {
                    $this->update(array('codigo_tarea' => $value['codigo_tarea'] + 1), 'id_tarea = ' . $value['id_tarea']);
                }
            }
        }

        return 'Posición actualizada';
    }
    
    /*
     * Función para validar las tareas a la hora de eliminar una actividad 
     */
    public function obtenerTareasVal($actividad) {
        
        return $this->getAdapter()->select()->from(array('n4' => $this->_name))
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->where('id_actividad = ?', $actividad)
                        ->query()->fetchAll();
    }

}
