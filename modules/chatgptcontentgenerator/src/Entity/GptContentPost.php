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

namespace PrestaShop\Module\Chatgptcontentgenerator\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GptContentPost extends \ObjectModel
{
    public $id_product;
    public $id_gptcontent_post_category;
    public $title;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $short_content;
    public $content;
    public $link_rewrite;
    public $active = 1;
    public $date_add;
    public $date_upd;
    public $is_featured = 0;
    public $cover;
    public $featured;
    public $author;
    public $likes = 0;
    public $views = 0;
    public $allow_comments = 3;
    public $video_code;

    public static $definition = [
        'table' => 'gptcontent_post',
        'primary' => 'id_gptcontent_post',
        'multilang' => true,
        'fields' => [
            'id_gptcontent_post_category' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'active' => ['type' => self::TYPE_BOOL],
            'is_featured' => ['type' => self::TYPE_BOOL],
            'author' => ['type' => self::TYPE_STRING],
            'likes' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'views' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'allow_comments' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'cover' => ['type' => self::TYPE_STRING],
            'featured' => ['type' => self::TYPE_STRING],
            'id_product' => ['type' => self::TYPE_INT],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'meta_keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'link_rewrite' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128],
            'short_content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'video_code' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    public function getCoversDir()
    {
        return 'chatgptcontentgenerator/covers/';
    }

    public function getCoversFullDir()
    {
        return _PS_IMG_DIR_ . $this->getCoversDir();
    }

    public function getCoverPath()
    {
        return _PS_IMG_DIR_ . $this->cover;
    }

    public function getCoverThumbPath()
    {
        if (!empty($this->cover)) {
            return $this->getCoversFullDir() . $this->id . '/' . $this->id . '-thumb.jpg';
        }

        return false;
    }

    public function createCover($img)
    {
        if (!file_exists($img)) {
            throw new \Exception('Image not exist');
        }

        $mime = \ImageManager::getMimeType($img);

        $imageTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        if (array_key_exists($mime, $imageTypes)) {
            $extension = $imageTypes[$mime];
        } else {
            throw new \Exception('Image type not allowed');
        }

        $this->cover = $this->getCoversDir() . $this->id . '/' . $this->id . '.' . $extension;

        if (!file_exists($this->getCoversFullDir() . $this->id . '/')) {
            mkdir($this->getCoversFullDir() . $this->id . '/', 0777, true);
        }

        move_uploaded_file($img, $this->getCoverPath());

        $thumb = $this->getCoversFullDir() . $this->id . '/' . $this->id . '-thumb.jpg';

        $module = \Module::getInstanceByName('chatgptcontentgenerator');

        $thumb_x = $module->getConfig('CHATGPTCONTENTGENERATOR_BLOG_THUMB_X', null, null, null, 600);
        $thumb_y = $module->getConfig('CHATGPTCONTENTGENERATOR_BLOG_THUMB_Y', null, null, null, 300);

        $statusThumb = \ImageManager::resize($this->getCoverPath(), $thumb, $thumb_x, $thumb_y, 'jpg');

        if ($statusThumb) {
            return $this->save();
        } else {
            throw new \Exception('Thumbnails not created');
        }

        return false;
    }

    public function deleteCover()
    {
        if (file_exists($this->getCoverPath())) {
            @unlink($this->getCoverPath());
        }

        if (file_exists($this->getCoverThumbPath())) {
            @unlink($this->getCoverThumbPath());
        }

        $this->cover = null;
        return $this->update();
    }

    public function delete()
    {
        $this->deleteCover();

        return parent::delete();
    }

    public function getCoverThumbnailLink()
    {
        $context = \Context::getContext();

        if (!empty($this->cover)) {
            $link = $context->link->getMediaLink('/img/' . $this->getCoversDir() . $this->id . '/' . $this->id . '-thumb.jpg');
            return $link;
        }

        return $context->link->getMediaLink(\Module::getInstanceByName('chatgptcontentgenerator')->getPathUri() . 'views/img/no.jpeg');
    }

    public function getCoverLink()
    {
        $context = \Context::getContext();

        if (!empty($this->cover)) {
            $link = $context->link->getMediaLink('/img/' . $this->cover);
            return $link;
        }

        return $context->link->getMediaLink(\Module::getInstanceByName('chatgptcontentgenerator')->getPathUri() . 'views/img/no.jpeg');
    }

    public function getLink()
    {
        $context = \Context::getContext();
        return $context->link->getModuleLink('chatgptcontentgenerator', 'blogpost', ['rewrite' => $this->link_rewrite]);
    }

    public function increaseViews()
    {
        $this->views = $this->views + 1;

        \Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'gptcontent_post SET views = views + 1 WHERE id_gptcontent_post = ' . $this->id . ' LIMIT 1');
    }

    public static function getPageLink($page_nb)
    {
        $context = \Context::getContext();
        return $context->link->getModuleLink('chatgptcontentgenerator', 'bloghome', ['p' => $page_nb]);
    }

    public static function getPosts($id_lang, $limit = 10, $search = null, $page = null, $active = true, $orderby = false, $orderway = false, $exclude = null, $featured = false, $id_shop = null, $filter = false, $selected = [], $or = false)
    {
        $context = \Context::getContext();

        $start = $limit * ($page == 0 ? 0 : $page - 1);

        $sql = new \DbQuery();
        $sql->select('SQL_CALC_FOUND_ROWS *');
        $sql->from('gptcontent_post', 'sbp');

        if ($id_lang) {
            $sql->innerJoin('gptcontent_post_lang', 'l', 'sbp.id_gptcontent_post = l.id_gptcontent_post AND l.id_lang = ' . (int) $id_lang);
        }

        if (!$id_shop) {
            $id_shop = $context->language->id_lang;
        }

        if (!is_null($search) && $id_lang) {
            $sql->where("l.content LIKE '%" . pSQL($search) . "%' OR l.title LIKE '%" . pSQL($search) . "%'");
        }

        if ($active) {
            $sql->where('sbp.active = 1');
        }

        if ($exclude) {
            $sql->where('sbp.id_gptcontent_post != ' . (int) $exclude);
        }

        if ($filter) {
            if ($or) {
                $sql->where('sbp.' . $filter . ' (' . join(',', $selected) . ') OR sbp.' . $or);
            } else {
                $sql->where('sbp.' . $filter . ' (' . join(',', $selected) . ')');
            }
        }

        if (!$orderby) {
            $orderby = 'sbp.date_add';
        }

        if (!$orderway) {
            $orderway = 'DESC';
        }

        $sql->orderBy($orderby . ' ' . $orderway);

        $sql->limit($limit, $start);

        $result = \Db::getInstance()->executeS($sql);

        $result = \ObjectModel::hydrateCollection(self::class, $result, $id_lang);
        $count = \Db::getInstance()->getValue('SELECT FOUND_ROWS()');

        return ['posts' => $result, 'count' => $count];
    }

    public static function getInstanceByRewrite($rewrite, $id_lang = false)
    {
        if ($id_lang === false) {
            $id_lang = \Context::getContext()->language->id_lang;
        }

        if (!$rewrite) {
            return new GptContentPost();
        }

        $sql = new \DbQuery();
        $sql->select('l.id_gptcontent_post ');
        $sql->from('gptcontent_post_lang', 'l');

        $sql->where('l.link_rewrite = \'' . $rewrite . '\' AND l.id_lang = ' . (int) $id_lang);

        $id = \Db::getInstance()->getValue($sql);

        if ($id) {
            $post = new GptContentPost((int) $id, $id_lang);
            return $post;
        }

        return new GptContentPost();
    }
}
