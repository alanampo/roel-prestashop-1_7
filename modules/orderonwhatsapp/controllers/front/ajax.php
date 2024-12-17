<?php
/**
* 2007-2023 Weblir
*
*  @author    weblir <hello@weblir.com>
*  @copyright 2012-2023 weblir
*  @license   weblir.com
*  You are allowed to modify this copy for your own use only. You must not redistribute it. License
*  is permitted for one Prestashop instance only but you can install it on your test instances.
*/

class OrderOnWhatsAppAjaxModuleFrontController extends ModuleFrontController
{
    /** @var bool If set to true, will be redirected to authentication page */
    public $auth = false;

    /** @var bool */
    public $ajax;

    public function __construct()
    {
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::getIsset('token') &&
            Tools::getValue('token') == Configuration::get('WL_OOW_TOKEN')
        ) {
            if (Tools::getIsset('action')) {
                if (Tools::getValue('action') == 'initOrder') {
                    $id_country = (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));

                    $id_state = (int)Tools::getValue('id_state', 0);
                    $city = pSQL(Tools::getValue('city'), 'Empty');
                    $postcode = pSQL(Tools::getValue('postcode'), 'Empty');
                    $first_name = pSQL(Tools::getValue('first_name'), 'Empty');
                    $last_name = pSQL(Tools::getValue('last_name'), 'Empty');
                    $email = pSQL(Tools::getValue('email'), 'Empty');
                    $address = pSQL(Tools::getValue('address'), 'Empty');
                    $mobile_phone = pSQL(Tools::getValue('mobile_phone'), '0123456789');

                    if (Tools::getValue('wa_page_type') == 'product' && (int)Tools::getValue('product_id')>0) {
                        $products = $this->context->cart->getProducts();
                        if (count($products)>0) {
                            foreach ($products as $product) {
                                $this->context->cart->deleteProduct($product["id_product"]);
                            }
                            $this->context->cart->delete();
                            $this->context->cookie->id_cart = 0;
                        } else {
                            if (!$this->context->cart->id) {
                                if (Context::getContext()->cookie->id_guest) {
                                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                                }
                                $this->context->cart->add();
                                if ($this->context->cart->id) {
                                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                                }
                            }
                        }

                        $this->context->cart->id_currency = (int)$this->context->cart->id_currency;
                        $this->context->cart->id_lang = (int)$this->context->language->id;

                        $this->context->cart->updateQty(
                            (int)Tools::getValue('product_qty'),
                            (int)Tools::getValue('product_id')
                        );
                        $this->context->cart->update();
                    } else {
                        //use cart products
                    }

                    $address_data = array(
                        'id_country' => $id_country,
                        'id_state' => $id_state,
                        'city' => $city,
                        'postcode' => $postcode,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'mobile_phone' => $mobile_phone,
                        'address' => $address,
                        'dni' => str_replace(',', '', $mobile_phone),
                    );
                    $customer_data = array(
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'mobile_phone' => $mobile_phone,
                    );

                    $id_address = $this->insertNewAddress($address_data);
                    $id_customer = $this->insertNewCustomer($customer_data);
                    $order_data = $this->createOrder($id_address, $id_customer);
                    $order_products = $order_data->getProducts();

                    $ordered_products_text = "";
                    foreach ($order_products as $product) {
                        $ordered_products_text .= $product['product_quantity']." x ".$product['product_name']."\r\n";
                    }

                    $wa_template = Configuration::getInt('WL_OOW_TEMPLATE')[$this->context->language->id];

                    $wa_template = str_replace('{customer_first_name}', $first_name, $wa_template);
                    $wa_template = str_replace('{customer_last_name}', $last_name, $wa_template);
                    $wa_template = str_replace('{customer_email}', $email, $wa_template);
                    $wa_template = str_replace(
                        '{customer_country}',
                        Country::getNameById($this->context->language->id, $id_country),
                        $wa_template
                    );
                    $wa_template = str_replace('{customer_state}', State::getNameById($id_state), $wa_template);
                    $wa_template = str_replace('{customer_city}', $city, $wa_template);
                    $wa_template = str_replace('{customer_postcode}', $postcode, $wa_template);
                    $wa_template = str_replace('{customer_address}', $address, $wa_template);
                    $wa_template = str_replace('{customer_mobile_number}', $mobile_phone, $wa_template);
                    $wa_template = str_replace(
                        '{admin_mobile_number}',
                        Configuration::get("WL_OOW_WNUMBER"),
                        $wa_template
                    );
                    $wa_template = str_replace('{products_ordered}', $ordered_products_text, $wa_template);
                    $wa_template = str_replace('{order_total}', $order_data->total_paid, $wa_template);
                    $wa_template = str_replace('{order_id}', $order_data->id, $wa_template);
                    $wa_template = str_replace('{order_reference}', $order_data->reference, $wa_template);
                    $wa_template = str_replace('{order_timestamp}', $order_data->date_add, $wa_template);
                    $wa_template = str_replace('{shop_name}', Configuration::get('PS_SHOP_NAME'), $wa_template);
                    $wa_template = str_replace('{shop_url}', _PS_BASE_URL_.__PS_BASE_URI__, $wa_template);

                    $link_params = array(
                        'text' => $wa_template,
                    );

                    $wa_link = "https://wa.me/".
                        Configuration::get("WL_OOW_WNUMBER")."?".http_build_query($link_params);

                    $arr = array(
                        'status' => 'success',
                        'msg' => 'Order successfully created',
                        'data' => array("whatsapp_link" => $wa_link)
                    );

                    echo json_encode($arr);
                } else {
                    $arr = array('status' => 'error', 'msg' => 'Action not defined!', 'data' => array());

                    echo json_encode($arr);
                }
            }
        } else {
            $arr = array('status' => 'error', 'msg' => 'Unauthorized access!', 'data' => array());
            echo json_encode($arr);
        }
    }

    public function insertNewCustomer($data)
    {
        if ((int)$this->context->customer->id > 0) {
            return $this->context->customer->id;
        } else {
            $customer = new Customer();
            $customer->lastname = pSQL($data['last_name']);
            $customer->firstname = pSQL($data['first_name']);
            $pwd='wsxedc';
            $customer->passwd=md5(pSQL(_COOKIE_KEY_.$pwd));
            if(Tools::strlen($data['email']) > 0 && Validate::isEmail($data['email'])) {
                $customer->email = pSQL($data['email']);
            } else {
                $customer->email = time()."@".Configuration::get('PS_SHOP_DOMAIN');
            }

            $customer->firstname = Tools::ucwords($customer->firstname);
            $customer->birthday = '1980-10-10';

            $customer->is_guest = 1;
            $customer->active = 1;

            $customer->add();

            return $customer->id;
        }
    }


    public function insertNewAddress($data)
    {
        $init = new Address();
        $init->firstname = pSQL($data['first_name']);
        $init->lastname = pSQL($data['last_name']);
        $init->email = pSQL($data['email']);
        $init->address1 = pSQL($data['address']);
        $init->city = pSQL($data['city']);
        $init->postcode = pSQL($data['postcode']);
        $init->phone_mobile = pSQL($data['mobile_phone']);
        $init->phone = pSQL($data['mobile_phone']);
        $init->dni = pSQL(str_replace(',', '', $data['mobile_phone']));
        $init->id_country = pSQL($data['id_country']);
        $init->id_state = pSQL($data['id_state']);
        $init->alias = pSQL("New ".$data['mobile_phone']);
        $init->add();
        return $init->id;
    }

    public function createOrder($address_id, $id_customer)
    {
        $whatsapp_order_state = Configuration::get('WL_OOW_ORDER_STATE');
        $order_status = null;
        $order_object = new Order();
        $order_object->id_address_delivery = (int)$address_id;
        $order_object->id_address_invoice = (int)$address_id;
        $order_object->id_cart = (int)$this->context->cart->id;
        $carrier = null;

        // if (!$this->context->cart->isVirtualCart() && isset($package['id_carrier'])) {
        //     $carrier = new Carrier((int)$package['id_carrier'], (int)$this->context->cart->id_lang);
        //     $order_object->id_carrier = (int)$carrier->id;
        //     $id_carrier = (int)$carrier->id;
        // } else {
        //     $order_object->id_carrier = 0;
        //     $id_carrier = 0;
        // }
        
        $order_object->id_currency = (int)$this->context->cart->id_currency;
        $order_object->id_customer = (int)$id_customer;
        
        // $CarrierObject = new CarrierCore();
        // $CarrierObject->name = "WhatsApp Order Custom Carrier";
        // $CarrierObject->active = 0;
        // $CarrierObject->deleted = 1;

        // $getlanguages = Language::getLanguages();

        // foreach ($getlanguages as $language) {
        //     $CarrierObject->delay[$language['id_lang']] = "2-4";
        // }
        //$CarrierObject->add();

        $id_carrier = (int)Configuration::get("WL_OOW_CARRIER");
        $order_object->id_carrier = $id_carrier;

        if (Configuration::get("WL_OOW_PAYMENT") == 'orderonwhatsapp') {
            $order_object->payment = 'WhatsApp Order Custom Payment';
            $order_object->module = "orderonwhatsapp";
        } else {
            $order_object->payment = Module::getModuleName(Configuration::get("WL_OOW_PAYMENT"));
            $order_object->module = Configuration::get("WL_OOW_PAYMENT");
        }

        //$products = $this->context->cart->getProducts();
        
        $order_object->product_list = $this->context->cart->getProducts();
        $order_object->valid = 1;
        $order_object->total_products = (float)$this->context->cart->getOrderTotal(
            false,
            Cart::ONLY_PRODUCTS,
            $order_object->product_list,
            $id_carrier
        );
        $order_object->total_products_wt = (float)$this->context->cart->getOrderTotal(
            true,
            Cart::ONLY_PRODUCTS,
            $order_object->product_list,
            $id_carrier
        );
        $order_object->total_discounts_tax_excl = (float)abs(
            $this->context->cart->getOrderTotal(
                false,
                Cart::ONLY_DISCOUNTS,
                $order_object->product_list,
                $id_carrier
            )
        );
        $order_object->total_discounts_tax_incl = (float)abs(
            $this->context->cart->getOrderTotal(
                true,
                Cart::ONLY_DISCOUNTS,
                $order_object->product_list,
                $id_carrier
            )
        );
        $order_object->total_discounts = $order_object->total_discounts_tax_incl;
        
        $order_object->total_shipping_tax_excl =
            (float)$this->context->cart->getPackageShippingCost(
                (int)$id_carrier,
                false,
                null,
                $order_object->product_list
            );
        $order_object->total_shipping_tax_incl =
            (float)$this->context->cart->getPackageShippingCost(
                (int)$id_carrier,
                true,
                null,
                $order_object->product_list
            );
        $order_object->total_shipping = $order_object->total_shipping_tax_incl;
      
        $order_object->id_customer = (int)$id_customer;
        $order_object->id_address_invoice = (int)$address_id;
        $order_object->id_address_delivery = (int)$address_id;
        $order_object->id_currency = $this->context->currency->id;
        
        $order_object->id_lang = (int)$this->context->cart->id_lang;
        $order_object->id_cart = (int)$this->context->cart->id;
        $order_object->reference = Order::generateReference();
        $order_object->id_shop = (int)$this->context->shop->id;
        $order_object->id_shop_group = (int)$this->context->shop->id_shop_group;
        
        $order_object->secure_key = $this->context->customer->secure_key;
        
        
        $order_object->current_state = (int)$whatsapp_order_state;
        $order_status = (int)$whatsapp_order_state;
        
        $order_object->total_wrapping_tax_excl = (float)abs(
            $this->context->cart->getOrderTotal(
                false,
                Cart::ONLY_WRAPPING,
                $order_object->product_list,
                $id_carrier
            )
        );
        $order_object->total_wrapping_tax_incl = (float)abs(
            $this->context->cart->getOrderTotal(
                true,
                Cart::ONLY_WRAPPING,
                $order_object->product_list,
                $id_carrier
            )
        );
        $order_object->total_wrapping = $order_object->total_wrapping_tax_incl;
        $order_object->conversion_rate = $this->context->currency->conversion_rate;
        $order_object->total_paid_tax_excl = (float)Tools::ps_round(
            (float)$this->context->cart->getOrderTotal(false, Cart::BOTH, $order_object->product_list, $id_carrier),
            _PS_PRICE_COMPUTE_PRECISION_
        );
        $order_object->total_paid_tax_incl = (float)Tools::ps_round(
            (float)$this->context->cart->getOrderTotal(true, Cart::BOTH, $order_object->product_list, $id_carrier),
            _PS_PRICE_COMPUTE_PRECISION_
        );
        $order_object->total_paid = $order_object->total_paid_tax_incl;
        $order_object->total_paid_real =$order_object->total_paid_tax_incl;
        $order_object->round_mode = Configuration::get('PS_PRICE_ROUND_MODE');
        $order_object->round_type = Configuration::get('PS_ROUND_TYPE');
        $order_date = new \DateTime();
        $order_date = $order_date->format('Y-m-d H:i:s');
        $order_object->invoice_date = $order_date;
        $order_object->delivery_date = $order_date;
        $order_object->secure_key = md5(uniqid(rand(), true));
        $order_object->add();
        //$order_list[] = $order_object;
        //$this->dev($order->product_list);
        
        // Insert new Order detail list using cart for the current order
        $order_detail = new OrderDetail(null, null, $this->context);
        $order_detail->createList(
            $order_object,
            $this->context->cart,
            $order_status,
            $order_object->product_list,
            0,
            true
        );
        //$order_detail_list[] = $order_detail;
        // Adding an entry in order_carrier table
        if (!is_null($carrier)) {
            $order_carrier = new OrderCarrier();
            $order_carrier->id_order = (int)$order_object->id;
            $order_carrier->id_carrier = (int)$id_carrier;
            $order_carrier->weight = (float)$order_object->getTotalWeight();
            $order_carrier->shipping_cost_tax_excl = (float)$order_object->total_shipping_tax_excl;
            $order_carrier->shipping_cost_tax_incl = (float)$order_object->total_shipping_tax_incl;
            $order_carrier->add();
        }

        // Hook validate order
        $customer = new Customer((int)$id_customer);
        Hook::exec('actionValidateOrder', [
            'cart' => $this->context->cart,
            'order' => $order_object,
            'customer' => $customer,
            'currency' => $this->context->currency,
            'orderStatus' => $order_status,
        ]);

        // $_GET['id_order'] = (int)$order_object->id;
        // $order_object->setInvoice(true);
        return $order_object;
    }

    public function display()
    {
        $this->ajax = 1;
        $this->ajaxDie();
    }
}
