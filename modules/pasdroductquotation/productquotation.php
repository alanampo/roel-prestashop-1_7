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

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once(dirname(__FILE__).'/model/QuoteModel.php');
include_once(dirname(__FILE__).'/model/MessageModel.php');
include_once(dirname(__FILE__).'/model/FieldsQuote.php');
class ProductQuotation extends Module
{
    public $trans           = array();
    public function __construct()
    {
        $this->name = 'productquotation';
        $this->tab = 'front_office_features';
        $this->version = '2.3.6';
        $this->author = 'FMM Modules';
        $this->bootstrap = true;
        parent::__construct();
        $this->trans = $this->getTranslatableText();
        $this->displayName = $this->l('Product Quotation');
        $this->description = $this->l('Customers can send you quotation using this module.');
        $this->module_key = '2761b37376a2ed138eae8f8cb2390646';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
    }

    public function install()
    {
        mkdir(_PS_IMG_DIR_.'quote', 0777, true);
        return (parent::install()
            && $this->registerHook('displayProductButtons')
            && $this->registerHook('header')
            && $this->registerHook('displayTop')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayShoppingCart')
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('ModuleRoutes')
            && $this->registerHook('registerGDPRConsent')
            && $this->registerHook('actionDeleteGDPRCustomer')
            && $this->registerHook('actionExportGDPRData')
            && $this->registerHook('displayproductlistreviews')
            && $this->registerHook('actionCartSave')
            && Configuration::updateValue('PQUOTE_PRICE', 0)
            && Configuration::updateValue('PQUOTE_CART', 0)
            && Configuration::updateValue('PQUOTE_LISTING', 0)
            && Configuration::updateValue('PQUOTE_TAX', 0)
            && Configuration::updateValue('PQUOTE_CATALOG', 0)
            && Configuration::updateValue('PQUOTE_STOCK', 1)
            && Configuration::updateValue('groupBox', 3)
            && $this->installDb()
            && $this->installTab());
    }

