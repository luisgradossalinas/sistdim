<?php

class Application_Model_Proceso0 extends Zend_Db_Table {

    protected $_name = 'proceso_n0';
    protected $_primary = 'id_proceso_n0';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'proceso_n0';
    const TIENE_HIJO = 1;

    public function guardar($datos) {
        $id = 0;
        if (!empty($datos["id_proceso_n0"])) {
            $id = (int) $datos["id_proceso_n0"];
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

    public function obtenerProcesos0($proyecto) {
        return $this->getAdapter()->select()->from($this->_name)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->where('id_proyecto = ?', $proyecto)
                        ->order('descripcion asc')
                        //->order(array('codigo_tipoproceso desc','descripcion asc'))
                        ->query()->fetchAll();
    }

    /* SELECT tp.`descripcion`,n0.`descripcion` AS nivel0,IFNULL(n1.descripcion,'') AS nivel1
      ,IFNULL(n2.`descripcion`,'') AS nivel2
      ,IFNULL(n3.`descripcion`,'') AS nivel3
      ,IFNULL(n4.`descripcion`,'') AS nivel4
      FROM proceso_n0 n0 INNER JOIN tipoproceso tp ON tp.`codigo_tipoproceso` = n0.`codigo_tipoproceso`
      LEFT JOIN proceso_n1 n1 ON n1.`id_proceso_n0` = n0.`id_proceso_n0`
      LEFT JOIN proceso_n2 n2 ON n2.`id_proceso_n1` = n1.`id_proceso_n1`
      LEFT JOIN proceso_n3 n3 ON n3.`id_proceso_n2` = n2.`id_proceso_n2`
      LEFT JOIN proceso_n4 n4 ON n4.`id_proceso_n3` = n3.`id_proceso_n3`
      WHERE n0.`id_proyecto` = 1 ORDER BY tp.`orden` ASC,n0.`descripcion` ASC
     * */

    public function obtenerInventarioProcesos($proyecto) {

        return $this->getAdapter()->select()->from(array('n0' => self::TABLA), array('tipoproceso' => 'tp.descripcion', 'nivel0' => 'descripcion'))
                        ->joinLeft(array('tp' => Application_Model_Tipoproceso::TABLA), 'tp.codigo_tipoproceso = n0.codigo_tipoproceso', null)
                        ->joinLeft(array('n1' => Application_Model_Proceso1::TABLA), 'n1.id_proceso_n0 = n0.id_proceso_n0', array('nivel1' => "IFNULL(n1.descripcion,'')"))
                        ->joinLeft(array('n2' => Application_Model_Proceso2::TABLA), 'n2.id_proceso_n1 = n1.id_proceso_n1', array('nivel2' => "IFNULL(n2.descripcion,'')"))
                        ->joinLeft(array('n3' => Application_Model_Proceso3::TABLA), 'n3.id_proceso_n2 = n2.id_proceso_n2', array('nivel3' => "IFNULL(n3.descripcion,'')"))
                        ->joinLeft(array('n4' => Application_Model_Proceso4::TABLA), 'n4.id_proceso_n3 = n3.id_proceso_n3', array('nivel4' => "IFNULL(n4.descripcion,'')"))
                        ->where('n0.id_proyecto = ?', $proyecto)
                        ->order(array('tp.orden asc', 'n0.descripcion asc'))->query()->fetchAll();
    }

}
