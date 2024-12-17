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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once('classes/tvcmsbrandlist_status.class.php');
include_once('classes/tvcmsbrandlist_image_upload.class.php');

class TvcmsBrandList extends Module
{
    public $id_shop_group = '';
    public $id_shop = '';
    public function __construct()
    {
        $this->name = 'tvcmsbrandlist';
        $this->tab = 'front_office_features';
        $this->version = '4.0.0';
        $this->author = 'JHP Template';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('JHP Template - Brand List');
        $this->description = $this->l('Its Show Brand List on Front Side');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = '';

        $this->confirmUninstall = $this->l('Warning: all the data saved in your database will be deleted.'.
            ' Are you sure you want uninstall this module?');
        
        $this->id_shop_group = (int)Shop::getContextShopGroupID();
        $this->id_shop = (int)Context::getContext()->shop->id;
    }


    public function install()
    {
        $this->installTab();
        $this->createTable();
        
        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayHeader')
            // && $this->registerHook('displayHome');
            && $this->registerHook('displayWrapperBottom')
            && $this->registerHook('displayContentWrapperTop')
            && $this->registerHook('displayWrapperTop');
            // && $this->registerHook('displayFooterColumn');
            // && $this->registerHook('displayFooterBefore');
    }

    public function installTab()
    {
        $response = true;

        // First check for parent tab
        $parentTabID = Tab::getIdFromClassName('AdminJHPTemplate');

        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminJHPTemplate";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "JHPTemplate Extension";
            }
            $parentTab->id_parent = 0;
            $parentTab->module = $this->name;
            $response &= $parentTab->add();
        }
        
        // Check for parent tab2
        $parentTab_2ID = Tab::getIdFromClassName('AdminJHPTemplateModules');
        if ($parentTab_2ID) {
            $parentTab_2 = new Tab($parentTab_2ID);
        } else {
            $parentTab_2 = new Tab();
            $parentTab_2->active = 1;
            $parentTab_2->name = array();
            $parentTab_2->class_name = "AdminJHPTemplateModules";
            foreach (Language::getLanguages() as $lang) {
                $parentTab_2->name[$lang['id_lang']] = "JHPTemplate Configure";
            }
            $parentTab_2->id_parent = $parentTab->id;
            $parentTab_2->module = $this->name;
            $response &= $parentTab_2->add();
        }
        // Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Admin'.$this->name;
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Brand List";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function createDefaultData()
    {
        $this->reset();
        $num_of_data = 6;
        $this->createVariable();
        $this->createTable();
        $this->insertSmapleData($num_of_data);
    }

    public function createVariable()
    {
        $languages = Language::getLanguages();
        $result = array();
        foreach ($languages as $lang) {
            $result['TVCMSBRANDLIST_TITLE'][$lang['id_lang']] = 'OUR BRAND PARTNERS';
            $result['TVCMSBRANDLIST_SUB_DESCRIPTION'][$lang['id_lang']] = 'Shop By Brand';
            $result['TVCMSBRANDLIST_DESCRIPTION'][$lang['id_lang']] = 'Description';
            $result['TVCMSBRANDLIST_IMG'][$lang['id_lang']] = 'demo_main_img.jpg';
        }

        Configuration::updateValue('TVCMSBRANDLIST_TITLE', $result['TVCMSBRANDLIST_TITLE']);
        Configuration::updateValue('TVCMSBRANDLIST_SUB_DESCRIPTION', $result['TVCMSBRANDLIST_SUB_DESCRIPTION']);
        Configuration::updateValue('TVCMSBRANDLIST_DESCRIPTION', $result['TVCMSBRANDLIST_DESCRIPTION']);
        Configuration::updateValue('TVCMSBRANDLIST_IMG', $result['TVCMSBRANDLIST_IMG']);
    }


    public function createTable()
    {
        $create_table = array();
        $create_table[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tvcmsbrandlist` (
                        `id_tvcmsbrandlist` int(11) AUTO_INCREMENT PRIMARY KEY,
                        `position` int(11),
                        `id_shop_group` int(11),
                        `id_shop` int(11),
                        `image` VARCHAR(100),
                        `link` VARCHAR(255),
                        `status` varchar(3)
                    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $create_table[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tvcmsbrandlist_lang` (
                        `id_tvcmsbrandlist_lang` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `id_tvcmsbrandlist` INT NOT NULL,
                        `id_shop_group` int(11),
                        `id_shop` int(11),
                        `id_lang` INT NOT NULL,
                        `title` VARCHAR(255),
                        `short_description` TEXT,
                        `description` TEXT
                    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        foreach ($create_table as $table) {
            Db::getInstance()->execute($table);
        }
    }

    public function insertSmapleData($num_of_data)
    {
        $demo_data = array();
        $languages = Language::getLanguages();

        for ($i = 1; $i<=$num_of_data; $i++) {
            $demo_data[] =  'INSERT INTO 
                            `'._DB_PREFIX_.'tvcmsbrandlist`
                        SET 
                            `id_tvcmsbrandlist` = '.$i.',
                            `position` = '.$i.',
                            `id_shop_group` = '.$this->id_shop_group.',
                            `id_shop` = '.$this->id_shop.',
                            `image` = \'demo_img_'.$i.'.png\',
                            `link` = \'#\',
                            `status` = \'1\';';
            foreach ($languages as $lang) {
                $demo_data[] = 'INSERT INTO
                                `'._DB_PREFIX_.'tvcmsbrandlist_lang`
                            SET
                                `id_tvcmsbrandlist_lang` = NULL,
                                `id_tvcmsbrandlist` = '.$i.',
                                `id_shop_group` = '.$this->id_shop_group.',
                                `id_shop` = '.$this->id_shop.',
                                `id_lang` = '.$lang['id_lang'].',
                                `title` = \'test '.$i.'\',
                                `short_description` = \'test '.$i.'\',
                                `description` = \'test '.$i.'\';';
            }
        }
        foreach ($demo_data as $data) {
            Db::getInstance()->execute($data);
        }
    }

    public function maxId()
    {
        $select_data = 'SELECT MAX(id_tvcmsbrandlist) as max_id FROM `'._DB_PREFIX_.'tvcmsbrandlist`';
        $ans = Db::getInstance()->executeS($select_data);
        return $ans[0]['max_id'];
    }

    public function selectAllLangIdById($id_tvcmsbrandlist)
    {
        $select_data = 'SELECT 
                            id_lang 
                        FROM 
                            `'._DB_PREFIX_.'tvcmsbrandlist_lang` 
                        WHERE 
                            id_tvcmsbrandlist = '.$id_tvcmsbrandlist;
        $ans = Db::getInstance()->executeS($select_data);
        $return = array();
        foreach ($ans as $a) {
            $return[] = $a['id_lang'];
        }
        return $return;
    }

    public function insertData($data)
    {
        $result = array();
        $insert_data = array();
        if ($data['id']) {
            $id = $data['id'];
            $insert_data[] = 'UPDATE 
                                `'._DB_PREFIX_.'tvcmsbrandlist` 
                            SET
                                `image` = \''.$data['image'].'\',
                                `link` = \''.$data['link'].'\',
                                `status` = '.$data['status'].'
                            WHERE
                                `id_shop_group` = '.$this->id_shop_group.'
                            AND `id_shop` = '.$this->id_shop.'
                            AND id_tvcmsbrandlist = '.$id;
            $result = $this->selectAllLangIdById($id);

            $languages = Language::getLanguages();
            $i = 0;
            foreach ($languages as $lang) {
                if (in_array($lang['id_lang'], $result)) {
                    $insert_data[] = 'UPDATE
                                        `'._DB_PREFIX_.'tvcmsbrandlist_lang`
                                    SET
                                        `title` = \''.$data['lang_info'][$i]['title'].'\',
                                        `short_description` = \''.$data['lang_info'][$i]['short_description'].'\',
                                        `description` = \''.$data['lang_info'][$i]['description'].'\'
                                    WHERE
                                            `id_shop_group` = '.$this->id_shop_group.'
                                        AND 
                                            `id_shop` = '.$this->id_shop.'
                                        AND
                                            `id_tvcmsbrandlist` = '.$id.'
                                        AND
                                            `id_lang` = '.$lang['id_lang'].';';
                } else {
                    $insert_data[] = 'INSERT INTO
                                        `'._DB_PREFIX_.'tvcmsbrandlist_lang`
                                    SET
                                        `id_tvcmsbrandlist_lang` = NULL,
                                        `id_tvcmsbrandlist` = '.$id.',
                                        `id_shop_group` = '.$this->id_shop_group.',
                                        `id_shop` = '.$this->id_shop.',
                                        `id_lang` = '.$lang['id_lang'].',
                                        `title` = \''.$data['lang_info'][$i]['title'].'\',
                                        `short_description` = \''.$data['lang_info'][$i]['short_description'].'\',
                                        `description` = \''.$data['lang_info'][$i]['description'].'\';';
                }
                $i++;
            }
        } else {
            $max_id = $this->maxId();
            $new_id = $max_id+1;
            $insert_data = array();

            $insert_data[] = 'INSERT INTO 
                                `'._DB_PREFIX_.'tvcmsbrandlist` 
                            SET
                                `id_tvcmsbrandlist` = '.$new_id.',
                                `position` = '.$new_id.',
                                `id_shop_group` = '.$this->id_shop_group.',
                                `id_shop` = '.$this->id_shop.',
                                `image` = \''.$data['image'].'\',
                                `link` = \''.$data['link'].'\',
                                `status` = '.$data['status'].';';

            foreach ($data['lang_info'] as $lang) {
                $insert_data[] = 'INSERT INTO
                                    `'._DB_PREFIX_.'tvcmsbrandlist_lang`
                                SET
                                    `id_tvcmsbrandlist_lang` = NULL,
                                    `id_tvcmsbrandlist` = '.$new_id.',
                                    `id_shop_group` = '.$this->id_shop_group.',
                                    `id_shop` = '.$this->id_shop.',
                                    `id_lang` = '.$lang['id_lang'].',
                                    `title` = \''.$lang['title'].'\',
                                    `short_description` = \''.$lang['short_description'].'\',
                                    `description` = \''.$lang['description'].'\';';
            }
        }

        foreach ($insert_data as $data) {
            Db::getInstance()->execute($data);
        }
    }

    public function showAdminData()
    {
        $result = array();
        $return_data = array();
        $default_lang_id = $this->context->language->id;

        $select_data = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsbrandlist`'
            .' WHERE `id_shop_group` = '.$this->id_shop_group.' AND `id_shop` = '.$this->id_shop
            .' ORDER BY `position`;';

        $result['tvcmsbrandlist'] = Db::getInstance()->executeS($select_data);

        $select_data = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsbrandlist_lang`'
            .' WHERE `id_shop_group` = '.$this->id_shop_group.' AND `id_shop` = '.$this->id_shop.';';


        $result['tvcmsbrandlist_lang'] = Db::getInstance()->executeS($select_data);

        foreach ($result['tvcmsbrandlist'] as $key => $data) {
            $return_data[$key]['id'] = $data['id_tvcmsbrandlist'];
            $id = $data['id_tvcmsbrandlist'];

            foreach ($result['tvcmsbrandlist_lang'] as $lang) {
                if ($default_lang_id == $lang['id_lang'] && $id == $lang['id_tvcmsbrandlist']) {
                    // $lang_id = $lang['id_lang'];
                    $return_data[$key]['id_lang'] = $lang['id_lang'];
                    $return_data[$key]['title'] = $lang['title'];
                    $return_data[$key]['short_description'] = $lang['short_description'];
                    $return_data[$key]['description'] = $lang['description'];
                }
            }

            $return_data[$key]['image'] = $data['image'];
            $return_data[$key]['link'] = $data['link'];
            $return_data[$key]['status'] = $data['status'];
        }
        return $return_data;
    }

    public function showData($id = null)
    {
        $select_data = array();
        $result = array();
        $return_data = array();

        $select_data = '';
        $select_data .= 'SELECT * FROM `'._DB_PREFIX_.'tvcmsbrandlist` 
                WHERE 
                `id_shop_group` = '.$this->id_shop_group
                .' AND `id_shop` = '.$this->id_shop;
        if ($id) {
            $select_data .= ' AND `id_tvcmsbrandlist` = '.$id;
        } else {
            $select_data .= ' ORDER BY `position`';
        }



        $result['tvcmsbrandlist'] = Db::getInstance()->executeS($select_data);

        $select_data = '';
        $select_data .= 'SELECT * FROM `'._DB_PREFIX_.'tvcmsbrandlist_lang`'
            .' WHERE `id_shop_group` = '.$this->id_shop_group.' AND `id_shop` = '.$this->id_shop;
        if ($id) {
            $select_data .= ' AND id_tvcmsbrandlist = '.$id;
        }

        $result['tvcmsbrandlist_lang'] = Db::getInstance()->executeS($select_data);

        foreach ($result['tvcmsbrandlist'] as $key => $data) {
            $return_data[$key]['id'] = $data['id_tvcmsbrandlist'];
            $id = $data['id_tvcmsbrandlist'];
            foreach ($result['tvcmsbrandlist_lang'] as $lang) {
                if ($id == $lang['id_tvcmsbrandlist']) {
                    $lang_id = $lang['id_lang'];
                    $return_data[$key]['lang_info'][$lang_id]['id_lang'] = $lang['id_lang'];
                    $return_data[$key]['lang_info'][$lang_id]['title'] = $lang['title'];
                    $return_data[$key]['lang_info'][$lang_id]['short_description'] = $lang['short_description'];
                    $return_data[$key]['lang_info'][$lang_id]['description'] = $lang['description'];
                }
            }
            $return_data[$key]['image'] = $data['image'];
            $return_data[$key]['link'] = $data['link'];
            $return_data[$key]['status'] = $data['status'];
        }

        return $return_data;
    }

    public function showFrontData()
    {
        $cookie = Context::getContext()->cookie;
        $id_lang = $cookie->id_lang;

        $select_data = '
            SELECT 
                mainTable.id_tvcmsbrandlist AS code,
                mainTable.image,
                mainTable.link,
                subTable.title,
                subTable.short_description,
                subTable.description
            FROM 
                `'._DB_PREFIX_.'tvcmsbrandlist` mainTable
            LEFT JOIN
                '._DB_PREFIX_.'tvcmsbrandlist_lang subTable
            ON
                mainTable.id_tvcmsbrandlist = subTable.id_tvcmsbrandlist
            WHERE 
                mainTable.id_shop_group = '.$this->id_shop_group.' 
            AND 
                mainTable.id_shop = '.$this->id_shop.'
            AND 
                mainTable.status = 1
            AND
                subTable.id_lang = '.$id_lang.'
            ORDER BY `position`';
      
        $result = Db::getInstance()->executeS($select_data);
        $result_data = array();
        if (!empty($result)) {
            $result_data = $result;
        }
        return $result_data;
    }

    public function uninstall()
    {
        $this->uninstallTab();
        $this->deleteVariable();
        $this->deleteTable();
        return parent::uninstall();
    }

    public function deleteVariable()
    {
        Configuration::deleteByName('TVCMSBRANDLIST_TITLE');
        Configuration::deleteByName('TVCMSBRANDLIST_SUB_DESCRIPTION');
        Configuration::deleteByName('TVCMSBRANDLIST_DESCRIPTION');
        Configuration::deleteByName('TVCMSBRANDLIST_IMG');
    }

    public function deleteRecord($id)
    {
        $this->removeImage($id);

        $delete_data = array();
        $delete_data[] = 'DELETE FROM `'._DB_PREFIX_.'tvcmsbrandlist`
            WHERE 
                    `id_shop_group` = '.$this->id_shop_group.'
                AND 
                    `id_shop` = '.$this->id_shop.' 
                AND 
                    `id_tvcmsbrandlist` = '.$id;

        $delete_data[] = 'DELETE FROM `'._DB_PREFIX_.'tvcmsbrandlist_lang` 
            WHERE 
                    `id_shop_group` = '.$this->id_shop_group.'
                AND 
                    `id_shop` = '.$this->id_shop.' 
                AND 
                    id_tvcmsbrandlist = '.$id;

        foreach ($delete_data as $data) {
            Db::getInstance()->execute($data);
        }
    }

    public function removeImage($id)
    {
        $remove_images = array();
        $result = $this->showData($id);

        $remove_images[] = $result[0]['image'];


        foreach ($remove_images as $image) {
            // Match Pattern Which image you Don't want to delete.
            $res = preg_match('/^demo_main_img.*$/', $image);
            $res2 = preg_match('/^demo_img_.*$/', $image);

            if (file_exists(dirname(__FILE__).'./views/img/'.$image)
                && $res != '1'
                && $res2 != '1') {
                unlink(dirname(__FILE__).'./views/img/'.$image);
            }
        }
    }

    public function deleteTable()
    {
        $delete_table = array();
        $delete_table[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tvcmsbrandlist`';
        $delete_table[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tvcmsbrandlist_lang`';

        foreach ($delete_table as $table) {
            Db::getInstance()->execute($table);
        }
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('Admin'.$this->name);
        $tab = new Tab($id_tab);
        $tab->delete();
        return true;
    }

    public function reset()
    {
        $trn_tbl = array();
        $trn_tbl[] = 'TRUNCATE `'._DB_PREFIX_.'tvcmsbrandlist`';
        $trn_tbl[] = 'TRUNCATE `'._DB_PREFIX_.'tvcmsbrandlist_lang`';
        foreach ($trn_tbl as $table) {
            Db::getInstance()->execute($table);
        }
    }

    public function getContent()
    {
        $useSSL = (isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode() ? true : false;
        $protocol_content = $useSSL ? 'https://' : 'http://';
        $baseDir = $protocol_content . Tools::getHttpHost() . __PS_BASE_URI__;
        $link = PS_ADMIN_DIR;
        if (Tools::substr(strrchr($link, "/"), 1)) {
            $admin_folder = Tools::substr(strrchr($link, "/"), 1);
        } else {
            $admin_folder = Tools::substr(strrchr($link, "\'"), 1);
        }
        $static_token = Tools::getAdminToken('AdminModules' . (int) Tab::getIdFromClassName('AdminModules') . (int) $this->context->employee->id);
        $url_slidersampleupgrade = $baseDir . $admin_folder . '/index.php?controller=AdminModules&configure='.$this->name.'&tab_module=front_office_features&module_name='.$this->name.'&token=' . $static_token;
        $this->context->smarty->assign('tvurlupgrade', $url_slidersampleupgrade);

        if (Tools::isSubmit('submitTvcmsSampleinstall')) {
            $this->createDefaultData();
        }

        $message = $this->postProcess();
        $output = $message
                .$this->renderForm()
                .$this->showAdminResult();
        return $output;
    }

    public function postProcess()
    {
        $message = '';
        $result = array();

        $no_image_selected = false;
        if (Tools::getValue('action')) {
            if (Tools::getValue('action') == 'remove') {
                $id = Tools::getValue('id');
                $this->deleteRecord($id);
                $message .= $this->displayConfirmation($this->l("Record is Deleted."));
            }
        }

        if (Tools::isSubmit('submittvcmsBrandListForm')) {
            $old_file = '';
            $result['id'] = '';
            if (Tools::getValue('id')) {
                $result['id'] = Tools::getValue('id');
                $id = Tools::getValue('id');
                $res = $this->showData($id);
                $old_file = $res[0]['image'];
            }
            

            $tvcms_obj = new TvcmsBrandListStatus();
            $show_fields = $tvcms_obj->fieldStatusInformation();
            $result['image'] = '';
            if ($show_fields['image']) {
                if (!empty($_FILES['image']['name'])) {
                    $new_file = $_FILES['image'];
                    $obj_image = new TvcmsBrandListImageUpload();
                    $ans = $obj_image->imageUploading($new_file, $old_file);
                    if ($ans['success']) {
                        $result['image'] = $ans['name'];
                    } else {
                        $message .= $ans['error'];
                        $result['image'] = $old_file;
                        if (!Tools::getValue('id')) {
                            $no_image_selected = true;
                        }
                    }
                } else {
                    $result['image'] = $old_file;
                    if (!Tools::getValue('id')) {
                        $message .= $this->displayError($this->l("Please Select Image."));
                        $no_image_selected = true;
                    }
                }
            }

            if (!$no_image_selected) {
                $result['link'] = Tools::getValue('link');
                $result['status'] = Tools::getValue('status');

                $languages = Language::getLanguages();
                $i = 0;
                foreach ($languages as $lang) {
                    $result['lang_info'][$i]['id_lang'] = $lang['id_lang'];

                    $result['lang_info'][$i]['title'] = Tools::getValue('title_'.$lang['id_lang']);
                    $tmp = Tools::getValue('short_description_'.$lang['id_lang']);
                    $result['lang_info'][$i]['short_description'] = $tmp;
                    $result['lang_info'][$i]['description'] = Tools::getValue('description_'.$lang['id_lang']);
                    $i++;
                }
                $this->insertData($result);
                
                $this->clearCustomSmartyCache('tvcmsbrandlist_display_home.tpl');

                if (Tools::getValue('id')) {
                    $message .= $this->displayConfirmation($this->l("BrandList Record is Updated."));
                } else {
                    $message .= $this->displayConfirmation($this->l("BrandList Record is Inserted."));
                }
            }
        }

        if (Tools::isSubmit('submittvcmsBrandListMainTitleForm')) {
            $languages = Language::getLanguages();
            $obj_image = new TvcmsBrandListImageUpload();
            foreach ($languages as $lang) {
                if (!empty($_FILES['TVCMSBRANDLIST_IMG_'.$lang['id_lang']]['name'])) {
                    $old_file = Configuration::get('TVCMSBRANDLIST_IMG', $lang['id_lang']);
                    $new_file = $_FILES['TVCMSBRANDLIST_IMG_'.$lang['id_lang']];
                    $ans = $obj_image->imageUploading($new_file, $old_file);
                    if ($ans['success']) {
                        $result['TVCMSBRANDLIST_IMG'][$lang['id_lang']] = $ans['name'];
                    } else {
                        $message .= $ans['error'];
                        $result['TVCMSBRANDLIST_IMG'][$lang['id_lang']] = $old_file;
                    }
                } else {
                    $old_file = Configuration::get('TVCMSBRANDLIST_IMG', $lang['id_lang']);
                    $result['TVCMSBRANDLIST_IMG'][$lang['id_lang']] = $old_file;
                }

                $tmp = Tools::getValue('TVCMSBRANDLIST_TITLE_'.$lang['id_lang']);
                $result['TVCMSBRANDLIST_TITLE'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSBRANDLIST_SUB_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSBRANDLIST_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSBRANDLIST_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSBRANDLIST_DESCRIPTION'][$lang['id_lang']] = $tmp;
            }
            Configuration::updateValue('TVCMSBRANDLIST_IMG', $result['TVCMSBRANDLIST_IMG']);
            Configuration::updateValue('TVCMSBRANDLIST_TITLE', $result['TVCMSBRANDLIST_TITLE']);
            $tmp = $result['TVCMSBRANDLIST_SUB_DESCRIPTION'];
            Configuration::updateValue('TVCMSBRANDLIST_SUB_DESCRIPTION', $tmp);

            $tmp = $result['TVCMSBRANDLIST_DESCRIPTION'];
            Configuration::updateValue('TVCMSBRANDLIST_DESCRIPTION', $tmp);
            $message .= $this->displayConfirmation($this->l("Main Title is Updated."));
        }

            
        return $message;
    }

    public function clearCustomSmartyCache($cache_id)
    {
        if (Cache::isStored($cache_id)) {
            Cache::clean($cache_id);
        }
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form = array();

        $tvcms_obj = new TvcmsBrandListStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        if ($show_fields['main_status']) {
            $form[] = $this->tvcmsBrandListMainTitleForm();
        }

        if ($show_fields['record_form']) {
            $form[] = $this->tvcmsBrandListForm();
        }

        

        return $helper->generateForm($form);
    }

    protected function tvcmsBrandListMainTitleForm()
    {
        $tvcms_obj = new TvcmsBrandListStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $input = array();

        if ($show_fields['main_title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSBRANDLIST_TITLE',
                    'label' => $this->l('Main Title'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_sub_title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSBRANDLIST_SUB_DESCRIPTION',
                    'label' => $this->l('Short Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSBRANDLIST_DESCRIPTION',
                    'label' => $this->l('Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_image']) {
            $input[] = array(
                        'type' => 'image_file',
                        'name' => 'TVCMSBRANDLIST_IMG',
                        'label' => $this->l('Brand Image'),
                );
        }


        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Main Title'),
                'icon' => 'icon-support',
                ),
                'input' => $input,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submittvcmsBrandListMainTitleForm',
                ),
            ),
        );
    }


    protected function tvcmsBrandListForm()
    {
        $tvcms_obj = new TvcmsBrandListStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $input = array();

        if (Tools::getValue('action')) {
            if (Tools::getValue('action') == 'edit') {
                $input[] = array(
                        'type' => 'hidden',
                        'name' => 'id',
                    );
            }
        }

        if ($show_fields['title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'title',
                    'label' => $this->l('Title'),
                    'lang' => true,
                );
        }

        if ($show_fields['short_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'short_description',
                    'label' => $this->l('Short description'),
                    'lang' => true,
                );
        }

        if ($show_fields['description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'description',
                    'label' => $this->l('Description'),
                    'lang' => true,
                    // 'cols' => 40,
                    // 'rows' => 10,
                    // 'class' => 'rte',
                    // 'autoload_rte' => true,
                );
        }

        if ($show_fields['link']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'link',
                    'label' => $this->l('Link'),
                    'desc' => $this->l('You must write full link. Ex:- https://www.demo.com/'),
                );
        }


        if ($show_fields['image']) {
            $input[] =  array(
                        'col' => 8,
                        'type' => 'tvcmsbrandlist_img',
                        'name' => 'image',
                        'label' => $this->l('Image'),
                    );
        }

        if ($show_fields['status']) {
            $input[] =  array(
                        'type' => 'switch',
                        'label' => $this->l('Status'),
                        'name' => 'status',
                        'desc' => $this->l('Hide or show icons.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Hide')
                            )
                        )
                    );
        }
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Brand List'),
                'icon' => 'icon-support',
                ),
                'input' => $input,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submittvcmsBrandListForm',
                ),
            ),
        );
    }

    public function hookdisplayHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }//hookDisplayHeader()
    public function hookDisplayBackOfficeHeader()
    {
        if ($this->name == Tools::getValue('configure')) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }//hookDisplayBackOfficeHeader()
 
    protected function getConfigFormValues()
    {
        $fields = array();
        $languages = Language::getLanguages();
        $fields['id'] = '';
        foreach ($languages as $lang) {
            $fields['title'][$lang['id_lang']] = '';
            $fields['short_description'][$lang['id_lang']] = '';
            $fields['description'][$lang['id_lang']] = '';
        }

        $fields['image'] = '';
        $fields['link'] = '';
        $fields['status'] = '';

        if (Tools::getValue('action')) {
            if (Tools::getValue('action') == 'edit') {
                $id = Tools::getValue('id');
                $array_list = $this->showData($id);
                $array_list = $array_list[0];

                $fields['id'] = $id;
                foreach ($languages as $lang) {
                    if (!empty($array_list['lang_info'][$lang['id_lang']])) {
                        $fields['title'][$lang['id_lang']] = $array_list['lang_info'][$lang['id_lang']]['title'];
                        $tmp = $array_list['lang_info'][$lang['id_lang']]['short_description'];
                        $fields['short_description'][$lang['id_lang']] = $tmp;
                        $tmp = $array_list['lang_info'][$lang['id_lang']]['description'];
                        $fields['description'][$lang['id_lang']] = $tmp;
                    }
                }

                $fields['image'] = $array_list['image'];
                $fields['link'] = $array_list['link'];
                $fields['status'] = $array_list['status'];
            }
        }

        foreach ($languages as $lang) {
            $tmp = Configuration::get('TVCMSBRANDLIST_TITLE', $lang['id_lang']);
            $fields['TVCMSBRANDLIST_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSBRANDLIST_SUB_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSBRANDLIST_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSBRANDLIST_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSBRANDLIST_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSBRANDLIST_IMG', $lang['id_lang']);
            $fields['TVCMSBRANDLIST_IMG'][$lang['id_lang']] = $tmp;
        }

        $path = _MODULE_DIR_.$this->name."/views/img/";
        $this->context->smarty->assign("path", $path);
        
        return $fields;
    }

    public function showAdminResult()
    {
        $tvcms_obj = new TvcmsBrandListStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $default_lang_id = $this->context->language->id;
        $array_list = $this->showAdminData();
        $this->context->smarty->assign('array_list', $array_list);
        $this->context->smarty->assign('show_fields', $show_fields);
        $this->context->smarty->assign('default_lang_id', $default_lang_id);

        return $this->display(__FILE__, "views/templates/admin/tvcmsbrandlist_manage.tpl");
    }


    public function hookDisplayTopColumn()
    {
        return $this->hookDisplayHome();
    }

    public function hookdisplayWrapperBottom()
    {
        return $this->hookDisplayHome();
    }

    public function hookdisplayFooterColumn()
    {
        return $this->hookDisplayHome();
    }

    public function hookDisplayFooterBefore()
    {
        return $this->hookDisplayHome();
    }

    public function hookdisplayWrapperTop()
    {
        return $this->hookDisplayHome();
    }

    public function hookdisplayContentWrapperTop()
    {
        return $this->hookDisplayHome();
    }


    public function getArrMainTitle($main_heading, $main_heading_data)
    {
        if (!$main_heading['main_title'] || empty($main_heading_data['title'])) {
            $main_heading['main_title'] = false;
        }
        if (!$main_heading['main_sub_title'] || empty($main_heading_data['short_desc'])) {
            $main_heading['main_sub_title'] = false;
        }
        if (!$main_heading['main_description'] || empty($main_heading_data['desc'])) {
            $main_heading['main_description'] = false;
        }
        if (!$main_heading['main_image'] || empty($main_heading_data['image'])) {
            $main_heading['main_image'] = false;
        }
        if (!$main_heading['main_title'] &&
            !$main_heading['main_sub_title'] &&
            !$main_heading['main_description'] &&
            !$main_heading['main_image']) {
            $main_heading['main_status'] = false;
        }
        return $main_heading;
    }


    public function showFrontSideResult()
    {
        $cookie = Context::getContext()->cookie;
        $id_lang = $cookie->id_lang;

        $tvcms_obj = new TvcmsBrandListStatus();
        $main_heading = $tvcms_obj->fieldStatusInformation();

        if ($main_heading['main_status']) {
            $main_heading_data = array();
            $main_heading_data['title'] = Configuration::get('TVCMSBRANDLIST_TITLE', $id_lang);
            $main_heading_data['short_desc'] = Configuration::get('TVCMSBRANDLIST_SUB_DESCRIPTION', $id_lang);
            $main_heading_data['desc'] = Configuration::get('TVCMSBRANDLIST_DESCRIPTION', $id_lang);
            $main_heading_data['image'] = Configuration::get('TVCMSBRANDLIST_IMG', $id_lang);
            $main_heading = $this->getArrMainTitle($main_heading, $main_heading_data);
            $main_heading['data'] = $main_heading_data;
        }

        $disArrResult = array();
        $disArrResult['data'] = $this->showFrontData();
        $disArrResult['status'] = empty($disArrResult['data'])?false:true;
        $disArrResult['path'] = _MODULE_DIR_.$this->name."/views/img/";
        $disArrResult['id_lang'] = $id_lang;

        $this->context->smarty->assign('main_heading', $main_heading);
        $this->context->smarty->assign('dis_arr_result', $disArrResult);


        return $disArrResult['status']?true:false;
    }

    public function hookDisplayHome()
    {
        if (!Cache::isStored('tvcmsbrandlist_display_home.tpl')) {
            $result = $this->showFrontSideResult();
            if ($result) {
                $output = $this->display(__FILE__, 'views/templates/front/display_home.tpl');
            } else {
                $output = '';
            }
            Cache::store('tvcmsbrandlist_display_home.tpl', $output);
        }

        return Cache::retrieve('tvcmsbrandlist_display_home.tpl');
    }
}