    public function installDb()
    {
        $return = true;
        $return = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'productquotation` (
                    `id_productquotation` int(10) NOT NULL auto_increment,
                    `id_quote` int(10) NOT NULL,
                    `status` int(10) unsigned NOT NULL,
                    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `email` varchar(255) NOT NULL,
                    `template` int(10) unsigned NOT NULL,
                    `id_customer` int(10) NOT NULL DEFAULT \'0\',
                    `id_currency` int(10) NOT NULL DEFAULT \'0\',
                    `voucher` int(10) NOT NULL DEFAULT \'0\',
                    `key` varchar(255) NOT NULL,
                    `coupon_sent` int(10) NOT NULL DEFAULT \'0\',
                    `coupon_id` int(10) NOT NULL DEFAULT \'0\',
                    `id_shop` int(10) NOT NULL DEFAULT \'0\',
                    `id_lang` int(10) NOT NULL DEFAULT \'0\',
                    `id_cart` int(10) NOT NULL DEFAULT \'0\',
                    PRIMARY KEY (`id_productquotation`, `id_quote`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'productquotation_templates` (
                    `id_productquotation_templates` int(10) NOT NULL auto_increment,
                    `status` int(10) unsigned NOT NULL,
                    PRIMARY KEY (`id_productquotation_templates`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
        $return &= Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'productquotation_templates_lang` (
                    `id_productquotation_templates` int(10) NOT NULL,
                    `id_lang` int(10) NOT NULL,
                    `title` varchar(255) NOT NULL,
                    `form` text,
                    PRIMARY KEY (`id_productquotation_templates`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );
        $return &= Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'productquotation_templates_shop` (
                    `id_productquotation_templates` int(10) NOT NULL,
                    `id_shop` int(10) NOT NULL,
                    PRIMARY KEY (`id_productquotation_templates`, `id_shop`),
                    KEY `id_shop` (`id_shop`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );
        $return &= Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes` (
                    `id_quotes` int(10) NOT NULL auto_increment,
                    `id_lang` int(10) NOT NULL,
                    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id_quotes`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );
        $return &= Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes_products` (
                    `id_quotes_products` int(10) NOT NULL auto_increment,
                    `id_quotes` int(10) NOT NULL,
                    `id_product` int(10) NOT NULL,
                    `combination` int(10) NOT NULL,
                    `qty` int(10) NOT NULL,
                    PRIMARY KEY (`id_quotes_products`, `id_quotes`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );
        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quote_data` (
                    `id_quote_data` int(10) NOT NULL auto_increment,
                    `id_quote` int(10) NOT NULL,
                    `title` varchar(255) NOT NULL,
                    `value` text,
                    PRIMARY KEY (`id_quote_data`, `id_quote`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quote_data_file` (
                    `file_id` int(10) NOT NULL auto_increment,
                    `id_quotation` int(10) NOT NULL,
                    `id_customer` int(10) NOT NULL,
                    `file` varchar(255) NOT NULL,
                    PRIMARY KEY (`file_id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quote_messages` (
                    `id_quote_messages` int(10) NOT NULL auto_increment,
                    `id_quotation` int(10) NOT NULL,
                    `message` text,
                    `author` int(10) NOT NULL,
                    `read` int(10) NOT NULL,
                    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id_quote_messages`, `id_quotation`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields(
        `id_fmm_quote_fields`       int(11) unsigned NOT NULL auto_increment,
        `field_type`            enum(\'text\',\'textarea\',\'date\',\'boolean\',\'multiselect\',\'select\',\'checkbox\',\'radio\',\'message\',\'image\',\'attachment\') default \'text\',
        `field_validation`      varchar(255) default NULL,
        `position`              tinyint(4) default 0,
        `assoc_shops`           varchar(255) default ' . (string) Context::getContext()->shop->id . ',
        `value_required`        tinyint(1) default NULL,
        `editable`              tinyint(1) default 1,
        `extensions`            varchar(128) DEFAULT \'jpg\',
        `attachment_size`       DECIMAL(10,2) NOT NULL DEFAULT \'2.0\',
        `alert_type`            varchar(30) default NULL,
        `show_customer`         tinyint(1) default NULL,
        `show_email`            tinyint(1) default NULL,
        `show_admin`            tinyint(1) default NULL,
        `active`                tinyint(1) default NULL,
        `dependant`             tinyint(1) default \'0\',
        `dependant_field`       int(11) default \'0\',
        `dependant_value`       int(11) default \'0\',
        `limit`                 int(11) default \'0\',
        `id_heading`            int(11) default \'0\',
        `created_time`          datetime default NULL,
        `update_time`           datetime default NULL,
        PRIMARY KEY             (`id_fmm_quote_fields`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields_lang(
        `id_fmm_quote_fields`       int(11) NOT NULL auto_increment,
        `id_lang`               int(11) NOT NULL,
        `field_name`            varchar(255) default NULL,
        `default_value`         varchar(255) default NULL,
        PRIMARY KEY             (`id_fmm_quote_fields`,`id_lang`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields_values(
        `field_value_id`        int(11) NOT NULL auto_increment,
        `id_fmm_quote_fields`       int(11) NOT NULL,
        PRIMARY KEY             (`field_value_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields_values_lang(
        `field_value_id`        int(11) NOT NULL,
        `id_lang`               int(11) NOT NULL DEFAULT ' . (int) Configuration::get('PS_LANG_DEFAULT') . ',
        `field_value`           text,
        PRIMARY KEY             (`field_value_id`, `id_lang`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_userdata(
        `value_id`              int(10) unsigned NOT NULL auto_increment,
        `id_fmm_quote_fields`       int(10) unsigned default NULL,
        `id_customer`           int(10) unsigned default NULL,
        `id_guest`              int(10) unsigned default 0,
        `field_value_id`        mediumtext,
        `value`                 mediumtext,
        `id_quote`              int(10) unsigned default 0,
        PRIMARY KEY             (`value_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fmm_quote_fee` (
                    `id_productquotation` int(10) NOT NULL auto_increment,
                    `id_cart` int(10) NOT NULL,
                    `id_product` int(10) NOT NULL,
                    `name` text,
                    PRIMARY KEY (`id_productquotation`, `id_cart`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

        return $return;
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminQuotes';
        $tab->id_parent = 0;
        $tab->module = $this->name;
        $tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('Product Quotations');
        $tab->add();
        $tab = new Tab();
        $tab->class_name = 'AdminProductQuotes';
        $tab->id_parent = Tab::getIdFromClassName('AdminQuotes');
        $tab->module = $this->name;
        $tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('Product Quotes');
        $tab->add();
        // $subtab = new Tab();
        // $subtab->class_name = 'AdminProductQuotation';
        // $subtab->id_parent = Tab::getIdFromClassName('AdminQuotes');
        // $subtab->module = $this->name;
        // $subtab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('Quotation Templates');
        // $subtab->add();
        $thirdtab = new Tab();
        $thirdtab->class_name = 'AdminQuotationMessages';
        $thirdtab->id_parent = Tab::getIdFromClassName('AdminQuotes');
        $thirdtab->module = $this->name;
        $thirdtab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('Quotation Messages');
        $thirdtab->add();

        $fortab = new Tab();
        $fortab->class_name = 'AdminQuoteFields';
        $fortab->id_parent = Tab::getIdFromClassName('AdminQuotes');
        $fortab->module = $this->name;
        $fortab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Manage Form Fields');
        
        $fortab->add();

        return true;
    }

    public function uninstall()
    {
        return ( parent::uninstall()
            && $this->uninstallDB()
            && $this->uninstallTab()
            && Configuration::deleteByName('PQUOTE_CART')
            && Configuration::deleteByName('PQUOTE_CATALOG')
            && Configuration::deleteByName('PQUOTE_LISTING')
            && Configuration::deleteByName('PQUOTE_TAX')
            && Configuration::deleteByName('PQUOTE_STOCK')
            && Configuration::deleteByName('groupBox')
            && Configuration::deleteByName('PQUOTE_PRICE'));
    }

    public function uninstallTab()
    {
        $id_tab_quotes = (int)Tab::getIdFromClassName('AdminProductQuotes');
        if ($id_tab_quotes) {
            $tab2 = new Tab($id_tab_quotes);
            return $tab2->delete();
        } else {
            return false;
        }
    }

    public function uninstallDB()
    {
        return (Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'productquotation`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'productquotation_templates`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'productquotation_templates_lang`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'quotes`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'quotes_products`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'quote_data`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fmm_quote_fields`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fmm_quote_fields_lang`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fmm_quote_fields_values_lang`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fmm_quote_fields_values`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fmm_quote_userdata`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'quote_data_file`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fmm_quote_fee`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'quote_messages`')
                && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'productquotation_templates_shop`'));
    }

    public function initProcess()
    {
        $action = Tools::getValue('action');
        if ($action == 'getSearchProducts') {
            $this->getSearchProducts();
            die();
        }
    }
    
    public function postProcess()
    {
        $criterea_value = '';
        if (Tools::isSubmit('submitPquote')) {
            $criterea = (int)Tools::getValue('PQUOTE_SELECTION_CRITEREA');
            if ($criterea == 1) {
                $criterea_value = Tools::getValue('product');
                if (!empty($criterea_value)) {
                    $criterea_value = implode(',', $criterea_value);
                }
            } elseif ($criterea == 2) {
                $criterea_value = Tools::getValue('category');
                if (!empty($criterea_value)) {
                    $criterea_value = implode(',', $criterea_value);
                }
            }
            $languages = Language::getLanguages(false);
            $values = array();
            foreach ($languages as $lang) {
                $values['PQUOTE_HEAD'][$lang['id_lang']] = Tools::getValue('PQUOTE_HEAD_'.$lang['id_lang']);
                $values['PQUOTE_MESSAGE'][$lang['id_lang']] = Tools::getValue('PQUOTE_MESSAGE_'.$lang['id_lang']);
                $values['PQUOTE_HEAD_CART'][$lang['id_lang']] = Tools::getValue('PQUOTE_HEAD_CART_'.$lang['id_lang']);
            }

            Configuration::updateValue('PQUOTE_HEAD', $values['PQUOTE_HEAD']);
            Configuration::updateValue('PQUOTE_HEAD_CART', $values['PQUOTE_HEAD_CART']);
            Configuration::updateValue('PQUOTE_MESSAGE', $values['PQUOTE_MESSAGE']);
            Configuration::updateValue('PQUOTE_SENDMAIL_ADMIN', Tools::getValue('PQUOTE_SENDMAIL_ADMIN'));

            Configuration::updateValue('PQUOTE_PRICE', Tools::getValue('PQUOTE_PRICE'));

            $approval_states = (Tools::getValue('groupBox')) ? implode(',', Tools::getValue('groupBox')) : '';

            Configuration::updateValue(
                'groupBox',
                $approval_states,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            Configuration::updateValue('PQUOTE_CART', Tools::getValue('PQUOTE_CART'));
            Configuration::updateValue('PQUOTE_CATALOG', Tools::getValue('PQUOTE_CATALOG'));
            Configuration::updateValue('PQUOTE_LISTING', Tools::getValue('PQUOTE_LISTING'));
            Configuration::updateValue('PQUOTE_TAX', Tools::getValue('PQUOTE_TAX'));
            Configuration::updateValue('PQUOTE_STOCK', Tools::getValue('PQUOTE_STOCK'));

            Configuration::updateValue('PQUOTE_ADMIN_EMAIL', Tools::getValue('PQUOTE_ADMIN_EMAIL'));
            Configuration::updateValue('PQUOTE_VOUCHER', Tools::getValue('PQUOTE_VOUCHER'));
            Configuration::updateValue('PQUOTE_SELECTION_CRITEREA', $criterea);
            Configuration::updateValue('PQUOTE_CRITEREA_VALUE', $criterea_value);
            $this->_clearCache('hook.tpl');
            return $this->displayConfirmation($this->l('The settings have been updated.'));
        }
        return '';
    }

    public function getContent()
    {
        $this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
        return $this->initProcess().$this->postProcess().$this->html.$this->renderForm();
    }

    public function renderForm()
    {
        $groups = Group::getGroups($this->context->language->id, true);

        $products = array();
        $craeterea = (int)Configuration::get('PQUOTE_SELECTION_CRITEREA');

        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.6', '>=')) {
            $status_admin = array(
                'type' => 'switch',
                'label' => $this->l('Notify Admin by Email?'),
                'name' => 'PQUOTE_SENDMAIL_ADMIN',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'maomail_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'maomail_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                'hint' => $this->l('Send email to admin each time user asks question/replies.')
                );
            $status_price = array(
                'type' => 'switch',
                'label' => $this->l('Hide Price on Product Page'),
                'name' => 'PQUOTE_PRICE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'price_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'price_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                'hint' => $this->l('If you want hide price on Product page then enable this option')
                );

            $status_tax = array(
                'type' => 'switch',
                'label' => $this->l('Tax Included in Product Price / Cart Rules'),
                'name' => 'PQUOTE_TAX',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'price_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'price_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                'hint' => $this->l('If you want include tax in Price then Enable this')
                );

            $status_stock = array(
                'type' => 'switch',
                'label' => $this->l('Show Quote also Out of Stock Product'),
                'name' => 'PQUOTE_STOCK',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'price_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'price_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                'hint' => $this->l('If you want show Quote option on out of stock product')
                );

            $status_cart = array(
                'type' => 'switch',
                'label' => $this->l('Hide Add to Cart button'),
                'name' => 'PQUOTE_CART',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'cart_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'cart_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                'hint' => $this->l('If you want hide Add to cart button on Product page then enable this')
                );

            $status_catalog = array(
                'type' => 'switch',
                'label' => $this->l('Enable module Only in Catalog mode'),
                'name' => 'PQUOTE_CATALOG',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'cart_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'cart_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                'hint' => $this->l('Enable this option if you want enable module only in Catalog mode')
                );

            $status_listing = array(
                'type' => 'switch',
                'label' => $this->l('Hide Quote button On listing page'),
                'name' => 'PQUOTE_LISTING',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'listing_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'listing_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                'hint' => $this->l('If you want hide Quote Button on listing page then enable this option')
                );

            $status_group = array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'name' => 'groupBox',
                    'values' => $groups,
                    'required' => true,
                    'col' => '6',
                    'hint' => $this->l('')
            );
        } else {
            $status_admin = array(
                'type' => 'radio',
                'label' => $this->l('Notify Admin by Email?'),
                'name' => 'PQUOTE_SENDMAIL_ADMIN',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                        ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                        )
                    ),
                );
             $status_price = array(
                'type' => 'radio',
                'label' => $this->l('Price Show when Enable Quotation'),
                'name' => 'PQUOTE_PRICE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                        ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                        )
                    ),
                );

             $status_cart = array(
                'type' => 'radio',
                'label' => $this->l('Add to Cart button Hide'),
                'name' => 'PQUOTE_CART',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                        ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                        )
                    ),
                );

             $status_listing = array(
                'type' => 'radio',
                'label' => $this->l('Hide Quote button On listing page'),
                'name' => 'PQUOTE_LISTING',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                        ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                        )
                    ),
                );
        }
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-envelope-o'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Button Text'),
                        'name' => 'PQUOTE_HEAD',
                        'desc' => $this->l('Enter Heading for the product page button or leave empty')
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Cart Button Text'),
                        'name' => 'PQUOTE_HEAD_CART',
                        'desc' => $this->l('Enter Heading for the cart page button or leave empty')
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Voucher Expiry(HOURS)'),
                        'name' => 'PQUOTE_VOUCHER',
                        'desc' => $this->l('Default voucher expiry time in Hours.')
                    ),
                    array(
                    'type' => 'textarea',
                    'label' => $this->l('Email Message to User:'),
                    'name' => 'PQUOTE_MESSAGE',
                    'lang' => true,
                    'cols' => 60,
                    'rows' => 10,
                    'class' => 'rte',
                    'hint' => $this->l('Invalid characters:').' <>;=#{}'
                    ),

                    
                    $status_admin,
                    $status_group,
                    $status_price,
                    $status_cart,
                    $status_listing,
                    $status_catalog,
                    $status_tax,
                    $status_stock,

                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Administrator Email:'),
                        'name' => 'PQUOTE_ADMIN_EMAIL',
                        'desc' => $this->l('Leave empty for default email')
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Eable module for?:'),
                        'name' => 'products',
                        'values' => array()
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $id_lang = (int)$this->context->language->id;
        $categories = Category::getSimpleCategories($id_lang);

       

        if ($craeterea == 1) {
            $criterea_value = Configuration::get('PQUOTE_CRITEREA_VALUE');
            if (!empty($criterea_value)) {
                $products = explode(',', $criterea_value);
                if (!empty($products) && is_array($products)) {
                    foreach ($products as &$product) {
                        $product = new Product((int)$product, true, (int)$id_lang);
                        $product->id_product_attribute = (int)Product::getDefaultAttribute(
                            $product->id
                        ) > 0 ? (int)Product::getDefaultAttribute($product->id) : 0;
                        $_cover = ((int)$product->id_product_attribute > 0) ? Product::getCombinationImageById(
                            (int)$product->id_product_attribute,
                            $id_lang
                        ) : Product::getCover($product->id);
                        if (!is_array($_cover)) {
                            $_cover = Product::getCover($product->id);
                        }
                        $product->id_image = $_cover['id_image'];
                    }
                }
            }
        }

        $url = $this->context->link->getAdminLink('AdminModules', true);
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPquote';
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'categories' => $categories,
            'products' => $products,
            'ps_17' => $ps_17,
            'action_url' =>
            $url.'&configure=productquotation&action=getSearchProducts&forceJson=1'.
            '&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20'
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        $criterea_value = Configuration::get('PQUOTE_CRITEREA_VALUE');
        if (!empty($criterea_value)) {
            $criterea_value = explode(',', $criterea_value);
        } else {
            $criterea_value = array();
        }
        $languages = Language::getLanguages(false);
        $fields = array();
        foreach ($languages as $lang) {
            $fields['PQUOTE_HEAD'][$lang['id_lang']] = Tools::getValue(
                'PQUOTE_HEAD_'.$lang['id_lang'],
                Configuration::get(
                    'PQUOTE_HEAD',
                    $lang['id_lang']
                )
            );
            $fields['PQUOTE_MESSAGE'][$lang['id_lang']] = Tools::getValue(
                'PQUOTE_MESSAGE'.$lang['id_lang'],
                Configuration::get(
                    'PQUOTE_MESSAGE',
                    $lang['id_lang']
                )
            );
            $fields['PQUOTE_HEAD_CART'][$lang['id_lang']] = Tools::getValue(
                'PQUOTE_HEAD_CART_'.$lang['id_lang'],
                Configuration::get(
                    'PQUOTE_HEAD_CART',
                    $lang['id_lang']
                )
            );
        }
        $fields['PQUOTE_SENDMAIL_ADMIN'] = (int)Configuration::get('PQUOTE_SENDMAIL_ADMIN');
        $fields['PQUOTE_PRICE'] = (int)Configuration::get('PQUOTE_PRICE');
        
        //$selected_groups = Configuration::get('groupBox');

        $approval_states = (Configuration::get(
            'groupBox',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ) ? explode(
            ',',
            Configuration::get('groupBox', null, $this->context->shop->id_shop_group, $this->context->shop->id)
        ) : '');

        $fields['groupBox_1'] = '';
        $fields['groupBox_2'] = '';
        $fields['groupBox_3'] = '';

        foreach ($approval_states as $group) {
            $fields['groupBox_'.$group] = 'true';
        }

        $fields['PQUOTE_CART'] = (int)Configuration::get('PQUOTE_CART');
        $fields['PQUOTE_CATALOG'] = (int)Configuration::get('PQUOTE_CATALOG');
        $fields['PQUOTE_LISTING'] = (int)Configuration::get('PQUOTE_LISTING');
        $fields['PQUOTE_TAX'] = (int)Configuration::get('PQUOTE_TAX');
        $fields['PQUOTE_STOCK'] = (int)Configuration::get('PQUOTE_STOCK');
        $fields['PQUOTE_ADMIN_EMAIL'] = Configuration::get('PQUOTE_ADMIN_EMAIL');
        $fields['PQUOTE_VOUCHER'] = Configuration::get('PQUOTE_VOUCHER');
        $fields['PQUOTE_SELECTION_CRITEREA'] = Configuration::get('PQUOTE_SELECTION_CRITEREA');
        $fields['selection'] = $criterea_value;

        return $fields;
    }

