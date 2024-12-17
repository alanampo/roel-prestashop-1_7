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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\StatusColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShopBundle\Form\Admin\Type\IntegerMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HooksPS81 extends AbstractHooks
{
    public function getRegisterHooks(): array
    {
        return array_merge(
            parent::getRegisterHooks(),
            [
                'actionProductGridDefinitionModifier',
                'actionProductGridQueryBuilderModifier',
                'actionAfterUpdateCombinationListFormHandler',
            ]
        );
    }

    protected function getAdminPageName(): array
    {
        $adminPageName = '';
        $adminPageId = 0;

        if ($request = $this->getRequest()) {
            if (in_array($request->attributes->get('_route'), ['admin_products_index', 'admin_product_catalog'])) {
                $adminPageName = 'productsList';
            } elseif ($request->attributes->get('_route') == 'admin_categories_index') {
                $adminPageName = 'categoriesList';
            } elseif (in_array($request->attributes->get('_route'), ['admin_product_form', 'admin_products_edit'])) {
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
        return (int) $this->getRequest()->attributes->get('productId');
    }

    protected function getMediaPatchForVersion(): string
    {
        return 'ps81';
    }

    public function _hookActionCategoryGridDefinitionModifier(array $params)
    {
        $this->setDefinitionModifier($params['definition']);
    }

    public function _hookActionCategoryGridQueryBuilderModifier(array $params)
    {
        $subTable = $this->prepareContentGeneratorSql($params, ContentGeneratorEntity::TYPE_CATEGORY);

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

        $params['count_query_builder']->leftJoin(
            'p',
            '`' . pSQL(_DB_PREFIX_) . 'spinoff_connections`',
            'con',
            'p.`id_product` = con.`id_spinoff`'
        );

        $params['search_query_builder']->leftJoin(
            'p',
            '`' . pSQL(_DB_PREFIX_) . 'spinoff_connections`',
            'con',
            'p.`id_product` = con.`id_spinoff`'
        );
    }

    public function _hookActionProductGridDefinitionModifier(array $params)
    {
        $this->setDefinitionModifier($params['definition']);
    }

    public function _hookActionProductGridQueryBuilderModifier(array $params)
    {
        $subTable = $this->prepareContentGeneratorSql($params, ContentGeneratorEntity::TYPE_PRODUCT);

        $params['search_query_builder']->leftJoin(
            'p',
            $subTable,
            'content_gen',
            'content_gen.`id_object` = p.`id_product`'
        );

        $params['count_query_builder']->leftJoin(
            'p',
            $subTable,
            'content_gen',
            'content_gen.`id_object` = p.`id_product`'
        );

        $params['count_query_builder']->leftJoin(
            'p',
            '`' . pSQL(_DB_PREFIX_) . 'spinoff_connections`',
            'con',
            'p.`id_product` = con.`id_spinoff`'
        );

        $params['search_query_builder']->leftJoin(
            'p',
            '`' . pSQL(_DB_PREFIX_) . 'spinoff_connections`',
            'con',
            'p.`id_product` = con.`id_spinoff`'
        );
    }

    protected function setDefinitionModifier($definition)
    {
        $definition
            ->getColumns()
            ->addAfter(
                'name',
                (new DataColumn('generated_langs'))
                    ->setName($this->translator->trans('Content ChatGPT', [], 'Modules.Chatgptcontentgenerator.Admin'))
                    ->setOptions([
                        'field' => 'generated_langs',
                    ])
            )
            ->addAfter(
                'generated_langs',
                (new DataColumn('translated_langs'))
                    ->setName($this->translator->trans('Tranlsate ChatGPT', [], 'Modules.Chatgptcontentgenerator.Admin'))
                    ->setOptions([
                        'field' => 'translated_langs',
                    ])
            )
            ->addAfter(
                'active',
                (new StatusColumn('is_spinoff'))
                    ->setName($this->translator->trans('is Spin-off', [], 'Modules.Chatgptspinoff.Admin'))
                    ->setOptions([
                        'field' => 'is_spinoff',
                    ])
            )
            ->addAfter(
                'active',
                (new DataColumn('count_spinoffs'))
                    ->setName($this->translator->trans('Spin-offs', [], 'Modules.Chatgptspinoff.Admin'))
                    ->setOptions([
                        'field' => 'count_spinoffs',
                    ])
            )
        ;

        $definition->getFilters()
            ->add(
                (new Filter('generated_langs', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'multiple' => true,
                        'placeholder' => $this->translator->trans('All', [], 'Admin.Global'),
                        'choices' => $this->getLanguages(),
                        'attr' => [
                            'class' => 'gpt-select2',
                        ],
                    ])
                    ->setAssociatedColumn('generated_langs')
            )
            ->add(
                (new Filter('translated_langs', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'multiple' => true,
                        'choices' => $this->getLanguages(),
                        'attr' => [
                            'class' => 'gpt-select2',
                        ],
                    ])
                    ->setAssociatedColumn('translated_langs')
            )
            ->add(
                (new Filter('is_spinoff', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('is_spinoff')
            )
            ->add(
                (new Filter('count_spinoffs', IntegerMinMaxFilterType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('count_spinoffs')
            )
        ;
    }

    protected function prepareContentGeneratorSql(&$params, $type)
    {
        /** @var QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];

        /** @var CustomerFilters $searchCriteria */
        $searchCriteria = $params['search_criteria'];

        /** @var QueryBuilder $countQueryBuilder */
        $countQueryBuilder = $params['count_query_builder'];

        $subSelect = '';

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('generated_langs' === $filterName) {
                if (!is_array($filterValue)) {
                    $filterValue = [$filterValue];
                }

                $subSelect .= ', SUM(IF(gptgc.id_lang IN (' .
                    pSql(implode(', ', $filterValue)) . ') ' .
                    'AND IFNULL(gptgc.is_generated, 0)=1, 1, 0)) AS `gcolumn`';

                $sqlWhereGeneratedLangs = 'IFNULL(content_gen.gcolumn, 0) = ' . count($filterValue);
                $searchQueryBuilder->andWhere($sqlWhereGeneratedLangs);
                $countQueryBuilder->andWhere($sqlWhereGeneratedLangs);
            }

            if ('translated_langs' === $filterName) {
                if (!is_array($filterValue)) {
                    $filterValue = [$filterValue];
                }

                $subSelect .= ', SUM(IF(gptgc.id_lang IN (' .
                    pSql(implode(', ', $filterValue)) . ') ' .
                    'AND IFNULL(gptgc.is_translated, 0)=1, 1, 0)) AS `tcolumn`';

                $sqlWhereTranslatedLangs = 'IFNULL(content_gen.tcolumn, 0) = ' . count($filterValue);
                $searchQueryBuilder->andWhere($sqlWhereTranslatedLangs);
                $countQueryBuilder->andWhere($sqlWhereTranslatedLangs);
            }

            if ('is_spinoff' === $filterName) {
                if ($filterValue) {
                    $sqlWhereIsSpinOff = 'con.`id_spinoff` > 0';
                } else {
                    $sqlWhereIsSpinOff = 'con.`id_spinoff` IS NULL';
                }
                $searchQueryBuilder->andWhere($sqlWhereIsSpinOff);
                $countQueryBuilder->andWhere($sqlWhereIsSpinOff);
            }

            if ('count_spinoffs' === $filterName) {
                $sqlQueryLeftJoin = '(
                    SELECT id_product, COUNT(*) AS count_spinoffs
                    FROM ' . pSQL(_DB_PREFIX_) . 'spinoff_connections
                    GROUP BY id_product
                )';

                $searchQueryBuilder->leftJoin(
                    'p',
                    $sqlQueryLeftJoin,
                    'soc',
                    'p.`id_product` = soc.`id_product`'
                );

                $countQueryBuilder->leftJoin(
                    'p',
                    $sqlQueryLeftJoin,
                    'soc',
                    'p.`id_product` = soc.`id_product`'
                );

                if (isset($filterValue['min_field'])) {
                    $searchQueryBuilder->andWhere('IFNULL(count_spinoffs, 0) >= :count_spinoffs_min');
                    $searchQueryBuilder->setParameter('count_spinoffs_min', $filterValue['min_field']);

                    $countQueryBuilder->andWhere('IFNULL(count_spinoffs, 0) >= :count_spinoffs_min');
                    $countQueryBuilder->setParameter('count_spinoffs_min', $filterValue['min_field']);
                }
                if (isset($filterValue['max_field'])) {
                    $searchQueryBuilder->andWhere('IFNULL(count_spinoffs, 0) <= :count_spinoffs_max');
                    $searchQueryBuilder->setParameter('count_spinoffs_max', $filterValue['max_field']);

                    $countQueryBuilder->andWhere('IFNULL(count_spinoffs, 0) <= :count_spinoffs_max');
                    $countQueryBuilder->setParameter('count_spinoffs_max', $filterValue['max_field']);
                }
            }
        }

        $subTable = '(
            SELECT
                gptgc.id_object,
                GROUP_CONCAT(IF(IFNULL(gptgc.is_generated, 0)=1, l.iso_code, NULL) SEPARATOR \',\') AS `generated_langs`,
                GROUP_CONCAT(IF(IFNULL(gptgc.is_translated, 0)=1, l.iso_code, NULL) SEPARATOR \',\') AS `translated_langs`
                ' . $subSelect . '
            FROM `' . pSQL(_DB_PREFIX_) . 'content_generator` AS gptgc
            LEFT JOIN `' . pSQL(_DB_PREFIX_) . 'lang` as l ON (gptgc.`id_lang` = l.`id_lang`)
            WHERE gptgc.object_type = ' . (int) $type .
            ' GROUP BY gptgc.id_object
        )';

        $searchQueryBuilder->addSelect('
            IFNULL(content_gen.`generated_langs`, \'---\') AS generated_langs,
            IFNULL(content_gen.`translated_langs`, \'---\') AS translated_langs,
            CASE WHEN con.`id_spinoff` IS NULL THEN 0 ELSE 1 END AS `is_spinoff`,
            IFNULL(
                (SELECT COUNT(*) FROM `' . pSQL(_DB_PREFIX_) . 'spinoff_connections` con2
                    WHERE con2.`id_product` = p.`id_product` GROUP BY con2.`id_product`),
                \'---\') AS count_spinoffs
        ');

        return $subTable;
    }

    public function _hookActionAfterUpdateCombinationListFormHandler(array $params)
    {
        if (!empty($params['id']) && !empty($params['form_data'])) {
            $idProduct = $params['id'];
            $product_list = [];

            if (!GptSpinoffConnections::getConectionsBySpinOffId($idProduct)) {
                foreach ($params['form_data'] as $combination) {
                    $product_list[] = [
                        'product_id' => $idProduct,
                        'product_attribute_id' => $combination['combination_id'],
                        'quantity' => $combination['delta_quantity']['quantity'],
                    ];
                }
            }

            if ($product_list) {
                GptSpinoffConnections::updateStockByProductList($product_list, $this->context->language->id);
            }
        }
    }
}
