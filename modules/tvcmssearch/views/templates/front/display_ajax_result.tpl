{**
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
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2022 PrestaShop SA
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
    {strip}
    <div class="tvcmssearch-dropdown">
        <div class="tvsearch-dropdown-close-wrapper tvsearch-dropdown-close">
            <i class="material-icons">&#xe5cd;</i>
        </div>
        <div class="tvsearch-dropdown-total-wrapper">
            <div class="tvsearch-dropdown-total">{l s='Search Result:' mod='tvcmssearch'} ({$result_data.total})</div>
        </div>
        <div class="tvsearch-all-dropdown-wrapper">
            {$result_data.html nofilter}
        </div>
        <div class="tvsearch-more-search-wrapper">
            <button class="tvsearch-more-search tvall-inner-btn btn"><span>{l s='More Result' mod='tvcmssearch'}</span></button>
        </div>
    </div>
    {/strip}