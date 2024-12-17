<?php
/**
* 2007-2019 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductQuotationAjaxModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function init()
    {
        parent::init();
        require_once($this->module->getLocalPath().'model/QuoteModel.php');
    }

    public function initContent()
    {
        parent::initContent();
        $obj = new Quote;
        $id_product = (int)Tools::getValue('id_product');
        $action = Tools::getValue('action');
        $cid = (int)Tools::getValue('comb_id');
        $qty = (int)Tools::getValue('quantity');
        $tax_price = (int)Configuration::get('PQUOTE_TAX');
        $id_lang = (int)$this->context->language->id;
        $id_quote = (int)$this->context->cookie->id_quote;
        $image_name = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ?
        ImageType::getByNameNType(ImageType::getFormattedName('small')) :
        ImageType::getByNameNType(ImageType::getFormatedName('small'));

        $coo = new Cookie('psFront');
        $id_employee = (int)$coo->__get('id_employee');
        $valid_token = Tools::getAdminToken('Ajax'.(Tab::getIdFromClassName('Ajax')).$id_employee);
        if ($action == 'add') {
            $token = Tools::getValue('token');
            if ($valid_token == $token) {
                if (!$id_quote) {
                    $id = $obj->insertNewQuote($id_lang);
                    $obj->saveNewQuoteData($id, $id_product, $cid, $qty);
                    $this->context->cookie->id_quote = (int)$id;
                   
                    $products = $obj->getQuoteProducts($id, $tax_price);
                    
                    $this->context->smarty->assign(array(
                        'products' => $products,
                        'image_name' => $image_name,
                        'action' => $action
                    ));
                    echo ($this->context->smarty->fetch(_PS_MODULE_DIR_ .'productquotation/views/templates/front/test.tpl'));
                } elseif ($id_quote > 0) {
                    $id = (int)$this->context->cookie->id_quote;
                    $pre_exist = (int)$obj->checkPreExistance($id, $id_product, $cid);
                    if ($pre_exist <= 0) {
                        $obj->saveNewQuoteData($id, $id_product, $cid, $qty);
                      
                        $products = $obj->getQuoteProducts($id, $tax_price);

                        $this->context->smarty->assign(array(
                            'products' => $products,
                            'image_name' => $image_name,
                            'action' => $action
                        ));
                        echo ($this->context->smarty->fetch(_PS_MODULE_DIR_ .'productquotation/views/templates/front/test.tpl'));
                    } else {
                        echo '-1';
                    }
                }
            } else {
                echo $this->module->l('Invalid Token');
            }
        } elseif ($id_quote > 0 && $action == 'count') {
            $token = Tools::getValue('token');
            if ($valid_token == $token) {
                echo $obj->getCount($id_quote);
            } else {
                echo $this->module->l('Invalid Token');
            }
        } elseif ($id_quote > 0 && $action == 'update') {
            $token = Tools::getValue('token');
            if ($valid_token == $token) {
                $qty_updt = (int)Tools::getValue('qty');
                $qid = (int)Tools::getValue('quote_id');
                $obj->updateQuoteProductQty($qid, $qty_updt);
                die($obj->getQuoteProductsTotal($id_quote, $tax_price));
            } else {
                echo $this->module->l('Invalid Token');
            }
        } elseif ($id_quote > 0 && $action == 'check') {
            $token = Tools::getValue('token');
            if ($valid_token == $token) {
                $id_product = (int)Tools::getValue('id_product');
                $id_comb = (int)Tools::getValue('id_comb');
                $result = StockAvailable::getQuantityAvailableByProduct($id_product, $id_comb);
                echo($result);
            } else {
                echo $this->module->l('Invalid Token');
            }
        } elseif ($id_quote > 0 && $action == 'check_16') {
            $token = Tools::getValue('token');
            if ($valid_token == $token) {
                $id_product = (int)Tools::getValue('id_product');
                $id_comb = (int)Tools::getValue('id_comb');
                $result = StockAvailable::getQuantityAvailableByProduct($id_product, $id_comb);
                echo($result);
            } else {
                echo $this->module->l('Invalid Token');
            }
        } elseif ($id_quote > 0 && $action == 'html') {
            $token = Tools::getValue('token');
            if ($valid_token == $token) {
                $id = (int)$this->context->cookie->id_quote;
                
                $products = $obj->getQuoteProducts($id, $tax_price);
                $this->context->smarty->assign(array(
                    'products' => $products,
                    'image_name' => $image_name,
                    'action' => $action
                ));
                echo ($this->context->smarty->fetch(_PS_MODULE_DIR_ .'productquotation/views/templates/front/test.tpl'));
            } else {
                echo $this->module->l('Invalid Token');
            }
        } elseif ($action == 'delete') {
            $token = Tools::getValue('token');
            if ($valid_token == $token) {
                $id = (int)Tools::getValue('quote_id');
                $obj->dropQuote($id);
                $products = $obj->getQuoteProducts($id_quote, $tax_price);

                $this->context->smarty->assign(array(
                    'products' => $products,
                    'image_name' => $image_name,
                    'action' => $action
                ));

                echo ($this->context->smarty->fetch(_PS_MODULE_DIR_ .'productquotation/views/templates/front/test.tpl'));
            } else {
                echo $this->module->l('Invalid Token');
            }
        }
        die();
    }
}
