<?php
class App_Controller_Action_Helper_Aviso extends Zend_Controller_Action_Helper_Abstract
{
    private $_aw;
    private $_avisoId;
    private $_postulacion;
    private $_empresa;
    private $_config;
    private $_anuncioWeb;
    private $_adecysEnte;
    private $_empresaEnte;
    private $_compraAdecsysCode;
    
    /**
     * @var Zend_Cache
     */
    protected $_cache = null;

    public function __construct()
    {
        if ($this->_cache==null) {
            $this->_cache = Zend_Registry::get('cache');
        }    
        $this->_postulacion = new Application_Model_Postulacion();
        $this->_adecsysEnte = new Application_Model_AdecsysEnte();
        $this->_empresaEnte = new Application_Model_EmpresaEnte();
        $this->_compra = new Application_Model_Compra();
        $this->_aw = new Application_Model_AnuncioWeb();
        $this->_awd = new Application_Model_AnuncioWebDetalle();
        $this->_ai = new Application_Model_AnuncioImpreso();
        $this->_empresa = new Application_Model_Empresa();
        $this->_compraAdecsysCode = new Application_Model_CompraAdecsysCodigo();
        if (isset($_SESSION)) {
            $this->auth = Zend_Auth::getInstance()->getStorage()->read();
            $this->_config = Zend_Registry::get('config');
            $this->session = new Zend_Session_Namespace('aptitus');
        }
    }
    
    public function confirmarCompraAviso($compraId, $registrarEnAdecsys = 1)
    {
        if (!$this->_compra->verificarUsuarioActivoPorCompra($compraId)) {
            return;
        }
        
        $config = Zend_Registry::get('config');
        
        $this->actualizaValoresCompraAviso($compraId);
        
        $rowCompra = $this->_compra->getDetalleCompraAnuncio($compraId);
        $mailer = new App_Controller_Action_Helper_Mail();
        
        //Inserta las postulaciones al extender el aviso
        if ($rowCompra['anuncioClase'] != Application_Model_AnuncioWeb::TIPO_SOLOWEB) {
            foreach ($rowCompra['anunciosWeb'] as $data) {
                if (isset($data['extiende_a']) && $data['extiende_a'] != $data['id'] 
                    && $data['republicado'] == 0) {
                        //Extender
                    $this->extenderAviso($data['id'], $rowCompra['usuario']);
                } elseif (isset($data['extiende_a']) && $data['extiende_a'] != $data['id'] 
                    && $data['republicado'] == 1) {
                        //Republica
                    $this->_aw->bajaAnuncioWeb($data['extiende_a'], $data['id'], $rowCompra['usuario']);
                } 
            }
        } else {
            if (isset($rowCompra['extiendeA']) && $rowCompra['extiendeA'] != $rowCompra['anuncioId']
                && $rowCompra['republicado'] == 0) {
                    //Extender
                $this->extenderAviso($rowCompra['anuncioId'], $rowCompra['usuario']);
            } elseif (isset($rowCompra['extiendeA']) && $rowCompra['extiendeA'] != $rowCompra['anuncioId']
                && $rowCompra['republicado'] == 1) {
                    //Republica
                $this->_aw->bajaAnuncioWeb($rowCompra['extiendeA'], $rowCompra['anuncioId'], $rowCompra['usuario']);
            }
        }
        
        if ($registrarEnAdecsys == 1) {
            $this->registrarAvisoEnAdecsys($compraId);
            
            if ($rowCompra['tipoAnuncio'] == Application_Model_Compra::TIPO_SOLOWEB ||
                $rowCompra['tipoAnuncio'] == Application_Model_Compra::TIPO_CLASIFICADO ) {

                $modelCompAdecCod = new Application_Model_CompraAdecsysCodigo;
                $anuncioWebModelo = new Application_Model_AnuncioWeb;
                $dataAnuncioWeb = $anuncioWebModelo->fetchRow('id_compra = '. $rowCompra['compraId']);
                $arrayCompAdecCod = $modelCompAdecCod->getCodAdecsysByCodCompra($rowCompra['compraId']);
                $objbiu = new Application_Model_InstitucionBdu();
                $databiu = $objbiu->getNamesInstitucionById($config->app->institucion);
                
                $dataMail = array (
                        'to' => $rowCompra['emailContacto'],
                        'usuario' => $rowCompra['emailContacto'],
                        'nombre' => $rowCompra['nombreContacto']." ".$rowCompra['apePatContacto'],
                        'anuncioPuesto' => trim($rowCompra['anuncioPuesto']),
                        'razonSocial' => $rowCompra['nombre_comercial'],
                        'montoTotal' => $rowCompra['montoTotal'],
                        'medioPago' => $rowCompra['medioPago'],
                        'anuncioClase' => $rowCompra['anuncioClase'],
                        'productoNombre' => $rowCompra['productoNombre'],
                        'anuncioUrl' => $rowCompra['anuncioUrl'],
                        'fechaPago' => $rowCompra['fechaPago'],
                        'anuncioFechaVencimiento' => $rowCompra['anuncioFechaVencimiento'],
                        'fechaPublicConfirmada' => $rowCompra['fechaPublicConfirmada'],
                        'medioPublicacion' => $rowCompra['medioPublicacion'],
                        'anuncioSlug' => $rowCompra['anuncioSlug'],
                        'anuncioFechaVencimientoProceso' => $rowCompra['anuncioFechaVencimientoProceso'],
                        'codigo_adecsys_compra' => $arrayCompAdecCod['adecsys_code'],
                        'ver_aptitus' => $dataAnuncioWeb['ver_aptitus'],
                        'correoinforma' => $config->contacto->info->aptitus,
                        'nombrebolsa'=> !empty($databiu['nombre_corto'])?$databiu['nombre_corto']:''
                    );
                $mailer->confirmarCompra($dataMail);
            } elseif ($rowCompra['tipoAnuncio']==Application_Model_Compra::TIPO_PREFERENCIAL) {
                $rowCompra = $this->_compra->getDetalleCompraAnuncio($compraId);

                $anuncios = array();
                foreach ($rowCompra['anunciosWeb'] as $aWeb) {
                    $anun = array();
                    $anun["id"] = $aWeb["id"];
                    $anun["titulo"] = $aWeb["puesto"];
                    $anun["slug"] = $aWeb["slug"];
                    $anun["urlId"] = $aWeb["url_id"];

                    $anuncios[] = $anun;
                }
                
                $dataMail = array (
                    'to' => $rowCompra['emailContacto'],
                    'usuario' => $rowCompra['emailContacto'],
                    'titulo' => strtoupper($rowCompra['anuncioClase'])." ".$rowCompra['tamanio'],
                    'nombre' => $rowCompra['nombreContacto']." ".$rowCompra['apePatContacto'],
                    'nroPuestos' => count($anuncios),
                    'razonSocial' => $rowCompra['nombre_comercial'],
                    'montoTotal' => $rowCompra['montoTotal'],
                    'medioPago' => $rowCompra['medioPago'],
                    'anuncioClase' => strtoupper($rowCompra['anuncioClase']),
                    'tipoAviso' => $rowCompra['tamanio']." (".$rowCompra['tamanioCentimetros']." cm.)",
                    'urlScotAptitus' => $rowCompra['urlScotAptitus'],
                    'urlScotTalan' => $rowCompra['urlScotTalan'],
                    //'pass' => "clave",
                    'productoNombre' => $rowCompra['productoNombre'],
                    'fechaPago' => $rowCompra['fechaPago'],
                    'anuncioFechaVencimiento' => $rowCompra['anuncioFechaVencimiento'],
                    'fechaPublicConfirmada' => $rowCompra['fechaPublicConfirmada'],
                    'medioPublicacion' => $rowCompra['medioPublicacion'],
                    'anuncioSlug' => $rowCompra['anuncioSlug'],
                    'anuncioFechaVencimientoProceso' => $rowCompra['anuncioFechaVencimientoProceso'],
                    'codigoAviso' => $rowCompra['codigoAdecsys'],
                    'anunciosWeb' => $anuncios
                );
                $mailer->confirmarCompraPreferencial($dataMail);
            }
        } elseif ($registrarEnAdecsys == 0) {
            
            $anuncioWebModelo = new Application_Model_AnuncioWeb;
            $usuarioModelo = new Application_Model_Usuario;
            $institucionBduModelo = new Application_Model_InstitucionBdu;
            $dataUsuario = $usuarioModelo->getUsuarioAdminBolsaPrincipal($config->app->institucion);
            $objbiu = new Application_Model_InstitucionBdu();
            $databiu = $objbiu->getNamesInstitucionById($config->app->institucion);
            
            $dataAnuncioWeb = $anuncioWebModelo->fetchRow('id_compra = '. $rowCompra['compraId']);
            $dataMail = array (
                'to' => $rowCompra['emailContacto'],
                'usuario' => $rowCompra['emailContacto'],
                //'nombre' => $rowCompra['nombreContacto']." ".$rowCompra['apePatContacto'],
                'anuncioPuesto' => trim($rowCompra['anuncioPuesto']),
                'razonSocial' => $rowCompra['nombre_comercial'],
                'montoTotal' => $rowCompra['montoTotal'],
                'medioPago' => $rowCompra['medioPago'],
                'anuncioClase' => $rowCompra['anuncioClase'],
                'productoNombre' => $rowCompra['productoNombre'],
                'anuncioUrl' => $rowCompra['anuncioUrl'],
                'fechaPago' => $rowCompra['fechaPago'],
                'anuncioFechaVencimiento' => $rowCompra['anuncioFechaVencimiento'],
                'fechaPublicConfirmada' => $rowCompra['fechaPublicConfirmada'],
                'medioPublicacion' => $rowCompra['medioPublicacion'],
                'anuncioSlug' => $rowCompra['anuncioSlug'],
                'anuncioFechaVencimientoProceso' => $rowCompra['anuncioFechaVencimientoProceso'],
                'ver_aptitus' => $dataAnuncioWeb['ver_aptitus'],
                'correoinforma' => $config->contacto->info->aptitus,
                'nombrebolsa'=> !empty($databiu['nombre_corto'])?$databiu['nombre_corto']:''
            );
            $mailer->confirmarCompra($dataMail);
        }
    }
    
