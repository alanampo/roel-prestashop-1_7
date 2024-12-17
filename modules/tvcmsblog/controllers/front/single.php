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

class TvcmsBlogSingleModuleFrontController extends TvcmsBlogMainModuleFrontController
{
    public $blogpost;
    public $tvcmserrors;
    public $id_identity;
    public $rewrite;

    public function init()
    {
        parent::init();
        $this->rewrite = Tools::getValue('rewrite');
        $id_identity = Tools::getValue('id');
        if (!isset($id_identity) || empty($id_identity)) {
            $this->id_identity = (int)TvcmsPostsClass::getTheId($this->rewrite, $this->page_type);
        } else {
            $this->id_identity = (int)$id_identity;
        }
        if (!TvcmsPostsClass::PostExists($this->id_identity, $this->page_type)) {
            $url = TvcmsBlog::tvcmsBlogLink();
            Tools::redirect($url);
            $this->errors[] = Tools::displayError($this->l('Blog Post Not Found.'));
        }
        if (!$this->id_identity || !Validate::isUnsignedId($this->id_identity)) {
            Tools::redirect('index.php?controller=404');
            $this->errors[] = Tools::displayError($this->l('Blog Post Not Found.'));
        } else {
            $this->blogpost = TvcmsPostsClass::getSinglePost($this->id_identity);
            TvcmsPostsClass::postCountUpdate($this->id_identity);
        }
    }
    
    public function initContent()
    {
         $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        $blogid = $this->blogpost['id_tvcmsposts'];

        $select_data = 'SELECT MAX(id_view) as max_id FROM `'._DB_PREFIX_.'tvcmsposts_view` where `id_tvcmsposts` = '.$blogid.' AND `ipadress` = \''.$ipaddress.'\' ';
        $ans = Db::getInstance()->executeS($select_data);
         
        if (1>$ans[0]['max_id']) {
                $dataquery = 'INSERT INTO `'._DB_PREFIX_.'tvcmsposts_view`
                                SET 
                                    `id_tvcmsposts` = '.$blogid.',
                                    ipadress = \''.$ipaddress.'\'';
                Db::getInstance()->execute($dataquery);
        }
        $check_data = 'SELECT `id_tvcmsposts` FROM `'._DB_PREFIX_.'tvcmsposts_view` where `id_tvcmsposts` = '.$blogid.'';
        $ansaa = Db::getInstance()->executeS($check_data);
        if (!empty($ansaa)) {
            $blogview = count($ansaa);
        } else {
            $blogview = 0;
        }
        $this->context->smarty->assign('blog_view', $blogview);
        parent::initContent();
        if (isset($this->blogpost) && !empty($this->blogpost)) {
            $this->context->smarty->assign('tvcmsblogpost', $this->blogpost);
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $this->blogpost['meta_title'];
            if (isset($this->blogpost['meta_title']) && !empty($this->blogpost['meta_title'])) {
                $this->context->smarty->assign('meta_title', $this->blogpost['meta_title']);
            } else {
                $this->context->smarty->assign('meta_title', $this->blogpost['post_title']);
            }
            if (isset($this->blogpost['meta_description']) && !empty($this->blogpost['meta_description'])) {
                $this->context->smarty->assign('meta_description', $this->blogpost['meta_description']);
            } else {
                $this->context->smarty->assign('meta_description', $this->blogpost['post_excerpt']);
            }
            $this->context->smarty->assign('meta_keywords', $this->blogpost['meta_keyword']);
        }
        if (isset($this->id_identity) && !empty($this->id_identity)) {
            $tvcmsblog_commets = TvcmsCommentClass::getComments($this->id_identity);
            $this->context->smarty->assign('tvcmsblog_commets', $tvcmsblog_commets);
        }
        if (isset($this->tvcmserrors) && !empty($this->tvcmserrors)) {
            $this->context->smarty->assign('tvcmserrors', $this->tvcmserrors);
        }
        $path = TvcmsPostsClass::getsinglepath($this->id_identity, $this->page_type);
        $this->context->smarty->assign('path', $path);

        $disable_blog_com = (int)Configuration::get(TvcmsBlog::$tvcmsblogshortname."disable_blog_com");
        $this->context->smarty->assign('disable_blog_com', $disable_blog_com);

        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcms_image_type` ';
        $sql .= ' WHERE active = 1 AND id_shop = '.(int)$id_shop;
        $queryexec = Db::getInstance()->executeS($sql);
        $lagewidth = $queryexec[2]['width'];
        $lageheight = $queryexec[2]['height'];
        $this->context->smarty->assign('lagewidth', $lagewidth);
        $this->context->smarty->assign('lageheight', $lageheight);

        $this->context->smarty->assign('tvcmsblog_dir', _PS_MODULE_DIR_.TvcmsBlog::$ModuleName);
        $this->context->smarty->assign('tvcmsblog_uri', __PS_BASE_URI__.TvcmsBlog::$ModuleName);
        $this->context->smarty->assign('tvcmsblog_img_uri', __PS_BASE_URI__.TvcmsBlog::$ModuleName.'/img/');
        $this->context->smarty->assign('tvcmsblog_css_uri', __PS_BASE_URI__.TvcmsBlog::$ModuleName.'/css/');
        $this->context->smarty->assign('tvcmsblog_js_uri', __PS_BASE_URI__.TvcmsBlog::$ModuleName.'/js/');
        $template = "single.tpl";
        if (!empty($this->page_type)) {
            if (isset($this->blogpost['post_format']) && !empty($this->blogpost['post_format'])) {
                $post_format = "-".$this->blogpost['post_format'];
            } else {
                $post_format = '';
            }

            $page_type = (isset($this->page_type) && !empty($this->page_type)) ? $this->page_type."-" : "";
            $template1 = $page_type.'single'.$post_format.'.tpl';
            $template2 = $page_type.'single.tpl';
            $template3 = 'single'.$post_format.'.tpl';
            if ($this->getTemplatePath($template1)) {
                $template = $template1;
            } elseif ($this->getTemplatePath($template2)) {
                $template = $template2;
            } elseif ($this->getTemplatePath($template3)) {
                $template = $template3;
            } else {
                $template = "single.tpl";
            }
            $id_identity = Tools::getValue('id');
            if (isset($id_identity) && !empty($id_identity)) {
                $tvcmsblog_total_commets = TvcmsCommentClass::getCountComments($id_identity);
                $this->context->smarty->assign('tvcmsblog_total_commets', $tvcmsblog_total_commets);
            }
        }
        $this->setTemplate($template);
    }
    public function getLayout()
    {
        $entity = 'module-tvcmsblog-single';
        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);
        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            array(
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
            )
        )) {
            return $overridden_layout;
        }
        if ((int)Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }
        return $layout;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $blog_title = Configuration::get(TvcmsBlog::$tvcmsblogshortname."meta_title");
        $breadcrumb['links'][] = array(
            'title' => $blog_title,
            'url' => TvcmsBlog::tvcmsBlogLink(),
        );

        $breadcrumb['links'][] = array(
            'title' => $this->blogpost['category_arr']['title'],
            'url' => $this->blogpost['category_arr']['link'],
        );

        $breadcrumb['links'][] = array(
            'title' => $this->blogpost['post_title'],
            'url' => $this->blogpost['link'],
        );

        return $breadcrumb;
    }
}
