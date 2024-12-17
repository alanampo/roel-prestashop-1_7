<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2022 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once('classes/tvcmsmap_status.class.php');
include_once('classes/tvcmsmap_image_upload.class.php');

class TvcmsMap extends Module
{
    public function __construct()
    {
        $this->name = 'tvcmsmap';
        $this->tab = 'front_office_features';
        $this->version = '3.0.0';
        $this->author = 'JHP Template';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('JHP Template - Map');
        $this->description = $this->l('Its Show Map on Front Side');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = '';

        $this->confirmUninstall = $this->l('Warning: all the data saved in your database will be deleted.'.
            ' Are you sure you want uninstall this module?');
    }


    public function install()
    {
        $this->installTab();
        // $this->createDefaultData();
        
        return parent::install()
            && $this->registerHook('displayMapInformation')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayShowMap')
            && $this->registerHook('displayWrapperBottom')
            && $this->registerHook('displayHeader');
    }
    public function installTab()
    {
        $response = true;

        // First check for parent tab
        $parentTabID = Tab::getIdFromClassName('AdminJHPTemplate');

        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminJHPTemplate";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "JHPTemplate Extension";
            }
            $parentTab->id_parent = 0;
            $parentTab->module = $this->name;
            $response &= $parentTab->add();
        }
        
        // Check for parent tab2
        $parentTab_2ID = Tab::getIdFromClassName('AdminJHPTemplateModules');
        if ($parentTab_2ID) {
            $parentTab_2 = new Tab($parentTab_2ID);
        } else {
            $parentTab_2 = new Tab();
            $parentTab_2->active = 1;
            $parentTab_2->name = array();
            $parentTab_2->class_name = "AdminJHPTemplateModules";
            foreach (Language::getLanguages() as $lang) {
                $parentTab_2->name[$lang['id_lang']] = "JHPTemplate Configure";
            }
            $parentTab_2->id_parent = $parentTab->id;
            $parentTab_2->module = $this->name;
            $response &= $parentTab_2->add();
        }
        // Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Admin'.$this->name;
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Map Section";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function createDefaultData()
    {
        $languages = Language::getLanguages();
        $result = array();
        foreach ($languages as $lang) {
            $result['TVCMSMAP_MAIN_TITLE'][$lang['id_lang']] = 'Main Title';
            $result['TVCMSMAP_MAIN_SUB_DESCRIPTION'][$lang['id_lang']] = 'Sub Description';
            $result['TVCMSMAP_MAIN_DESCRIPTION'][$lang['id_lang']] = '<h1>Description</h1>';
            $result['TVCMSMAP_MAIN_IMG'][$lang['id_lang']] = 'demo_main_img.jpg';

            $result['TVCMSMAP_TITLE'][$lang['id_lang']] = 'Test';
            $result['TVCMSMAP_SUB_DESCRIPTION'][$lang['id_lang']] = 'Sub Description';
            $result['TVCMSMAP_DESCRIPTION'][$lang['id_lang']] = '<h1>Description</h1>';
            $result['TVCMSMAP_IMG'][$lang['id_lang']] = 'demo_img_1.png';
            $result['TVCMSMAP_BTN_TITLE'][$lang['id_lang']] = 'See Map';
        }

        Configuration::updateValue('TVCMSMAP_IMG', $result['TVCMSMAP_IMG']);
        Configuration::updateValue('TVCMSMAP_TITLE', $result['TVCMSMAP_TITLE']);
        Configuration::updateValue('TVCMSMAP_SUB_DESCRIPTION', $result['TVCMSMAP_SUB_DESCRIPTION']);
        Configuration::updateValue('TVCMSMAP_DESCRIPTION', $result['TVCMSMAP_DESCRIPTION'], true);
        Configuration::updateValue('TVCMSMAP_BTN_TITLE', $result['TVCMSMAP_BTN_TITLE']);
        Configuration::updateValue('TVCMSMAP_API_KEY', '');
        Configuration::updateValue('TVCMSMAP_MAP_TYPE', 'roadmap');
        Configuration::updateValue('TVCMSMAP_ZOOM', '4');
        Configuration::updateValue('TVCMSMAP_LETITUDE', '');
        Configuration::updateValue('TVCMSMAP_LONGITUDE', '');
    }

    public function uninstall()
    {
        $this->uninstallTab();
        $this->deleteVariable();
        return parent::uninstall();
    }

    public function deleteVariable()
    {
        Configuration::deleteByName('TVCMSMAP_MAIN_TITLE');
        Configuration::deleteByName('TVCMSMAP_MAIN_SUB_DESCRIPTION');
        Configuration::deleteByName('TVCMSMAP_MAIN_DESCRIPTION');
        Configuration::deleteByName('TVCMSMAP_MAIN_IMG');

        Configuration::deleteByName('TVCMSMAP_TITLE');
        Configuration::deleteByName('TVCMSMAP_SUB_DESCRIPTION');
        Configuration::deleteByName('TVCMSMAP_DESCRIPTION');
        Configuration::deleteByName('TVCMSMAP_IMG');
        Configuration::deleteByName('TVCMSMAP_BTN_TITLE');
        Configuration::deleteByName('TVCMSMAP_API_KEY');
        Configuration::deleteByName('TVCMSMAP_MAP_TYPE');
        Configuration::deleteByName('TVCMSMAP_ZOOM');
        Configuration::deleteByName('TVCMSMAP_LETITUDE');
        Configuration::deleteByName('TVCMSMAP_LONGITUDE');
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('Admin'.$this->name);
        $tab = new Tab($id_tab);
        $tab->delete();
        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitTvcmsSampleinstall') && Tools::getValue('tvinstalldata') == "1") {
            $this->createDefaultData();
        }

        $message = $this->postProcess();
        $output = $message
                .$this->renderForm();
        return $output;
    }

    public function postProcess()
    {
        $message = '';
        $result = array();

        if (Tools::isSubmit('submitvcmsMapForm')) {
            $languages = Language::getLanguages();
            $obj_image = new TvcmsMapImageUpload();
            foreach ($languages as $lang) {
                if (!empty($_FILES['TVCMSMAP_IMG_'.$lang['id_lang']]['name'])) {
                    $old_file = Configuration::get('TVCMSMAP_IMG', $lang['id_lang']);
                    $new_file = $_FILES['TVCMSMAP_IMG_'.$lang['id_lang']];
                    $ans = $obj_image->imageUploading($new_file, $old_file);
                    if ($ans['success']) {
                        $result['TVCMSMAP_IMG'][$lang['id_lang']] = $ans['name'];
                    } else {
                        $message .= $ans['error'];
                        $result['TVCMSMAP_IMG'][$lang['id_lang']] = $old_file;
                    }
                } else {
                    $old_file = Configuration::get('TVCMSMAP_IMG', $lang['id_lang']);
                    $result['TVCMSMAP_IMG'][$lang['id_lang']] = $old_file;
                }

                $tmp = Tools::getValue('TVCMSMAP_TITLE_'.$lang['id_lang']);
                $result['TVCMSMAP_TITLE'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSMAP_SUB_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSMAP_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSMAP_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSMAP_DESCRIPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSMAP_BTN_TITLE_'.$lang['id_lang']);
                $result['TVCMSMAP_BTN_TITLE'][$lang['id_lang']] = $tmp;
            }
            Configuration::updateValue('TVCMSMAP_IMG', $result['TVCMSMAP_IMG']);
            Configuration::updateValue('TVCMSMAP_TITLE', $result['TVCMSMAP_TITLE']);
            $tmp = $result['TVCMSMAP_SUB_DESCRIPTION'];
            Configuration::updateValue('TVCMSMAP_SUB_DESCRIPTION', $tmp);

            $tmp = $result['TVCMSMAP_DESCRIPTION'];
            Configuration::updateValue('TVCMSMAP_DESCRIPTION', $tmp, true);

            $tmp = $result['TVCMSMAP_BTN_TITLE'];
            Configuration::updateValue('TVCMSMAP_BTN_TITLE', $tmp);

            Configuration::updateValue('TVCMSMAP_API_KEY', Tools::getValue('TVCMSMAP_API_KEY'));
            Configuration::updateValue('TVCMSMAP_MAP_TYPE', Tools::getValue('TVCMSMAP_MAP_TYPE'));
            Configuration::updateValue('TVCMSMAP_ZOOM', Tools::getValue('TVCMSMAP_ZOOM'));
            Configuration::updateValue('TVCMSMAP_LETITUDE', Tools::getValue('TVCMSMAP_LETITUDE'));
            Configuration::updateValue('TVCMSMAP_LONGITUDE', Tools::getValue('TVCMSMAP_LONGITUDE'));

            $message .= $this->displayConfirmation($this->l("Map is Updated."));
        }

        if (Tools::isSubmit('submitvcmsMapMainTitleForm')) {
            $languages = Language::getLanguages();
            $obj_image = new TvcmsMapImageUpload();
            foreach ($languages as $lang) {
                if (!empty($_FILES['TVCMSMAP_MAIN_IMG_'.$lang['id_lang']]['name'])) {
                    $old_file = Configuration::get('TVCMSMAP_MAIN_IMG', $lang['id_lang']);
                    $new_file = $_FILES['TVCMSMAP_MAIN_IMG_'.$lang['id_lang']];
                    $ans = $obj_image->imageUploading($new_file, $old_file);
                    if ($ans['success']) {
                        $result['TVCMSMAP_MAIN_IMG'][$lang['id_lang']] = $ans['name'];
                    } else {
                        $message .= $ans['error'];
                        $result['TVCMSMAP_MAIN_IMG'][$lang['id_lang']] = $old_file;
                    }
                } else {
                    $old_file = Configuration::get('TVCMSMAP_MAIN_IMG', $lang['id_lang']);
                    $result['TVCMSMAP_MAIN_IMG'][$lang['id_lang']] = $old_file;
                }

                $tmp = Tools::getValue('TVCMSMAP_MAIN_TITLE_'.$lang['id_lang']);
                $result['TVCMSMAP_MAIN_TITLE'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSMAP_MAIN_SUB_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSMAP_MAIN_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSMAP_MAIN_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSMAP_MAIN_DESCRIPTION'][$lang['id_lang']] = $tmp;
            }
            Configuration::updateValue('TVCMSMAP_MAIN_IMG', $result['TVCMSMAP_MAIN_IMG']);
            Configuration::updateValue('TVCMSMAP_MAIN_TITLE', $result['TVCMSMAP_MAIN_TITLE']);
            $tmp = $result['TVCMSMAP_MAIN_SUB_DESCRIPTION'];
            Configuration::updateValue('TVCMSMAP_MAIN_SUB_DESCRIPTION', $tmp);

            $tmp = $result['TVCMSMAP_MAIN_DESCRIPTION'];
            Configuration::updateValue('TVCMSMAP_MAIN_DESCRIPTION', $tmp);
            $message .= $this->displayConfirmation($this->l("Main Title is Updated."));
        }

            
        return $message;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form = array();

        $tvcms_obj = new TvcmsMapStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        if ($show_fields['main_form']) {
            $form[] = $this->tvcmsMapMainTitleForm();
        }

        if ($show_fields['record_form']) {
            $form[] = $this->tvcmsMapForm();
        }

        return $helper->generateForm($form);
    }


    protected function tvcmsMapMainTitleForm()
    {
        $tvcms_obj = new TvcmsMapStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $input = array();

        if ($show_fields['main_title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_MAIN_TITLE',
                    'label' => $this->l('Main Title'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_short_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_MAIN_SUB_DESCRIPTION',
                    'label' => $this->l('Short Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_MAIN_DESCRIPTION',
                    'label' => $this->l('Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_image']) {
            $input[] = array(
                        'type' => 'image_file',
                        'name' => 'TVCMSMAP_MAIN_IMG',
                        'label' => $this->l('Image'),
                );
        }


        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Main Title'),
                'icon' => 'icon-support',
                ),
                'input' => $input,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitvcmsMapMainTitleForm',
                ),
            ),
        );
    }

    protected function tvcmsMapForm()
    {
        $tvcms_obj = new TvcmsMapStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $input = array();

        $input[] = array(
            'col' => 12,
            'type' => 'BtnInstallData',
            'name' => 'BtnInstallData',
            'label' => '',
        );

        if ($show_fields['image']) {
            $input[] = array(
                        'type' => 'tvcmsmap_img',
                        'name' => 'TVCMSMAP_IMG',
                        'label' => $this->l('Logo Image'),
                );
        }

        if ($show_fields['title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_TITLE',
                    'label' => $this->l('Main Title'),
                    'lang' => true,
                );
        }

        if ($show_fields['short_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_SUB_DESCRIPTION',
                    'label' => $this->l('Short Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'textarea',
                    'name' => 'TVCMSMAP_DESCRIPTION',
                    'label' => $this->l('Description'),
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                    'class' => 'rte',
                    'autoload_rte' => true,
                );
        }

        if ($show_fields['btn_title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_BTN_TITLE',
                    'label' => $this->l('Button Title'),
                    'lang' => true,
                );
        }
        

        if ($show_fields['api_key']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_API_KEY',
                    'label' => $this->l('Api Key'),
                );
        }

        if ($show_fields['map_type']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'select',
                    'name' => 'TVCMSMAP_MAP_TYPE',
                    'label' => $this->l('Map Type'),
                    'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'roadmap',
                                    'name' => 'Roadmap'
                                ),
                                array(
                                    'id_option' => 'satellite',
                                    'name' => 'Satellite'
                                ),
                                array(
                                    'id_option' => 'hybrid',
                                    'name' => 'Satellite with Street Names'
                                ),
                                array(
                                    'id_option' => 'terrain',
                                    'name' => 'Terrain'
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                );
        }

        if ($show_fields['zoom']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'select',
                    'name' => 'TVCMSMAP_ZOOM',
                    'label' => $this->l('Zoom'),
                    'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => '2',
                                    'name' => '2000 km'
                                    ),
                                array(
                                    'id_option' => '3',
                                    'name' => '1000 km'
                                    ),
                                array(
                                    'id_option' => '4',
                                    'name' => '400 km'
                                    ),
                                array(
                                    'id_option' => '5',
                                    'name' => '200 km'
                                    ),
                                array(
                                    'id_option' => '6',
                                    'name' => '100 km'
                                    ),
                                array(
                                    'id_option' => '7',
                                    'name' => '50 km'
                                    ),
                                array(
                                    'id_option' => '8',
                                    'name' => '30 km'
                                    ),
                                array(
                                    'id_option' => '9',
                                    'name' => '15 km'
                                    ),
                                array(
                                    'id_option' => '10',
                                    'name' => '8 km'
                                    ),
                                array(
                                    'id_option' => '11',
                                    'name' => '4 km'
                                    ),
                                array(
                                    'id_option' => '12',
                                    'name' => '2 km'
                                    ),
                                array(
                                    'id_option' => '13',
                                    'name' => '1 km'
                                    ),
                                array(
                                    'id_option' => '14',
                                    'name' => '400 m'
                                    ),
                                array(
                                    'id_option' => '15',
                                    'name' => '200 m'
                                    ),
                                array(
                                    'id_option' => '16',
                                    'name' => '100 m'
                                    ),
                                array(
                                    'id_option' => '17',
                                    'name' => '50 m'
                                    ),
                                array(
                                    'id_option' => '18',
                                    'name' => '20 m'
                                    ),
                                array(
                                    'id_option' => '19',
                                    'name' => '10 m'
                                    ),
                                array(
                                    'id_option' => '20',
                                    'name' => '5 m'
                                    ),
                                array(
                                    'id_option' => '21',
                                    'name' => '2.5 m'
                                    ),
                                ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                );
        }

        if ($show_fields['letitude']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_LETITUDE',
                    'label' => $this->l('Letitude'),
                );
        }

        if ($show_fields['longitude']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSMAP_LONGITUDE',
                    'label' => $this->l('Longitude'),
                );
        }

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Map'),
                'icon' => 'icon-support',
                ),
                'input' => $input,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitvcmsMapForm',
                ),
            ),
        );
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->name == Tools::getValue('configure')) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }//hookDisplayBackOfficeHeader()

    protected function getConfigFormValues()
    {
        $fields = array();
        $languages = Language::getLanguages();
        
        foreach ($languages as $lang) {
            $tmp = Configuration::get('TVCMSMAP_MAIN_TITLE', $lang['id_lang']);
            $fields['TVCMSMAP_MAIN_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_MAIN_SUB_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSMAP_MAIN_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_MAIN_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSMAP_MAIN_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_MAIN_BTN_TITLE', $lang['id_lang']);
            $fields['TVCMSMAP_MAIN_BTN_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_MAIN_IMG', $lang['id_lang']);
            $fields['TVCMSMAP_MAIN_IMG'][$lang['id_lang']] = $tmp;


            $tmp = Configuration::get('TVCMSMAP_TITLE', $lang['id_lang']);
            $fields['TVCMSMAP_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_SUB_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSMAP_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSMAP_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_BTN_TITLE', $lang['id_lang']);
            $fields['TVCMSMAP_BTN_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSMAP_IMG', $lang['id_lang']);
            $fields['TVCMSMAP_IMG'][$lang['id_lang']] = $tmp;
        }


   
        $fields['TVCMSMAP_API_KEY'] = Configuration::get('TVCMSMAP_API_KEY');
        $fields['TVCMSMAP_MAP_TYPE'] = Configuration::get('TVCMSMAP_MAP_TYPE');
        $fields['TVCMSMAP_ZOOM'] = Configuration::get('TVCMSMAP_ZOOM');
        $fields['TVCMSMAP_LETITUDE'] = Configuration::get('TVCMSMAP_LETITUDE');
        $fields['TVCMSMAP_LONGITUDE'] = Configuration::get('TVCMSMAP_LONGITUDE');

        // $data = array();
        // $data['tvmap_type'] = Configuration::get('TVCMSMAP_MAP_TYPE');
        // $data['tvmap_zoom'] = Configuration::get('TVCMSMAP_ZOOM');
        // $data['tvmap_letitude'] = Configuration::get('TVCMSMAP_LETITUDE');
        // $data['tvmap_longitude'] = Configuration::get('TVCMSMAP_LONGITUDE');



        $path = _MODULE_DIR_.$this->name."/views/img/";
        $this->context->smarty->assign("path", $path);
        return $fields;
    }

    public function hookdisplayHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }
    public function hookdisplayTopColumn()
    {
        return $this->hookDisplayHome();
    }

    public function hookdisplayFooterBefore()
    {
        return $this->hookDisplayHome();
    }
    public function hookdisplayWrapperBottom()
    {
        return $this->hookdisplayMapInformation();
    }
    

    public function hookdisplayShowMap()
    {
        $cookie = Context::getContext()->cookie;
        $id_lang = $cookie->id_lang;
      
        $path = _MODULE_DIR_.$this->name."/views/img/";
        $this->context->smarty->assign("path", $path);
        
        $this->context->smarty->assign('id_lang', $id_lang);
        $show_map = Configuration::get('TVCMSMAP_API_KEY');
        
        $this->context->smarty->assign('show_map', $show_map);




        return $this->display(__FILE__, 'views/templates/front/display_map.tpl');
    }

    public function hookdisplayMapInformation()
    {
        $cookie = Context::getContext()->cookie;
        $id_lang = $cookie->id_lang;
      
        $path = _MODULE_DIR_.$this->name."/views/img/";
        $this->context->smarty->assign("path", $path);
        
        $this->context->smarty->assign('id_lang', $id_lang);
        return $this->display(__FILE__, 'views/templates/front/display_home.tpl');
    }
}
