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

class HTMLTemplateCustomPdf extends HTMLTemplate
{
    //public $custom_temp;

    public $id_quote;

    public $id_quotation;

    public function __construct($custom_object, $smarty)
    {
        //$this->custom_temp = $custom_object;
        $this->id_quote = $custom_object->id_quote;
        $this->id_quotation = $custom_object->id_quotation;
        $this->smarty = $smarty;
        $this->id_lang = Context::getContext()->language->id;
        $this->title = HTMLTemplateCustomPdf::l('Custom Title');
        $this->shop = new Shop(Context::getContext()->shop->id);
    }

    public function getContent()
    {
        $id_quote = $this->id_quote;
        $id_quotation = $this->id_quotation;
        $obj = new Quote;
        $tax_status = (int) Configuration::get('PQUOTE_TAX');
        $customer_id = (int) Context::getContext()->customer->id;
        $get_user_quotes = $obj->getUserBaseQuotes($customer_id);
        $quotation_static = $obj->getQuotationDetailsStatic($id_quotation);
        
        if ($id_quote > 0) {
            $get_quotation_data = $obj->getQuotationData($id_quote);
            
            $get_quotation_form_data = $obj->getQuotationFormData($id_quote);
            $fields = FieldsQuote::getCustomFields(Context::getContext()->language->id, Context::getContext()->shop->id);

            if (!count($fields)) {
                return '';
            }
            $groupedFields = array();
            
            $user_data = array();
            foreach ($fields as $key => $val) {
                $id_customer = (isset(Context::getContext()->customer->id))?Context::getContext()->customer->id : Context::getContext()->cookie->id_customer;
                $id_guest = (!$id_customer)? Context::getContext()->cookie->id_guest : 0;

                $user_data[$key]['id'] = $fields[$key]['id_fmm_quote_fields'];
                $user_data[$key]['field'] = $fields[$key]['field_name'];

                $field_type = $fields[$key]['field_type'];
                if ($field_type == 'attachment') {
                    $user_data[$key]['callback'] = 'downloadFile';
                } else if ($field_type == 'image') {
                    $user_data[$key]['callback'] = 'image';
                } else {
                    $user_data[$key]['callback'] = '';
                }

                $record = FieldsQuote::getFormatedValue($val, null, $id_customer, $id_guest, $id_quote);
                $user_data[$key]['value'] = $record;
            }


            $currency = new Currency($quotation_static['id_currency']);//$currency = Currency::getCurrency($quotation_static['id_currency']);
            $products = $obj->getQuoteProductsSubmitted($id_quote, $currency, $tax_status);
            $products_total = $obj->getQuoteProductsTotalSubmitted($id_quote, $currency, $tax_status);
            $key = $obj->getKey($id_quotation);
            $voucher_details = $obj->getVoucherDetails($id_quotation);
            $this->smarty->assign(array(
                'quotes_data' => $get_quotation_data,
                'products' => $products,
                'total' => $products_total,
                'key' => $key,
                'user_data' => $user_data,
                'id_quotation' => $id_quotation,
                'version' => _PS_VERSION_,
                'voucher' => $voucher_details,
                'link' => Context::getContext()->link,
            ));
        }
        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'productquotation/views/templates/front/pdf.tpl'
        );
    }
    
    public function getHeader()
    {
        $id_shop = (int) $this->shop->id;
        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $logo = _PS_IMG_DIR_.$logo;
        }

        $this->smarty->assign(array(
            'logo' => $logo,
        ));

        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'productquotation/views/templates/front/ticket_header.tpl'
        );
    }

    public function getFilename()
    {
        return 'ticket_pdf.pdf';
    }

    public function getBulkFilename()
    {
        return 'ticket_pdf.pdf';
    }

    public function getFooter()
    {
        return $this->smarty->fetch(
            _PS_MODULE_DIR_ . 'productquotation/views/templates/front/ticket_footer.tpl'
        );
    }
}
