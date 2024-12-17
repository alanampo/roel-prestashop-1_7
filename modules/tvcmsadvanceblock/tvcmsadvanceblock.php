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

include_once('classes/tvcmsadvanceblock_status.class.php');
include_once('classes/tvcmsadvanceblock_image_upload.class.php');
// include_once(_PS_MODULE_DIR_.'tvcmscustomsetting/classes/tvcmsresizemasterclass.php');

class TvcmsAdvanceBlock extends Module
{
    public function __construct()
    {
        $this->name = 'tvcmsadvanceblock';
        $this->tab = 'front_office_features';
        $this->version = '4.0.1';
        $this->author = 'JHP Template';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('JHP Template - Advance Block');
        $this->description = $this->l('Its Show Advance Block on Front Side');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = '';

        $this->confirmUninstall = $this->l('Warning: all the data saved in your database will be deleted.'.
            ' Are you sure you want uninstall this module?');
    }


    public function install()
    {
        $this->installTab();
        // $this->createDefaultData();
        $this->createTable();
        
        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayHome');
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
            $tab->name[$lang['id_lang']] = "Advance Block";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function createDefaultData()
    {
        $this->reset();
        $num_of_data = 4;
        $this->createVariable();
        $this->createTable();
        $this->insertSmapleData($num_of_data);
    }

    public function createVariable()
    {
        $languages = Language::getLanguages();
        $result = array();
        foreach ($languages as $lang) {
            $result['TVCMSADVANCEBLOCK_TITLE'][$lang['id_lang']] = 'Our Journey To Dreams';
            $result['TVCMSADVANCEBLOCK_SUB_DESCRIPTION'][$lang['id_lang']] = 'About Our Store';
            $result['TVCMSADVANCEBLOCK_DESCRIPTION'][$lang['id_lang']] = 'Give You the Creative Ideal To Table Your Home!';
            $result['TVCMSADVANCEBLOCK_IMG'][$lang['id_lang']] = 'demo_main_img.jpg';


            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE'][$lang['id_lang']] = 'Patty OFurniture';
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE'][$lang['id_lang']] = 'Founder Of Growee';
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION'][$lang['id_lang']] = 'Luckily, we have a few ideas on watering for optimum plant health.';
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION'][$lang['id_lang']] = 'Empowering all people to be plant people — a collection of articles from The Sill’s team of Plant Experts across a variety of plant care topics to inspire confidence in the next generation of plant parents. Welcome to Plant Parenthood™.';
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK'][$lang['id_lang']] = '#';
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION'][$lang['id_lang']] = 'SHOP NOW';
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE'][$lang['id_lang']] = 'OFFER ENDS IN';

            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'][$lang['id_lang']] = 'demo_main_block_img.png';
            $ImageSizePath = _MODULE_DIR_.$this->name."/views/img/";
            $imagedata = getimagesize(_PS_BASE_URL_.$ImageSizePath.$result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'][$lang['id_lang']]);
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1'][$lang['id_lang']] = $imagedata[0];
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1'][$lang['id_lang']] = $imagedata[1];


            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'][$lang['id_lang']] = 'demo_main_block_back_img.jpg';
            $ImageSizePath = _MODULE_DIR_.$this->name."/views/img/";
            $imagedata = getimagesize(_PS_BASE_URL_.$ImageSizePath.$result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'][$lang['id_lang']]);
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1'][$lang['id_lang']] = $imagedata[0];
            $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1'][$lang['id_lang']] = $imagedata[1];
            
        }
        
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1', $tmp);
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1', $tmp);

        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1', $tmp);
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1', $tmp);


        Configuration::updateValue('TVCMSADVANCEBLOCK_TITLE', $result['TVCMSADVANCEBLOCK_TITLE']);
        Configuration::updateValue('TVCMSADVANCEBLOCK_SUB_DESCRIPTION', $result['TVCMSADVANCEBLOCK_SUB_DESCRIPTION']);
        Configuration::updateValue('TVCMSADVANCEBLOCK_DESCRIPTION', $result['TVCMSADVANCEBLOCK_DESCRIPTION']);
        Configuration::updateValue('TVCMSADVANCEBLOCK_IMG', $result['TVCMSADVANCEBLOCK_IMG']);

        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE', $tmp);
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE', $tmp);
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION', $tmp);
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION', $tmp);
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK', $tmp);
        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION', $tmp);
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG', $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG']);
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG', $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG']);

