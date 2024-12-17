<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2019
*  @license   Single domain
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_3_0($module)
{
    $return = array();

    mkdir(_PS_IMG_DIR_.'quote', 0777, true);
    $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fmm_quote_fee` (
                    `id_productquotation` int(10) NOT NULL auto_increment,
                    `id_cart` int(10) NOT NULL,
                    `id_product` int(10) NOT NULL,
                    PRIMARY KEY (`id_productquotation`, `id_cart`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
    
    $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quote_data_file` (
                    `file_id` int(10) NOT NULL auto_increment,
                    `id_quotation` int(10) NOT NULL,
                    `id_customer` int(10) NOT NULL,
                    `file` varchar(255) NOT NULL,
                    PRIMARY KEY (`file_id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
    
    return true;
}
