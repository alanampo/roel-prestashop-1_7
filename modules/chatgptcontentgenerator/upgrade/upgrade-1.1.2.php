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

function upgrade_module_1_1_2($module)
{
    $result = true;

    try {
        $module->registerHook('moduleRoutes');
        $module->registerHook('displayFooterProduct');
        $module->registerHook('actionFrontControllerSetMedia');
        $module->registerHook('actionProductDelete');
        $module->registerHook('actionObjectProductAddAfter');
        $module->registerHook('actionObjectProductUpdateAfter');
        $module->registerHook('actionUpdateQuantity');

        // admin listing
        $module->registerHook('actionCategoryGridDataModifier');
        $module->registerHook('actionAdminProductsListingFieldsModifier');
        $module->registerHook('actionAdminProductsListingResultsModifier');
        $module->registerHook('actionProductGridDefinitionModifier');
        $module->registerHook('actionProductGridQueryBuilderModifier');

        // admin product form
        $module->registerHook('actionAfterUpdateCombinationListFormHandler');

        $idParentCatalog = (int) Db::getInstance()->getValue(
            'SELECT id_tab FROM ' . _DB_PREFIX_ . 'tab WHERE class_name =\'AdminCatalog\''
        );

        $module->installTabs(
            [
                [
                    'visible' => false,
                    'class_name' => 'AdminChatGtpPostAjax',
                    'name' => 'ChatGPT Post Ajax',
                    'id_parent' => -1,
                    'icon' => null,
                ],
                [
                    'visible' => false,
                    'class_name' => 'AdminChatGtpFilesAjax',
                    'name' => 'ChatGPT Files Ajax',
                    'id_parent' => -1,
                    'icon' => null,
                ],
                [
                    'visible' => true,
                    'class_name' => 'AdminChatGtpContentBlog',
                    'name' => 'ChatGPT blog',
                    'id_parent' => 0,
                    'icon' => null,
                ],
                [
                    'visible' => true,
                    'class_name' => 'AdminChatGtpContentBlogPost',
                    'name' => 'Posts by ChatGPT',
                    'parent_class_name' => 'AdminChatGtpContentBlog',
                    'icon' => null,
                ],
                [
                    'visible' => true,
                    'class_name' => 'AdminChatGtpContentBlogSettings',
                    'name' => 'Blog settings',
                    'parent_class_name' => 'AdminChatGtpContentBlog',
                    'icon' => null,
                ],
                [
                    'visible' => false,
                    'class_name' => 'AdminChatGtpSpinOffAjax',
                    'name' => 'ChatGPT Spin Off',
                    'id_parent' => -1,
                    'icon' => null,
                ],
                [
                    'visible' => true,
                    'class_name' => 'AdminChatGtpSpinOff',
                    'name' => 'Spin-offs',
                    'id_parent' => $idParentCatalog,
                    'icon' => null,
                ],
            ]
        );

        $module->setConfigGlobal('BLOG_POSTS_PER_PAGE', 9);
        $module->setConfigGlobal('BLOG_MAIN_TITLE', 'Blog');
        $module->setConfigGlobal('BLOG_DISPLAY_DATE', true);
        $module->setConfigGlobal('BLOG_DISPLAY_FEATURED', true);
        $module->setConfigGlobal('BLOG_DISPLAY_RELATED', true);
        $module->setConfigGlobal('BLOG_DISPLAY_THUMBNAIL', true);
        $module->setConfigGlobal('BLOG_DISPLAY_DESCRIPTION', true);
        $module->setConfigGlobal('BLOG_DISPLAY_POST_ON_PRODUCT_PAGE', true);

        $module->setConfigGlobal('BLOG_THUMB_X', 600);
        $module->setConfigGlobal('BLOG_THUMB_Y', 300);

        $sqlInstaller = new \PrestaShop\Module\Chatgptcontentgenerator\Sql\Installer();
        $sqlInstaller->installPosts();
        $sqlInstaller->installSpinOff();
    } catch (Exception $e) {
        $result &= false;
    }

    return (bool) $result;
}
