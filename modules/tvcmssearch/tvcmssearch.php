<?php
/**
* 2007-2022 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2022 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class TvcmsSearch extends Module
{
    private $templateFile;
    private $options;
    private $optionsCount = 0;

    public function __construct()
    {
        $this->name = 'tvcmssearch';
        $this->tab = 'front_office_features';
        $this->author = 'JHP Template';
        $this->version = '4.0.0';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'JHPTemplate - Quick Search';
        $this->description = 'Adds a quick search field to your website.';

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = '';

        $this->confirmUninstall = $this->l('Warning: all the data saved in your database will be deleted.'.
            ' Are you sure you want uninstall this module?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayNavSearchBlock')
            && $this->registerHook('displaySearch')
            && $this->registerHook('displayMobileSearchBlock')
            && $this->registerHook('displayHeader');
    }

    public function hookdisplayHeader()
    {
        $this->context->controller->addJqueryUI('ui.autocomplete');
        $this->context->controller->registerJavascript('modules-tvcmssearch', 'modules/'
            .$this->name.'/views/js/tvcmssearch.js', array('position' => 'bottom', 'priority' => 150));
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    protected function generateCategoriesMenu($categories)
    {
        $html = '';
        static $n = 0;
        foreach ($categories as $category) {
            if ($category['level_depth'] > 1 && $category['level_depth'] <= 3) {
                $OptionaChar ='';
                for ($i=1; $i<$n; $i++) {
                    // $OptionaChar .= "--";
                    $OptionaChar .= "&nbsp;&nbsp;";
                }
                $this->options[$this->optionsCount++] = array('id_category' => $category['id_category'],
                                                 'name' => $OptionaChar.' '.$category["name"]);
            }
            if (isset($category['children']) && !empty($category['children'])) {
                $n++;
                $html .= $this->generateCategoriesMenu($category['children']);
                $n--;
            }
        }
    }

    public function getAllCategories()
    {
        $cookie = Context::getContext()->cookie;
        $id_lang = $cookie->id_lang;

        $tmp = Category::getNestedCategories(null, $id_lang);
        if (empty($this->options)) {
            $this->generateCategoriesMenu($tmp);
        }
        $this->context->smarty->assign('options', $this->options);

        $tmp = $this->context->link->getPageLink('search', null, null, null, false, null, true);
        $this->context->smarty->assign('search_controller_url', $tmp);
    }

    public function getAjaxResult()
    {
        // Number of Product to show
        $num_of_product = 6;
        $context = Context::getContext();
        $result = array();
        $search_words = Tools::getValue('search_words');
        $category_id = Tools::getValue('category_id');
        $cat_id = trim($category_id);

        $cookie = Context::getContext()->cookie;
        $id_lang = $cookie->id_lang;
        $result = Search::find($id_lang, $search_words, 1, $num_of_product);

        $return_data = array();

        if ($cat_id != "undefined" && $cat_id != "0") {
            foreach ($result['result'] as $product) {
                $all_cat = Product::getProductCategories($product['id_product']);
                if (in_array($cat_id, $all_cat)) {
                    $return_data[$product['id_product']] = $product;
                    $image = Image::getCover($product['id_product']);
                    $img_type = ImageType::getFormattedName('small');
                    $tmp = $context->link->getImageLink($product['link_rewrite'], $image['id_image'], $img_type);
                    $return_data[$product['id_product']]['cover_image'] = $tmp;
                }
            }
        } else {
            foreach ($result['result'] as $product) {
                $return_data[$product['id_product']] = $product;
                $return_data[$product['id_product']]['all_cat'] = Product::getProductCategories($product['id_product']);
                $image = Image::getCover($product['id_product']);
                $img_type = ImageType::getFormattedName('small');
                $tmp = $context->link->getImageLink($product['link_rewrite'], $image['id_image'], $img_type);
                $return_data[$product['id_product']]['cover_image'] = $tmp;
            }
        }

        $html = '';
        $result_data = array();
        $result_data['total'] = 0;
        if (!empty($return_data)) {
            $result_data['total'] = $result['total'];

            $i = 1;
            $show_product = 6;
            foreach ($return_data as $data) {
                if ($i<= $show_product) {
                    $prod_img = $data['cover_image'];
                    $prod_name = $data['name'];
                    $prod_link = $data['link'];

                    if (!empty($data['specific_prices'])) {
                        $tmp = $data['price'];
                        $new_price = Tools::displayPrice($tmp);
                        $tmp = $data['price_without_reduction'];
                        $old_price = Tools::displayPrice($tmp);
                        if ($data['specific_prices']['reduction_type'] == 'percentage') {
                            $reduction = $data['specific_prices']['reduction'] * 100;
                            $prod_reduction = '-'.$reduction.'%';
                        } else {
                            $tmp = $data['specific_prices']['reduction'];
                            $prod_reduction = Tools::displayPrice($tmp);
                        }

                        $prod_price = '<span class=\'price\'>'.$new_price.'</span>
                            <span class=\'regular-price\'>'.$old_price.'</span>';
                    } else {
                        $tmp = $data['price'];
                        $new_price = Tools::displayPrice($tmp);
                        $prod_price = '<div class=\'price\'>'.$new_price.'</div>';
                    }

                    $html .= '
                        <div class=\'tvsearch-dropdown-wrapper clearfix\'>
                            <a href=\''.$prod_link.'\'>
                                <div class=\'tvsearch-dropdown-img-block\'>
                                    <img src=\''.$prod_img.'\' alt=\''.$prod_name.'\' />
                                </div>
                                <div class=\'tvsearch-dropdown-content-box\'>
                                    <div class=\'tvsearch-dropdown-title\'>'.$prod_name.'</div>
                                    <div class=\'product-price-and-shipping\'>'.$prod_price.'</div>
                                </div>
                            </a>
                        </div>';
                    $i++;
                } else {
                    return;
                }
            }
        }
        if (!empty($html)) {
            $result_data['html'] = $html;
            $this->context->smarty->assign('result_data', $result_data);
            return $this->display(__FILE__, "views/templates/front/display_ajax_result.tpl");
        }
    }

    public function hookdisplayNavSearchBlock()
    {
        $this->getAllCategories();
        return $this->display(__FILE__, "views/templates/front/display_search.tpl");
    }

    public function hookdisplaySearch()
    {
        $this->getAllCategories();
        return $this->display(__FILE__, "views/templates/front/display_search.tpl");
    }

    public function hookdisplayMobileSearchBlock()
    {
        $this->getAllCategories();
        return $this->display(__FILE__, "views/templates/front/display_mobile_search.tpl");
    }
}
