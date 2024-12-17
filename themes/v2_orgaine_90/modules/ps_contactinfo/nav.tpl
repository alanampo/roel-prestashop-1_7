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
<div id="_desktop_contact_link" class="">
    <div id="contact-link">
        <a href="tel:{$contact_infos.phone}">
             {* <i class='material-icons'>&#xe0b0;</i> *}
             <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20.1px" height="20px" viewBox="0 0 50.1 50" style="enable-background:new 0 0 50.1 50;" xml:space="preserve">
            <path id="XMLID_25_" style="fill:#222222;" d="M0,11.3c0.2-0.6,0.2-1.3,0.5-1.8c0.6-1,1.2-1.9,2-2.8C4.2,4.9,6,3.1,7.8,1.3c1.7-1.7,4.7-1.7,6.4,0c2.5,2.5,5,5.1,7.4,7.6c1.4,1.5,2,3.1,1,5.2c-0.2,0.4-0.5,0.8-0.9,1.2c-1.1,1.1-2.2,2.3-3.4,3.4c-0.3,0.3-0.3,0.5-0.2,0.8c2.2,4.8,5.7,8.5,10.2,11.2c0.7,0.5,1.5,0.8,2.3,1.3c0.3,0.2,0.5,0.2,0.8-0.1c1.1-1.2,2.3-2.3,3.5-3.5c1.5-1.4,3.8-1.6,5.5-0.5c0.3,0.2,0.6,0.6,0.9,0.8c2.2,2.2,4.3,4.4,6.5,6.5c1,1,2,2,2.2,3.5c0,0.3,0,0.6,0,0.9c-0.2,1.5-1.1,2.5-2.2,3.5c-1.6,1.6-3.2,3.2-4.8,4.8c-0.4,0.3-0.8,0.6-1.2,0.9c-0.9,0.6-2,0.9-3.1,1.1c-0.3,0-0.6,0-1,0c-1-0.3-2.1-0.5-3-0.9c-8-3.3-15.1-7.8-21.1-14C7.5,28.5,2.6,21.1,0,12.4C0,12,0,11.7,0,11.3z M37.9,30.8c-0.1,0.2-0.2,0.3-0.4,0.5c-1.7,1.7-3.4,3.4-5.2,5.2c-0.2,0.3-0.4,0.3-0.8,0.2c-3.1-1.3-5.9-3-8.6-5c-4.5-3.4-7.5-7.8-9.5-13.1c-0.1-0.3-0.1-0.5,0.1-0.7c1.8-1.7,3.5-3.5,5.3-5.2c0.3-0.3,0.3-0.5,0-0.8c-2.5-2.5-4.9-5-7.4-7.6c-0.5-0.5-0.7-0.5-1.3,0C8.4,6.2,6.6,8.1,4.7,9.9c-0.8,0.8-1.1,1.7-0.7,2.8c3.3,8.6,8.3,16.1,15.1,22.3c5.3,4.7,11.3,8.2,17.8,10.8c1.3,0.5,2.5,0.3,3.5-0.7c1.8-1.8,3.6-3.6,5.5-5.5c0.1-0.1,0.2-0.3,0.3-0.4C43.5,36.5,40.7,33.7,37.9,30.8z"></path></svg>
           <div class="tvheader-contact-wrapper">
            <span class="tvcall-on">
                {l s='24/7 Support' d='Shop.Theme.Actions'}&nbsp;
            </span>
            {if $contact_infos.phone}
            {* [1][/1] is for a HTML tag. *}
            {l
            s='[1]%phone%[/1]'
            sprintf=[
            '[1]' => '<span>',
                '[/1]' => '</span>',
            '%phone%' => $contact_infos.phone
            ]
            d='Shop.Theme.Global'
            }
            {/if}
           </div>
        </a>
    </div>
</div>
{/strip}

