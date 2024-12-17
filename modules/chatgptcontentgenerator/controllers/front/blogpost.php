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

use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentPost;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;

class ChatgptcontentgeneratorBlogPostModuleFrontController extends ModuleFrontController
{
    public $post_rewrite;
    public $post;

    public function init()
    {
        parent::init();

        if (!Configuration::getGlobalValue('CHATGPTSPINOFF_MANAGE')) {
            Tools::redirect('index.php?controller=404');
        }

        $post_rewrite = Tools::getValue('rewrite', 0);

        if ($post_rewrite) {
            $this->post_rewrite = $post_rewrite;
        }

        $id_lang = Context::getContext()->language->id;

        $post = GptContentPost::getInstanceByRewrite($this->post_rewrite, $id_lang);

        if (!Validate::isLoadedObject($post) || Validate::isLoadedObject($post) && !$post->active) {
            Tools::redirect('index.php?controller=404');
        }

        if (!empty($post->meta_title)) {
            $this->context->smarty->assign('meta_title', $post->meta_title);
        } else {
            $this->context->smarty->assign('meta_title', $post->title);
        }

        if (!empty($post->meta_description)) {
            $this->context->smarty->assign('meta_description', $post->meta_description);
        }

        if (!empty($post->meta_keywords)) {
            $this->context->smarty->assign('meta_keywords', $post->meta_keywords);
        }

        $settings = [
            'is_display_time' => (bool) $this->module->getConfig('BLOG_DISPLAY_DATE', null, null, null, false),
            'is_share_icons' => (bool) $this->module->getConfig('BLOG_DISPLAY_SHARER', null, null, null, false),
            'is_cover' => (bool) $this->module->getConfig('BLOG_DISPLAY_FEATURED', null, null, null, false),
        ];
        $this->context->smarty->assign($settings);

        $this->post = $post;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Blog', [], 'Modules.Chatgptcontentgenerator.Front'),
            'url' => Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghome'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->post->title,
            'url' => $this->post->getLink(),
        ];
        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();

        $this->post->increaseViews();

        $result = GptContentPost::getPosts($this->context->language->id, 3, null, null, true, false, false, $this->post->id);
        $posts = $result['posts'];

        $this->context->smarty->assign([
            'post' => $this->post,
            'recents_posts' => $posts,
            'is_shortdescription' => (bool) $this->module->getConfig('BLOG_DISPLAY_DESCRIPTION', null, null, null, false),
        ]);

