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

function upgrade_module_1_0_11($module)
{
    $result = true;

    try {
        $module->installTabs(
            [
                [
                    'visible' => false,
                    'class_name' => 'AdminChatGtpTemplate',
                    'name' => 'ChatGPT Templates',
                    'id_parent' => (int) Db::getInstance()->getValue(
                        'SELECT id_tab FROM ' . _DB_PREFIX_ . 'tab WHERE class_name =\'AdminCatalog\''
                    ),
                    'icon' => null,
                ],
            ]
        );

        Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'content_template` (
                `id_content_template` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(128) NOT NULL,
                `type` enum("product","category","cms") NOT NULL DEFAULT "product",
                `active` tinyint(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (`id_content_template`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );

        Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'content_template_lang` (
                `id_content_template` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `short_code` text NULL,
                PRIMARY KEY (`id_content_template`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );
    } catch (Exception $e) {
        $result &= false;
    }

    return (bool) $result;
}
