<?php
/**
* Product Quotation
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules All right reserved
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category  front_office_features
* @package   productquotation
*/

class Mess extends ObjectModel
{
    public $id_quote_messages;
    public $id_quotation;
    public $author;

    public static $definition = array(
        'table' => 'quote_messages',
        'primary' => 'id_quote_messages',
        'multilang' => false,
        'fields' => array(
            'id_quote_messages' => array('type' => self::TYPE_INT),
            'id_quotation' => array('type' => self::TYPE_INT),
            'author' => array('type' => self::TYPE_STRING)
        ),
    );

    public static function getQuotationIdByMessage($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `id_quotation`
        FROM `'._DB_PREFIX_.'quote_messages`
        WHERE `id_quote_messages` = '.(int)$id);
        return $result['id_quotation'];
    }

    public function getUserThreads($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT `message`, `author`, `date`
        FROM `'._DB_PREFIX_.'quote_messages`
        WHERE `id_quotation` = '.(int)$id);
        return $result;
    }

    public function saveMessage($id_quotation, $message)
    {
        return Db::getInstance()->execute('
        INSERT INTO '._DB_PREFIX_.'quote_messages (`id_quotation`, `message`, `author`)
        VALUES('.(int)$id_quotation.', "'.pSQL($message).'", 1)
        ');
    }

    public static function getUserInfo($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `key`, `email`
        FROM `'._DB_PREFIX_.'productquotation`
        WHERE `id_productquotation` = '.(int)$id);
        return $result;
    }

    public static function getNextMessageId()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `id_quote_messages`
        FROM `'._DB_PREFIX_.'quote_messages` ORDER BY `id_quote_messages` DESC');
        return $result['id_quote_messages'] + 1;
    }

    public static function deleteByIdQuote($id_quotation)
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
            'quote_messages',
            'id_quotation = ' . (int) $id_quotation
        );
    }
}
