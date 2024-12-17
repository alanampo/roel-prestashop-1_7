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

use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentPost;

class AdminChatGtpContentBlogPostController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = GptContentPost::$definition['table'];
        $this->className = GptContentPost::class; // 'PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentPost';
        $this->identifier = GptContentPost::$definition['primary'];
        $this->identifier_name = $this->identifier;
        $this->list_no_link = true;
        $this->lang = true;

        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('preview');
        $this->addRowAction('delete');

        parent::__construct();

        $this->displayInformations = $this->trans('Some option may be available after saving post', [], 'Modules.Chatgptcontentgenerator.Admin');

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'confirm' => $this->trans('Delete selected items?', [], 'Modules.Chatgptcontentgenerator.Admin'),
            ],
            'enableSelection' => ['text' => $this->trans('Enable selection', [], 'Modules.Chatgptcontentgenerator.Admin')],
            'disableSelection' => ['text' => $this->trans('Disable selection', [], 'Modules.Chatgptcontentgenerator.Admin')],
        ];

        $this->fields_list = [
            $this->identifier => [
                'title' => $this->trans('ID', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'align' => 'center',
                'width' => 30,
            ],
            'cover' => [
                'title' => $this->trans('Post thumbnail', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'width' => 150,
                'orderby' => false,
                'search' => false,
                'callback' => 'getPostThumbnail',
            ],
            'title' => [
                'title' => $this->trans('Title', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'width' => 'auto',
                'filter_key' => 'b!title',
            ],
            'short_content' => [
                'title' => $this->trans('Description', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'width' => 500,
                'orderby' => false,
                'callback' => 'printDescription',
            ],
            'views' => [
                'title' => $this->trans('Views', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'width' => 30,
                'align' => 'center',
                'search' => false,
            ],
            'date_add' => [
                'title' => $this->trans('Publication date', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'date',
                'filter_key' => 'a!date_add',
            ],
            'active' => [
                'title' => $this->trans('Displayed', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
        ];
    }

    public function displayPreviewLink($token, $id)
    {
        $url = $this->context->link->getModuleLink(
            $this->module->name,
            'blogpost',
            [
                'rewrite' => (new GptContentPost($id, $this->context->language->id))->link_rewrite,
            ]
        );
        return implode('', ['<', 'a ', 'href="', $url, '" target="_blank"', '>', '<i ', 'class="icon-eye"', '></', 'i> ', $this->trans('Preview', [], 'Modules.Chatgptcontentgenerator.Admin'), '<', '/', 'a>']);
    }

    public function renderList()
    {
        $subscriptionExpiredAlertHtml = $this->module->getSubscriptionExpiredAlertHtml(null, true);

        if (!$subscriptionExpiredAlertHtml) {
            $shopInfo = $this->module->getShopInfo();

            if (!$shopInfo || empty($shopInfo['subscription'])) {
                $subscriptionExpiredAlertHtml = $this->module->getSubscriptionExpiredAlertHtml(
                    null,
                    true,
                    $this->trans('<b>Attention!</b><br>
                        Please order subscription plan!',
                        [],
                        'Modules.Chatgptcontentgenerator.Admin'
                    )
                );
            }
        }

        if ($subscriptionExpiredAlertHtml) {
            return $subscriptionExpiredAlertHtml;
        }

        return parent::renderList();
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);

        if (!Validate::isLoadedObject($obj)) {
            $this->fields_value['date_add'] = date('Y-m-d H:i:s');
        }

        $cover = false;

        if (Validate::isLoadedObject($obj)) {
            $cover = ImageManager::thumbnail($obj->getCoverThumbPath(), 'gpt_' . pathinfo($obj->cover, PATHINFO_BASENAME), 300, 'jpg', true, true);
        }

        $coverSize = $cover ? filesize($obj->getCoverPath()) / 1000 : false;

        $selected_categories = Tools::getValue('blog_caid_gptcontent_post_categorytegory', [$obj->id_gptcontent_post_category]);

        $this->fields_form = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Post', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'icon' => false,
                    ],
                    'input' => [
                        [
                            'type' => 'text',
                            'label' => $this->trans('Title:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            'name' => 'title',
                            'required' => true,
                            'lang' => true,
                            'id' => 'name',
                            'class' => 'copyNiceUrl',
                        ],
                        [
                            'type' => 'textarea',
                            'label' => $this->trans('Short content:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            'name' => 'short_content',
                            'lang' => true,
                            'rows' => 5,
                            'cols' => 40,
                            'autoload_rte' => true,
                        ],
                        [
                            'type' => 'textarea',
                            'label' => $this->trans('Full post content:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            'name' => 'content',
                            'lang' => true,
                            'rows' => 15,
                            'cols' => 40,
                            'autoload_rte' => true,
                        ],
                        [
                            'type' => 'categories',
                            'label' => $this->trans('Related products category:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            'name' => 'id_gptcontent_post_category',
                            'required' => false,
                            'tree' => [
                                'id' => 'categories-tree',
                                'selected_categories' => $selected_categories,
                                'disabled_categories' => null,
                                'root_category' => $this->context->shop->getCategory(),
                                'use_checkbox' => false,
                            ],
                        ],
                        [
                            'type' => 'switch',
                            'label' => $this->trans('Displayed:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            'name' => 'active',
                            'required' => false,
                            'class' => 't',
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->trans('Enabled', [], 'Modules.Chatgptcontentgenerator.Admin'),
                                ],
                                [
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->trans('Disabled', [], 'Modules.Chatgptcontentgenerator.Admin'),
                                ],
                            ],
                        ],
                        [
                            'type' => 'datetime',
                            'label' => $this->trans('Publication date:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            'name' => 'date_add',
                            'required' => true,
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Save and stay', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'stay' => true,
                    ],
                ],
            ],
        ];

        $tpl_imagePreview = $this->context->smarty->createTemplate($this->getTemplatePath() . 'blog_post/cover_preview.tpl');
        $tpl_imagePreview->assign([
            'cover' => $cover ? $cover : false,
            'cover_size' => $coverSize,
            'delete_link' => $this->context->link->getAdminLink('AdminChatGtpContentBlogPost', true, [], [
                $this->identifier => $obj->id,
                'update' . $this->table => '',
                'deleteCover' => '1',
            ]),
        ]);

        $this->fields_form[] = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Images', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    'icon' => 'icon-picture',
                ],
                'input' => [
                    [
                        'type' => 'html',
                        'name' => 'cover_preview',
                        'title' => '',
                        'html_content' => $tpl_imagePreview->fetch(),
                    ],
                   [
                        'type' => 'file',
                        'label' => $this->trans('Cover', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'display_image' => true,
                        'name' => 'cover_img',
                   ],
                ],
                'submit' => [
                    'title' => $this->trans('Save and stay', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    'stay' => true,
                ],
            ],
        ];

        $this->fields_form[] = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('SEO', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    'icon' => 'icon-folder-close',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Meta title:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'name' => 'meta_title',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Meta description:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'name' => 'meta_description',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Meta keywords:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'name' => 'meta_keywords',
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Friendly URL:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'name' => 'link_rewrite',
                        'required' => true,
                        'lang' => true,
                        'suffix' => implode('', ['<', 'a ', 'href="#" ', 'class', '="generate-link-rewrite"', '>', '<i ', 'class', '="icon-refresh"', '>', '<', '/', 'i>', '<', '/', 'a', '>']),
                        'desc' => $this->trans(
                            'Click on %icon% to generate the friendly url based on post title',
                            [
                                '%icon%' => implode('', ['<i ', 'class', '="icon-refresh"', '>', '<', '/', 'i>']),
                            ],
                            'Modules.Chatgptcontentgenerator.Admin'
                        ),
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save and stay', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    'stay' => true,
                ],
            ],
        ];

        $this->multiple_fieldsets = true;

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('viewsimpleblog_post') && ($id_gptcontent_post = (int) Tools::getValue('id_gptcontent_post')) && ($gptBlogPost = new GptContentPost($id_gptcontent_post, $this->context->language->id)) && Validate::isLoadedObject($gptBlogPost)) {
            Tools::redirectAdmin(Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'single', ['rewrite' => $gptBlogPost->link_rewrite, 'sb_category' => $gptBlogPost->category_rewrite]));
        }

        if (Tools::isSubmit('deleteCover')) {
            $this->deleteCover((int) Tools::getValue('id_gptcontent_post'));
        }

        return parent::postProcess();
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();
        if (!Validate::isLoadedObject($object)) {
            return false;
        }

        try {
            if (isset($_FILES['cover_img'])) {
                $isImageValid = ImageManager::validateUpload($_FILES['cover_img']);

                if ($isImageValid === false) {
                    $object->createCover($_FILES['cover_img']['tmp_name']);
                } else {
                    throw new Exception($isImageValid);
                }
            }
        } catch (Exception $th) {
            $this->errors[] = $th->getMessage();
            $this->display = 'edit';
            return false;
        }
        return $object;
    }

    public function deleteCover($id)
    {
        $gptBlogPost = new GptContentPost($id, $this->context->language->id);
        if (Validate::isLoadedObject($gptBlogPost) && $gptBlogPost->deleteCover()) {
            $this->confirmations[] = $this->trans('Cover deleted', [], 'Modules.Chatgptcontentgenerator.Admin');
        }
        Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminChatGtpContentBlogPost'));
    }

    public function processAdd()
    {
        $object = parent::processAdd();

        if ($object) {
            try {
                if (isset($_FILES['cover_img']) && $_FILES['cover_img']['errors'] == 0) {
                    $isImageValid = ImageManager::validateUpload($_FILES['cover_img']);
                    if ($isImageValid === false) {
                        $object->createCover($_FILES['cover_img']['tmp_name']);
                    } else {
                        throw new Exception($isImageValid);
                    }
                }
            } catch (Exception $th) {
                $this->errors[] = $th->getMessage();
            }
        }

        return $object;
    }

    public static function getPostThumbnail($cover, $row)
    {
        if ($cover) {
            return ImageManager::thumbnail(_PS_IMG_DIR_ . $cover, 'gpt-list_' . pathinfo($cover, PATHINFO_BASENAME), 75, 'jpg', true, true);
        }
    }

    public function printDescription($value)
    {
        return strip_tags($value);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef([
            'PS_ALLOW_ACCENTED_CHARS_URL' => false,
        ]);
    }
}
