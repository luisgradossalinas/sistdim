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

    public function obtenerInventarioProcesos($proyecto) {

        return $this->getAdapter()->select()->from(array('n0' => self::TABLA), array('tipoproceso' => 'tp.descripcion', 'nivel0' => 'descripcion'))
                        ->joinLeft(array('tp' => Application_Model_Tipoproceso::TABLA), 'tp.codigo_tipoproceso = n0.codigo_tipoproceso', null)
                        ->joinLeft(array('n1' => Application_Model_Proceso1::TABLA), 'n1.id_proceso_n0 = n0.id_proceso_n0', array('nivel1' => "IFNULL(n1.descripcion,'')"))
                        ->joinLeft(array('n2' => Application_Model_Proceso2::TABLA), 'n2.id_proceso_n1 = n1.id_proceso_n1', array('nivel2' => "IFNULL(n2.descripcion,'')"))
                        ->joinLeft(array('n3' => Application_Model_Proceso3::TABLA), 'n3.id_proceso_n2 = n2.id_proceso_n2', array('nivel3' => "IFNULL(n3.descripcion,'')"))
                        ->joinLeft(array('n4' => Application_Model_Proceso4::TABLA), 'n4.id_proceso_n3 = n3.id_proceso_n3', array('nivel4' => "IFNULL(n4.descripcion,'')"))
                        ->where('n0.id_proyecto = ?', $proyecto)
                        ->order(array('tp.orden asc', 'n0.descripcion asc', 'n1.descripcion asc', 'n2.descripcion asc', 'n3.descripcion'))->query()->fetchAll();
    }

    public function obtenerMatrizDimensionamiento($proyecto) {

        return $this->getAdapter()->query("SELECT `tp`.`descripcion` AS `tipoproceso`, `n0`.`descripcion` AS `nivel0`, IFNULL(n1.descripcion,'') AS `nivel1`, IFNULL(n2.descripcion,'') AS `nivel2`,
 IFNULL(n3.descripcion,'') AS `nivel3`, IFNULL(n4.descripcion,'') AS `nivel4`,IFNULL(a.`codigo_actividad`,'') AS num_act
 ,IFNULL(a.`descripcion`,'') AS actividad,IFNULL(t.`codigo_tarea`,'') AS num_tarea,IFNULL(t.`descripcion`,'') AS tarea,
  CASE a.`tiene_tarea` WHEN 1 THEN 'tarea'
 WHEN 0 THEN  'actividad'
 ELSE '' END AS que_es,
 IFNULL(nat.descripcion,'') AS natu_orga,IFNULL(p.num_correlativo,'') AS num_puesto,IFNULL(p.descripcion,'') AS puesto,
IFNULL(o.organo,'') AS organo,IFNULL(uo.descripcion,'') AS unidad_organica,
IFNULL(per.descripcion,'') AS periodicidad,
  CASE a.`tiene_tarea` WHEN 1 THEN IFNULL(IF(t.frecuencia=0.00,'',t.frecuencia),'')
 WHEN 0 THEN  IFNULL(IF(a.frecuencia=0.00,'',a.frecuencia),'')
 ELSE '' END AS frecuencia,
 CASE a.`tiene_tarea` WHEN 1 THEN IFNULL(IF(t.frecuencia=0.00,'',ROUND(t.frecuencia*per.valor,2)),'')
 WHEN 0 THEN  IFNULL(IF(a.frecuencia=0.00,'',ROUND(a.frecuencia*per.valor,2)),'')
 ELSE '' END AS frecuencia_mensual,
  CASE a.`tiene_tarea` WHEN 1 THEN IFNULL(IF(t.duracion=0.00,'',ROUND(t.duracion*ti.valor,2)),'')
 WHEN 0 THEN  IFNULL(IF(a.duracion=0.00,'',ROUND(a.duracion*ti.valor,2)),'')
 ELSE '' END AS duracion_horas,
 '10%' AS tiempo_suple,
  CASE a.`tiene_tarea` WHEN 1 THEN IFNULL(IF(t.frecuencia=0.00,'',ROUND(t.frecuencia*per.valor*t.duracion*ti.valor+t.frecuencia*per.valor*t.duracion*ti.valor*0.1,3)),'')
 WHEN 0 THEN  IFNULL(IF(a.frecuencia=0.00,'',ROUND(a.frecuencia*per.valor*a.duracion*ti.valor+a.frecuencia*per.valor*a.duracion*ti.valor*0.1,3)),'')
 ELSE '' END AS total_tiempo_mensual,
 '176' AS horas_trabaja,
   CASE a.`tiene_tarea` WHEN 1 THEN IFNULL(IF(t.frecuencia=0.00,'',ROUND((t.frecuencia*per.valor*t.duracion*ti.valor+t.frecuencia*per.valor*t.duracion*ti.valor*0.1)/176,3)),'')
 WHEN 0 THEN  IFNULL(IF(a.frecuencia=0.00,'',ROUND((a.frecuencia*per.valor*a.duracion*ti.valor+a.frecuencia*per.valor*a.duracion*ti.valor*0.1)/176,3)),'')
 ELSE '' END AS servidores_publicos,
 IFNULL(gr.descripcion,'') AS grupo,IFNULL(fam.descripcion,'') AS familia,IFNULL(rp.descripcion,'') AS rol,
 IFNULL(np.descripcion,'') AS nivel_puesto,IFNULL(cp.descripcion,'') AS categoria_puesto,
   CASE a.`tiene_tarea` WHEN 1 THEN IFNULL(t.nombre_puesto,'')
 WHEN 0 THEN  IFNULL(a.nombre_puesto,'')
 ELSE '' END AS nombre_puesto
  FROM `proceso_n0` AS `n0` 
 LEFT JOIN  `tipoproceso` AS `tp` ON tp.codigo_tipoproceso = n0.codigo_tipoproceso 
 LEFT JOIN `proceso_n1` AS `n1` ON n1.id_proceso_n0 = n0.id_proceso_n0 
 LEFT JOIN `proceso_n2` AS `n2` ON n2.id_proceso_n1 = n1.id_proceso_n1 
 LEFT JOIN `proceso_n3` AS `n3` ON n3.id_proceso_n2 = n2.id_proceso_n2 
 LEFT JOIN `proceso_n4` AS `n4` ON n4.id_proceso_n3 = n3.id_proceso_n3 
  LEFT JOIN actividad a ON (n1.`id_proceso_n1` = a.`id_proceso` AND a.`nivel` = 1) OR (n2.`id_proceso_n2` = a.`id_proceso` AND a.`nivel` = 2) 
  OR (n3.`id_proceso_n3` = a.`id_proceso` AND a.`nivel` = 3) OR (n4.`id_proceso_n4` = a.`id_proceso` AND a.`nivel` = 4)  
  LEFT JOIN tarea t ON t.`id_actividad` = a.`id_actividad`
  LEFT JOIN puesto p ON (p.id_puesto = t.id_puesto) OR (p.id_puesto = a.id_puesto)
  LEFT JOIN unidad_organica uo ON uo.id_uorganica = p.id_uorganica
  LEFT JOIN organo o ON o.id_organo = uo.id_organo
  LEFT JOIN natuorganica nat ON nat.codigo_natuorganica = o.codigo_natuorganica
  LEFT JOIN periodicidad per ON (per.id_periodicidad = a.id_periodicidad) OR (per.id_periodicidad = t.id_periodicidad)
  LEFT JOIN tiempo ti ON (ti.id_tiempo = a.id_tiempo) OR (ti.id_tiempo = t.id_tiempo)
 LEFT JOIN grupo gr ON gr.codigo_grupo = p.codigo_grupo
 LEFT JOIN familia fam ON fam.codigo_familia = p.codigo_familia
 LEFT JOIN rolpuesto rp ON rp.codigo_rol_puesto = p.codigo_rol_puesto
 LEFT JOIN nivel_puesto np ON (np.id_nivel_puesto = a.id_nivel_puesto) OR (np.id_nivel_puesto = t.id_nivel_puesto)
 LEFT JOIN categoria_puesto cp ON (cp.id_categoria_puesto = a.id_categoria_puesto) OR (cp.id_categoria_puesto = t.id_categoria_puesto)
 WHERE (n0.id_proyecto = '".$proyecto."') ORDER BY `tp`.`orden` ASC, `n0`.`descripcion` ASC, `n1`.`descripcion` ASC, `n2`.`descripcion` ASC, `n3`.`descripcion` ASC,
 a.`codigo_actividad` ASC,t.`codigo_tarea` ASC")->fetchAll();
        
    }

}
