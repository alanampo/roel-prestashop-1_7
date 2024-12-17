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

class TvcmsPostsClass extends ObjectModel
{
    public $id;
    public $id_tvcmsposts;
    public $post_author;
    public $post_date;
    public $post_modified;
    public $comment_status;
    public $post_password;
    public $post_parent;
    public $post_type;
    public $post_format;
    public $category;
    public $comment_count;
    public $post_title;
    public $post_excerpt;
    public $post_content;
    public $post_img;
    public $link_rewrite;
    public $position;
    public $active;
    public $video;
    public $audio;
    public $gallery;
    public $meta_title;
    public $meta_description;
    public $meta_keyword;
    public $related_products;
    public static $definition = array(
        'table' => 'tvcmsposts',
        'primary' => 'id_tvcmsposts',
        'multilang' => true,
        'fields' => array(
            'post_title' =>         array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'meta_title' =>         array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'post_excerpt' =>       array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'meta_description' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'meta_keyword' =>       array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'post_content' =>       array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml','lang' => true),
            'link_rewrite' =>       array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'related_products' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'video' =>              array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'audio' =>              array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'gallery' =>            array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'post_password' =>      array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'post_type' =>          array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'post_format' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'category' =>   array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'post_date' =>          array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'post_modified' =>      array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'post_img' =>           array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'post_author' =>        array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'post_parent' =>        array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'comment_status' =>     array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'comment_count' =>      array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position' =>           array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );
    
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('tvcmsposts', array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }
    
    public function update($null_values = false)
    {
        if (isset($_FILES['post_img'])
            && isset($_FILES['post_img']['tmp_name'])
            && !empty($_FILES['post_img']['tmp_name'])) {
            $tmp = Tools::getValue('id_tvcmsposts');
            $res = $this->getSinglePost($tmp);
            $categories = TvcmsImageTypeClass::getAllImageTypes();
            if (file_exists(TVCMSBLOG_IMG_DIR.$res['post_img'])) {
                unlink(TVCMSBLOG_IMG_DIR.$res['post_img']);
            }

            foreach ($categories as $category) {
                if (file_exists(TVCMSBLOG_IMG_DIR.$category['name'].'-'.$res['post_img'])) {
                    unlink(TVCMSBLOG_IMG_DIR.$category['name'].'-'.$res['post_img']);
                }
            }

            $this->post_img = TvcmsBlog::uploadMedia('post_img');
        } else {
            $id_tvcmsposts = Tools::getValue('id_tvcmsposts');
            $img = $this->getSinglePost($id_tvcmsposts);
            $this->post_img = $img['post_img'];
        }
        $tvaudio_temp = Tools::getValue('audio_temp');
        if ($tvaudio_temp
            && !empty($tvaudio_temp)
            && is_array($tvaudio_temp)) {
            $this->audio = @implode(",", $tvaudio_temp);
        }
        $tvvideo_temp = Tools::getValue('video_temp');
        if ($tvvideo_temp
            && !empty($tvvideo_temp)
            && is_array($tvvideo_temp)) {
            $this->video = @implode(",", $tvvideo_temp);
        }
        $this->galleryUploadsUpdated();
        $tags = Tools::getValue("meta_tag");
        $tags_ids = self::getCategoryTypeIds($tags);
        self::tagPostInsert($this->id, $tags_ids);
        $this->post_modified = date("Y-m-d H:i:s");
        if (!parent::update($null_values)) {
            return false;
        }
        return true;
    }
    
    public function add($autodate = true, $null_values = false)
    {
        if (isset($_FILES['post_img'])
            && isset($_FILES['post_img']['tmp_name'])
            && !empty($_FILES['post_img']['tmp_name'])) {
            $this->post_img = TvcmsBlog::uploadMedia('post_img');
        }
        
        $gallery_temp = TvcmsBlog::bulkuploadMedia("gallery_temp");
        if (isset($gallery_temp) && !empty($gallery_temp) && is_array($gallery_temp)) {
            $this->gallery = @implode(",", $gallery_temp);
        }
        $tvaudio_temp = Tools::getValue('audio_temp');
        if ($tvaudio_temp
            && !empty($tvaudio_temp)
            && is_array($tvaudio_temp)) {
            $this->audio = @implode(",", $tvaudio_temp);
        }
        $tvvideo_temp = Tools::getValue('video_temp');
        if ($tvvideo_temp
            && !empty($tvvideo_temp)
            && is_array($tvvideo_temp)) {
            $this->video = @implode(",", $tvvideo_temp);
        }
        $this->post_author = (int)Context::getContext()->employee->id;
        $this->post_date = date("Y-m-d H:i:s");
        $this->post_modified = date("Y-m-d H:i:s");
        if ($this->position <= 0) {
            $this->position = self::getTopPosition() + 1;
        }
        if (($this->post_img) && !empty($this->post_img)) {
            $this->post_img = $this->post_img;
        } else {
            $this->post_img = TvcmsBlog::uploadMedia('post_img');
        }
        $tags = Tools::getValue("meta_tag");
        $tags_ids = self::getCategoryTypeIds($tags);
        if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this)) {
            return false;
        } else {
            self::tagPostInsert($this->id, $tags_ids);
            return true;
        }
    }
    
