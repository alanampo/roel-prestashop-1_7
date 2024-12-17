{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
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
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{strip}
<div class="tvcms-header-currency tvheader-currency-wrapper" id="tvcmsdesktop-currency-selector">
    {* <span class="tv-currency-lable">{l s='Currency:' d='Shop.Theme.Global'}</span> *}
    <button class="btn-unstyle tv-currency-btn">
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="18px" height="18px" viewBox="0 0 51.8 51.8" style="enable-background:new 0 0 51.8 51.8;" xml:space="preserve">
            <path style="fill:#000000;" d="M25.9,51.8c-6.9,0-13.4-2.7-18.3-7.6C2.7,39.3,0,32.8,0,25.9S2.7,12.5,7.6,7.6C12.5,2.7,19,0,25.9,0s13.4,2.7,18.3,7.6c4.9,4.9,7.6,11.4,7.6,18.3s-2.7,13.4-7.6,18.3C39.3,49.1,32.8,51.8,25.9,51.8L25.9,51.8z M25.9,3.2C13.4,3.2,3.2,13.4,3.2,25.9s10.2,22.7,22.7,22.7s22.7-10.2,22.7-22.7S38.4,3.2,25.9,3.2L25.9,3.2z M25.9,3.2"></path>
            <path style="fill:#000000;" d="M25.9,24.3c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4c0,0.9,0.7,1.6,1.6,1.6c0.9,0,1.6-0.7,1.6-1.6c0-3.5-2.4-6.4-5.7-7.1v-1.8c0-0.9-0.7-1.6-1.6-1.6c-0.9,0-1.6,0.7-1.6,1.6v1.8c-3.2,0.7-5.7,3.6-5.7,7.1c0,4,3.3,7.3,7.3,7.3c2.2,0,4,1.8,4,4c0,2.2-1.8,4-4,4s-4-1.8-4-4c0-0.9-0.7-1.6-1.6-1.6c-0.9,0-1.6,0.7-1.6,1.6c0,3.5,2.4,6.4,5.7,7.1v1.8c0,0.9,0.7,1.6,1.6,1.6c0.9,0,1.6-0.7,1.6-1.6v-1.8c3.2-0.7,5.7-3.6,5.7-7.1C33.2,27.6,29.9,24.3,25.9,24.3L25.9,24.3z M25.9,24.3"></path>
        </svg>
        <span class="tv-currency-span">{$current_currency.iso_code} {* {$current_currency.sign} *} </span>
        <i class="material-icons expand-more">&#xe313;</i>
    </button>
    <ul class="tv-currency-dropdown tv-dropdown">
        {foreach from=$currencies item=currency}
        <li {if $currency.current} class="current" {/if}> <a title="{$currency.name}" rel="nofollow" href="{$currency.url}">{$currency.iso_code} {$currency.sign}</a>
        </li>
        {/foreach}
    </ul>
</div>
{/strip}