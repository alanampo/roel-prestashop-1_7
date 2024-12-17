<?php
/**
* Product Quotation
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules All right reserved
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category  front_office_features
* @package   productquotation
*/

class AdminProductQuotationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'Quote';
        $this->table = 'productquotation_templates';
        $this->deleted = false;
        $this->identifier = 'id_productquotation_templates';
        $this->lang = true;
        $this->bootstrap = true;
        $this->explicitSelect = true;
        $this->context = Context::getContext();
        parent::__construct();
        $this->fields_list = array(
            'id_productquotation_templates' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 30),
            'title' => array('title' => $this->l('Title'), 'align' => 'center'),
            'status' => array('title' => $this->l('Enabled'), 'align' => 'center', 'type' => 'bool',
            'callback' => 'getStatus')
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        $edit_data = array('status' => '', 'title' => '', 'form' => '');
        $langs_data = array();
        $shop_data = array();
        $langs = Language::getLanguages();
        $shops = Shop::getShops(true, null, false);
        $id = (int)Tools::getValue('id_productquotation_templates');
        if ($id > 0) {
            $obj = new Quote;
            $edit_data = $obj->getAllEditData($id);
            $edit_data = array_shift($edit_data);
            $langs_data = $obj->getAllEditDataLang($id);
            $shop_data = $obj->getAllEditDataShop($id);
        }
        $this->context->smarty->assign(array(
            'id' => $id,
            'data' => $edit_data,
            'lang_data' => $langs_data,
            'shop_data' => $shop_data,
            'languages' => $langs,
            'shops' => $shops,
            'action' => self::$currentIndex.'&token='.$this->token,
            'pq_url' => $this->module->getPathUri()
        ));

        parent::renderForm();
        return $this->context->smarty->fetch(dirname(__FILE__).
        '/../../views/templates/admin/product_quotation/helpers/form/view.tpl');
    }

    public function init()
    {
        parent::init();
        require_once($this->module->getLocalPath().'model/QuoteModel.php');
    }

    public function postProcess()
    {
        $obj = new Quote;
        $id = (int)Tools::getValue('id_productquotation_templates');
        $shops = Tools::getValue('shops');
        $status = (int)Tools::getValue('status');
        $langs = Tools::getValue('langs');
        $title = Tools::getValue('title');
        $content = Tools::getValue('template_content');
        if (Tools::isSubmit('submitTemplates')) {
            if ($id > 0) {
                $obj->resetShopsLangs($id);
                $obj->changeNewTemplate($id, $status);
                $obj->addNewTemplateLangs($id, $title, $content, $langs);
                $obj->addNewTemplateShops($id, $shops);
            } else {
                if (empty($langs)) {
                    $this->errors[] = $this->l('Not Saved: Please select language options');
                } elseif (empty($shops)) {
                    $this->errors[] = $this->l('Not Saved: Please select shop options');
                } elseif (empty($title)) {
                    $this->errors[] = $this->l('Not Saved: Title cannot be empty');
                } else {
                    $id = $obj->addNewTemplate($status);
                    $obj->addNewTemplateLangs($id, $title, $content, $langs);
                    $obj->addNewTemplateShops($id, $shops);
                }
            }
        }
        parent::postProcess();
    }

    public function getStatus($id)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return $id;
        } else {
            if ((int)$id == 0) {
                $return = $this->l('No');
            } elseif ((int)$id == 1) {
                $return = $this->l('Yes');
            }
            return $return;
        }
    }
}
