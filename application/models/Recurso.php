<?php

class Application_Model_Recurso extends Zend_Db_Table {

    protected $_name = 'recurso';
    protected $_primary = 'id';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'recurso';
    const PADRE = 1;
    const SERVIR = 1;
    const FUNCION_LISTADO = 'fetchAll';

    public function guardar($datos) {
        $id = 0;
        if (!empty($datos['id'])) {
            $id = (int) $datos['id'];
        }
        unset($datos['id']);

        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }
        return $id;
    }

    //Para generar el menú dinámico 
    public function recursoByRol($rol) {
        return $this->getAdapter()->select()->from(array("a" => $this->_name))
                        ->joinInner(array("b" => "rol_recurso"), "b.id_recurso = a.id", null)
                        ->where("b.id_rol = ?", $rol)->where("estado = ?", self::ESTADO_ACTIVO)->where("orden  != ?", self::PADRE)
                        ->order(array('a.padre asc', 'a.orden asc'))->query()->fetchAll();
    }

    public function obtenerPadre($key) {
        return $this->getAdapter()->select()->from($this->_name, array('padre', 'funcion_listado', 'estado'))
                        ->where('access = ?', 'admin:' . $key)->query()->fetch(Zend_Db::FETCH_NUM);
    }

    public function numRecursoCorrelativo($padre) {
        //SELECT COUNT(1) + 1 FROM recurso WHERE padre = 90
        return $this->getAdapter()->select()->from($this->_name, array('num' => 'count(1) + 1'))
                        ->where('padre = ?', $padre)->query()->fetchColumn();
    }

    public function listaRecursosPadre() {
        return $this->getAdapter()->select()->from(
                        $this->_name, array('key' => 'padre', 'value' => 'nombre')
                )->where('orden = ?', 1)->query()->fetchAll();
    }

    //Para generar el menú dinámico 
    public function recursosPadre($rol, $servir, $proyecto, $usuario) {

        if ($servir == 0) {
            return $this->getAdapter()->select()->from($this->_name, array('padre', 'nombre', 'accion'))
                            ->where('padre in (select distinct r.padre FROM recurso r 
                inner join rol_recurso rr ON rr.`id_recurso` = r.`id`
                where rr.`id_rol` = ' . $rol . ' ORDER BY r.`id`)')
                            ->where('orden = ?', self::PADRE)
                            ->where('servir = ?', $servir)
                            ->order(array('orden asc'))->query()->fetchAll();
        } else {
            return $this->getAdapter()->select()->from($this->_name, 
                array('padre', 'nombre', 'accion'))
                ->where('padre in (select padre from recurso  where id in
                (select id_recurso from rol_recurso where id_usuario = '.$usuario.' and '
                        . 'id_proyecto = '.$proyecto.' and estado = '.self::ESTADO_ACTIVO.'))')
                ->where('orden = ?', self::PADRE)
                ->where('estado = ?', self::ESTADO_ACTIVO)->query()->fetchAll();
        }
    }

    //Para generar el menú dinámico 
    public function recursosHijo($rol, $padre, $servir, $proyecto, $usuario) {

        $sqlHijo = $this->getAdapter()->select()->distinct()->from(array("a" => $this->_name))
                ->joinInner(array("b" => "rol_recurso"), "b.id_recurso = a.id", null)
                ->where("b.id_rol = ?", $rol)->where("a.estado = ?", self::ESTADO_ACTIVO)
                ->where('a.orden != ?', self::PADRE)
                ->where('a.padre = ?', $padre)
                ->where('a.servir = ?', $servir)
                ->order(array('a.orden asc'));

        if (!empty($servir)) {
            $sqlHijo->where('b.id_proyecto = ?', $proyecto)
                    ->where('b.id_usuario = ?', $usuario)
                    ->where('b.estado = ?', self::ESTADO_ACTIVO);
        }

        return $sqlHijo->query()->fetchAll();

    }

    public function validaAcceso($rol, $url) {
        return $this->getAdapter()->select()->from(array('r' => $this->_name), array('acceso' => 'count(1)'))
                        ->joinInner(array('rr' => 'rol_recurso'), 'rr.id_recurso = r.id', null)
                        ->joinInner(array('ro' => 'rol'), 'ro.id = rr.id_rol', null)
                        ->where('ro.id = ?', $rol)
                        ->where('r.url = ?', $url)
                        ->query()->fetchColumn();
    }

    //Para generar el menú a SUṔER
    public function listaRecursosSuper() {
        return $this->getAdapter()->select()->distinct()->from(array("a" => $this->_name))
                        ->where("estado = ?", self::ESTADO_ACTIVO)->where("orden  != ?", self::PADRE)
                        ->order(array('a.padre asc', 'a.orden asc'))->query()->fetchAll();
    }

    //Recursos dependiendo del ROL
    public function listadoPorRol($rol) {
        return $this->getAdapter()->select()->from(array("a" => $this->_name), array(
                            'a.id', 'a.nombre', 'a.access', 'a.estado', 'a.accion',
                            'a.padre', 'a.orden', 'a.url', 'a.funcion_listado', 'a.tab',
                            'a.usuario_crea', 'a.fecha_crea', 'a.usuario_actu', 'a.fecha_actu',
                            'checked' => '(SELECT COUNT(1) FROM rol_recurso rr WHERE rr.id_recurso = a.id AND 
                    rr.id_rol = ' . $rol . ' LIMIT 1)'))
                        ->where("a.estado = ?", self::ESTADO_ACTIVO)
                        ->where("a.orden  != ?", self::PADRE)
                        ->where("a.servir = ?", 0)
                        ->order(array('a.padre asc', 'a.orden asc'))->query()->fetchAll();
    }

    //Generación de menú
    public function generacionMenu($padre, $active, $servir, $proyecto, $usuario) {
        
        $auth = Zend_Auth::getInstance();
        $rol = $auth->getIdentity()->id_rol;

        $dataRecursos = $this->recursosPadre($rol, $servir, $proyecto, $usuario);

        $menu = '';

        foreach ($dataRecursos as $reg) {

            $idPadre = $reg['padre'];
            $dataHijos = $this->recursosHijo($rol, $idPadre, $servir, $proyecto, $usuario);

            if (count($dataHijos) > 0) {

                $open = '';
                if ($padre == $idPadre) {
                    $open = 'open';
                }

                $menu .= '<li class="submenu ' . $open . '">';
                $menu .= '<a href="#" title="' . $reg['accion'] . '" class="tip-right"><i class="icon icon-th-list"></i>';
                $menu .= '<span>' . $reg['nombre'] . '</span><span class="label">' . count($dataHijos) . '</span></a>';
                $menu .= '<ul>';

                foreach ($dataHijos as $hijo) {
                    $class = '';
                    if (!empty($active)) {
                        if ('admin:' . $active == $hijo['access']) {
                            $class = 'class="active"';
                            empty($active);
                        }
                    }
                    $menu .= '<li ' . $class . '><a  href="' . SITE_URL . '/' . $hijo['url'] . '" title="' . $hijo['accion'] . '" class="tip-right">' . $hijo['nombre'] . '</a></li>';
                }

                $menu .= '</ul>';
                $menu .= '</il>';
            } else {
                //El único que no tendra hijos será el INICIO por el momento
                $menu .= '<li><a href="' . SITE_URL . '/admin"><i class="icon icon-th"></i> <span>' . $reg['nombre'] . '</span></a></li>';
            }
        }

        $nReg = 0;
        return array("menu" => $menu, "registro" => $nReg);
    }

    //Recursos por usuario
    public function recursosUsuario($usuario, $proyecto) {

        $rolRecursoModel = new Application_Model_RolRecurso();

        //Si no tiene datos en la tabla rol_recurso, mostrar todos las filas
        $valida = $rolRecursoModel->validaRecursoUsuario($usuario, $proyecto);

        //Primera vez
        if (empty($valida)) {
            return $this->getAdapter()->select()->distinct()->from(array("r" => $this->_name), array(
                                'id_recurso' => 'r.id', 'r.nombre', 'estado_permiso' => new Zend_Db_Expr("0")))
                            ->joinLeft(array("rr" => Application_Model_RolRecurso::TABLA), 'rr.id_recurso = r.id', array())
                            ->joinLeft(array("u" => Application_Model_Usuario::TABLA), 'u.id = rr.id_usuario', array())
                            ->where("r.servir = ?", self::SERVIR)
                            ->where("r.orden  <> ?", self::PADRE)->query()->fetchAll();
        }
        return $this->getAdapter()->select()->from(array("r" => $this->_name), array(
                            'id_recurso' => 'r.id', 'r.nombre', 'estado_permiso' => new Zend_Db_Expr("IFNULL(rr.estado,0)")))
                        ->joinLeft(array("rr" => Application_Model_RolRecurso::TABLA), 'rr.id_recurso = r.id', array())
                        ->joinLeft(array("u" => Application_Model_Usuario::TABLA), 'u.id = rr.id_usuario', array())
                        ->where("r.servir = ?", self::SERVIR)
                        ->where("rr.id_usuario = ?", $usuario)
                        ->where("rr.id_proyecto = ?", $proyecto)
                        ->where("r.orden  <> ?", self::PADRE)
                        ->order(array("r.id asc"))
                        ->query()->fetchAll();
    }

}
