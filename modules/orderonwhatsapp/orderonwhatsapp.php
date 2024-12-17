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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Orderonwhatsapp extends Module
{
    private $html = '';

    public function __construct()
    {
        $this->name = 'orderonwhatsapp';
        $this->tab = 'front_office_features';
        $this->version = '2.4.8';
        $this->author = 'weblir';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);

        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->displayName = $this->l('WhatsApp Integration PRO - Quick Order, Chat, Contact & Agents');
        $this->description =
            $this->l('Allow visitors to place quick orders using WhatsApp, add contact buttons with multiple agents');
        $this->table_name = $this->name;
        $this->module_key = '7ff0572e78460a3aed653a9c8bf9f4d9';
        parent::__construct();
    }

    public function install()
    {
        $wa_template = '';

        $template = $this->l('Hello').' {shop_name},'."\r\n"."\r\n".
            $this->l('I would like to purchase the following products from').
            ' {shop_url}:'."\r\n"."\r\n".'{products_ordered}'."\r\n"."\r\n".
            $this->l('Here are my contact details:')."\r\n".
            $this->l('First name:').' {customer_first_name}'."\r\n".
            $this->l('Last name:').' {customer_last_name}'."\r\n".
            $this->l('Country:').' {customer_country}'."\r\n".
            $this->l('State:').' {customer_state}'."\r\n".
            $this->l('City:').' {customer_city}'."\r\n".
            $this->l('Address:').' {customer_address}'."\r\n".
            $this->l('Mobile phone:').' {customer_mobile_number}'."\r\n"."\r\n".
            $this->l('My order has the id').' {order_id} '.
            $this->l('and has been placed on').' {order_timestamp}'."\r\n"."\r\n".
            $this->l('Thank you!');

        $cta_title = array();
        $wa_template = array();
        foreach (Language::getLanguages(true, $this->context->shop->id) as $lang) {
            $cta_title[$lang['id_lang']] = $this->l('Checkout using WhatsApp');
            $wa_template[$lang['id_lang']] = $template;
        }

        if (Language::countActiveLanguages()>1) {
            Configuration::updateValue('WL_OOW_CTA_TEXT', $cta_title, true);
            Configuration::updateValue('WL_OOW_TEMPLATE', $wa_template, true);
            Configuration::updateValue('WL_OOW_ORDER_TEMPLATE', $wa_template, true);
        } else {
            Configuration::updateValue('WL_OOW_CTA_TEXT', $this->l('Checkout using WhatsApp'));
            Configuration::updateValue('WL_OOW_TEMPLATE', $template);
            Configuration::updateValue('WL_OOW_ORDER_TEMPLATE', $template);
        }

        $this->addOrderStatus(
            'WL_OOW_ORDER_STATE',
            $this->l('WhatsApp Order'),
            '#25D366',
            'whatsapp'
        );

        Configuration::updateValue('WL_OOW_TOKEN', $this->randomString());
        Configuration::updateValue('WL_OOW_SWITCH', 0);
        Configuration::updateValue('WL_OOW_PRODUCTPAGE', 1);
        Configuration::updateValue('WL_OOW_FLOATING_POSITION', 'left');
        Configuration::updateValue('WL_OOW_WNUMBER', '');
        Configuration::updateValue('WL_OOW_CART_DROPDOWN', 1);
        Configuration::updateValue('WL_OOW_CART_PAGE', 1);
        Configuration::updateValue('WL_OOW_AUTO_ADDRESS', 1);
        Configuration::updateValue('WL_OOW_REQUIRED_first_name', 1);
        Configuration::updateValue('WL_OOW_REQUIRED_last_name', 1);
        Configuration::updateValue('WL_OOW_REQUIRED_country', 1);
        Configuration::updateValue('WL_OOW_REQUIRED_state', 1);
        Configuration::updateValue('WL_OOW_REQUIRED_city', 1);
        Configuration::updateValue('WL_OOW_REQUIRED_address', 1);
        Configuration::updateValue('WL_OOW_REQUIRED_mobile_phone', 1);

        Configuration::updateValue('WL_OOW_ENABLE_CONTACT', 1);
        Configuration::updateValue('WL_OOW_DISPLAY_ON', 1);
        Configuration::updateValue('WL_OOW_DISPLAY_TYPE', 3);
        Configuration::updateValue('WL_OOW_DISPLAY_POS', "right");

        if (!parent::install() or
            !$this->installMyTables() or
            !$this->registerHook('displayHeader') or
            !$this->registerHook('displayFooter') or
            !$this->registerHook('displayAdminOrderSide') or
            !$this->registerHook('actionGetAdminOrderButtons') or
            !$this->registerHook('displayAdminOrderContentOrder') or
            !$this->registerHook('displayAdminOrderTabOrder') or
            !$this->registerHook('displayShoppingCart') or
            !$this->registerHook('displayProductButtons')
            ) {
            return false;
        }
            
        return true;
    }


    public function uninstall()
    {
        if (!parent::uninstall() or
            !Configuration::deleteByName('WL_OOW_SWITCH') or
            !Configuration::deleteByName('WL_OOW_PRODUCTPAGE') or
            !$this->removeTable() or
            !Configuration::deleteByName('WL_OOW_FLOATING_POSITION') or
            !Configuration::deleteByName('WL_OOW_REQUIRED_first_name') or
            !Configuration::deleteByName('WL_OOW_REQUIRED_last_name') or
            !Configuration::deleteByName('WL_OOW_REQUIRED_country') or
            !Configuration::deleteByName('WL_OOW_REQUIRED_state') or
            !Configuration::deleteByName('WL_OOW_REQUIRED_city') or
            !Configuration::deleteByName('WL_OOW_REQUIRED_address') or
            !Configuration::deleteByName('WL_OOW_REQUIRED_mobile_phone') or
            !Configuration::deleteByName('WL_OOW_WNUMBER') or
            !Configuration::deleteByName('WL_OOW_CART_DROPDOWN') or
            !Configuration::deleteByName('WL_OOW_CART_PAGE') or
            !Configuration::deleteByName('WL_OOW_TEMPLATE') or
            !Configuration::deleteByName('WL_OOW_ORDER_TEMPLATE') or
            !Configuration::deleteByName('WL_OOW_CONTACT_TEXT') or
            !Configuration::deleteByName('WL_OOW_CTA_TEXT')
            ) {
            return false;
        }
        return true;
    }

    private function removeTable()
    {
        if (!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.$this->table_name.'_agents`')) {
            return false;
        }
        return true;
    }

    private function installMyTables()
    {
        $logins = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->table_name .'_agents` (
                `id` INT(12) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `wa_number` VARCHAR(255) NOT NULL,
                `availability` VARCHAR(255) NOT NULL,
                `cta` VARCHAR(255) NOT NULL,
                `position` INT(11) NOT NULL,
                PRIMARY KEY ( `id` )
                ) ENGINE = ' ._MYSQL_ENGINE_;

        if (!Db::getInstance()->Execute($logins)
        ) {
            return false;
        }
        return true;
    }

    public function getAgents($page = 1, $fields_list = 50)
    {
        if ($page == 1) {
            $offset = 0;
        } else {
            $offset = ($page-1)*$fields_list;
        }
        $sql = 'SELECT * FROM '._DB_PREFIX_.$this->table_name.'_agents
            ORDER BY `position` ASC
            LIMIT '.$offset.', '.$fields_list;

        return Db::getInstance()->ExecuteS($sql);
    }

    public function getAgentyID($id)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.$this->table_name.'_agents
            WHERE id ='.$id;

        return Db::getInstance()->getRow($sql);
    }

    public function countAgents()
    {
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.$this->table_name.'_agents';

        return Db::getInstance()->getValue($sql);
    }

    public function deleteAgent($id)
    {
        $delete_agent = Db::getInstance()->delete(
            $this->table_name.'_agents',
            'id = '.(int)$id
        );

        if (!$delete_agent
        ) {
            return false;
        }
            
        return true;
    }

    public function randomString($length = 7)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = Tools::strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function addOrderStatus($configKey, $statusName, $statusColor, $statusIconName)
    {
        if (!Configuration::get($configKey)) {
            $orderState = new OrderState();
            $orderState->name = array();
            $orderState->module_name = $this->name;
            $orderState->send_email = false;
            $orderState->color = $statusColor;
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = true;
            $orderState->invoice = false;
            $orderState->paid = false;
            foreach (Language::getLanguages() as $language) {
                $orderState->name[$language['id_lang']] = $statusName;
            }

            if ($orderState->add()) {
                $revoluticon = dirname(__FILE__).'/views/img/'.$statusIconName.'.gif';
                $newStateIcon = dirname(__FILE__).'/../../img/os/'.(int) $orderState->id.'.gif';
                copy($revoluticon, $newStateIcon);
            }

            Configuration::updateValue($configKey, (int)$orderState->id);
        }
    }

    public function deleteOrderState($id_order_state)
    {
        $orderState = new OrderState($id_order_state);
        $orderState->delete();
    }

    public function getContent()
    {
        $this->postProcess();
        $this->displayForm();

        return $this->html;
    }


    private function postProcess()
    {
        if (Tools::isSubmit('submitUpdate')) {
            if (Tools::strlen(Tools::getValue('WL_OOW_WNUMBER'))<1) {
                $this->html .=
                $this->displayError('Please fill the WhatsApp Phone Number field!');
                return true;
            }

            if (Language::countActiveLanguages()>1) {
                $cta_title = array();
                $template = array();
                $order_template = array();
                $cta_contact = array();

                foreach (Language::getLanguages(true, $this->context->shop->id) as $lang) {
                    $cta_title[$lang['id_lang']] = Tools::getValue('WL_OOW_CTA_TEXT_'.$lang['id_lang']);
                    $template[$lang['id_lang']] = Tools::getValue('WL_OOW_TEMPLATE_'.$lang['id_lang']);
                    $order_template[$lang['id_lang']] = Tools::getValue('WL_OOW_ORDER_TEMPLATE_'.$lang['id_lang']);
                    $cta_contact[$lang['id_lang']] = Tools::getValue('WL_OOW_CONTACT_TEXT_'.$lang['id_lang']);
                }
                Configuration::updateValue('WL_OOW_CTA_TEXT', $cta_title, true);
                Configuration::updateValue('WL_OOW_TEMPLATE', $template, true);
                Configuration::updateValue('WL_OOW_ORDER_TEMPLATE', $order_template, true);
                Configuration::updateValue('WL_OOW_CONTACT_TEXT', $cta_contact, true);
            } else {
                Configuration::updateValue('WL_OOW_CTA_TEXT', Tools::getValue('WL_OOW_CTA_TEXT'));
                Configuration::updateValue('WL_OOW_TEMPLATE', Tools::getValue('WL_OOW_TEMPLATE'));
                Configuration::updateValue('WL_OOW_ORDER_TEMPLATE', Tools::getValue('WL_OOW_ORDER_TEMPLATE'));
                Configuration::updateValue('WL_OOW_CONTACT_TEXT', Tools::getValue('WL_OOW_CONTACT_TEXT'));
            }

            
            

            Configuration::updateValue(
                'WL_OOW_SWITCH',
                Tools::getValue('WL_OOW_SWITCH')
            );

            Configuration::updateValue(
                'WL_OOW_PRODUCTPAGE',
                Tools::getValue('WL_OOW_PRODUCTPAGE')
            );

            Configuration::updateValue(
                'WL_OOW_FLOATING_POSITION',
                Tools::getValue('WL_OOW_FLOATING_POSITION')
            );

            Configuration::updateValue(
                'WL_OOW_WNUMBER',
                Tools::getValue('WL_OOW_WNUMBER')
            );

            Configuration::updateValue(
                'WL_OOW_CART_DROPDOWN',
                Tools::getValue('WL_OOW_CART_DROPDOWN')
            );

            Configuration::updateValue(
                'WL_OOW_CART_PAGE',
                Tools::getValue('WL_OOW_CART_PAGE')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_first_name',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_first_name')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_last_name',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_last_name')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_email',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_email')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_country',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_country')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_state',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_state')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_city',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_city')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_postcode',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_postcode')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_address',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_address')
            );

            Configuration::updateValue(
                'WL_OOW_REQUIRED_mobile_phone',
                Tools::getValue('WL_OOW_REQUIRED_FIELDS_cust_mobile_phone')
            );

            Configuration::updateValue(
                'WL_OOW_CARRIER',
                Tools::getValue('WL_OOW_CARRIER')
            );
            Configuration::updateValue(
                'WL_OOW_AUTO_ADDRESS',
                Tools::getValue('WL_OOW_AUTO_ADDRESS')
            );
            Configuration::updateValue(
                'WL_OOW_PAYMENT',
                Tools::getValue('WL_OOW_PAYMENT')
            );
            Configuration::updateValue(
                'WL_OOW_ENABLE_CONTACT',
                Tools::getValue('WL_OOW_ENABLE_CONTACT')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_ON',
                Tools::getValue('WL_OOW_DISPLAY_ON')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_PAGES_index',
                Tools::getValue('WL_OOW_DISPLAY_PAGES_index')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_PAGES_product',
                Tools::getValue('WL_OOW_DISPLAY_PAGES_product')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_PAGES_category',
                Tools::getValue('WL_OOW_DISPLAY_PAGES_category')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_PAGES_contact',
                Tools::getValue('WL_OOW_DISPLAY_PAGES_contact')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_PAGES_cms',
                Tools::getValue('WL_OOW_DISPLAY_PAGES_cms')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_TYPE',
                Tools::getValue('WL_OOW_DISPLAY_TYPE')
            );
            Configuration::updateValue(
                'WL_OOW_DISPLAY_POS',
                Tools::getValue('WL_OOW_DISPLAY_POS')
            );

            $this->html .= $this->displayConfirmation($this->l('Settings Updated'));
        } elseif (Tools::isSubmit('submitAgent')) {
            $insert = Db::getInstance()->insert(
                pSQL($this->table_name).'_agents',
                array(
                    'name' => pSQL(Tools::getValue('name')),
                    'wa_number' => pSQL(Tools::getValue('wa_number')),
                    'availability' => Tools::getValue('availability'),
                    'cta' => pSQL(Tools::getValue('cta')),
                    'position' => pSQL(Tools::getValue('position'))
                )
            );

            if (!$insert) {
                $this->displayError(
                    'Failed to insert new agent! Please fill the form with the right informations!'
                );
            } else {
                $this->html .= $this->displayConfirmation($this->l('WhatsApp Agent successfully added!'));
            }
        } elseif (Tools::isSubmit('editAgent')) {
            $id = (int)Tools::getValue('id');
            if (!Db::getInstance()->update(
                $this->table_name."_agents",
                array(
                    'name' => pSQL(Tools::getValue('name')),
                    'wa_number' => pSQL(Tools::getValue('wa_number')),
                    'availability' => Tools::getValue('availability'),
                    'cta' => pSQL(Tools::getValue('cta')),
                    'position' => pSQL(Tools::getValue('position'))
                ),
                'id = '. (int)$id
            )
            ) {
                $this->_errors[] = $this->l('Failed to edit agent! Please fill the form with the right informations!');
            } else {
                $this->html .= $this->displayConfirmation($this->l('WhatsAPp Agent successfully updated!'));
            }
        } elseif (Tools::isSubmit('delete'.$this->name.'_agents')) {
            //start delete entry
            $id = Tools::getValue('id');

            if ($this->deleteAgent($id)) {
                $this->html .= $this->displayConfirmation($this->l('Agent removed.'));
            } else {
                $this->_errors[] =
                    $this->l('Unable to delete agent with id:').' '.(int)$id;
            }
            //end delete entry
        }
    }

    private function displayCustomTop()
    {
        $shop = Tools::getHttpHost(true).__PS_BASE_URI__;
        $ref = implode('', array('a','d','d','o','n','s'));
        $module_version = $this->version;

        $this->context->smarty->assign(array(
            'path'=> $this->_path,
            'shop'=> $shop,
            'ref'=> $ref,
            'moduleversion'=> $module_version,
            'modulename'=> $this->name,
            'moduletitle'=> $this->displayName
        ));
        $this->html .= $this->display(__FILE__, 'top.tpl');
    }

    private function displayCustomBottom()
    {
        $this->html .= $this->display(__FILE__, 'bottom.tpl');
    }

    public function displayForm()
    {
        if (Tools::isSubmit('add_new')) {
            //start add new entry
            $this->html .= $this->generateAgentForm();
            //end add new entry
        } elseif (Tools::isSubmit('update'.$this->name.'_agents')) {
            //start edit entry
            $this->html .= $this->generateAgentForm(true);
            //end edit entry
        } else {
            $this->html .= $this->displayCustomTop();
            $this->html .= $this->displayCustomBottom();
            $this->html .= $this->generateForm();
            $this->html .= $this->generateSupportAgentList();
        }
    }

    private function generateForm()
    {
        $inputs = array();
        $contact_inputs = array();
        $inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'switch'),
            'label' => $this->l('Enable'),
            'class' => 't',
            'name' => 'WL_OOW_SWITCH',
            'desc' => $this->l('Choose to enable/disable the module on your website').
            '<br><small>'.$this->l('Default: Yes').'</small>',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                    ),
                array(
                    'id' => 'active_ff',
                    'value' => 0,
                    'label' => $this->l('No')
                    )
                )
        );

        $inputs[] = array(
            'type' => 'text',
            'label' => $this->l('WhatsApp Phone Number'),
            'name' => 'WL_OOW_WNUMBER',
            'desc' => $this->l('Enter your WhatsApp Number'),
            'class' => 'fixed-width-lg',
            'lang' => false
        );

        $inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'switch'),
            'class' => 't',
            'label' => $this->l('Display on Product page'),
            'name' => 'WL_OOW_PRODUCTPAGE',
            'desc' => $this->l('Choose to display the button on the product page').
            '<br><small>'.$this->l('Default: Yes').'</small>',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                    ),
                array(
                    'id' => 'active_ff',
                    'value' => 0,
                    'label' => $this->l('No')
                    )
                )
        );

        $inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'switch'),
            'class' => 't',
            'label' => $this->l('Display on Cart dropdown'),
            'name' => 'WL_OOW_CART_DROPDOWN',
            'desc' => $this->l('Choose to display the button on the cart dropdown').
            '<br><small>'.$this->l('Default: Yes').'</small>',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                    ),
                array(
                    'id' => 'active_ff',
                    'value' => 0,
                    'label' => $this->l('No')
                    )
                )
        );

        $inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'switch'),
            'class' => 't',
            'label' => $this->l('Display on Cart page'),
            'name' => 'WL_OOW_CART_PAGE',
            'desc' => $this->l('Choose to display the button on the cart page').
            '<br><small>'.$this->l('Default: Yes').'</small>',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                    ),
                array(
                    'id' => 'active_ff',
                    'value' => 0,
                    'label' => $this->l('No')
                    )
                )
        );

        $inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'switch'),
            'class' => 't',
            'label' => $this->l('Address autocomplete'),
            'name' => 'WL_OOW_AUTO_ADDRESS',
            'desc' => $this->l('Enabling this option will autocomplete customer\'s address form').
            '<br><small>'.$this->l('Default: Yes').'</small>',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                    ),
                array(
                    'id' => 'active_ff',
                    'value' => 0,
                    'label' => $this->l('No')
                    )
                )
        );

        $inputs[] = array(
            'type' => 'checkbox',
            'label' => $this->l('Required fields'),
            'name' => 'WL_OOW_REQUIRED_FIELDS',
            'desc' => $this->l('Set the required fields for ordering via WhatsApp'),
            'values' => array(
                'query' => array(
                    array(
                        'id' => 'cust_first_name',
                        'name' => $this->l('First name'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_last_name',
                        'name' => $this->l('Last name'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_email',
                        'name' => $this->l('Email'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_country',
                        'name' => $this->l('Country'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_state',
                        'name' => $this->l('State'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_city',
                        'name' => $this->l('City'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_address',
                        'name' => $this->l('Address'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_postcode',
                        'name' => $this->l('Post Code'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cust_mobile_phone',
                        'name' => $this->l('Mobile phone'),
                        'val' => '1',
                    )
                ),
                'id' => 'id',
                'name' => 'name'
            )
        );


        $carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, "ALL_CARRIERS");
        $payments = PaymentModule::getInstalledPaymentModules();

        foreach ($payments as $key => $payment) {
            $payments[$key]['title'] = Module::getModuleName($payment['name']).' ('.$payment['name'].')';
        }

        $payments[] = array('title' => "WhatsApp Order Custom Payment", 'name' => 'orderonwhatsapp' );

        $inputs[] = array(
            'type' => 'select',
            'label' => $this->l('Default carrier'),
            'desc' => $this->l('Set the default carrier for the WhatsApp orders'),
            'name' => 'WL_OOW_CARRIER',
            'class' => 'carrier_select',
            'options' => array(
                'query' => $carriers,
                'id' => 'id_carrier',
                'name' => 'name'
            )
        );
        $inputs[] = array(
            'type' => 'select',
            'label' => $this->l('Default payment'),
            'desc' => $this->l('Set the default payment method for the WhatsApp orders').'<br><strong>'.
            $this->l('Important: selected payment option must be an offline method like CoD, Bankwire, etc.').
            '</strong>'.'<br><strong>'.
            $this->l('Customers will not be able to pay via online payment options like credit card.')
            .'</strong>'.'<br>'.$this->l('Recommended option:').' <strong>'.$this->l('Cash on delivery').'</strong>',
            'name' => 'WL_OOW_PAYMENT',
            'class' => 'payment_select',
            'options' => array(
                'query' => $payments,
                'id' => 'name',
                'name' => 'title'
            )
        );

        $inputs[] = array(
            'type' => 'text',
            'label' => $this->l('WhatsApp Order CTA'),
            'name' => 'WL_OOW_CTA_TEXT',
            'desc' => $this->l('Set the text for the button').
            '<br><small>'.$this->l('Default: Order on WhatsApp').'</small>',
            'class' => '',
            'lang' => (Language::countActiveLanguages()>1?true:false)
        );

        $inputs[] = array(
            'type' => 'textarea',
            'label' => $this->l('Message template'),
            'name' => 'WL_OOW_TEMPLATE',
            'desc' => $this->l('Enter the text message content which will be sent
                to the customer via WhatsApp while order is placed.').
                '<br>'.$this->l('You can use the following shortcodes to
                compose the WhatsApp message template:').'<br><br>{customer_first_name}  <br>{customer_last_name}
                <br>{customer_email}<br>{customer_country} <br>{customer_state} <br>{customer_city}
                <br>{customer_postcode}<br>{customer_address}
                <br>{customer_mobile_number} <br>{admin_mobile_number} <br>{products_ordered} <br>{order_total}
                <br>{order_id} <br>{order_reference} <br>{order_timestamp} <br>{shop_name} <br>{shop_url}',
            'disabled' => '',
            'class' => 't',
            'lang' => (Language::countActiveLanguages()>1?true:false)
        );

        $inputs[] = array(
            'type' => 'textarea',
            'label' => $this->l('Order edit Message template'),
            'name' => 'WL_OOW_ORDER_TEMPLATE',
            'desc' => $this->l('Enter the text message content which will be sent
                to the customer via WhatsApp while order is edited.'),
            'disabled' => '',
            'class' => 't',
            'lang' => (Language::countActiveLanguages()>1?true:false)
        );

        $contact_inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'switch'),
            'class' => 't',
            'label' => $this->l('Enable WhatsApp contact button'),
            'name' => 'WL_OOW_ENABLE_CONTACT',
            'desc' => $this->l('Choose to display the WhatsApp contact button on your shop').
            '<br><small>'.$this->l('Default: Yes').'</small>',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                    ),
                array(
                    'id' => 'active_ff',
                    'value' => 0,
                    'label' => $this->l('No')
                    )
                )
        );

        $contact_inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'radio'),
            'label' => $this->l('Display button'),
            'name' => 'WL_OOW_DISPLAY_ON',
            'desc' => $this->l('Display the WhatsAPp contact button on:'),
            'class' => 't the-design',
            'is_bool'   => false,
            'values'    => array(
              array(
                'id'    => '1',
                'value' => '1',
                'label' => $this->l('All pages')
              ),
              array(
                'id'    => '2',
                'value' => '2',
                'label' => $this->l('Only on selected pages')
              ),
            ),
        );

        $contact_inputs[] = array(
            'type' => 'checkbox',
            'label' => $this->l('Select page type'),
            'name' => 'WL_OOW_DISPLAY_PAGES',
            'desc' => $this->l('Set the page types to display the button'),
            'values' => array(
                'query' => array(
                    array(
                        'id' => 'index',
                        'name' => $this->l('Homepage'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'product',
                        'name' => $this->l('Product pages'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'category',
                        'name' => $this->l('Category pages'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'contact',
                        'name' => $this->l('Contact page'),
                        'val' => '1',
                    ),
                    array(
                        'id' => 'cms',
                        'name' => $this->l('CMS Pages'),
                        'val' => '1',
                    )
                ),
                'id' => 'id',
                'name' => 'name'
            )
        );

        $contact_inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'radio'),
            'label' => $this->l('Display style'),
            'name' => 'WL_OOW_DISPLAY_TYPE',
            'desc' => $this->l('Select the display type for the button'),
            'class' => 't the-design',
            'is_bool'   => false,
            'values'    => array(
              array(
                'id'    => '1',
                'value' => '1',
                'label' => $this->l('Style 1 - Simple button')
              ),
              array(
                'id'    => '2',
                'value' => '2',
                'label' => $this->l('Style 2 - Simple button with expanding circles')
              ),
              array(
                'id'    => '3',
                'value' => '3',
                'label' => $this->l('Style 3 - Expandable floating button')
              ),
              array(
                'id'    => '4',
                'value' => '4',
                'label' => $this->l('Style 4 - Chat widget')
              ),
              array(
                'id'    => '5',
                'value' => '5',
                'label' => $this->l('Style 5 - Chat widget similar to WhatsApp')
              ),
              array(
                'id'    => '6',
                'value' => '6',
                'label' => $this->l('Style 6 - Chat widget with multiple WhatsApp Agents')
              ),
            ),
        );

        $contact_inputs[] = array(
            'type' => ($this->psversion() == 5 ? 'radio' : 'radio'),
            'label' => $this->l('Display position'),
            'name' => 'WL_OOW_DISPLAY_POS',
            'desc' => $this->l('Display the WhatsAPp contact button on:'),
            'class' => 't the-design',
            'is_bool'   => false,
            'values'    => array(
              array(
                'id'    => '1',
                'value' => 'left',
                'label' => $this->l('Left side')
              ),
              array(
                'id'    => '2',
                'value' => 'right',
                'label' => $this->l('Right side')
              ),
            ),
        );

        $contact_inputs[] = array(
            'type' => 'text',
            'label' => $this->l('Contact button text'),
            'name' => 'WL_OOW_CONTACT_TEXT',
            'desc' => $this->l('Set the text for the WhatsApp CTA button'),
            'class' => '',
            'lang' => (Language::countActiveLanguages()>1?true:false)
        );
        
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Order on WhatsApp CTA Settings'),
                    'icon' => 'icon-cogs'
                    ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitUpdate'
                    )
                )
        );

        $fields_form_2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('WhatsApp Contact CTA button Settings'),
                    'icon' => 'icon-cogs'
                    ),
                'input' => $contact_inputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitUpdate'
                    )
                )
        );
        $activeLanguages = array();
        foreach ($this->context->controller->getLanguages() as $lang) {
            if ($lang['active'] == 1) {
                $activeLanguages[] = $lang;
            }
        }
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        $helper->default_form_language = $lang->id;
        // $helper->submit_action = 'submitUpdate';
        $helper->allow_employee_form_lang =
        Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $activeLanguages,
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form, $fields_form_2));
    }

    private function generateAgentForm($edit = false)
    {
        $inputs = array();

        $submit_name = "submitAgent";
        if ($edit == true) {
            $inputs[] = array(
                'type' => 'hidden',
                'label' => $this->l('Agent ID'),
                'name' => 'id',
                'class' => '',
                'lang' => false
            );
            $submit_name = "editAgent";
        }

        $inputs[] = array(
            'type' => 'text',
            'label' => $this->l('Contact Name'),
            'name' => 'name',
            'class' => '',
            'lang' => false
        );

        $inputs[] = array(
            'type' => 'text',
            'label' => $this->l('WhatsApp Number'),
            'name' => 'wa_number',
            'class' => '',
            'lang' => false
        );

        $inputs[] = array(
            'type' => 'textarea',
            'label' => $this->l('Availability'),
            'name' => 'availability',
            'autoload_rte' => true,
            'class' => '',
            'lang' => false
        );

        $inputs[] = array(
            'type' => 'text',
            'label' => $this->l('CTA Text'),
            'name' => 'cta',
            'class' => '',
            'lang' => false
        );

        $inputs[] = array(
            'type' => 'text',
            'label' => $this->l('Position'),
            'name' => 'position',
            'class' => '',
            'lang' => false
        );
        
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add new WhatsApp agent'),
                    'icon' => 'icon-user'
                    ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => $submit_name
                    )
                )
        );
        $activeLanguages = array();
        foreach ($this->context->controller->getLanguages() as $lang) {
            if ($lang['active'] == 1) {
                $activeLanguages[] = $lang;
            }
        }
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        $helper->default_form_language = $lang->id;
        // $helper->submit_action = 'submitUpdate';
        $helper->allow_employee_form_lang =
        Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getAgentValues($edit),
            'languages' => $activeLanguages,
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getAgentValues($edit = false)
    {
        if ($edit) {
            $input_values = $this->getAgentyID((int)Tools::getValue('id'));
        } else {
            $input_values = array(
                'name' => '',
                'wa_number' => '',
                'availability' => '',
                'cta' => '',
                'position' => '',
            );
        }

        return $input_values;
    }

    public function getConfigFieldsValues()
    {
        $input_values = array(
            'WL_OOW_SWITCH' => Configuration::get('WL_OOW_SWITCH'),
            'WL_OOW_PRODUCTPAGE' => Configuration::get('WL_OOW_PRODUCTPAGE'),
            'WL_OOW_WNUMBER' => Configuration::get('WL_OOW_WNUMBER'),
            'WL_OOW_CART_DROPDOWN' => Configuration::get('WL_OOW_CART_DROPDOWN'),
            'WL_OOW_CART_PAGE' => Configuration::get('WL_OOW_CART_PAGE'),
            'WL_OOW_AUTO_ADDRESS' => Configuration::get('WL_OOW_AUTO_ADDRESS'),
            'WL_OOW_FLOATING_POSITION' => Configuration::get('WL_OOW_FLOATING_POSITION'),

            'WL_OOW_REQUIRED_FIELDS_cust_first_name' => Configuration::get("WL_OOW_REQUIRED_first_name"),
            'WL_OOW_REQUIRED_FIELDS_cust_email' => Configuration::get("WL_OOW_REQUIRED_email"),
            'WL_OOW_REQUIRED_FIELDS_cust_postcode' => Configuration::get("WL_OOW_REQUIRED_postcode"),
            'WL_OOW_REQUIRED_FIELDS_cust_last_name' => Configuration::get("WL_OOW_REQUIRED_last_name"),
            'WL_OOW_REQUIRED_FIELDS_cust_country' => Configuration::get("WL_OOW_REQUIRED_country"),
            'WL_OOW_REQUIRED_FIELDS_cust_state' => Configuration::get("WL_OOW_REQUIRED_state"),
            'WL_OOW_REQUIRED_FIELDS_cust_city' => Configuration::get("WL_OOW_REQUIRED_city"),
            'WL_OOW_REQUIRED_FIELDS_cust_address' => Configuration::get("WL_OOW_REQUIRED_address"),
            'WL_OOW_REQUIRED_FIELDS_cust_mobile_phone' => Configuration::get("WL_OOW_REQUIRED_mobile_phone"),

            'WL_OOW_CARRIER' => Configuration::get("WL_OOW_CARRIER"),
            'WL_OOW_PAYMENT' => Configuration::get("WL_OOW_PAYMENT"),
            'WL_OOW_ENABLE_CONTACT' => Configuration::get("WL_OOW_ENABLE_CONTACT"),
            'WL_OOW_DISPLAY_ON' => Configuration::get("WL_OOW_DISPLAY_ON"),
            'WL_OOW_DISPLAY_PAGES_index' => Configuration::get("WL_OOW_DISPLAY_PAGES_index"),
            'WL_OOW_DISPLAY_PAGES_product' => Configuration::get("WL_OOW_DISPLAY_PAGES_product"),
            'WL_OOW_DISPLAY_PAGES_category' => Configuration::get("WL_OOW_DISPLAY_PAGES_category"),
            'WL_OOW_DISPLAY_PAGES_contact' => Configuration::get("WL_OOW_DISPLAY_PAGES_contact"),
            'WL_OOW_DISPLAY_PAGES_cms' => Configuration::get("WL_OOW_DISPLAY_PAGES_cms"),
            'WL_OOW_DISPLAY_TYPE' => Configuration::get("WL_OOW_DISPLAY_TYPE"),
            'WL_OOW_DISPLAY_POS' => Configuration::get("WL_OOW_DISPLAY_POS"),
        );

        if (Language::countActiveLanguages()>1) {
            $input_values['WL_OOW_CTA_TEXT'] = Configuration::getInt('WL_OOW_CTA_TEXT');
            $input_values['WL_OOW_TEMPLATE'] = Configuration::getInt('WL_OOW_TEMPLATE');
            $input_values['WL_OOW_ORDER_TEMPLATE'] = Configuration::getInt('WL_OOW_ORDER_TEMPLATE');
            $input_values['WL_OOW_CONTACT_TEXT'] = Configuration::getInt('WL_OOW_CONTACT_TEXT');
        } else {
            $input_values['WL_OOW_CTA_TEXT'] = Configuration::get('WL_OOW_CTA_TEXT');
            $input_values['WL_OOW_TEMPLATE'] = Configuration::get('WL_OOW_TEMPLATE');
            $input_values['WL_OOW_ORDER_TEMPLATE'] = Configuration::get('WL_OOW_ORDER_TEMPLATE');
            $input_values['WL_OOW_CONTACT_TEXT'] = Configuration::get('WL_OOW_CONTACT_TEXT');
        }

        return $input_values;
    }

    private function generateSupportAgentList()
    {
        if (Tools::getIsset('page')) {
            $page = Tools::getValue('page');
        } else {
            $page = 1;
        }

        if (Tools::getIsset('selected_pagination')) {
            $selected_pagination = Tools::getValue('selected_pagination');
        } else {
            $selected_pagination = 30;
        }

        $content = $this->getAgents($page, $selected_pagination);

        foreach ($content as $key => $value) {
            $content[$key]['availability'] = strip_tags($value['availability']);
        }

        $fields_list = array(
            'id' => array(
                'title' => 'ID',
                'align' => 'center',
                'search' => false,
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'search' => false,
            ),
            'wa_number' => array(
                'title' => $this->l('WhatsApp Number'),
                'search' => false,
            ),
            'availability' => array(
                'title' => $this->l('Availability'),
                'search' => false,
            ),
            'cta' => array(
                'title' => $this->l('CTA Text'),
                'search' => false,
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'search' => false,
            )
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->actions = array('edit','delete');
        $helper->module = $this;
        $helper->listTotal = $this->countAgents();
        $helper->identifier = 'id';
        $helper->toolbar_btn = array(
          'new' => array(
              'desc' => $this->l('Create new WhatsApp Support Agent'),
              'href' => AdminController::$currentIndex.'?configure=' . $this->name .
              '&module_name=' . $this->name."&token=".Tools::getAdminTokenLite('AdminModules')."&add_new",
          ),
        );
        $helper->title = $this->l('WhatsApp Agent List');
        $helper->table = $this->name.'_agents';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name .'&module_name=' . $this->name;

        return $helper->generateList($content, $fields_list);
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $ver = explode(".", $version);
        return $ver[1];
    }

    public function hookDisplayProductButtons($params)
    {
        if (Configuration::get('WL_OOW_SWITCH') == '1' &&
            Configuration::get('WL_OOW_PRODUCTPAGE') == '1'
        ) {
            if (Language::countActiveLanguages()>1) {
                $WL_OOW_CTA_TEXT = Configuration::getInt('WL_OOW_CTA_TEXT')[$this->context->language->id];
            } else {
                $WL_OOW_CTA_TEXT = Configuration::get('WL_OOW_CTA_TEXT');
            }

            $this->context->smarty->assign(
                array(
                    'WL_OOW_CTA_TEXT' => $WL_OOW_CTA_TEXT,
                    'WL_OOW_WNUMBER' =>
                        Configuration::get('WL_OOW_WNUMBER'),
                    'modulepath'=> $this->_path
                )
            );

            if ($this->psversion() == 6) {
                return $this->display(__FILE__, 'product.tpl');
            } elseif ($this->psversion() == 7) {
                return $this->display(__FILE__, 'product-17.tpl');
            } else {
                return $this->display(__FILE__, 'product.tpl');
            }
        }
    }

    public function formatMobileNumber($number, $country_code)
    {
        if ($country_code == false) {
            if (isset($this->context) &&
                isset($this->context->country) &&
                isset($this->context->country->call_prefix)
            ) {
                $country_code = $this->context->country->call_prefix;
            } else {
                $country_data = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
                $country_code = $country_data->call_prefix;
            }
        }
        $country_code = str_replace('+', '', $country_code);
        $num = preg_replace('/^(?:\+?'.$country_code.'|0)?/', '+'.$country_code, $number);

        $num = str_replace("+", "", $num);

        return $num;
    }

    public function hookDisplayAdminOrderSide($params)
    {
        $data = new Order(Tools::getValue('id_order'));
        $address = new Address($data->id_address_delivery, $this->context->language->id);
        $country_data = new Country($address->id_country);

        if (Tools::strlen($address->phone_mobile)<1) {
            $customer_mobile = $this->formatMobileNumber($address->phone, $country_data->call_prefix);
        } else {
            $customer_mobile = $this->formatMobileNumber($address->phone_mobile, $country_data->call_prefix);
        }

        if (Language::countActiveLanguages()>1) {
            $wa_order_msg = Configuration::getInt('WL_OOW_ORDER_TEMPLATE')[$this->context->language->id];
        } else {
            $wa_order_msg = Configuration::get('WL_OOW_ORDER_TEMPLATE');
        }

        $this->smarty->assign(array(
            'customer_mobile' => $customer_mobile,
            'wa_order_msg' => $wa_order_msg,
            'theorderid' => $params['order']->id,
            'thecustomerid' => $params['order']->id_customer,
        ));
            
        return $this->display(__FILE__, 'views/templates/hook/admin-order-side.tpl');
    }

    public function hookActionGetAdminOrderButtons(array $params)
    {
        /** @var \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButtonsCollection $bar */
        $bar = $params['actions_bar_buttons_collection'];

        $data = new Order((int)$params['id_order']);
        $address = new Address($data->id_address_delivery, $this->context->language->id);
        $country_data = new Country($address->id_country);

        if (Tools::strlen($address->phone_mobile)<1) {
            $customer_mobile = $this->formatMobileNumber($address->phone, $country_data->call_prefix);
        } else {
            $customer_mobile = $this->formatMobileNumber($address->phone_mobile, $country_data->call_prefix);
        }

        $bar->add(
            new \PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton(
                'btn-success material-icons',
                [
                    'href' => 'https://wa.me/'.$customer_mobile,
                    'target' => '_blank',
                    'title' => $this->l('Contact customer on WhatsApp'),
                ],
                'whatsapp'
            )
        );
    }

    public function hookDisplayAdminOrderTabOrder($params)
    {
        if (!$this->active) {
            return;
        }
    
        return $this->display(__FILE__, 'views/templates/hook/admin-order-tab.tpl');
    }

    public function hookDisplayAdminOrderContentOrder($params)
    {
        $address = new Address($params['order']->id_address_delivery, $this->context->language->id);
        $country_data = new Country($address->id_country);

        $customer_mobile = $address->phone_mobile;

        if (Tools::strlen($customer_mobile)<1) {
            $customer_mobile = $this->formatMobileNumber($address->phone, $country_data->call_prefix);
        } else {
            $customer_mobile = $this->formatMobileNumber($address->phone_mobile, $country_data->call_prefix);
        }

        $this->smarty->assign(array(
            'customer_mobile' => $customer_mobile,
            'theorderid' => $params['order']->id,
            'thecustomerid' => $params['order']->id_customer,
        ));
            
        return $this->display(__FILE__, 'views/templates/hook/admin-order-content.tpl');
    }

    public function hookDisplayShoppingCart($params)
    {
        if (Configuration::get('WL_OOW_SWITCH') == '1' &&
            Configuration::get('WL_OOW_CART_PAGE') == '1'
        ) {
            if (Language::countActiveLanguages()>1) {
                $WL_OOW_CTA_TEXT = Configuration::getInt('WL_OOW_CTA_TEXT')[$this->context->language->id];
            } else {
                $WL_OOW_CTA_TEXT = Configuration::get('WL_OOW_CTA_TEXT');
            }

            $this->context->smarty->assign(
                array(
                    'WL_OOW_CTA_TEXT' => $WL_OOW_CTA_TEXT,
                    'WL_OOW_WNUMBER' =>
                        Configuration::get('WL_OOW_WNUMBER'),
                    'modulepath'=> $this->_path
                )
            );

            if ($this->psversion() == 6) {
                return $this->display(__FILE__, 'product.tpl');
            } elseif ($this->psversion() == 7) {
                return $this->display(__FILE__, 'product-17.tpl');
            } else {
                return $this->display(__FILE__, 'product.tpl');
            }
        }
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get('WL_OOW_SWITCH') == '1') {
            $this->context->controller->addCSS($this->_path.'views/css/front.css');
            $this->context->controller->addJS($this->_path.'views/js/front.js');
        }

        $params = array();
        $params['token'] = Configuration::get('WL_OOW_TOKEN');

        Media::addJsDef(
            array(
                'whatsapp_order_link' => $this->context->link->getModuleLink($this->name, 'ajax', $params)
            )
        );

        if (Configuration::get('WL_OOW_ENABLE_CONTACT') == 1) {
            $display = false;

            if (Configuration::get('WL_OOW_DISPLAY_ON') == 1) {
                $display = true;
            } else {
                if (isset($this->context->controller->php_self)) {
                    switch ($this->context->controller->php_self) {
                        case 'product':
                            if (Configuration::get('WL_OOW_DISPLAY_PAGES_product') == 1) {
                                $display = true;
                            }
                            break;

                        case 'category':
                            if (Configuration::get('WL_OOW_DISPLAY_PAGES_category') == 1) {
                                $display = true;
                            }
                            break;

                        case 'contact':
                            if (Configuration::get('WL_OOW_DISPLAY_PAGES_contact') == 1) {
                                $display = true;
                            }
                            break;

                        case 'cms':
                            if (Configuration::get('WL_OOW_DISPLAY_PAGES_cms') == 1) {
                                $display = true;
                            }
                            break;
                            
                        case 'index':
                            if (Configuration::get('WL_OOW_DISPLAY_PAGES_index') == 1) {
                                $display = true;
                            }
                            break;
                    }
                }
            }
            if ($display == true) {
                if (Language::countActiveLanguages()>1) {
                    $WL_OOW_CONTACT_TEXT = Configuration::getInt('WL_OOW_CONTACT_TEXT')[$this->context->language->id];
                } else {
                    $WL_OOW_CONTACT_TEXT = Configuration::get('WL_OOW_CONTACT_TEXT');
                }

                $this->context->smarty->assign(
                    array(
                        'WL_OOW_DISPLAY_POS' => Configuration::get('WL_OOW_DISPLAY_POS'),
                        'WL_OOW_WNUMBER' => Configuration::get('WL_OOW_WNUMBER'),
                        'WL_OOW_DISPLAY_TYPE' => Configuration::get('WL_OOW_DISPLAY_TYPE'),
                        'WL_OOW_CONTACT_TEXT' => $WL_OOW_CONTACT_TEXT,
                        'img_path'=> $this->_path,
                        'wa_agent_list'=> $this->getAgents(1, 20),
                    )
                );

                if ((int)Configuration::get("WL_OOW_DISPLAY_TYPE")>0 &&
                (int)Configuration::get("WL_OOW_DISPLAY_TYPE")<7
                ) {
                    $this->context->controller->addCSS(
                        $this->_path.'views/css/view-'.(int)Configuration::get("WL_OOW_DISPLAY_TYPE").'.css'
                    );
                }

                if ((int)Configuration::get("WL_OOW_DISPLAY_TYPE") == 5
                ) {
                    $this->context->controller->addCSS($this->_path.'views/css/floating-wpp.min.css');
                    $this->context->controller->addJS($this->_path.'views/js/floating-wpp.min.js');
                }

                if ($this->psversion() == 7) {
                    return $this->display(__FILE__, 'contact-17.tpl');
                } else {
                    return $this->display(__FILE__, 'contact.tpl');
                }
            }
        }
    }

    public function hookDisplayFooter($params)
    {
        $address_data = false;
        $customer_informations = false;
        if ((int)$this->context->customer->id > 0 && Configuration::get('WL_OOW_AUTO_ADDRESS') == '1') {
            $customer_address_id = Address::getFirstCustomerAddressId((int)$this->context->customer->id);
            $address_data = new Address($customer_address_id);
            $customer_informations = new Customer((int)$this->context->customer->id);
        }

        if (Configuration::get('WL_OOW_SWITCH') == '1') {
            if ($this->context->controller->php_self == 'cart' ||
                $this->context->controller->php_self == 'checkout' ||
                $this->context->controller->php_self == 'product'
            ) {
                if ($this->context->cart && $this->context->cart->id) {
                    $this->context->smarty->assign(
                        array(
                            'id_cart_user' => $this->context->cart->id
                        )
                    );
                }
                $countries = Country::getCountries($this->context->language->id, true, false, true);

                $store_countries = array();
                $store_states = array();
                $displayed_products = array();
                $page_type = false;

                foreach ($countries as $country) {
                    $store_countries[$country['id_country']] = $country['name'];
                    if (isset($country['states']) && $country['contains_states'] == 1) {
                        foreach ($country['states'] as $state) {
                            if ($state['active'] == 1) {
                                $store_states[$state['id_state']] = array(
                                    "id_state" => $state['id_state'],
                                    "id_country" => $state['id_country'],
                                    "name" => $state['name']
                                );
                            }
                        }
                    }
                }

                if ($this->context->controller->php_self == 'product') {
                    $init_product = new Product(Tools::getValue('id_product'), false, $this->context->language->id);
                    $page_type = 'product';

                    $displayed_products[] = array(
                        'id_product' => (int)Tools::getValue('id_product'),
                        'name' => $init_product->name
                    );
                }

                if ($this->context->controller->php_self == 'cart' ||
                    $this->context->controller->php_self == 'checkout'
                ) {
                    $products = Context::getContext()->cart->getProducts(true);
                    $displayed_products = array();
                    $page_type = 'cart';

                    foreach ($products as $product) {
                        $displayed_products[] = array(
                            'id_product' => $product['id_product'],
                            'name' => $product['name']
                        );
                    }
                }

                if (Language::countActiveLanguages()>1) {
                    $WL_OOW_CTA_TEXT = Configuration::getInt('WL_OOW_CTA_TEXT')[$this->context->language->id];
                } else {
                    $WL_OOW_CTA_TEXT = Configuration::get('WL_OOW_CTA_TEXT');
                }

                $this->context->smarty->assign(
                    array(
                        'WL_COUNTRY_DEFAULT' => Configuration::get('PS_COUNTRY_DEFAULT'),
                        'WL_OOW_CTA_TEXT' => $WL_OOW_CTA_TEXT,
                        'WL_OOW_WNUMBER' => Configuration::get('WL_OOW_WNUMBER'),
                        'WL_OOW_CART_DROPDOWN' =>
                            Configuration::get('WL_OOW_CART_DROPDOWN'),
                        'WL_OOW_CART_PAGE' => Configuration::get('WL_OOW_CART_PAGE'),
                        'WL_OOW_FLOATING_POSITION' =>
                            Configuration::get('WL_OOW_FLOATING_POSITION'),
                        'img_path'=> $this->_path,
                        'store_countries'=> $store_countries,
                        'store_states'=> $store_states,
                        'required_first_name' => Configuration::get("WL_OOW_REQUIRED_first_name"),
                        'required_last_name' => Configuration::get("WL_OOW_REQUIRED_last_name"),
                        'required_email' => Configuration::get("WL_OOW_REQUIRED_email"),
                        'required_country' => Configuration::get("WL_OOW_REQUIRED_country"),
                        'required_state' => Configuration::get("WL_OOW_REQUIRED_state"),
                        'required_city' => Configuration::get("WL_OOW_REQUIRED_city"),
                        'required_postcode' => Configuration::get("WL_OOW_REQUIRED_postcode"),
                        'required_address' => Configuration::get("WL_OOW_REQUIRED_address"),
                        'required_mobile_phone' => Configuration::get("WL_OOW_REQUIRED_mobile_phone"),
                        'wa_products'=> $displayed_products,
                        'wa_page_type'=> $page_type,
                        'customer_address_data'=> $address_data,
                        'customer_informations'=> $customer_informations,
                    )
                );

                if ($this->psversion() == 6) {
                    return $this->display(__FILE__, 'footer.tpl');
                } elseif ($this->psversion() == 7) {
                    return $this->display(__FILE__, 'footer-17.tpl');
                } else {
                    return $this->display(__FILE__, 'footer.tpl');
                }
            }
        }
    }
}