    public function galleryUploadsUpdated()
    {

        $main_update_gallery = '';
        $gallery_temp = TvcmsBlog::bulkuploadMedia("gallery_temp");
        $tvgallery_temp_delete = Tools::getValue("gallery_temp_delete");
        if ($tvgallery_temp_delete && !empty($tvgallery_temp_delete)) {
            $gallery_temp_delete = @explode(",", $tvgallery_temp_delete);
        }


        if (isset($this->gallery) && !empty($this->gallery)) {
            $thisgallery = @explode(",", $this->gallery);
        } else {
            $thisgallery = array();
        }

        $categories = TvcmsImageTypeClass::getAllImageTypes();
        if (isset($gallery_temp_delete)) {
            foreach ($gallery_temp_delete as $img) {
                if (file_exists(TVCMSBLOG_IMG_DIR.$img)) {
                    unlink(TVCMSBLOG_IMG_DIR.$img);
                }
                foreach ($categories as $category) {
                    if (file_exists(TVCMSBLOG_IMG_DIR.$category['name'].'-'.$img)) {
                        unlink(TVCMSBLOG_IMG_DIR.$category['name'].'-'.$img);
                    }
                }
            }
        }

        $org = array();
        if (isset($thisgallery)
            && !empty($thisgallery)
            && isset($gallery_temp_delete)
            && !empty($gallery_temp_delete)) {
            foreach ($thisgallery as $galvalue) {
                if (!in_array($galvalue, $gallery_temp_delete)) {
                    $org[] = $galvalue;
                }
            }
            if (isset($org) && !empty($org) && isset($gallery_temp) && !empty($gallery_temp)) {
                $main_update_gallery = @array_merge($gallery_temp, $org);
            } else {
                if (isset($org) && !empty($org)) {
                    $main_update_gallery = $org;
                }
                if (isset($gallery_temp) && !empty($gallery_temp)) {
                    $main_update_gallery = $gallery_temp;
                }
            }
        } else {
            if (isset($thisgallery) && !empty($thisgallery) && isset($gallery_temp) && !empty($gallery_temp)) {
                $main_update_gallery = @array_merge($gallery_temp, $thisgallery);
            } elseif (isset($thisgallery) && !empty($thisgallery)) {
                $main_update_gallery = $thisgallery;
            } elseif (isset($gallery_temp) && !empty($gallery_temp)) {
                $main_update_gallery = $gallery_temp;
            }
        }
        if (isset($main_update_gallery) && !empty($main_update_gallery) && is_array($main_update_gallery)) {
            // echo "Welcome Here";
            $this->gallery = @implode(",", $main_update_gallery);
        }
    }
    
    public static function getsinglepath($id_post = null, $post_type = 'post')
    {
        if ($id_post == null) {
            return false;
        }
        $pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $posts = self::getTheTitle($id_post, $post_type);
        $category = $posts['category'] ? $posts['category'] : null;
        $categories = self::getTheCategory($category);
        $title = $categories['name'] ? $categories['name'] : "";
        $name = $posts['post_title'];
        $params = array();
        $params['id'] = $categories['id_tvcmscategory'] ? $categories['id_tvcmscategory'] : 0;
        $params['rewrite'] = $categories['link_rewrite'] ? $categories['link_rewrite'] : '';
        $params['page_type'] = 'category';
        $params['subpage_type'] = $post_type ?  $post_type : 'post';
        $link = TvcmsBlog::tvcmsBlogCategoryLink($params);
        $meta_title = Configuration::get(TvcmsBlog::$tvcmsblogshortname."meta_title");
        $meta_title = (isset($meta_title) ? $meta_title : "Blog");
        $blog_url = TvcmsBlog::tvcmsBlogLink();
        $full_paths = '<a href="'.$blog_url.'" title="'.$meta_title.'" data-gg="">'.$meta_title
            .'</a><span class="navigation-pipe">'.$pipe.'</span>';

        $str = '<a href="'.$link.'" title="'.$title.'" data-gg="">'.$title.'</a><span class="navigation-pipe">'
            .$pipe.'</span>'.$name;
        return $full_paths.$str;
    }
    
