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
*  @copyright 2007-2022 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class TvcmsBlogMainModuleFrontController extends ModuleFrontController
{
    public $nbProducts;
    public $page_type;
    public $module_name = 'tvcmsblog';
    public static $initialized = false;
    public $display_column_left = true;
    public $display_column_right = true;
    
    public function __construct()
    {
        parent::__construct();
        $this->controller_type = 'modulefront';
    }
    
    public function init()
    {
        $this->page_type = Tools::getValue('page_type');
        $post_per_page = (int)Configuration::get(TvcmsBlog::$tvcmsblogshortname."post_per_page");
        $this->n = (isset($post_per_page) && !empty($post_per_page)) ? $post_per_page : 12;
        if (self::$initialized) {
            return;
        }
        self::$initialized = true;
        parent::init();
    }
    
    public function initContent()
    {
        parent::initContent();
        // $test = new TvcmsBlogMainModuleFrontController();
        // echo '<pre>'; print_r($test); die;
        $tvcmsblog = new TvcmsBlog();
        $tvcmsblogsettings = $tvcmsblog->getSettingsValueS();
        $column_use = Configuration::get(TvcmsBlog::$tvcmsblogshortname."column_use");
        if ($this->display_column_left && ($column_use == 'own_ps')) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN'  => Hook::exec('displaytvcmsblogleft'),
            ));
        }
        if ($this->display_column_right && ($column_use == 'own_ps')) {
            $this->context->smarty->assign(array(
                'HOOK_RIGHT_COLUMN'  => Hook::exec('displaytvcmsblogright'),
            ));
        }
        $this->context->smarty->assign('tvcmsblogsettings', $tvcmsblogsettings);
    }
    
    public function setTemplate($template, $params = array(), $locale = null)
    {
        if (!$path = $this->getTemplatePath($template)) {
            $themename = TvcmsBlog::getThemeName();
            throw new PrestaShopException("JHPTemplate".$themename." Theme '$template' Template not found");
        }
        $this->template = $path;
    }
    
    public function getTemplatePath($template)
    {
        $themename = TvcmsBlog::getThemeName();
        if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.TvcmsBlog::$ModuleName.'/views/templates/front/'
                .$themename.'/'.$template)) {
            return _PS_THEME_DIR_.'modules/'.TvcmsBlog::$ModuleName.'/views/templates/front/'.$themename.'/'.$template;
        } elseif (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.TvcmsBlog::$ModuleName.'/views/templates/front/'
                .$template)) {
            return _PS_THEME_DIR_.'modules/'.TvcmsBlog::$ModuleName.'/views/templates/front/'.$template;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.TvcmsBlog::$ModuleName.'/views/templates/front/'.$themename
                .'/'.$template)) {
            return _PS_MODULE_DIR_.TvcmsBlog::$ModuleName.'/views/templates/front/'.$themename.'/'.$template;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.TvcmsBlog::$ModuleName.'/views/templates/front/'
                .$template)) {
            return _PS_MODULE_DIR_.TvcmsBlog::$ModuleName.'/views/templates/front/'.$template;
        }
        return false;
    }
    
    public function pagination($total_products = null)
    {
        if (!self::$initialized) {
            $this->init();
        } elseif (!$this->context) {
            $this->context = Context::getContext();
        }
        // Retrieve the default number of products per page and the other available selections
        $default_products_per_page = max(1, (int)Configuration::get(TvcmsBlog::$tvcmsblogshortname."post_per_page"));
        $n_array = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);
        if ((int)Tools::getValue('n') && (int)$total_products > 0) {
            $n_array[] = $total_products;
        }
        // Retrieve the current number of products per page (either the default,
            // the GET parameter or the one in the cookie)
        $this->n = $default_products_per_page;
        if (isset($this->context->cookie->nb_item_per_page)
            && in_array($this->context->cookie->nb_item_per_page, $n_array)
        ) {
            $this->n = (int)$this->context->cookie->nb_item_per_page;
        }
        if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $n_array)) {
            $this->n = (int)Tools::getValue('n');
        }
        // Retrieve the page number (either the GET parameter or the first page)
        $this->p = (int)Tools::getValue('p', 1);
        // Remove the page parameter in order to get a clean URL for the pagination template
        $current_url = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));
        if ($this->n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $this->n;
        }
        $pages_nb = ceil($total_products / (int)$this->n);
        if ($this->p > $pages_nb && $total_products != 0) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, $pages_nb, false));
        }
        $range = 2; /* how many pages around page selected */
        $start = (int)($this->p - $range);
        if ($start < 1) {
            $start = 1;
        }
        $stop = (int)($this->p + $range);
        if ($stop > $pages_nb) {
            $stop = (int)$pages_nb;
        }
        $this->context->smarty->assign(array(
            'nb_products'       => $total_products,
            'products_per_page' => $this->n,
            'pages_nb'          => $pages_nb,
            'p'                 => $this->p,
            'n'                 => $this->n,
            'nArray'            => $n_array,
            'range'             => $range,
            'start'             => $start,
            'stop'              => $stop,
            'current_url'       => $current_url,
        ));
    }
}