    public function generarCompraAnuncio($rowAnuncio)
    {
        if ($rowAnuncio['enteId'] == '') {
            $rowAnuncio['enteId'] = null;
        }
        if (!isset($rowAnuncio['cip'])) {
            $rowAnuncio['cip'] = null;
        }
        $data = array(
            'id_tarifa' => $rowAnuncio['tarifaId'],
            'id_empresa' => $rowAnuncio['empresaId'],
            'tipo_doc' => $rowAnuncio['tipoDoc'],
            'medio_pago' => $rowAnuncio['tipoPago'],
            'estado' => 'pagado',
            'fh_creacion' => date('Y-m-d H:i:s'),
            'cip' => $rowAnuncio['cip'],
            'precio_base' => $rowAnuncio['tarifaPrecio'],
            'adecsys_ente_id' => $rowAnuncio['enteId'],//Cambiar por el id del ente seleccionado
            'creado_por' => $rowAnuncio['usuarioId'],
            'precio_total' => $rowAnuncio['totalPrecio'],
            'tipo_anuncio' => $rowAnuncio['tipo']
        );
        
        if (isset($rowAnuncio['tipoContrato'])) {
           $data["tipo_contrato"] = $rowAnuncio['tipoContrato'];
           $data["nro_contrato"] = $rowAnuncio['nroContrato'];
        }
        
        //var_dump($data);
        //exit;
        
        $idCompra = $this->_compra->insert($data);
        if (empty($rowAnuncio['anuncioImpresoId'])) {
            $where = $this->_aw->getAdapter()->quoteInto('id = ?', $rowAnuncio['anuncioId']);
        } else {      
            $where = $this->_aw->getAdapter()->quoteInto('id_anuncio_impreso = ?', $rowAnuncio['anuncioImpresoId']);
        }
            $okUpdateP = $this->_aw->update(
                array(
                'estado' => 'pendiente_pago',
                'id_compra' => $idCompra,
                ), $where
            );
            if ($rowAnuncio['tipo']=='clasificado' || $rowAnuncio['tipo']=='preferencial') {
                $where = $this->_ai->getAdapter()->quoteInto('id = ?', $rowAnuncio['anuncioImpresoId']);
                $okUpdateP = $this->_ai->update(
                    array(
                    'estado' => 'pendiente_pago',
                    'id_compra' => $idCompra,
                    ), $where
                );
            }
        
        /* CAMBIAR LUEGO PARA INSERTAR TODOS LOS BENEFICIOS DE ANUNCIOS WEB */
        $this->_awd = new Application_Model_AnuncioWebDetalle();
        
        foreach ($rowAnuncio['anunciosWeb'] as $key => $value) {
//        var_dump($value);exit;
            $where = $this->_awd->getAdapter()->quoteInto(
                'id_anuncio_web = ?', $value['id']
            );
            //var_dump($value);
            $okDeleteP = $this->_awd->delete($where);
            if (count($rowAnuncio['beneficios']) > 0) {
                foreach ($rowAnuncio['beneficios'] as $key => $benefValue) {
                    $data = array(
                        'id_anuncio_web' => $value['id'],
                        'codigo' => $rowAnuncio['beneficios'][$key]['codigo'],
                        'valor' => $rowAnuncio['beneficios'][$key]['valor'],
                        'descripcion' => $rowAnuncio['beneficios'][$key]['nombreBeneficio']
                    );
                    $this->_awd->insert($data);
                }
            }
        }
        
       /**/
        /*$this->_aid = new Application_Model_AnuncioImpresoDetalle();
        $where = $this->_aid->getAdapter()->quoteInto(
            'id_anuncio_impreso = ?', $rowAnuncio['anuncioImpresoId']
        );
        $okDeleteP = $this->_aid->delete($where);*/
//        exit;
        if (isset($rowAnuncio['extracargosComprados'])) {
            foreach ($rowAnuncio['extracargosComprados'] as $key => $value) {
                $data = array(
                    'id_anuncio_impreso' => $rowAnuncio['anuncioImpresoId'],
                    'codigo' => $rowAnuncio['extracargosComprados'][$key]['codigoBeneficio'],
                    'adecsys_cod' => $rowAnuncio['extracargosComprados'][$key]['adecsysCod'],
                    'adecsys_cod_envio_dos' => $rowAnuncio['extracargosComprados'][$key]['adecsysCodEnvioDos'],
                    'precio' => $rowAnuncio['extracargosComprados'][$key]['precioExtracargo'],
                    'descripcion' => $rowAnuncio['extracargosComprados'][$key]['nombreBeneficio']
                );
                $this->_aid->insert($data);
            }
        }
        return $idCompra;
    }

    public function actualizaValoresCompraAviso($compraId)
    {
        $rowCompra = $this->_compra->getDetalleCompraAnuncio($compraId);
        //var_dump($rowCompra); exit;
        //$fecVenAnuncio = new Zend_Date();
        $fecVenAnuncio = new DateTime(date("Y-m-d"));
        $fecVenProceso = new DateTime(date("Y-m-d"));
        $ndiaspub = Application_Model_Beneficio::CODE_NDIASPUB;
        if (array_key_exists($ndiaspub, $rowCompra['beneficios'])) {
            $diasAnuncio =
                $rowCompra['beneficios'][$ndiaspub]['valor'];
//        } else {
//            $this->_awd->insert(
//                array(
//                    'id_anuncio_web' => $rowCompra['anuncioId'],
//                    'codigo' => 'ndiaspub',
//                    'descripcion' => 'dias de Publicación',
//                    'valor' => 14
//                )
//            );
//            $diasAnuncio = 14;
        }
        //$fecVenAnuncio->add($diasAnuncio, Zend_date::DAY);
        $fecVenAnuncio->add(new DateInterval('P'.$diasAnuncio.'D'));
        //$fecVenProceso = new Zend_Date();
        $ndiasproc = Application_Model_Beneficio::CODE_NDIASPROC;
        
        if (array_key_exists($ndiasproc, $rowCompra['beneficios'])) {
            $diasProceso =
                $rowCompra['beneficios'][$ndiasproc]['valor'];
//        } else {
////            throw new Zend_Exception('No se cuenta con número de días de Proceso');
//            $this->_awd->insert(
//                array(
//                    'id_anuncio_web' => $rowCompra['anuncioId'],
//                    'codigo' => 'ndiasproc',
//                    'descripcion' => 'dias del Proceso',
//                    'valor' => 30
//                )
//            );
//            $diasProceso = 30;
        }
        
        $fecVenProceso->add(new DateInterval('P'.$diasProceso.'D'));
        //$fecVenProcesoo->add( new DateInterval('P'.$diasProceso.'D'));
        
        //$fecVenProceso->add($diasProceso, Zend_date::DAY);
        //echo $fecVenAnuncio->get(Zend_Date::DATE_MEDIUM,'yyyy-mm-dd');

        $db = $this->_aw->getAdapter();
        // @codingStandardsIgnoreStart
        $where1 = $db->quoteInto('id_compra = ?', $compraId);
        $where2 = $db->quoteInto('chequeado = ?', 1);
        $where3 = $db->quoteInto('online = ?', 1);
        $whereAnuncioWeb = $where1 . ' AND ' . $where2 . ' AND ' . $where3;
        // @codingStandardsIgnoreEnd
        $okUpdateP = $this->_aw->update(
            array(
            'estado' => Application_Model_AnuncioWeb::ESTADO_PAGADO,
            'fh_pub' => date('Y-m-d H:i:s'),
            'estado_publicacion' => 1,
            //'fh_vencimiento' => $fecVenAnuncio->toString('YYYY-MM-dd'),
            'fh_vencimiento' => $fecVenAnuncio->format('Y-m-d'),
            'online' => 1,
            'borrador' => 0,
            //'fh_vencimiento_proceso' => $fecVenProceso->toString('YYYY-MM-dd'),
            'fh_vencimiento_proceso' => $fecVenProceso->format('Y-m-d'),
            'proceso_activo' => 1,
            ), $whereAnuncioWeb
        );
        /*Paul verificar el campo estado debeira ser de anuncio impreso*/
        $whereAnuncioImpreso = $db->quoteInto('id_compra = ?', $compraId);
        $okUpdateP = $this->_ai->update(
            array(
            'estado' => Application_Model_AnuncioWeb::ESTADO_PAGADO,
            //'fh_vencimiento' => $fecVenAnuncio->toString('YYYY-MM-dd'),
            ), $whereAnuncioImpreso
        );

        $where = $this->_compra->getAdapter()->quoteInto('id = ?', $compraId);
        $okUpdateP = $this->_compra->update(
            array(
                'estado' => 'pagado',
                'fh_confirmacion' => date('Y-m-d H:i:s'),
            ), $where
        );
        if ($rowCompra['anuncioTarifaId']!=1) {
            $medioPublicacion = $rowCompra['medioPublicacion'];
            if ($medioPublicacion == 'aptitus y talan') {
//                $medioPublicacion = 'talan';
                $medioPublicacion = 'combo';
            }
            $cierre = $this->_config->cierre->toArray();
//            var_dump($cierre);
//            $cierre[$medioPublicacion]['hora'];
            $fecNow = new Zend_Date();
            $fecVenc = clone $fecNow;
            $fecVenc->set($cierre[$medioPublicacion]['dia'], Zend_Date::WEEKDAY_DIGIT);
            $fecVenc->set($cierre[$medioPublicacion]['hora'], Zend_Date::HOUR);
            $fecVenc->set(0, Zend_Date::MINUTE);
            $fecVenc->set(0, Zend_Date::SECOND);
            $fecImpre = clone $fecVenc;
            $fecImpre->set(0, Zend_Date::HOUR);
            if ($cierre[$medioPublicacion]['semanaActual'] == 0) {
                $fecImpre->add(7, Zend_Date::DAY);
            }
            $fecImpre->set($cierre['aptitus']['diaPublicacion'], Zend_Date::WEEKDAY_DIGIT);
            if ($fecNow->isLater($fecVenc)) {
                $fecImpre->add(7, Zend_Date::DAY);
            }
            $whereAi = $this->_ai->getAdapter()->quoteInto('id = ?', $rowCompra['anuncioImpresoId']);
            $okUpdateP = $this->_ai->update(
                array(
                'id_compra' => $compraId,
                'fh_pub_confirmada' => $fecImpre->toString('YYYY-MM-dd'),
                ), $whereAi
            );
        }
        
        //$zl = new ZendLucene();
        $anuncios = $this->_aw->getAnunciosPorCompra($compraId);
        foreach ($anuncios as $key => $row) {
            //$zl->agregarNuevoDocumentoAviso($row['anuncioId']);
            $this->_cache->remove('anuncio_web_'.$row['anuncioUrl']);
        }
    }

    public function registrarAvisoEnAdecsys($compraId)
    {
        $options = array();
        if ($this->_config->adecsys->proxy->enabled) {
            $options = $this->_config->adecsys->proxy->param->toArray();
        }
        $ws = new Adecsys_Wrapper($this->_config->adecsys->wsdl, $options);
        $cliente = $ws->getSoapClient();
        $db = Zend_Db_Table::getDefaultAdapter();
        $aptitus = new Aptitus_Adecsys($ws, $db);

        //Cabecera

        $rowAnuncio = $this->_compra->getDetalleCompraAnuncio($compraId);
        if ($rowAnuncio['enteId'] == null) {
            $this->registrarCodigoEnte($ws, $cliente, $aptitus, $rowAnuncio, $compraId);
            $rowAnuncio = $this->_compra->getDetalleCompraAnuncio($compraId);
        }
        
        $correlativo = $compraId . "0";
        
        if ($rowAnuncio['tipoAnuncio']==Application_Model_Compra::TIPO_SOLOWEB ||
            $rowAnuncio['tipoAnuncio']==Application_Model_Compra::TIPO_CLASIFICADO ) {
            $this->logicaRegistroAnuncioClasificadoYWeb($rowAnuncio, $aptitus, $cliente);            
        } else if ($rowAnuncio['tipoAnuncio']==Application_Model_Compra::TIPO_PREFERENCIAL) {
            
            $this->logicaRegistroAnuncioPreferencial($rowAnuncio);
        //    Registrar en SCOT
            //$this->registrarPreferencialEnSCOT($rowAnuncio);
        }
        
        
        $this->_anuncioWeb = null;
    }

    public function getSubArrayByKeyValues($array, $keyValues, $sensitive = true) 
    {
        $subArray = array();
        
        if (!$sensitive) {
            $arrayD = array();
            foreach ($array as $key => $data) {   
                $keyD = strtolower($key);
                $arrayD[$key] = $array[$key];
            }
            $array = $arrayD;
        }
        
        foreach ($keyValues as $key) {
            if (!$sensitive) {
                $key = strtolower($key);
            }
            if (isset($array[$key])) {
            $subArray[$key] = $array[$key];
            }
        }
        
        return $subArray;
    }
    
