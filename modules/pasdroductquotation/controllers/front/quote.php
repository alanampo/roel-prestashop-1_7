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

class ProductQuotationQuoteModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function _initContent()
    {
        parent::initContent();
        $obj = new Quote;
        $tax_status = (int)Configuration::get('PQUOTE_TAX');
        $shop_ids = Shop::getContextListShopID();
        $id_lang = (int)$this->context->language->id;
        $id_quote = $this->context->cookie->id_quote;
        $products = $obj->getQuoteProducts($id_quote, $tax_status);

        $products_total = $obj->getQuoteProductsTotal($id_quote, $tax_status);
        $form = $obj->getForm($id_lang, $shop_ids);
        $form_action = $this->context->link->getModuleLink('productquotation', 'quote');
        $success = (int)Tools::getValue('success');
        $id_module = $this->module->id;
        $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $email = Context::getContext()->cookie->email;

        $coo = new Cookie('psFront');
        $id_employee = (int)$coo->__get('id_employee');
        $token = Tools::getAdminToken('Ajax'.(Tab::getIdFromClassName('Ajax')).($id_employee));

        $this->context->smarty->assign(array(
            'id_quote' => (int)$id_quote,
            'products' => $products,
            'total' => $products_total,
            'path_uri' => $this->module->getPathUri(),
            'forms' => $form,
            'form_action' => $form_action,
            'success' => $success,
            'id_module' => $id_module,
            'id_language' => $id_lang,
            'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
            'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,
            'force_ssl' => $force_ssl,
            'token' => $token,
            'email' => $email
        ));
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            return $this->setTemplate('module:productquotation/views/templates/front/quote-17.tpl');
        } else {
            $this->setTemplate('quote.tpl');
        }
    }

    public function initContent()
    {
        parent::initContent();

        $id_lang = $this->context->language->id;
        $objModel = new FieldsQuote();
        $quote_fields = $objModel->getCustomFields($id_lang);
        $id_customer = (isset($this->context->customer->id))?$this->context->customer->id : $this->context->cookie->id_customer;
        $id_guest = (!$id_customer)? $this->context->cookie->id_guest : 0;
        $field = new FieldsQuote();
        $fields = array();
        if ($id_customer || $id_guest) {
            $fields = $field->getAllFields(
                (($id_customer)? 'val.id_customer = '.(int)$id_customer : 'val.id_guest = '.(int)$id_guest),
                $id_lang,
                'a.position'
            );
            if (isset($fields) && $fields) {
                foreach ($fields as &$field) {
                    if (in_array($field['field_type'], array('multiselect', 'radio', 'checkbox')) && $field['field_value_id']) {
                        $field['field_value'] = explode(',', $field['field_value_id']);
                    } elseif (in_array($field['field_type'], array('message', 'select'))  && $field['field_value_id']) {
                        $field['field_value'] = $field['field_value_id'];
                    }
                }
            }
        }

        $quote_fields_options = array();
        foreach ($quote_fields as $sf) {
            $quote_fields_options[$sf['id_fmm_quote_fields']] = $objModel->getCustomFieldsValues($sf['id_fmm_quote_fields']);
        }
        if (isset($quote_fields) && !empty($quote_fields)) {
            foreach ($quote_fields as &$field) {
                if (isset($field['id_heading']) && (int)$field['id_heading'] > 0) {
                    $field['sub_heading'] = $objModel->getSubHeading($field['id_heading'], $id_lang);
                } else {
                    $field['sub_heading'] = '';
                }
            }
        }

        $obj = new Quote;
        $tax_status = (int)Configuration::get('PQUOTE_TAX');
        $shop_ids = Shop::getContextListShopID();
        $id_lang = (int)$this->context->language->id;
        $id_quote = $this->context->cookie->id_quote;
        $products = $obj->getQuoteProducts($id_quote, $tax_status);
        
        $products_total = $obj->getQuoteProductsTotal($id_quote, $tax_status);
        $form = $obj->getForm($id_lang, $shop_ids);
        $form_action = $this->context->link->getModuleLink('productquotation', 'quote');
        $success = (int)Tools::getValue('success');
        $id_module = $this->module->id;
        $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $email = Context::getContext()->cookie->email;

        $coo = new Cookie('psFront');
        $id_employee = (int)$coo->__get('id_employee');
        $token = Tools::getAdminToken('Ajax'.(Tab::getIdFromClassName('Ajax')).($id_employee));
        
        $this->context->smarty->assign(array(
            'id_quote' => (int)$id_quote,
            'products' => $products,
            'total' => $products_total,
            'path_uri' => $this->module->getPathUri(),
            'forms' => $form,
            'form_action' => $form_action,
            'success' => $success,
            'id_module' => $id_module,
            'id_language' => $id_lang,
            'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
            'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,
            'force_ssl' => $force_ssl,
            'token' => $token,
            'email' => $email
        ));

        if ($quote_fields_options) {
            $this->context->smarty->assign('version', _PS_VERSION_);
            $this->context->smarty->assign('id_guest', $id_guest);
            $this->context->smarty->assign('id_customer', $id_customer);
            $this->context->smarty->assign('id_lang', $this->context->cookie->id_lang);
            $this->context->smarty->assign('summary_fields_values', $quote_fields_options);
            $this->context->smarty->assign('summary_fields', $quote_fields);
            $this->context->smarty->assign('value_reg_fields', $fields);
            $this->context->smarty->assign('is_psgdpr', (Module::isInstalled('psgdpr') && Module::isEnabled('psgdpr')));
            $this->context->smarty->assign('quote_FIELDS_HEADING', Configuration::get('quote_FIELDS_HEADING', $this->context->language->id, $this->context->shop->id_shop_group, $this->context->shop->id));
            $this->context->smarty->assign('actionLink', $this->context->link->getModuleLink('productquotation', 'ajax', array('action' => 'download', 'me' => base64_encode($id_customer)), true));
            $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
            $this->context->smarty->assign('ps_17', (int)$ps_17);
            if ($ps_17 > 0) {
                $this->context->smarty->assign('base_dir', _PS_BASE_URL_.__PS_BASE_URI__);
            }
        }
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            return $this->setTemplate('module:productquotation/views/templates/front/newquote-17.tpl');
        } else {
            $this->setTemplate('newquote-16.tpl');
        }
    }

    public function postProcess()
    {
        $obj = new Quote;
        $id_quote = $this->context->cookie->id_quote;

        $new_link = $this->context->link->getModuleLink('productquotation', 'quote').'?success=1';
        $as_quote = (int)Tools::getValue('asquote');
        
        $id_lang = (int)$this->context->language->id;
        $id_currency = (int)$this->context->currency->id;

        if (Tools::isSubmit('submitQuote')) {
            if (Tools::getValue('fields')) {
                $id_customer = $this->context->customer->id;
                $objModel = new FieldsQuote();
                $id_guest = FieldsQuote::getGuestId($id_customer);
                
                $result = $objModel->saveFieldValues(Tools::getValue('fields'), $id_customer, $id_quote);
                
                if (isset($result) && $result) {
                    if ($result['result'] == false && isset($result['errors'])) {
                        foreach ($result['errors'] as $error) {
                            $this->context->controller->errors[] = $error;
                            return true;
                        }
                        $this->context->controller->errors;
                    }
                }
            }

            $email = Tools::getValue('email');
            $template = Tools::getValue('quote_template');
            $customer_id = (int)$this->context->customer->id;
            $key_code = md5($email);
            
            $obj->saveQuote(
                $id_quote,
                $email,
                $template,
                $customer_id,
                $id_currency,
                $key_code
            );
            
            foreach ($_POST as $key => $val) {
                if (!in_array($key, ['quote_template', 'submitQuote', 'email', 'fields', 'errors'])) {
                    $obj->saveQuoteData((int)$id_quote, $key, $val);
                }
            }

            $this->context->cookie->id_quote = 0;
            $send_mail_admin = (int)Configuration::get('PQUOTE_SENDMAIL_ADMIN');
            $id_quotation = (int)$obj->getQoutationIdByQuote($id_quote);
            if ($send_mail_admin > 0) {
                $this->sendMailToAdmin($id_quote, $id_quotation, $email);
            }
            $this->sendMailToUser($id_quotation, $email, $key_code);
            Tools::redirect($new_link);
        } elseif ($as_quote > 0) {
            $quote_link = $this->context->link->getModuleLink('productquotation', 'quote');
            $id_cart = (int)$this->context->cart->id;
            if ($id_cart <= 0) {
                $this->errors[] = $this->module->l('Cart is empty, no products to add.');
            } else {
                $cart = new Cart($id_cart);
                $cart_products = $cart->getProducts();
                if (empty($cart_products)) {
                    $this->errors[] = $this->module->l('Cart is empty, no products to add.');
                } else {
                    $id_quote = (int)$this->context->cookie->id_quote;
                    if (!$id_quote) {
                        $id = $obj->insertNewQuote($id_lang);
                        foreach ($cart_products as $row) {
                            $obj->saveNewQuoteData(
                                $id,
                                $row['id_product'],
                                $row['id_product_attribute'],
                                $row['cart_quantity']
                            );
                        }
                        $this->context->cookie->id_quote = (int)$id;
                        Tools::redirect($quote_link);
                    } elseif ($id_quote > 0) {
                        $id = (int)$this->context->cookie->id_quote;
                        foreach ($cart_products as $row) {
                            $pre_exist = (int)$obj->checkPreExistance(
                                $id,
                                $row['id_product'],
                                $row['id_product_attribute']
                            );
                            if ($pre_exist <= 0) {
                                $obj->saveNewQuoteData(
                                    $id,
                                    $row['id_product'],
                                    $row['id_product_attribute'],
                                    $row['cart_quantity']
                                );
                            }
                        }
                        Tools::redirect($quote_link);
                    }
                }
            }
        }
    }

    private function sendMailToAdmin($id, $id_quotation, $email)
    {
        $employee = new Employee(1);
        $admin_email = Configuration::get('PQUOTE_ADMIN_EMAIL');
        $admin_email = (empty($admin_email)) ? $employee->email : $admin_email;
        $id_lang = (int)$this->context->language->id;

        $vars = array(
            '{id}' => $id,
            '{email}' => $email
        );

        // send quote attachment in email 
        $file_attachement = [];
        $file_attachement['mime'] = 'application/pdf';
        $file_attachement['name'] = sprintf('quote_%d.pdf', $id);
        $file_attachement['content'] = $this->module->generateQuotePdf($id, $id_quotation);

        return Mail::Send(
            (int) $id_lang,
            'notifyadmin',
            Mail::l('Quote Received', (int)$id_lang),
            $vars,
            $admin_email,
            null,
            null,
            $this->context->shop->name,
            $file_attachement,
            null,
            _PS_MODULE_DIR_.'productquotation/mails/',
            false,
            $this->context->shop->id
        );
    }

    private function sendMailToUser($id_quotation, $email, $key)
    {
        $id_lang = (int)$this->context->language->id;
        $template = 'notifyuser';
        $message = Configuration::get('PQUOTE_MESSAGE', $id_lang);
        $heading = Mail::l('Your Quote Details', (int)$id_lang);
        $message_link = $this->context->link->getModuleLink(
            'productquotation',
            'messages'
        ).'?id_quotation='.$id_quotation.'&key='.$key;
        $vars = array('{link_message}' => $message_link, '{message}' => $message);
        return Mail::Send(
            (int)$id_lang,
            $template,
            $heading,
            $vars,
            $email,
            null,
            null,
            $this->context->shop->name,
            null,
            null,
            _PS_MODULE_DIR_.'productquotation/mails/',
            false,
            1
        );
    }
}
