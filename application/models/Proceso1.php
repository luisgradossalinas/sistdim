<?php

class Application_Model_Proceso1 extends Zend_Db_Table {

    protected $_name = 'proceso_n1';
    protected $_primary = 'id_proceso_n1';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'proceso_n1';
    const TIENE_HIJO = 1;

    public function guardar($datos) {
        $id = 0;
        if (!empty($datos["id_proceso_n1"])) {
            $id = (int) $datos["id_proceso_n1"];
        }

        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, $this->_primary . ' = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }

        return $id;
    }

    public function listado() {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }

    public function combo($proyecto) {
        return $this->getAdapter()->select()->from($this->_name, array('key' => $this->_primary, 'value' => 'descripcion'))
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->where('id_proyecto = ?', $proyecto)
                        ->order('descripcion asc')
                        ->query()->fetchAll();
    }

    /*
      No considera los procesos que ya tiene actividades, esto es para que no se agreguen más niveles
     * */

    public function obtenerProcesos1($proceso0) {
        return $this->getAdapter()->select()->from(array('n1' => $this->_name))
                        ->joinInner(array('n0' => Application_Model_Proceso0::TABLA), 'n0.id_proceso_n0 = n1.id_proceso_n0', array('nivel0' => 'descripcion'))
                        ->joinInner(array('tp' => Application_Model_Tipoproceso::TABLA), 'tp.codigo_tipoproceso = n0.codigo_tipoproceso', array('tipo' => 'descripcion'))
                        ->where('n1.estado = ?', self::ESTADO_ACTIVO)
                        ->where('n1.id_proceso_n0 = ?', $proceso0)
                        ->where('n1.tiene_actividad = ?', 0)
                        ->order('n1.descripcion asc')
                        ->query()->fetchAll();
    }

    //Actividad
    //Si viene nivel 1 solo listar los que no tienen hijos
    public function obtenerProcesos1Actividad($proceso0, $nivel) {

        $select = $this->getAdapter()->select()->from($this->_name)
                ->where('id_proceso_n0 = ?', $proceso0);

        if ($nivel == 1) {
            $select->where('tiene_hijo <> ?', self::TIENE_HIJO);
        }
        return $select->query()->fetchAll();
    }

}