        $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE'];
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE', $tmp);

        $tmp = date('Y-m-d', strtotime(date('Y-m-d'). ' + 2 days'));
        Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE', $tmp, true);
    }


    public function createTable()
    {
        $create_table = array();
        $create_table[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tvcmsadvanceblock` (
                        `id_tvcmsadvanceblock` int(11) AUTO_INCREMENT PRIMARY KEY,
                        `position` int(11),
                        `image` VARCHAR(100),
                        `link` VARCHAR(255),
                        `status` varchar(3)
                    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $create_table[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tvcmsadvanceblock_lang` (
                        `id_tvcmsadvanceblock_lang` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `id_tvcmsadvanceblock` INT NOT NULL,
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
                            `'._DB_PREFIX_.'tvcmsadvanceblock`
                        SET 
                            `id_tvcmsadvanceblock` = '.$i.',
                            `position` = '.$i.',
                            `image` = \'demo_img_'.$i.'.png\',
                            `link` = \'#\',
                            `status` = \'1\';';
            foreach ($languages as $lang) {
                if ($i == 1) {
                    $title = 'Garden Care';
                    $description = 'Service quality generally refers to a customers comparison of service expectations to a companys.';
                } elseif ($i == 2) {
                    $title = 'Plant Renovation';
                    $description = 'Engineering is focused on delivering quality products at a time and provide the best solution.';
                } elseif ($i == 3) {
                    $title = 'Seed Supply';
                    $description = 'Customer satisfaction indicates the fulfillment that customers derive from doing business';
                } elseif ($i == 4) {
                    $title = 'Watering Graden';
                    $description = 'Low Maintenace';
                } elseif ($i == 5) {
                    $title = 'Special Feature';
                    $description = 'Air Purifying';
                } else {
                    $title = 'This is a very unfriendly wine. It hits your mouth and then turns it inside out. It usually means the wine has very high acidity and very little fruit flavors. An austere wine is not fruit-forward nor opulent.'.$i;
                    $description = 'This is a very unfriendly wine. It hits your mouth and then turns it inside out. It usually means the wine has very high acidity and very little fruit flavors. An austere wine is not fruit-forward nor opulent.'.$i;
                }
                $demo_data[] = 'INSERT INTO
                                `'._DB_PREFIX_.'tvcmsadvanceblock_lang`
                            SET
                                `id_tvcmsadvanceblock_lang` = NULL,
                                `id_tvcmsadvanceblock` = '.$i.',
                                `id_lang` = '.$lang['id_lang'].',
                                `title` = \''.$title.'\',
                                `short_description` = \''.$short_desc.'\',
                                `description` = \'test '.$description.'\';';
            }
        }
        foreach ($demo_data as $data) {
            Db::getInstance()->execute($data);
        }
    }

    public function maxId()
    {
        $select_data = 'SELECT MAX(id_tvcmsadvanceblock) as max_id FROM `'._DB_PREFIX_.'tvcmsadvanceblock`';
        $ans = Db::getInstance()->executeS($select_data);
        return $ans[0]['max_id'];
    }

    public function selectAllLangIdById($id_tvcmsadvanceblock)
    {
        $select_data = 'SELECT 
                            id_lang 
                        FROM 
                            `'._DB_PREFIX_.'tvcmsadvanceblock_lang` 
                        WHERE 
                            id_tvcmsadvanceblock = '.$id_tvcmsadvanceblock;
        $ans = Db::getInstance()->executeS($select_data);
        $return = array();
        foreach ($ans as $a) {
            $return[] = $a['id_lang'];
        }
        return $return;
    }

    public function reset()
    {
        $trn_tbl = array();
        $trn_tbl[] = 'TRUNCATE `'._DB_PREFIX_.'tvcmsadvanceblock`';
        $trn_tbl[] = 'TRUNCATE `'._DB_PREFIX_.'tvcmsadvanceblock_lang`';
        foreach ($trn_tbl as $table) {
            Db::getInstance()->execute($table);
        }
    }

    public function insertData($data)
    {
        $result = array();
        $insert_data = array();
        if ($data['id']) {
            $id = $data['id'];
            $insert_data[] = 'UPDATE 
                                `'._DB_PREFIX_.'tvcmsadvanceblock` 
                            SET
                                `image` = \''.$data['image'].'\',
                                `link` = \''.$data['link'].'\',
                                `status` = '.$data['status'].'
                            WHERE
                                `id_tvcmsadvanceblock` = '.$id.';';
            $result = $this->selectAllLangIdById($id);

            $languages = Language::getLanguages();
            $i = 0;
            foreach ($languages as $lang) {
                if (in_array($lang['id_lang'], $result)) {
                    $insert_data[] = 'UPDATE
                                        `'._DB_PREFIX_.'tvcmsadvanceblock_lang`
                                    SET
                                        `title` = \''.$data['lang_info'][$i]['title'].'\',
                                        `short_description` = \''.$data['lang_info'][$i]['short_description'].'\',
                                        `description` = \''.$data['lang_info'][$i]['description'].'\'
                                    WHERE
                                            `id_tvcmsadvanceblock` = '.$id.'
                                        AND
                                            `id_lang` = '.$lang['id_lang'].';';
                } else {
                    $insert_data[] = 'INSERT INTO
                                        `'._DB_PREFIX_.'tvcmsadvanceblock_lang`
                                    SET
                                        `id_tvcmsadvanceblock_lang` = NULL,
                                        `id_tvcmsadvanceblock` = '.$id.',
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
                                `'._DB_PREFIX_.'tvcmsadvanceblock` 
                            SET
                                `id_tvcmsadvanceblock` = '.$new_id.',
                                `position` = '.$new_id.',
                                `image` = \''.$data['image'].'\',
                                `link` = \''.$data['link'].'\',
                                `status` = '.$data['status'].';';

            foreach ($data['lang_info'] as $lang) {
                $insert_data[] = 'INSERT INTO
                                    `'._DB_PREFIX_.'tvcmsadvanceblock_lang`
                                SET
                                    `id_tvcmsadvanceblock_lang` = NULL,
                                    `id_tvcmsadvanceblock` = '.$new_id.',
                                    `id_lang` = '.$lang['id_lang'].',
                                    `title` = \''.$lang['title'].'\',
                                    `short_description` = \''.$lang['short_description'].'\',
                                    `description` = \''.$lang['description'].'\';';
            }
        }
        // echo "<pre>";
        // print_r($insert_data);

        // exit;
        foreach ($insert_data as $data) {
            Db::getInstance()->execute($data);
        }
    }

    public function showAdminData()
    {
        $result = array();
        $return_data = array();
        $default_lang_id = $this->context->language->id;

        $select_data = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsadvanceblock` ORDER BY `position`';
        $result['tvcmsadvanceblock'] = Db::getInstance()->executeS($select_data);

        $select_data = 'SELECT * FROM `'._DB_PREFIX_.'tvcmsadvanceblock_lang`';
        $result['tvcmsadvanceblock_lang'] = Db::getInstance()->executeS($select_data);

        foreach ($result['tvcmsadvanceblock'] as $key => $data) {
            $return_data[$key]['id'] = $data['id_tvcmsadvanceblock'];
            $id = $data['id_tvcmsadvanceblock'];

            foreach ($result['tvcmsadvanceblock_lang'] as $lang) {
                if ($default_lang_id == $lang['id_lang'] && $id == $lang['id_tvcmsadvanceblock']) {
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
        $select_data .= 'SELECT * FROM `'._DB_PREFIX_.'tvcmsadvanceblock` ';

        if (!empty($id)) {
            $select_data .= 'WHERE id_tvcmsadvanceblock = '.$id;
        } else {
            $select_data .=  'ORDER BY `position`';
        }

        $result['tvcmsadvanceblock'] = Db::getInstance()->executeS($select_data);

        $select_data = '';
        $select_data .= 'SELECT * FROM `'._DB_PREFIX_.'tvcmsadvanceblock_lang`';
        if (!empty($id)) {
            $select_data .= 'WHERE id_tvcmsadvanceblock = '.$id;
        }
        $result['tvcmsadvanceblock_lang'] = Db::getInstance()->executeS($select_data);

        foreach ($result['tvcmsadvanceblock'] as $key => $data) {
            $return_data[$key]['id'] = $data['id_tvcmsadvanceblock'];
            $id = $data['id_tvcmsadvanceblock'];
            foreach ($result['tvcmsadvanceblock_lang'] as $lang) {
                if ($id == $lang['id_tvcmsadvanceblock']) {
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

    public function uninstall()
    {
        $this->uninstallTab();
        $this->deleteVariable();
        $this->deleteTable();
        return parent::uninstall();
    }

    public function deleteVariable()
    {
        Configuration::deleteByName('TVCMSADVANCEBLOCK_TITLE');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_SUB_DESCRIPTION');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_DESCRIPTION');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_IMG');

        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1');

        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG');

        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE');
        Configuration::deleteByName('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE');
    }

    public function deleteRecord($id)
    {
        $this->removeImage($id);

        $delete_data = array();
        $delete_data[] = 'DELETE FROM `'._DB_PREFIX_.'tvcmsadvanceblock` WHERE id_tvcmsadvanceblock = '.$id;
        $delete_data[] = 'DELETE FROM `'._DB_PREFIX_.'tvcmsadvanceblock_lang` WHERE id_tvcmsadvanceblock = '.$id;

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
        $delete_table[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tvcmsadvanceblock`';
        $delete_table[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tvcmsadvanceblock_lang`';

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

        if (Tools::isSubmit('submitvcmsAdvanceBlockForm')) {
            $old_file = '';
            if (Tools::getValue('id')) {
                $result['id'] = Tools::getValue('id');
                $id = $result['id'];
                $res = $this->showData($id);
                $old_file = $res[0]['image'];
            }

            $tvcms_obj = new TvcmsAdvanceBlockStatus();
            $show_fields = $tvcms_obj->fieldStatusInformation();
            if ($show_fields['image']) {
                if (!empty($_FILES['image']['name'])) {
                    $new_file = $_FILES['image'];
                    $obj_image = new TvcmsAdvanceBlockImageUpload();
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
                    $tmp = Tools::getValue('title_'.$lang['id_lang']);
                    $result['lang_info'][$i]['title'] = addslashes($tmp);
                    $tmp = Tools::getValue('short_description_'.$lang['id_lang']);
                    $result['lang_info'][$i]['short_description'] = addslashes($tmp);
                    $tmp = Tools::getValue('description_'.$lang['id_lang']);
                    $result['lang_info'][$i]['description'] = addslashes($tmp);
                    $i++;
                }
                $this->insertData($result);
                if (Tools::getValue('id')) {
                    $message .= $this->displayConfirmation($this->l("Record is Updated."));
                } else {
                    $message .= $this->displayConfirmation($this->l("Record is Inserted."));
                }
            }
        }

        if (Tools::isSubmit('submitvcmsAdvanceBlockMainTitleForm')) {
            $languages = Language::getLanguages();
            $obj_image = new TvcmsAdvanceBlockImageUpload();
            foreach ($languages as $lang) {
                if (!empty($_FILES['TVCMSADVANCEBLOCK_IMG_'.$lang['id_lang']]['name'])) {
                    $old_file = Configuration::get('TVCMSADVANCEBLOCK_IMG', $lang['id_lang']);
                    $new_file = $_FILES['TVCMSADVANCEBLOCK_IMG_'.$lang['id_lang']];
                    $ans = $obj_image->imageUploading($new_file, $old_file);
                    if ($ans['success']) {
                        $result['TVCMSADVANCEBLOCK_IMG'][$lang['id_lang']] = $ans['name'];
                    } else {
                        $message .= $ans['error'];
                        $result['TVCMSADVANCEBLOCK_IMG'][$lang['id_lang']] = $old_file;
                    }
                } else {
                    $old_file = Configuration::get('TVCMSADVANCEBLOCK_IMG', $lang['id_lang']);
                    $result['TVCMSADVANCEBLOCK_IMG'][$lang['id_lang']] = $old_file;
                }

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_TITLE_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_TITLE'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_SUB_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_DESCRIPTION'][$lang['id_lang']] = $tmp;
            }
            Configuration::updateValue('TVCMSADVANCEBLOCK_IMG', $result['TVCMSADVANCEBLOCK_IMG']);
            Configuration::updateValue('TVCMSADVANCEBLOCK_TITLE', $result['TVCMSADVANCEBLOCK_TITLE']);
            $tmp = $result['TVCMSADVANCEBLOCK_SUB_DESCRIPTION'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_SUB_DESCRIPTION', $tmp);

            $tmp = $result['TVCMSADVANCEBLOCK_DESCRIPTION'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_DESCRIPTION', $tmp);
            $message .= $this->displayConfirmation($this->l("Main Title is Updated."));
        }


        if (Tools::isSubmit('submitvcmsAdvanceBlockMainBlockForm')) {
            $languages = Language::getLanguages();
            $obj_image = new TvcmsAdvanceBlockImageUpload();

            foreach ($languages as $lang) {
                if (!empty($_FILES['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_'.$lang['id_lang']]['name'])) {
                    $old_file = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG', $lang['id_lang']);
                    $old_file_main = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG', $lang['id_lang']);
                    $new_file = $_FILES['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_'.$lang['id_lang']];
                    $ans = $obj_image->imageUploading($new_file, $old_file);
                    if ($ans['success']) {
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'][$lang['id_lang']] = $ans['name'];
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1'][$lang['id_lang']] = $ans['width'];
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1'][$lang['id_lang']] = $ans['height'];
                    } else {
                        $message .= $ans['error'];
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'][$lang['id_lang']] = $old_file;
                    }
                } else {
                    $old_file = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG', $lang['id_lang']);
                    $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'][$lang['id_lang']] = $old_file;
                }

                /********Remove tv resize images*********/
                // $tvpath = dirname(__FILE__).'/views/img/';
                // $MediumImgPath = $tvpath.'medium/'.$old_file_main;
                // if (file_exists($MediumImgPath)) {
                //     @unlink($MediumImgPath);
                // }
                // $SmallImgPath = $tvpath.'small/'.$old_file_main;
                // if (file_exists($SmallImgPath)) {
                //     @unlink($SmallImgPath);
                // }
                /********Remove tv resize images*********/

                /***********Image Resize*************/
                // $path = dirname(__FILE__).'/views/img/';
                // $ImageName = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'][$lang['id_lang']];
                // if (file_exists($path.$ImageName)) {
                //     $MediumImgPath = $path.'medium/';
                //     if (!is_dir($MediumImgPath)) {
                //         mkdir($MediumImgPath);
                //     }
                //     $resizeObj = new TvcmsResizeMasterClass($path.$ImageName);
                //     $resizeObj->resizeImage(770, 770, 4);
                //     $resizeObj->saveImage($MediumImgPath.$ImageName);

                //     $SmallImgPath = $path.'small/';
                //     if (!is_dir($SmallImgPath)) {
                //         mkdir($SmallImgPath);
                //     }
                //     $resizeObj = new TvcmsResizeMasterClass($path.$ImageName);
                //     $resizeObj->resizeImage(500, 500, 4);
                //     $resizeObj->saveImage($SmallImgPath.$ImageName);
                // }

                /********Remove tv resize images*********/

                if (!empty($_FILES['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_'.$lang['id_lang']]['name'])) {
                    $old_file = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG', $lang['id_lang']);
                    $old_file_back = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG', $lang['id_lang']);
                    $new_file = $_FILES['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_'.$lang['id_lang']];
                    $ans = $obj_image->imageUploading($new_file, $old_file);
                    if ($ans['success']) {
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'][$lang['id_lang']] = $ans['name'];
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1'][$lang['id_lang']] = $ans['width'];
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1'][$lang['id_lang']] = $ans['height'];
                    } else {
                        $message .= $ans['error'];
                        $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'][$lang['id_lang']] = $old_file;
                    }
                } else {
                    $old_file = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG', $lang['id_lang']);
                    $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'][$lang['id_lang']] = $old_file;
                }

                /********Remove tv resize images*********/
                // $tvpath = dirname(__FILE__).'/views/img/';
                // $MediumImgPath = $tvpath.'medium/'.$old_file_back;
                // if (file_exists($MediumImgPath)) {
                //     @unlink($MediumImgPath);
                // }
                // $SmallImgPath = $tvpath.'small/'.$old_file_back;
                // if (file_exists($SmallImgPath)) {
                //     @unlink($SmallImgPath);
                // }
                /********Remove tv resize images*********/

                /***********Image Resize*************/
                // $path = dirname(__FILE__).'/views/img/';
                // $ImageName = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'][$lang['id_lang']];
                // if (file_exists($path.$ImageName)) {
                //     $MediumImgPath = $path.'medium/';
                //     if (!is_dir($MediumImgPath)) {
                //         mkdir($MediumImgPath);
                //     }

                //     $resizeObj = new TvcmsResizeMasterClass($path.$ImageName);
                //     $resizeObj->resizeImage(770, 770, 4);
                //     $resizeObj->saveImage($MediumImgPath.$ImageName);

                //     $SmallImgPath = $path.'small/';
                //     if (!is_dir($SmallImgPath)) {
                //         mkdir($SmallImgPath);
                //     }

                //     $resizeObj = new TvcmsResizeMasterClass($path.$ImageName);
                //     $resizeObj->resizeImage(500, 500, 4);
                //     $resizeObj->saveImage($SmallImgPath.$ImageName);
                // }

                /********Remove tv resize images*********/

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE'][$lang['id_lang']] = $tmp;

                        


                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK'][$lang['id_lang']] = $tmp;

                $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE_'.$lang['id_lang']);
                $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE'][$lang['id_lang']] = $tmp;

                // $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE_'.$lang['id_lang']);
                // $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE'][$lang['id_lang']] = $tmp;
            }
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1', $tmp);

            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1', $tmp);

            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1', $tmp);

            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1', $tmp);

            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG', $tmp);
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG', $tmp);
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE', $tmp);
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE', $tmp);
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION', $tmp);
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION', $tmp);
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION', $tmp);
            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK', $tmp);

            $tmp = $result['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE'];
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE', $tmp);

            $tmp = Tools::getValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE');
            Configuration::updateValue('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE', $tmp, true);
            
            $message .= $this->displayConfirmation($this->l("Main Block is Updated."));
        }

        return $message;
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
        $helper->show_cancel_button = true;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form = array();

        $tvcms_obj = new TvcmsAdvanceBlockStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        if ($show_fields['main_form']) {
            $form[] = $this->tvcmsAdvanceBlockMainTitleForm();
        }

        if ($show_fields['main_block_form']) {
            $form[] = $this->tvcmsAdvanceBlockMainBlockForm();
        }

        

        if ($show_fields['record_form']) {
            $form[] = $this->tvcmsAdvanceBlockForm();
        }

        

        return $helper->generateForm($form);
    }

    protected function tvcmsAdvanceBlockMainTitleForm()
    {
        $tvcms_obj = new TvcmsAdvanceBlockStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $input = array();

        if ($show_fields['main_title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_TITLE',
                    'label' => $this->l('Main Title'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_short_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_SUB_DESCRIPTION',
                    'label' => $this->l('Short Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_DESCRIPTION',
                    'label' => $this->l('Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_image']) {
            $input[] = array(
                        'type' => 'image_file',
                        'name' => 'TVCMSADVANCEBLOCK_IMG',
                        'label' => $this->l('Main Block Image'),
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
                    'name' => 'submitvcmsAdvanceBlockMainTitleForm',
                ),
            ),
        );
    }

    protected function tvcmsAdvanceBlockMainBlockForm()
    {
        $tvcms_obj = new TvcmsAdvanceBlockStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $input = array();

        if ($show_fields['main_block_sub_title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE',
                    'label' => $this->l('Main Sub Title'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_block_title']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE',
                    'label' => $this->l('Main Title'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_block_short_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION',
                    'label' => $this->l('Short Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_block_description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION',
                    'label' => $this->l('Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_block_image']) {
            $input[] = array(
                        'type' => 'main_block_image_file',
                        'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG',
                        'label' => $this->l('Left block image'),
                );
        }

        if ($show_fields['main_block_back_image']) {
            $input[] = array(
                        'type' => 'main_block_back_image_file',
                        'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG',
                        'label' => $this->l('Right block image'),
                );
        }

        if ($show_fields['main_block_btn_caption']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION',
                    'label' => $this->l('Button Caption'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_block_link']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK',
                    'label' => $this->l('Link'),
                    'lang' => true,
                );
        }

        if ($show_fields['main_block_timer_status']) {
            $input[] = array(
                'col' => 7,
                'type' => 'text',
                'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE',
                'label' => $this->l('Main Block Timer Title'),
                'lang' => true,
            );
            $input[] = array(
                'col' => 7,
                'type' => 'date',
                'name' => 'TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE',
                'label' => $this->l('Main Block Timer End Date'),
                //'lang' => true,
            );
        }

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Image & Caption Block'),
                'icon' => 'icon-support',
                ),
                'input' => $input,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitvcmsAdvanceBlockMainBlockForm',
                ),
            ),
        );
    }


    protected function tvcmsAdvanceBlockForm()
    {
        $tvcms_obj = new TvcmsAdvanceBlockStatus();
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
                    'label' => $this->l('Short Description'),
                    'lang' => true,
                );
        }

        if ($show_fields['description']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => $this->l('Description'),
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                    'class' => 'rte',
                    'autoload_rte' => true,
                );
        }

        if ($show_fields['link']) {
            $input[] = array(
                    'col' => 7,
                    'type' => 'text',
                    'name' => 'link',
                    'label' => $this->l('Link'),
                    'desc' => $this->l('You Must Write Full Link. Ex:- https://www.demo.com/'),
                );
        }


        if ($show_fields['image']) {
            $input[] =  array(
                        'col' => 9,
                        'type' => 'tvcmsadvanceblock_img',
                        'name' => 'image',
                        'label' => $this->l('Image'),
                    );
        }

        if ($show_fields['status']) {
            $input[] =  array(
                        'type' => 'switch',
                        'label' => $this->l('Status'),
                        'name' => 'status',
                        'desc' => $this->l('Hide or Show Icons.'),
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
                'title' => $this->l('Advance Block'),
                'icon' => 'icon-support',
                ),
                'input' => $input,
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitvcmsAdvanceBlockForm',
                ),
            ),
        );
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
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
            // Main Title Information
            $tmp = Configuration::get('TVCMSADVANCEBLOCK_TITLE', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_SUB_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_IMG', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_IMG'][$lang['id_lang']] = $tmp;

            //  Main Block Information
            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_WIDTH_1'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG_HEIGHT_1'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_WIDTH_1'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG_HEIGHT_1'][$lang['id_lang']] = $tmp;

            $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE', $lang['id_lang']);
            $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_TITLE'][$lang['id_lang']] = $tmp;

            // $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE', $lang['id_lang']);
            // $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE'][$lang['id_lang']] = $tmp;
        }
        $tmp = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE');
        $fields['TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE'] = $tmp;

        $path = _MODULE_DIR_.$this->name."/views/img/";
        $this->context->smarty->assign("path", $path);
        
        return $fields;
    }

    public function showAdminResult()
    {
        $tvcms_obj = new TvcmsAdvanceBlockStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $default_lang_id = $this->context->language->id;

        $array_list = $this->showAdminData();

        if (empty($array_list)) {
            return '';
        }
        $this->context->smarty->assign('array_list', $array_list);
        $this->context->smarty->assign('show_fields', $show_fields);
        $this->context->smarty->assign('default_lang_id', $default_lang_id);

        return $this->display(__FILE__, "views/templates/admin/tvcmsadvanceblock_manage.tpl");
    }

    public function hookdisplayHeader()
    {
        //start countdown timer
        // $timerEndDate = Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_TIMER_END_DATE');
        // $timerEndDate = date("m/d/Y", strtotime($timerEndDate));
        // Media::addJsDef(array(
        //     'timerEndDate' => $timerEndDate
        // ));
        // $this->context->controller->addJS($this->_path.'views/js/timer/kinetic.js');
        // $this->context->controller->addJS($this->_path.'views/js/timer/jquery.final-countdown.js');
        //end

        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    public function hookDisplayTopColumn()
    {
        return $this->hookDisplayHome();
    }

    public function hookDisplayFooterBefore()
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

    public function hookDisplayHome()
    {
        $cookie = Context::getContext()->cookie;
        $id_lang = $cookie->id_lang;
        $result = array();
        $result = $this->showData();

        $tvcms_obj = new TvcmsAdvanceBlockStatus();
        $show_fields = $tvcms_obj->fieldStatusInformation();
        $main_heading = $tvcms_obj->fieldStatusInformation();
        if ($main_heading['main_status']) {
            $main_heading_data = array();
            $main_heading_data['title'] = Configuration::get('TVCMSADVANCEBLOCK_TITLE', $id_lang);
            $main_heading_data['short_desc'] = Configuration::get('TVCMSADVANCEBLOCK_SUB_DESCRIPTION', $id_lang);
            $main_heading_data['desc'] = Configuration::get('TVCMSADVANCEBLOCK_DESCRIPTION', $id_lang);
            $main_heading_data['image'] = Configuration::get('TVCMSADVANCEBLOCK_IMG', $id_lang);
            $main_heading = $this->getArrMainTitle($main_heading, $main_heading_data);
            $main_heading['data'] = $main_heading_data;
        }
        $this->context->smarty->assign('main_heading', $main_heading);
        $AdvanceBlockImgpath = _MODULE_DIR_.$this->name."/views/img/";
        $this->context->smarty->assign("AdvanceBlockImgpath", $AdvanceBlockImgpath);

        // if (Context::getContext()->getDevice() == 1) {
        //     $AdvanceBlockImgpath_bk = _MODULE_DIR_.$this->name."/views/img/";
        //     $this->context->smarty->assign("AdvanceBlockImgpath_bk", $AdvanceBlockImgpath_bk);
        // } else if (Context::getContext()->getDevice() == 2) {
        //     $AdvanceBlockImgpath_bk = _MODULE_DIR_.$this->name."/views/img/medium/";
        //     $this->context->smarty->assign("AdvanceBlockImgpath_bk", $AdvanceBlockImgpath_bk);
        // } else {
        //     $AdvanceBlockImgpath_bk = _MODULE_DIR_.$this->name."/views/img/small/";
        //     $this->context->smarty->assign("AdvanceBlockImgpath_bk", $AdvanceBlockImgpath_bk);
        // }
        $disArrResult = array();
        $disArrResult['path'] = _MODULE_DIR_.$this->name."/views/img/";
        
        $this->context->smarty->assign("show_fields", $show_fields);
        $this->context->smarty->assign('arr_result', $result);
        $this->context->smarty->assign('id_lang', $id_lang);
        $this->context->smarty->assign('dis_arr_result', $disArrResult);
        return $this->display(__FILE__, 'views/templates/front/display_home.tpl');
    }
}
