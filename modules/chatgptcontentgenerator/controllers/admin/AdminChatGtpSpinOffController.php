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
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptSpinoffConnections;

class AdminChatGtpSpinOffController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = GptSpinoffConnections::$definition['table'];
        $this->className = GptSpinoffConnections::class;
        $this->identifier = GptSpinoffConnections::$definition['primary'];
        $this->bootstrap = true;

        parent::__construct();

        $this->_select .= 'pl.`name` AS name, p.`active` AS status, p.reference AS reference,
		par.`name` AS parentname, sav.`quantity` AS `sav_quantity`,
		(
			CASE WHEN a.`stock` = ' . GptSpinoffConnections::SPINOFF_STOCK_COMMON . '
			THEN "' . $this->trans('Common', [], 'Modules.Chatgptcontentgenerator.Admin') . '"
			ELSE "' . $this->trans('Individual', [], 'Modules.Chatgptcontentgenerator.Admin') . '"
			END
            ) AS `stock_type` ';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` AS pl
		ON (a.`id_spinoff` = pl.`id_product` AND pl.`id_lang` = ' . $this->context->language->id . ')';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product` AS p
		ON (a.`id_spinoff` = p.`id_product`)';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` AS par
		ON (a.`id_product` = par.`id_product` AND par.`id_lang` = ' . $this->context->language->id . ')';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 ' .
            \StockAvailable::addSqlShopRestriction(null, $this->context->shop->id, 'sav') . ')';
        $this->_orderWay = 'DESC';

        $this->fields_list = [
            'id_spinoff' => [
                'title' => $this->trans('ID', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'filter_key' => 'pl!name',
                'width' => 'auto',
            ],
            'parentname' => [
                'title' => $this->trans('Parent product', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'filter_key' => 'par!name',
                'callback' => 'getParentLinkField',
            ],
            'reference' => [
                'title' => $this->trans('Reference', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'filter_key' => 'p!reference',
            ],
            'sav_quantity' => [
                'title' => $this->trans('Quantity', [], 'Admin.Global'),
                'align' => 'center',
                'filter_key' => 'sav!quantity',
            ],
            'stock_type' => [
                'title' => $this->trans('Stock type', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'select',
                'align' => 'center',
                'filter_key' => 'stock',
                'list' => [
                    GptSpinoffConnections::SPINOFF_STOCK_COMMON => $this->trans('Common', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    GptSpinoffConnections::SPINOFF_STOCK_INDIVIDUAL => $this->trans('Individual', [], 'Modules.Chatgptcontentgenerator.Admin'),
                ],
            ],
            'status' => [
                'title' => $this->trans('Active', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'bool',
                'align' => 'center',
                'filter_key' => 'p!active',
            ],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selection', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'confirm' => $this->trans(
                    'These spin-off products will be deleted for good. Please confirm.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
            ],
        ];

        $this->page_header_toolbar_title = $this->trans(
            'Spin-off Products',
            [],
            'Modules.Chatgptcontentgenerator.Admin'
        );

        $this->list_no_link = true;

        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/spinoffs.list.css', 'all');
    }

    public function initToolbar()
    {
        parent::initToolbar();

        unset($this->toolbar_btn['new']);
    }

    public function getParentLinkField($text, $data)
    {
        $parentLink = $this->context->link->getAdminLink(
            'AdminProducts',
            true,
            ['id_product' => $data['id_product']]
        );

        $this->context->smarty->assign([
            'parentProductName' => $text,
            'parentProductLink' => $parentLink,
        ]);

        return $this->context->smarty->fetch('module:chatgptcontentgenerator/views/templates/admin/spin_off/spinoff.list.parent.link.tpl');
    }

    public function renderForm()
    {
        if (!Tools::getValue('id')) {
            return;
        }

        $conection = new GptSpinoffConnections(Tools::getValue('id'));

        $productObj = new Product((int) $conection->id_spinoff);
        if ($productObj->id) {
            $productAdminLink = Context::getContext()->link->getAdminLink(
                'AdminProducts', true, ['id_product' => (int) $productObj->id]
            );

            Tools::redirectAdmin($productAdminLink);
        } else {
            $this->errors[] = $this->trans('Spin-off not exist.', [], 'Modules.Chatgptcontentgenerator.Admin');
        }
    }

    public function renderList()
    {
        $subscriptionExpiredAlertHtml = $this->module->getSubscriptionExpiredAlertHtml(null, true);

        if (!$subscriptionExpiredAlertHtml) {
            $shopInfo = $this->module->getShopInfo();

            if (!$shopInfo || empty($shopInfo['subscription'])) {
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
        }

        if ($subscriptionExpiredAlertHtml) {
            return $subscriptionExpiredAlertHtml;
        }

        return parent::renderList();
    }
}
