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

include_once(dirname(_PS_MODULE_DIR_).'/modules/productquotation/HTMLTemplateCustomPdf.php');
class ProductQuotationQuotationsModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initContent()
    {
        parent::initContent();
        if (Tools::isSubmit('downloadFile')) {
            $link = base64_decode(Tools::getValue('l'));
            
            $file = FieldsQuote::actionDownload($link);
        }
        $id_quote = (int) Tools::getValue('id_quote');
        $id_quotation = (int)Tools::getValue('id_quotation');
        if (Tools::getValue('pdf')) {
            $this->module->generateQuotePdf($id_quote, $id_quotation);
            exit();
        }
        if (!Context::getContext()->customer->isLogged()) {
            Tools::redirect(
                'index.php?controller=authentication&redirect=module&module=productquotation&action=quotations'
            );
        }
        $obj = new Quote;
        $tax_status = (int)Configuration::get('PQUOTE_TAX');
        $customer_id = (int)$this->context->customer->id;

        $get_user_quotes = $obj->getUserBaseQuotes($customer_id);
        $id_quote = (int)Tools::getValue('id_quote');

        $id_quotation = (int)Tools::getValue('id_quotation');
        $quotation_static = $obj->getQuotationDetailsByQuote($id_quote);
        $file_path = '';
        $file_data = '';
        
        foreach ($get_user_quotes as $key => $value) {
            $id_productquotation = $value['id_productquotation'];
            $id_customer = $value['id_customer'];
            $file_data = FieldsQuote::getFileData($id_productquotation, $id_customer);
            $file_path = _PS_BASE_URL_._PS_IMG_.'quote/'.$file_data;
            $get_user_quotes[$key]['file_path']= $file_path;
            $get_user_quotes[$key]['file_name']= $file_data;
        }
        $this->context->smarty->assign(array(
            'file_path' => $file_path,
            'file_data' => $file_data
        ));

        if ($id_quote > 0) {
            $get_quotation_data = $obj->getQuotationData($id_quote);
            $get_quotation_form_data = $obj->getQuotationFormData($id_quote);
            $fields = FieldsQuote::getCustomFields($this->context->language->id, $this->context->shop->id);

            //$fields = FieldsQuote::getAllFields('val.id_customer = 4', null, 'a.position');
            if (!count($fields)) {
                return '';
            }
            $groupedFields = array();

            $user_data = array();
            foreach ($fields as $key => $val) {
                $id_customer = (isset($this->context->customer->id))?$this->context->customer->id : $this->context->cookie->id_customer;
                $id_guest = (!$id_customer)? $this->context->cookie->id_guest : 0;

                $user_data[$key]['id'] = $fields[$key]['id_fmm_quote_fields'];
                $user_data[$key]['field'] = $fields[$key]['field_name'];
                // $value = $get_quotation_form_data[$key]['value'];

                $field_type = $fields[$key]['field_type'];
                if ($field_type == 'attachment') {
                    $user_data[$key]['callback'] = 'downloadFile';
                } else if ($field_type == 'image') {
                    $user_data[$key]['callback'] = 'image';
                } else {
                    $user_data[$key]['callback'] = '';
                }
                
                // if (!$value) {
                //     $field_value_id = $get_quotation_form_data[$key]['field_value_id'];
                //     $fielder = new FieldsQuote;
                //     $new_val = $fielder->getFieldsValueById($field_value_id);

                //     $user_data[$key]['value'] = $new_val;
                // } else {
                //     $user_data[$key]['value'] = $get_quotation_form_data[$key]['value'];
                // }

                $record = FieldsQuote::getFormatedValue($val, null, $id_customer, $id_guest, $id_quote);
                $user_data[$key]['value'] = $record;
            }
            
            $currency = Currency::getCurrency($quotation_static['id_currency']);
            $currency = new Currency($quotation_static['id_currency']);
         
            $products = $obj->getQuoteProductsSubmitted($id_quote, $currency, $tax_status);
            $products_total = $obj->getQuoteProductsTotalSubmitted($id_quote, $currency, $tax_status);
            $key = $obj->getKey($id_quotation);
            $voucher_details = $obj->getVoucherDetails($id_quotation);
            $coupon_id = $voucher_details['coupon_id'];
            $cart_result = new CartRule($coupon_id);

            $this->context->smarty->assign(array(
            'quotes_data' => $get_quotation_data,
            'products' => $products,
            'total' => $products_total,
            'key' => $key,
            'currencyy' => $currency->iso_code,
            'user_data' => $user_data,
            'id_quotation' => $id_quotation,
            'cart_result' => (int)$cart_result->reduction_amount,
            'root' => _PS_ROOT_DIR_,
            'voucher' => $voucher_details,
            'id_quote' => $id_quote
            ));
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $this->setTemplate('module:productquotation/views/templates/front/account_details-17.tpl');
            } else {
                $this->setTemplate('account_details.tpl');
            }
        } else {
            $this->context->smarty->assign(array(
            'quotes' => $get_user_quotes
            ));
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $this->setTemplate('module:productquotation/views/templates/front/account-17.tpl');
            } else {
                $this->setTemplate('account.tpl');
            }
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $page_meta = Meta::getMetaByPage('my-account', $this->context->language->id);
        $page_meta = $page_meta['title'];
        $id = (int)Tools::getValue('id_quote');
        $meta_title = Configuration::get('PQUOTE_HEAD', $this->context->language->id);
        $meta_title = empty($meta_title) ? 'My Quotations' : $meta_title;
        $breadcrumb['links'][] = array(
        'title' => $page_meta,
        'url' => $this->context->link->getPageLink('my-account'),
        );
        if ($id > 0) {
            $breadcrumb['links'][] = array(
                'title' => $meta_title,
                'url' => $this->context->link->getModuleLink('productquotation', 'quotations'),
            );
        }
        return $breadcrumb;
    }
}
