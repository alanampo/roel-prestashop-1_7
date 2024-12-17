<?php // NOSONAR
if (!defined('_PS_VERSION_')) {
    exit();
}

include_once(dirname( __FILE__ )  . '/chilexpress_oficial_api.php');
include_once(dirname( __FILE__ )  . '/controllers/admin/ChilexpressOrderController.php');
include_once(dirname( __FILE__ )  . '/classes/ChilexpressOficialApiCache.php');

define('CXP_CHILEXPRESS_ORDER', 'ChilexpressOrder');
define('CXP_API_KEY_GEOREFERENCIA_VALUE','api_key_georeferencia_value');
define('CXP_API_KEY_GENERACION_OT_VALUE','api_key_generacion_ot_value');
define('CXP_API_KEY_COTIZADOR_VALUE','api_key_cotizador_value');
define('CXP_API_KEY_GEOREFERENCIA_ENABLED','api_key_georeferencia_enabled');
define('CXP_API_KEY_GENERACION_OT_ENABLED','api_key_generacion_ot_enabled');
define('CXP_API_KEY_COTIZADOR_ENABLED','api_key_cotizador_enabled');
define('CXP_AMBIENTE', 'ambiente');
define('CXP_REGION_ORIGEN', 'region_origen');
define('CXP_ARTICULOS_TIENDA', 'articulos_tienda');
define('CXP_COMUNA_ORIGEN', 'comuna_origen');
define('CXP_COMUNA_DESTINO', 'comuna_destino');
define('CXP_NUMERO_TCC_ORIGEN', 'numero_tcc_origen');
define('CXP_RUT_MARKETPLACE_REMITENTE', 'rut_marketplace_remitente');
define('CXP_REGION_DEVOLUCION', 'region_devolucion');
define('CXP_COMUNA_DEVOLUCION', 'comuna_devolucion');
define('CXP_ADMINMODULES', 'AdminModules');
define('CXP_ID_FORM', 'id_form');
define('CXP_LEGEND', 'legend');
define('CXP_TITLE', 'title');
define('CXP_INPUT', 'input');
define('CXP_SWITCH', 'switch');
define('CXP_LABEL', 'label');
define('CXP_MODULES_CONTACTFORM_ADMIN', 'Modules.Contactform.Admin');

/// 
define('CXP_IS_BOOL', 'is_bool');
define('CXP_REQUIRED', 'required');
define('CXP_VALUES', 'values');
define('CXP_VALUE', 'value');
define('CXP_ADMIN_GLOBAL', 'Admin.Global');
define('CXP_HABILITADO', 'Habilitado');
define('CXP_DESHABILITADO', 'Deshabilitado');
define('CXP_MODULES_BANNER_ADMIN', 'Modules.Banner.Admin');
define('CXP_SELECT', 'select');
define('CXP_OPTIONS', 'options');
define('CXP_QUERY', 'query');
define('CXP_SUBMIT', 'submit');
define('CXP_GUARDAR', 'Guardar');
define('CXP_CLASS', 'class');
define('CXP_BTN_BTN_DEFAULT_PULL_RIGHT', 'btn btn-default pull-right');
define('CXP_NOMBRE_REMITENTE', 'nombre_remitente');
define('CXP_TELEFONO_REMITENTE', 'telefono_remitente');
define('CXP_EMAIL_REMITENTE', 'email_remitente');
define('CXP_RUT_SELLER_REMITENTE', 'rut_seller_remitente');
define('CXP_CALLE_DEVOLUCION', 'calle_devolucion');
define('CXP_NUMERO_CALLE_DEVOLUCION', 'numero_calle_devolucion');
define('CXP_COMPLEMENTO_DEVOLUCION', 'complemento_devolucion');
define('CXP_ID_CARRIER', 'id_carrier');
define('CXP_CARRIER', 'carrier');
define('CXP_DELIVERY', 'delivery');
define('CXP_ID_ZONE', 'id_zone');
define('CXP_ID_PRODUCT', 'id_product');
define('CXP_CART_QUANTITY', 'cart_quantity');
define('CXP_WIDTH', 'width');
define('CXP_HEIGHT', 'height');
define('CXP_DEPTH', 'depth');
define('CXP_WEIGHT', 'weight');
define('CXP_TOTAL', 'total');
define('CXP_SERVICETYPECODE', 'serviceTypeCode');
define('CXP_SERVICEVALUE', 'serviceValue');
define('CXP_FIELDS', 'fields');
define('CXP_TEXT_CENTER', 'text-center');
define('CXP_ALIGN', 'align');
define('CXP_FIXED_WIDTH_XS', 'fixed-width-xs');
define('CXP_FILTER_KEY', 'filter_key');
define('CXP_CARRIER_NAME', 'carrier_name');
define('CXP_TRACKING_NUMBER', 'tracking_number');
define('CXP_ID_ORDER', 'id_order');
define('CXP_ORDER_ID_EQ', '&order_id=');
define('CXP_A_HREF', '<a href="');
define('CXP_ORDER', 'order');
define('CXP_ADMIN_INTERNATIONAL_NOTIFICATION', 'CXP_ADMIN_INTERNATIONAL_NOTIFICATION');


class Chilexpress_Oficial extends CarrierModule // NOSONAR
{
    public $id_carrier;

    protected $_errors = array();

    public function __construct()
    {
        $this->name = 'chilexpress_oficial';
        $this->tab = 'shipping_logistics';
        $this->version = '1.2.4';
        $this->author = 'Chilexpress';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_); // NOSONAR
        $this->limited_countries = array('cl');
        $this->bootstrap = true;

        $this->carriers_list = array(
            'CHILEXPRESS_OFCL_DHS'  => 'EXPRESS',
            'CHILEXPRESS_OFCL_DHSS' => 'EXTENDIDO',
            'CHILEXPRESS_OFCL_3DH'  => 'EXTREMOS'
        );

        $this->carriers_serviceTypeCode = array(
            'CHILEXPRESS_OFCL_DHS' => 3,
            'CHILEXPRESS_OFCL_DHSS' => 4,
            'CHILEXPRESS_OFCL_3DH' => 5
        );

        $this->api = new Chilexpress_Oficial_API();

        parent::__construct();

