{*
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
*}

<div class="btn-group-action">
    <div class="btn-group">
        <a href="{$productAdminLink|escape:'html':'UTF-8'}" title="" class="btn tooltip-link product-edit">
            <i class="material-icons">mode_edit</i>
        </a>
        <button class="btn btn-link dropdown-toggle dropdown-toggle-split product-edit no-rotate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
        <div class="dropdown-menu dropdown-menu-right" style="">
            <a class="dropdown-item product-edit" href="{$productLink|escape:'html':'UTF-8'}" target="_blank">
                <i class="material-icons">remove_red_eye</i> {l s='Preview' d='Modules.Chatgptcontentgenerator.Admin'}</a>
            <a class="dropdown-item product-edit delete-spin-off" href="#" data-spinoffid="{$productId|escape:'html':'UTF-8'}">
                <i class="material-icons">delete</i> {l s='Delete' d='Modules.Chatgptcontentgenerator.Admin'}</a>
        </div>
    </div>
</div>
