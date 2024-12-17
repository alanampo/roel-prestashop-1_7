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

function upgrade_module_2_2_0($module)
{
    $return = array();

    $fortab = new Tab();
    $fortab->class_name = 'AdminQuoteFields';
    $fortab->id_parent = Tab::getIdFromClassName('AdminQuotes');
    $fortab->module = $module->name;
    $fortab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = 'Manage Form Fields';

    $fortab->add();

    $return = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields(
        `id_fmm_quote_fields`       int(11) unsigned NOT NULL auto_increment,
        `field_type`            enum(\'text\',\'textarea\',\'date\',\'boolean\',\'multiselect\',\'select\',\'checkbox\',\'radio\',\'message\',\'image\',\'attachment\') default \'text\',
        `field_validation`      varchar(255) default NULL,
        `position`              tinyint(4) default 0,
        `assoc_shops`           varchar(255) default ' . (string) Context::getContext()->shop->id . ',
        `value_required`        tinyint(1) default NULL,
        `editable`              tinyint(1) default 1,
        `extensions`            varchar(128) DEFAULT \'jpg\',
        `attachment_size`       DECIMAL(10,2) NOT NULL DEFAULT \'2.0\',
        `alert_type`            varchar(30) default NULL,
        `show_customer`         tinyint(1) default NULL,
        `show_email`            tinyint(1) default NULL,
        `show_admin`            tinyint(1) default NULL,
        `active`                tinyint(1) default NULL,
        `dependant`             tinyint(1) default \'0\',
        `dependant_field`       int(11) default \'0\',
        `dependant_value`       int(11) default \'0\',
        `limit`                 int(11) default \'0\',
        `id_heading`            int(11) default \'0\',
        `created_time`          datetime default NULL,
        `update_time`           datetime default NULL,
        PRIMARY KEY             (`id_fmm_quote_fields`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields_lang(
        `id_fmm_quote_fields`       int(11) NOT NULL auto_increment,
        `id_lang`               int(11) NOT NULL,
        `field_name`            varchar(255) default NULL,
        `default_value`         varchar(255) default NULL,
        PRIMARY KEY             (`id_fmm_quote_fields`,`id_lang`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields_values(
        `field_value_id`        int(11) NOT NULL auto_increment,
        `id_fmm_quote_fields`       int(11) NOT NULL,
        PRIMARY KEY             (`field_value_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_fields_values_lang(
        `field_value_id`        int(11) NOT NULL,
        `id_lang`               int(11) NOT NULL DEFAULT ' . (int) Configuration::get('PS_LANG_DEFAULT') . ',
        `field_value`           text,
        PRIMARY KEY             (`field_value_id`, `id_lang`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fmm_quote_userdata(
        `value_id`              int(10) unsigned NOT NULL auto_increment,
        `id_fmm_quote_fields`       int(10) unsigned default NULL,
        `id_customer`           int(10) unsigned default NULL,
        `id_guest`              int(10) unsigned default 0,
        `field_value_id`        mediumtext,
        `value`                 mediumtext,
        `id_quote`              int(10) unsigned default 0,
        PRIMARY KEY             (`value_id`),
        UNIQUE KEY `uniq`       (`id_fmm_quote_fields`,`id_customer`,`id_quote`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

    return true;
}
