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

// session_start();
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once(_PS_MODULE_DIR_.'tvcmsblog/tvcmsblog.php');
$commentobj = new TvcmsCommentClass();
$results = array();
$results['error'] = array();
if (Tools::getValue('name') && !empty(Tools::getValue('name'))) {
    $commentobj->name = Tools::getValue('name');
} else {
    $results['error'][] = "Name Is Required.<br>";
}
if (Tools::getValue('email') && !empty(Tools::getValue('email')) && Validate::isEmail(Tools::getValue('email'))) {
    $commentobj->email = Tools::getValue('email');
} else {
    $results['error'][] = "Valid Email Address Is Required.<br>";
}
$tmp = Tools::getValue('website');
$commentobj->website = ($tmp && !empty($tmp)) ? Tools::getValue('website') : '#';
if (Tools::getValue('subject') && !empty(Tools::getValue('subject'))) {
    $commentobj->subject = Tools::getValue('subject');
} else {
    $results['error'][] = "Subject Is Required.<br>";
}
if (Tools::getValue('content') && !empty(Tools::getValue('content'))) {
    $commentobj->content = Tools::getValue('content');
} else {
    $results['error'][] = "Comment Content Is Required.<br>";
}
$tmp = Tools::getValue('id_parent');
$commentobj->id_parent = ($tmp && !empty($tmp)) ? (int)Tools::getValue('id_parent') : 0;

$tmp = Tools::getValue('id_post');
$commentobj->id_post = ($tmp && !empty($tmp)) ? (int)Tools::getValue('id_post') : 0;
if ($results['error'] && !empty($results['error'])) {
    die(Tools::jsonEncode($results));
} else {
    $commentobj->id_customer    =   0;
    $commentobj->id_guest   =   0;
    $commentobj->position   =   0;
    $commentobj->uniqueid   =   'abc';
    $commentobj->active     =   1;
    $commentobj->created    =   date("Y-m-d h:i:s");
    $commentobj->updated    =   date("Y-m-d h:i:s");
    if ($commentobj->add()) {
        $results['success'][] = "Successfully Comment Added.<br>";
        $results['results'] = $commentobj;
        die(Tools::jsonEncode($results));
    } else {
        $results['error'][] = "Something Wrong Please Try Again ! .<br>";
        die(Tools::jsonEncode($results));
    }
}
