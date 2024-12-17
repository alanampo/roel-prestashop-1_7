<?php
/**
* 2007-2015 PrestaShop
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminProductQuotesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'productquotation';
        $this->identifier = 'id_productquotation';
        parent::__construct();
    }

    public function renderList()
    {
        parent::renderList();
        $obj = new Quote;
        $get_count = (int)$obj->getQuotesCount();
        $get_quotes = $obj->getUserQuotes();
        $this->context->smarty->assign(array(
            'action' => self::$currentIndex.'&token='.$this->token,
            'count' => $get_count,
            'quotes' => $get_quotes,
            'gify_url' => $this->module->getPathUri().'views/img/load.gif',
        ));
        return $this->context->smarty->fetch(dirname(__FILE__).
        '/../../views/templates/admin/product_quotation/helpers/form/list.tpl');
    }

    public function renderView()
    {
        parent::renderView();
        $obj = new Quote;
        $id = (int)Tools::getValue('id_productquotation');
        

        $quotation_static = $obj->getQuotationDetailsStatic($id);
        $id_quote = $obj->getIdQuote($id);

        $tax_status = (int)Configuration::get('PQUOTE_TAX');

        $get_quotation_data = $obj->getQuotationData($id_quote);

        $get_quotation_form_data = $obj->getQuotationFormData($id_quote);
        $fields = FieldsQuote::getCustomFields($this->context->language->id, $this->context->shop->id);
        $summary_fields = FieldsQuote::getCustomFields($this->context->language->id, $this->context->shop->id);
        $id_customer = (isset($this->context->customer->id))?$this->context->customer->id : $this->context->cookie->id_customer;
        $id_guest = (!$id_customer)? $this->context->cookie->id_guest : 0;
        
        $id_cust = $quotation_static['id_customer'];
        $file_data = FieldsQuote::getFileData($id, $id_cust);
        $fee_name = FieldsQuote::getFeeProductName($id);
        $fee_price = FieldsQuote::getFeeProductIdPrice($id);
    
        $file_path = _PS_BASE_URL_._PS_IMG_.'quote/'.$file_data;
        $user_data = array();
       
        foreach ($fields as $key => $val) {
            $user_data[$key]['id'] = $fields[$key]['id_fmm_quote_fields'];
            $user_data[$key]['field'] = $fields[$key]['field_name'];

            $user_data[$key]['allval'] = FieldsQuote::getCustomFieldsValues($fields[$key]['id_fmm_quote_fields']);

            $user_data[$key]['field_type'] = $fields[$key]['field_type'];

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
        $currency = new Currency($quotation_static['id_currency']);
        $products = $obj->getQuoteProductsSubmitted($id_quote, $currency, $tax_status);
        $products_total = $obj->getQuoteProductsTotalSubmitted($id_quote, $currency, $tax_status);
        $totalwithout = $obj->getQuoteProductsTotalSubmittedWithout($id_quote, $currency, $tax_status);

        $id_message = (int)$obj->getMessageId($id);
        $message_link = $this->context->link->getAdminLink('AdminQuotationMessages').
        '&viewquote_messages&id_quote_messages='.$id_message.'&id_productquotation='.$id;
        

        $coupon_id = $quotation_static['coupon_id'];
        $cart_result = new CartRule($coupon_id);
        $discoutcode = $cart_result->code;
        $reduction_amount = $cart_result->reduction_amount;
        $date_to = $cart_result->date_to;
        $this->context->smarty->assign(array(
            'action' => self::$currentIndex.'&token='.$this->token,
            'quotation_details_basic' => $quotation_static,
            'file_data' => $file_path,

            'discoutcode' => $discoutcode,
            'reduction_amount' => (int)$reduction_amount,
            'date_to' => $date_to,

            'fee_price' => $fee_price,
            'fee_name' => $fee_name,
            'file_namee' => $file_data,
            'id_quote' => $id_quote,
            'gify_url' => $this->module->getPathUri().'views/img/load.gif',
            'get_quotation_data' => $get_quotation_data,
            'user_data' => $user_data,
            'id_productquotation' => $id,
            'products' => $products,
            'total' => $products_total,
            'totalwithout' => $totalwithout,
            'currency_sign' => $currency->sign,
            'message_link' => $message_link,
            'upload_directory' => $this->getBaseLink().'upload/'
        ));
        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/product_quotation/helpers/form/form.tpl');
    }

    

    public function postProcess()
    {
        $updateQ = Tools::getValue('submitAddfmm_quote_fields');
        
        if (isset($_FILES['file_quote'])) {
            $allowed = array('jpg','png','jpeg','pdf');
            $f_sz = Tools::ps_round($_FILES['file_quote']['size'] / 1000000, 3, PS_ROUND_UP);
            $timestamp = time();
            $id_productquotation = Tools::getValue('id_productquotation');
            $id_customer = Tools::getValue('id_customer');
            $file_name = $_FILES['file_quote']['name'];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_name = pathinfo($file_name, PATHINFO_FILENAME);
            if (in_array(Tools::strtolower($extension), $allowed)) {
                move_uploaded_file(
                    $_FILES['file_quote']['tmp_name'],
                    _PS_IMG_DIR_.'quote/quote_'.$id_productquotation.$file_name.'.'.$extension
                );
                //Configuration::updateValue('FMM_API_SLIDER1_'.$id_l, 'slider1_'.$file_name.'.'.$extension);
                    //array_push($slider_message, "Slider 1");
                $file_nam = 'quote_'.$id_productquotation.$file_name.'.'.$extension;
                $is_newfile = FieldsQuote::isNewFile($id_productquotation, $id_customer);


                if ($is_newfile != 0) {
                    FieldsQuote::updateFile($id_productquotation, $id_customer, $file_nam);
                } else {
                    FieldsQuote::saveFile($id_productquotation, $id_customer, $file_nam);
                }
                
                $f_sz = Tools::ps_round($_FILES['file_quote']['size'] / 1000000, 3, PS_ROUND_UP);
                $timestamp = time();
                $file_name = $_FILES['file_quote']['name'];
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $file_name = pathinfo($file_name, PATHINFO_FILENAME);
                $extension = pathinfo($_FILES['file_quote']['name'], PATHINFO_EXTENSION);
            }
        }

        if ($updateQ == 1) {
            if (Tools::getValue('fields')) {
                $all_fileds = Tools::getValue('fields');

                $id_productquotation = Tools::getValue('id_quote');
                $objModel = new FieldsQuote();
                $result = $objModel->updateFieldValues($all_fileds, $id_productquotation);
            }
        }
        
        $obj = new Quote;
        $tax_status = (int)Configuration::get('PQUOTE_TAX');
        if (Tools::isSubmit('downloadFile')) {
            $link = base64_decode(Tools::getValue('l'));
            FieldsQuote::actionDownload($link);
        }
        if (Tools::isSubmit('send_mail_quote')) {
            $valid_too = Tools::getValue('valid_too');
            $discount_code = Tools::getValue('discount_code');
            $discount_price = Tools::getValue('discount_price');

            $total_quot = Tools::getValue('total_quot');
            $id_productquotation = Tools::getValue('id_productquotation');
            $emaill = Tools::getValue('emaill');

            $id_cart = FieldsQuote::getCartData($id_productquotation);
            
            $currency = $this->context->currency->id;
            $this->sendMailToUserByAdmn($id_cart, $total_quot, $discount_price, $discount_code, $valid_too, $currency, $emaill);
        }
        if (Tools::isSubmit('submitNewFee')) {
            $id_productquotation = Tools::getValue('id_productquotation');
            $fee_value = (int)Tools::getValue('charge_value');
            $charge_name = Tools::getValue('charge_name');

            $p_quantity = 1;
            //new product for Fee charges
            $p_name = 'Quotation Fee';
            $object = new Product();
            $id_lang = $this->context->cookie->id_lang;
            $object->price = $fee_value;
            $object->id_tax_rules_group = 0;
            $object->id_manufacturer = 0;
            $object->id_supplier = 0;
            $object->quantity = $p_quantity;
            $object->minimal_quantity = 1;
            $object->additional_shipping_cost = 0;
            $object->wholesale_price = 0;
            $object->ecotax = 0;
            $object->width = 0;
            $object->height = 0;
            $object->depth = 0;
            $object->weight = 0;
            $object->out_of_stock = 0;
            $object->active = 1;
            $object->id_category_default = 2;
            $object->available_for_order = 1;
            $object->show_price = 0;
            $object->on_sale = 0;
            $object->is_virtual = 1;
            
            $object->online_only = 1;
            $object->meta_keywords = $p_name;
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $object->name[$language['id_lang']] = $p_name;
                $object->link_rewrite[$language['id_lang']] = Tools::str2url($p_name);
            }

            $object->visibility = 'none';
            $categories = new Category($object->id_category_default, $id_lang);
            $object->category = $categories->link_rewrite;
            $object->addToCategories(2);

            $object->save();
            StockAvailable::setQuantity($object->id, 0, $p_quantity);
            Product::updateIsVirtual((int) $object->id, true);
            
            Product::updateIsVirtual((int) $object->id, true);
            $object->addToCategories(2);
            $object->update($object->id);


            $image = new Image();
            $id_product = $object->id;
            $legend = $object->link_rewrite;
            $image->id_product = (int) $id_product;
            $image->position = Image::getHighestPosition($id_product) + 1;
            Image::deleteCover((int) $id_product);
            $image->cover = true;
            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                $image->legend[$language['id_lang']] = $legend[$language['id_lang']];
            }
            $image->id_image = $image->id;
            $image->add();
            $tmp_name = tempnam(_PS_PROD_IMG_DIR_, 'PS');
            $p_img = '';
            move_uploaded_file($p_img, $tmp_name);
            StockAvailable::setQuantity($object->id, 0, $p_quantity);
            $new_path = $image->getPathForCreation();
            ImageManager::resize($tmp_name, $new_path . '.' . $image->image_format);
            $images_types = ImageType::getImagesTypes('products');
            foreach ($images_types as $imageType) {
                ImageManager::resize(
                    $tmp_name,
                    $new_path . '-' . Tools::stripslashes($imageType['name']) . '.' . $image->image_format,
                    $imageType['width'],
                    $imageType['height'],
                    $image->image_format
                );
            }


            $id_cart = FieldsQuote::getCartData($id_productquotation);
            FieldsQuote::addFeeCharge($id_productquotation, $id_cart, $object->id, $charge_name);
        }
        if (Tools::isSubmit('submitNewVoucher')) {
            $discount = Tools::getValue('discount_value');
            $id_quotation = (int)Tools::getValue('id_quotation');
            $quotation_details = $obj->getQuotationDetailsStatic($id_quotation);
            
            if (empty($discount) || $discount <= 0) {
                $this->errors[] = Tools::displayError($this->l('Not Sent: Please enter discount value.'));
            } else {
                $id_quote = (int)$obj->getIdQuote($id_quotation);
                $get_products_static = $obj->getQuoteProductsStatic($id_quote);
                $currency = Currency::getCurrency($quotation_details['id_currency']);
                $currency = new Currency($quotation_details['id_currency']);
                $product_total = $obj->getQuoteProductsTotalSubmitted($id_quote, $currency, $tax_status);
                $product_total = preg_replace('/[^0-9\.-]/', '', $product_total);
                $discount = preg_replace('/[^0-9\.-]/', '', $discount);
                $id_cart = $obj->saveCart(
                    $quotation_details['id_shop'],
                    $quotation_details['id_lang'],
                    $quotation_details['id_currency'],
                    $quotation_details['id_customer'],
                    $quotation_details['key']
                );
                foreach ($get_products_static as $product) {
                    $obj->saveCartProducts(
                        $id_cart,
                        $quotation_details['id_shop'],
                        $product['id_product'],
                        $product['combination'],
                        $product['qty']
                    );
                }

                $id_voucher = $this->generateVoucher(
                    $id_cart,
                    $product_total,
                    $quotation_details['id_currency'],
                    $discount,
                    $id_quotation,
                    $quotation_details['email']
                );
                $obj->updateQuotationState($id_quotation, $id_voucher, $id_cart);
                $obj->updateQuoteState($id_quote, 7);
            }
        }

        $status = (int)Tools::getValue('updatestatus');
        if ($status > 0) {
            $quote_id = (int)Tools::getValue('quote_id');
            $value = (int)Tools::getValue('value');
            if ($value == 6) {
                $obj->dropQuotation($quote_id);
                echo '-1';
                exit;
            } else {
                $obj->updateQuoteState($quote_id, $value);
            }
        }
        parent::postProcess();
    }

    public function generateVoucher($id_cart, $min_total, $currency, $discount, $id_quotation, $email)
    {
        $cart_rule = new CartRule();

        $tax_status = (int)Configuration::get('PQUOTE_TAX');
        if ($tax_status == 0) {
            $tax = false;
        } else {
            $tax = true;
        }

        $pquote_voucher = (int)Configuration::get('PQUOTE_VOUCHER');
        $voucher_end = ($pquote_voucher <= 0) ? 72 * 60 * 60 : $pquote_voucher * 60 * 60;
        $date_to = strtotime(date('Y-m-d H:i:s')) + $voucher_end;
        $cart_rule->reduction_amount = (float)$discount;
        $cart_rule->reduction_currency = (int)$currency;
        $cart_rule->date_to = date('Y-m-d H:i:s', $date_to);
        $cart_rule->partial_use = false;
        $cart_rule->date_from = date('Y-m-d H:i:s');
        $cart_rule->reduction_tax = $tax;
        $cart_rule->quantity = 1;
        $cart_rule->quantity_per_user = 1;
        $cart_rule->cart_rule_restriction = 0;
        $cart_rule->minimum_amount = $min_total - 1;
        $cart_rule->minimum_amount_currency = $currency;
        $cart_rule->minimum_amount_tax = $tax;
        $cart_rule->active = true;
        $cart_rule->product_restriction = 1;
        $cart_rule->name[Configuration::get('PS_LANG_DEFAULT')] = 'Quotation Voucher ID '.(int)$id_quotation;
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) {
            $cart_rule->id_discount_type = Discount::AMOUNT;
        }
        $code = 'PQ'.Tools::strtoupper(Tools::passwdGen(8));
        $cart_rule->code = $code;
        $cart_rule->add();

        //updated works
        if ($cart_rule->product_restriction) {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES ('.(int)$cart_rule->id.', 1)');
            $id_product_rule_group = Db::getInstance()->Insert_ID();

            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES ('.(int)$id_product_rule_group.', \'products\')');
            $id_product_rule = Db::getInstance()->Insert_ID();

            $obj = new Quote;
            $id_quote = (int)$obj->getIdQuote($id_quotation);
            $get_products_static = $obj->getQuoteProductsStatic($id_quote);
            $values = array();
            foreach ($get_products_static as $product) {
                $values[] = $product['id_product'];
            }
            $values = array_unique($values);

            foreach ($values as $key => $value) {
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ('.(int)$id_product_rule.','.$value.')');
            }
        }
        $valid_until = date('Y-m-d H:i:s', $date_to);
        $this->sendMailToUser(
            $id_cart,
            $min_total,
            $discount,
            $code,
            $valid_until,
            $currency,
            $email
        );
        return $cart_rule->id;
    }

    private function sendMailToUserByAdmn($id_cart, $min_total, $discount, $code, $valid_until, $id_currency, $email)
    {
        $id_lang = (int)$this->context->language->id;
        $currency = Currency::getCurrency($id_currency);
        $currency = new Currency($id_currency);

        $required_total = $min_total;
        $discount_total = $discount;
        $template = 'notifyuservoucher';
        $heading = Mail::l('Discount Voucher Received', (int)$id_lang);
        $cart_link = $this->context->link->getModuleLink('productquotation', 'restore').'?id_cart='.(int)$id_cart;
        $vars = array(
            '{link_cart}' => $cart_link,
            '{min_total}' => $required_total,
            '{discount}' => $discount_total,
            '{validity}' => $valid_until,
            '{coupon}' => $code
        );
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

    private function sendMailToUser($id_cart, $min_total, $discount, $code, $valid_until, $id_currency, $email)
    {
        $id_lang = (int)$this->context->language->id;
        $currency = Currency::getCurrency($id_currency);
        $currency = new Currency($id_currency);

        $required_total = Product::convertAndFormatPrice($min_total, $currency);
        $discount_total = Product::convertAndFormatPrice($discount, $currency);
        $template = 'notifyuservoucher';
        $heading = Mail::l('Discount Voucher Received', (int)$id_lang);
        $cart_link = $this->context->link->getModuleLink('productquotation', 'restore').'?id_cart='.(int)$id_cart;
        $vars = array(
            '{link_cart}' => $cart_link,
            '{min_total}' => $required_total,
            '{discount}' => $discount_total,
            '{validity}' => $valid_until,
            '{coupon}' => $code
        );
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
    
    public function getBaseLink($idShop = null, $ssl = null, $relativeProtocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $idShop !== null) {
            $shop = new Shop($idShop);
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relativeProtocol) {
            $base = '//'.($ssl && $this->ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
    }
}
