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

class AdminChatGtpPostAjaxController extends AdminChatGtpContentAjaxController
{
    public function ajaxProcessGenerateProductPost()
    {
        $name = trim(Tools::getValue('name'));

        if ($name === '') {
            $name = (new Product((int) Tools::getValue('id'), false, (int) Tools::getValue('id_language')))->name;
        }

        if (trim($name) === '') {
            return $this->module->errorResponse(1, 'Product name is empty');
        }

        // template content
        $id_template = 0; // (int) Tools::getValue('id_content_template', 0);
        $prompt = null;
        if ($id_template) {
            $template = $this->getContentTemplate($id_template, $language->id);
            $object = $this->getObjectByEntity((int) Tools::getValue('id'), $language->id, 'product');
            $prompt = $template->getContentByObject($object);
        }

        try {
            if ($id_template) {
                throw new Exception('Please create method to generate the product post by prompt');
            } else {
                $post = $this->apiClient->generatePostForProduct([
                    'name' => $name, // product name
                    'maxLength' => Tools::getValue('length'),
                    'langIsoCode' => (new Language((int) Tools::getValue('id_language')))->iso_code,
                    'categoryName' => '',
                    'brandName' => '',
                ]);
            }

            $post_content = $post['text'];

            $images = Tools::getValue('images', []);
            $post_content = $this->insertImagesIntoText($images, $post_content);

            // prepare post data
            $content = [];
            $title = [];
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $content[$lang['id_lang']] = $post_content;
                $title[$lang['id_lang']] = self::getTitleFromContent($post['text'], $name);
            }

            $product_post = $this->createPost((int) Tools::getValue('id'), $title, $content);
        } catch (Exception $e) {
            $this->module->jsonExeptionResponse($e);
        }

