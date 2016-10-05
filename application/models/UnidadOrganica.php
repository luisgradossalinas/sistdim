<?php

class Application_Model_UnidadOrganica extends Zend_Db_Table {

    protected $_name = 'unidad_organica';
    protected $_primary = 'id_uorganica';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'unidad_organica';

    public function guardar($datos) {
        $id = 0;
        if (!empty($datos["id"])) {
            $id = (int) $datos["id"];
        }

        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id_uorganica = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }

        return $id;
    }

    public function listado() {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }

    public function combo() {
        return $this->getAdapter()->select()->from(
                                $this->_name, array('key' => 'id_uorganica', 'value' => 'descripcion'))
                        ->query()->fetchAll();
    }

    /*
      Obtiene todos las unidades orgánicas que tiene un proyecto
     */

    function obtenerUOrganica($proyecto, $organo) {

        $sql = $this->getAdapter()->select()->from(self::TABLA, array('id_uorganica', 'descripcion', 'estado', 'id_proyecto', 'id_organo', 'siglas'))
                ->where('id_proyecto = ?', $proyecto)
                ->where('estado = ?', self::ESTADO_ACTIVO);

        if (!is_null($organo))
            $sql->where('id_organo = ?', $organo);

        $sql->order("descripcion asc");

        return $sql->query()->fetchAll();
    }

    //Obtener los órganos y unidades orgánicas de un proyecto
    function obtenerOrganoUOrganica($proyecto) {

        $sql = $this->getAdapter()->select()->from(array('o' => Application_Model_Organo::TABLA), array('organo'))
                ->joinInner(array('uo' => self::TABLA), 'uo.id_organo = o.id_organo', array('descripcion'))
                ->where('uo.id_proyecto = ?', $proyecto)
                ->where('uo.estado = ?', self::ESTADO_ACTIVO)
                ->order(array("o.organo asc", "uo.descripcion asc"));

        return $sql->query()->fetchAll();
    }

}
