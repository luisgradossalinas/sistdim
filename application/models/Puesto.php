<?php

class Application_Model_Puesto extends Zend_Db_Table {

    protected $_name = 'puesto';
    protected $_primary = 'id_puesto';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'puesto';

    public function guardar($datos) {
        $id = 0;
        if (!empty($datos["id_puesto"])) {
            $id = (int) $datos["id_puesto"];
        }

        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id_puesto = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }

        return $id;
    }

    public function listado() {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }

    public function obtenerPuestos($unidad) {

        return $this->getAdapter()->select()->from(array('p' => self::TABLA), array('id_puesto', 'puesto' => 'descripcion',
                            'numcor' => 'num_correlativo', 'cantidad', 'total_dotacion', 'nombre_trabajador', 'nombre_personal'))
                        ->joinInner(array('uo' => Application_Model_UnidadOrganica::TABLA), 'uo.id_uorganica = p.id_uorganica', array('id_uorganica', 'unidad' => 'descripcion'))
                        ->joinInner(array('o' => Application_Model_Organo::TABLA), 'o.id_organo = uo.id_organo', array('id_organo', 'organo'))
                        ->joinInner(array('g' => Application_Model_Grupo::TABLA), 'g.codigo_grupo = p.codigo_grupo', array('codigo_grupo', 'grupo' => 'descripcion'))
                        ->joinInner(array('f' => Application_Model_Familia::TABLA), 'f.codigo_familia = p.codigo_familia', array('codigo_familia', 'familia' => 'descripcion'))
                        ->joinInner(array('rp' => Application_Model_Rolpuesto::TABLA), 'rp.codigo_rol_puesto = p.codigo_rol_puesto', array('codigo_rol_puesto', 'rpuesto' => 'descripcion'))
                        ->where('p.id_uorganica = ?', $unidad)
                        ->order('p.descripcion asc')
                        ->query()->fetchAll();
    }

    /*
      Esta función sirve para listar los puestos en la tabla donde se van a crear actividades.
     *  */

    public function puestosActividades($unidad) {

        return $this->getAdapter()->select()->from($this->_name)
                        ->where('id_uorganica = ?', $unidad)
                        ->where('estado = ?', self::ESTADO_ACTIVO)
                        ->order('descripcion asc')
                        ->query()->fetchAll();
    }

    public function obtenerPuestoPertinencia($unidad) {

        return $this->getAdapter()->query('SELECT puesto,descripcion,nombre_puesto,SUM(dotacion) AS dotacion
            FROM
            (SELECT p.`descripcion` AS puesto,np.`descripcion`,a.`nombre_puesto`,ROUND(SUM((pe.`valor`*a.`frecuencia`)*(ROUND(t.`valor`*a.`duracion`,2)*1.1/176)),2)
                        AS dotacion
                        FROM puesto p INNER JOIN actividad a ON p.`id_puesto` = a.`id_puesto` 
                       LEFT JOIN nivel_puesto np ON np.`id_nivel_puesto` = a.`id_nivel_puesto` 
                       LEFT JOIN tiempo t ON t.`id_tiempo` = a.`id_tiempo` 
                       LEFT JOIN periodicidad pe ON pe.id_periodicidad = a.`id_periodicidad` 
                       WHERE  np.`id_nivel_puesto` IN (1,2,3,4) AND a.`tiene_tarea` = 0 AND a.`id_uorganica` = '.$unidad.' 
                       GROUP BY p.`descripcion`,np.`descripcion`,a.`nombre_puesto`
                       UNION ALL
                       SELECT p.`descripcion` AS puesto,np.`descripcion`,a.`nombre_puesto`,ROUND(SUM((pe.`valor`*a.`frecuencia`)*(ROUND(t.`valor`*a.`duracion`,2)*1.1/176)),2) AS dotacion
                        FROM puesto p INNER JOIN tarea a ON p.`id_puesto` = a.`id_puesto` 
                       LEFT JOIN nivel_puesto np ON np.`id_nivel_puesto` = a.`id_nivel_puesto` 
                       LEFT JOIN tiempo t ON t.`id_tiempo` = a.`id_tiempo` 
                       LEFT JOIN periodicidad pe ON pe.id_periodicidad = a.`id_periodicidad` 
                       WHERE  np.`id_nivel_puesto` IN (1,2,3,4) AND a.`estado` = 1 AND a.`id_uorganica` = ' . $unidad . ' GROUP BY p.`descripcion`,np.`descripcion`,a.`nombre_puesto`)
                       t 
                       GROUP BY puesto,descripcion,nombre_puesto')
                        ->fetchAll();
    }

}