        $this->displayName = $this->l('Chilexpress Oficial', $this->name);
        $this->description = $this->l('Agrega soporte oficial para Chilexpress en Prestashop agregando soporte de calculo de costos de envio y generación de OTs.', 'chilexpress_oficial');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', $this->name);
    }


    public function installTab(){
        $id_tab = (int)Tab::getIdFromClassName(CXP_CHILEXPRESS_ORDER);
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = CXP_CHILEXPRESS_ORDER;
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = $this->author;
            }
            $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentShipping');
            $tab->module = $this->name;
            $tab->visible = false;
            return $tab->add();
        } else {
            return true;
        }
    }

    public function uninstallTab(){
        $id_tab = (int)Tab::getIdFromClassName(CXP_CHILEXPRESS_ORDER);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        } else {
            return false;
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()) {
            return false;
        }
        // Execute module install SQL statements
        $sql_file = dirname(__FILE__).'/install/install.sql';
        if (!$this->loadSQLFile($sql_file)) {
            return false;
        }

        return
            $this->checkAndAddChileanStatesOnInstall() &&
            $this->installTab() &&
            $this->installCarriers() &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBackOfficeTop') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('updateCarrier') &&
            $this->registerHook('actionAdminCustomersListingFieldsModifier') &&
            $this->registerHook('actionAdminOrdersListingFieldsModifier') &&

            $this->registerHook('actionOrderGridDefinitionModifier') &&
            $this->registerHook('actionOrderGridDataModifier') &&
            $this->registerHook('actionOrderGridQueryBuilderModifier') &&

            $this->registerHook('displayAdminOrderContentShip') &&
            Configuration::updateValue('chilexpress_oficial_url', 'wlsdMpnDBn8') && 
            Configuration::updateValue(CXP_API_KEY_GEOREFERENCIA_VALUE, '134b01b545bc4fb29a994cddedca9379') && 
            Configuration::updateValue(CXP_API_KEY_GENERACION_OT_VALUE, '0112f48125034f8fa42aef2441773793') && 
            Configuration::updateValue(CXP_API_KEY_COTIZADOR_VALUE, 'fd46aa18a9fe44c6b49626692605a2e8') && 
            Configuration::updateValue(CXP_API_KEY_GEOREFERENCIA_ENABLED, true) && 
            Configuration::updateValue(CXP_API_KEY_GENERACION_OT_ENABLED, true) && 
            Configuration::updateValue(CXP_API_KEY_COTIZADOR_ENABLED, true) && 
            Configuration::updateValue(CXP_AMBIENTE, 'staging') && 
            Configuration::updateValue(CXP_REGION_ORIGEN, 'RM') &&
            Configuration::updateValue(CXP_ARTICULOS_TIENDA, '5') &&
            Configuration::updateValue(CXP_COMUNA_ORIGEN, 'SANT') && 
            Configuration::updateValue(CXP_NUMERO_TCC_ORIGEN, '18578680') && 
            Configuration::updateValue(CXP_RUT_MARKETPLACE_REMITENTE, '96756430') && 
            Configuration::updateValue(CXP_REGION_DEVOLUCION, 'RM') && 
            Configuration::updateValue(CXP_COMUNA_DEVOLUCION, 'STGO');
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (!Module::isEnabled($this->name)) {
            return;
        }   
        
        // Necesitamos activar que modulos en especifico contendran nuestro js
        $controller = Dispatcher::getInstance()->getController();
        if ($controller == CXP_ADMINMODULES || $controller == 'AdminOrders' || $controller == 'adminaddresses') {
            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }
            $this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
        }
    }

    public function uninstall() // NOSONAR
    {
        if (!parent::uninstall())
        {
            return false;
        }

        // Execute module install SQL statements
        $sql_file = dirname(__FILE__).'/install/uninstall.sql';
        if (!$this->loadSQLFile($sql_file))
        {
            return false;
        }

        if (!$this->uninstallTab())
        {
            return false;
        }
        
        return $this->uninstallCarriers();
    }

    public function postProcess()
    {
        
        return parent::postProcess();
    }

    public function displayForm() // NOSONAR
    {        
        $this->checkAndAddChileanStatesOnInstall();
        // < init fields for form array >
        $articulos_query = array();
        $regiones_query = array();
        $comunas_origen_query = array();
        $comunas_devolucion_query = array();
        $regiones = $this->api->obtener_regiones();
        foreach($regiones as $region_id => $region_nombre) {
            $regiones_query[] = array('id' => $region_id, 'name' => $region_nombre);
        }
        $articulos = $this->api->obtener_categorias_articulos();
        foreach($articulos as $art_id => $art_nombre) {
            $articulos_query[] = array('id' => $art_id, 'name' => $art_nombre);
        }
        $ro = Configuration::get(CXP_REGION_ORIGEN);
        $rd = Configuration::get(CXP_REGION_DEVOLUCION);

        $comunas_origen = $this->api->obtener_comunas_desde_region(isset($ro)?$ro:'R1');
        $comunas_devolucion = $this->api->obtener_comunas_desde_region(isset($rd)?$rd:'R1');
        
        foreach($comunas_origen as $comuna_id => $comuna_nombre) {
            $comunas_origen_query[] = array('id' => $comuna_id, 'name' => $comuna_nombre);
        }
        foreach($comunas_devolucion as $comuna_id => $comuna_nombre) {
            $comunas_devolucion_query[] = array('id' => $comuna_id, 'name' => $comuna_nombre);
        }
        
        $fields_form = array(
                array(
                    'form' => array(
                        CXP_ID_FORM => 'modules_chilexpress',
                        CXP_LEGEND => array(
                            CXP_TITLE => $this->l('Habilitación de Módulos')
                        ),
                        CXP_INPUT => array(
                            array(
                                'type' => CXP_SWITCH,
                                CXP_LABEL => $this->trans(
                                    'Habilitar módulo de Georeferencia',
                                    [],
                                    CXP_MODULES_CONTACTFORM_ADMIN
                                ),
                                'desc' => $this->trans(
                                    "Necesitas este módulo para poder obtener información actualizada de Regiones y Comunas, crea tu API KEY en https://developers.wschilexpress.com/product#product=georeference",
                                    [],
                                    CXP_MODULES_CONTACTFORM_ADMIN
                                ),
                                'name' => CXP_API_KEY_GEOREFERENCIA_ENABLED,
                                CXP_IS_BOOL => true,
                                CXP_REQUIRED => true,
                                CXP_VALUES => array(
                                    array(
                                        'id' => CXP_API_KEY_GEOREFERENCIA_ENABLED . '_on',
                                        CXP_VALUE => 1,
                                        CXP_LABEL => $this->trans(CXP_HABILITADO, [], CXP_ADMIN_GLOBAL)
                                    ),
                                    array(
                                        'id' => CXP_API_KEY_GEOREFERENCIA_ENABLED . '_off',
                                        CXP_VALUE => 0,
                                        CXP_LABEL => $this->trans(CXP_DESHABILITADO, [], CXP_ADMIN_GLOBAL)
                                    )
                                )
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('API KEY Georeferencia', array(), CXP_MODULES_BANNER_ADMIN),
                                'desc' => $this->l('Puedes encontrar esta Api Key, bajo el producto Coberturas en tu página de perfil: https://developers.wschilexpress.com/developer'),
                                'name' => CXP_API_KEY_GEOREFERENCIA_VALUE,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => false
                            ),
                            array(
                                'type' => CXP_SWITCH,
                                CXP_LABEL => $this->trans(
                                    'Habilitar módulo de generación de OT',
                                    [],
                                    CXP_MODULES_CONTACTFORM_ADMIN
                                ),
                                'desc' => $this->trans(
                                    "Necesitas este módulo para poder obtener generar Ordenes de Transporte e Imprimir tus etiquetas, crea tu API KEY en https://developers.wschilexpress.com/product#product=transportorders",
                                    [],
                                    CXP_MODULES_CONTACTFORM_ADMIN
                                ),
                                'name' => CXP_API_KEY_GENERACION_OT_ENABLED,
                                CXP_IS_BOOL => true,
                                CXP_REQUIRED => true,
                                CXP_VALUES => array(
                                    array(
                                        'id' => CXP_API_KEY_GENERACION_OT_ENABLED . '_on',
                                        CXP_VALUE => 1,
                                        CXP_LABEL => $this->trans(CXP_HABILITADO, [], CXP_ADMIN_GLOBAL)
                                    ),
                                    array(
                                        'id' => CXP_API_KEY_GENERACION_OT_ENABLED . '_off',
                                        CXP_VALUE => 0,
                                        CXP_LABEL => $this->trans(CXP_DESHABILITADO, [], CXP_ADMIN_GLOBAL)
                                    )
                                )
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('API KEY Órdenes de transporte', array(), CXP_MODULES_BANNER_ADMIN),
                                'desc' => $this->l('Puedes encontrar esta Api Key, bajo el producto Envíos en tu página de perfil: https://developers.wschilexpress.com/developer'),
                                'name' => CXP_API_KEY_GENERACION_OT_VALUE,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => false
                            ),
                            array(
                                'type' => CXP_SWITCH,
                                CXP_LABEL => $this->trans(
                                    'Habilitar módulo de cotización',
                                    [],
                                    CXP_MODULES_CONTACTFORM_ADMIN
                                ),
                                'desc' => $this->trans(
                                    "Necesitas este módulo para poder obtener generar Ordenes de Transporte e Imprimir tus etiquetas, crea tu API KEY en https://developers.wschilexpress.com/product#product=rating",
                                    [],
                                    CXP_MODULES_CONTACTFORM_ADMIN
                                ),
                                'name' => CXP_API_KEY_COTIZADOR_ENABLED,
                                CXP_IS_BOOL => true,
                                CXP_REQUIRED => true,
                                CXP_VALUES => array(
                                    array(
                                        'id' => CXP_API_KEY_COTIZADOR_ENABLED . '_on',
                                        CXP_VALUE => 1,
                                        CXP_LABEL => $this->trans(CXP_HABILITADO, [], CXP_ADMIN_GLOBAL)
                                    ),
                                    array(
                                        'id' => CXP_API_KEY_COTIZADOR_ENABLED . '_off',
                                        CXP_VALUE => 0,
                                        CXP_LABEL => $this->trans(CXP_DESHABILITADO, [], CXP_ADMIN_GLOBAL)
                                    )
                                )
                            ),
                             array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('API KEY Módulo de Cotización', array(), CXP_MODULES_BANNER_ADMIN),
                                'desc' => $this->l('Puedes encontrar esta Api Key, bajo el producto Cotizador en tu página de perfil: https://developers.wschilexpress.com/developer'),
                                'name' => CXP_API_KEY_COTIZADOR_VALUE,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => false
                            ),
                            array(
                                CXP_LABEL => $this->trans('Ambiente', array(), 'Modules.Dashactivity.Admin'),
                                'desc' => $this->l('Elige el ambiente de Staging para hacer las pruebas con tu plugin, y el ambiente de production una vez estas seguro(a) que todo funciona correctamente.'),
                                'name' => CXP_AMBIENTE,
                                'type' => CXP_SELECT,
                                CXP_OPTIONS => array(
                                    CXP_QUERY => array(
                                        array('id' => 'staging', 'name' => 'Staging'),
                                        array('id' => 'production', 'name' => 'Production'),
                                    ),
                                    'id' => 'id',
                                    'name' => 'name',
                                ),
                            )
                           
                        ),
                        CXP_SUBMIT => array(
                            CXP_TITLE => $this->l(CXP_GUARDAR),
                            CXP_CLASS => CXP_BTN_BTN_DEFAULT_PULL_RIGHT
                        )
                    )
                ),

                array(
                    'form' => array(
                        CXP_ID_FORM => 'datos_tienda_chilexpress',
                        CXP_LEGEND => array(
                            CXP_TITLE => $this->l('Datos de la tienda')
                        ),
                        CXP_INPUT => array(
                            array(
                                'type' => CXP_SELECT,
                                CXP_OPTIONS => array(
                                    CXP_QUERY =>$articulos_query,
                                    'id' => 'id',
                                    'name' => 'name',
                                ),
                                CXP_LABEL => $this->trans('Descripción de articulos de la tienda', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_ARTICULOS_TIENDA,
                                'lang' => false,
                                CXP_REQUIRED => true
                            ),
                        ),
                        CXP_SUBMIT => array(
                            CXP_TITLE => $this->l(CXP_GUARDAR),
                            CXP_CLASS => CXP_BTN_BTN_DEFAULT_PULL_RIGHT
                        )
                    )
                ),
                array(
                    'form' => array(
                        CXP_ID_FORM => 'general_chilexpress',
                        CXP_LEGEND => array(
                            CXP_TITLE => $this->l('Datos de Origen')
                        ),
                        CXP_INPUT => array(
                            array(
                                'type' => CXP_SELECT,
                                CXP_OPTIONS => array(
                                    CXP_QUERY =>$regiones_query,
                                    'id' => 'id',
                                    'name' => 'name',
                                ),
                                CXP_LABEL => $this->trans('Región de Origen', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_REGION_ORIGEN,
                                'lang' => false,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => CXP_SELECT,
                                CXP_OPTIONS => array(
                                    CXP_QUERY => $comunas_origen_query,
                                    'id' => 'id',
                                    'name' => 'name',
                                ),
                                CXP_LABEL => $this->trans('Código de comuna de origen', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_COMUNA_ORIGEN,
                                'lang' => false,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('Número TCC', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_NUMERO_TCC_ORIGEN,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            )
                        ),
                        CXP_SUBMIT => array(
                            CXP_TITLE => $this->l(CXP_GUARDAR),
                            CXP_CLASS => CXP_BTN_BTN_DEFAULT_PULL_RIGHT
                        )
                    )
                ),
                array(
                    'form' => array(
                        CXP_ID_FORM => 'general_chilexpress2',
                        CXP_LEGEND => array(
                            CXP_TITLE => $this->l('Datos del Remitente')
                        ),
                        CXP_INPUT => array(
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('Nombre', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_NOMBRE_REMITENTE,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('Teléfono', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_TELEFONO_REMITENTE,
                                'lang' => false,
                                'size' => 6,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                'prefix'=>'<i class="icon-envelope-o"></i>',
                                CXP_LABEL => $this->trans('E-mail', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_EMAIL_REMITENTE,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('RUT Seller', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_RUT_SELLER_REMITENTE,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('RUT Marketplace', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_RUT_MARKETPLACE_REMITENTE,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            )
                        ),
                        CXP_SUBMIT => array(
                            CXP_TITLE => $this->l(CXP_GUARDAR),
                            CXP_CLASS => CXP_BTN_BTN_DEFAULT_PULL_RIGHT
                        )
                    )
                ),
                array(
                    'form' => array(
                        CXP_ID_FORM => 'general_chilexpress3',
                        CXP_LEGEND => array(
                            CXP_TITLE => $this->l('Dirección de Devolución')
                        ),
                        CXP_INPUT => array(
                            array(
                                'type' => CXP_SELECT,
                                CXP_OPTIONS => array(
                                    CXP_QUERY => $regiones_query,
                                    'id' => 'id',
                                    'name' => 'name',
                                ),
                                CXP_LABEL => $this->trans('Región de Devolución', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_REGION_DEVOLUCION,
                                'lang' => false,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => CXP_SELECT,
                                CXP_OPTIONS => array(
                                    CXP_QUERY => $comunas_devolucion_query,
                                    'id' => 'id',
                                    'name' => 'name',
                                ),
                                CXP_LABEL => $this->trans('Código Comuna', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_COMUNA_DEVOLUCION,
                                'lang' => false,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('Calle Devolución', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_CALLE_DEVOLUCION,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('Número de la Dirección', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_NUMERO_CALLE_DEVOLUCION,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            ),
                            array(
                                'type' => 'text',
                                CXP_LABEL => $this->trans('Complemento', array(), CXP_MODULES_BANNER_ADMIN),
                                'name' => CXP_COMPLEMENTO_DEVOLUCION,
                                'lang' => false,
                                'size' => 20,
                                CXP_REQUIRED => true
                            )
                        ),
                        CXP_SUBMIT => array(
                            CXP_TITLE => $this->l(CXP_GUARDAR),
                            CXP_CLASS => CXP_BTN_BTN_DEFAULT_PULL_RIGHT
                        )
                    )
                )
        );

        // < load helperForm >
        $helper = new HelperForm();

        // < module, token and currentIndex >
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite(CXP_ADMINMODULES);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // < title and toolbar >
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = false;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = CXP_SUBMIT.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite(CXP_ADMINMODULES),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite(CXP_ADMINMODULES),
                'desc' => $this->l('Back to list')
            )
        );

        // < load current value >
        $helper->fields_value[CXP_API_KEY_GEOREFERENCIA_VALUE] = Configuration::get(CXP_API_KEY_GEOREFERENCIA_VALUE);
        $helper->fields_value[CXP_API_KEY_GENERACION_OT_VALUE] = Configuration::get(CXP_API_KEY_GENERACION_OT_VALUE);
        $helper->fields_value[CXP_API_KEY_COTIZADOR_VALUE] = Configuration::get(CXP_API_KEY_COTIZADOR_VALUE);
        
        $helper->fields_value[CXP_API_KEY_GEOREFERENCIA_ENABLED] = Configuration::get(CXP_API_KEY_GEOREFERENCIA_ENABLED);
        $helper->fields_value[CXP_API_KEY_GENERACION_OT_ENABLED] = Configuration::get(CXP_API_KEY_GENERACION_OT_ENABLED);
        $helper->fields_value[CXP_API_KEY_COTIZADOR_ENABLED] = Configuration::get(CXP_API_KEY_COTIZADOR_ENABLED);
        $helper->fields_value[CXP_AMBIENTE] = Configuration::get(CXP_AMBIENTE);

        $helper->fields_value[CXP_ARTICULOS_TIENDA] = Configuration::get(CXP_ARTICULOS_TIENDA);

        $helper->fields_value[CXP_REGION_ORIGEN] = Configuration::get(CXP_REGION_ORIGEN);
        $helper->fields_value[CXP_COMUNA_ORIGEN] = Configuration::get(CXP_COMUNA_ORIGEN);
        $helper->fields_value[CXP_NUMERO_TCC_ORIGEN] = Configuration::get(CXP_NUMERO_TCC_ORIGEN);

        $helper->fields_value[CXP_NOMBRE_REMITENTE] = Configuration::get(CXP_NOMBRE_REMITENTE);
        $helper->fields_value[CXP_TELEFONO_REMITENTE] = Configuration::get(CXP_TELEFONO_REMITENTE);
        $helper->fields_value[CXP_EMAIL_REMITENTE] = Configuration::get(CXP_EMAIL_REMITENTE);
        $helper->fields_value[CXP_RUT_SELLER_REMITENTE] = Configuration::get(CXP_RUT_SELLER_REMITENTE);
        $helper->fields_value[CXP_RUT_MARKETPLACE_REMITENTE] = Configuration::get(CXP_RUT_MARKETPLACE_REMITENTE);

        $helper->fields_value[CXP_REGION_DEVOLUCION] = Configuration::get(CXP_REGION_DEVOLUCION);
        $helper->fields_value[CXP_COMUNA_DEVOLUCION] = Configuration::get(CXP_COMUNA_DEVOLUCION);
        $helper->fields_value[CXP_CALLE_DEVOLUCION] = Configuration::get(CXP_CALLE_DEVOLUCION);
        $helper->fields_value[CXP_NUMERO_CALLE_DEVOLUCION] = Configuration::get(CXP_NUMERO_CALLE_DEVOLUCION);
        $helper->fields_value[CXP_COMPLEMENTO_DEVOLUCION] = Configuration::get(CXP_COMPLEMENTO_DEVOLUCION);

        return $helper->generateForm($fields_form);
        
    }

    public function getContent() // NOSONAR
    {
        $output = null;

        // < here we check if the form is submited for this module >
        if (Tools::isSubmit(CXP_SUBMIT.$this->name)) {
            $chilexpress_url = strval(Tools::getValue('chilexpress_oficial_url'));

            $api_key_georeferencia_value = strval(Tools::getValue(CXP_API_KEY_GEOREFERENCIA_VALUE));
            $api_key_generacion_ot_value = strval(Tools::getValue(CXP_API_KEY_GENERACION_OT_VALUE));
            $api_key_cotizador_value = strval(Tools::getValue(CXP_API_KEY_COTIZADOR_VALUE));

            $api_key_georeferencia_enabled = boolval(Tools::getValue(CXP_API_KEY_GEOREFERENCIA_ENABLED));
            $api_key_generacion_ot_enabled = boolval(Tools::getValue(CXP_API_KEY_GENERACION_OT_ENABLED));
            $api_key_cotizador_enabled = boolval(Tools::getValue(CXP_API_KEY_COTIZADOR_ENABLED));

            $ambiente = strval(Tools::getValue(CXP_AMBIENTE));

            $articulos_tienda = strval(Tools::getValue(CXP_ARTICULOS_TIENDA));

            $region_origen = strval(Tools::getValue(CXP_REGION_ORIGEN));
            $comuna_origen = strval(Tools::getValue(CXP_COMUNA_ORIGEN));
            $numero_tcc_origen = strval(Tools::getValue(CXP_NUMERO_TCC_ORIGEN));

            $nombre_remitente = strval(Tools::getValue(CXP_NOMBRE_REMITENTE));
            $telefono_remitente = strval(Tools::getValue(CXP_TELEFONO_REMITENTE));
            $email_remitente = strval(Tools::getValue(CXP_EMAIL_REMITENTE));
            $rut_seller_remitente = strval(Tools::getValue(CXP_RUT_SELLER_REMITENTE));
            $rut_marketplace_remitente = strval(Tools::getValue(CXP_RUT_MARKETPLACE_REMITENTE));

            $region_devolucion = strval(Tools::getValue(CXP_REGION_DEVOLUCION));
            $comuna_devolucion = strval(Tools::getValue(CXP_COMUNA_DEVOLUCION));
            $calle_devolucion = strval(Tools::getValue(CXP_CALLE_DEVOLUCION));
            $numero_calle_devolucion = strval(Tools::getValue(CXP_NUMERO_CALLE_DEVOLUCION));
            $complemento_devolucion = strval(Tools::getValue(CXP_COMPLEMENTO_DEVOLUCION));


            if (isset($api_key_georeferencia_value)) {
                Configuration::updateValue(CXP_API_KEY_GEOREFERENCIA_VALUE, $api_key_georeferencia_value);
            }
            if (isset($api_key_generacion_ot_value)) {
                Configuration::updateValue(CXP_API_KEY_GENERACION_OT_VALUE, $api_key_generacion_ot_value);
            }
            if (isset($api_key_cotizador_value)) {
                Configuration::updateValue(CXP_API_KEY_COTIZADOR_VALUE, $api_key_cotizador_value);
            }

            if (isset($api_key_georeferencia_enabled)) {
                Configuration::updateValue(CXP_API_KEY_GEOREFERENCIA_ENABLED, $api_key_georeferencia_enabled);
            }
            if (isset($api_key_generacion_ot_enabled)) {
                Configuration::updateValue(CXP_API_KEY_GENERACION_OT_ENABLED, $api_key_generacion_ot_enabled);
            }
            if (isset($api_key_cotizador_enabled)) {
                Configuration::updateValue(CXP_API_KEY_COTIZADOR_ENABLED, $api_key_cotizador_enabled);
            }
            if (isset($ambiente)) {
                Configuration::updateValue(CXP_AMBIENTE, $ambiente);
            }

            if (isset($articulos_tienda)) {
                Configuration::updateValue(CXP_ARTICULOS_TIENDA, $articulos_tienda);
            }
            ///
            if (isset($region_origen)) {
                Configuration::updateValue(CXP_REGION_ORIGEN, $region_origen);
            }
            if (isset($comuna_origen)) {
                Configuration::updateValue(CXP_COMUNA_ORIGEN, $comuna_origen);
            }
            if (isset($numero_tcc_origen)) {
                Configuration::updateValue(CXP_NUMERO_TCC_ORIGEN, $numero_tcc_origen);
            }
            ///
            if (isset($nombre_remitente)) {
                Configuration::updateValue(CXP_NOMBRE_REMITENTE, $nombre_remitente);
            }
            if (isset($telefono_remitente)) {
                Configuration::updateValue(CXP_TELEFONO_REMITENTE, $telefono_remitente);
            }
            if (isset($email_remitente)) {
                Configuration::updateValue(CXP_EMAIL_REMITENTE, $email_remitente);
            }
            if (isset($rut_seller_remitente)) {
                Configuration::updateValue(CXP_RUT_SELLER_REMITENTE, $rut_seller_remitente);
            }
            if (isset($rut_marketplace_remitente)) {
                Configuration::updateValue(CXP_RUT_MARKETPLACE_REMITENTE, $rut_marketplace_remitente);
            }

            ///
            if (isset($nombre_remitente)) {
                Configuration::updateValue(CXP_REGION_DEVOLUCION, $region_devolucion);
            }
            if (isset($telefono_remitente)) {
                Configuration::updateValue(CXP_COMUNA_DEVOLUCION, $comuna_devolucion);
            }
            if (isset($calle_devolucion)) {
                Configuration::updateValue(CXP_CALLE_DEVOLUCION, $calle_devolucion);
            }
            if (isset($numero_calle_devolucion)) {
                Configuration::updateValue(CXP_NUMERO_CALLE_DEVOLUCION, $numero_calle_devolucion);
            }
            if (isset($complemento_devolucion)) {
                Configuration::updateValue(CXP_COMPLEMENTO_DEVOLUCION, $complemento_devolucion);
            }

            $output .= $this->displayConfirmation($this->l('Datos Actualizados con éxito'));
        }
        return $output.$this->displayForm();
    }

    public function uninstallCarriers() // NOSONAR
    {
        $carrier_query = new DbQuery();
        $carrier_query->select(CXP_ID_CARRIER);
        $carrier_query->from(CXP_CARRIER);
        $carrier_query->where("name = 'Chilexpress'");
        
        $carriers_id_query = Db::getInstance()->executeS($carrier_query);
        $carriers_ids = [];

        foreach($carriers_id_query as $carrier_id)
        {
            $carriers_ids[] = $carrier_id[CXP_ID_CARRIER];
        }

        $carriers_ids = implode(",", $carriers_ids);

        Db::getInstance()->delete(CXP_CARRIER, 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('carrier_group', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('carrier_lang', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('carrier_shop', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('carrier_tax_rules_group_shop', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('carrier_zone', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('cart', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('cart_rule_carrier', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete(CXP_DELIVERY, 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('range_price', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('range_weight', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('warehouse_carrier', 'id_carrier IN (' . $carriers_ids . ')'); // NOSONAR
        Db::getInstance()->delete('configuration', 'name LIKE "%CHILEXPRESS_OFCL%"'); // NOSONAR

        return true;
    }

    public function installCarriers() // NOSONAR
    {   
        foreach ($this->carriers_list as $carrier_key => $carrier_name)
        {
            if (Configuration::get($carrier_key) < 1)
            {
                // Create new carrier
                $carrier = new Carrier();
                $carrier->name = $this->author;
                $carrier->id_tax_rules_group = 0;
                $carrier->active = 1;
                $carrier->deleted = 0;
                foreach (Language::getLanguages(true) as $language){
                    $carrier->delay[(int)$language['id_lang']] = $carrier_name;
                }
                $carrier->shipping_handling = false;
                $carrier->range_behavior = 0;
                $carrier->is_module = true;
                $carrier->shipping_external = true;
                $carrier->external_module_name = $this->name;
                $carrier->need_range = true;
                if (!$carrier->add()) {
                    return false;
                }

                // Associate carrier to all groups
                $groups = Group::getGroups(true);
                foreach ($groups as $group) {
                    Db::getInstance()->insert('carrier_group', array(CXP_ID_CARRIER => (int)$carrier->id, 'id_group' => (int)$group['id_group']));
                }

                // Create price range
                $rangePrice = new RangePrice();
                $rangePrice->id_carrier = $carrier->id;
                $rangePrice->delimiter1 = '0';
                $rangePrice->delimiter2 = '10000000';
                $rangePrice->add();

                // Create weight range
                $rangeWeight = new RangeWeight();
                $rangeWeight->id_carrier = $carrier->id;
                $rangeWeight->delimiter1 = '0';
                $rangeWeight->delimiter2 = '10000000';
                $rangeWeight->add();

                // Associate carrier to all zones
                $zones = Zone::getZones(true);
                foreach ($zones as $zone)
                {
                    Db::getInstance()->insert('carrier_zone', array(CXP_ID_CARRIER => (int)$carrier->id, CXP_ZONE => (int)$zone[CXP_ZONE]));
                    Db::getInstance()->insert(CXP_DELIVERY, array(CXP_ID_CARRIER => (int)$carrier->id, 'id_range_price' => (int)$rangePrice->id, 'id_range_weight' => NULL, CXP_ZONE => (int)$zone[CXP_ZONE], 'price' => '0'));
                    Db::getInstance()->insert(CXP_DELIVERY, array(CXP_ID_CARRIER => (int)$carrier->id, 'id_range_price' => NULL, 'id_range_weight' => (int)$rangeWeight->id, CXP_ZONE => (int)$zone[CXP_ZONE], 'price' => '0'));
                }

                // Copy the carrier logo
                copy(dirname(__FILE__).'/logo_medio_transporte.png', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg');

                // Save the carrier ID in the Configuration table
                Configuration::updateValue($carrier_key, $carrier->id);
            }
            else
            {
                $carrier_query = new DbQuery();
                $carrier_query->from('carrier_lang');
                $carrier_query->where("delay = '". $carrier_name ."'");
                
                $carrier = Db::getInstance()->executeS($carrier_query);
                
                $update = Db::getInstance()->update(CXP_CARRIER, array('deleted' => 0), 'id_carrier = '. (int) $carrier[0][CXP_ID_CARRIER]);
                
                // Copy the carrier logo
                copy(dirname(__FILE__).'/logo_medio_transporte.png', _PS_SHIP_IMG_DIR_.'/'.(int) $carrier[0][CXP_ID_CARRIER].'.jpg');
            }
        }

        return true;
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        $this->context->smarty->assign(
              array(
                  'chilexpress_oficial_ajax_url' => $this->context->link->getModuleLink( $this->name, 'ajax', array()),
              ));
        return $this->display(__FILE__, $this->name .'.tpl');
    }

    public function hookDisplayBackOfficeTop($params)
    {
        
        return $this->display(__FILE__, $this->name .'_footer'.'.tpl');
    }

    public function hookdisplayHeader($params)
    {
        // 
        $this->context->smarty->assign(
              array(
                  'chilexpress_oficial_ajax_url' => $this->context->link->getModuleLink( $this->name , 'ajax', array()),
              ));
        
        $this->context->controller->registerJavascript('chilexpress-official-front-script', 'modules/chilexpress_oficial/views/js/chilexpress_oficial_front.js', ['position' => 'bottom', 'priority' => 10]);
        return $this->display(__FILE__, $this->name .'_front.tpl');
    }

    public function hookUpdateCarrier($params)
    {
        $id_carrier_old = (int)($params[CXP_ID_CARRIER]);
        $id_carrier_new = (int)($params[CXP_CARRIER]->id);
        foreach ($this->carriers_list as $carrier_key => $carrier_name) {
            $carrier_id = Configuration::get($carrier_key);
            if ($id_carrier_old == $carrier_id) {
                Configuration::updateValue($carrier_key, $id_carrier_new);
            }
        }
        $payment_post_data = unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT_DATA'));
        if(isset($payment_post_data['delivery_method'][$id_carrier_old])){
            $payment_post_data['delivery_method'][$id_carrier_new] = $payment_post_data['delivery_method'][$id_carrier_old];
            unset($payment_post_data['delivery_method'][$id_carrier_old]);
        }
        //print("<pre>".print_r($carriers,true)."</pre>");die;
        Configuration::updateValue('VELOCITY_SUPERCHECKOUT_DATA', serialize($payment_post_data));        
    }
    
    public function getPackageShippingCost($params, $shipping_fees, $products)
    {
        return false;
        // var_dump($params);
        // echo "<br>------------------<br>";
        $currency = new Currency( $params->id_currency );
        if( $currency->iso_code !== 'CLP' )
          return false;
        
        $result = Db::getInstance()->delete('chilexpress_oficial_apicache', 'created < (NOW() - INTERVAL 24 HOUR)');

        $carrier = new Carrier($this->id_carrier);
        $current_carrier_key = false;
        $current_carrier_name = '';
        $current_carrier_id = 0;

        // var_dump($this->carriers_list);
        foreach ($this->carriers_list as $carrier_key => $carrier_name)
        {
            $carrier_id = Configuration::get($carrier_key);
            // echo "carrier_id ".$carrier_id." == ";
            // echo "id_carrier ".$this->id_carrier." == ";
            // echo "id_reference ".$carrier->id_reference."<br>";
            // if ($carrier_id == $carrier->id_reference)
            if ($carrier_id == $this->id_carrier)
            {
                // echo "aqi".$carrier_id.$carrier->id_reference;
                $current_carrier_key = ($carrier_key);
                $current_carrier_name = ($carrier_name);
                $current_carrier_id = ($carrier_id);
            }
            // echo "current_carrier_key ".$current_carrier_key." == ";
            // echo "carrier_key ".$carrier_key." <br> ";
        }
         
        if (!$current_carrier_key) {
            // echo "aqyui1-->";
            return false;
        }
        // echo "current_carrier_key2 ".$current_carrier_key."<br>";
        // echo "<br>------------------<br>";
        $address = new Address($params->id_address_delivery);
        $pais = $address->country;

        if( $pais !== 'Chile' ){
            // echo "aqyui1";
          return false;
        }

        $comuna_code = $address->city; 
        // $products = $params->getProducts(true);

        $products_required_fields = array();
        $total = 0;
        $total_weight = 0;

        $biggest_product = false;
        $biggest_size = 0;
        $product_quantity = array();

        foreach($products as $product) {

            $products_required_fields[] = array(
                "id" => $product[CXP_ID_PRODUCT],
                CXP_CART_QUANTITY => $product[CXP_CART_QUANTITY],
                CXP_WIDTH => $product[CXP_WIDTH],
                CXP_HEIGHT => $product[CXP_HEIGHT],
                CXP_DEPTH => $product[CXP_DEPTH],
                CXP_WEIGHT => $product[CXP_WEIGHT],
                CXP_TOTAL => $product[CXP_TOTAL]
            );

            $product_quantity[] = $product[CXP_ID_PRODUCT] . ":" . $product[CXP_CART_QUANTITY];
            if (
                $product[CXP_WIDTH]!="" && $product[CXP_HEIGHT]!="" && $product[CXP_DEPTH]!="" &&
                ($product[CXP_WIDTH]*$product[CXP_HEIGHT]*$product[CXP_DEPTH] > $biggest_size)
                ) {
                  
                $biggest_size = $product[CXP_WIDTH]*$product[CXP_HEIGHT]*$product[CXP_DEPTH];
                $biggest_product = $product;
                
            }

            $total_weight += $product[CXP_WEIGHT] * $product[CXP_CART_QUANTITY];
            $total += $product[CXP_TOTAL];
        }


        $conf = array(
            CXP_COMUNA_ORIGEN => Configuration::get(CXP_COMUNA_ORIGEN),
            CXP_COMUNA_DESTINO => $comuna_code,
            CXP_TOTAL => $total,
            "productos" => $products_required_fields
        );

        if ($biggest_product) { $conf[CXP_WIDTH] = $biggest_product[CXP_WIDTH]; }
        else { $conf[CXP_WIDTH] = 1; }
        if ($biggest_product) { $conf[CXP_HEIGHT] = $biggest_product[CXP_HEIGHT]; }
        else { $conf[CXP_HEIGHT] = 1; }
        if ($biggest_product) { $conf[CXP_DEPTH] = $biggest_product[CXP_DEPTH]; }
        else { $conf[CXP_DEPTH] = 1; }

        $sql = '
        SELECT * FROM 
            ' . _DB_PREFIX_ . 'chilexpress_oficial_apicache
        WHERE
            carrier_key = "'.$current_carrier_key.'" AND 
            comuna_origen = "'.$conf[CXP_COMUNA_ORIGEN].'" AND 
            comuna_destino = "'.$conf[CXP_COMUNA_DESTINO].'" AND 
            cart_id = "'.$params->id.'" AND 
            costo_total = "'.$conf[CXP_TOTAL].'" AND 
            product_quantity = "'.implode(",",$product_quantity).'" 
        ORDER BY created DESC';
        // echo $sql."<br>";
        $row = Db::getInstance()->getRow($sql);

        // echo "current_carrier_key2 -->".$current_carrier_key."<br>";
        // echo "<br>------------------<br>";
        if ($row) {
            // echo "aqiui00";
            if (!$row["service_value"]) {
                 // echo "aqiui00";
                return false;
            }
            // echo intval($row["service_value"]) + $shipping_fees."<br>";
            return  intval($row["service_value"]) + $shipping_fees;
        }
        /*var_dump($conf[CXP_COMUNA_ORIGEN]);
        var_dump($conf[CXP_COMUNA_DESTINO]);
        var_dump($total_weight);
        var_dump($conf[CXP_WIDTH]);
        var_dump($conf[CXP_HEIGHT]);
        var_dump($conf[CXP_DEPTH]);
        var_dump($conf[CXP_TOTAL]);*/
        $jresult = $this->api->obtener_cotizacion($conf[CXP_COMUNA_ORIGEN], $conf[CXP_COMUNA_DESTINO], $total_weight,  $conf[CXP_WIDTH],  $conf[CXP_HEIGHT],  $conf[CXP_DEPTH], $conf[CXP_TOTAL]);
        /*var_dump($jresult);*/
        $api_cache = new ChilexpressOficialApiCache();
        $api_cache->carrier_key = $current_carrier_key;
        $api_cache->comuna_origen = $conf[CXP_COMUNA_ORIGEN];
        $api_cache->comuna_destino = $conf[CXP_COMUNA_DESTINO];
        $api_cache->cart_id = $params->id ;
        $api_cache->costo_total = $conf[CXP_TOTAL];
        $api_cache->product_quantity = implode(",",$product_quantity);
        $api_cache->created = date('Y-m-d H:i:s');
        $api_cache->service_value = 0;

        $serviceValue = false;
        if (!$jresult){
            $api_cache->add();
            return false;
        } else {
            $courier_service_options = [];
            foreach ($jresult["data"]["courierServiceOptions"] as $option) {
                if ($option[CXP_SERVICETYPECODE] >= 3) {
                    $courier_service_options[] = $option;
                }
            }
            if (isset($courier_service_options[0]) && $courier_service_options[0][CXP_SERVICETYPECODE] == $this->carriers_serviceTypeCode[$current_carrier_key]) {
                $serviceValue = intval($courier_service_options[0][CXP_SERVICEVALUE]);
            }
        }
        $api_cache->service_value = $serviceValue;
        $api_cache->add();

        if (!$serviceValue) {
            return false;
        }

        return $serviceValue + $shipping_fees;
    }
    /** shipping related functions **/
    // This function is calles as many times as many carriers we'va got registered in the shop
    public function getOrderShippingCost($params, $shipping_fees) // NOSONAR
    {
        return false;
        // var_dump($params);
        // echo "<br>------------------<br>";
        $currency = new Currency( $params->id_currency );
        if( $currency->iso_code !== 'CLP' )
          return false;
        
        $result = Db::getInstance()->delete('chilexpress_oficial_apicache', 'created < (NOW() - INTERVAL 24 HOUR)');

        $carrier = new Carrier($this->id_carrier);
        $current_carrier_key = false;
        $current_carrier_name = '';
        $current_carrier_id = 0;

        // var_dump($this->carriers_list);
        foreach ($this->carriers_list as $carrier_key => $carrier_name)
        {
            $carrier_id = Configuration::get($carrier_key);
            // echo "carrier_id ".$carrier_id." == ";
            // echo "id_carrier ".$this->id_carrier." == ";
            // echo "id_reference ".$carrier->id_reference."<br>";
            // if ($carrier_id == $carrier->id_reference)
            if ($carrier_id == $this->id_carrier)
            {
                // echo "aqi".$carrier_id.$carrier->id_reference;
                $current_carrier_key = ($carrier_key);
                $current_carrier_name = ($carrier_name);
                $current_carrier_id = ($carrier_id);
            }
            // echo "current_carrier_key ".$current_carrier_key." == ";
            // echo "carrier_key ".$carrier_key." <br> ";
        }
         
        if (!$current_carrier_key) {
            // echo "aqyui1-->";
            return false;
        }
        // echo "current_carrier_key2 ".$current_carrier_key."<br>";
        // echo "<br>------------------<br>";
        $address = new Address($params->id_address_delivery);
        $pais = $address->country;

        if( $pais !== 'Chile' ){
            // echo "aqyui1";
          return false;
        }

        $comuna_code = $address->city; 
        $products = $params->getProducts(true);

        $products_required_fields = array();
        $total = 0;
        $total_weight = 0;

        $biggest_product = false;
        $biggest_size = 0;
        $product_quantity = array();

        foreach($products as $product) {

            $products_required_fields[] = array(
                "id" => $product[CXP_ID_PRODUCT],
                CXP_CART_QUANTITY => $product[CXP_CART_QUANTITY],
                CXP_WIDTH => $product[CXP_WIDTH],
                CXP_HEIGHT => $product[CXP_HEIGHT],
                CXP_DEPTH => $product[CXP_DEPTH],
                CXP_WEIGHT => $product[CXP_WEIGHT],
                CXP_TOTAL => $product[CXP_TOTAL]
            );

            $product_quantity[] = $product[CXP_ID_PRODUCT] . ":" . $product[CXP_CART_QUANTITY];
            if (
                $product[CXP_WIDTH]!="" && $product[CXP_HEIGHT]!="" && $product[CXP_DEPTH]!="" &&
                ($product[CXP_WIDTH]*$product[CXP_HEIGHT]*$product[CXP_DEPTH] > $biggest_size)
                ) {
                  
                $biggest_size = $product[CXP_WIDTH]*$product[CXP_HEIGHT]*$product[CXP_DEPTH];
                $biggest_product = $product;
                
            }

            $total_weight += $product[CXP_WEIGHT] * $product[CXP_CART_QUANTITY];
            $total += $product[CXP_TOTAL];
        }


        $conf = array(
            CXP_COMUNA_ORIGEN => Configuration::get(CXP_COMUNA_ORIGEN),
            CXP_COMUNA_DESTINO => $comuna_code,
            CXP_TOTAL => $total,
            "productos" => $products_required_fields
        );

        if ($biggest_product) { $conf[CXP_WIDTH] = $biggest_product[CXP_WIDTH]; }
        else { $conf[CXP_WIDTH] = 1; }
        if ($biggest_product) { $conf[CXP_HEIGHT] = $biggest_product[CXP_HEIGHT]; }
        else { $conf[CXP_HEIGHT] = 1; }
        if ($biggest_product) { $conf[CXP_DEPTH] = $biggest_product[CXP_DEPTH]; }
        else { $conf[CXP_DEPTH] = 1; }

        $sql = '
        SELECT * FROM 
            ' . _DB_PREFIX_ . 'chilexpress_oficial_apicache
        WHERE
            carrier_key = "'.$current_carrier_key.'" AND 
            comuna_origen = "'.$conf[CXP_COMUNA_ORIGEN].'" AND 
            comuna_destino = "'.$conf[CXP_COMUNA_DESTINO].'" AND 
            cart_id = "'.$params->id.'" AND 
            costo_total = "'.$conf[CXP_TOTAL].'" AND 
            product_quantity = "'.implode(",",$product_quantity).'" 
        ORDER BY created DESC';
        $row = Db::getInstance()->getRow($sql);

        // echo "current_carrier_key2 -->".$current_carrier_key."<br>";
        // echo "<br>------------------<br>";
        if ($row) {
            // echo "aqiui00";
            if (!$row["service_value"]) {
                 // echo "aqiui00";
                return false;
            }
            // echo intval($row["service_value"]) + $shipping_fees."<br>";
            return  intval($row["service_value"]) + $shipping_fees;
        }
        /*var_dump($conf[CXP_COMUNA_ORIGEN]);
        var_dump($conf[CXP_COMUNA_DESTINO]);
        var_dump($total_weight);
        var_dump($conf[CXP_WIDTH]);
        var_dump($conf[CXP_HEIGHT]);
        var_dump($conf[CXP_DEPTH]);
        var_dump($conf[CXP_TOTAL]);*/
        $jresult = $this->api->obtener_cotizacion($conf[CXP_COMUNA_ORIGEN], $conf[CXP_COMUNA_DESTINO], $total_weight,  $conf[CXP_WIDTH],  $conf[CXP_HEIGHT],  $conf[CXP_DEPTH], $conf[CXP_TOTAL]);
        /*var_dump($jresult);*/
        $api_cache = new ChilexpressOficialApiCache();
        $api_cache->carrier_key = $current_carrier_key;
        $api_cache->comuna_origen = $conf[CXP_COMUNA_ORIGEN];
        $api_cache->comuna_destino = $conf[CXP_COMUNA_DESTINO];
        $api_cache->cart_id = $params->id ;
        $api_cache->costo_total = $conf[CXP_TOTAL];
        $api_cache->product_quantity = implode(",",$product_quantity);
        $api_cache->created = date('Y-m-d H:i:s');
        $api_cache->service_value = 0;

        $serviceValue = false;
        if (!$jresult){
            $api_cache->add();
            return false;
        } else {
            $courier_service_options = [];
            foreach ($jresult["data"]["courierServiceOptions"] as $option) {
                if ($option[CXP_SERVICETYPECODE] >= 3) {
                    $courier_service_options[] = $option;
                }
            }
            if (isset($courier_service_options[0]) && $courier_service_options[0][CXP_SERVICETYPECODE] == $this->carriers_serviceTypeCode[$current_carrier_key]) {
                $serviceValue = intval($courier_service_options[0][CXP_SERVICEVALUE]);
            }
        }
        $api_cache->service_value = $serviceValue;
        $api_cache->add();

        if (!$serviceValue) {
            return false;
        }

        return $serviceValue + $shipping_fees;
    }

    public function getOrderShippingCostExternal($params) // NOSONAR
    {
        $currency = new Currency( $params->id_currency );
        if( $currency->iso_code !== 'CLP' )
          return false;

          $carrier = new Carrier($this->id_carrier);
          $current_carrier_key = false;
          $current_carrier_name = '';
          $current_carrier_id = 0;

          foreach ($this->carriers_list as $carrier_key => $carrier_name) {
              $carrier_id = Configuration::get($carrier_key);
              if ($carrier_id == $this->id_carrier) {
                $current_carrier_key = ($carrier_key);
                $current_carrier_name = ($carrier_name);
                $current_carrier_id = ($carrier_id);
              }
          }

          if (!$current_carrier_key) {
            return false;
          }

          $address = new Address($params->id_address_delivery);
          $pais = $address->country;

          if( $pais !== 'Chile' )
          return false;

          $comuna_code = $address->city; 
          $products = $params->getProducts(true);

          $products_required_fields = array();
          $total = 0;
          $total_weight = 0;
            
          $biggest_product = false;
          $biggest_size = 0;

          foreach($products as $product) {
            $products_required_fields[] = array(
                "id" => $product[CXP_ID_PRODUCT],
                CXP_CART_QUANTITY => $product[CXP_CART_QUANTITY],
                CXP_WIDTH => $product[CXP_WIDTH],
                CXP_HEIGHT => $product[CXP_HEIGHT],
                CXP_DEPTH => $product[CXP_DEPTH],
                CXP_WEIGHT => $product[CXP_WEIGHT],
                CXP_TOTAL => $product[CXP_TOTAL]
            );
            if (
                ($product[CXP_WIDTH]!="" && $product[CXP_HEIGHT]!="" && $product[CXP_DEPTH]!="") &&
                ($product[CXP_WIDTH]*$product[CXP_HEIGHT]*$product[CXP_DEPTH] > $biggest_size) ) {

                $biggest_size = $product[CXP_WIDTH]*$product[CXP_HEIGHT]*$product[CXP_DEPTH];
                $biggest_product = $product;
                
            }

            $total_weight +=$product[CXP_WEIGHT]; 
            $total += $product[CXP_TOTAL];
          }


          $conf = array(
            CXP_COMUNA_ORIGEN => Configuration::get(CXP_COMUNA_ORIGEN),
            CXP_COMUNA_DESTINO => $comuna_code,
            CXP_TOTAL => $total,
            "productos" => $products_required_fields
          );
          if ($biggest_product) { $conf[CXP_WIDTH] = $biggest_product[CXP_WIDTH]; }
          else { $conf[CXP_WIDTH] = 1; }
          if ($biggest_product) { $conf[CXP_HEIGHT] = $biggest_product[CXP_HEIGHT]; }
          else { $conf[CXP_HEIGHT] = 1; }
          if ($biggest_product) { $conf[CXP_DEPTH] = $biggest_product[CXP_DEPTH]; }
          else { $conf[CXP_DEPTH] = 1; }


            $jresult = $this->api->obtener_cotizacion($conf[CXP_COMUNA_ORIGEN], $conf[CXP_COMUNA_DESTINO], $total_weight,  $conf[CXP_WIDTH],  $conf[CXP_HEIGHT],  $conf[CXP_DEPTH], $conf[CXP_TOTAL]);

            $serviceValue = false;
            if (!$jresult){
                return false;
            } else {

            $lowestService = null;
            foreach ($jresult["data"]["courierServiceOptions"] as $option)
            {
                if ($option[CXP_SERVICETYPECODE] == $this->carriers_serviceTypeCode[$current_carrier_key] && intval($option[CXP_SERVICETYPECODE]) >=3)
                {
                    if($lowestService == NULL)
                    {
                        $lowestService = $option;
                        $serviceValue = intval($option[CXP_SERVICEVALUE]);
                    }
                    else
                    {
                        if($lowestService[CXP_SERVICETYPECODE] > $option[CXP_SERVICETYPECODE])
                        {
                            $lowestService = $option;
                            $serviceValue = intval($option[CXP_SERVICEVALUE]);
                        }
                    }
                }
            }
        }

        if(!serviceValue){
           return false;
        }

        return $serviceValue;
    }

    public function hookActionAdminCustomersListingFieldsModifier($params)
    {
        if (!isset($params[CXP_FIELDS]['customer_group'])) {
            $params[CXP_FIELDS]['customer_group'] = array(
                CXP_TITLE => 'Grupo',
                CXP_ALIGN => CXP_TEXT_CENTER,
                CXP_CLASS => CXP_FIXED_WIDTH_XS,
                CXP_FILTER_KEY => "cg!id_group"
            );
        }

        if (isset($params[CXP_SELECT])) {
            $params[CXP_SELECT] .= ", cg.id_group AS customer_group";
        }

        if (isset($params['join'])) {
            $params['join'] .= 'LEFT JOIN `' . _DB_PREFIX_ . 'customer_group` cg ON (a.`id_customer` = cg.`id_customer`)'; // NOSONAR
        }
    }

    /**
     * Hook allows to modify Order grid definition since 1.7.7.0
     *
     * @param array $params
     */
    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        if (empty($params['definition'])) {
            return;
        }

        /** @var PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface $definition */
        $definition = $params['definition'];

        $column1 = new PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn('carrier_reference');
        $column1->setName($this->l('Acciones Chilexpress'));
        $column1->setOptions([
            'field' => CXP_CARRIER_NAME,
        ]);

        $column2 = new PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn(CXP_TRACKING_NUMBER);
        $column2->setName($this->l('Tracking Chilexpress'));
        $column2->setOptions([
            'field' => CXP_TRACKING_NUMBER,
        ]);

        $definition->getColumns()->remove('country_name');

        $definition
            ->getColumns()
            ->addBefore(
                'actions',
                $column1
            );

            $definition
            ->getColumns()
            ->addBefore(
                'actions',
                $column2
            );
    }

    /**
     * Hook allows to modify Order grid data since 1.7.7.0
     *
     * @param array $params
     */
    public function hookActionOrderGridDataModifier(array $params) // NOSONAR
    {
        if (empty($params['data'])) {
            return;
        }

        /** @var PrestaShop\PrestaShop\Core\Grid\Data\GridData $gridData */
        $gridData = $params['data'];
        $modifiedRecords = $gridData->getRecords()->all();

        foreach ($modifiedRecords as $key => $data) {

            $order = new Order($data[CXP_ID_ORDER]);
            $res_carrier_name = '<div class="btn-group-action text-center"><div class="btn-group d-flex">';
            $res_tracking_number = "";
            if (Validate::isLoadedObject($order)) {
                $tracking_number = $order->shipping_number;
                $current_state = $order->current_state;
                $carrier_id = $order->id_carrier;

                foreach ($this->carriers_list as $carrier_key => $carrier_name) {
                    
                    $official_carrier_id = Configuration::get($carrier_key);
                    // print("<pre>".print_r($carrier_id,true)."</pre>");
                    // print("<pre>".print_r($official_carrier_id,true)."</pre>");
                    // print("<pre>".print_r($current_state,true)."</pre>");
                    
                    if( $carrier_id == $official_carrier_id && array_search( $current_state , array(100,5,4,8,2,11,12,9,3,7,10)) ) { // when state was the first then returned 0 that is false
            
                        if($tracking_number) {
                            $numbers = explode(",", $tracking_number);
                            $out = array();
                            foreach ($numbers as $number)
                            {
                                $out[] = '<a href="javascript:;" data-tracking-code="'.$number.'" data-orderid="'.$data[CXP_ID_ORDER].'">'.$number.'</a>';
                            }
                            $res_carrier_name .= CXP_A_HREF.$this->context->link->getAdminLink(CXP_CHILEXPRESS_ORDER).CXP_ORDER_ID_EQ.$data[CXP_ID_ORDER].'&action=ver_ot"  data-confirm-message="" data-toggle="pstooltip" data-placement="top" data-original-title="Imprimir OT" class="btn tooltip-link js-link-row-action dropdown-item inline-dropdown-item"><i class="material-icons">print</i></a>';
                            $res_tracking_number = implode('<br />', $out);
                        } else { 
                            $res_carrier_name .= CXP_A_HREF.$this->context->link->getAdminLink(CXP_CHILEXPRESS_ORDER).CXP_ORDER_ID_EQ.$data[CXP_ID_ORDER].'&action=generar_ot" data-confirm-message="" data-toggle="pstooltip" data-placement="top" data-original-title="Generar OT" class="btn tooltip-link js-link-row-action dropdown-item inline-dropdown-item"><i class="material-icons">settings</i></a>';
                        }
                    }
                }
            }
            $res_carrier_name .= '</div></div>';

            if (empty($data[CXP_CARRIER_NAME])) {
                $modifiedRecords[$key][CXP_CARRIER_NAME] = $res_carrier_name;
            }
            
            if (empty($data[CXP_TRACKING_NUMBER])) {
                $modifiedRecords[$key][CXP_TRACKING_NUMBER] = $res_tracking_number;
            }
        }

        $params['data'] = new PrestaShop\PrestaShop\Core\Grid\Data\GridData(
            new PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection($modifiedRecords),
            $gridData->getRecordsTotal(),
            $gridData->getQuery()
        );
    }

    /**
     * Hook allows to modify Order query builder and add custom sql statements since 1.7.7.0
     *
     * @param array $params
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        
        if (isset($params[CXP_SELECT])) {
            $params[CXP_SELECT] .= ", oca.tracking_number AS tracking_number";
            $params[CXP_SELECT] .= ", ca.name AS carrier_name";
        }

        if (isset($params['join'])) {
            $params['join'] .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_carrier` oca ON (a.`id_order` = oca.`id_order`)'; // NOSONAR
            $params['join'] .= 'LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (ca.`id_carrier` = oca.`id_carrier`)'; // NOSONAR
        }
    }

    /**
     * Hook allows to modify Order grid data before 1.7.7.0
     *
     * @param array $params
     */
    public function hookActionAdminOrdersListingFieldsModifier($params)
    {
        if (!isset($params[CXP_FIELDS][CXP_CARRIER_NAME])) {
            $params[CXP_FIELDS][CXP_CARRIER_NAME] = array(
                CXP_TITLE => 'Acciones',
                CXP_ALIGN => CXP_TEXT_CENTER,
                CXP_CLASS => CXP_FIXED_WIDTH_XS,
                CXP_FILTER_KEY => "ca!name",
                'callback' => 'callbackMethod2',
                'orderby' => false,
                'search' => false,
                'callback_object' => Module::getInstanceByName($this->name)
            );
        } 

        if (!isset($params[CXP_FIELDS][CXP_TRACKING_NUMBER])) {

            $params[CXP_FIELDS][CXP_TRACKING_NUMBER] = array(
                CXP_TITLE => 'Tracking',
                CXP_ALIGN => CXP_TEXT_CENTER,
                CXP_CLASS => CXP_FIXED_WIDTH_XS,
                CXP_FILTER_KEY => "oca!tracking_number",
                'callback' => 'callbackMethod',
                'orderby' => false,
                'search' => false,
                'callback_object' => Module::getInstanceByName($this->name)
            );
        }
        
        if (isset($params[CXP_SELECT])) {
            $params[CXP_SELECT] .= ", oca.tracking_number AS tracking_number";
            $params[CXP_SELECT] .= ", ca.name AS carrier_name";
        }

        if (isset($params['join'])) {
            $params['join'] .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_carrier` oca ON (a.`id_order` = oca.`id_order`)'; // NOSONAR
            $params['join'] .= 'LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (ca.`id_carrier` = oca.`id_carrier`)'; // NOSONAR
        }

    }

    public function callbackMethod($value, $tr)
    {
        $order = new Order($tr[CXP_ID_ORDER]);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }
        $tracking_number = $order->shipping_number;
        $current_state = $order->current_state;
        $carrier_id = $order->id_carrier;

        foreach ($this->carriers_list as $carrier_key => $carrier_name) {
            $official_carrier_id = Configuration::get($carrier_key);
            if( $carrier_id == $official_carrier_id && array_search( $current_state , array(100,5,4,8,2,11,12,9,3,7,10)) && $tracking_number) {
                $numbers = explode(",", $tracking_number);
                $out = array();
                foreach ($numbers as $number)
                {
                    $out[] = '<a href="javascript:;" data-tracking-code="'.$number.'" data-orderid="'.$tr[CXP_ID_ORDER].'">'.$number.'</a>';
                }
                return implode('<br />', $out);
            }
        }

        return '-';
    }

    public function callbackMethod2($value, $tr) // NOSONAR
    {
        $order = new Order($tr[CXP_ID_ORDER]);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }
        $tracking_number = $order->shipping_number;
        $current_state = $order->current_state;
        $carrier_id = $order->id_carrier;

        foreach ($this->carriers_list as $carrier_key => $carrier_name) {
            $official_carrier_id = Configuration::get($carrier_key);
            if( $carrier_id == $official_carrier_id && array_search( $current_state , array(100,5,4,8,2,11,12,9,3,7,10)) )
            {
                if(!$tracking_number) {
                    return CXP_A_HREF.$this->context->link->getAdminLink(CXP_CHILEXPRESS_ORDER).CXP_ORDER_ID_EQ.$tr[CXP_ID_ORDER].'&action=generar_ot" title="Generar OT" class="btn btn-default with-tooltip"><i class="icon-cog" data-toggle="tooltip" data-placement="top" title="Generar OT"></i></a>';
                } else {
                    return CXP_A_HREF.$this->context->link->getAdminLink(CXP_CHILEXPRESS_ORDER).CXP_ORDER_ID_EQ.$tr[CXP_ID_ORDER].'&action=ver_ot" title="Imprimir OT" class="btn btn-default with-tooltip"><i class="icon-print" data-toggle="tooltip" data-placement="top" title="Imprimir OT"></i></a>';
                }
            }
        }

        return '';
    }

    /**
     * Add buttons to main buttons bar
     */
    public function hookActionGetAdminOrderButtons(array $params)
    {
        $order = new Order($params[CXP_ID_ORDER]);

        /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router */
        $router = $this->get('router');

        /** @var \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButtonsCollection $bar */
        $bar = $params['actions_bar_buttons_collection'];

        $viewCustomerUrl = $router->generate('admin_customers_view', ['customerId'=> (int)$order->id_customer]);
        $bar->add(
            new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                'btn-secondary', ['href' => $viewCustomerUrl], 'View customer'
            )
        );
        $bar->add(
            new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                'btn-info', ['href' => 'https://www.prestashop.com/'], 'Go to prestashop'
            )
        );
        $bar->add(
            new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                'btn-dark', ['href' => 'https://github.com/PrestaShop/example-modules/tree/master/demovieworderhooks'], 'Go to GitHub'
            )
        );
        $createAnOrderUrl = $router->generate('admin_orders_create');
        $bar->add(
            new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                'btn-link', ['href' => $createAnOrderUrl], 'Create an order'
            )
        );
    }

    public function loadSQLFile($sql_file)
    {
      // Get install SQL file content
      $sql_content = file_get_contents($sql_file);

      // Replace prefix and store SQL command in array
      $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
      $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content); // NOSONAR

      // Execute each SQL statement
      $result = true;
      foreach($sql_requests as $request){
        if (!empty($request)) {
            $result &= Db::getInstance()->execute(trim($request));
        }
      }

      // Return result
      return $result;
    }
    public function hookDisplayAdminOrderTabLink($params)
    {
        //display tab in order(admin) page
        $module_settings = Tools::unserialize(Configuration::get('VELOCITY_SUPERCHECKOUT'));
        if ($module_settings['enable'] == 1) {
            $this->context->controller->addCSS($this->_path . 'views/css/preferred_delivery.css');
            $this->context->smarty->assign('kb_version', '1.7.7');
            return $this->display(__FILE__, 'custom_fields_data_tab.tpl');
        }
    }
    // function hookDisplayAdminOrderContentShip($param)
    public function hookDisplayAdminOrderTabContent($param)
    {
    
        $id_order = $param['id_order'];
        $order = new Order((int) $id_order);
       // print("<pre>".print_r($order->shipping_number,true)."</pre>");
        // die('gruiz');
        if ( $order->shipping_number!= '' )
        {
            $order_id = $order->id;
            $carrier_id = $order->id_carrier;


            foreach ($this->carriers_list as $carrier_key => $carrier_name) {
                $official_carrier_id = Configuration::get($carrier_key);
                if ( $carrier_id == $official_carrier_id){
                    $query = new DbQuery();
                    $query->from('chilexpress_oficial_ordermeta', 'com');
                    $query->where('com.id_order = "'.$order_id.'"');
                    $metasArray = Db::getInstance()->executeS($query);
                    $out = '<div class="well"><div class="row"><div class="col-md-12"><h3>Etiquetas de Chilexpress</h3></div>';
                    foreach ($metasArray as $meta) {
                        $src = 'data:image/jpg;base64,'.$meta['labelData'];
                        $out.= '<img src="'.$src.'" style="display:block; margin:0 auto;"/> <br />';

                    }
                    $out.= CXP_A_HREF.$this->context->link->getAdminLink(CXP_CHILEXPRESS_ORDER).CXP_ORDER_ID_EQ.$order_id.'&action=print_ot'.'" class="btn btn-primary btn-block"><i class="icon-print"></i> Imprimir Etiquetas</a>';

                    $out .= "</div></div>";
                    // return $this->display(__FILE__, 'custom_fields_data_content.tpl');
                    // return $out;
                }
            }
        }
        if (!isset($out)) {
            $out = '';
        }
        // echo __FILE__, 'custom_fields_data_content.tpl';
        $this->context->smarty->assign('out', $out);
        return $this->display(__FILE__, 'custom_fields_data_content.tpl');
        // return '';
        
    }
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }    
    public function hookDisplayAdminOrder($params)
    {
        $id_order = $params['id_order'];
        $order = new Order((int) $id_order);
        $order_id = intval($id_order);
        $this->page_name = Dispatcher::getInstance()->getController();
        if (in_array($this->page_name, array('order')))
        {

            // recibe post
            if (Tools::getValue('call_chilexpress')) {
                $this->Kberrors = [];

                $query = "SELECT * FROM "._DB_PREFIX_."order_state_lang WHERE id_lang='".(int) Context::getContext()->language->id."' AND id_order_state=3";
                $estados = Db::getInstance()->getRow($query);                  


                if (!Validate::isLoadedObject($order)) {
                   $this->Kberrors[] = 'Orden no encontrada';
                }
                if($order->current_state !== '3'){
                    // $this->Kberrors[] = 'Solo puede generar etiquetas para pedidos con estado "'.$estados['name'].'"'; 
                }
                if (empty($this->Kberrors)) {
                    $address_obj = new Address($order->id_address_delivery);
                    $customer = new Customer($address_obj->id_customer);

                    $address = array(
                        "city" => $address_obj->city,
                        "address1" => $address_obj->address1,
                        "other" => $address_obj->other,
                    );
                    $devolucion = array(
                        "city" => Configuration::get('comuna_devolucion'),
                        "address1" => Configuration::get('calle_devolucion'),
                        "address2" => Configuration::get('numero_calle_devolucion'),
                        "other" => Configuration::get('complemento_devolucion')
                    );
                    $remitente = array(
                        "nombre" => Configuration::get('nombre_remitente'),
                        "telefono" => Configuration::get('telefono_remitente'),
                        "email" => Configuration::get('email_remitente')
                    );
                    $destinatario = array(
                        "nombre" => $customer->firstname.' '.$customer->lastname,
                        "telefono" => $address_obj->phone,
                        "email" => $customer->email
                    );

                    $cart = new Cart($order->id_cart);
                    // $products = $cart->getProducts(true);
                    $products = $order->getProducts();
                    $products_array = array();

                    foreach($products as $product) {
                        $products_array[] = array(
                            "id" => $product["id_product"],
                            "cart_quantity" => $product["product_quantity"],
                            "name" => $product["product_name"],
                            "width" => $product["width"],
                            "height" => $product["height"],
                            "depth" => $product["depth"],
                            "weight" => $product["weight"],
                            "total" => $product["total_price_tax_incl"],
                        );
                    }
                    $this->carriers_serviceTypeCode = array(
                        'CHILEXPRESS_OFCL_DHS' => 3,
                        'CHILEXPRESS_OFCL_DHSS' => 4,
                        'CHILEXPRESS_OFCL_3DH' => 5
                    );
                    $serviceTypeId  = 0;
                    foreach ($this->carriers_serviceTypeCode as $carrier_key => $stypeid)
                    {
                        if (Configuration::get($carrier_key) == $order->id_carrier)
                        {
                            $serviceTypeId = $stypeid;
                        }
                    }

                    $payload_header = array(
                        "certificateNumber" => 0, //Número de certificado, si no se ingresa se creará uno nuevo
                        "customerCardNumber"=> Configuration::get('numero_tcc_origen'), // Número de Tarjeta Cliente Chilexpress (TCC)
                        "countyOfOriginCoverageCode"=> Configuration::get('comuna_origen'), // Comuna de origen
                        "labelType"=> 2, // Imagen
                        "marketplaceRut"=> intval(Configuration::get('numero_tcc_origen')), // Rut asociado al Marketplace
                        "sellerRut"=> "DEFAULT", // Rut asociado al Vendedor
                        "sourceChannel" => 6 // prestashop se identifica en el sistema como 6
                    );
                    $payload_address_destino = array(
                                        "addressId" => 0, // Id de la dirección obtenida de la API Validar dirección
                                        "countyCoverageCode"=>  $address["city"], // Cobertura de destino obtenido por la API Consultar Coberturas
                                        "streetName"=> $address["address1"], // Nombre de la calle
                                        // "streetNumber"=> $address["other"], // Numeraci'on de la calle
                                        "supplement"=> $address["other"], // Información complementaria de la dirección
                                        "addressType"=> "DEST", // Tipo de dirección; DEST = Entrega, DEV = Devolución.
                                        "deliveryOnCommercialOffice"=> false, // Indicador si es una entrega en oficina comercial (true) o entrega en domicilio (false)
                                        "commercialOfficeId"=> "",
                                        "observation"=> "DEFAULT" // Observaciones adicionales
                                    );
                    $payload_address_devolucion = array(
                                        "addressId"=> 0,
                                        "countyCoverageCode"=> $devolucion['city'],
                                        "streetName"=> $devolucion['address1'],
                                        "streetNumber"=> $devolucion['address2'],
                                        "supplement"=> $devolucion['other'],
                                        "addressType"=> "DEV",
                                        "deliveryOnCommercialOffice"=> false,
                                        "observation"=> "DEFAULT"
                                    );
                    $payload_contact_devolucion = array(
                                        "name"=> $remitente['nombre'],
                                        "phoneNumber"=> $remitente['telefono'],
                                        "mail"=> $remitente['email'],
                                        "contactType"=> "R" // Tipo de contacto; Destinatario (D), Remitente (R)
                                    );
                    $payload_contact_destino = array(
                                        "name"=> $destinatario["nombre"],
                                        "phoneNumber"=> $destinatario["telefono"],
                                        "mail"=> $destinatario["email"],
                                        "contactType"=> "D" // Tipo de contacto; Destinatario (D), Remitente (R)
                                    );

                    $pre_paquetes = array();
                    $paquetes = array();
                    
                    foreach ($products_array as $key => $value) {
                        $opcion_paquetes[$value['id']] = 1; 
                    }
                    foreach($opcion_paquetes as $prodid => $numero_paquete ):
                        foreach ($products_array as $product ):
                            if ($product["id"] == "$prodid") {
                                if (isset($pre_paquetes[$numero_paquete])) {
                                    $pre_paquetes[$numero_paquete]["names"][] = $product["name"]." x ".$product["cart_quantity"];
                                    $pre_paquetes[$numero_paquete]["weight"] += $product["weight"]*$product["cart_quantity"];
                                    $pre_paquetes[$numero_paquete]["total"] += $product["total"]*$product["cart_quantity"];
                                    $pre_paquetes[$numero_paquete]["volumes"]["$item_id"] = $product['height']*$product["cart_quantity"]*$product['width']*$product['depth'];
                                } else {
                                    $names = array($product["name"]." x ".$product["cart_quantity"]);
                                    $pre_paquetes[$numero_paquete] = array(
                                        "names" => $names,
                                        "weight"=> $product["weight"]*$product["cart_quantity"],
                                        "total"=> $product["total"]*$product["cart_quantity"],
                                        "volumes" => array(
                                            "$prodid" => $product['height']*$product["cart_quantity"]*$product['width']*$product['depth']
                                        )
                                    );
                                }
                            }
                        endforeach;
                    endforeach;


                    foreach($pre_paquetes as $numero_paquete => $base_paquete ):
                        // ordenamos los volumenes en volumen de mayor a menor
                        arsort($base_paquete["volumes"]);
                        // obtenemos el id del producto 
                        $biggest_product_id = $this->array_key_first($base_paquete["volumes"]);
                        foreach ($products_array as $product ):
                            $item_id = $product["id"];

                            if ($item_id == $biggest_product_id) {

                                if($base_paquete["weight"] == 0) {
                                    $base_paquete["weight"] += 1;
                                }

                                if($product['height'] == 0) {
                                    $product['height'] += 1;
                                }

                                if($product['width'] == 0) {
                                    $product['width'] += 1;
                                }

                                if($product['depth'] == 0) {
                                    $product['depth'] += 1;
                                }
                                
                                $articulos_tienda = Configuration::get('articulos_tienda');
                                if (!$articulos_tienda) {
                                    $articulos_tienda = 5;
                                }

                                $paquetes[] =  array(
                                        "weight"=> $base_paquete["weight"], // Peso en kilogramos
                                        "height"=> $product['height'], // Altura en centímetros
                                        "width"=> $product['width'], // Ancho en centímetros
                                        "length"=> $product['depth'],  // Largo en centímetros
                                        "serviceDeliveryCode"=> $serviceTypeId, // Código del servicio de entrega, obtenido de la API Cotización
                                        "productCode"=> "3", // Código del tipo de roducto a enviar; 1 = Documento, 3 = Encomienda
                                        "deliveryReference"=> "ORDEN-".$order_id, // Referencia que permite identificar el envío por parte del cliente.
                                        "groupReference"=> "ORDEN-".$order_id."-GRUPO-1", // Referencia que permite identificar un grupo de bultos que va por parte del cliente.
                                        "declaredValue"=> round($base_paquete["total"]), // Valor declarado del producto
                                        "declaredContent"=> $articulos_tienda, // Tipo de producto enviado; 1 = Moda, 2 = Tecnologia, 3 = Repuestos, 4 = Productos medicos, 5 = Otros
                                        "descriptionContent" => implode(";",$base_paquete["names"]),
                                        "extendedCoverageAreaIndicator"=> false, // Indicador de contratación de cobertura extendida 0 = No, 1 = Si
                                        "receivableAmountInDelivery"=> 0 // Monto a cobrar, en caso que el cliente tenga habilitada esta opción. Se deja en 0 a solicitud de RCEA
                                    );
                            }
                        endforeach;
                    endforeach;             
                    

                    $payload = array(
                        "header" => $payload_header,
                        "details" => array(
                            array(
                                "addresses" => array(
                                    $payload_address_destino,
                                    $payload_address_devolucion
                                ),
                                "contacts" => array( // Se debe entregar un detalle para los datos de contacto del destinatario (D) y otro para los del remitente (R)
                                    $payload_contact_devolucion,
                                    $payload_contact_destino
                                ),
                                "packages" => $paquetes

                            )
                        )
                    );

                    $jresult = $this->api->generar_ot(json_encode($payload));
                    $serviceValue = false;
                    $shipping_numbers = array();
                    // print("<pre>".print_r($payload,true)."</pre>");
                    // print("<pre>".var_dump($jresult)."</pre>");
                    // die('aqui');
                    if (!$jresult){
                        $this->Kberrors[] = 'Error tratando de crear Orden de Transporte';
                    } else { 
                        foreach($jresult['data']['detail'] as $d) {
                            $OrderMeta = new ChilexpressOficialOrderMeta();
                            $OrderMeta->id_order = (int)$order_id;
                            $shipping_numbers[] = $d['transportOrderNumber'];

                            $OrderMeta->transportOrderNumber = $d['transportOrderNumber'];
                            $OrderMeta->reference = $d['reference'];
                            $OrderMeta->productDescription = $d['productDescription'];
                            $OrderMeta->serviceDescription = $d['serviceDescription'];
                            $OrderMeta->genericString1 = $d['genericString1'];
                            $OrderMeta->genericString2 = $d['genericString2'];
                            $OrderMeta->deliveryTypeCode = $d['deliveryTypeCode'];
                            $OrderMeta->destinationCoverageAreaName = $d['destinationCoverageAreaName'];
                            $OrderMeta->additionalProductDescription = $d['additionalProductDescription'];
                            $OrderMeta->barcode = $d['barcode'];
                            $OrderMeta->classificationData = $d['classificationData'];
                            $OrderMeta->printedDate = $d['printedDate'];
                            $OrderMeta->labelVersion = $d['labelVersion'];
                            $OrderMeta->distributionDescription = $d['distributionDescription'];
                            $OrderMeta->companyName = $d['companyName'];
                            $OrderMeta->recipient = $d['recipient'];
                            $OrderMeta->address = $d['address'];
                            $OrderMeta->groupReference = $d['groupReference'];
                            $OrderMeta->createdDate = $d['createdDate'];
                            $OrderMeta->labelData = $d['label']['labelData'];
                            $OrderMeta->add();
                        }

                        $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
                        $order_carrier->tracking_number = implode(',', $shipping_numbers);
                        $order_carrier->save();

                        $order->shipping_number = implode(',', $shipping_numbers);
                        $order->save();

                        $version_parts = explode('.', _PS_VERSION_);
                        if (count($version_parts) >= 3 && $version_parts[0] >= 1 && $version_parts[1] >= 7 && $version_parts[2] >= 7) {
                            // newer than 1.7.7
                            // $link = new Link();
                            // $url = ($link->getAdminLink('AdminOrders', true, [], ['id_order' => $order_id, 'vieworder' => 1]));
                            // header("Location: $url");
                            // die();
                        } else {
                            // older than 1.7.7
                            // $url = ($this->context->link->getAdminLink('AdminOrders', true, [], ['id_order' => $order_id, 'vieworder' => 1]) . '&amp;id_order='.$order_id.'&amp;vieworder');
                            
                        }
                        // Tools::redirect($url);

                    }    
                    if (count($this->Kberrors) > 0) {
                        $this->context->cookie->__set('redirect_error', implode('####', $this->Kberrors));
                    } else {
                        $this->Kbconfirmation[] = 'Etiqueta creada correctamente';
                        $this->context->cookie->__set('redirect_success', implode('####', $this->Kbconfirmation));
                    }
                }
                            
                $request_param = array();

                $request_param['id_order'] = (int)$id_order;
                $request_param['render_type'] = 'view';

                Tools::redirect($this->context->link->getModuleLink(
                    'kbmarketplace',
                    'order',
                    $request_param,
                    (bool)Configuration::get('PS_SSL_ENABLED')
                ));                
            }
            
            // fin recibe post

            if ( $order->shipping_number!= '' )
            {
                $order_id = $order->id;
                $carrier_id = $order->id_carrier;


                foreach ($this->carriers_list as $carrier_key => $carrier_name) {
                    $official_carrier_id = Configuration::get($carrier_key);
                    if ( $carrier_id == $official_carrier_id){
                        $query = new DbQuery();
                        $query->from('chilexpress_oficial_ordermeta', 'com');
                        $query->where('com.id_order = "'.$order_id.'"');
                        $metasArray = Db::getInstance()->executeS($query);

                        
                        $src = '';
                        $out = '<div class="well"><div class="row"><div class="col-md-12"><h3>Etiquetas de Chilexpress</h3></div>';
                        foreach ($metasArray as $meta) {
                            $src = 'data:image/jpg;base64,'.$meta['labelData'];
                            $out.= '<img src="'.$src.'" style="display:block; margin:0 auto;"/> <br />';

                        }
                        $out.= CXP_A_HREF.$this->context->link->getAdminLink(CXP_CHILEXPRESS_ORDER).CXP_ORDER_ID_EQ.$order_id.'&action=print_ot'.'" class="btn btn-primary btn-block"><i class="icon-print"></i> Imprimir Etiquetas</a>';

                        $out .= "</div></div>";
                        // return $this->display(__FILE__, 'custom_fields_data_content.tpl');
                        // return $out;
                        $request_param['action'] = 'print_ot';
                        $request_param['order_id'] = $order_id;
                        $url_imprimir =  $this->context->link->getModuleLink(
                        'chilexpress_oficial',
                        'ajax',
                        $request_param,
                        (bool)Configuration::get('PS_SSL_ENABLED')
                        );                        
                        
                        $this->context->smarty->assign([
                            'metasArray' => $metasArray,
                            'src' => $src,
                            'url_imprimir' => $url_imprimir,

                        ]);
                    }
                }

            }

        if (!isset($out)) {
            $out = '';
        }
        $this->context->smarty->assign([
            // 'swa_error' => $error,
            // 'swa_shipping' => $exists,
            // 'cart_cch' => $cart_cch,
            // 'carrier_order' => $carrier_order,
            'out' => $out,
            'orderid' => $id_order,
            // 'envio' => $envio,
        ]); 
        // var_dump($this->page_name);
        // die('gruizfff');
        return $this->display(__FILE__, 'views/templates/hook/front_order.tpl');


        } else{
            
        } 

    }
    private function checkAndAddChileanStatesOnInstall() // NOSONAR
    {
        // // // // // // // //
        $id_country = (int) Country::getByIso(strval('CL'));
        $regiones = $this->api->obtener_regiones();
        foreach ($regiones as $iso => $name) {
            
            $id_state = State::getIdByIso($iso, $id_country);
            if (!$id_state) {
                $state = new State();
                $state->name = substr ($name , 0 , 32) ;
                $state->iso_code = strval($iso);
                $state->id_country = $id_country;

                $id_zone = (int) Zone::getIdByName(strval('South America'));

                if (!$id_zone) {
                    $zone = new Zone();
                    $zone->name = 'South America';
                    $zone->active = true;
                    
                    if (!$zone->add()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid Zone name.', array(), CXP_ADMIN_INTERNATIONAL_NOTIFICATION);

                        return false;
                    }

                    $id_zone = $zone->id;
                }
                $state->id_zone = $id_zone;

                $country = new Country($state->id_country);
                if (!$country->contains_states) {
                    $country->contains_states = 1;
                    if (!$country->update()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('Cannot update the associated country: %s', array($country->name), CXP_ADMIN_INTERNATIONAL_NOTIFICATION);
                    }
                }

                if (!$state->add()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while adding the state.', array(), CXP_ADMIN_INTERNATIONAL_NOTIFICATION);

                    return false;
                }

            }
        }
        return true;
        // // // // // // // //
    }

    public function getErrors()
    {
        return $this->_errors;
    }

}
