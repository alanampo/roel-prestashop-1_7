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

namespace PrestaShop\Module\Chatgptcontentgenerator\Hooks;

use PrestaShop\Module\Chatgptcontentgenerator\Api\Client as ApiClient;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentGenerator as ContentGeneratorEntity;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentTemplate;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptSpinoffConnections;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HooksPS17 extends AbstractHooks
{
    protected $isNewVersion = false;

    public function getRegisterHooks(): array
    {
        return [
            'actionAdminControllerSetMedia',
            'actionAdminProductsListingFieldsModifier',
            'actionAdminProductsListingResultsModifier',
            'actionCategoryGridQueryBuilderModifier',
            'actionCategoryGridDataModifier',
            'actionCategoryGridDefinitionModifier',
            'actionAdminCategoriesListingFieldsModifier',
            'moduleRoutes',
            'actionProductDelete',
            'actionObjectProductAddAfter',
            'actionObjectProductUpdateAfter',
            'displayFooterProduct',
            'actionFrontControllerSetMedia',
            'actionUpdateQuantity',
        ];
    }

    protected function getGptVersionSelectors()
    {
        return array_merge(
            parent::getGptVersionSelectors(),
            [
                'pfLiTabs6Id' => 'tab_step6',
                'pfContent6Id' => 'step6',
                'pfTabsContentId' => 'form-loading',
                'pfIsoCodeId' => 'form_switch_language',
                'pfName' => '#form_step1_name_',
                'pfDescription' => '#description',
                'pfDescriptionShort' => '#description_short',
                'pfManufacturerId' => '#form_step1_id_manufacturer',

                'plProductFormId' => 'product_catalog_list',
                'plBulkMenu' => '.bulk-catalog .dropdown-menu',
                'plBulkSelectedName' => 'bulk_action_selected_products[]',
            ]
        );
    }

    protected function getContentEditorPreffix()
    {
        return array_merge(
            parent::getContentEditorPreffix(),
            [
                'description' => 'form_step1_description_',
                'descriptionShort' => 'form_step1_description_short_',
                'name' => 'form_step1_name_',
            ]
        );
    }

    public function _hookActionAdminControllerSetMedia()
    {
        $shopInfo = $this->module->getShopInfo();

        $this->module->checkSubscriptionDeadline($shopInfo);

        \Media::addJsDef([
            'gptApiHost' => ApiClient::getApiHostUrl(),
            'gptModuleVersion' => $this->module->version,
            'gptSiteVersion' => _PS_VERSION_,
            'gptServerIp' => ApiClient::getServerIp(),
            'gptPatchVersion' => $this->getMediaPatchForVersion(),
        ]);

        if ($this->context->controller && $this->context->controller instanceof \AdminModulesController) {
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.content.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.modal.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.forms.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.module.js');
            $this->context->controller->addCss($this->module->getPathUri() . 'views/css/back.css');
            return;
        }

        $request = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()
            ->get('request_stack')->getMasterRequest();

        $adminPageName = '';
        $isLegacyController = false;

        if ($request) {
            if ($request->attributes->get('_route') == 'admin_product_catalog') {
                $adminPageName = 'productsList';
            } elseif ($request->attributes->get('_route') == 'admin_categories_index') {
                $adminPageName = 'categoriesList';
            } elseif ($request->attributes->get('_route') == 'admin_product_form') {
                $adminPageName = 'productForm';
            } elseif ($request->attributes->get('_route') == 'admin_categories_edit') {
                $adminPageName = 'categoryForm';
                $categoryId = (int) $request->attributes->get('categoryId');
            } elseif ($request->attributes->get('_route') == 'admin_cms_pages_edit'
                || $request->attributes->get('_route') == 'admin_cms_pages_create') {
                $adminPageName = 'cmsForm';
                $cmsId = (int) $request->attributes->get('cmsPageId');
            }
        } else {
            $controller = (isset($this->context->controller) ? $this->context->controller : false);

            if ($controller && $controller instanceof \AdminCategoriesController) {
                $isLegacyController = true;
                $adminPageName = 'categoriesList';
                if (\Tools::isSubmit('updatecategory') && \Tools::getValue('id_category')) {
                    $adminPageName = 'categoryForm';
                    $categoryId = (int) \Tools::getValue('id_category');
                }
            } elseif ($controller && $controller instanceof \AdminCmsContentController) {
                if (\Tools::isSubmit('updatecms') && \Tools::getValue('id_cms')) {
                    $adminPageName = 'cmsForm';
                    $cmsId = (int) \Tools::getValue('id_cms');
                    $isLegacyController = true;
                }
            } elseif ($controller && $controller instanceof \AdminChatGtpContentBlogPostController) {
                $isLegacyController = true;
                $adminPageName = 'postForm';
            }
        }

        $buttonName = '';

        if ($adminPageName == 'productsList') {
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.bulkactions.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.entities.list.js');
        } elseif ($adminPageName == 'categoriesList') {
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.bulkactions.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.entities.list.js');
        } elseif ($adminPageName == 'productForm') {
            $productId = (int) $request->attributes->get('id');

            if ($productId) {
                $buttonName = $this->translator->trans(
                    'Generate description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                );

                \Media::addJsDef([
                    'idProduct' => (int) $productId,
                ]);
            }
        } elseif ($adminPageName == 'categoryForm') {
            if ($categoryId) {
                $buttonName = $this->translator->trans(
                    'Generate description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                );

                \Media::addJsDef([
                    'idCategory' => (int) $categoryId,
                ]);
            }
        } elseif ($adminPageName == 'cmsForm') {
            $buttonName = $this->translator->trans(
                'Generate content',
                [],
                'Modules.Chatgptcontentgenerator.Admin'
            );

            \Media::addJsDef([
                'idCms' => (int) $cmsId,
            ]);
        }

        if ($adminPageName == '') {
            return;
        }

        if (isset($shopInfo['subscription']['plan']) || \Configuration::get('CHATGPTSPINOFF_SHOW_SPINOFF_PRODUCTS')) {
            \Configuration::updateGlobalValue('CHATGPTSPINOFF_MANAGE', true);
        } else {
            \Configuration::updateGlobalValue('CHATGPTSPINOFF_MANAGE', false);
        }

        \Media::addJsDef([
            'gptLanguages' => \Language::getLanguages(),
            'gptLanguagesIds' => \Language::getLanguages(false, false, true),
            'gptLanguageId' => (int) $this->context->language->id,
            'gptAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpContentAjax'),
            'gptPostAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpPostAjax'),
            'gptFilesAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpFilesAjax'),
            'gptPostEditUrl' => $this->context->link->getAdminLink(
                'AdminChatGtpContentBlogPost',
                true,
                [],
                ['update' . \PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentPost::$definition['table'] => 1]
            ),
            'adminPageName' => $adminPageName,
            'gptShopInfo' => $shopInfo,
            'gptShopUrl' => $this->context->shop->getBaseURL(true),
            'gptShopEmail' => $this->context->employee->email,
            'gptFullName' => trim($this->context->employee->firstname . ' ' . $this->context->employee->lastname),
            'gptServerParams' => $this->getServerParams(),
            'isLegacyController' => $isLegacyController,

            'gptUseProductCategory' => (int) $this->module->getConfigGlobal('USE_PRODUCT_CATEGORY', null, 1),
            'gptUseProductBrand' => (int) $this->module->getConfigGlobal('USE_PRODUCT_BRAND', null, 1),

            'gptContentTemplates' => GptContentTemplate::getContentTemplatesByPage($adminPageName, true),

            'cookieQuotaLimit' => (int) $this->context->cookie->gptc_quota_limit,

            'spinOffStockCommon' => GptSpinoffConnections::SPINOFF_STOCK_COMMON,
            'spinOffStockIndividual' => GptSpinoffConnections::SPINOFF_STOCK_INDIVIDUAL,
            'spinOffStock' => (int) \Configuration::get('CHATGPTSPINOFF_STOCK'),
            'subscriptionExpiredAlertHtml' => $this->module->getSubscriptionExpiredAlertHtml($shopInfo),
        ]);

        $this->setGptJsVariables($buttonName);

        $this->context->controller->addCss($this->module->getPathUri() . 'views/css/admin.css');
        if ($isLegacyController) {
            $this->context->controller->addCss($this->module->getPathUri() . 'views/css/admin.legacy.css');
        }
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.forms.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.content.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.modal.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.module.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.actions.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.custom.request.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.translate.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.content.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.js');
        $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.files.uploader.js');

        if (\Tools::getValue('controller') == 'AdminProducts' && $productId) {
            $spinOffConection = GptSpinoffConnections::getConectionsBySpinOffId($productId);

            if (!$spinOffConection) {
                $this->context->controller->addJS($this->module->getPathUri() . '/views/js/admin.spinoff.product.tab.js');

                \Media::addJsDef([
                    'gptSpinOffAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpSpinOffAjax'),
                ]);
            } else {
                $parentProduct = new \Product($spinOffConection['id_product'], false, $this->context->language->id);

                \Media::addJsDef([
                    'parentProductLink' => $this->context->link->getAdminLink(
                        'AdminProducts',
                        true,
                        ['id_product' => $spinOffConection['id_product']]
                    ),
                    'spinOffProductStock' => $spinOffConection['stock'],
                    'editSpinOffProduct' => $this->translator->trans(
                        'Edit spin-off product',
                        [],
                        'Modules.Chatgptcontentgenerator.Admin'
                    ),
                    'parentProductBlockTitle' => $this->translator->trans(
                        'Parent product',
                        [],
                        'Modules.Chatgptcontentgenerator.Admin'
                    ),
                    'parentProductName' => $parentProduct->name,
                ]);
                $this->context->controller->addJS($this->module->getPathUri() . '/views/js/admin.spinoff.product.js');
            }
        } elseif ($adminPageName == 'productsList') {
            $this->context->controller->addJS($this->module->getPathUri() . '/views/js/admin.spinoff.productslist.js');
        }
    }

    public function _hookActionAdminProductsListingResultsModifier($params)
    {
        $products = (is_array($params['products']) ? $params['products'] : []);
        $products = array_map(
            function ($product) {
                return [
                    'id_product' => (int) $product['id_product'],
                    'generated_langs' => $product['generated_langs'],
                    'translated_langs' => $product['translated_langs'],
                ];
            },
            $products
        );

        $spinOffs = GptSpinoffConnections::getConectionsBySpinOffIdList(array_column($products, 'id_product'));
        $spinOffs = array_column($spinOffs, 'id_spinoff');

        foreach ($products as &$product) {
            if (in_array($product['id_product'], $spinOffs)) {
                $product['isSpinoff'] = true;
                $product['spinoffsCount'] = null;
            } else {
                $product['isSpinoff'] = false;
                $product['spinoffsCount'] = GptSpinoffConnections::countSpinOffsByProductId($product['id_product'])[0]['spinoffs_count'];
            }
        }

        $sortIsSpinoff = $this->context->cookie->sort_is_spinoff ?: '';
        $sortSpinoffsCount = $this->context->cookie->sort_spinoffs_count ?: '';

        $isSpinofSelectValue = $this->context->cookie->filter_column_isspinoff ?: '';

        $filter_column_spinofs_count_min = $this->context->cookie->filter_column_spinofs_count_min ?: null;
        $filter_column_spinofs_count_max = $this->context->cookie->filter_column_spinofs_count_max ?: null;

        \Media::addJsDef([
            'catalogProductsList' => $products,
            'gptHomeCategory' => (int) \Configuration::get('PS_HOME_CATEGORY'),
            'columnGeneratedLangs' => isset($this->context->cookie->filter_column_product_generated_langs)
                ? explode(',', (string) $this->context->cookie->filter_column_product_generated_langs)
                : false,
            'columnTranslatedLangs' => isset($this->context->cookie->filter_column_product_translated_langs)
                ? explode(',', (string) $this->context->cookie->filter_column_product_translated_langs)
                : false,
            'isSpinofSelectValue' => $isSpinofSelectValue,
            'filter_column_spinofs_count_min' => $filter_column_spinofs_count_min,
            'filter_column_spinofs_count_max' => $filter_column_spinofs_count_max,
            'sortIsSpinoff' => $sortIsSpinoff,
            'sortSpinoffsCount' => $sortSpinoffsCount,
        ]);
    }

    public function _hookActionAdminProductsListingFieldsModifier($params)
    {
        if (\Tools::isSubmit('filter_column_generated_langs')) {
            $selectedLangs = \Tools::getValue('filter_column_generated_langs', []);
            if (is_array($selectedLangs) && !empty($selectedLangs)) {
                $this->context->cookie->filter_column_product_generated_langs = implode(',', $selectedLangs);
            } else {
                unset($this->context->cookie->filter_column_product_generated_langs);
            }
        } elseif (\Tools::isSubmit('filter_column_id_product')) {
            unset($this->context->cookie->filter_column_product_generated_langs);
        }

        if (\Tools::isSubmit('filter_column_translated_langs')) {
            $selectedLangs = \Tools::getValue('filter_column_translated_langs', []);
            if (is_array($selectedLangs) && !empty($selectedLangs)) {
                $this->context->cookie->filter_column_product_translated_langs = implode(',', $selectedLangs);
            } else {
                unset($this->context->cookie->filter_column_product_translated_langs);
            }
        } elseif (\Tools::isSubmit('filter_column_id_product')) {
            unset($this->context->cookie->filter_column_product_translated_langs);
        }

        if (!isset($params['sql_where'])) {
            return;
        }

        if (!$params['sql_where'] || count($params['sql_where']) >= 3) {
            foreach ($params['sql_where'] as &$condition) {
                if (is_string($condition) && trim($condition) == 'state = 1') {
                    $condition = 'p.' . $condition;
                    break;
                }
            }
            unset($condition);

            $subSelect = '';
            if (isset($this->context->cookie->filter_column_product_generated_langs)
                && $this->context->cookie->filter_column_product_generated_langs !== '') {
                $subSelect .= ', SUM(IF(gptgc.id_lang IN (' .
                    pSql($this->context->cookie->filter_column_product_generated_langs) . ') ' .
                    'AND IFNULL(gptgc.is_generated, 0)=1, 1, 0)) AS `gcolumn`';

                $langs = explode(',', $this->context->cookie->filter_column_product_generated_langs);
                $params['sql_where'][] = 'IFNULL(content_gen.gcolumn, 0) = ' . count($langs);
            }

            if (isset($this->context->cookie->filter_column_product_translated_langs)
                && $this->context->cookie->filter_column_product_translated_langs !== '') {
                $subSelect .= ', SUM(IF(gptgc.id_lang IN (' .
                    pSql($this->context->cookie->filter_column_product_translated_langs) . ') ' .
                    'AND IFNULL(gptgc.is_translated, 0)=1, 1, 0)) AS `tcolumn`';

                $langs = explode(',', $this->context->cookie->filter_column_product_translated_langs);
                $params['sql_where'][] = 'IFNULL(content_gen.tcolumn, 0) = ' . count($langs);
            }

            $subTable = '(
                    SELECT
                        gptgc.id_object,
                        GROUP_CONCAT(IF(IFNULL(gptgc.is_generated, 0)=1, gptgc.id_lang, NULL) SEPARATOR \',\') AS `generated_langs`,
                        GROUP_CONCAT(IF(IFNULL(gptgc.is_translated, 0)=1, gptgc.id_lang, NULL) SEPARATOR \',\') AS `translated_langs`' . $subSelect . '
                    FROM `' . _DB_PREFIX_ . 'content_generator` AS gptgc
                    WHERE gptgc.object_type = ' . ContentGeneratorEntity::TYPE_PRODUCT .
                    ' GROUP BY gptgc.id_object
                )';
            $params['sql_table']['ON content_gen.`id_object` = p.`id_product`'] = [
                'table' => 'product` AS ppd2 ON (ppd2.id_product = p.id_product) ' .
                    'LEFT JOIN ' . $subTable . ' AS `content_gen',
                'join' => 'LEFT JOIN',
            ];

            $params['sql_select']['generated_langs'] = [
                'table' => 'content_gen',
                'field' => 'generated_langs',
            ];
            $params['sql_select']['translated_langs'] = [
                'table' => 'content_gen',
                'field' => 'translated_langs',
            ];
        }

        $resetPagination = false;

        if (\Tools::isSubmit('orderBy') && \Tools::getValue('orderBy') != '') {
            unset($this->context->cookie->sort_is_spinoff);
            unset($this->context->cookie->sort_spinoffs_count);
            $resetPagination = true;
        }

        if (\Tools::isSubmit('filter_column_isspinoff')) {
            $this->context->cookie->filter_column_isspinoff = \Tools::getValue('filter_column_isspinoff');
            $resetPagination = true;
        } elseif (!isset($this->context->cookie->filter_column_isspinoff)) {
            $this->context->cookie->filter_column_isspinoff = 'no';
        }

        if (\Tools::isSubmit('sort_is_spinoff') && \Tools::getValue('sort_is_spinoff') != '') {
            $this->context->cookie->sort_is_spinoff = \Tools::getValue('sort_is_spinoff');
            unset($this->context->cookie->sort_spinoffs_count);
            $resetPagination = true;
        }

        if (($this->context->cookie->filter_column_isspinoff && $this->context->cookie->filter_column_isspinoff != '')
            || $this->context->cookie->sort_is_spinoff
        ) {
            $params['sql_table']['con'] = [
                'table' => 'spinoff_connections',
                'join' => 'LEFT JOIN',
                'on' => 'p.`id_product` = con.`id_spinoff`',
            ];

            if (in_array($this->context->cookie->sort_is_spinoff, ['asc', 'desc'])) {
                $params['sql_select']['is_spinoff'] = [
                    'select' => 'CASE WHEN con.`id_spinoff` IS NULL THEN 0 ELSE 1 END',
                ];

                array_unshift(
                    $params['sql_order'],
                    'is_spinoff ' . $this->context->cookie->sort_is_spinoff
                );
            }

            if ($this->context->cookie->filter_column_isspinoff === 'no') {
                $params['sql_where'][] = 'con.id_spinoff IS NULL';
            } elseif ($this->context->cookie->filter_column_isspinoff === 'yes') {
                $params['sql_where'][] = 'p.`id_product` IN (SELECT `id_spinoff` FROM ' . _DB_PREFIX_ . 'spinoff_connections)';
            }
        }

        if (\Tools::isSubmit('filter_column_spinofs_count_min') && \Tools::isSubmit('filter_column_spinofs_count_max')) {
            $this->context->cookie->filter_column_spinofs_count_min = \Tools::getValue('filter_column_spinofs_count_min');
            $this->context->cookie->filter_column_spinofs_count_max = \Tools::getValue('filter_column_spinofs_count_max');
            $resetPagination = true;
        }

        if (\Tools::isSubmit('sort_spinoffs_count') && \Tools::getValue('sort_spinoffs_count') != '') {
            $this->context->cookie->sort_spinoffs_count = \Tools::getValue('sort_spinoffs_count');
            unset($this->context->cookie->sort_is_spinoff);
            $resetPagination = true;
        }

        if ((isset($this->context->cookie->filter_column_spinofs_count_min)
            && isset($this->context->cookie->filter_column_spinofs_count_max))
            || $this->context->cookie->sort_spinoffs_count
        ) {
            $params['sql_table']['ON (p.id_product = soc.id_product)'] = [
                'table' => 'spinoff_connections` AS ppd3 ON (ppd3.id_spinoff = p.id_product) ' .
                    'LEFT JOIN (
                            SELECT id_product, COUNT(*) AS count_spinoffs
                            FROM ' . _DB_PREFIX_ . 'spinoff_connections
                            GROUP BY id_product
                        ) AS `soc',
                'join' => 'LEFT JOIN',
            ];

            $params['sql_select']['count_spinoffs'] = [
                'select' => 'IFNULL(soc.count_spinoffs, 0)',
            ];

            if (in_array($this->context->cookie->sort_spinoffs_count, ['asc', 'desc'])) {
                array_unshift(
                    $params['sql_order'],
                    'count_spinoffs ' . $this->context->cookie->sort_spinoffs_count
                );
            }

            $conditionMin = $this->context->cookie->filter_column_spinofs_count_min;
            $conditionMax = $this->context->cookie->filter_column_spinofs_count_max;

            $countSpinofsCondition = '';

            if ($conditionMin and $conditionMax) {
                $countSpinofsCondition = 'BETWEEN ' . $conditionMin . ' AND ' . $conditionMax;
            } elseif ($conditionMin) {
                $countSpinofsCondition = '>=' . $conditionMin;
            } elseif ($conditionMax) {
                $countSpinofsCondition = '<=' . $conditionMax;
            }

            if ($countSpinofsCondition) {
                $sql = 'IFNULL(count_spinoffs, 0) ' . $countSpinofsCondition;

                $check = true;
                foreach ($params['sql_where'] as $val) {
                    if ($val == $sql) {
                        $check = false;
                    }
                }

                if ($check) {
                    $params['sql_where'][] = $sql;
                }
            }
        }

        if (\Tools::isSubmit('paginator_jump_page') && \Tools::getValue('paginator_jump_page')
            && \Tools::isSubmit('paginator_select_page_limit') && \Tools::getValue('paginator_select_page_limit')
        ) {
            $params['sql_limit'] =
                ((int) \Tools::getValue('paginator_jump_page') - 1) * (int) \Tools::getValue('paginator_select_page_limit') .
                ', ' . \Tools::getValue('paginator_select_page_limit');

            if ($resetPagination) {
                $params['sql_limit'] = '0, ' . \Tools::getValue('paginator_select_page_limit');
            }
        }
    }

    public function _hookActionAdminCategoriesListingFieldsModifier($params)
    {
        $this->handleCategoriesFilter();

        $languages = \Language::getLanguages();
        $params['fields']['generated_langs'] = [
            'title' => $this->translator->trans('Content ChatGPT', [], 'Modules.Chatgptcontentgenerator.Admin'),
            'type' => 'select',
            'list' => array_combine(array_column($languages, 'id_lang'), array_column($languages, 'iso_code')),
            'filter_key' => 'content_gen!content_generated',
            'filter_type' => 'int',
            'orderby' => false,
            'callback_object' => $this,
            'callback' => 'printGeneratedLangs',
        ];
        if (count($languages)) {
            $params['fields']['translated_langs'] = [
                'title' => $this->translator->trans('Tranlsate ChatGPT', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'select',
                'list' => array_combine(array_column($languages, 'id_lang'), array_column($languages, 'iso_code')),
                'filter_key' => 'content_gen!content_translated',
                'filter_type' => 'int',
                'orderby' => false,
                'callback_object' => $this,
                'callback' => 'printTranslatedLangs',
            ];
        }

        if (!isset($params['join'])) {
            $params['join'] = '';
        }
        $params['join'] .= ' LEFT JOIN ' . $this->prepareCategoryContentGeneratorSql($params) . ' AS content_gen ' .
            ' ON (content_gen.`id_object` = a.`id_category`)';

        if (array_key_exists('select', $params)) {
            $params['select'] .= ', content_gen.generated_langs AS `gl2`, content_gen.translated_langs AS `tl2`';
        }

        \Media::addJsDef([
            'columnGeneratedLangs' => isset($this->context->cookie->filter_column_category_generated_description)
                ? explode(',', (string) $this->context->cookie->filter_column_category_generated_description)
                : false,
            'columnTranslatedLangs' => isset($this->context->cookie->filter_column_category_translated_description)
                ? explode(',', (string) $this->context->cookie->filter_column_category_translated_description)
                : false,
        ]);
    }

    public function _hookActionCategoryGridDefinitionModifier($params)
    {
        $request = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()
            ->get('request_stack')->getMasterRequest();
        if (!$request || $request->attributes->get('_route') != 'admin_categories_search') {
            return;
        }

        $this->handleCategoriesFilter();
    }

    public function _hookActionCategoryGridDataModifier($params)
    {
        $records = $params['data']->getRecords()->all();
        $records = (is_array($records) ? $records : []);
        $categories = array_map(
            function ($category) {
                return [
                    'id_category' => (int) $category['id_category'],
                    'generated_langs' => $category['generated_langs'],
                    'translated_langs' => $category['translated_langs'],
                ];
            },
            $records
        );

        \Media::addJsDef([
            'catalogCategoriesList' => $categories,
            'columnGeneratedLangs' => isset($this->context->cookie->filter_column_category_generated_description)
                ? explode(',', (string) $this->context->cookie->filter_column_category_generated_description)
                : false,
            'columnTranslatedLangs' => isset($this->context->cookie->filter_column_category_translated_description)
                ? explode(',', (string) $this->context->cookie->filter_column_category_translated_description)
                : false,
        ]);
    }

    public function _hookActionCategoryGridQueryBuilderModifier($params)
    {
        $subTable = $this->prepareCategoryContentGeneratorSql($params);

        $params['search_query_builder']->leftJoin(
            'c',
            $subTable,
            'content_gen',
            'content_gen.`id_object` = c.`id_category`'
        );
        $params['count_query_builder']->leftJoin(
            'c',
            $subTable,
            'content_gen',
            'content_gen.`id_object` = c.`id_category`'
        );
        $params['search_query_builder']
            ->addSelect('content_gen.generated_langs AS `generated_langs`')
            ->addSelect('content_gen.translated_langs AS `translated_langs`')
        ;
    }

    private function prepareCategoryContentGeneratorSql(&$params)
    {
        $subSelect = '';
        if (isset($this->context->cookie->filter_column_category_generated_description)
            && $this->context->cookie->filter_column_category_generated_description !== '') {
            $subSelect .= ', SUM(IF(gptgc.id_lang IN (' .
                pSql($this->context->cookie->filter_column_category_generated_description) . ') ' .
                'AND IFNULL(gptgc.is_generated, 0)=1, 1, 0)) AS `gcolumn`';

            $langs = explode(',', $this->context->cookie->filter_column_category_generated_description);

            if (isset($params['search_query_builder'])) {
                $params['search_query_builder']->andWhere('IFNULL(content_gen.gcolumn, 0) = ' . count($langs));
            } elseif (array_key_exists('where', $params)) {
                $params['where'] .= ' AND IFNULL(content_gen.gcolumn, 0) = ' . count($langs);
            }
        }

        if (isset($this->context->cookie->filter_column_category_translated_description)
            && $this->context->cookie->filter_column_category_translated_description !== '') {
            $subSelect .= ', SUM(IF(gptgc.id_lang IN (' .
                pSql($this->context->cookie->filter_column_category_translated_description) . ') ' .
                'AND IFNULL(gptgc.is_translated, 0)=1, 1, 0)) AS `tcolumn`';

            $langs = explode(',', $this->context->cookie->filter_column_category_translated_description);
            if (isset($params['search_query_builder'])) {
                $params['search_query_builder']->andWhere('IFNULL(content_gen.tcolumn, 0) = ' . count($langs));
            } elseif (array_key_exists('where', $params)) {
                $params['where'] .= ' AND IFNULL(content_gen.tcolumn, 0) = ' . count($langs);
            }
        }

        return '(
            SELECT
                gptgc.id_object,
                1 AS `content_generated`,
                1 AS `content_translated`,
                GROUP_CONCAT(IF(IFNULL(gptgc.is_generated, 0)=1, gptgc.id_lang, NULL) SEPARATOR \',\') AS `generated_langs`,
                GROUP_CONCAT(IF(IFNULL(gptgc.is_translated, 0)=1, gptgc.id_lang, NULL) SEPARATOR \',\') AS `translated_langs`' .
                $subSelect .
            ' FROM `' . _DB_PREFIX_ . 'content_generator` AS gptgc
            WHERE gptgc.object_type = ' . ContentGeneratorEntity::TYPE_CATEGORY .
            ' GROUP BY gptgc.id_object
        )';
    }

    private function handleCategoriesFilter()
    {
        if (\Tools::isSubmit('submitResetcategory') || \Tools::getValue('submitFiltercategory') === '0') {
            unset($this->context->cookie->filter_column_category_generated_description);
            unset($this->context->cookie->filter_column_category_translated_description);
            $this->context->cookie->write();
            return;
        }

        if (\Tools::isSubmit('filter_column_generated_langs')) {
            $selectedLangs = array_filter(\Tools::getValue('filter_column_generated_langs', []));
            if (is_array($selectedLangs) && !empty($selectedLangs)) {
                $this->context->cookie->filter_column_category_generated_description = implode(',', $selectedLangs);
                $this->context
                    ->cookie
                    ->{'categoriescategoryFilter_content_gen!content_generated'} = 1;
                $this->context->cookie->write();
            } else {
                unset($this->context->cookie->filter_column_category_generated_description);
                $this->context->cookie->write();
            }
        } elseif (\Tools::isSubmit('category')) {
            unset($this->context->cookie->filter_column_category_generated_description);
            $this->context->cookie->write();
        }

        if (\Tools::isSubmit('filter_column_translated_langs')) {
            $selectedLangs = array_filter(\Tools::getValue('filter_column_translated_langs', []));
            if (is_array($selectedLangs) && !empty($selectedLangs)) {
                $this->context->cookie->filter_column_category_translated_description = implode(',', $selectedLangs);
                $this->context
                    ->cookie
                    ->{'categoriescategoryFilter_content_gen!content_translated'} = 1;
                $this->context->cookie->write();
            } else {
                unset($this->context->cookie->filter_column_category_translated_description);
                $this->context->cookie->write();
            }
        } elseif (\Tools::isSubmit('category')) {
            unset($this->context->cookie->filter_column_category_translated_description);
            $this->context->cookie->write();
        }
    }

    protected function getAdminPageName(): array
    {
        return ['', 0];
    }

    protected function getProductId(): int
    {
        return (int) $this->getRequest()->attributes->get('id');
    }

    protected function getMediaPatchForVersion(): string
    {
        return 'ps17';
    }
}
