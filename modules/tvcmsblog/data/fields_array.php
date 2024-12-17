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

class MyFieldsForm
{
    public $fields_form = array();

    public function getAllForm($t)
    {
        error_reporting(0);
        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $t->l('General Setting'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $t->l('Meta Title'),
                    'desc' => $t->l('Inser Blog Meta Title'),
                    'name' => 'meta_title',
                    'default_val' => 'Blog Title',
                ),
                array(
                    'type' => 'tags',
                    'label' => $t->l('Meta Keyword'),
                    'desc' => $t->l('Inser Blog Meta Keyword'),
                    'name' => 'meta_keyword',
                    'default_val' => 'Blog,tvcmsblog',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $t->l('Meta Description'),
                    'name' => 'meta_description',
                    'desc' => $t->l('Please Input Meta Description'),
                    'default_val' => 'Meta Description',
                ),
                array(
                    'type' => 'select',
                    'label' => $t->l('Select Blog Template'),
                    'name' => 'theme_name',
                    'default_val' => 'default',
                    'options' => array(
                        'query' => TvcmsBlog::getAllThemes(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $t->l('Blog Posts Per Page'),
                    'desc' => $t->l('Please Enter How many Blog Post Display Per Page'),
                    'name' => 'post_per_page',
                    'class' => 'fixed-width-sm',
                    'default_val' => '20',
                ),
                array(
                    'type' => 'select',
                    'label' => $t->l('Select Left/Right Column'),
                    'name' => 'column_use',
                    'default_val' => 'default_ps',
                    'desc' => 'Which Column Do you want to use. displayleftcolumn,displayrightcolumn or'
                        .'displaytvcmsblogleft,displaytvcmsblogright column hook.',
                    'options' => array(
                        'query' => array(
                                array(
                                    'id' => 'default_ps',
                                    'name' => 'Default Prestashop Column',
                                ),
                                array(
                                    'id' => 'own_ps',
                                    'name' => 'JHPTemplate Own Column',
                                ),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $t->l('Auto Comment Approved'),
                    'name' => 'comment_approved',
                    'default_val' => '1',
                    'values' => array(
                        array(
                            'id' => 'active',
                            'value' => 1,
                            'label' => $t->l('Enabled')
                        ),
                        array(
                            'id' => 'active',
                            'value' => 0,
                            'label' => $t->l('Disabled')
                        )
                    ),
                ),
                
                array(
                    'type' => 'switch',
                    'label' => $t->l('Disable Blog Comments'),
                    'name' => 'disable_blog_com',
                    'default_val' => '1',
                    'values' => array(
                        array(
                            'id' => 'active',
                            'value' => 1,
                            'label' => $t->l('Enabled')
                        ),
                        array(
                            'id' => 'active',
                            'value' => 0,
                            'label' => $t->l('Disabled')
                        )
                    ),
                ),
                // array(
                //     'type' => 'switch',
                //     'label' => $t->l('Show in all page'),
                //     'name' => 'status_blog_all',
                //     'desc' => 'Note: Yes status means show in all pages, No means show in only homepage',
                //     'default_val' => '1',
                //     'values' => array(
                //         array(
                //             'id' => 'active',
                //             'value' => 1,
                //             'label' => $t->l('Enabled')
                //         ),
                //         array(
                //             'id' => 'active',
                //             'value' => 0,
                //             'label' => $t->l('Disabled')
                //         )
                //     ),
                // ),
            ),

            'submit' => array(
                'title' => $t->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $this->fields_form[1]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $t->l('URL Setting'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $t->l('Main Blog Url'),
                    'desc' => $t->l('Inser Main Blog Url'),
                    'name' => 'main_blog_url',
                    'prefix' => 'http://domain.com/',
                    'suffix' => '.html',
                    'default_val' => 'blog',
                    'class' => 'fixed-width-sm',
                ),
                array(
                    'type' => 'text',
                    'label' => $t->l('Category Blog Url'),
                    'desc' => $t->l('Inser Category Blog Url'),
                    'name' => 'category_blog_url',
                    'prefix' => 'http://domain.com/blog/',
                    'suffix' => '/1_rewrite.html',
                    'default_val' => 'category/{id}',
                    'class' => 'fixed-width-sm',
                ),
                array(
                    'type' => 'text',
                    'label' => $t->l('Single Blog Url'),
                    'desc' => $t->l('Inser Single Blog Url'),
                    'name' => 'single_blog_url',
                    'prefix' => 'http://domain.com/blog/',
                    'suffix' => '/1_rewrite.html',
                    'default_val' => 'post/{id}',
                    'class' => 'fixed-width-sm',
                ),
                array(
                    'type' => 'text',
                    'label' => $t->l('Tag Blog Url'),
                    'desc' => $t->l('Inser Tag Blog Url'),
                    'name' => 'tag_blog_url',
                    'prefix' => 'http://domain.com/blog/',
                    'suffix' => '/1_rewrite.html',
                    'default_val' => 'tag/{id}',
                    'class' => 'fixed-width-sm',
                ),
                // /category/{id_category}_{rewrite}
                // /category/{rewrite}_{id_category}
                // /category/{rewrite}

                // /tag/{id_tag}_{rewrite}
                // /tag/{rewrite}_{id_tag}
                // /tag/{rewrite}

                // /post/{id_post}_{rewrite}
                // /post/{rewrite}_{id_post}
                // /post/{rewrite}
                array(
                    'type' => 'radio',
                    'label' => $t->l('Url Format'),
                    'name' => 'url_format',
                    'default_val' => 'wthotid_seo_url',
                    'values' => array(
                        array(
                            'id' => 'default_seo_url',
                            'value' => 'default_seo_url',
                            'label' => $t->l('Default SEO Friendly: http://domain'
                                .'.com/module/tvcmsblog/single/?id_post=1'),
                        ),
                        array(
                            'id' => 'wthotid_seo_url',
                            'value' => 'wthotid_seo_url',
                            'label' => $t->l('URL Format: http://domain.com/blog/post/rewrite/'),
                        ),
                    ),
                ),
                array(
                    'type' => 'radio',
                    'label' => $t->l('Enable Use .html'),
                    'name' => 'postfix_url_format',
                    'default_val' => 'disable_html',
                    'values' => array(
                        array(
                            'id' => 'enable_html',
                            'value' => 'enable_html',
                            'label' => $t->l('Enable .html URL format.'),
                        ),
                        array(
                            'id' => 'disable_html',
                            'value' => 'disable_html',
                            'label' => $t->l('Disable .html URL format.'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $t->l('Save'),
                'class' => 'btn btn-default pull-right',
            )
        );

        return (array)$this->fields_form;
    }
}
