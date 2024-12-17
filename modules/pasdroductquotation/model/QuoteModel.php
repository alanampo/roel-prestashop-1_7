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

class Quote extends ObjectModel
{
    public $id_productquotation_templates;
    public $status;
    public $title;

    public static $definition = array(
        'table' => 'productquotation_templates',
        'primary' => 'id_productquotation_templates',
        'multilang' => true,
        'fields' => array(
            'status' => array('type' => self::TYPE_BOOL),
            'title' =>  array('type' => self::TYPE_STRING)
        ),
    );

    public function addNewTemplate($status)
    {
        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.
        'productquotation_templates SET `status` = '.(int)$status);
        $last_id = (int)Db::getInstance()->Insert_ID();
        return $last_id;
    }

    public static function setMessageAsRead($id_quotation, $author = 1)
    {
        if (!$id_quotation) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
            'quote_messages',
            ['read' => 1],
            sprintf('id_quotation = %d AND author = %d', (int) $id_quotation, (int) $author)
        );
    }

    public function addNewTemplateLangs($id, $title, $content, $langs)
    {
        $languages = Language::getLanguages();
        if ($langs[0] == 0) {
            foreach ($languages as $lang) {
                Db::getInstance()->execute('
                    INSERT INTO '._DB_PREFIX_.
                    'productquotation_templates_lang (id_productquotation_templates, id_lang, title, form)
                    VALUES('.(int)$id.', '.(int)$lang['id_lang'].',
                    "'.pSQL($title).'", "'.pSQL($content, true).'")
                ');
            }
        } else {
            foreach ($langs as $lang) {
                Db::getInstance()->execute('
                    INSERT INTO '._DB_PREFIX_.
                    'productquotation_templates_lang (id_productquotation_templates, id_lang, title, form)
                    VALUES('.(int)$id.', '.(int)$lang.', "'.pSQL($title).'", "'.pSQL($content, true).'")
                ');
            }
        }
    }

    public function addNewTemplateShops($id, $shops)
    {
        $store_shops = Shop::getShops(true, null, false);
        if ($shops[0] == 0) {
            foreach ($store_shops as $shop) {
                Db::getInstance()->execute('
                    INSERT INTO '._DB_PREFIX_.'productquotation_templates_shop
                    (id_productquotation_templates, id_shop)
                    VALUES('.(int)$id.', '.(int)$shop['id_shop'].')
                ');
            }
        } else {
            foreach ($shops as $shop) {
                Db::getInstance()->execute('
                    INSERT INTO '._DB_PREFIX_.'productquotation_templates_shop
                    (id_productquotation_templates, id_shop)
                    VALUES('.(int)$id.', '.(int)$shop.')
                ');
            }
        }
    }

    public function getAllEditData($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT pt.`status`, pl.`title`, pl.`form`
        FROM `'._DB_PREFIX_.'productquotation_templates` pt
        LEFT JOIN `'._DB_PREFIX_.
        'productquotation_templates_lang` pl ON (pt.`id_productquotation_templates` =
        pl.`id_productquotation_templates`)
        WHERE pt.`id_productquotation_templates` = '.(int)$id);
        return $result;
    }

    public function getAllEditDataLang($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT `id_lang`
        FROM `'._DB_PREFIX_.'productquotation_templates_lang`
        WHERE `id_productquotation_templates` = '.(int)$id);
        $new_array = array();
        foreach ($result as $key => $value) {
            $new_array[$key] = $value['id_lang'];
        }
        return $new_array;
    }

    public function getAllEditDataShop($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT `id_shop`
        FROM `'._DB_PREFIX_.'productquotation_templates_shop`
        WHERE `id_productquotation_templates` = '.(int)$id);
        $new_array = array();
        foreach ($result as $key => $value) {
            $new_array[$key] = $value['id_shop'];
        }
        return $new_array;
    }

    public function resetShopsLangs($id)
    {
        Db::getInstance()->execute('
        DELETE FROM '._DB_PREFIX_.'productquotation_templates_lang
        WHERE `id_productquotation_templates` = '.(int)$id);
        Db::getInstance()->execute('
        DELETE FROM '._DB_PREFIX_.'productquotation_templates_shop
        WHERE `id_productquotation_templates` = '.(int)$id);
    }

    public function changeNewTemplate($id, $state)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'productquotation_templates`
            SET `status` = '.(int)$state.'
            WHERE `id_productquotation_templates` = '.(int)$id;

        Db::getInstance()->execute($sql);
    }

    public function insertNewQuote($id_lang)
    {
        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'quotes
                                   SET `id_lang` = '.(int)$id_lang);
        $last_id = (int)Db::getInstance()->Insert_ID();
        return $last_id;
    }

    public function saveNewQuoteData($id, $id_product, $cid, $qty)
    {
        return Db::getInstance()->execute('
        INSERT INTO '._DB_PREFIX_.'quotes_products (id_quotes, id_product, combination, qty)
        VALUES('.(int)$id.', '.(int)$id_product.', '.(int)$cid.', '.(int)$qty.')
        ');
    }

    public function checkPreExistance($id, $id_product, $cid)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `id_quotes_products`
        FROM `'._DB_PREFIX_.'quotes_products`
        WHERE `id_quotes` = '.(int)$id.' AND `id_product` = '.(int)$id_product.' AND
        `combination` = '.(int)$cid);
        return $result['id_quotes_products'];
    }

    public function getCount($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT COUNT(`id_quotes_products`) AS count
        FROM `'._DB_PREFIX_.'quotes_products`
        WHERE `id_quotes` = '.(int)$id);
        return $result['count'];
    }

    public function getQuoteProducts($id, $tax_price)
    {
        if ($tax_price == 0) {
            $tax = false;
        } else {
            $tax = true;
        }
        $link = new Link;
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $id_currency = Context::getContext()->currency->id;
        $id_cart = (int)Context::getContext()->cookie->id_cart;
        $currency = new Currency($id_currency);
        $result = Db::getInstance()->executeS('
        SELECT pq.*, p.`link_rewrite`, p.`name`, i.`id_image`
        FROM `'._DB_PREFIX_.'quotes_products` pq
        LEFT JOIN `'._DB_PREFIX_.'product_lang` p ON (pq.`id_product` = p.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (pq.`id_product` = i.`id_product`)
        WHERE pq.`id_quotes` = '.(int)$id.' AND p.`id_lang` = '.(int)$id_lang.'
        AND p.`id_shop` = '.(int)$id_shop.' AND i.`cover` = 1');
        foreach ($result as &$row) {
            $row['name'] = Product::getProductName($row['id_product'], $row['combination']);
            $product = new Product($row['id_product']);
            $row['customizations'] = $this->getCustomizationFields($row['id_product'], $id_cart);
            $row['link'] = $link->getProductLink($product, null, null, null, null, null, $row['combination']);
            $row_price = Product::getPriceStatic((int)$row['id_product'], $tax, $row['combination'], 2);
            $row['price'] = Product::convertAndFormatPrice($row_price, $currency);
            $row['img_id'] = $this->getProductAttributeImage((int)$row['id_product'], $row['combination']);
            $row['reference'] = $product->reference;
            if ($row['combination']) {
                $comb = new Combination((int) $row['combination']);
                $row['reference'] = $comb->reference;
            }
        }
        return $result;
    }

    public function getQuoteProductsStatic($id)
    {
        return Db::getInstance()->executeS('
        SELECT `id_product`, `combination`, `qty`
        FROM `'._DB_PREFIX_.'quotes_products`
        WHERE `id_quotes` = '.(int)$id);
    }

    public function getQuoteProductsSubmitted($id, $currency, $tax_status)
    {
        if ($tax_status == 0) {
            $tax = false;
        } else {
            $tax = true;
        }

        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $id_productquotation = (int)Tools::getValue('id_productquotation');
        $id_cart = (int)Db::getInstance()->getValue(
            'SELECT `id_cart` FROM '._DB_PREFIX_.
            'productquotation WHERE `id_productquotation` = '.(int)$id_productquotation
        );
        $result = Db::getInstance()->executeS('
        SELECT pq.*, p.`link_rewrite`, p.`name`, i.`id_image`
        FROM `'._DB_PREFIX_.'quotes_products` pq
        LEFT JOIN `'._DB_PREFIX_.'product_lang` p ON (pq.`id_product` = p.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (pq.`id_product` = i.`id_product`)
        WHERE pq.`id_quotes` = '.(int)$id.' AND p.`id_lang` = '.(int)$id_lang.'
        AND p.`id_shop` = '.(int)$id_shop.' AND i.`cover` = 1');
        foreach ($result as &$row) {
            $row['name'] = Product::getProductName($row['id_product'], $row['combination']);
            $product = new Product($row['id_product']);
            $row['customizations'] = $this->getCustomizationFields($row['id_product'], $id_cart);
            $row['link'] = Context::getContext()->link->getProductLink($product, null, null, null, null, null, $row['combination']);
            $row_price = Product::getPriceStatic((int)$row['id_product'], $tax, $row['combination'], 2);
            $row['price'] = Product::convertAndFormatPrice($row_price, $currency);
            $row['img_id'] = $this->getProductAttributeImage((int)$row['id_product'], $row['combination']);
            $row['reference'] = $product->reference;
            if ($row['combination']) {
                $comb = new Combination((int) $row['combination']);
                $row['reference'] = $comb->reference;
            }
        }
        return $result;
    }

    public function dropQuote($id)
    {
        return Db::getInstance()->execute('
        DELETE FROM '._DB_PREFIX_.'quotes_products
        WHERE `id_quotes_products` = '.(int)$id);
    }
    
    public function getQuoteProductsTotal($id, $tax_price)
    {
        if ($tax_price == 0) {
            $tax = false;
        } else {
            $tax = true;
        }

        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sign = Context::getContext()->currency->sign;
        $total = 0;
        $result = Db::getInstance()->executeS('
        SELECT pq.*, p.`link_rewrite`, p.`name`, i.`id_image`
        FROM `'._DB_PREFIX_.'quotes_products` pq
        LEFT JOIN `'._DB_PREFIX_.'product_lang` p ON (pq.`id_product` = p.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (pq.`id_product` = i.`id_product`)
        WHERE pq.`id_quotes` = '.(int)$id.' AND p.`id_lang` = '.(int)$id_lang.'
        AND p.`id_shop` = '.(int)$id_shop.' AND i.`cover` = 1');
        foreach ($result as &$row) {
            $total = $total + Product::getPriceStatic(
                (int)$row['id_product'],
                $tax,
                $row['combination'],
                2
            ) * $row['qty'];
        }
        return $sign.$total;
    }

    public function getQuoteProductsTotalSubmitted($id, $currency, $tax_price)
    {
        if ($tax_price == 0) {
            $tax = false;
        } else {
            $tax = true;
        }

        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $total = 0;
        $result = Db::getInstance()->executeS('
        SELECT pq.*, p.`link_rewrite`, p.`name`, i.`id_image`
        FROM `'._DB_PREFIX_.'quotes_products` pq
        LEFT JOIN `'._DB_PREFIX_.'product_lang` p ON (pq.`id_product` = p.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (pq.`id_product` = i.`id_product`)
        WHERE pq.`id_quotes` = '.(int)$id.' AND p.`id_lang` = '.(int)$id_lang.'
        AND p.`id_shop` = '.(int)$id_shop.' AND i.`cover` = 1');
        foreach ($result as &$row) {
            $total = $total + Product::getPriceStatic(
                (int)$row['id_product'],
                $tax,
                $row['combination'],
                2
            ) * $row['qty'];
        }
        return Product::convertAndFormatPrice($total, $currency);
    }

    public function getQuoteProductsTotalSubmittedWithout($id, $currency, $tax_price)
    {
        if ($tax_price == 0) {
            $tax = false;
        } else {
            $tax = true;
        }

        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $total = 0;
        $result = Db::getInstance()->executeS('
        SELECT pq.*, p.`link_rewrite`, p.`name`, i.`id_image`
        FROM `'._DB_PREFIX_.'quotes_products` pq
        LEFT JOIN `'._DB_PREFIX_.'product_lang` p ON (pq.`id_product` = p.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (pq.`id_product` = i.`id_product`)
        WHERE pq.`id_quotes` = '.(int)$id.' AND p.`id_lang` = '.(int)$id_lang.'
        AND p.`id_shop` = '.(int)$id_shop.' AND i.`cover` = 1');
        foreach ($result as &$row) {
            $total = $total + Product::getPriceStatic(
                (int)$row['id_product'],
                $tax,
                $row['combination'],
                2
            ) * $row['qty'];
        }
        return $total;
    }

    public static function getForm($id_lang, $shop_ids)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'productquotation_templates` c
                '.Shop::addSqlAssociation('productquotation_templates', 'c', false).'
                LEFT JOIN `'._DB_PREFIX_.'productquotation_templates_lang` cl ON 
                (c.`id_productquotation_templates` = cl.`id_productquotation_templates`)
                LEFT JOIN `'._DB_PREFIX_.'productquotation_templates_shop` sp ON 
                (c.`id_productquotation_templates` = sp.`id_productquotation_templates`)
                WHERE cl.`id_lang` = '.(int)$id_lang.'
                AND sp.`id_shop` IN ('.implode(', ', array_map('intval', $shop_ids)).')
                AND c.`status` = 1
                GROUP BY c.`id_productquotation_templates`';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function saveQuote($id, $email, $template, $customer_id, $id_currency, $key)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $id_cart = (int)Context::getContext()->cookie->id_cart;
        return Db::getInstance()->execute('
        INSERT INTO '._DB_PREFIX_.'productquotation (
            `id_quote`,
            `status`,
            `email`,
            `template`,
            `id_customer`,
            `id_currency`,
            `key`,
            `id_shop`,
            `id_lang`,
            `id_cart`
        )
        VALUES('.(int)$id.', 0, "'.pSQL($email).'", '.(int)$template.', '.
        (int)$customer_id.', '.(int)$id_currency.', "'.pSQL($key).'", '.
        (int)$id_shop.', '.(int)$id_lang.', '.(int)$id_cart.')
        ');
    }

    public function saveQuoteData($id, $key, $val)
    {
        return Db::getInstance()->execute('
        INSERT INTO '._DB_PREFIX_.'quote_data (`id_quote`, `title`, `value`)
        VALUES('.(int)$id.', "'.pSQL($key).'", "'.pSQL($val).'")
        ');
    }

    public function getQuotesCount()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT COUNT(`id_productquotation`) AS count
        FROM `'._DB_PREFIX_.'productquotation`');
        return $result['count'];
    }

    public static function getUserQuotes()
    {
        $this_obj = new Quote;
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT *
                FROM `'._DB_PREFIX_.'productquotation`');
        foreach ($sql as &$row) {
            $row['template_name'] = $this_obj->getTemplateName($row['template']);
            $row['client'] = $this_obj->getClientName($row['email']);
            $row['status_name'] = $this_obj->getStatusName($row['status']);
        }
        return $sql;
    }

    public static function getTemplateName()
    {
        $id_lang = (int)Context::getContext()->language->id;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `title`
        FROM `'._DB_PREFIX_.'productquotation_templates_lang`
        WHERE `id_lang` = '.(int)$id_lang);
        return $result['title'];
    }

    public static function getClientName($email)
    {
        $cust = new Customer;
        $customer_exist = $cust->customerExists($email);
        if ($customer_exist > 0) {
            $customer = $cust->getByEmail($email);
            return $customer->firstname.' '.$customer->lastname;
        } else {
            return 'Visitor';
        }
    }

    public static function getStatusName($status)
    {
        if ($status == 0) {
            return 'Pending';
        } elseif ($status == 1) {
            return 'In study';
        } elseif ($status == 2) {
            return 'Validated By Client';
        } elseif ($status == 3) {
            return 'Rejected';
        } elseif ($status == 4) {
            return 'Cancelled';
        } elseif ($status == 5) {
            return 'Ordered';
        } elseif ($status == 7) {
            return 'Approved';
        }
    }

    public function updateQuoteState($id, $value)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'productquotation`
            SET `status` = '.(int)$value.'
            WHERE `id_productquotation` = '.(int)$id;
        return Db::getInstance()->execute($sql);
    }

    public function dropQuotation($id)
    {
        return Db::getInstance()->execute('
        DELETE FROM '._DB_PREFIX_.'productquotation
        WHERE `id_productquotation` = '.(int)$id);
    }

    public static function getUserBaseQuotes($id)
    {
        $this_obj = new Quote;
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT *
        FROM `'._DB_PREFIX_.'productquotation` WHERE `id_customer` = '.(int)$id);
        foreach ($sql as &$row) {
            $row['status_name'] = $this_obj->getStatusName($row['status']);
            $row['product_count'] = $this_obj->getCount($row['id_quote']);
        }
        return $sql;
    }

    public function getQuotationData($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'quote_data`
        WHERE `id_quote` = '.(int)$id);
        foreach ($result as &$row) {
            $row['title'] = str_replace('_', ' ', $row['title']);
        }
        return $result;
    }
    
    public function getQuotationFormData($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'fmm_quote_userdata`
        WHERE `id_quote` = '.(int)$id);
        
        return $result;
    }

    public static function getQuotationDetailsByQuote($id)
    {
        $this_obj = new Quote;
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT *
                FROM `'._DB_PREFIX_.'productquotation`
                WHERE `id_quote` = '.(int)$id);
        foreach ($sql as &$row) {
            $row['template_name'] = $this_obj->getTemplateName($row['template']);
            $row['client'] = $this_obj->getClientName($row['email']);
            $row['status_name'] = $this_obj->getStatusName($row['status']);
        }
        return array_shift($sql);
    }

    public static function getQuotationDetailsStatic($id)
    {
        $this_obj = new Quote;
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT *
                FROM `'._DB_PREFIX_.'productquotation`
                WHERE `id_productquotation` = '.(int)$id);
        foreach ($sql as &$row) {
            $row['template_name'] = $this_obj->getTemplateName($row['template']);
            $row['client'] = $this_obj->getClientName($row['email']);
            $row['status_name'] = $this_obj->getStatusName($row['status']);
        }
        return array_shift($sql);
    }

    public static function getIdQuote($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `id_quote`
        FROM `'._DB_PREFIX_.'productquotation`
        WHERE `id_productquotation` = '.(int)$id);
        return $result['id_quote'];
    }

    public static function getKey($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `key`
        FROM `'._DB_PREFIX_.'productquotation`
        WHERE `id_productquotation` = '.(int)$id);
        return $result['key'];
    }

    public static function getQoutationIdByQuote($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `id_productquotation`
        FROM `'._DB_PREFIX_.'productquotation`
        WHERE `id_quote` = '.(int)$id);
        return $result['id_productquotation'];
    }

    public static function checkAuthorizeLevel($id_quotation, $key)
    {
        if (!$id_quotation || empty($key)) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT `id_quote`
        FROM `'._DB_PREFIX_.'productquotation`
        WHERE `id_productquotation` = '.(int)$id_quotation.'
        AND `key` = "'.pSQL($key).'"');
    }

    public static function countMessage($id_quotation, $author = 1)
    {
        if (!$id_quotation) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('COUNT(*)');
        $sql->from('quote_messages');
        $sql->where('`read` = 0');
        $sql->where('`id_quotation` = ' .(int) $id_quotation);
        $sql->where('`author` = ' .(int) $author);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getUserThreads($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT `message`, `author`, `date`
        FROM `'._DB_PREFIX_.'quote_messages`
        WHERE `id_quotation` = '.(int)$id);
        return $result;
    }

    public function saveMessage($id_quotation, $message)
    {
        return Db::getInstance()->execute('
        INSERT INTO '._DB_PREFIX_.'quote_messages (`id_quotation`, `message`, `author`)
        VALUES('.(int)$id_quotation.', "'.pSQL($message).'", 0)
        ');
    }

    public static function getMessageId($id_quotation)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `id_quote_messages`
        FROM `'._DB_PREFIX_.'quote_messages`
        WHERE `id_quotation` = '.(int)$id_quotation);
        return $result['id_quote_messages'];
    }

    public function saveCart($id_shop, $id_lang, $id_currency, $id_customer, $key)
    {
        $date = date('Y-m-d H:i:s');
        Db::getInstance()->execute('
        INSERT INTO '._DB_PREFIX_.'cart (
            `id_shop`,
            `id_lang`,
            `id_currency`,
            `id_customer`,
            `secure_key`,
            `date_add`,
            `date_upd`
        )
        VALUES('.(int)$id_shop.', '.(int)$id_lang.', '.
        (int)$id_currency.', '.(int)$id_customer.', "'.
        pSQL($key).'", "'.pSQL($date).'", "'.pSQL($date).'")
        ');
        $last_id = (int)Db::getInstance()->Insert_ID();
        return $last_id;
    }

    public function saveCartProducts($id_cart, $id_shop, $id_product, $combination, $qty)
    {
        return Db::getInstance()->execute('
        INSERT INTO '._DB_PREFIX_.
        'cart_product (`id_cart`, `id_product`, `id_shop`, `id_product_attribute`, `quantity`)
        VALUES('.(int)$id_cart.', '.(int)$id_product.', '.
        (int)$id_shop.', '.(int)$combination.', '.(int)$qty.')
        ');
    }

    public function updateQuotationState($id_quotation, $id_voucher, $id_cart)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'productquotation`
            SET `coupon_sent` = 1, `coupon_id` = '.(int)$id_voucher.', `id_cart` = '.(int)$id_cart.'
            WHERE `id_productquotation` = '.(int)$id_quotation;
        return Db::getInstance()->execute($sql);
    }

    public static function getVoucherDetails($id)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT p.`coupon_id`, c.`date_to`, c.`code`
        FROM `'._DB_PREFIX_.'productquotation` p
        LEFT JOIN `'._DB_PREFIX_.'cart_rule` c ON (p.`coupon_id` = c.`id_cart_rule`)
        WHERE p.`id_productquotation` = '.(int)$id);
    }
    
    public function updateQuoteProductQty($id, $qty)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'quotes_products`
            SET `qty` = '.(int)$qty.'
            WHERE `id_quotes_products` = '.(int)$id;
        return Db::getInstance()->execute($sql);
    }
    
    public function getCustomizationFields($id, $id_cart)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $result = Db::getInstance()->executeS('
        SELECT c.`id_customization`, cd.`index`, cd.`value`, cd.`type`
        FROM `'._DB_PREFIX_.'customization` c
        LEFT JOIN `'._DB_PREFIX_.'customized_data` cd ON (c.`id_customization` = cd.`id_customization`)
        WHERE c.`id_product` = '.(int)$id.'
        AND c.`id_cart` = '.(int)$id_cart.' ORDER BY cd.`index` ASC');
        foreach ($result as &$row) {
            $row['label'] = Customization::getLabel($row['index'], $id_lang);
        }
        return $result;
    }
    
    public function getProductAttributeImage($id_product, $id_product_attribute)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;

        $product = new Product($id_product);
        
        $link_rewrite = $this->checkLinkRewrite($product->link_rewrite);

        $link = new Link();
        if (!$id_product_attribute) {
            $id_image = Product::getCover($id_product);
            $image_link = $link->getImageLink($link_rewrite, $id_image['id_image'], 'home_default');
        } else {
            $image_data = Image::getBestImageAttribute($id_shop, $id_lang, $id_product, $id_product_attribute);
            $id_image = $image_data['id_image'];
            if (empty($id_image)) {
                $image_data = Image::getCover($id_product);
                $id_image = $image_data['id_image'];
            }
            if (_PS_VERSION_ > 1.6) {
                $image_link = $link->getImageLink($link_rewrite, $id_image, ImageType::getFormattedName('small'));
            } else {
                $image_link = $link->getImageLink($link_rewrite, $id_image, null);
            }
        }
        
        return Tools::getProtocol().$image_link;
    }
    
    private function checkLinkRewrite($link_rewrite)
    {
        $link_rewrite = $link_rewrite;

        if (is_array($link_rewrite)) {
            $filteredArray = array_filter($link_rewrite);
            $link_rewrite = current($filteredArray);
        }

        return $link_rewrite;
    }

    public static function getFormatedName($name)
    {
        $theme_name = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(array('_' . $theme_name, $theme_name . '_'), '', $name);

        //check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name)) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name . '_' . $theme_name)) {
            return $name_without_theme_name . '_' . $theme_name;
        } elseif (ImageType::getByNameNType($theme_name . '_' . $name_without_theme_name)) {
            return $theme_name . '_' . $name_without_theme_name;
        } else {
            return $name_without_theme_name . '_default';
        }
    }
}
