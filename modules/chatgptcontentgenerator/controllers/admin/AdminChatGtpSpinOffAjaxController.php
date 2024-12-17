<?php
/**
 * 2007-2023 PrestaShop
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
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptSpinoffConnections;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminChatGtpSpinOffAjaxController extends AdminChatGtpContentAjaxController
{
    public function ajaxProcessProductSettingsSpinOffTabContent()
    {
        if (!Configuration::getGlobalValue('CHATGPTSPINOFF_MANAGE')) {
            $subscriptionExpiredAlertHtml = $this->module->getSubscriptionExpiredAlertHtml(null, true);

            if (!$subscriptionExpiredAlertHtml) {
                $subscriptionExpiredAlertHtml = $this->module->getSubscriptionExpiredAlertHtml(
                    null,
                    true,
                    $this->trans('<b>Attention!</b><br>
                        Please order subscription plan!',
                        [],
                        'Modules.Chatgptcontentgenerator.Admin'
                    )
                );
            }

            return $this->module->jsonResponse(
                [
                    'tab_content' => $subscriptionExpiredAlertHtml,
                ]
            );
        }

        $idProduct = Tools::getValue('idProduct');

        $spinoff = GptSpinoffConnections::getConectionsByProductId($idProduct);

        $products = [];

        if ($spinoff) {
            $products = self::getProducts(
                $this->context->language->id,
                array_column($spinoff, 'id_spinoff')
            );
        }

        foreach ($products as &$product) {
            $product['actions'] = $this->trans('Actions', [], 'Modules.Chatgptcontentgenerator.Admin');
        }

        $fields_list = [
            'id_product' => [
                'title' => $this->trans('ID', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'align' => 'center',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'width' => 'auto',
            ],
            'sav_quantity' => [
                'title' => $this->trans('Quantity', [], 'Admin.Global'),
                'width' => 'auto',
            ],
            'stock_type' => [
                'title' => $this->trans('Stock type', [], 'Admin.Chatgptcontentgenerator.Global'),
                'width' => 'auto',
            ],
            'price' => [
                'title' => $this->trans('Price', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'currency' => true,
                'type' => 'price',
                'align' => 'center',
            ],
            'active' => [
                'title' => $this->trans('Active', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'bool',
                'align' => 'center',
            ],
            'actions' => [
                'title' => $this->trans('Actions', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'callback' => 'getSpinOffListActions',
                'align' => 'center',
            ],
        ];

        $helper_list = new HelperList();

        $helper_list->shopLinkType = '';
        $helper_list->identifier = 'id_product';
        $helper_list->show_toolbar = true;
        $helper_list->title = 'Spin-offs list';
        $helper_list->currentIndex = '#';
        $helper_list->simple_header = true;

        $spinoffTable = $helper_list->generateList($products, $fields_list);

        $this->context->smarty->assign([
            'spinoffTable' => $spinoffTable,
        ]);

        return $this->module->jsonResponse(
            [
                'tab_content' => $this->context->smarty->
                    fetch('module:chatgptcontentgenerator/views/templates/admin/spin_off/productsettings.spinofftab.tpl'),
            ]
        );
    }

    public function getSpinOffListActions($fieldValue, $product)
    {
        $productObj = new Product((int) $product['id_product']);
        $productCategories = Product::getProductCategoriesFull((int) $product['id_product']);
        $productLink = $this->context->link->getProductLink(
            $productObj, null, end($productCategories)['link_rewrite']
        );

        $productAdminLink = Context::getContext()->link->getAdminLink(
            'AdminProducts', true, ['id_product' => (int) $product['id_product']]
        );

        $this->context->smarty->assign([
            'productLink' => $productLink,
            'productAdminLink' => $productAdminLink,
            'productId' => (int) $product['id_product'],
        ]);

        return $this->context->smarty->
            fetch('module:chatgptcontentgenerator/views/templates/admin/spin_off/spinoff.list.actions.tpl');
    }

    public function ajaxProcessProductSettingsSpinOffCreate()
    {
        $idProduct = Tools::getValue('idProduct');
        $numberOfSpinOff = (int) Tools::getValue('numberOfSpinOff');
        $spinOffStock = (int) Tools::getValue('spinOffStock');

        if (!$numberOfSpinOff || $numberOfSpinOff < 1) {
            return $this->module->errorResponse($code = 500, $message = 'Error: number of spin off must be > 0');
        }

        if (!$idProduct) {
            return $this->module->errorResponse($code = 500, $message = 'Error: product id is empty.');
        }

        if (GptSpinoffConnections::SPINOFF_STOCK_COMMON !== $spinOffStock) {
            $spinOffStock = GptSpinoffConnections::SPINOFF_STOCK_INDIVIDUAL;
        }

        $productUpdater = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()
            ->get('prestashop.core.admin.data_updater.product_interface');

        $spinoff_ids = [];

        for ($i = 1; $i <= $numberOfSpinOff; ++$i) {
            $spinoff_ids[] = $id_spinoff = $productUpdater->duplicateProduct($idProduct, '%s');

            $gptConnection = new GptSpinoffConnections();

            $gptConnection->id_spinoff = $id_spinoff;
            $gptConnection->id_product = $idProduct;
            $gptConnection->stock = $spinOffStock;

            $gptConnection->save();
        }

        $product_original = new Product($idProduct);
        $combinations_original = $product_original->getAttributeCombinations();
        $default_combination_original = $product_original->getDefaultIdProductAttribute();

        foreach ($spinoff_ids as $spinoff_id) {
            $spinoff = new Product($spinoff_id);
            $combinations_spinoff = $spinoff->getAttributeCombinations();

            if ($combinations_original) {
                foreach ($combinations_original as $key => $val) {
                    StockAvailable::setQuantity(
                        $spinoff_id,
                        $combinations_spinoff[$key]['id_product_attribute'],
                        $combinations_original[$key]['quantity']
                    );

                    if ($default_combination_original == $val['id_product_attribute']) {
                        $spinoff->deleteDefaultAttributes();
                        $spinoff->setDefaultAttribute($combinations_spinoff[$key]['id_product_attribute']);
                    }
                }
            } else {
                StockAvailable::setQuantity(
                    $spinoff_id,
                    0,
                    StockAvailable::getQuantityAvailableByProduct($idProduct)
                );
            }

            $spinoff->active = true;
            $spinoff->visibility = Configuration::get('CHATGPTSPINOFF_VISIBILITY');

            $spinoff->save();
        }

        return $this->module->jsonResponse(
            ['spinoff_ids' => $spinoff_ids]
        );
    }

    public function ajaxProcessDeleteSpinOff()
    {
        $id_product = Tools::getValue('id_product');

        $product = new Product($id_product);

        try {
            if ($product->delete()) {
                return $this->module->jsonResponse(
                    ['delete_spinoff' => 'Spin-off delete successful.']
                );
            } else {
                return $this->module->errorResponse();
            }
        } catch (Exception $e) {
            print_r($this->module->jsonExeptionResponse($e));
        }
    }

    public static function getProducts(
        $id_lang,
        array $ids_product,
        $start = 0,
        $limit = 0,
        $order_by = 'id_product',
        $order_way = 'desc',
        $id_category = false,
        $only_active = false,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            exit(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name,
                    sav.`quantity` AS `sav_quantity`,
                    (
                        CASE WHEN sc.`stock` = ' . GptSpinoffConnections::SPINOFF_STOCK_COMMON . '
                            THEN "' . $context->getTranslator()->trans('Common', [], 'Modules.Chatgptspinoff.Admin') . '"
                            ELSE "' . $context->getTranslator()->trans('Individual', [], 'Modules.Chatgptspinoff.Admin') . '"
                        END
                    ) AS `stock_type`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 ' .
                    \StockAvailable::addSqlShopRestriction(null, $context->shop->id, 'sav') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `' . _DB_PREFIX_ . 'spinoff_connections` sc ON (p.`id_product` = sc.`id_spinoff`)' .
            ($id_category ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` c ON (c.`id_product` = p.`id_product`)' : '') . '
                WHERE pl.`id_lang` = ' . (int) $id_lang . '
                 AND p.`id_product` IN (' . implode(', ', $ids_product) . ') ' .
            ($id_category ? ' AND c.`id_category` = ' . (int) $id_category : '') .
            ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
            ($only_active ? ' AND product_shop.`active` = 1' : '') . '
                ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) .
            ($limit > 0 ? ' LIMIT ' . (int) $start . ',' . (int) $limit : '');

        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }

        foreach ($rq as &$row) {
            $row = Product::getTaxesInformations($row);
        }

        return $rq;
    }
}
