<?php
/**
 *  NOTICE OF LICENSE
 *
 *  This product is licensed for one customer to use on one installation (test stores and multishop included).
 *  Site developer has the right to modify this module to suit their needs, but can not redistribute the module
 *  in whole or in part. Any other use of this module constitues a violation of the user agreement.
 *
 *  DISCLAIMER
 *
 *  NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE WITH
 *  YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF
 *  DOLLARS IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
 *
 * @author    Software Agil Ltda
 * @copyright 2022
 * @license   See above
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class Cart extends CartCore
{
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    public $swaStarkenCarrier;
    /**
     * Return package shipping cost
     *
     * @param int          $id_carrier      Carrier ID (default : current carrier)
     * @param bool         $use_tax
     * @param Country|null $default_country
     * @param array|null   $product_list    List of product concerned by the shipping.
     * If null, all the product of the cart are used to calculate the shipping cost
     * @param int|null $id_zone
     *
     * @return float Shipping total
     */
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    public function getPackageShippingCost(
        $id_carrier = null,
        $use_tax = true,
        Country $default_country = null,
        $product_list = null,
        $id_zone = null,
        bool $keepOrderPrices = false
    ) {
        if (version_compare(_PS_VERSION_, '1.7.0', '<=')) {
            return $this->getPackageShippingCostSwaStarkencl16(
                $id_carrier,
                $use_tax,
                $default_country,
                $product_list,
                $id_zone
            );
        }
        return $this->getPackageShippingCostSwaStarkencl17(
            $id_carrier,
            $use_tax,
            $default_country,
            $product_list,
            $id_zone,
            $keepOrderPrices
        );
    }
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    public function getPackageShippingCostSwaStarkencl16(
        $id_carrier = null,
        $use_tax = true,
        Country $default_country = null,
        $product_list = null,
        $id_zone = null
    ) {
        if ($id_carrier != null && $id_carrier > 0) {
            $this->swaStarkenCarrier = new Carrier($id_carrier);
        }
        
        if ($this->isVirtualCart()) {
            return 0;
        }
        if (!$default_country) {
            $default_country = Context::getContext()->country;
        }
        if (!is_null($product_list)) {
            foreach ($product_list as $key => $value) {
                if ($value['is_virtual'] == 1) {
                    unset($product_list[$key]);
                }
            }
        }
        if (is_null($product_list)) {
            $products = $this->getProducts();
        } else {
            $products = $product_list;
        }
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int)$this->id_address_invoice;
        } elseif (count($product_list)) {
            $prod = current($product_list);
            $address_id = (int)$prod['id_address_delivery'];
        } else {
            $address_id = null;
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }
        if (is_null($id_carrier) && !empty($this->id_carrier)) {
            $id_carrier = (int)$this->id_carrier;
        }
        $cache_id = 'getPackageShippingCost_' . ((int) $this->id) . '_' . ((int) $address_id) . '_'
        . ((int)$id_carrier) . '_'. ((int)$use_tax) . '_' . ((int)$default_country->id) . '_' . ((int)$id_zone);
        if ($products) {
            foreach ($products as $product) {
                $cache_id .= '_'.(int)$product['id_product'].'_'.(int)$product['id_product_attribute'];
            }
        }
        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }
        $order_total = $this->getOrderTotal(true, Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING, $product_list);
        $shipping_cost = 0;
        if (!count($products)) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if (!isset($id_zone)) {
            if (!$this->isMultiAddressDelivery()
                && isset($this->id_address_delivery) // Be carefull, id_address_delivery is not usefull one 1.5
                && $this->id_address_delivery
                && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)
            ) {
                $id_zone = Address::getZoneById((int)$this->id_address_delivery);
            } else {
                if (!Validate::isLoadedObject($default_country)) {
                    $default_country = new Country(
                        Configuration::get('PS_COUNTRY_DEFAULT'),
                        Configuration::get('PS_LANG_DEFAULT')
                    );
                }
                $id_zone = (int)$default_country->id_zone;
            }
        }
        if ($id_carrier && !$this->isCarrierInRange((int)$id_carrier, (int)$id_zone)) {
            $id_carrier = '';
        }
        if (empty($id_carrier)
            && $this->isCarrierInRange((int)Configuration::get('PS_CARRIER_DEFAULT'), (int)$id_zone)
        ) {
            $id_carrier = (int)Configuration::get('PS_CARRIER_DEFAULT');
        }
        $total_package_without_shipping_tax_inc = $this->getOrderTotal(
            true,
            Cart::BOTH_WITHOUT_SHIPPING,
            $product_list
        );
        if (empty($id_carrier)) {
            if ((int)$this->id_customer) {
                $customer = new Customer((int)$this->id_customer);
                $result = Carrier::getCarriers(
                    (int)Configuration::get('PS_LANG_DEFAULT'),
                    true,
                    false,
                    (int)$id_zone,
                    $customer->getGroups()
                );
                unset($customer);
            } else {
                $result = Carrier::getCarriers((int)Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$id_zone);
            }
            foreach ($result as $k => $row) {
                if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT')) {
                    continue;
                }
                if (!isset(self::$_carriers[$row['id_carrier']])) {
                    self::$_carriers[$row['id_carrier']] = new Carrier((int)$row['id_carrier']);
                }
                
                $carrier = self::$_carriers[$row['id_carrier']];
                $shipping_method = $carrier->getShippingMethod();
                if ((
                        $shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                        && $carrier->getMaxDeliveryPriceByWeight((int)$id_zone) === false
                    )
                    || (
                        $shipping_method == Carrier::SHIPPING_METHOD_PRICE
                        && $carrier->getMaxDeliveryPriceByPrice((int)$id_zone) === false
                    )
                ) {
                    unset($result[$k]);
                    continue;
                }
                if ($row['range_behavior']) {
                    $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
                        $row['id_carrier'],
                        $this->getTotalWeight(),
                        (int)$id_zone
                    );
                    $total_order = $total_package_without_shipping_tax_inc;
                    $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
                        $row['id_carrier'],
                        $total_order,
                        (int)$id_zone,
                        (int)$this->id_currency
                    );
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !$check_delivery_price_by_weight)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !$check_delivery_price_by_price)) {
                        unset($result[$k]);
                        continue;
                    }
                }
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping = $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), (int)$id_zone);
                } else {
                    $shipping = $carrier->getDeliveryPriceByPrice($order_total, (int)$id_zone, (int)$this->id_currency);
                }
                
                $min_shipping_price = null;
                if (!isset($min_shipping_price)) {
                    $min_shipping_price = $shipping;
                }
                if ($shipping <= $min_shipping_price) {
                    $id_carrier = (int)$row['id_carrier'];
                    $min_shipping_price = $shipping;
                }
            }
        }
        if (empty($id_carrier)) {
            $id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
        }
        if (!isset(self::$_carriers[$id_carrier])) {
            self::$_carriers[$id_carrier] = new Carrier((int)$id_carrier, Configuration::get('PS_LANG_DEFAULT'));
        }
        $carrier = self::$_carriers[$id_carrier];
        if (!Validate::isLoadedObject($carrier)) {
            Cache::store($cache_id, 0);
            return 0;
        }
        $shipping_method = $carrier->getShippingMethod();
        if (!$carrier->active) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if ($carrier->is_free == 1) {
            Cache::store($cache_id, 0);
            return 0;
        }
        if ($use_tax && !Tax::excludeTaxeOption()) {
            $address = Address::initialize((int)$address_id);
            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                $carrier_tax = 0;
            } else {
                $carrier_tax = $carrier->getTaxesRate($address);
            }
        }
        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT'
        ));
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = Tools::convertPrice(
                (float)$configuration['PS_SHIPPING_FREE_PRICE'],
                Currency::getCurrencyInstance((int)$this->id_currency)
            );
        }
        $orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, null, null, false);
        if ($orderTotalwithDiscounts >= (float)($free_fees_price) && (float)($free_fees_price) > 0) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])
            && $this->getTotalWeight() >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT']
            && (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if ($carrier->range_behavior) {
            if (!isset($id_zone)) {
                if (isset($this->id_address_delivery)
                    && $this->id_address_delivery
                    && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)) {
                    $id_zone = Address::getZoneById((int)$this->id_address_delivery);
                } else {
                    $id_zone = (int)$default_country->id_zone;
                }
            }
            if ((
                    $shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                    && !Carrier::checkDeliveryPriceByWeight($carrier->id, $this->getTotalWeight(), (int)$id_zone)
                )
                || (
                    $shipping_method == Carrier::SHIPPING_METHOD_PRICE
                    && !Carrier::checkDeliveryPriceByPrice(
                        $carrier->id,
                        $total_package_without_shipping_tax_inc,
                        $id_zone,
                        (int)$this->id_currency
                    )
                )
            ) {
                $shipping_cost += 0;
            } else {
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping_cost += $carrier->getDeliveryPriceByWeight(
                        $this->getTotalWeight($product_list),
                        $id_zone
                    );
                } else { // by price
                    $shipping_cost += $carrier->getDeliveryPriceByPrice(
                        $order_total,
                        $id_zone,
                        (int)$this->id_currency
                    );
                }
            }
        } else {
            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
            } else {
                $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)$this->id_currency);
            }
        }
        if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling) {
            $shipping_cost += (float)$configuration['PS_SHIPPING_HANDLING'];
        }
        foreach ($products as $product) {
            if (!$product['is_virtual']) {
                $shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
            }
        }
        $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int)$this->id_currency));
        if ($carrier->shipping_external) {
            $module_name = $carrier->external_module_name;
            
            $module = Module::getInstanceByName($module_name);
            if (Validate::isLoadedObject($module)) {
                if (array_key_exists('id_carrier', $module)) {
                    $module->id_carrier = $carrier->id;
                }
                if ($carrier->need_range) {
                    if (method_exists($module, 'getPackageShippingCost')) {
                        $shipping_cost = $module->getPackageShippingCost($this, $shipping_cost, $products);
                    } else {
                        $shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
                    }
                } else {
                    $shipping_cost = $module->getOrderShippingCostExternal($this);
                }
                if ($shipping_cost === false) {
                    Cache::store($cache_id, false);
                    return false;
                }
            } else {
                Cache::store($cache_id, false);
                return false;
            }
        }
        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            if (!$use_tax) {
                    $shipping_cost /= (1 + $this->getAverageProductsTaxRate());
            }
        } else {
            if ($use_tax && isset($carrier_tax)) {
                $shipping_cost *= 1 + ($carrier_tax / 100);
            }
        }
        $shipping_cost = (float)Tools::ps_round(
            (float)$shipping_cost,
            (Currency::getCurrencyInstance((int)$this->id_currency)->decimals * _PS_PRICE_DISPLAY_PRECISION_)
        );
        Cache::store($cache_id, $shipping_cost);
        return $shipping_cost;
    }
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    public function getPackageShippingCostSwaStarkencl17(
        $id_carrier = null,
        $use_tax = true,
        Country $default_country = null,
        $product_list = null,
        $id_zone = null,
        $keepOrderPrices = false
    ) {
        if ($this->isVirtualCart()) {
            return 0;
        }
        if (!$default_country) {
            $default_country = Context::getContext()->country;
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            if (null === $product_list) {
                $products = $this->getProducts(false, false, null, true, $keepOrderPrices);
            } else {
                foreach ($product_list as $key => $value) {
                    if ($value['is_virtual'] == 1) {
                        unset($product_list[$key]);
                    }
                }
                $products = $product_list;
            }
        } else {
            if (null !== $product_list) {
                foreach ($product_list as $key => $value) {
                    if ($value['is_virtual'] == 1) {
                        unset($product_list[$key]);
                    }
                }
            }
            if (null === $product_list) {
                $products = $this->getProducts(false, false, null, true);
            } else {
                $products = $product_list;
            }
        }
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int) $this->id_address_invoice;
        } elseif (is_array($product_list) && count($product_list)) {
            $prod = current($product_list);
            $address_id = (int) $prod['id_address_delivery'];
        } else {
            $address_id = null;
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }
        if (null === $id_carrier && !empty($this->id_carrier)) {
            $id_carrier = (int) $this->id_carrier;
        }
        $cache_id = 'getPackageShippingCost_' . (int) $this->id . '_' . (int) $address_id . '_' . (int) $id_carrier
        . '_' . (int) $use_tax . '_' . (int) $default_country->id . '_' . (int) $id_zone;
        if ($products) {
            foreach ($products as $product) {
                $cache_id .= '_' . (int) $product['id_product'] . '_' . (int) $product['id_product_attribute'];
            }
        }
        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $order_total = $this->getOrderTotal(
                true,
                Cart::BOTH_WITHOUT_SHIPPING,
                $product_list,
                $id_carrier,
                false,
                $keepOrderPrices
            );
        } else {
            $order_total = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $product_list);
        }
        $shipping_cost = 0;
        if (!count($products)) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if (!isset($id_zone)) {
            if (!$this->isMultiAddressDelivery()
                && isset($this->id_address_delivery) // Be carefull, id_address_delivery is not usefull one 1.5
                && $this->id_address_delivery
                && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)
            ) {
                $id_zone = Address::getZoneById((int) $this->id_address_delivery);
            } else {
                if (!Validate::isLoadedObject($default_country)) {
                    $default_country = new Country(
                        Configuration::get('PS_COUNTRY_DEFAULT'),
                        Configuration::get('PS_LANG_DEFAULT')
                    );
                }
                $id_zone = (int) $default_country->id_zone;
            }
        }
        if ($id_carrier && !$this->isCarrierInRange((int) $id_carrier, (int) $id_zone)) {
            $id_carrier = '';
        }
        if (empty($id_carrier)
            && $this->isCarrierInRange((int) Configuration::get('PS_CARRIER_DEFAULT'), (int) $id_zone)
        ) {
            $id_carrier = (int) Configuration::get('PS_CARRIER_DEFAULT');
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            $total_package_without_shipping_tax_inc = $this->getOrderTotal(
                true,
                Cart::BOTH_WITHOUT_SHIPPING,
                $product_list
            );
        }
        if (empty($id_carrier)) {
            if ((int) $this->id_customer) {
                $customer = new Customer((int) $this->id_customer);
                $result = Carrier::getCarriers(
                    (int) Configuration::get('PS_LANG_DEFAULT'),
                    true,
                    false,
                    (int) $id_zone,
                    $customer->getGroups()
                );
                unset($customer);
            } else {
                $result = Carrier::getCarriers(
                    (int) Configuration::get('PS_LANG_DEFAULT'),
                    true,
                    false,
                    (int) $id_zone
                );
            }
            foreach ($result as $k => $row) {
                if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT')) {
                    continue;
                }
                if (!isset(self::$_carriers[$row['id_carrier']])) {
                    self::$_carriers[$row['id_carrier']] = new Carrier((int) $row['id_carrier']);
                }
                
                $carrier = self::$_carriers[$row['id_carrier']];
                $shipping_method = $carrier->getShippingMethod();
                if ((
                        $shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                        && $carrier->getMaxDeliveryPriceByWeight((int) $id_zone) === false
                    )
                    || (
                        $shipping_method == Carrier::SHIPPING_METHOD_PRICE
                        && $carrier->getMaxDeliveryPriceByPrice((int) $id_zone) === false
                    )
                ) {
                    unset($result[$k]);
                    continue;
                }
                if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
                    if ($row['range_behavior']) {
                        $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
                            $row['id_carrier'],
                            $this->getTotalWeight(),
                            (int) $id_zone
                        );
                        $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
                            $row['id_carrier'],
                            $order_total,
                            (int) $id_zone,
                            (int) $this->id_currency
                        );
                        if ((
                                $shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                                && !$check_delivery_price_by_weight
                            )
                            || (
                                $shipping_method == Carrier::SHIPPING_METHOD_PRICE
                                && !$check_delivery_price_by_price
                            )
                        ) {
                            unset($result[$k]);
                            continue;
                        }
                    }
                } else {
                    if ($row['range_behavior']) {
                        $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
                            $row['id_carrier'],
                            $this->getTotalWeight(),
                            (int) $id_zone
                        );
                        $total_order = $total_package_without_shipping_tax_inc;
                        $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
                            $row['id_carrier'],
                            $total_order,
                            (int) $id_zone,
                            (int) $this->id_currency
                        );
                        if ((
                                $shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                                && !$check_delivery_price_by_weight
                            )
                            || (
                                $shipping_method == Carrier::SHIPPING_METHOD_PRICE
                                && !$check_delivery_price_by_price
                            )
                        ) {
                            unset($result[$k]);
                            continue;
                        }
                    }
                }
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping = $carrier->getDeliveryPriceByWeight(
                        $this->getTotalWeight($product_list),
                        (int) $id_zone
                    );
                } else {
                    $shipping = $carrier->getDeliveryPriceByPrice(
                        $order_total,
                        (int) $id_zone,
                        (int) $this->id_currency
                    );
                }
                
                $min_shipping_price = null;
                if (!isset($min_shipping_price)) {
                    $min_shipping_price = $shipping;
                }
                if ($shipping <= $min_shipping_price) {
                    $id_carrier = (int) $row['id_carrier'];
                    $min_shipping_price = $shipping;
                }
            }
        }
        if (empty($id_carrier)) {
            $id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
        }
        if (!isset(self::$_carriers[$id_carrier])) {
            self::$_carriers[$id_carrier] = new Carrier((int) $id_carrier, Configuration::get('PS_LANG_DEFAULT'));
        }
        $carrier = self::$_carriers[$id_carrier];
        if (!Validate::isLoadedObject($carrier)) {
            Cache::store($cache_id, 0);
            return 0;
        }
        $shipping_method = $carrier->getShippingMethod();
        if (!$carrier->active) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if ($carrier->is_free == 1) {
            Cache::store($cache_id, 0);
            return 0;
        }
        if ($use_tax && !Tax::excludeTaxeOption()) {
            $address = Address::initialize((int) $address_id);
            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                $carrier_tax = 0;
            } else {
                $carrier_tax = $carrier->getTaxesRate($address);
            }
        }
        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT',
        ));
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = Tools::convertPrice(
                (float) $configuration['PS_SHIPPING_FREE_PRICE'],
                Currency::getCurrencyInstance((int) $this->id_currency)
            );
        }
        $orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, null, null, false);
        if ($orderTotalwithDiscounts >= (float) ($free_fees_price) && (float) ($free_fees_price) > 0) {
            $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])
            && $this->getTotalWeight() >= (float) $configuration['PS_SHIPPING_FREE_WEIGHT']
            && (float) $configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
            $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if ($carrier->range_behavior) {
            if (!isset($id_zone)) {
                if (isset($this->id_address_delivery)
                    && $this->id_address_delivery
                    && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)) {
                    $id_zone = Address::getZoneById((int) $this->id_address_delivery);
                } else {
                    $id_zone = (int) $default_country->id_zone;
                }
            }
            if ((
                    $shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                    && !Carrier::checkDeliveryPriceByWeight($carrier->id, $this->getTotalWeight(), (int) $id_zone)
                )
                || (
                    $shipping_method == Carrier::SHIPPING_METHOD_PRICE
                    && !Carrier::checkDeliveryPriceByPrice(
                        $carrier->id,
                        $total_package_without_shipping_tax_inc,
                        $id_zone,
                        (int) $this->id_currency
                    )
                )
            ) {
                $shipping_cost += 0;
            } else {
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping_cost += $carrier->getDeliveryPriceByWeight(
                        $this->getTotalWeight($product_list),
                        $id_zone
                    );
                } else { // by price
                    $shipping_cost += $carrier->getDeliveryPriceByPrice(
                        $order_total,
                        $id_zone,
                        (int) $this->id_currency
                    );
                }
            }
        } else {
            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
            } else {
                $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int) $this->id_currency);
            }
        }
        if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling) {
            $shipping_cost += (float) $configuration['PS_SHIPPING_HANDLING'];
        }
        foreach ($products as $product) {
            if (!$product['is_virtual']) {
                $shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
            }
        }
        $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int) $this->id_currency));
        $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
        if ($shipping_cost === false) {
            Cache::store($cache_id, false);
            return false;
        }
        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            if (!$use_tax) {
                $shipping_cost /= (1 + $this->getAverageProductsTaxRate());
            }
        } else {
            if ($use_tax && isset($carrier_tax)) {
                $shipping_cost *= 1 + ($carrier_tax / 100);
            }
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $shipping_cost = (float) Tools::ps_round(
                (float) $shipping_cost,
                Context::getContext()->getComputingPrecision()
            );
        } else {
            $shipping_cost = (float) Tools::ps_round((float) $shipping_cost, 2);
        }
        
        Cache::store($cache_id, $shipping_cost);
        return $shipping_cost;
    }
    /**
     * Ask the module the package shipping cost.
     *
     * If a carrier has been linked to a carrier module, we call it order to review the shipping costs.
     *
     * @param Carrier $carrier The concerned carrier (Your module may have several carriers)
     * @param float $shipping_cost The calculated shipping cost from the core, regarding package dimension & cart total
     * @param array $products The list of products
     *
     * @return bool|float The package price for the module (0 if free, false is disabled)
     */
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    protected function getPackageShippingCostFromModule(Carrier $carrier, $shipping_cost, $products)
    {
        $this->swaStarkenCarrier = $carrier;
        if (!$carrier->shipping_external) {
            return $shipping_cost;
        }
        
        $module = Module::getInstanceByName($carrier->external_module_name);
        if (!Validate::isLoadedObject($module)) {
            return false;
        }
        if (property_exists($module, 'id_carrier')) {
            $module->id_carrier = $carrier->id;
        }
        if (!$carrier->need_range) {
            return $module->getOrderShippingCostExternal($this);
        }
        if (method_exists($module, 'getPackageShippingCost')) {
            $shipping_cost = $module->getPackageShippingCost($this, $shipping_cost, $products);
        } else {
            $shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
        }
        return $shipping_cost;
    }
    /**
     * @override parent::getOrderTotal()
     */
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    public function getOrderTotal(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false,
        bool $keepOrderPrices = false
    ) {
        if ($this->id_carrier == null && $id_carrier != null) {
            $this->id_carrier = $id_carrier;
        }
        if (version_compare(_PS_VERSION_, '1.7.0', '<=')) {
            return $this->getOrderTotalSwaStarkencl16(
                $withTaxes,
                $type,
                $products,
                $this->id_carrier,
                $use_cache
            );
        }
        return $this->getOrderTotalSwaStarkencl17(
            $withTaxes,
            $type,
            $products,
            $this->id_carrier,
            $use_cache,
            $keepOrderPrices
        );
    }
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    private function getOrderTotalSwaStarkencl16(
        $with_taxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = true
    ) {
        $address_factory    = Adapter_ServiceLocator::get('Adapter_AddressFactory');
        $price_calculator    = Adapter_ServiceLocator::get('Adapter_ProductPriceCalculator');
        $configuration        = Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        $ps_tax_address_type = $configuration->get('PS_TAX_ADDRESS_TYPE');
        $ps_use_ecotax = $configuration->get('PS_USE_ECOTAX');
        $ps_round_type = $configuration->get('PS_ROUND_TYPE');
        $compute_precision = $configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        if (!$this->id) {
            return 0;
        }
        $type = (int)$type;
        $array_type = array(
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        );
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $this;
        if (!in_array($type, $array_type)) {
            die(Tools::displayError());
        }
        $with_shipping = in_array($type, array(Cart::BOTH, Cart::ONLY_SHIPPING));
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }
        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }
        if ($with_shipping || $type == Cart::ONLY_DISCOUNTS) {
            if (is_null($products) && is_null($id_carrier)) {
                $shipping_fees = $this->getTotalShippingCost(null, (bool)$with_taxes);
            } else {
                $shipping_fees = $this->getPackageShippingCost((int)$id_carrier, (bool)$with_taxes, null, $products);
            }
        } else {
            $shipping_fees = 0;
        }
        if ($type == Cart::ONLY_SHIPPING) {
            return $shipping_fees;
        }
        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }
        $param_product = true;
        if (is_null($products)) {
            $param_product = false;
            $products = $this->getProducts();
        }
        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if ($product['is_virtual']) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }
        require_once(_PS_MODULE_DIR_ . '/swastarkencl/classes/SwastarkenclCarrier.php');
        $swastarkenclCarrier = new SwastarkenclCarrier(SwastarkenclCarrier::getIdByCarrier($this->id_carrier));
        if ((int) $swastarkenclCarrier->payment_type == 3 && !in_array($type, [Cart::ONLY_WRAPPING])) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }
        $order_total = 0;
        if (Tax::excludeTaxeOption()) {
            $with_taxes = false;
        }
        $products_total = array();
        foreach ($products as $product) {
            if ($virtual_context->shop->id != $product['id_shop']) {
                $virtual_context->shop = new Shop((int)$product['id_shop']);
            }
            if ($ps_tax_address_type == 'id_address_invoice') {
                $id_address = (int)$this->id_address_invoice;
            } else {
                $id_address = (int)$product['id_address_delivery'];
            } // Get delivery address of the product from the cart
            if (!$address_factory->addressExists($id_address)) {
                $id_address = null;
            }
            $null = null;
            $price = $price_calculator->getProductPrice(
                (int)$product['id_product'],
                $with_taxes,
                (int)$product['id_product_attribute'],
                6,
                null,
                false,
                true,
                $product['cart_quantity'],
                false,
                (int)$this->id_customer ? (int)$this->id_customer : null,
                (int)$this->id,
                $id_address,
                $null,
                $ps_use_ecotax,
                true,
                $virtual_context
            );
            if ($with_taxes) {
                $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
                    (int)$product['id_product'],
                    $virtual_context
                );
            } else {
                $id_tax_rules_group = 0;
            }
            if (in_array($ps_round_type, array(Order::ROUND_ITEM, Order::ROUND_LINE))) {
                if (!isset($products_total[$id_tax_rules_group])) {
                    $products_total[$id_tax_rules_group] = 0;
                }
            } elseif (!isset($products_total[$id_tax_rules_group.'_'.$id_address])) {
                $products_total[$id_tax_rules_group.'_'.$id_address] = 0;
            }
            switch ($ps_round_type) {
                case Order::ROUND_TOTAL:
                    $products_total[$id_tax_rules_group.'_'.$id_address] += $price * (int)$product['cart_quantity'];
                    break;
                case Order::ROUND_LINE:
                    $product_price = $price * $product['cart_quantity'];
                    $products_total[$id_tax_rules_group] += Tools::ps_round($product_price, $compute_precision);
                    break;
                case Order::ROUND_ITEM:
                default:
                    $product_price = $price;
                    $products_total[$id_tax_rules_group] += Tools::ps_round(
                        $product_price,
                        $compute_precision
                    ) * (int)$product['cart_quantity'];
                    break;
            }
        }
        foreach ($products_total as $key => $price) {
            $order_total += $price;
        }
        $order_total_products = $order_total;
        if ($type == Cart::ONLY_DISCOUNTS) {
            $order_total = 0;
        }
        $wrapping_fees = 0;
        $include_gift_wrapping = (!$configuration->get('PS_ATCP_SHIPWRAP') || $type !== Cart::ONLY_PRODUCTS);
        if ($this->gift && $include_gift_wrapping) {
            $wrapping_fees = Tools::convertPrice(
                Tools::ps_round($this->getGiftWrappingPrice($with_taxes), $compute_precision),
                Currency::getCurrencyInstance((int)$this->id_currency)
            );
        }
        if ($type == Cart::ONLY_WRAPPING) {
            return $wrapping_fees;
        }
        $order_total_discount = 0;
        $order_shipping_discount = 0;
        if (!in_array($type, array(Cart::ONLY_SHIPPING, Cart::ONLY_PRODUCTS)) && CartRule::isFeatureActive()) {
            if ($with_shipping || $type == Cart::ONLY_DISCOUNTS) {
                $cart_rules = $this->getCartRules(CartRule::FILTER_ACTION_ALL);
            } else {
                $cart_rules = $this->getCartRules(CartRule::FILTER_ACTION_REDUCTION);
                foreach ($this->getCartRules(CartRule::FILTER_ACTION_GIFT) as $tmp_cart_rule) {
                    $flag = false;
                    foreach ($cart_rules as $cart_rule) {
                        if ($tmp_cart_rule['id_cart_rule'] == $cart_rule['id_cart_rule']) {
                            $flag = true;
                        }
                    }
                    if (!$flag) {
                        $cart_rules[] = $tmp_cart_rule;
                    }
                }
            }
            $id_address_delivery = 0;
            if (isset($products[0])) {
                $id_address_delivery = (
                    is_null($products) ? $this->id_address_delivery : $products[0]['id_address_delivery']
                );
            }
            $package = ['id_carrier' => $id_carrier, 'id_address' => $id_address_delivery, 'products' => $products];
            $flag = false;
            foreach ($cart_rules as $cart_rule) {
                if (($with_shipping || $type == Cart::ONLY_DISCOUNTS) && $cart_rule['obj']->free_shipping && !$flag) {
                    $order_shipping_discount = (float)Tools::ps_round(
                        $cart_rule['obj']->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_SHIPPING,
                            ($param_product ? $package : null),
                            $use_cache
                        ),
                        $compute_precision
                    );
                    $flag = true;
                }
                if ((int)$cart_rule['obj']->gift_product) {
                    $in_order = false;
                    if (is_null($products)) {
                        $in_order = true;
                    } else {
                        foreach ($products as $product) {
                            if ($cart_rule['obj']->gift_product == $product['id_product']
                                && $cart_rule['obj']->gift_product_attribute == $product['id_product_attribute']
                            ) {
                                $in_order = true;
                            }
                        }
                    }
                    if ($in_order) {
                        $order_total_discount += $cart_rule['obj']->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_GIFT,
                            $package,
                            $use_cache
                        );
                    }
                }
                if ($cart_rule['obj']->reduction_percent > 0 || $cart_rule['obj']->reduction_amount > 0) {
                    $order_total_discount += Tools::ps_round(
                        $cart_rule['obj']->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_REDUCTION,
                            $package,
                            $use_cache
                        ),
                        $compute_precision
                    );
                }
            }
            $order_total_discount = min(
                Tools::ps_round($order_total_discount, 2),
                (float)$order_total_products
            ) + (float)$order_shipping_discount;
            $order_total -= $order_total_discount;
        }
        if ($type == Cart::BOTH) {
            $order_total += $shipping_fees + $wrapping_fees;
        }
        if ($order_total < 0 && $type != Cart::ONLY_DISCOUNTS) {
            return 0;
        }
        if ($type == Cart::ONLY_DISCOUNTS) {
            return $order_total_discount;
        }
        return Tools::ps_round((float)$order_total, $compute_precision);
    }
    /*
    * module: swastarkencl
    * date: 2023-03-09 21:50:18
    * version: 3.6.1
    */
    private function getOrderTotalSwaStarkencl17(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false,
        $keepOrderPrices = false
    ) {
        if ($use_cache) {
        }
        if ((int) $id_carrier <= 0) {
            $id_carrier = null;
        }
        
        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }
        $type = (int) $type;
        $allowedTypes = array(
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        );
        if (!in_array($type, $allowedTypes)) {
            throw new \Exception('Invalid calculation type: ' . $type);
        }
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }
        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }
        if (null === $products) {
            $products = $this->getProducts(
                false,
                false,
                null,
                true,
                $keepOrderPrices
            );
        }
        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if ($product['is_virtual']) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }
        $swastarkenclCarrier = new SwastarkenclCarrier(SwastarkenclCarrier::getIdByCarrier($this->id_carrier));
        if ((int) $swastarkenclCarrier->payment_type == 3 && !in_array($type, [Cart::ONLY_WRAPPING])) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }
        if (Tax::excludeTaxeOption()) {
            $withTaxes = false;
        }
        $cartRules = array();
        if (in_array($type, [Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_DISCOUNTS])) {
            $cartRules = $this->getTotalCalculationCartRules($type, $type == Cart::BOTH);
        }
        $calculator = null;
        $computePrecision = null;
        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            $computePrecision = Context::getContext()->getComputingPrecision();
            $calculator = $this->newCalculator(
                $products,
                $cartRules,
                $id_carrier,
                $computePrecision,
                $keepOrderPrices
            );
        } else {
            $calculator = $this->newCalculator(
                $products,
                $cartRules,
                $id_carrier
            );
            $computePrecision = $this->configuration->get(
                '_PS_PRICE_COMPUTE_PRECISION_'
            );
        }
        switch ($type) {
            case Cart::ONLY_SHIPPING:
                $calculator->calculateRows();
                $calculator->calculateFees($computePrecision);
                $amount = $calculator->getFees()->getInitialShippingFees();
                break;
            case Cart::ONLY_WRAPPING:
                $calculator->calculateRows();
                $calculator->calculateFees($computePrecision);
                $amount = $calculator->getFees()->getInitialWrappingFees();
                break;
            case Cart::BOTH:
                $calculator->processCalculation($computePrecision);
                $amount = $calculator->getTotal();
                break;
            case Cart::BOTH_WITHOUT_SHIPPING:
                $calculator->calculateRows();
                $calculator->calculateCartRulesWithoutFreeShipping();
                $amount = $calculator->getTotal(true);
                break;
            case Cart::ONLY_PRODUCTS:
                $calculator->calculateRows();
                $amount = $calculator->getRowTotal();
                break;
            case Cart::ONLY_DISCOUNTS:
                $calculator->processCalculation($computePrecision);
                $amount = $calculator->getDiscountTotal();
                break;
            default:
                throw new \Exception('unknown cart calculation type : ' . $type);
        }
        $value = $withTaxes ? $amount->getTaxIncluded() : $amount->getTaxExcluded();
        return Tools::ps_round($value, $computePrecision);
    }
}