    public function getPrice(
        $id,
        $tax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1
    ) {
        return Product::getPriceStatic(
            $id,
            $tax,
            $id_product_attribute,
            $decimals,
            $divisor,
            $only_reduc,
            $usereduc,
            $quantity
        );
    }

    public function hookDisplayProductButtons($params)
    {
        $jQuery_path = Media::getJqueryPath(_PS_JQUERY_VERSION_);
        
        if (is_array($jQuery_path) && isset($jQuery_path[0])) {
            $jQuery_path = $jQuery_path[0];
        }
        $this->context->smarty->assign(array('jQuery_path' => $jQuery_path));

        $groups = Customer::getGroupsStatic($this->context->customer->id);
        $upgroups = (Configuration::get('groupBox') ? explode(',', Configuration::get('groupBox')) : '');
        $group_access = '';
        if ($upgroups != null) {
            $group_access = array_intersect($upgroups, $groups);
        }
        if (isset($upgroups) && $upgroups && isset($group_access) && $group_access) {
            $catalog_status = (int)Configuration::get('PQUOTE_CATALOG');
            $catalog_status_presta = (int)Configuration::get('PS_CATALOG_MODE');

            if ($catalog_status == 1
                && $catalog_status_presta == 1
                || $catalog_status == 0
                && $catalog_status_presta == 1
                || $catalog_status == 0
                && $catalog_status_presta == 0
            ) {
                $lang_id = (int)$this->context->language->id;
                $id_product = (int)(isset($params) && isset(
                    $params['id_product']
                ) ? $params['id_product'] : ((isset(
                    $params
                ) && is_object($params['product'])) ? $params['product']->id : Tools::getValue('id_product')));

                //$price = Product::getPriceStatic($id_product);//tax exclude
        
                $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
                $criterea = (int)Configuration::get('PQUOTE_SELECTION_CRITEREA');
                $hide_price = (int)Configuration::get('PQUOTE_PRICE');
                $hide_cart = (int)Configuration::get('PQUOTE_CART');
                $tax_status = (int)Configuration::get('PQUOTE_TAX');
                $stock_status = (int)Configuration::get('PQUOTE_STOCK');

                //$controller = 'ajax';

                $coo = new Cookie('psFront');
                $id_employee = (int)$coo->__get('id_employee');
                $token = Tools::getAdminToken('Ajax'.(Tab::getIdFromClassName('Ajax')).$id_employee);

                
                //$token = Tools::getAdminToken($controller);

                $this->context->smarty->assign(array(
                    'gify_url' => $this->_path.'views/img/load.gif',
                    'button_text' => Configuration::get('PQUOTE_HEAD', $lang_id),
                    'hide_price' => $hide_price,
                    'hide_cart' => $hide_cart,
                    'tax_status' => $tax_status,
                    'id_product' => $id_product,
                    'stock_status' => $stock_status,
                    'token' => $token,
                    'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
                    'ps_ver' => (int)$PS_VERSION
                ));
                //If its for all products
                if ($criterea <= 0) {
                    if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                        return $this->display(__FILE__, 'hook17.tpl');
                    } else {
                        return $this->display(__FILE__, 'hook.tpl');
                    }
                } elseif ($criterea > 0) {
                    $criterea_value = Configuration::get('PQUOTE_CRITEREA_VALUE');
                    if (!empty($criterea_value)) {
                        $collection = explode(',', $criterea_value);
                        //If its individual product selection
                        if ($criterea == 1 && in_array($id_product, $collection)) {
                            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                                return $this->display(__FILE__, 'hook17.tpl');
                            } else {
                                return $this->display(__FILE__, 'hook.tpl');
                            }
                        } elseif ($criterea == 2) {
                            //If its category selection
                            $flag = false;
                            $product = new Product((int)$id_product, true, $this->context->language->id);
                            $category_data = $product->getCategories();
                            foreach ($category_data as $category) {
                                if (in_array($category, $collection)) {
                                    //checking needle in hay
                                    $flag = true;
                                }
                            }
                            if ($flag == true) {
                                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                                    return $this->display(__FILE__, 'hook17.tpl');
                                } else {
                                    return $this->display(__FILE__, 'hook.tpl');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookDisplayProductListReviews($params)
    {
        $groups = Customer::getGroupsStatic($this->context->customer->id);
        $upgroups = (Configuration::get('groupBox') ? explode(',', Configuration::get('groupBox')) : '');
        $group_access = '';
        if ($upgroups != null) {
            $group_access = array_intersect($upgroups, $groups);
        }
        

        $coo = new Cookie('psFront');
        $id_employee = (int)$coo->__get('id_employee');
        $token = Tools::getAdminToken('Ajax'.(Tab::getIdFromClassName('Ajax')).($id_employee));


        if (isset($upgroups) && $upgroups && isset($group_access) && $group_access) {
            $catalog_status = (int)Configuration::get('PQUOTE_CATALOG');
            $catalog_status_presta = (int)Configuration::get('PS_CATALOG_MODE');

            if ($catalog_status == 1
                && $catalog_status_presta == 1
                || $catalog_status == 0
                && $catalog_status_presta == 1
                || $catalog_status == 0
                && $catalog_status_presta == 0
            ) {
                $page_name= Dispatcher::getInstance()->getController();
                $stock_status = (int)Configuration::get('PQUOTE_STOCK');
        
                $hide_listing = (int)Configuration::get('PQUOTE_LISTING');
                if ($hide_listing == 0 && $page_name != 'product') {
                    $lang_id = (int)$this->context->language->id;
                    $id_product = $params['product']['id_product'];

                    $stock_result = StockAvailable::getQuantityAvailableByProduct($id_product, 0);
                    if ($stock_result <= 0 && $stock_status == 0) {
                        return false;
                    }
                    $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
                    $PS_1780 = (Tools::version_compare(_PS_VERSION_, '1.7.8.0', '>=') == true) ? 1 : 0;
                    $criterea = (int)Configuration::get('PQUOTE_SELECTION_CRITEREA');
                    $hide_price = (int)Configuration::get('PQUOTE_PRICE');

                    $hide_cart = (int)Configuration::get('PQUOTE_CART');

                    $this->context->smarty->assign(array(
                        'gify_url' => $this->_path.'views/img/load.gif',
                        'button_text' => Configuration::get('PQUOTE_HEAD', $lang_id),
                        'hide_price' => $hide_price,
                        'hide_cart' => $hide_cart,
                        'p_id' => $id_product,
                        'token' => $token,
                        'ps_ver' => (int)$PS_VERSION,
                        'ps_1780' => $PS_1780
                    ));
        
                    //If its for all products
                    if ($criterea <= 0) {
                        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                            return $this->display(__FILE__, 'hook_list17.tpl');
                        } else {
                            return $this->display(__FILE__, 'hook_list.tpl');
                        }
                    } elseif ($criterea > 0) {
                        $criterea_value = Configuration::get('PQUOTE_CRITEREA_VALUE');

                        if (!empty($criterea_value)) {
                            $collection = explode(',', $criterea_value);

                            //If its individual product selection
                            if ($criterea == 1 && in_array($id_product, $collection)) {
                                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                                    return $this->display(__FILE__, 'hook_list17.tpl');
                                } else {
                                    return $this->display(__FILE__, 'hook_list.tpl');
                                }
                            } elseif ($criterea == 2) {
                                //If its category selection
                                $flag = false;
                                $product = new Product((int)$id_product, true, $this->context->language->id);
                                $category_data = $product->getCategories();
                                foreach ($category_data as $category) {
                                    if (in_array($category, $collection)) {
                                    //checking needle in hay
                                        $flag = true;
                                    }
                                }
                                if ($flag == true) {
                                    if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                                        return $this->display(__FILE__, 'hook_list17.tpl');
                                    } else {
                                        return $this->display(__FILE__, 'hook_list.tpl');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookDisplayTop()
    {
        $groups = Customer::getGroupsStatic($this->context->customer->id);
        $upgroups = (Configuration::get('groupBox') ? explode(',', Configuration::get('groupBox')) : '');
        $group_access = '';
        if ($upgroups != null) {
            $group_access = array_intersect($upgroups, $groups);
        }
       
        if (isset($upgroups) && $upgroups && isset($group_access) && $group_access) {
            $model = new Quote;
            $id_quote = (int)$this->context->cookie->id_quote;
            $count = (int)$model->getCount($id_quote);
            $tax_status = (int)Configuration::get('PQUOTE_TAX');

            $products = $model->getQuoteProducts($id_quote, $tax_status);
            $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
            $this->context->smarty->assign(array(
                'count' => $count,
                'products' => $products,
                'tax_status' => $tax_status,
                'ps_ver' => (int)$PS_VERSION
            ));

            return $this->display(__FILE__, 'top.tpl');
        }
    }

    public function hookHeader()
    {
        $delete_dis = Tools::getValue('deleteDiscount');
        $del_pro = Tools::getValue('delete');
        if ($del_pro) {
            $id_product_del = Tools::getValue('id_product');
            $is_fee_product = FieldsQuote::isFeeProductId($id_product_del);

            $pp = new Cart($this->context->cart->id);
            $aapplied = $pp->getCartRules();
            
            if ($is_fee_product !=0 && $aapplied) {
                $quantity = 1;
                $id_product_attribute = 0;
                $operator = 'up';
                $id_cust = false;
                $id_add_dli = 0;
                $this->context->cart->updateQty(
                    (int)$quantity,
                    (int)$id_product_del,
                    (int)$id_product_attribute,
                    $id_cust,
                    $operator,
                    (int)$id_add_dli
                );
            }
        }
        if ($delete_dis) {
            $id_cart_rule = Tools::getValue('deleteDiscount');
            $id_productquotation = FieldsQuote::getCouponVal($id_cart_rule);
            $id_productfee = FieldsQuote::getFeeProductId($id_productquotation);

            if ($id_productfee) {
                $id_product_attribute = 0;
                $operator = 'up';
                $id_add_dliv = 0;
                $id_custom = false;
                $this->context->cart->deleteProduct($id_productfee, $id_product_attribute, $id_custom, $id_add_dliv);
            }
        }
        $ps_ver = _PS_VERSION_;
        if ($ps_ver < '1.7') {
            $cartrule = Tools::getValue('addingCartRule');
            if ($cartrule) {
                $id_cart = $this->context->cart->id;
                $cart = new Cart($id_cart);
                $ruleofcart = $cart->getCartRules();
                $id_cart_rule = $ruleofcart[0]['id_cart_rule'];
                
                $pp = new Cart($this->context->cart->id);
                $applied = $pp->getCartRules();
                $id_productquotation = FieldsQuote::getCouponVal($id_cart_rule);
                $id_productfee = FieldsQuote::getFeeProductId($id_productquotation);

                if ($id_productfee) {
                    $quantity = 1;
                    $id_product_attribute = 0;
                    $operator = 'up';
                    $id_cust = false;
                    $id_add_dli = 0;
                    $this->context->cart->updateQty(
                        (int)$quantity,
                        (int)$id_productfee,
                        (int)$id_product_attribute,
                        $id_cust,
                        $operator,
                        (int)$id_add_dli
                    );
                }
            }
        } else {
            $discount_name = Tools::getValue('discount_name');
            if ($discount_name) {
                $id_cart_rule = CartRule::getIdByCode($discount_name);

                $pp = new Cart($this->context->cart->id);
                $applied = $pp->getCartRules();
                $id_productquotation = FieldsQuote::getCouponVal($id_cart_rule);
                $id_productfee = FieldsQuote::getFeeProductId($id_productquotation);

                if ($id_productfee && $applied) {
                    $quantity = 1;
                    $id_product_attribute = 0;
                    $operator = 'up';
                    $id_cust = false;
                    $id_add_dli = 0;
                    $this->context->cart->updateQty(
                        (int)$quantity,
                        (int)$id_productfee,
                        (int)$id_product_attribute,
                        $id_cust,
                        $operator,
                        (int)$id_add_dli
                    );
                }
            }
        }

        $this->context->controller->addJS($this->_path.'views/js/popupscript.js');

        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            return ($this->context->controller->addCSS($this->_path.'views/css/productquote.css')
                || $this->context->controller->addJS($this->_path.'views/js/productquote-17.js'));
        } else {
            return ($this->context->controller->addCSS($this->_path.'views/css/productquote.css')
                || $this->context->controller->addJS($this->_path.'views/js/productquote.js'));
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin.css');
    }

    public function hookModuleRoutes()
    {
        return array(
            'module-productquotation-quote' => array(
                    'controller' => 'quote',
                    'rule' => 'quote',
                    'keywords' => array(),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'productquotation',
                    ),
                ),
            );
    }

    public function hookDisplayShoppingCart()
    {
        $lang_id = $this->context->language->id;
        $tax_status = (int)Configuration::get('PQUOTE_TAX');

        $this->context->smarty->assign(array(
            'button_text' => Configuration::get('PQUOTE_HEAD_CART', $lang_id),
            'tax_status' => $tax_status
        ));
        return $this->display(__FILE__, 'cart.tpl');
    }

    public function hookDisplayCustomerAccount()
    {
        $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $this->context->smarty->assign(array(
            'ps_ver' => $PS_VERSION
        ));
        return $this->display(__FILE__, 'account.tpl');
    }

    public function hookActionExportGDPRData($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = 'SELECT pd.*, qpd.*,qd.*
            FROM `'._DB_PREFIX_.'productquotation` pd
            LEFT JOIN `'._DB_PREFIX_.'quotes_products` qpd ON (pd.`id_quote` = qpd.`id_quotes`)
            LEFT JOIN `'._DB_PREFIX_.'quote_data` qd ON (pd.`id_quote` = qd.`id_quote`)
            WHERE pd.`id_customer` = '.(int)($customer['id']).'';
            $res = Db::getInstance()->ExecuteS($sql);
            $result = array();
            foreach ($res as $key => $res1) {
                $result[$key][$this->l('First Name')] = $customer['firstname'];
                $result[$key][$this->l('Last Name')] = $customer['lastname'];
                $result[$key][$this->l('Email')] = $res1['email'];
                $result[$key][$this->l('Product Name')] = Product::getProductName($res1['id_product']);
                $result[$key][$this->l('Title')] = $res1['title'];
                $result[$key][$this->l('Values')] = $res1['value'];
            } if ($result) {
                return json_encode($result);
            } else {
                return json_encode($this->l('ProductQuotation Popup : Unable to export customer using email.'));
            }
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = 'DELETE pd.*, qpd.*,qd.*
            FROM `'._DB_PREFIX_.'productquotation` pd
            LEFT JOIN `'._DB_PREFIX_.'quotes_products` qpd ON (pd.`id_quote` = qpd.`id_quotes`)
            LEFT JOIN `'._DB_PREFIX_.'quote_data` qd ON (pd.`id_quote` = qd.`id_quote`)
            WHERE pd.`id_customer` = '.(int)($customer['id']).'';
            if (Db::getInstance()->execute($sql)) {
                return json_encode(true);
            }
            return json_encode($this->l('ProductQuotation : Unable to delete customer using email.'));
        }
    }
    
    protected function getSearchProducts()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            die(json_encode($this->l('Found Nothing.')));
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $forceJson = Tools::getValue('forceJson', false);
        $disableCombination = Tools::getValue('disableCombination', false);
        $excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', true);
        $exclude_packs = (bool)Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql = '
        SELECT p.`id_product`,
        pl.`link_rewrite`,
        p.`reference`,
        pl.`name`,
        image_shop.`id_image` id_image,
        il.`legend`,
        p.`cache_default_attribute`
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.
                (int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
                LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.
                    (int)$context->shop->id.')
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.
                (int)$context->language->id.')
                WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.
                (!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
                ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.
                    'product_download` pd WHERE (pd.id_product = p.id_product))' : '').
                ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '').
                ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);
        if ($items && ($disableCombination ||$excludeIds)) {
            $results = array();
            foreach ($items as $item) {
                if (!$forceJson) {
                    $item['name'] = str_replace('|', '&#124;', $item['name']);
                    $results[] = trim($item['name']).(
                        !empty($item['reference']) ? ' (ref: '.$item['reference'].')' : ''
                    ).'|'.(int)$item['id_product'];
                } else {
                    $cover = Product::getCover($item['id_product']);
                    $results[] = array(
                        'id' => $item['id_product'],
                        'name' => $item['name'].(!empty($item['reference']) ? ' (ref: '.$item['reference'].')' : ''),
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                (
                                    ($item['id_image'])?$item['id_image']:$cover['id_image']
                                ),
                                $this->getFormatedName('home')
                            )
                        ),
                    );
                }
            }

            if (!$forceJson) {
                echo implode("\n", $results);
            } else {
                echo json_encode($results);
            }
        } elseif ($items) {
            // packs
            $results = array();
            foreach ($items as $item) {
                // check if product have combination
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = '
                    SELECT pa.`id_product_attribute`,
                    pa.`reference`,
                    ag.`id_attribute_group`,
                    pai.`id_image`,
                    agl.`name` AS group_name,
                    al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `'._DB_PREFIX_.'product_attribute` pa
                            '.Shop::addSqlAssociation('product_attribute', 'pa').'
                            LEFT JOIN `'._DB_PREFIX_.
                            'product_attribute_combination` pac ON pac.`id_product_attribute` =
                            pa.`id_product_attribute`
                            LEFT JOIN `'._DB_PREFIX_.
                            'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `'._DB_PREFIX_.
                            'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `'._DB_PREFIX_.
                            'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.
                            (int)$context->language->id.')
                            LEFT JOIN `'._DB_PREFIX_.
                            'attribute_group_lang` agl ON (ag.`id_attribute_group` =
                                agl.`id_attribute_group` AND agl.`id_lang` = '.
                            (int)$context->language->id.')
                            LEFT JOIN `'._DB_PREFIX_.
                            'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = '.(int)$item['id_product'].'
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $k => $combination) {
                            $cover = Product::getCover($item['id_product']);
                            $results[$k['id_product_attribute']]['id'] = $item['id_product'];

                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] =
                            $combination['id_product_attribute'];
                            !empty(
                                $results[$combination['id_product_attribute']]['name']
                            ) ? $results[$combination['id_product_attribute']]['name'] .=
                            ' '.$combination['group_name'].'-'.
                            $combination['attribute_name']
                            : $results[$combination['id_product_attribute']]['name'] =
                            $item['name'].' '.$combination['group_name'].'-'.$combination['attribute_name'];
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] =
                                !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                $results[$combination['id_product_attribute']]['image'] = str_replace(
                                    'http://',
                                    Tools::getShopProtocol(),
                                    $context->link->getImageLink(
                                        $item['link_rewrite'],
                                        (($combination['id_image'])?$combination['id_image']:$cover['id_image']),
                                        $this->getFormatedName('home')
                                    )
                                );
                            }
                        }
                    } else {
                        $results[] = array(
                            'id' => $item['id_product'],
                            'name' => $item['name'],
                            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace(
                                'http://',
                                Tools::getShopProtocol(),
                                $context->link->getImageLink(
                                    $item['link_rewrite'],
                                    $item['id_image'],
                                    $this->getFormatedName('home')
                                )
                            ),
                        );
                    }
                } else {
                    $results[] = array(
                        'id' => $item['id_product'],
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                $item['id_image'],
                                $this->getFormatedName('home')
                            )
                        ),
                    );
                }
            }
            echo json_encode(array_values($results));
        } else {
            echo json_encode(array());
        }
    }
    
    public function getFormatedName($name)
    {
        $theme_name = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(array('_'.$theme_name, $theme_name.'_'), '', $name);
        //check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name, 'products')) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name.'_'.$theme_name, 'products')) {
            return $name_without_theme_name.'_'.$theme_name;
        } elseif (ImageType::getByNameNType($theme_name.'_'.$name_without_theme_name, 'products')) {
            return $theme_name.'_'.$name_without_theme_name;
        } else {
            return $name_without_theme_name.'_default';
        }
    }

    protected function getTranslatableText()
    {
        return array(
            'invalid' => $this->l('is invalid.'),
            'required' => $this->l('is required.'),
            'type' => $this->l('invalid file type.'),
            'size' => $this->l('size exceeds the limit.'),
            'limit' => $this->l('character size exceeds the limit.'),
            'upload_error' => $this->l('An error occurred while attempting to upload the file.'),
            'update_success' => $this->l('Registration fields updated successfully.')
        );
    }

    public function generateQuotePdf($id_quote, $id_quotation, $display = true)
    {
        include_once(dirname(_PS_MODULE_DIR_).'/modules/productquotation/HTMLTemplateCustomPdf.php');

        $object = Tools::jsonDecode(Tools::jsonEncode([
            'id_quote' => $id_quote,
            'id_quotation' => $id_quotation
        ]));

        $cpdf = new HTMLTemplateCustomPdf(
            $object,
            'CustomPdf',
            Context::getContext()->smarty
        );

        $pdf = new PDF($cpdf, 'CustomPdf', Context::getContext()->smarty);
        return $pdf->render($display);
    }
}