        if ($this->module->getConfig('BLOG_DISPLAY_RELATED')) {
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = $presenterFactory->getPresenter();

            if ($this->post->id_gptcontent_post_category != 0) {
                $productCategory = new Category($this->post->id_gptcontent_post_category);
                $products = $productCategory->getProducts($this->context->language->id, 1, 3);

                $presentedProducts = [];

                if ($products) {
                    foreach ($products as $product) {
                        $presentedProducts[] = $presenter->present(
                            $presentationSettings,
                            Product::getProductProperties($this->context->language->id, $product, $this->context),
                            $this->context->language
                        );
                    }
                }

                if (!empty($presentedProducts)) {
                    $this->context->smarty->assign([
                        'category_products' => $presentedProducts,
                    ]);
                }
            } elseif (!empty($this->post->id_product)) {
                $productObj = new Product((int) $this->post->id_product, false, $this->context->language->id);
                if (Validate::isLoadedObject($productObj)) {
                    $this->product = $productObj;
                }

                $product = (new ObjectPresenter())->present($productObj);
                $product['id_product'] = (int) $productObj->id;
                $product['out_of_stock'] = (int) $productObj->out_of_stock;
                $product['new'] = (int) $productObj->new;

                $product_full = Product::getProductProperties($this->context->language->id, $product, $this->context);

                if ($product_full['unit_price_ratio'] > 0) {
                    $unitPrice = ($presentationSettings->include_taxes) ? $product_full['price'] : $product_full['price_tax_exc'];
                    $product_full['unit_price'] = $unitPrice / $product_full['unit_price_ratio'];
                }

                $group_reduction = GroupReduction::getValueForProduct($productObj->id, (int) Group::getCurrent()->id);
                if ($group_reduction === false) {
                    $group_reduction = Group::getReduction((int) $this->context->cookie->id_customer) / 100;
                }
                $product_full['customer_group_discount'] = $group_reduction;

                $product_full['rounded_display_price'] = Tools::ps_round(
                    $product_full['price'],
                    Context::getContext()->currency->precision
                );
                $product_full['id_customization'] = 0;

                $product_for_template = $presenter->present($presentationSettings, $product_full, $this->context->language);

                $productManufacturer = new Manufacturer((int) $productObj->id_manufacturer, $this->context->language->id);

                $manufacturerImageUrl = $this->context->link->getManufacturerImageLink($productManufacturer->id);
                $undefinedImage = $this->context->link->getManufacturerImageLink(null);
                if ($manufacturerImageUrl === $undefinedImage) {
                    $manufacturerImageUrl = null;
                }

                $this->context->smarty->assign([
                    'product' => $product_for_template,
                    'displayUnitPrice' => (!empty($productObj->unity) && $productObj->unit_price_ratio > 0.000000) ? true : false,
                    'priceDisplay' => Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer),
                    'displayPackPrice' => false,
                    'product_manufacturer' => $productManufacturer,
                ]);

                $this->assignAttributesGroups($product_for_template);
            }
        }

        $popularpost = GptContentPost::getPosts($this->context->language->id, 3, null, null, true, 'views', false, $this->post->id);
        $this->context->smarty->assign('popular_posts', $popularpost['posts']);

        $this->setTemplate('module:chatgptcontentgenerator/views/templates/front/blogpost.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->registerStylesheet(
            'front-' . $this->module->name,
            '/modules/' . $this->module->name . '/views/css/front.css',
            [
                'media' => 'all',
                'priority' => 990,
            ]
        );

        $this->context->controller->registerJavascript(
            'front-blogpost',
            '/modules/' . $this->module->name . '/views/js/blogpost.js',
        );
    }

    protected function assignAttributesGroups($product_for_template = null)
    {
        $colors = [];
        $groups = [];
        $this->combinations = [];

        $attributes_groups = $this->product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $this->product->getCombinationImages($this->context->language->id);
            $combination_prices_set = [];

            foreach ($attributes_groups as $k => $row) {
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];

                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }

                    $colors[$row['id_attribute']]['attributes_quantity'] += (int) $row['quantity'];
                }

                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = [
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    ];
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = [
                    'name' => $row['attribute_name'],
                    'html_color_code' => $row['attribute_color'],
                    'texture' => (@filemtime(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) ? _THEME_COL_DIR_ . $row['id_attribute'] . '.jpg' : '',
                    'selected' => (isset($product_for_template['attributes'][$row['id_attribute_group']]['id_attribute']) && $product_for_template['attributes'][$row['id_attribute_group']]['id_attribute'] == $row['id_attribute']) ? true : false,
                ];

                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                }

                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }

                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];

                $this->combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $this->combinations[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
                $this->combinations[$row['id_product_attribute']]['price'] = (float) $row['price'];

                if (!isset($combination_prices_set[(int) $row['id_product_attribute']])) {
                    $combination_specific_price = null;
                    Product::getPriceStatic((int) $this->product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int) $row['id_product_attribute']] = true;
                    $this->combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }

                $this->combinations[$row['id_product_attribute']]['ecotax'] = (float) $row['ecotax'];
                $this->combinations[$row['id_product_attribute']]['weight'] = (float) $row['weight'];
                $this->combinations[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                $this->combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $this->combinations[$row['id_product_attribute']]['ean13'] = $row['ean13'];
                $this->combinations[$row['id_product_attribute']]['mpn'] = $row['mpn'];
                $this->combinations[$row['id_product_attribute']]['upc'] = $row['upc'];
                $this->combinations[$row['id_product_attribute']]['isbn'] = $row['isbn'];
                $this->combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $this->combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];

                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $this->combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $this->combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $this->combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $this->combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $combination_images[$row['id_product_attribute']][0]['id_image'];

                    if ($row['default_on']) {
                        foreach ($this->context->smarty->tpl_vars['product']->value['images'] as $image) {
                            if ($image['cover'] == 1) {
                                $current_cover = $image;
                            }
                        }

                        if (!isset($current_cover)) {
                            $current_cover = array_values($this->context->smarty->tpl_vars['product']->value['images'])[0];
                        }

                        if (is_array($combination_images[$row['id_product_attribute']])) {
                            foreach ($combination_images[$row['id_product_attribute']] as $tmp) {
                                if ($tmp['id_image'] == $current_cover['id_image']) {
                                    $this->combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $tmp['id_image'];

                                    break;
                                }
                            }
                        }

                        if ($id_image > 0) {
                            if (isset($this->context->smarty->tpl_vars['images']->value)) {
                                $product_images = $this->context->smarty->tpl_vars['images']->value;
                            }

                            if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image])) {
                                $product_images[$id_image]['cover'] = 1;
                                $this->context->smarty->assign('mainImage', $product_images[$id_image]);
                                if (count($product_images)) {
                                    $this->context->smarty->assign('images', $product_images);
                                }
                            }

                            $cover = $current_cover;

                            if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images)) {
                                $product_images[$cover['id_image']]['cover'] = 0;

                                if (isset($product_images[$id_image])) {
                                    $cover = $product_images[$id_image];
                                }

                                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id . '-' . $id_image) : (int) $id_image);
                                $cover['id_image_only'] = (int) $id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }

            $current_selected_attributes = [];
            $count = 0;
            foreach ($groups as &$group) {
                ++$count;
                if ($count > 1) {
                    $id_product_attributes = [0];
                    $query = 'SELECT pac.`id_product_attribute`
                        FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                        WHERE id_product = ' . $this->product->id . ' AND id_attribute IN (' . implode(',', array_map('intval', $current_selected_attributes)) . ')
                        GROUP BY id_product_attribute
                        HAVING COUNT(id_product) = ' . count($current_selected_attributes);
                    if ($results = Db::getInstance()->executeS($query)) {
                        foreach ($results as $row) {
                            $id_product_attributes[] = $row['id_product_attribute'];
                        }
                    }
                    $id_attributes = Db::getInstance()->executeS('SELECT pac2.`id_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac2' .
                        ((!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && 0 == Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) ?
                            ' INNER JOIN `' . _DB_PREFIX_ . 'stock_available` pa ON pa.id_product_attribute = pac2.id_product_attribute
                        WHERE pa.quantity > 0 AND ' :
                            ' WHERE ') .
                        'pac2.`id_product_attribute` IN (' . implode(',', array_map('intval', $id_product_attributes)) . ')
                        AND pac2.id_attribute NOT IN (' . implode(',', array_map('intval', $current_selected_attributes)) . ')');

                    foreach ($id_attributes as $k => $row) {
                        $id_attributes[$k] = (int) $row['id_attribute'];
                    }

                    foreach ($group['attributes'] as $key => $attribute) {
                        if (!in_array((int) $key, $id_attributes)) {
                            unset(
                                $group['attributes'][$key],
                                $group['attributes_quantity'][$key]
                            );
                        }
                    }
                }
                $index = 0;
                $current_selected_attribute = 0;

                foreach ($group['attributes'] as $key => $attribute) {
                    if ($index === 0) {
                        $current_selected_attribute = $key;
                    }

                    if ($attribute['selected']) {
                        $current_selected_attribute = $key;

                        break;
                    }
                }

                if ($current_selected_attribute > 0) {
                    $current_selected_attributes[] = $current_selected_attribute;
                }
            }

            if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => $quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }

                foreach ($colors as $key => $color) {
                    if ($color['attributes_quantity'] <= 0) {
                        unset($colors[$key]);
                    }
                }
            }
            foreach ($this->combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\'' . (int) $id_attribute . '\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $this->combinations[$id_product_attribute]['list'] = $attribute_list;
            }
            unset($group);

            $this->context->smarty->assign([
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $this->combinations,
                'combinationImages' => $combination_images,
            ]);
        } else {
            $this->context->smarty->assign([
                'groups' => [],
                'colors' => false,
                'combinations' => [],
                'combinationImages' => [],
            ]);
        }
    }

    public function getLayout()
    {
        return 'layouts/layout-left-column.tpl';
    }
}
