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

use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentGenerator as ContentGeneratorEntity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HooksPS80 extends AbstractHooks
{
    protected $isNewVersion = false;

    public function getRegisterHooks(): array
    {
        return array_merge(
            parent::getRegisterHooks(),
            [
                'actionCategoryGridDataModifier',
                'actionAdminProductsListingFieldsModifier',
                'actionAdminProductsListingResultsModifier',
            ]
        );
    }

    protected function getAdminPageName(): array
    {
        $adminPageName = '';
        $adminPageId = 0;

        if ($request = $this->getRequest()) {
            if ($request->attributes->get('_route') == 'admin_product_catalog') {
                $adminPageName = 'productsList';
            } elseif ($request->attributes->get('_route') == 'admin_categories_index') {
                $adminPageName = 'categoriesList';
            } elseif ($request->attributes->get('_route') == 'admin_product_form') {
                $adminPageName = 'productForm';
            } elseif ($request->attributes->get('_route') == 'admin_categories_edit') {
                $adminPageName = 'categoryForm';
                $adminPageId = (int) $request->attributes->get('categoryId');
            } elseif ($request->attributes->get('_route') == 'admin_cms_pages_edit'
                || $request->attributes->get('_route') == 'admin_cms_pages_create') {
                $adminPageName = 'cmsForm';
                $adminPageId = (int) $request->attributes->get('cmsPageId');
            }
        }

        return [$adminPageName, $adminPageId];
    }

    protected function getProductId(): int
    {
        return (int) $this->getRequest()->attributes->get('id');
    }

    protected function getMediaPatchForVersion(): string
    {
        return 'ps80';
    }

    protected function getGptVersionSelectors(): array
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

    protected function getContentEditorPreffix(): array
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

    public function _hookActionCategoryGridDefinitionModifier($params)
    {
        if (!$this->getRequest() || $this->getRequest()->attributes->get('_route') != 'admin_categories_search') {
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

        self::$entities = $products;
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
                $params['count_query_builder']->andWhere('IFNULL(content_gen.gcolumn, 0) = ' . count($langs));
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
                $params['count_query_builder']->andWhere('IFNULL(content_gen.tcolumn, 0) = ' . count($langs));
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
}