    public static function getTopPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'tvcmsposts`';
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }
    
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT `id_tvcmsposts`, `position`
            FROM `'._DB_PREFIX_.'tvcmsposts`
            ORDER BY `position` ASC'
        )) {
            return false;
        }
        if (!empty($res)) {
            foreach ($res as $tvcmsposts) {
                if ((int)$tvcmsposts['id_tvcmsposts'] == (int)$this->id) {
                    $moved_tvcmsposts = $tvcmsposts;
                }
            }
        }
        if (!isset($moved_tvcmsposts) || !isset($position)) {
            return false;
        }
        $queryx = ' UPDATE `'._DB_PREFIX_.'tvcmsposts`
        SET `position`= `position` '.($way ? '- 1' : '+ 1').'
        WHERE `position`
        '.($way
        ? '> '.(int)$moved_tvcmsposts['position'].' AND `position` <= '.(int)$position
        : '< '.(int)$moved_tvcmsposts['position'].' AND `position` >= '.(int)$position.'
        ');
        $queryy = ' UPDATE `'._DB_PREFIX_.'tvcmsposts`
        SET `position` = '.(int)$position.'
        WHERE `id_tvcmsposts` = '.(int)$moved_tvcmsposts['id_tvcmsposts'];
        return (Db::getInstance()->execute($queryx)
        && Db::getInstance()->execute($queryy));
    }
    
    public static function getCategoryTypeIds($values = null)
    {
        if ($values == null) {
            return false;
        }
        $results = array();
        if (isset($values) && !empty($values)) {
            $values = explode(",", $values);
            if (is_array($values)) {
                foreach ($values as $val) {
                    $results[] = self::tagInsert($val);
                }
                return $results;
            }
        } else {
            return false;
        }
    }
    
    public static function tagPostInsert($id_post = null, $category_ids = null, $tag = 'tag')
    {
        if ($id_post == null || $category_ids == null) {
            return false;
        } else {
            $queryval = '';
            
            self::deleteTagPost($id_post);
            if (isset($category_ids) && !empty($category_ids)) {
                foreach ($category_ids as $id_category) {
                    $queryval .= '('.(int)$id_post.','.(int)$id_category.',"'.$tag.'"),';
                }
                $queryval = rtrim($queryval, ',');
                if (Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_
                        .'tvcms_category_post`(`id_post`, `id_category`,`type`) VALUES '.$queryval)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    
    public static function deleteTagPost($id_post = null, $tag = 'tag')
    {
        if ($id_post == null) {
            return false;
        }
        $tmp = Tools::getValue('id_tvcmsposts');
        $res = self::getSinglePost($tmp);

        $categories = TvcmsImageTypeClass::getAllImageTypes();
        if (file_exists(TVCMSBLOG_IMG_DIR.$res['post_img'])) {
            unlink(TVCMSBLOG_IMG_DIR.$res['post_img']);
        }

        foreach ($categories as $category) {
            if (file_exists(TVCMSBLOG_IMG_DIR.$category['name'].'-'.$res['post_img'])) {
                unlink(TVCMSBLOG_IMG_DIR.$category['name'].'-'.$res['post_img']);
            }
        }

        if (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tvcms_category_post WHERE id_post = '
                .$id_post.' AND type = "'.$tag.'"')) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getTheTitle($id_post = null, $post_type = 'post')
    {
        if ($id_post == null) {
            return false;
        }
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xc.`id_tvcmsposts`,xc.`category`,xcl.`post_title`,xcl.`link_rewrite` FROM `'
            ._DB_PREFIX_.'tvcmsposts` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON (xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`post_type` = "'.($post_type?$post_type:'post').'" AND xc.`id_tvcmsposts` = '.$id_post;
        $rslts = Db::getInstance()->getrow($sql);
        return $rslts;
    }
    
    public static function getTheId($rewrite = null, $post_type = 'post')
    {
        if ($rewrite == null) {
            return false;
        }
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xc.`id_tvcmsposts` FROM `'._DB_PREFIX_.'tvcmsposts` xc INNER JOIN `'._DB_PREFIX_
            .'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` AND xcl.`id_lang` = '
            .$id_lang.') INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON '
            .'(xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.') ';
        $sql .= ' WHERE xc.`post_type` = "'.($post_type ? $post_type : 'post').'" AND xcl.`link_rewrite` = "'
            .$rewrite.'" ';
        $rslts = Db::getInstance()->getrow($sql);
        return isset($rslts['id_tvcmsposts']) ? $rslts['id_tvcmsposts'] : null;
    }
    
    public static function postExists($id_post = null, $post_type = 'post')
    {
        if ($id_post == null || $id_post == 0) {
            return false;
        }
        $sql = 'SELECT xc.`id_tvcmsposts` FROM `'._DB_PREFIX_.'tvcmsposts` xc WHERE xc.`post_type` = "'
            .($post_type ? $post_type : 'post').'" AND xc.active = 1 AND xc.`id_tvcmsposts` = '.$id_post;
        $rslts = Db::getInstance()->getrow($sql);
        
        $tmp = $rslts['id_tvcmsposts'];
        if (isset($tmp) && !empty($tmp)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getTheRewrite($id = null, $post_type = 'post')
    {
        if ($id == null) {
            return false;
        }
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xcl.`link_rewrite` FROM `'._DB_PREFIX_.'tvcmsposts` xc INNER JOIN `'._DB_PREFIX_
            .'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` AND xcl.`id_lang` = '
            .$id_lang.') INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON '
            .'(xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.') ';
        $sql .= ' WHERE xc.`post_type` = "'.($post_type ? $post_type : 'post').'" AND xc.`id_tvcmsposts` = "'.$id.'" ';
        $rslts = Db::getInstance()->getrow($sql);
        return isset($rslts['link_rewrite']) ? $rslts['link_rewrite'] : null;
    }
    
    public static function getTheCategory($id_category = null, $category_type = 'category')
    {
        if ($id_category == null) {
            return false;
        }
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xc.`id_tvcmscategory`,xcl.`name`,xcl.`link_rewrite` FROM `'._DB_PREFIX_.'tvcmscategory` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_lang` xcl ON (xc.`id_tvcmscategory` = xcl.`id_tvcmscategory` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_shop` xcs ON (xc.`id_tvcmscategory` = xcs.`id_tvcmscategory` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`category_type` = "'.($category_type ? $category_type : 'category').'" AND'
            .' xc.`id_tvcmscategory` = '.$id_category;
        $rslts = Db::getInstance()->getrow($sql);
        return $rslts;
    }
    
    public static function tagInsert($tag = null)
    {
        if ($tag == null) {
            return false;
        }
        $Languages = Language::getLanguages();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xc.`id_tvcmscategory` FROM `'._DB_PREFIX_.'tvcmscategory` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_lang` xcl ON (xc.`id_tvcmscategory` = xcl.`id_tvcmscategory` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_shop` xcs ON (xc.`id_tvcmscategory` = xcs.`id_tvcmscategory` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`category_type` = "tag" AND xcl.`name` = "'.$tag.'"';
        $rslts = Db::getInstance()->getrow($sql);
        if (isset($rslts) && !empty($rslts)) {
            return $rslts['id_tvcmscategory'];
        } else {
            $tagobj = new TvcmsCategoryClass();
            foreach ($Languages as $lang) {
                $tagobj->name[$lang['id_lang']] =   $tag;
                $tagobj->link_rewrite[$lang['id_lang']] =   Tools::str2url($tag);
                $tagobj->title[$lang['id_lang']]    =   $tag;
                $tagobj->description[$lang['id_lang']]  =   $tag;
                $tagobj->meta_description[$lang['id_lang']] =   $tag;
                $tagobj->keyword[$lang['id_lang']]  =   $tag;
            }
            $tagobj->category_type  =   'tag';
            $tagobj->category_group =   0;
            $tagobj->position   =   0;
            $tagobj->active =   1;
            if ($tagobj->add()) {
                return $tagobj->id;
            }
            return false;
        }
    }
    
    public static function getPostTags($id_post = null, $tag = 'tag')
    {
        if ($id_post == null) {
            return false;
        }
        $results = '';
        $id_lang = (int)Context::getContext()->language->id; // it use in id_lang from id_shop
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xcl.`name` FROM `'._DB_PREFIX_.'tvcms_category_post` xcp 
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory` xc ON (xcp.`id_category` = xc.`id_tvcmscategory` '
            .'AND xc.`category_type` = "'.$tag.'")
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_lang` xcl ON (xcp.`id_category` = xcl.`id_tvcmscategory` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_shop` xcs ON (xcp.`id_category` = xcs.`id_tvcmscategory` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xcp.`id_post` = '.$id_post.' AND xcp.`type` = "'.$tag.'"';
        $rslts = Db::getInstance()->executeS($sql);
        if (isset($rslts) && !empty($rslts)) {
            $countrslts = count($rslts);
            $i = 1;
            foreach ($rslts as $rslt) {
                if ($i == $countrslts) {
                    $results .= $rslt['name'];
                } else {
                    $results .= $rslt['name'].',';
                }
                $i++;
            }
        }
        return $results;
    }
    
    public static function getPostTagsResults($id_post = null, $tag = 'tag')
    {
        if ($id_post == null) {
            return false;
        }
        $results = array();
        $id_lang = (int)Context::getContext()->language->id; // it use in id_lang from id_shop
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xcp.`id_category`,xcl.`name`,xcl.`link_rewrite` FROM `'._DB_PREFIX_.'tvcms_category_post` xcp 
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory` xc ON (xcp.`id_category` = xc.`id_tvcmscategory` AND '
            .'xc.`category_type` = "'.$tag.'")
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_lang` xcl ON (xcp.`id_category` = xcl.`id_tvcmscategory` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_shop` xcs ON (xcp.`id_category` = xcs.`id_tvcmscategory` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xcp.`id_post` = '.$id_post.' AND xcp.`type` = "'.$tag.'"';
        $rslts = Db::getInstance()->executeS($sql);
        if (isset($rslts) && !empty($rslts)) {
            $i = 0;
            foreach ($rslts as $rslt) {
                $results[$i]['name'] = $rslt['name'];
                if ($tag == 'tag') {
                    $results[$i]['id_tag'] = $rslt['id_category'];
                    $results[$i]['link'] = TvcmsBlog::tvcmsBlogTagLink(array(
                        'id'=>$rslt['id_category'],
                        'rewrite'=>$rslt['link_rewrite'],
                        'page_type'=>'tag',
                        'subpage_type'=>'post'
                    ));
                } elseif ($tag == 'category') {
                    $results[$i]['id_category'] = $rslt['id_category'];
                    $results[$i]['link'] = TvcmsBlog::tvcmsBlogCategoryLink(array(
                        'id'=>$rslt['id_category'],
                        'rewrite'=>$rslt['link_rewrite'],
                        'page_type'=>'category',
                        'subpage_type'=>'post'
                    ));
                }
                $i++;
            }
        }
        return $results;
    }
    
    public static function getBlogTags($count = 10, $tag = 'tag')
    {
        $results = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xc.`id_tvcmscategory`,xcl.`name`,xcl.`link_rewrite` FROM `'._DB_PREFIX_.'tvcmscategory` xc
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_lang` xcl ON (xc.`id_tvcmscategory` = xcl.`id_tvcmscategory` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmscategory_shop` xcs ON (xc.`id_tvcmscategory` = xcs.`id_tvcmscategory` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`category_type` = "'.$tag.'" ';
        $sql .= ' ORDER BY xc.`id_tvcmscategory` DESC ';
        $sql .= ' LIMIT '.(int)$count;
        $rslts = Db::getInstance()->executeS($sql);
        if (isset($rslts) && !empty($rslts)) {
            $i = 0;
            foreach ($rslts as $rslt) {
                $results[$i]['name'] = $rslt['name'];
                if ($tag == 'tag') {
                    $results[$i]['id_tag'] = $rslt['id_tvcmscategory'];
                    $results[$i]['link'] = TvcmsBlog::tvcmsBlogTagLink(array(
                        'id'=>$rslt['id_tvcmscategory'],
                        'rewrite'=>$rslt['link_rewrite'],
                        'page_type'=>'tag',
                        'subpage_type'=>'post'
                    ));
                } elseif ($tag == 'category') {
                    $results[$i]['id_category'] = $rslt['id_tvcmscategory'];
                    $results[$i]['link'] = TvcmsBlog::tvcmsBlogCategoryLink(array(
                        'id'=>$rslt['id_tvcmscategory'],
                        'rewrite'=>$rslt['link_rewrite'],
                        'page_type'=>'category',
                        'subpage_type'=>'post'
                    ));
                }
                $i++;
            }
        }
        return $results;
    }
    
    public static function getCategoryPostsCount($category = null, $post_type = 'post')
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT count(xc.`id_tvcmsposts`) as alltvcmsposts FROM `'._DB_PREFIX_.'tvcmsposts` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON (xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`active` = 1 ';
        if ((int)$category != 0) {
            $sql .= ' AND xc.category = '.$category;
        }
        if ($post_type != null) {
            $sql .= ' AND xc.post_type = "'.$post_type.'" ';
        }
        $sql .= ' ORDER BY xc.`position` DESC ';
        $queryexec = Db::getInstance()->getrow($sql);
        return (int)$queryexec['alltvcmsposts'];
    }
    
    public static function getCategoryPosts(
        $category = null,
        $p = null,
        $n = null,
        $post_type = 'post',
        $order_by = 'DESC'
    ) {
        if ($p == null || $p < 1) {
            $p = 1;
        }
        if ($n == null) {
            $n = (int)Configuration::get(TvcmsBlog::$tvcmsblogshortname."post_per_page");
        }
        $results = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $GetAllImageTypes = TvcmsImageTypeClass::getAllImageTypes();
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsposts` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON (xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`active` = 1 ';
        if ((int)$category != 0) {
            $sql .= ' AND xc.category = '.$category;
        }
        if ($post_type != null) {
            $sql .= ' AND xc.post_type = "'.$post_type.'" ';
        }
        $sql .= ' ORDER BY xc.`position`  '.$order_by;
        $sql .= ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
        $queryexec = Db::getInstance()->executeS($sql);
        if (isset($queryexec) && !empty($queryexec)) {
            $i = 0;
            foreach ($queryexec as $qlvalue) {
                if (isset($qlvalue) && !empty($qlvalue)) {
                    foreach ($qlvalue as $qkey => $qvalue) {
                        $results[$i][$qkey] = $qvalue;
                        // start Image
                        if ($qkey == 'post_img') {
                            if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                foreach ($GetAllImageTypes as $imagetype) {
                                    $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                        .$imagetype['name'].'-'.$qvalue;
                                    if (!self::imageExists($imagetype['name'].'-'.$qvalue)) {
                                        $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                            .'noimage.jpg';
                                    }
                                }
                                if (!self::imageExists($qvalue)) {
                                    $results[$i]['post_img'] =  'noimage.jpg';
                                }
                            }
                        }
                        // end Image
                        if ($qkey == 'post_author') {
                            $post_author_arr = new Employee((int)$qvalue);
                            $results[$i]['post_author_arr']['lastname'] = $post_author_arr->lastname;
                            $results[$i]['post_author_arr']['firstname'] = $post_author_arr->firstname;
                        }
                        // $check_data = 'SELECT `id_tvcmsposts` FROM `'._DB_PREFIX_.'tvcmsposts_view` where `id_tvcmsposts` = '.$qlvalue['id_tvcmsposts'].'';
                        //     $ansaa = Db::getInstance()->executeS($check_data);

                        // if (!empty($ansaa)) {
                        //         $blogview = count($ansaa);
                        // } else {
                        //        $blogview = 0;
                        // }
                        //$results[$i]['blog_view']['view'] = $blogview;
                        $results[$i]['link'] = TvcmsBlog::tvcmsBlogPostLink(array(
                            'id'=>$qlvalue['id_tvcmsposts'],
                            'rewrite'=>$qlvalue['link_rewrite'],
                            'page_type'=>$post_type
                        ));
                        $results[$i]['post_tags'] = self::getPostTagsResults($qlvalue['id_tvcmsposts'], "tag");
                        if (isset($qlvalue['audio']) && !empty($qlvalue['audio'])) {
                            $results[$i]['audio_lists'] = @explode(",", $qlvalue['audio']);
                        }
                        if (isset($qlvalue['video']) && !empty($qlvalue['video'])) {
                            $results[$i]['video_lists'] = @explode(",", $qlvalue['video']);
                        }
                        if (isset($qlvalue['gallery']) && !empty($qlvalue['gallery'])) {
                            $gallery_lists = @explode(",", $qlvalue['gallery']);
                            if (isset($gallery_lists) && !empty($gallery_lists)) {
                                $ij = 0;
                                foreach ($gallery_lists as $gall) {
                                    $results[$i]['gallery_lists'][$ij]['main'] = TVCMSBLOG_IMG_URI.$gall;
                                    if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                        foreach ($GetAllImageTypes as $imagetype) {
                                            $results[$i]['gallery_lists'][$ij][$imagetype['name']] = TVCMSBLOG_IMG_URI
                                                .$imagetype['name'].'-'.$gall;
                                            if (!self::imageExists($imagetype['name'].'-'.$gall)) {
                                                $tmp = TVCMSBLOG_IMG_URI.'noimage.jpg';
                                                $results[$i]['gallery_lists'][$ij][$imagetype['name']] = $tmp;
                                            }
                                        }
                                    }
                                    $ij++;
                                }
                            }
                        }
                        if ($qkey == 'category') {
                            $category_arr = new TvcmsCategoryClass((int)$qvalue);
                            $results[$i]['category_arr']['id'] = @$category_arr->id;
                            $results[$i]['category_arr']['name'] = @$category_arr->name[$id_lang];
                            $results[$i]['category_arr']['link_rewrite'] = @$category_arr->link_rewrite[$id_lang];
                            $results[$i]['category_arr']['title'] = @$category_arr->title[$id_lang];
                            $results[$i]['category_arr']['link'] = TvcmsBlog::tvcmsBlogCategoryLink(array(
                                'id'=>$category_arr->id,
                                'rewrite'=>$category_arr->link_rewrite[$id_lang],
                                'page_type'=>'category',
                                'subpage_type'=>$post_type
                            ));
                        }

                        $post_id = $qlvalue['id_tvcmsposts'];
                        if (isset($post_id) && !empty($post_id)) {
                            $tvcmsblog_total_commets = TvcmsCommentClass::getCountComments($post_id);
                            $results[$i]['total_comments'] = $tvcmsblog_total_commets;
                        }
                    }
                    $i++;
                }
            }
        }
        return $results;
    }
    
    public static function getPopularPosts($count = 4, $post_type = 'post', $order_by = 'DESC')
    {
        $results = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $GetAllImageTypes = TvcmsImageTypeClass::getAllImageTypes();
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsposts` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON (xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`active` = 1 ';
        if ($post_type != null) {
            $sql .= ' AND xc.post_type = "'.$post_type.'" ';
        }
        $sql .= ' ORDER BY xc.`comment_count` '.$order_by;
        $sql .= ' LIMIT '.(int)$count;
        $queryexec = Db::getInstance()->executeS($sql);
        if (isset($queryexec) && !empty($queryexec)) {
            $i = 0;
            foreach ($queryexec as $qlvalue) {
                if (isset($qlvalue) && !empty($qlvalue)) {
                    foreach ($qlvalue as $qkey => $qvalue) {
                        $results[$i][$qkey] = $qvalue;
                        // start Image
                        if ($qkey == 'post_img') {
                            if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                foreach ($GetAllImageTypes as $imagetype) {
                                    $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                        .$imagetype['name'].'-'.$qvalue;
                                    if (!self::imageExists($imagetype['name'].'-'.$qvalue)) {
                                        $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                            .'noimage.jpg';
                                    }
                                }
                                if (!self::imageExists($qvalue)) {
                                    $results[$i]['post_img'] =  'noimage.jpg';
                                }
                            }
                        }
                        // end Image
                        if ($qkey == 'post_author') {
                            $post_author_arr = new Employee((int)$qvalue);
                            $results[$i]['post_author_arr']['lastname'] = $post_author_arr->lastname;
                            $results[$i]['post_author_arr']['firstname'] = $post_author_arr->firstname;
                        }
                        $results[$i]['link'] = TvcmsBlog::tvcmsBlogPostLink(array(
                            'id'=>$qlvalue['id_tvcmsposts'],
                            'rewrite'=>$qlvalue['link_rewrite'],
                            'page_type'=>$post_type
                        ));
                        $results[$i]['post_tags'] = self::getPostTagsResults($qlvalue['id_tvcmsposts'], "tag");
                        if (isset($qlvalue['audio']) && !empty($qlvalue['audio'])) {
                            $results[$i]['audio_lists'] = @explode(",", $qlvalue['audio']);
                        }
                        if (isset($qlvalue['video']) && !empty($qlvalue['video'])) {
                            $results[$i]['video_lists'] = @explode(",", $qlvalue['video']);
                        }
                        if (isset($qlvalue['gallery']) && !empty($qlvalue['gallery'])) {
                            $gallery_lists = @explode(",", $qlvalue['gallery']);
                            if (isset($gallery_lists) && !empty($gallery_lists)) {
                                $ij = 0;
                                foreach ($gallery_lists as $gall) {
                                    $results[$i]['gallery_lists'][$ij]['main'] = TVCMSBLOG_IMG_URI.$gall;
                                    if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                        foreach ($GetAllImageTypes as $imagetype) {
                                            $results[$i]['gallery_lists'][$ij][$imagetype['name']] = TVCMSBLOG_IMG_URI
                                                .$imagetype['name'].'-'.$gall;
                                            if (!self::imageExists($imagetype['name'].'-'.$gall)) {
                                                $tmp = TVCMSBLOG_IMG_URI.'noimage.jpg';
                                                $results[$i]['gallery_lists'][$ij][$imagetype['name']] = $tmp;
                                            }
                                        }
                                    }
                                    $ij++;
                                }
                            }
                        }
                        if ($qkey == 'category') {
                            $category_arr = new TvcmsCategoryClass((int)$qvalue);
                            $results[$i]['category_arr']['id'] = @$category_arr->id;
                            $results[$i]['category_arr']['name'] = @$category_arr->name[$id_lang];
                            $results[$i]['category_arr']['link_rewrite'] = @$category_arr->link_rewrite[$id_lang];
                            $results[$i]['category_arr']['title'] = @$category_arr->title[$id_lang];
                            $results[$i]['category_arr']['link'] = TvcmsBlog::tvcmsBlogCategoryLink(array(
                                'id'=>$category_arr->id,
                                'rewrite'=>$category_arr->link_rewrite[$id_lang],
                                'page_type'=>'category',
                                'subpage_type'=>$post_type
                            ));
                        }
                    }
                    $i++;
                }
            }
        }
        return $results;
    }
    
    public static function getRecentPosts($count = 4, $post_type = 'post', $order_by = 'DESC')
    {
        $results = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $GetAllImageTypes = TvcmsImageTypeClass::getAllImageTypes();
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsposts` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON (xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        ';
        $sql .= ' WHERE xc.`active` = 1 ';
        if ($post_type != null) {
            $sql .= ' AND xc.post_type = "'.$post_type.'" ';
        }
        $sql .= ' ORDER BY xc.`id_tvcmsposts` '.$order_by;
        $sql .= ' LIMIT '.(int)$count;
        $queryexec = Db::getInstance()->executeS($sql);
        if (isset($queryexec) && !empty($queryexec)) {
            $i = 0;
            foreach ($queryexec as $qlvalue) {
                if (isset($qlvalue) && !empty($qlvalue)) {
                    foreach ($qlvalue as $qkey => $qvalue) {
                        $results[$i][$qkey] = $qvalue;
                        // start Image
                        if ($qkey == 'post_img') {
                            if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                foreach ($GetAllImageTypes as $imagetype) {
                                    $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                        .$imagetype['name'].'-'.$qvalue;
                                    if (!self::imageExists($imagetype['name'].'-'.$qvalue)) {
                                        $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                            .'noimage.jpg';
                                    }
                                }
                                if (!self::imageExists($qvalue)) {
                                    $results[$i]['post_img'] =  'noimage.jpg';
                                }
                            }
                        }
                        // end Image
                        if ($qkey == 'post_author') {
                            $post_author_arr = new Employee((int)$qvalue);
                            $results[$i]['post_author_arr']['lastname'] = $post_author_arr->lastname;
                            $results[$i]['post_author_arr']['firstname'] = $post_author_arr->firstname;
                        }
                        $results[$i]['link'] = TvcmsBlog::tvcmsBlogPostLink(array(
                            'id'=>$qlvalue['id_tvcmsposts'],
                            'rewrite'=>$qlvalue['link_rewrite'],
                            'page_type'=>$post_type
                        ));
                        $results[$i]['post_tags'] = self::getPostTagsResults($qlvalue['id_tvcmsposts'], "tag");
                        if (isset($qlvalue['audio']) && !empty($qlvalue['audio'])) {
                            $results[$i]['audio_lists'] = @explode(",", $qlvalue['audio']);
                        }
                        if (isset($qlvalue['video']) && !empty($qlvalue['video'])) {
                            $results[$i]['video_lists'] = @explode(",", $qlvalue['video']);
                        }
                        if (isset($qlvalue['gallery']) && !empty($qlvalue['gallery'])) {
                            $gallery_lists = @explode(",", $qlvalue['gallery']);
                            if (isset($gallery_lists) && !empty($gallery_lists)) {
                                $ij = 0;
                                foreach ($gallery_lists as $gall) {
                                    $results[$i]['gallery_lists'][$ij]['main'] = TVCMSBLOG_IMG_URI.$gall;
                                    if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                        foreach ($GetAllImageTypes as $imagetype) {
                                            $results[$i]['gallery_lists'][$ij][$imagetype['name']] = TVCMSBLOG_IMG_URI
                                                .$imagetype['name'].'-'.$gall;
                                            if (!self::imageExists($imagetype['name'].'-'.$gall)) {
                                                $tmp = TVCMSBLOG_IMG_URI.'noimage.jpg';
                                                $results[$i]['gallery_lists'][$ij][$imagetype['name']] = $tmp;
                                            }
                                        }
                                    }
                                    $ij++;
                                }
                            }
                        }
                        if ($qkey == 'category') {
                            $category_arr = new TvcmsCategoryClass((int)$qvalue);
                            $results[$i]['category_arr']['id'] = @$category_arr->id;
                            $results[$i]['category_arr']['name'] = @$category_arr->name[$id_lang];
                            $results[$i]['category_arr']['link_rewrite'] = @$category_arr->link_rewrite[$id_lang];
                            $results[$i]['category_arr']['title'] = @$category_arr->title[$id_lang];
                            $results[$i]['category_arr']['link'] = TvcmsBlog::tvcmsBlogCategoryLink(array(
                                'id'=>$category_arr->id,
                                'rewrite'=>$category_arr->link_rewrite[$id_lang],
                                'page_type'=>'category',
                                'subpage_type'=>$post_type
                            ));
                        }
                    }
                    $i++;
                }
            }
        }
        return $results;
    }
    
    public static function imageExists($file = null)
    {
        if ($file == null) {
            return false;
        }
        $image = TVCMSBLOG_IMG_DIR.$file;
        if (file_exists($image)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function postCountUpdate($id = null)
    {
        if ($id == null || $id == 0) {
            return false;
        }
        $sql = 'UPDATE '._DB_PREFIX_.'tvcmsposts as xc SET xc.comment_count = (xc.comment_count+1) '
            .'where xc.id_tvcmsposts = '.$id;
        if (Db::getInstance()->execute($sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getSinglePost($id_post = null, $post_type = 'post')
    {
        if ($id_post == null) {
            return false;
        }
        $results = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $GetAllImageTypes = TvcmsImageTypeClass::getAllImageTypes();
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsposts` xc 
               INNER JOIN `'._DB_PREFIX_.'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` '
                    .'AND xcl.`id_lang` = '.$id_lang.')
               INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON (xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
                    .'AND xcs.`id_shop` = '.$id_shop.')
               ';
        $sql .= ' WHERE xc.`active` = 1 AND xc.post_type = "'.$post_type.'" AND xc.id_tvcmsposts = '.(int)$id_post;
        $queryexec = Db::getInstance()->getrow($sql);
        if (isset($queryexec) && !empty($queryexec)) {
            foreach ($queryexec as $qkey => $qvalue) {
                $results[$qkey] = $qvalue;
                // start Image
                if ($qkey == 'post_img') {
                    if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                        foreach ($GetAllImageTypes as $imagetype) {
                            $tmp = TVCMSBLOG_IMG_URI.$imagetype['name'].'-'.$qvalue;
                            $results['post_img_'.$imagetype['name']] = $tmp;
                            if (!self::imageExists($imagetype['name'].'-'.$qvalue)) {
                                $results['post_img_'.$imagetype['name']] =  TVCMSBLOG_IMG_URI
                                    .'noimage.jpg';
                            }
                        }
                        if (!self::imageExists($qvalue)) {
                            $results['post_img'] =  'noimage.jpg';
                        }
                    }
                }
                // end Image
                if ($qkey == 'post_author') {
                    $post_author_arr = new Employee((int)$qvalue);
                    $results['post_author_arr']['lastname'] = $post_author_arr->lastname;
                    $results['post_author_arr']['firstname'] = $post_author_arr->firstname;
                }
                if ($qkey == 'audio') {
                    $results['audio_lists'] = @explode(",", $qvalue);
                }
                if ($qkey == 'video') {
                    $results['video_lists'] = @explode(",", $qvalue);
                }
                if ($qkey == 'gallery') {
                    $gallery_lists = @explode(",", $qvalue);
                    if (isset($gallery_lists) && !empty($gallery_lists)) {
                        $ij = 0;
                        foreach ($gallery_lists as $gall) {
                            $results['gallery_lists'][$ij]['main'] = TVCMSBLOG_IMG_URI.$gall;
                            if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                foreach ($GetAllImageTypes as $imagetype) {
                                    $results['gallery_lists'][$ij][$imagetype['name']] = TVCMSBLOG_IMG_URI
                                        .$imagetype['name'].'-'.$gall;
                                    if (!self::imageExists($imagetype['name'].'-'.$gall)) {
                                        $tmp = TVCMSBLOG_IMG_URI.'noimage.jpg';
                                        $results['gallery_lists'][$ij][$imagetype['name']] = $tmp;
                                    }
                                }
                            }
                            $ij++;
                        }
                    }
                }
                $results['link'] = TvcmsBlog::tvcmsBlogPostLink(array(
                    'id'=>$queryexec['id_tvcmsposts'],
                    'rewrite'=>$queryexec['link_rewrite'],
                    'page_type'=>$post_type
                ));
                $results['post_tags'] = self::getPostTagsResults($queryexec['id_tvcmsposts'], "tag");
                if ($qkey == 'category') {
                    $category_arr = new TvcmsCategoryClass((int)$qvalue);
                    $results['category_arr']['id'] = @$category_arr->id;
                    $results['category_arr']['name'] = @$category_arr->name[$id_lang];
                    $results['category_arr']['title'] = @$category_arr->title[$id_lang];
                    $results['category_arr']['link_rewrite'] = @$category_arr->link_rewrite[$id_lang];
                    $results['category_arr']['link'] = TvcmsBlog::tvcmsBlogCategoryLink(array(
                        'id'=>$category_arr->id,
                        'rewrite'=>$category_arr->link_rewrite[$id_lang],
                        'subpage_type'=>$post_type,
                        'page_type'=>'category'
                    ));
                }
            }
        }
        return $results;
    }
    
    public static function getTagPosts($id_tag = null, $p = null, $n = null, $post_type = 'post', $order_by = 'DESC')
    {
        if ($id_tag == null || $id_tag == 0) {
            return false;
        }
        if ($p == null || $p < 1) {
            $p = 1;
        }
        if ($n == null) {
            $n = (int)Configuration::get(TvcmsBlog::$tvcmsblogshortname."post_per_page");
        }
        $results = array();
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $GetAllImageTypes = TvcmsImageTypeClass::getAllImageTypes();
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsposts` xc 
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_lang` xcl ON (xc.`id_tvcmsposts` = xcl.`id_tvcmsposts` '
            .'AND xcl.`id_lang` = '.$id_lang.')
        INNER JOIN `'._DB_PREFIX_.'tvcmsposts_shop` xcs ON (xc.`id_tvcmsposts` = xcs.`id_tvcmsposts` '
            .'AND xcs.`id_shop` = '.$id_shop.')
        INNER JOIN `'._DB_PREFIX_.'tvcms_category_post` xcp ON (xcp.`id_post` = xc.`id_tvcmsposts` '
            .'AND xcp.`id_category` = '.(int)$id_tag.' ) 
        ';
        $sql .= ' WHERE xc.`active` = 1 ';
        if ($post_type != null) {
            $sql .= ' AND xc.post_type = "'.$post_type.'" ';
        }
        $sql .= ' ORDER BY xc.`position`  '.$order_by;
        $sql .= ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
        $queryexec = Db::getInstance()->executeS($sql);
        if (isset($queryexec) && !empty($queryexec)) {
            $i = 0;
            foreach ($queryexec as $qlvalue) {
                if (isset($qlvalue) && !empty($qlvalue)) {
                    foreach ($qlvalue as $qkey => $qvalue) {
                        $results[$i][$qkey] = $qvalue;
                        // start Image
                        if ($qkey == 'post_img') {
                            if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                foreach ($GetAllImageTypes as $imagetype) {
                                    $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                        .$imagetype['name'].'-'.$qvalue;
                                    if (!self::imageExists($imagetype['name'].'-'.$qvalue)) {
                                        $results[$i]['post_img_'.$imagetype['name']] = TVCMSBLOG_IMG_URI
                                            .'noimage.jpg';
                                    }
                                }
                                if (!self::imageExists($qvalue)) {
                                    $results[$i]['post_img'] =  'noimage.jpg';
                                }
                            }
                        }
                        // end Image
                        if ($qkey == 'post_author') {
                            $post_author_arr = new Employee((int)$qvalue);
                            $results[$i]['post_author_arr']['lastname'] = $post_author_arr->lastname;
                            $results[$i]['post_author_arr']['firstname'] = $post_author_arr->firstname;
                        }
                        $results[$i]['link'] = TvcmsBlog::tvcmsBlogPostLink(array(
                            'id'=>$qlvalue['id_tvcmsposts'],
                            'rewrite'=>$qlvalue['link_rewrite'],
                            'page_type'=>$post_type
                        ));
                        $results[$i]['post_tags'] = self::getPostTagsResults($qlvalue['id_tvcmsposts'], "tag");
                        if (isset($qlvalue['audio']) && !empty($qlvalue['audio'])) {
                            $results[$i]['audio_lists'] = @explode(",", $qlvalue['audio']);
                        }
                        if (isset($qlvalue['video']) && !empty($qlvalue['video'])) {
                            $results[$i]['video_lists'] = @explode(",", $qlvalue['video']);
                        }
                        if (isset($qlvalue['gallery']) && !empty($qlvalue['gallery'])) {
                            $gallery_lists = @explode(",", $qlvalue['gallery']);
                            if (isset($gallery_lists) && !empty($gallery_lists)) {
                                $ij = 0;
                                foreach ($gallery_lists as $gall) {
                                    $results[$i]['gallery_lists'][$ij]['main'] = TVCMSBLOG_IMG_URI.$gall;
                                    if (isset($GetAllImageTypes) && !empty($GetAllImageTypes)) {
                                        foreach ($GetAllImageTypes as $imagetype) {
                                            $results[$i]['gallery_lists'][$ij][$imagetype['name']] = TVCMSBLOG_IMG_URI
                                                .$imagetype['name'].'-'.$gall;
                                            if (!self::imageExists($imagetype['name'].'-'.$gall)) {
                                                $tmp = TVCMSBLOG_IMG_URI.'noimage.jpg';
                                                $results[$i]['gallery_lists'][$ij][$imagetype['name']] = $tmp;
                                            }
                                        }
                                    }
                                    $ij++;
                                }
                            }
                        }
                        if ($qkey == 'category') {
                            $category_arr = new TvcmsCategoryClass((int)$qvalue);
                            $results[$i]['category_arr']['id'] = @$category_arr->id;
                            $results[$i]['category_arr']['name'] = @$category_arr->name[$id_lang];
                            $results[$i]['category_arr']['link_rewrite'] = @$category_arr->link_rewrite[$id_lang];
                            $results[$i]['category_arr']['title'] = @$category_arr->title[$id_lang];
                            $results[$i]['category_arr']['link'] = TvcmsBlog::tvcmsBlogCategoryLink(array(
                                'id'=>$category_arr->id,
                                'rewrite'=>$category_arr->link_rewrite[$id_lang],
                                'page_type'=>'category',
                                'subpage_type'=>$post_type
                            ));
                        }
                    }
                    $i++;
                }
            }
        }
        return $results;
    }
}