    public function registrarPreferencialEnSCOT($rowAnuncio)
    {   
    //  try{
        $ws = new Zend_Soap_Client($this->_config->SCOT->wsdl);
        $cliente = $ws->getSoapClient();
        
        $tamanio = strtoupper($rowAnuncio["tamanio"]);
        $funcion = "registrarOT";
        
       // var_dump($rowAnuncio);
        
        //var_dump($this->_config->SCOT->wsdl);
        //var_dump($ws->getTypes());
        //exit;
        $dataExt = array();
        $dataExt["dsc_mail_to"] = $rowAnuncio["emailContacto"];
        $dataExt["dsc_mail_contacto"] = $rowAnuncio["emailContacto"];
        $dataExt["tlf_contacto"] = $rowAnuncio["telefonoContacto"];
        $dataExt["cod_cliente"] = $rowAnuncio["userId"];
        $dataExt["dsc_tituloaviso"] = $rowAnuncio["productoNombre"]." ".$rowAnuncio["tamanio"];
        $dataExt["dsc_observacion"] = $rowAnuncio["notaDiseno"]." . . . . . ";
        $dataExt["id_medida"] = $rowAnuncio["medida"];
        $colRow = explode("x", $rowAnuncio["tamanio"]);
        $dataExt["nro_mod"] = $colRow[0];
        $dataExt["nro_col"] = $colRow[1];
        
        $dataExt["nom_contacto"] = $rowAnuncio["nombreContacto"]." ".$rowAnuncio["apePatContacto"];
        $dataExt["d_fechapub"] = $rowAnuncio["fechaPublicConfirmada"];
        
        //plantilla
        $dataExt["id_plantilla"] = $rowAnuncio['codigo_scot'];
        
        $arrayPlanillaLogos = array(
            $this->_config->plantillaLogoIds->cinco,
            $this->_config->plantillaLogoIds->seis,
            $this->_config->plantillaLogoIds->siete
        );
        
        if ($rowAnuncio['codigo_scot'] != null) {
            if (in_array($rowAnuncio['codigo_scot'], $arrayPlanillaLogos)) {
                $dataExt["id_aviso_imp"] = $rowAnuncio["anuncioImpresoId"];
                $dataExt["html_aviso"] = $rowAnuncio["textoAnuncioImpreso"];
            } else {
                $dataExt["id_aviso_imp"] = '0';
                $dataExt["html_aviso"] = $rowAnuncio["textoAnuncioImpreso"];
            }
        } else {
            //TODO id_plantilla con valor 0 provisionalmente
            
            $dataExt["id_plantilla"] = '0';
            $dataExt["id_aviso_imp"] = $rowAnuncio["anuncioImpresoId"];
            $dataExt["html_aviso"] = null;
        }
        
        $fechaCierre = new Zend_Date($rowAnuncio["fechaPublicConfirmada"], "YYYY-MM-dd");
        
        $dataExt["fch_iniciopub"] = $rowAnuncio["fechaPublicConfirmada"];
        $dataExt["fch_finpub"] = $rowAnuncio["fechaPublicConfirmada"];
        
        
        $resultTalan = null;
        $resultAptitus = null;
        
        if ($rowAnuncio["medioPublicacion"] == Application_Model_Tarifa::MEDIOPUB_APTITUS) {
            $tipo = Application_Model_Tarifa::MEDIOPUB_APTITUS;
            
            $dataExt["cod_interno"] = $rowAnuncio["nroAdecsysAptitus"];
            $dataExt["cod_aviso"] = $rowAnuncio["correlativoAptitus"];
        
            $diaCierre = $this->_config->cierre->aptitus->dia;
            $fechaCierre->setWeekday($diaCierre);
            $dataExt["fch_cierreaviso"] = $fechaCierre->get("YYYY-MM-dd");
        
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->parametrosSCOT->$tipo->general->toArray(),
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            );
            
            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            ) + $dataExt;
            $resultAptitus = $this->callWSRegistrarOT($ws, $dataConfig, $rowAnuncio, $tipo);
            //var_dump($dataConfig);
            //$dataConfig["ValorSemana"] = "1";
            //$dataConfig["ValorMedidaTarifa"] = $dataTam["Med_Horizontal"] * $dataTam["Med_Vertical"];
            
        } else if ($rowAnuncio["medioPublicacion"] == Application_Model_Tarifa::MEDIOPUB_TALAN) {
            $tipo = Application_Model_Tarifa::MEDIOPUB_TALAN;
            
            $dataExt["cod_interno"] = $rowAnuncio["nroAdecsysTalan"];
            $dataExt["cod_aviso"] = $rowAnuncio["correlativoTalan"];
            
            $diaCierre = $this->_config->cierre->talan->dia;
            $fechaCierre->setWeekday($diaCierre);
            $dataExt["fch_cierreaviso"] = $fechaCierre->get("YYYY-MM-dd");
            
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->parametrosSCOT->$tipo->general->toArray(),
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            );
            
            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            ) + $dataExt;
            
            $resultTalan = $this->callWSRegistrarOT($ws, $dataConfig, $rowAnuncio, $tipo);
        } else if ($rowAnuncio["medioPublicacion"] == Application_Model_Tarifa::MEDIOPUB_APTITUS_TALAN) {
            $tipo = Application_Model_Tarifa::MEDIOPUB_APTITUS."Combo";
            $tipoT = Application_Model_Tarifa::MEDIOPUB_APTITUS;
            
            $dataExt["cod_interno"] = $rowAnuncio["nroAdecsysAptitus"];
            $dataExt["cod_aviso"] = $rowAnuncio["correlativoAptitus"];
            
            $diaCierre = $this->_config->cierre->aptitus->dia;
            $fechaCierre->setWeekday($diaCierre);
            $dataExt["fch_cierreaviso"] = $fechaCierre->get("YYYY-MM-dd");
            
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->parametrosSCOT->$tipoT->general->toArray(),
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            );
            
            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            ) + $dataExt;
            
            $resultAptitus = $this->callWSRegistrarOT($ws, $dataConfig, $rowAnuncio, $tipo);
            
            $tipo = Application_Model_Tarifa::MEDIOPUB_TALAN."Combo";
            $tipoT = Application_Model_Tarifa::MEDIOPUB_TALAN;
            
            $dataExt["cod_interno"] = $rowAnuncio["nroAdecsysTalan"];
            $dataExt["cod_aviso"] = $rowAnuncio["correlativoTalan"];
            
            $diaCierre = $this->_config->cierre->talan->dia;
            $fechaCierre->setWeekday($diaCierre);
            $dataExt["fch_cierreaviso"] = $fechaCierre->get("YYYY-MM-dd");
            
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->parametrosSCOT->$tipoT->general->toArray(),
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            );
            
            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->parametrosSCOT->$funcion->toArray(),
                false
            ) + $dataExt;
            
            $resultTalan = $this->callWSRegistrarOT($ws, $dataConfig, $rowAnuncio, $tipo);
        }
        if ($resultAptitus != null || $resultTalan != null) {
          //try{
            $modelAI = new Application_Model_AnuncioImpreso();
            
            $nroOTApt = null;
            $nroOTTalan = null;
            $linkApt = null;
            $linkTalan = null;
            
            if ($resultAptitus != null) {
                $resultAptitus = (array) $resultAptitus;
                $resultAptitus = (array) $resultAptitus["Registar_OTResult"];
                $nroOTApt = $resultAptitus["nro_ot"];
                $linkApt = $resultAptitus["link_adjuntar"];
            }
            
            if ($resultTalan != null) {
                $resultTalan = (array) $resultTalan;
                $resultTalan = (array) $resultTalan["Registar_OTResult"];
                $nroOTTalan = $resultTalan["nro_ot"];
                $linkTalan = $resultTalan["link_adjuntar"];
            }
            //var_dump($result);
            //exit;
            
            $modelAI->setCodScotYUrlScot($nroOTApt, $nroOTTalan, $linkApt, $linkTalan, $rowAnuncio["anuncioImpresoId"]);
         /*
          }catch(Exception $e){
             
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $flashMessenger->addMessage('Ocurrio un error al momento de registrar el aviso.');
            
            var_dump($flashMessenger->getMessages());
            exit;
          }
          * 
          */ 
        }
        /*
      }catch(Exception $e){
        echo "<h1>".$e->getMessage()."</h1>";
        print_r($e->getTrace());
        exit;
      }
        */
        /*
        var_dump($resultAptitus);
        var_dump($resultTalan);
        var_dump($rowAnuncio["anuncioImpresoId"]);
        exit;
         * 
         */
    }
    
    private function callWSRegistrarOT($ws, $params, $rowAnuncio, $tipoPublicacion) 
    {
        $response = null;
        
        try{
            $response = $ws->Registar_OT(array("odatos" => $params));
            file_put_contents(
                APPLICATION_PATH . '/../logs/Impreso_'.$tipoPublicacion.'_'. 
                $rowAnuncio['anuncioImpresoId'].'_Registar_OT_envio.xml', $ws->getLastRequest(), FILE_APPEND
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/Impreso_'.$tipoPublicacion.'_'. 
                $rowAnuncio['anuncioImpresoId'].'_Registar_OT_rpta.xml', $ws->getLastResponse(), FILE_APPEND
            );
            
        } catch (Exception $ex) {
            file_put_contents(
                APPLICATION_PATH . '/../logs/Impreso_ERROR_'.$tipoPublicacion.'_'. 
                $rowAnuncio['anuncioImpresoId'].'_Registar_OT_envio.xml', $ws->getLastRequest(), FILE_APPEND
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/Impreso_ERROR_'.$tipoPublicacion.'_'. 
                $rowAnuncio['anuncioImpresoId'].'_Registar_OT_rpta.xml', $ws->getLastResponse(), FILE_APPEND
            );
        }
        return $response;
    }
    
    public function logicaRegistroAnuncioPreferencial($rowAnuncio)
    {
        $ws = new Zend_Soap_Client($this->_config->adecsysPreferenciales->wsdl);
        $cliente = $ws->getSoapClient();
        $params = array();
        $params["Registro_Aviso_Pref_InputBE"] = array();
        
        $tamanio = strtoupper($rowAnuncio["tamanio"]);
        $funcion = "registrarAviso";
        
        //var_dump($rowAnuncio); exit;
        
        $dataExt = array();
        $dataExt["Ape_Mat_Contacto"] = trim($rowAnuncio["apeMatContacto"]) == "" ? "-": $rowAnuncio["apeMatContacto"];
        $dataExt["Ape_Pat_Contacto"] = trim($rowAnuncio["apePatContacto"]) == "" ? "-": $rowAnuncio["apePatContacto"];

        
        $dataExt["Cod_Cliente"] = $rowAnuncio["codigoEnte"];
        $dataExt["Email_Contacto"] = $rowAnuncio["emailContacto"];
        $dataExt["Fec_Registro"] = date("Y-m-d");
        $dataExt["Importe"] = $rowAnuncio["montoTotal"];
        $dataExt["Nom_Contacto"] = trim($rowAnuncio["nombreContacto"]) == "" ? "-": $rowAnuncio["nombreContacto"];

        $dataExt["Num_Doc"] = $rowAnuncio["numDocumento"];
        $dataExt["RznSoc_Nombre"] = $rowAnuncio["razonSocial"];
        $dataExt["Telf_Contacto"] = $rowAnuncio["telefonoContacto"];
        $dataExt["Tip_Doc"] = $rowAnuncio["tipoDocumento"];
        $dataExt["Contenido_Aviso"] =  $rowAnuncio["textoAnuncioImpreso"].".";
        
        $dataExt["Cod_Contrato"] = $rowAnuncio["nroContrato"];
        $dataExt["Tipo_Contrato"] = $rowAnuncio["tipoContrato"];
        
        $dataExt["Puestos_Aviso"] = array();
        $dataExt["Puestos_Aviso"]["Puesto_AvisoBE"] = array();
        

//* Puesto_Id  	char(10)		: Id del Puesto(del maestro) 	default : 0		
//* Esp_Id  	integer	x	: Id de la Especialidad (del maestro) 			
//* Ind_Id  	integer		: Id de la Industria (del maestro)	default : 0		
//* Cod_Dpto  	char(6)	X	: Codigo de Departamento Adecsys			
//* Des_Aviso  	varchar(255)	x	: Titulos de l avisos			

        foreach ($rowAnuncio["anunciosWeb"] as $anuncio) {
            $aviso = array();
            $aviso["Puesto_Id"] = 0;
            $aviso["Esp_Id"] = 0;
            $aviso["Ind_Id"] = 0;
            $aviso["Cod_Dpto"] = 0;
            $aviso["Des_Aviso"] = $anuncio["puesto"];

            $dataExt["Puestos_Aviso"]["Puesto_AvisoBE"][] = $aviso;
        }
        
        //data de prueba
        $dataExt["Prim_Fec_Pub"] = $rowAnuncio["fechaPublicConfirmada"];
        $dataExt["Fechas_Pub_Aviso"] = array();
        $dataExt["Fechas_Pub_Aviso"][] = $rowAnuncio["fechaPublicConfirmada"];
        $dataExt["Cant_Fechas_Pub"] = 1;
        
        $fechaPub = new Zend_Date($rowAnuncio["fechaPublicConfirmada"], "YYYY-MM-dd");
        
        $dataExt["Fechas_Pub"] = $fechaPub->get("dd/MM/YYYY");
        
        $dataExt["Des_Adicional"] = $rowAnuncio["nombre_comercial"];
        //$dataExt["Cod_DireccdDspacho"] = "";
        
        /*
        $dataExt["Cod_ExtraCargos"] = $rowAnuncio[""];
        
        */
        $dataExt["Modulaje"] = "";
        $dataExt["Id_Paquete"] = "";       
        $dataExt["Id_num_solicitud"] = "";
        $dataExt["Id_Item"] = "";
        $dataExt["Aplicado"] = "";
         
        //$dataExt["Num_Doc"] = "20100132592";
        //$dataExt["Cod_Cliente"] = "4470";
        
        //$dataExt["Num_Doc"] = "20297868790";
        //$dataExt["Cod_Cliente"] = "4600";
        
        //$dataExt["Num_Doc"] = "20100132593";
        //$dataExt["Cod_Cliente"] = "4471";
        
        //$dataExt["Num_Doc"] = "20143860176";
        //$dataExt["Cod_Cliente"] = "100";
        
        // SOLO CREDITO
        //$dataExt["Num_Doc"] = "20504541267";
        //$dataExt["Cod_Cliente"] = "797210";
        
        //$dataExt["Num_Doc"] = "20507545379";
        //$dataExt["Cod_Cliente"] = "755009";
        
        // SOLO MEMBRESIA
        //$dataExt["Num_Doc"] = "20107203975";
        //$dataExt["Cod_Cliente"] = "102";
        
        //$dataExt["Num_Doc"] = "20395492129";
        //$dataExt["Cod_Cliente"] = "815006";

        if ($rowAnuncio["medioPublicacion"] == Application_Model_Tarifa::MEDIOPUB_APTITUS) {
            $tipo = Application_Model_Tarifa::MEDIOPUB_APTITUS;
            
            $correlativoAptitus = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $tipo
                )
            );
            
            $dataExt["Cod_Aviso"] = $correlativoAptitus;
            $rowAnuncio["correlativoAptitus"] = $correlativoAptitus;
            
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->adecsysPreferenciales->$tipo->general->toArray(),
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            );

            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            ) + $dataExt;
            
            $params["Registro_Aviso_Pref_InputBE"][] = $dataConfig;
            
            $response = $this->callWSRegistrarAvisoPreferencial($ws, $params, $rowAnuncio, $tipo);
            // @codingStandardsIgnoreStart
            if ($response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                ->BEAvisoResponseDatos->oRegistroError->errorCodigo  == 0) {
                $nroAdecsys = $response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                    ->BEAvisoResponseDatos->sNroAdecsys;
                $where = $this->_compraAdecsysCode->getAdapter()->quoteInto('id = ?', $correlativoAptitus);
                $okUpdateP = $this->_compraAdecsysCode->update(
                    array(
                        'medio_publicacion' => $tipo,
                        'adecsys_code' => $nroAdecsys
                    ), $where
                );

                $mAnuncioImpreso = new Application_Model_AnuncioImpreso();

                $where = $mAnuncioImpreso->getAdapter()->quoteInto('id = ?', $rowAnuncio['anuncioImpresoId']);
                $okUpdateP = $okUpdateP && $mAnuncioImpreso->update(
                    array(
                        'codigo_adecsys' => $nroAdecsys
                    ), $where
                );
                $rowAnuncio["nroAdecsysAptitus"] = $nroAdecsys;
            }
            // @codingStandardsIgnoreEnd
        } else if ($rowAnuncio["medioPublicacion"] == Application_Model_Tarifa::MEDIOPUB_TALAN) {
            $tipo = Application_Model_Tarifa::MEDIOPUB_TALAN;
            $correlativoTalan = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $tipo
                )
            );
            
            $dataExt["Cod_Aviso"] = $correlativoTalan;
            $rowAnuncio["correlativoTalan"] = $correlativoTalan;
            
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->adecsysPreferenciales->$tipo->general->toArray(),
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            );

            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            ) + $dataExt;

            $params["Registro_Aviso_Pref_InputBE"][] = $dataConfig;
            
            $response = $this->callWSRegistrarAvisoPreferencial($ws, $params, $rowAnuncio, $tipo);
            // @codingStandardsIgnoreStart
            if ($response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                ->BEAvisoResponseDatos->oRegistroError->errorCodigo  == 0) {
                $nroAdecsys = $response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                    ->BEAvisoResponseDatos->sNroAdecsys;
                $where = $this->_compraAdecsysCode->getAdapter()->quoteInto('id = ?', $correlativoTalan);
                $okUpdateP = $this->_compraAdecsysCode->update(
                    array(
                        'medio_publicacion' => $tipo,
                        'adecsys_code' => $nroAdecsys
                    ), $where
                );

                $mAnuncioImpreso = new Application_Model_AnuncioImpreso();

                $where = $mAnuncioImpreso->getAdapter()->quoteInto('id = ?', $rowAnuncio['anuncioImpresoId']);
                $okUpdateP = $okUpdateP && $mAnuncioImpreso->update(
                    array(
                        'codigo_adecsys' => $nroAdecsys
                    ), $where
                );
                $rowAnuncio["nroAdecsysTalan"] = $nroAdecsys;
            }
            // @codingStandardsIgnoreEnd
        } else if ($rowAnuncio["medioPublicacion"] == Application_Model_Tarifa::MEDIOPUB_APTITUS_TALAN) {
            $tipo = Application_Model_Tarifa::MEDIOPUB_APTITUS."Combo";
            $tipoT = Application_Model_Tarifa::MEDIOPUB_APTITUS;
            
            $correlativoAptitus = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $tipoT
                )
            );
            $dataExt["Cod_Aviso"] = $correlativoAptitus;
            $rowAnuncio["correlativoAptitus"] = $correlativoAptitus;
            
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->adecsysPreferenciales->$tipoT->general->toArray(),
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            );

            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            ) + $dataExt;

            
            $params["Registro_Aviso_Pref_InputBE"][] = $dataConfig;
            
            $response = $this->callWSRegistrarAvisoPreferencial($ws, $params, $rowAnuncio, $tipo);
            
            // @codingStandardsIgnoreStart
            if ($response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                ->BEAvisoResponseDatos->oRegistroError->errorCodigo  == 0) {
                $nroAdecsys = $response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                    ->BEAvisoResponseDatos->sNroAdecsys;
                $where = $this->_compraAdecsysCode->getAdapter()->quoteInto('id = ?', $correlativoAptitus);
                $okUpdateP = $this->_compraAdecsysCode->update(
                    array(
                        'medio_publicacion' => Application_Model_CompraAdecsysCodigo::MEDIO_PUB_APTITUS_COMBO,
                        'adecsys_code' => $nroAdecsys
                    ), $where
                );
                $mAnuncioImpreso = new Application_Model_AnuncioImpreso();

                $where = $mAnuncioImpreso->getAdapter()->quoteInto('id = ?', $rowAnuncio['anuncioImpresoId']);
                $okUpdateP = $okUpdateP && $mAnuncioImpreso->update(
                    array(
                        'codigo_adecsys' => $nroAdecsys
                    ), $where
                );
                $rowAnuncio["nroAdecsysAptitus"] = $nroAdecsys;
            }
            // @codingStandardsIgnoreEnd
            
            $tipo = Application_Model_Tarifa::MEDIOPUB_TALAN."Combo";
            $tipoT = Application_Model_Tarifa::MEDIOPUB_TALAN;
            
            $correlativoTalan = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $tipoT
                )
            );
            
            $dataExt["Cod_Aviso"] = $correlativoTalan;
            $rowAnuncio["correlativoTalan"] = $correlativoTalan;
            
            $dataTam = $this->_config->adecsysPreferenciales->$tipo->$tamanio->toArray();
            
            $dataConfig = $this->getSubArrayByKeyValues(
                $this->_config->adecsysPreferenciales->$tipoT->general->toArray(),
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            );

            $dataConfig = $dataConfig + $this->getSubArrayByKeyValues(
                $dataTam,
                $this->_config->adecsysPreferenciales->$funcion->toArray()
            ) + $dataExt;
            
            $params = array();
            $params["Registro_Aviso_Pref_InputBE"] = array();
            $params["Registro_Aviso_Pref_InputBE"][] = $dataConfig;
            
            $response = $this->callWSRegistrarAvisoPreferencial($ws, $params, $rowAnuncio, $tipo);
            // @codingStandardsIgnoreStart
            if ($response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                ->BEAvisoResponseDatos->oRegistroError->errorCodigo  == 0) {
                $nroAdecsys = $response->RegistrarAvisosResult->lisBEAvisoResponseDatos
                    ->BEAvisoResponseDatos->sNroAdecsys;
                $where = $this->_compraAdecsysCode->getAdapter()->quoteInto('id = ?', $correlativoTalan);
                $okUpdateP = $this->_compraAdecsysCode->update(
                    array(
                        'medio_publicacion' => Application_Model_CompraAdecsysCodigo::MEDIO_PUB_TALAN_COMBO,
                        'adecsys_code' => $nroAdecsys
                    ), $where
                );
                
                $rowAnuncio["nroAdecsysTalan"] = $nroAdecsys;
            }
            // @codingStandardsIgnoreEnd
        }

        $this->registrarPreferencialEnSCOT($rowAnuncio);
        //exit;
        //anuncio preferencial
    }
    
    private function callWSRegistrarAvisoPreferencial($ws, $params, $rowAnuncio, $tipoPublicacion) 
    {
        $response = null;
        
        try {
            $response = $ws->RegistrarAvisos(array('listDatosAviso' => $params));
            file_put_contents(
                APPLICATION_PATH . '/../logs/Compra_'.$tipoPublicacion.'_'.
                $rowAnuncio['compraId'].'_RegistrarAvisos_envio.xml', $ws->getLastRequest(), FILE_APPEND
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/Compra_'.$tipoPublicacion.'_'.
                $rowAnuncio['compraId'].'_RegistrarAvisos_rpta.xml', $ws->getLastResponse(), FILE_APPEND
            );
        } catch (Exception $ex) {
            file_put_contents(
                APPLICATION_PATH . '/../logs/Compra_'.$tipoPublicacion.'_'.
                $rowAnuncio['compraId'].'_RegistrarAvisos_error_envio.xml', $ws->getLastRequest(), FILE_APPEND
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/Compra_'.$tipoPublicacion.'_'.
                $rowAnuncio['compraId'].'_RegistrarAvisos_error_rpta.xml', $ws->getLastResponse(), FILE_APPEND
            );
        }
        
        return $response;
    }
    
    public function logicaRegistroAnuncioClasificadoYWeb($rowAnuncio, $aptitus, $cliente)
    {
        $tarifa = $rowAnuncio['anuncioTarifaId'];
        $rowAnuncio['combo'] = null;
        if ($tarifa == 1) {
            $correlativo = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $rowAnuncio['medioPublicacion']
                )
            );
            $this->registrarAvisoSoloWeb($correlativo, $aptitus, $rowAnuncio, $cliente);
        } else if ($tarifa == 4 || $tarifa == 7 || $tarifa == 10) {
            $rowAnuncio['producto'] = $rowAnuncio['producto'];
            $rowAnuncio['combo'] = "combo";
            $rowAnuncio['medioPublicacion'] = Application_Model_CompraAdecsysCodigo::MEDIO_PUB_APTITUS;
            $rowAnuncio['medioPublicacionAdecsys'] = 
                Application_Model_CompraAdecsysCodigo::MEDIO_PUB_APTITUS_COMBO;
            $correlativo = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $rowAnuncio['medioPublicacionAdecsys']
                )
            );
            $this->registrarAvisoClasificado($correlativo, $aptitus, $rowAnuncio, $cliente);
            $rowAnuncio['medioPublicacion'] = Application_Model_CompraAdecsysCodigo::MEDIO_PUB_TALAN;
            $rowAnuncio['medioPublicacionAdecsys'] = 
                Application_Model_CompraAdecsysCodigo::MEDIO_PUB_TALAN_COMBO;
            
            foreach ($rowAnuncio['extracargos'] as $key => $value) {
                $rowAnuncio['extracargos'][$key]['adecsys_cod'] 
                = $rowAnuncio['extracargos'][$key]['adecsys_cod_envio_dos'];
            }
            $correlativo = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $rowAnuncio['medioPublicacionAdecsys']
                )
            );
            $this->registrarAvisoClasificado($correlativo, $aptitus, $rowAnuncio, $cliente);
        } else {
            $rowAnuncio['medioPublicacionAdecsys'] = $rowAnuncio['medioPublicacion'];
            $correlativo = $this->_compraAdecsysCode->insert(
                array(
                    'id_compra' => $rowAnuncio['compraId'],
                    'medio_publicacion' => $rowAnuncio['medioPublicacionAdecsys']
                )
            );
            $this->registrarAvisoClasificado($correlativo, $aptitus, $rowAnuncio, $cliente);
        }
    }
    
    public function calcularTarifaPreferencialAdecsys($webService, $cliente, $aptitus, $data, $idCompra)
    {
        try {
            $calculoTar = $webService->calcularAviso($data);

            file_put_contents(
                APPLICATION_PATH . '/../logs/compra_'. 
                $rowAnuncio['compraId'].'_CalculoTarifa_envio.xml', $cliente->getLastRequest(), FILE_APPEND
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/compra_'. 
                $rowAnuncio['compraId'].'_CalculoTarifa_rpta.xml', $cliente->getLastResponse(), FILE_APPEND
            );
        } catch (Exception $ex) {
            //return;
        }
    }
    
    public function registrarCodigoEnte($webService, $cliente, $aptitus, $rowAnuncio, $compraId)
    {
        $tipoDoc = $this->_config->adecsys->parametrosGlobales->Tipo_doc;
        try{
            $ente = $webService->validarCliente($tipoDoc, $rowAnuncio['numeroDoc']);
            if (isset ($rowAnuncio['compraId'])) {
                file_put_contents(
                    APPLICATION_PATH . '/../logs/compra_'. 
                    $rowAnuncio['compraId'].'_consulEnte_envio.xml', $cliente->getLastRequest(), FILE_APPEND
                );
                file_put_contents(
                    APPLICATION_PATH . '/../logs/compra_'. 
                    $rowAnuncio['compraId'].'_consulEnte_rpta.xml', $cliente->getLastResponse(), FILE_APPEND
                );
            } else {
                file_put_contents(
                    APPLICATION_PATH . '/../logs/registroEnte_'. 
                    $rowAnuncio['anuncioId'].'_consulEnte_envio.xml', $cliente->getLastRequest(), FILE_APPEND
                );
                file_put_contents(
                    APPLICATION_PATH . '/../logs/registroEnte_'. 
                    $rowAnuncio['anuncioId'].'_consulEnte_rpta.xml', $cliente->getLastResponse(), FILE_APPEND
                );
            }
        } catch (Exception $e){
            //return;
        }
        if ($ente != null) {
            $dataEnte = array(
                // @codingStandardsIgnoreStart
                'ente_cod' => $ente->Id,
                'doc_tipo' => $ente->Tip_Doc,
                'doc_numero' => $ente->Num_Doc,
                'ape_pat' => $ente->Ape_Pat,
                'ape_mat' => $ente->Ape_Mat,
                'nombres' => $ente->Pre_Nom,
                'razon_social' => $ente->RznSoc_Nombre,
                'tipo_persona' => $ente->Tip_Per,
                'email' => $ente->Email,
                'telefono' => $ente->Telf,
                'ciudad_adecys_cod' => $ente->Ciudad,
                'urb_tipo' => $ente->Tip_Cen_Pob,
                'urb_nombre' => $ente->Nom_Cen_Pob,
                'direc_cod' => $ente->Cod_Direccion,
                'calle_tipo' => $ente->Tip_Calle,
                'calle_nombre' => $ente->Nom_Calle,
                'calle_num' => $ente->Num_Pta,
                'estado' => $ente->Est_Act
                //@codingStandardsIgnoreEnd
            );
            $enteId = $this->_adecsysEnte->insert($dataEnte);
            $this->_empresaEnte->insert(
                array(
                    'ente_id' => $enteId,
                    'empresa_id' => $rowAnuncio['empresaId'],
                    'esta_activo' => 1,
                    'fh_creacion' => date('Y-m-d H:i:s')
                )
            );
        } else {
            $dataParaEnte = $this->_empresa->datosParaEnteAdecsys($rowAnuncio['empresaId']);
            $newEnte = $aptitus->getNuevoEnte();
            //@codingStandardsIgnoreStart
            $newEnte->Tipo_Documento = $tipoDoc;
            $newEnte->Numero_Documento = $dataParaEnte['doc_numero'];
            $newEnte->Ape_Paterno = $dataParaEnte['ape_pat'];
            $newEnte->Nombres_RznSocial = $dataParaEnte['razon_social'];
            $newEnte->Email = $dataParaEnte['email'];
            $newEnte->Telefono = $dataParaEnte['telefono'];
            $newEnte->CodCiudad = $dataParaEnte['ubigeoId'];
            $newEnte->Nombre_RznComc = $dataParaEnte['razon_social'];
            try{
                $codEnte = $webService->registrarCliente($newEnte);
                if (isset($rowAnuncio['compraId'])) {
                    file_put_contents(
                        APPLICATION_PATH . '/../logs/compra_'.
                        $rowAnuncio['compraId'].'_regEnte_envio.xml', $cliente->getLastRequest(),FILE_APPEND
                    );
                    file_put_contents(
                        APPLICATION_PATH . '/../logs/compra_'.
                        $rowAnuncio['compraId'].'_regEnte_rpta.xml', $cliente->getLastResponse(),FILE_APPEND
                    );
                } else {
                    file_put_contents(
                        APPLICATION_PATH . '/../logs/registroEnte_'.
                        $rowAnuncio['anuncioId'].'_regEnte_envio.xml', $cliente->getLastRequest(),FILE_APPEND
                    );
                    file_put_contents(
                        APPLICATION_PATH . '/../logs/registroEnte_'.
                        $rowAnuncio['anuncioId'].'_regEnte_rpta.xml', $cliente->getLastResponse(),FILE_APPEND
                    );
                }
            } catch (Exception $e){
                return;
            }
            //@codingStandardsIgnoreEnd
            $datosEnte = array(
                'ente_cod' => $codEnte,
                'doc_tipo' => $tipoDoc,
                'doc_numero' => $dataParaEnte['doc_numero'],
                'email' => $dataParaEnte['email'],
                'nombres' => $dataParaEnte['razon_social'],
                'ape_pat' => $dataParaEnte['ape_pat'],
                'telefono' => $dataParaEnte['telefono']
            );
            $enteId = $this->_adecsysEnte->insert($datosEnte);
            $this->_empresaEnte->insert(
                array(
                    'ente_id' => $enteId,
                    'empresa_id' => $rowAnuncio['empresaId'],
                    'esta_activo' => 1,
                    'fh_creacion' => date('Y-m-d H:i:s')
                )
            );
        }
    //            } catch (Exception $ex) {
    //                echo PHP_EOL . $ex->getMessage() . PHP_EOL;
    //                print_r($ex->getTraceAsString());
    //            }
    
        if (isset($compraId)) {
            $where = $this->_compra->getAdapter()->quoteInto('id = ?', $compraId);
            $okUpdateP = $this->_compra->update(
                array(
                'adecsys_ente_id' => $enteId,
                ), $where
            );
        }
        
    }
    
    public function registrarAvisoSoloWeb($correlativo, $aptitus, $rowAnuncio, $client)
    {
        $ad = $aptitus->getAvisoPref(); // obteniendo una plantilla de aviso
        $fecha = date('Y-m-d');
        // @codingStandardsIgnoreStart
        $ad->Cod_Cliente = $rowAnuncio['codigoEnte'];
        $ad->Num_Doc = $rowAnuncio['numDocumento'];
        $ad->RznSoc_Nombre = $rowAnuncio['razonSocial'];
        $ad->Tit_Aviso = $rowAnuncio['anuncioPuesto'];
        $ad->Nom_Contacto = $rowAnuncio['nombreContacto'];
        $ad->Ape_Pat_Contacto = $rowAnuncio['apePatContacto'];
        $ad->Ape_Mat_Contacto = $rowAnuncio['apeMatContacto'];
        $ad->Telf_Contacto = $rowAnuncio['telefonoContacto'];
        $ad->Email_Contacto = $rowAnuncio['emailContacto'];
        $ad->Cod_Aviso = $correlativo;
//        var_dump($rowAnuncio);
        $ad->Puestos_Aviso->Puesto_AvisoBE->Puesto_Id = $rowAnuncio['puestoAdecsysCode'];
        $ad->Puestos_Aviso->Puesto_AvisoBE->Esp_Id = '0';
        $ad->Puestos_Aviso->Puesto_AvisoBE->Ind_Id = '0';
        $ad->Puestos_Aviso->Puesto_AvisoBE->Des_Aviso = $rowAnuncio['anuncioFunciones'].
        //@codingStandardsIgnoreEnd
            " ".$rowAnuncio['anuncioFunciones'];
        try {
            $codigoImpreso = $aptitus->publicarAvisoPreferencial($fecha, $ad);
            $l = Zend_Registry::get('log');
            $l->debug(
                "Código retornado de Adecsys: " . $codigoImpreso
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/adecsys/lastRequest_'.
                $correlativo.'.xml', $client->getLastRequest()
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/adecsys/lastResponse_'.
                $correlativo.'.xml', $client->getLastResponse()
            );
        } catch (Exception $ex) {
            echo "error";
//            echo PHP_EOL . $ex->getMessage() . PHP_EOL;
//            print_r($ex->getTraceAsString());
        }
        $where = $this->_compraAdecsysCode->getAdapter()->quoteInto('id = ?', $correlativo);
        $okUpdateP = $this->_compraAdecsysCode->update(
            array(
                'adecsys_code' => $codigoImpreso
            ), $where
        );
        return $codigoImpreso;
    }

    public function registrarAvisoClasificado($correlativo, $aptitus, $rowAnuncio, $client)
    {
        $ad = $aptitus->getAvisoEc(); // obteniendo una plantilla de aviso
        //$correlativo = 1990200;
        $fecha = new Zend_Date();
        $fecha->setDate($rowAnuncio['fechaPublicConfirmada'], 'YYYY-MM-dd');
        //$fecha = $fecha->toString('YYYY-MM-dd');
        // @codingStandardsIgnoreStart
        $ad->Cod_Aviso = $correlativo;
        $ad->Cod_Cliente = $rowAnuncio['codigoEnte']; // Código ente
        $ad->Tip_Doc = $rowAnuncio['tipoDocumento'];
        $ad->Num_Doc = $rowAnuncio['numDocumento'];
        $ad->RznSoc_Nombre = $rowAnuncio['razonSocial'];
        // Datos de contacto (Usuario que publica el aviso)
        $ad->Nom_Contacto = $rowAnuncio['nombreContacto'];
        $ad->Ape_Pat_Contacto = $rowAnuncio['apePatContacto'];
        $ad->Ape_Mat_Contacto = $rowAnuncio['apeMatContacto'];
        if ($rowAnuncio['telefonoContacto'] == ''){
            $telefono = $rowAnuncio['telefonoContacto2'];
        }else{
            $telefono = $rowAnuncio['telefonoContacto'];
        }
        $ad->Telf_Contacto = $telefono;
        $ad->Email_Contacto = $rowAnuncio['emailContacto'];
        // Datos del aviso
        
        $ad->Des_Puesto_Titulo = $rowAnuncio['nombreTipoPuesto'];
        $options = $this->_config->adecsys->proxy->param->toArray();
        $ad->Texto_Aviso = $rowAnuncio['textoAnuncioImpreso'].' '.
            $this->_config->adecsys->preCodigoAdecsys;
//            $rowAnuncio['anuncioUrl'];
        $ad->Num_Palabras = $rowAnuncio['beneficios']['npalabras']['valor'];
//        $d = new Zend_Date();
//        $d->set($rowAnuncio['fechaPublicConfirmada']);
//        $ad->Prim_Fec_Pub = $d->toString('YYYY-MM-dd');
        $ad->Puesto_Aviso->Puesto_Id = $rowAnuncio['puestoAdecsysCode'];
        $ad->Puesto_Aviso->Esp_Id = $rowAnuncio['puestoIdEspecialidad'];
        $extracargos = array();
        foreach ($rowAnuncio['extracargos'] as $key => $value) {
            $extracargos[] = $rowAnuncio['extracargos'][$key]['adecsys_cod'];
        }
        
        $vHelperCast = new App_View_Helper_LuceneCast();
        $nombreTipo = $vHelperCast->LuceneCast($rowAnuncio['productoNombre']);
        //var_dump($nombreTipo);
        //exit;
        $ad = $aptitus->completeAd(
            $ad, $rowAnuncio['puestoTipo'], 
            $rowAnuncio['medioPublicacion'], 
            $nombreTipo,
            $rowAnuncio['combo']
        );
        
        try {
            $codigoImpreso = $aptitus->publicarAviso($fecha, $ad, $extracargos);
            $l = Zend_Registry::get('log');
            $l->debug(
                "Código retornado de Adecsys: " . $codigoImpreso
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/compra_'.
                $rowAnuncio['compraId'].'_regAnuncio_'.$correlativo.'_envio.xml', $client->getLastRequest(),FILE_APPEND
            );
            file_put_contents(
                APPLICATION_PATH . '/../logs/compra_'.
                $rowAnuncio['compraId'].'_regAnuncio_'.$correlativo.'_rpta.xml', $client->getLastResponse(),FILE_APPEND
            );
            #$ret = $apt->importarAvisosTest('2011-08-23', 'E');
        } catch (Exception $ex) {
            echo PHP_EOL . $ex->getMessage() . PHP_EOL;
            print_r($ex->getTraceAsString());
        }
        $where = $this->_compraAdecsysCode->getAdapter()->quoteInto('id = ?', $correlativo);
        $okUpdateP = $this->_compraAdecsysCode->update(
            array(
                'medio_publicacion' => $rowAnuncio['medioPublicacionAdecsys'],
                'adecsys_code' => $codigoImpreso
            ), $where
        );
        return $codigoImpreso;
    }

    /**
     * Extiende un aviso, cambia los datos y migra las postulaciones
     * 
     * @param int $avisoId
     * @param int $idUsuario
     */
    public function extenderAviso($avisoId, $idUsuario)
    {
        //Verificar si es un anuncio esta vencido
        $avisoOrigen = $this->_aw->getAvisoExtendido($avisoId);
        $this->_postulacion->extenderPostulaciones($avisoOrigen['extiende_a'], $avisoId);
        $tipo = $this->_aw->getTipoAnuncioById($avisoOrigen['extiende_a']);
        if ($tipo == Application_Model_AnuncioWeb::TIPO_SOLOWEB || 
            $tipo == Application_Model_AnuncioWeb::TIPO_CLASIFICADO)
            $this->_aw->bajaAnuncioWeb($avisoOrigen['extiende_a'], $avisoId, $idUsuario);
        //$this->_aw->publicarAnuncioWeb($avisoId, $idUsuario);
        $this->actualizarPostulantes($avisoId);
        $this->actualizarNuevasPostulaciones($avisoId);
    }

    public function confirmarPago($compraId)
    {
        // AUN FALTA IMPLEMENTAR

        if ("extiende_a") {
            $this->extenderAviso($avisoId, $idUsuario);
        }

        //actualizar campos de pago anuncio compra
    }

    /**
     * Obtiene la cantidad de postulantes por cada anuncio y lo actualiza en la 
     * tabla anuncio_web
     * 
     * @param int $avisoId
     */
    public function actualizarPostulantes($avisoId)
    {
        $postulacion = new Application_Model_Postulacion();
        $aviso = new Application_Model_AnuncioWeb();
        $where = $aviso->getAdapter()->quoteInto("id = ?", $avisoId);
        $aviso->update(
            array('ntotal' => $postulacion->getPostulantesByAviso($avisoId)), $where
        );
    }

    /**
     * Obtiene la cantidad de invitaciones de un anuncio web y lo actualiza en la 
     * tabla anuncio_wen
     * 
     * @param int $avisoId
     */
    public function actualizarInvitaciones($avisoId)
    {
        $postulacion = new Application_Model_Postulacion();
        $aviso = new Application_Model_AnuncioWeb();
        $where = $aviso->getAdapter()->quoteInto("id = ?", $avisoId);
        $aviso->update(
            array('ninvitaciones' => $postulacion->getInvitacionesByAviso($avisoId)), $where
        );
    }

    /**
     * Obtiene la cantidad de nuevas postulaciones por anuncio web y lo actualiza
     * en la tabla anuncio_web
     * 
     * @param int $avisoId
     */
    public function actualizarNuevasPostulaciones($avisoId)
    {
        $postulacion = new Application_Model_Postulacion();
        $aviso = new Application_Model_AnuncioWeb();
        $where = $aviso->getAdapter()->quoteInto("id = ?", $avisoId);
        $aviso->update(
            array('nnuevos' => $postulacion->getNuevasPostulacionesByAviso($avisoId)), $where
        );
    }

    /**
     * Obtiene la cantidad de mensajes no leidos por postulacion y lo actualiza
     * en la tabla anuncio_web y postulacion
     * 
     * @param int $avisoId
     * @param int $postulacionId
     */
    public function actualizarMsgRsptNoLeidos($avisoId, $postulacionId)
    {
        $postulacion = new Application_Model_Postulacion();
        $aviso = new Application_Model_AnuncioWeb();
        $where = $aviso->getAdapter()->quoteInto("id = ?", $avisoId);
        
        $aviso->update(
            array('nmsjrespondidos' => $postulacion->getMsgRsptNoLeidosByAviso($avisoId)), 
            $where
        );
        
        $where = $postulacion->getAdapter()->quoteInto("id = ?", $postulacionId);
        $msgrespondido = $postulacion->getMsgRsptNoLeidosXPostulacion($postulacionId);
        $postulacion->update(
            array('msg_respondido' => 
                $msgrespondido
            ), 
            $where
        );
       /* $zl = new ZendLucene();
        $zl->updateIndexPostulaciones($postulacionId, "msgrespondido", $msgrespondido);*/
    }
    
    public function actualizarMsgRsptPerfil($idAw, $idPostulacion)
    {
        $modelMsj = new Application_Model_Mensaje();
        $idMensajes = $modelMsj->getIdMensajesRsptaXPostulacion($idPostulacion);
        $where = $modelMsj->getAdapter()->quoteInto('id in (?)', $idMensajes);
        
        $modelMsj->update(array('leido'=>1), $where);
        
        $postulacion = new Application_Model_Postulacion();
        
        $aviso = new Application_Model_AnuncioWeb();
        $where = $aviso->getAdapter()->quoteInto("id = ?", $idAw);
        
        $aviso->update(
            array('nmsjrespondidos' => $postulacion->getMsgRsptNoLeidosByAviso($idAw)), 
            $where
        );
        
        $where = $postulacion->getAdapter()->quoteInto("id = ?", $idPostulacion);
        $postulacion->update(
            array('msg_respondido' => 
                $postulacion->getMsgRsptNoLeidosXPostulacion($idPostulacion)
            ), 
            $where
        );
    }
    /**
     * Guarda un registro en la tabla anuncio_web y devuelve el ID del anuncio
     * ingresado
     * 
     * @param array $dataPost
     * @param String $extiende
     * @return int
     */
    public function _insertarNuevoPuesto(array $dataPost, $extiende = null, $emp="", $republica = null)
    {
        $action = $this->getActionController();
        $session = $this->session;
        $tarifa = new Application_Model_Tarifa();
        $datosTarifa = $tarifa->getProductoByTarifa($dataPost['id_tarifa']);
        
        if ($dataPost['salario'] == -1) {
            $salario[0] = null;
            $salario[1] = null;
        } else {
            $salario = explode('-', $dataPost['salario']);
            if ($salario[1] == 'max') {
                $salario[1] = null;
            }
        }
        
        $usuario = $this->auth['usuario'];
        //var_dump($dataPost);
        
        if ($emp!="" || $emp != null) {
            $empresa = $emp;
        } elseif (isset($this->auth['empresa'])) {
            $empresa = $this->auth['empresa'];
        } else {
            $empresa = "";
        }
        
        if ($empresa == "") {
            $empresa['logo'] = $dataPost['logo_empresa'];
            $empresa['id'] = $dataPost['id_empresa'];
            $empresa['nombre_comercial'] = $dataPost['nombre_comercial'];
        } 
        
        if (!isset($empresa['id']))
        {
            $empresa['id'] = $session->empresaBusqueda['idempresa'];
            $empresa['nombre_comercial'] = $session->empresaBusqueda['nombreComercial'];
            $empresa['logo'] = $session->empresaBusqueda['logo'];
        }
        
        if ($dataPost['mostrar_empresa'] == 1) {
            $nombreEmpresa = $empresa['nombre_comercial'];
        } else {
            $nombreEmpresa = $dataPost['otro_nombre_empresa'];
        }
        $anuncionWeb = new Application_Model_AnuncioWeb();
        $slugFilter = new App_Filter_Slug();
        //$genPassword = $action->getHelper('GenPassword');
        $_tu = new Application_Model_TempUrlId();
        
        $anuncio = array_merge(
            array('extiende_a' => $extiende), array(
            'id_puesto' => $dataPost['id_puesto'],
            'id_empresa_membresia' => $dataPost['id_empresa_membresia'],
            'id_producto' => $dataPost['id_producto'],
            'puesto' => $dataPost['nombre_puesto'],
            'id_area' => $dataPost['id_area'],
            'id_nivel_puesto' => $dataPost['id_nivel_puesto'],
            'funciones' => $dataPost['funciones'],
            'responsabilidades' => $dataPost['responsabilidades'],
            'mostrar_salario' => $dataPost['mostrar_salario'],
            'mostrar_empresa' => $dataPost['mostrar_empresa'],
            'salario_min' => $salario[0],
            'salario_max' => $salario[1],
            'online' => 1,
            'borrador' => '1',
            'id_empresa' => $empresa['id'],
            'id_ubigeo' => $dataPost['id_ubigeo'],
            'fh_creacion' => date('Y-m-d H:i:s'),
            'fh_edicion' => date('Y-m-d H:i:s'),
            'creado_por' => $usuario->id,
            //'url_id' => $genPassword->_genPassword(5),
            'prioridad' => $dataPost['prioridad'], 
            'url_id' => $_tu->popUrlId(),
            'slug' => $slugFilter->filter($dataPost['nombre_puesto']),
            'empresa_rs' => $nombreEmpresa,
            'estado' => 'registrado',
            'origen' => 'apt_2',
            'id_tarifa' => $datosTarifa['id_tarifa'],
            'id_producto' => $datosTarifa['id_producto'],
            'tipo' => $datosTarifa['tipo'],
            'medio_publicacion' => $datosTarifa['medio_publicacion'],
            'logo' => $empresa["logo"],
            'chequeado' => 1,
            'bolsa_uni' => $dataPost['bolsa_uni'],
            'ver_aptitus' => $dataPost['ver_aptitus']
            )
        );
        
        if ($republica != null) {
            $anuncio['republicado'] = $republica;
        }
         
        $this->_avisoId = $anuncionWeb->insert($anuncio);
        
        if ($extiende == null) {
            $where = $anuncionWeb->getAdapter()->quoteInto('id = ?', $this->_avisoId);
            $anuncionWeb->update(array('extiende_a' => $this->_avisoId), $where);
        }
        
        if ($extiende != null) {
            $arrayAnunciOld = $anuncionWeb->getAvisoInfoById($extiende);
            $where = $anuncionWeb->getAdapter()->quoteInto('id = ?', $this->_avisoId);
            $anuncionWeb->update(
                array(
                    'url_id' => $arrayAnunciOld['url_id'],
                    'extiende_a' => $arrayAnunciOld['id']
                ), 
                $where
            );
        }
        return $this->_avisoId;
    }

    /**
     * Registra los estudios relacionados al anuncio
     * 
     * @param App_Form_Manager $managerEstudio
     */
    public function _insertarEstudios(App_Form_Manager $managerEstudio)
    {
        $estudio = new Application_Model_AnuncioEstudio();
        $formsEstudios = $managerEstudio->getForms();
        foreach ($formsEstudios as $form) {
            $data = $form->getValues();
            if ($data['id_nivel_estudio'] != -1 && $data['id_carrera'] != -1) {
                $estudio->insert(
                    array(
                        'id_anuncio_web' => $this->_avisoId,
                        'id_nivel_estudio' => $data['id_nivel_estudio'],
                        'id_carrera' => $data['id_carrera']
                    )
                );
            }
        }
    }

    /**
     * Registra las experiencias relacionados a un anuncio web
     * 
     * @param App_Form_Manager $managerExperiencia
     */
    public function _insertarExperiencia(App_Form_Manager $managerExperiencia)
    {
        $experiencia = new Application_Model_AnuncioExperiencia();
        $formsExperiencias = $managerExperiencia->getForms();
        foreach ($formsExperiencias as $form) {
            $data = $form->getValues();
            if ($data['id_nivel_puesto'] != -1 && $data['id_area'] != -1) {
                $experiencia->insert(
                    array(
                        'id_anuncio_web' => $this->_avisoId,
                        'id_nivel_puesto' => $data['id_nivel_puesto'],
                        'id_area' => $data['id_area'],
                        'experiencia' => $data['experiencia']
                    )
                );
            }
        }
    }

    /**
     * Registra los programas de computo asociados a un anuncio web
     * 
     * @param App_Form_Manager $managerPrograma
     */
    public function _insertarPrograma(App_Form_Manager $managerPrograma)
    {
        $programa = new Application_Model_AnuncioProgramaComputo();
        $formsProgramas = $managerPrograma->getForms();
        foreach ($formsProgramas as $form) {
            $data = $form->getValues();
            if ($data['id_programa_computo'] != -1 && $data['nivel'] != -1) {
                $programa->insert(
                    array(
                        'id_programa_computo' => $data['id_programa_computo'],
                        'id_anuncio_web' => $this->_avisoId,
                        'nivel' => $data['nivel']
                    )
                );
            }
        }
    }

    /**
     * Registra los idiomas relacionas a un anuncio web
     * 
     * @param App_Form_Manager $managerIdioma
     */
    public function _insertarIdiomas(App_Form_Manager $managerIdioma)
    {
        $idioma = new Application_Model_AnuncioIdioma();
        $formsIdiomas = $managerIdioma->getForms();
        foreach ($formsIdiomas as $form) {
            $data = $form->getValues();
            if ($data['id_idioma'] != -1 && $data['nivel_idioma'] != -1) {
                $idioma->insert(
                    array(
                        'id_idioma' => $data['id_idioma'],
                        'id_anuncio_web' => $this->_avisoId,
                        'nivel' => $data['nivel_idioma']
                    )
                );
            }
        }
    }

    /**
     * Registra las preguntas y genera un cuestionario para la empresa
     * 
     * @param App_Form_Manager $managerPregunta
     */
    public function _insertarPreguntas(App_Form_Manager $managerPregunta, $idEmpresa = null)
    {
        
        if (isset($idEmpresa)) {
            $modelEmpresa = new Application_Model_Empresa();
            $empresa = $modelEmpresa->getEmpresa($idEmpresa);
            $empresa['nombre_comercial'] = $empresa['nombrecomercial'];
        } else {
            $empresa = $this->auth['empresa'];
        }
        
        $formsPreguntas = $managerPregunta->getForms();
        foreach ($formsPreguntas as $fpreg) {
            $data = $fpreg->getValues();
            if ($data['pregunta'] != "") {
                $cuestionario = new Application_Model_Cuestionario();
                $cuestionarioId = $cuestionario->insert(
                    array(
                        'id_empresa' => isset($idEmpresa)? $empresa['id_empresa']:$empresa['id'],
                        'id_anuncio_web' => $this->_avisoId,
                        'nombre' =>
                        'Cuestionario de la empresa ' . $empresa['nombre_comercial']
                    )
                );
                break;
            }
        }
        foreach ($formsPreguntas as $form) {
            $data = $form->getValues();
            if ($data['pregunta'] != "") {
                $pregunta = new Application_Model_Pregunta();
                $pregunta->insert(
                    array(
                        'id_cuestionario' => $cuestionarioId,
                        'pregunta' => $data['pregunta']
                    )
                );
            }
        }
    }

    public function _crearSlug($valuesPostulante, $lastId)
    {
        $slugFilter = new App_Filter_Slug(
            array('field' => 'slug',
                'model' => $this->_empresa)
        );

        $slug = $slugFilter->filter(
            $valuesPostulante['razon_social'] . ' ' .
            $valuesPostulante['ruc'] . ' ' .
            substr(md5($lastId), 0, 8)
        );
        return $slug;
    }

    /**
     * Actualiza los datos del anuncio web
     * 
     * @param Application_Form_Paso2PublicarAviso $formPuesto
     * @param int $idAviso
     */
    public function _actualizarDatosPuesto(
    Application_Form_Paso2PublicarAviso $formPuesto, $idAviso, $idEmpresa = null, $idUbigeo = null
    )
    {
        $aviso = new Application_Model_AnuncioWeb();
        $arrayAviso = $aviso->getAvisoById($idAviso);
        
        $this->_cache->remove('AnuncioWeb_getAvisoInfoById_'.$idAviso);
        $this->_cache->remove('AnuncioWeb_getAvisoById_'.$idAviso);
        //$this->_cache->remove('anuncio_web_'.$arrayAviso['url_id']);
        
        if (strpos($this->getFrontController()->getModuleDirectory(), 'admin')) {
            $admin = true;
        }
        $data = $formPuesto->getValues();
        $aviso = new Application_Model_AnuncioWeb();
        $arrayAviso = $aviso->getAvisoById($idAviso);
   
        $where = $aviso->getAdapter()
            ->quoteInto('id = ?', $idAviso);
        if ($arrayAviso['online'] == 1 && !isset($admin)) {
            $aviso->update(
                array(
                'funciones' => $data['funciones'], 
                'responsabilidades' => $data['responsabilidades'],  
                'fh_edicion' => date('Y-m-d H:i:s')
                ), $where
            );
        } 
        if ($arrayAviso['online'] == 0 && $arrayAviso['borrador'] == 1) {
            $slugFilter = new App_Filter_Slug();
            $aviso->update(
                array(
                    'slug' => $slugFilter->filter($data['nombre_puesto'])
                ), 
                $where
            );
        }
        if (isset($idEmpresa)) {
            $aviso->update(array('creado_por' => $this->auth['usuario']->id), $where);
        }
        $this->_cache->remove('AnuncioWeb_getAvisoInfoById_'.$idAviso);
        $this->_cache->remove('AnuncioWeb_getFullAvisoById_'.$idAviso);
        $this->_cache->remove('AnuncioWeb_getAvisoById_'.$idAviso);
        $this->_cache->remove('anuncio_web_'.$arrayAviso['url_id']);
        
    }

    /**
     * Actualiza los estudios de un anuncio web
     * 
     * @param App_Form_Manager $managerEstudio
     * @param int $idAviso
     */
    public function _actualizarEstudios(App_Form_Manager $managerEstudio, $idAviso)
    {
        $formEstudio = $managerEstudio->getForms();
        foreach ($formEstudio as $form) {
            $data = $form->getValues();
            $idEst = $data['id_estudio'];
            $estudio = new Application_Model_AnuncioEstudio();
            unset($data['id_estudio']);
            if ($data['id_nivel_estudio'] != -1 && $data['id_carrera'] != -1) {
                if ($data['id_carrera'] == -1) {
                    $data['id_carrera'] = null;
                }
                if ($idEst) {
                    $where = $estudio->getAdapter()
                            ->quoteInto('id_anuncio_web = ?', $idAviso) .
                            $estudio->getAdapter()
                            ->quoteInto(' and id = ?', $idEst);
                    $estudio->update(
                        array(
                        'id_nivel_estudio' => $data['id_nivel_estudio'],
                        'id_carrera' => $data['id_carrera']
                        ), $where
                    );
                } else {
                    $estudio->insert(
                        array(
                            'id_anuncio_web' => $idAviso,
                            'id_nivel_estudio' => $data['id_nivel_estudio'],
                            'id_carrera' => $data['id_carrera']
                        )
                    );
                }
            }
        }
    }

    /**
     * Actualiza las experiencias de un anuncio web
     * 
     * @param App_Form_Manager $managerExperiencia
     * @param int $idAviso
     */
    public function _actualizarExperiencas(App_Form_Manager $managerExperiencia, $idAviso)
    {
        $formsExperiencia = $managerExperiencia->getForms();
        foreach ($formsExperiencia as $form) {
            $data = $form->getValues();
            $idExp = $data['id_Experiencia'];

            $experiencia = new Application_Model_AnuncioExperiencia();

            unset($data['id_Experiencia']);
            if ($data['id_nivel_puesto'] != -1 && $data['id_area'] != -1) {
                if ($idExp) {
                    $where = $experiencia->getAdapter()
                            ->quoteInto('id_anuncio_web = ?', $idAviso) .
                            $experiencia->getAdapter()
                            ->quoteInto(' and id = ?', $idExp);
                    $experiencia->update(
                        array(
                        'id_nivel_puesto' => $data['id_nivel_puesto'],
                        'id_area' => $data['id_area'],
                        'experiencia' => $data['experiencia']
                        ), $where
                    );
                } else {
                    $idExperiencia = $experiencia->insert(
                        array(
                            'id_anuncio_web' => $idAviso,
                            'id_nivel_puesto' => $data['id_nivel_puesto'],
                            'id_area' => $data['id_area'],
                            'experiencia' => $data['experiencia']
                        )
                    );
                }
            }
        }
    }

    /**
     * Actualizar el idioma del anuncio
     * 
     * @param App_Form_Manager $managerIdioma
     * @param int $idAviso
     */
    public function _actualizarIdioma(App_Form_Manager $managerIdioma, $idAviso)
    {
        $formIdioma = $managerIdioma->getForms();
        foreach ($formIdioma as $form) {
            $data = $form->getValues();
            $idIdi = $data['id_dominioIdioma'];
            $idioma = new Application_Model_AnuncioIdioma();
            unset($data['id_dominioIdioma']);
            if ($data['id_idioma'] != -1 && $data['nivel_idioma'] != -1) {
                if ($idIdi) {
                    $where = $idioma->getAdapter()
                            ->quoteInto('id_anuncio_web = ?', $idAviso) .
                            $idioma->getAdapter()
                            ->quoteInto(' and id = ?', $idIdi);
                    $idioma->update(
                        array(
                        'id_idioma' => $data['id_idioma'],
                        'nivel' => $data['nivel_idioma']
                        ), $where
                    );
                } else {
                    $idioma->insert(
                        array(
                            'id_idioma' => $data['id_idioma'],
                            'id_anuncio_web' => $idAviso,
                            'nivel' => $data['nivel_idioma']
                        )
                    );
                }
            }
        }
    }

    /**
     * Actualiza los programas de nu anuncio web
     * 
     * @param App_Form_Manager $managerPrograma
     * @param int $idAviso
     */
    public function _actualizarPrograma(App_Form_Manager $managerPrograma, $idAviso)
    {
        $formPrograma = $managerPrograma->getForms();
        foreach ($formPrograma as $form) {

            $data = $form->getValues();
            $idProg = $data['id_dominioComputo'];
            if ($data['id_programa_computo'] != -1 && $data['nivel'] != -1) {
                $programa = new Application_Model_AnuncioProgramaComputo();
                unset($data['id_dominioComputo']);

                if ($idProg) {

                    $where = $programa->getAdapter()
                            ->quoteInto('id_anuncio_web = ?', $idAviso) .
                            $programa->getAdapter()
                            ->quoteInto(' and id = ?', $idProg);
                    $programa->update(
                        array(
                        'id_programa_computo' => $data['id_programa_computo'],
                        'nivel' => $data['nivel']
                        ), $where
                    );
                } else {
                    $programa->insert(
                        array(
                            'id_programa_computo' => $data['id_programa_computo'],
                            'id_anuncio_web' => $idAviso,
                            'nivel' => $data['nivel']
                        )
                    );
                }
            }
        }
    }

    public function _actualizarPregunta(App_Form_Manager $managerPregunta, $idAviso, $idEmpresa = null)
    {
        $formPregunta = $managerPregunta->getForms();
        $cuestionario = new Application_Model_Cuestionario();
        if (!$cuestionario->getPreguntasByAnuncioWeb($idAviso)) {
            if (isset($idEmpresa)) {
                $modelEmpresa = new Application_Model_Empresa();
                $empresa = $modelEmpresa->getEmpresa($idEmpresa);
                $empresa['nombre_comercial'] = $empresa['nombrecomercial'];
            } else {
                $empresa = $this->auth['empresa'];
            }
            $cuestionarioId = $cuestionario->insert(
                array(
                    'id_empresa' => isset($idEmpresa)? $empresa['id_empresa']:$empresa['id'],
                    'id_anuncio_web' => $idAviso,
                    'nombre' =>
                    'Cuestionario de la empresa ' . $empresa['nombre_comercial']
                )
            );
        } else {
            $cuestionarioId = $cuestionario->getCuestionarioByAnuncioWeb($idAviso);
        }
        foreach ($formPregunta as $form) {
            $data = $form->getValues();
            $idPreg = $data['id_pregunta'];
            if ($data['pregunta'] != "") {
                $pregunta = new Application_Model_Pregunta();
                if ($idPreg) {
                    $where = $pregunta->getAdapter()
                            ->quoteInto('id_cuestionario = ?', $cuestionarioId) .
                            $pregunta->getAdapter()
                            ->quoteInto(' and id = ?', $idPreg);
                    $pregunta->update(
                        array(
                        'pregunta' => $data['pregunta']
                        ), $where
                    );
                } else {
                    $pregunta->insert(
                        array(
                            'id_cuestionario' => $cuestionarioId,
                            'pregunta' => $data['pregunta']
                        )
                    );
                }
            }
        }
    }
    
    /**
     *
     * @param int $idCompra
     * @param int $idEmpresa
     * @return boolean
     */
    public function perteneceCompraAEmpresa($idCompra,$idEmpresa)
    {
        $cant = $this->_compra->perteneceCompraEmpresa($idCompra, $idEmpresa);
        if (is_numeric($idCompra) && $cant > 0) {
            return true;
        }
        return false;
    }
    
    public function perteneceAvisoAEmpresa($idAviso,$idEmpresa)
    {
        $cant = $this->_aw->perteneceAvisoEmpresa($idAviso, $idEmpresa);
        if (is_numeric($idAviso) && $cant > 0) {
            return true;
        }
        return false;
    }
    
    
    public function getUrlIdGeneralPostulante($idData, $tipoModelo)
    {
        $nomModel = '';
        if ('Pregunta' == $tipoModelo ) {
            $nomModel = 'Application_Model_'.ucfirst($tipoModelo);
        } else {
            $nomModel = 'Application_Model_Anuncio'.ucfirst($tipoModelo);
        }
        $model = new $nomModel();
        $urlId= $model->getUrlById($idData);
        return $urlId;
    }
    
    /**
     * Genera una URL para hacer una redireccion en el editar aviso
     * 
     * @param unknown_type $url
     */
    public function EncodeRedirect($url)
    {
        $url = str_replace('/', '*', $url);
        return base64_encode($url);
    }
    
    public function DecodeRedirect($url)
    {
        $url = base64_decode($url);
        $url = str_replace('*', '/', $url);
        return $url;
    }
    
    public function accesoPublicarAvisoAdmin($idProd, $rol) 
    {
        if ( Application_Form_Login::ROL_ADMIN_SOPORTE == $rol) {
            $idProdAcceso = 1;
            if($idProdAcceso == $idProd) {
                return true;
            }
        } elseif (Application_Form_Login::ROL_ADMIN_CALLCENTER == $rol) {
            $idProdAcceso = '2,3,4';
            $var = explode(',', $idProdAcceso);
            foreach ($var as $row) { 
                if ($row == $idProd) {
                    return true;
                }
            }
        } elseif (Application_Form_Login::ROL_ADMIN_MASTER == $rol) {
            $idProdAcceso = '1,2,3,4';
            $var = explode(',', $idProdAcceso);
            foreach ($var as $row) { 
                if ($row == $idProd) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function accesoPublicaAvisoAdminInstitucion($empresa, $institucion)
    {
        $empresaModelo = new Application_Model_Empresa;
        $activo = Application_Model_EmpresaInstitucion::ESTADO_ACTIVO;
        $data = $empresaModelo->getAccesoAvisoEmpresaBolsa($activo, $empresa, $institucion);
        
        if ($data == null)
            return false;
        else
            return true;
        
        
    }
    
    public function getFechaPublicacionImpresoByPaquete($paquete)
    {
//        var_dump($this->_config->cierre->toArray());exit;
        $cierre = $this->_config->cierre->toArray();
        $fecNow = new Zend_Date();
        $fecVenc = clone $fecNow;
        $fecVenc->set($cierre[$paquete]['dia'], Zend_Date::WEEKDAY_DIGIT);
        $fecVenc->set($cierre[$paquete]['hora'], Zend_Date::HOUR);
        $fecVenc->set(0, Zend_Date::MINUTE);
        $fecVenc->set(0, Zend_Date::SECOND);
        $fecImpre = clone $fecVenc;
        $fecImpre->set(0, Zend_Date::HOUR);
        if ($cierre[$paquete]['semanaActual'] == 0) {
            $fecImpre->add(7, Zend_Date::DAY);
        }
        $fecImpre->set($cierre[$paquete]['diaPublicacion'], Zend_Date::WEEKDAY_DIGIT);
        if ($fecNow->isLater($fecVenc)) {
            $fecImpre->add(7, Zend_Date::DAY);
        }
        return $fecImpre;
    }
    
    public function eliminarCacheAptitus($urlId)
    {
        $curl = curl_init();
        $url = 'http://devel.aptitus.info/empresa/publica-aviso/eliminar-cache/url_id/'.$urlId;
        
        curl_setopt(
            $curl, 
            CURLOPT_URL, 
            $url
        );
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1);   
        $content = @curl_exec($curl);
        curl_close($curl);
    }
}