        $this->module->jsonResponse([
            'text' => $post['text'],
            'nbWords' => (!isset($post['nbWords'])
                ? str_word_count($post['text'])
                : $post['nbWords']),
            'inQueue' => $post['inQueue'],
            'requestId' => $post['requestId'],
            'post' => (new GptContentPost($product_post->id, $this->context->language->id)),
            // 'post' => $post,
        ]);
    }

    public function ajaxProcessTranslateProductPostById()
    {
        try {
            $post = new GptContentPost((int) Tools::getValue('id'));
            if (!Validate::isLoadedObject($post)) {
                throw new Exception(sprintf('Post #%s is not found', Tools::getValue('id')));
            }
            $from_language = new Language((int) Tools::getValue('fromLangaugeId'));
            if (!Validate::isLoadedObject($post)) {
                throw new Exception('Initial language is not defined');
            }
            $to_language = new Language((int) Tools::getValue('toLanguageId'));
            if (!Validate::isLoadedObject($post)) {
                throw new Exception('Target language is not defined');
            }

            $text = isset($post->content[$from_language->id]) ? trim($post->content[$from_language->id]) : '';

            if ($text !== '') {
                $text = $this->apiClient->translatePostContent($text, $from_language->iso_code, $to_language->iso_code);
            }

            // update post content
            $post->content[$to_language->id] = $text['text'];
            $post->title[$to_language->id] = self::getTitleFromContent($text['text'], $post->title[$to_language->id]);
            $post->short_content[$to_language->id] = self::getShortDescriptionFromContent($text['text'], $post->short_content[$to_language->id]);
            $post->update();
        } catch (Exception $e) {
            $this->module->jsonExeptionResponse($e);
        }

        $this->module->jsonResponse([
            'text' => $text['text'],
            'nbWords' => (!isset($text['nbWords']) ? str_word_count($text['text']) : $text['nbWords']),
            'inQueue' => $text['inQueue'],
            'requestId' => $text['requestId'],
        ]);
    }

    public function ajaxProcessGeneratePostContent()
    {
        $name = trim(Tools::getValue('name'));
        if ($name === '') {
            return $this->module->errorResponse(1, 'Post name is empty');
        }

        $language = $this->context->language;
        if (Tools::getValue('id_language')) {
            $language = new Language((int) Tools::getValue('id_language'));
        }

        $length = Tools::getValue('length');
        if (!is_numeric($length)) {
            $length = 0;
        }

        try {
            $content = $this->apiClient
                    ->postContent($name, $length, $language->iso_code);

            $post_content = $content['text'];
            $images = Tools::getValue('images', []);
            $post_content = $this->insertImagesIntoText($images, $post_content);
        } catch (Exception $e) {
            return $this->module->jsonExeptionResponse($e);
        }

        return $this->module->jsonResponse([
            'text' => $post_content,
            'nbWords' => (!isset($content['nbWords']) ? str_word_count($content['text']) : $content['nbWords']),
            'inQueue' => $content['inQueue'],
            'requestId' => $content['requestId'],
        ]);
    }

    private function createPost($id_product, array $title, array $content)
    {
        $post = new GptContentPost();
        $post->id_product = (int) $id_product;
        $post->title = $title;
        $post->content = $content;
        $post->link_rewrite = array_map(
            function ($t) {
                return Tools::link_rewrite($t);
            },
            $post->title
        );
        $post->short_content = [];
        foreach ($content as $id_lang => $html_content) {
            $post->short_content[$id_lang] = self::getShortDescriptionFromContent($html_content, '');
        }

        $post->add();

        return $post;
    }

    public static function getTitleFromContent($htmlContent, $defautlTitle = '')
    {
        $m = null;
        preg_match_all('/<h1>((.|\r\n|\r|\n)*?)<\/h1>/', $htmlContent, $m);
        if ($m && isset($m[1]) && !empty($m[1]) && trim($m[1][0]) !== '') {
            return trim(str_replace("\n", '', $m[1][0]));
        }
        return $defautlTitle;
    }

    public static function getShortDescriptionFromContent($htmlContent, $defautlContent = '')
    {
        $m = null;
        preg_match_all('/<p>((.|\r\n|\r|\n)*?)<\/p>/', $htmlContent, $m);
        if ($m && isset($m[0]) && !empty($m[0])) {
            return trim($m[0][0]);
        }
        return $defautlContent;
    }

    private function insertImagesIntoText($images, $htmlContent)
    {
        if (!is_array($images) || empty($images)) {
            return $htmlContent;
        }

        // split HTML by paragraphs
        $paragraphs = null;
        preg_match_all('/<p>((.|\r\n|\r|\n)*?)<\/p>/', $htmlContent, $paragraphs);

        if (!is_array($paragraphs) || !isset($paragraphs[0]) || empty($paragraphs[0])) {
            return $htmlContent;
        }

        $paragraphs = $paragraphs[0];

        $dir = _PS_IMG_DIR_ . 'chatgptcontentgenerator/post-media/';
        if (!file_exists(rtrim($dir))) {
            mkdir($dir, 0777, true);
        }
        foreach ($images as $k => $img) {
            if (!file_exists($img['save_path'])) {
                unset($images[$k]);
                continue;
            }
            $path_parts = pathinfo($img['save_path']);
            $fileName = date('Y-m-d_h-i-s') . '-' . $path_parts['filename'] . '.' . $path_parts['extension'];
            copy($img['save_path'], $dir . $fileName);
            $images[$k]['url'] = $this->context->link->getMediaLink('/img/chatgptcontentgenerator/post-media/' . $fileName);
        }

        $images = array_values($images);

        $nbParagraphs = count($paragraphs);
        $avgImages = ceil(count($images) / $nbParagraphs);

        for ($i = 0; $i < $nbParagraphs; $i = $i + 1) {
            $pImages = [];

            if ($images) {
                for ($j = 0; $j < $avgImages; $j = $j + 1) {
                    if (!isset($images[$j])) {
                        break;
                    }
                    $pImages[] = $images[$j];
                    unset($images[$j]);
                    $images = array_values($images);
                }
            }
            if ($pImages) {
                $htmlContent = str_replace(
                    $paragraphs[$i],
                    $paragraphs[$i] . '<p>' .
                            implode(
                                ' ',
                                array_map(
                                    function ($img) {
                                        return '<img src="' . $img['url'] . '" class="blog-content-images" alt="" style="display: inline-block;"/>';
                                    },
                                    $pImages
                                )
                            ) .
                        '</p>',
                    $htmlContent
                );
            }
        }

        return $htmlContent;
    }
}
