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

class TvcmsCategoryClass extends ObjectModel
{
    public $id;
    public $id_tvcmscategory;
    public $name;
    public $description;
    public $link_rewrite;
    public $category_img;
    public $category_group;
    public $category_type;
    public $title;
    public $meta_description;
    public $keyword;
    public $position;
    public $active;
    public static $definition = array(
        'table' => 'tvcmscategory',
        'primary' => 'id_tvcmscategory',
        'multilang' => true,
        'fields' => array(
            'name' =>           array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'link_rewrite' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'title' =>          array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'description' =>    array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'meta_description' =>array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),
            'keyword' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString','lang' => true),

            'category_img' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'category_type' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'category_group' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position' =>       array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'active' =>         array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );
    
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('tvcmscategory', array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }
    
    public static function categoryExists($id_category = null, $category_type = 'post')
    {
        if ($id_category == null || $id_category == 0) {
            return false;
        }
        $sql = 'SELECT xc.`id_tvcmscategory` FROM `'._DB_PREFIX_.'tvcmscategory` xc WHERE '
            .'xc.`category_type` = "'.($category_type ? $category_type : 'category')
            .'" AND xc.active = 1 AND xc.`id_tvcmscategory` = '.$id_category;
        $rslts = Db::getInstance()->getrow($sql);
            return (isset($rslts['id_tvcmscategory']) && !empty($rslts['id_tvcmscategory'])) ? true : false;
    }
    
    public function update($null_values = false)
    {
        if (isset($_FILES['category_img'])
            && isset($_FILES['category_img']['tmp_name'])
            && !empty($_FILES['category_img']['tmp_name'])) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmscategory` WHERE '
            .' `id_tvcmscategory` = '.Tools::getValue('id_tvcmscategory');
            $res = Db::getInstance()->executeS($sql);
            
            if (file_exists(TVCMSBLOG_IMG_DIR.$res['category_img'])) {
                unlink(TVCMSBLOG_IMG_DIR.$res['category_img']);
            }
            $this->category_img = TvcmsBlog::uploadMedia('category_img');
        } else {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmscategory` WHERE '
            .' `id_tvcmscategory` = '.Tools::getValue('id_tvcmscategory');
            

            $res = Db::getInstance()->executeS($sql);
            $this->category_img = $res[0]['category_img'];
        }
        if (!parent::update($null_values)) {
            return false;
        }
        return true;
    }
    
    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0) {
            $this->position = self::getTopPosition() + 1;
        }
        $this->category_img = TvcmsBlog::uploadMedia('category_img');
        if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this)) {
            return false;
        }
        return true;
    }
    
    public static function getTopPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'tvcmscategory`';
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }
    
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT `id_tvcmscategory`, `position`
            FROM `'._DB_PREFIX_.'tvcmscategory`
            ORDER BY `position` ASC'
        )) {
            return false;
        }
        if (!empty($res)) {
            foreach ($res as $tvcmscategory) {
                if ((int)$tvcmscategory['id_tvcmscategory'] == (int)$this->id) {
                    $moved_tvcmscategory = $tvcmscategory;
                }
            }
        }
        if (!isset($moved_tvcmscategory) || !isset($position)) {
            return false;
        }
        $queryx = ' UPDATE `'._DB_PREFIX_.'tvcmscategory`
        SET `position`= `position` '.($way ? '- 1' : '+ 1').'
        WHERE `position`
        '.($way
        ? '> '.(int)$moved_tvcmscategory['position'].' AND `position` <= '.(int)$position
        : '< '.(int)$moved_tvcmscategory['position'].' AND `position` >= '.(int)$position.'
        ');
        $queryy = ' UPDATE `'._DB_PREFIX_.'tvcmscategory`
        SET `position` = '.(int)$position.'
        WHERE `id_tvcmscategory` = '.(int)$moved_tvcmscategory['id_tvcmscategory'];
        return (Db::getInstance()->execute($queryx)
        && Db::getInstance()->execute($queryy));
    }
    
    public static function getCategoryPath($id_category = null, $category_type = 'category')
    {
        $meta_title = Configuration::get(TvcmsBlog::$tvcmsblogshortname."meta_title");
        if ($id_category == null) {
            return (isset($meta_title) ? $meta_title : "Blog");
        }
        $pipe = Configuration::get('PS_NAVIGATION_PIPE');
        if (empty($pipe)) {
            $pipe = '>';
        }
        $blog_url = TvcmsBlog::tvcmsBlogLink();
        $full_paths = '<a href="'.$blog_url.'" title="'.$meta_title.'" data-gg="">'.$meta_title
            .'</a><span class="navigation-pipe">'.$pipe.'</span>';
        $results = self::getTheTitle($id_category, $category_type);
        $str = (isset($results['name']) ? $results['name'] : $meta_title);
        return $full_paths.$str;
    }
    
    public static function getTheTitle($id_category = null, $category_type = 'category')
    {
        if ($id_category == null) {
            return false;
        }
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xc.`id_tvcmscategory`,xcl.`name`,xcl.`link_rewrite` FROM `'._DB_PREFIX_
            .'tvcmscategory` xc INNER JOIN `'._DB_PREFIX_.'tvcmscategory_lang` xcl ON (xc.`id_tvcmscategory` = '
            .'xcl.`id_tvcmscategory` AND xcl.`id_lang` = '.$id_lang.') INNER JOIN `'._DB_PREFIX_
            .'tvcmscategory_shop` xcs ON (xc.`id_tvcmscategory` = xcs.`id_tvcmscategory` AND xcs.`id_shop` = '
            .$id_shop.') ';
        $sql .= ' WHERE xc.`category_type` = "'.($category_type ? $category_type : 'category').'" AND '
            .'xc.`id_tvcmscategory` = '.$id_category;
        $rslts = Db::getInstance()->getrow($sql);
        return $rslts;
    }
    
    public static function getTheId($rewrite = null, $category_type = 'category')
    {
        if ($rewrite == null) {
            return false;
        }
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT xc.`id_tvcmscategory` FROM `'._DB_PREFIX_.'tvcmscategory` xc INNER JOIN `'._DB_PREFIX_
            .'tvcmscategory_lang` xcl ON (xc.`id_tvcmscategory` = xcl.`id_tvcmscategory` AND xcl.`id_lang` = '
            .$id_lang.') INNER JOIN `'._DB_PREFIX_.'tvcmscategory_shop` xcs ON (xc.`id_tvcmscategory` = '
            .'xcs.`id_tvcmscategory` AND xcs.`id_shop` = '.$id_shop.') ';
        $sql .= ' WHERE xc.`category_type` = "'.($category_type ? $category_type : 'category')
            .'" AND xcl.`link_rewrite` = "'.$rewrite.'" ';
        $rslts = Db::getInstance()->getrow($sql);
        return isset($rslts['id_tvcmscategory']) ? $rslts['id_tvcmscategory'] : null;
    }
    
    public static function getCategories($category_type = 'category', $category_group = null)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tvcmscategory` xc 
               INNER JOIN `'._DB_PREFIX_.'tvcmscategory_lang` xcl ON
                    (xc.`id_tvcmscategory` = xcl.`id_tvcmscategory` '
                    .'AND xcl.`id_lang` = '.$id_lang.')
               INNER JOIN `'._DB_PREFIX_.'tvcmscategory_shop` xcs ON
                    (xc.`id_tvcmscategory` = xcs.`id_tvcmscategory` '
                    .'AND xcs.`id_shop` = '.$id_shop.')
               ';
        $sql .= ' WHERE xc.`active` = 1 AND  category_type = "'.$category_type.'" ';
        if ($category_group != null) {
            $sql .= ' AND category_group = '.$category_group;
        }
        $sql .= ' ORDER BY xc.`position` ASC ';
        return Db::getInstance()->executeS($sql);
    }
    
    public static function serializeCategory($brief = true)
    {
        $results = array();
        if ($brief == true) {
            $results[0]['id'] = 0;
            $results[0]['name'] = 'Select Category';
        }
        $category = self::getCategories();
        if (isset($category) && !empty($category)) {
            $i = (int)$brief;
            foreach ($category as $categoryvalue) {
                $results[$i]['id'] = $categoryvalue['id_tvcmscategory'];
                $results[$i]['name'] = $categoryvalue['name'];
                $i++;
            }
        }
        return $results;
    }
}
