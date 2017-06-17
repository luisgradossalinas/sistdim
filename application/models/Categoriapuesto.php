<?php

class Application_Model_Categoriapuesto extends Zend_Db_Table {

    protected $_name = 'categoria_puesto';
    protected $_primary = 'id_categoria_puesto';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'categoria_puesto';

    public function guardar($datos) {

        $id = 0;
        if (!empty($datos["id"])) {
            $id = (int) $datos["id"];
        }

        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id_categoria_puesto = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }

        return $id;
    }

    //Para el registro de puesto en pertinencia
    public function obtenerCategoria($familia) {

        if (empty($familia)) {
            return $this->getAdapter()->select()->from($this->_name)
                            ->query()->fetchAll();
        } else {
            return $this->getAdapter()->select()->from($this->_name)
                            ->where('codigo_famila = ?', $familia)
                            ->query()->fetchAll();
        }
    }

}
