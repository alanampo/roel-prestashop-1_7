{*
* 2007-2022 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<div id="ph_social_preview_panel" class="ph_social_panel">
    <div class="ph_social_preview_wrapper button_size_{$PH_SL_BUTTON_SIZE|escape:'html':'UTF-8'} button_border_{$PH_SL_BUTTON_BORDER|escape:'html':'UTF-8'} button_type_flat_icon {if $PH_SL_HIDE_ON_MOBILE} hide_mobile{/if}" data-button-size="{$PH_SL_BUTTON_SIZE|escape:'html':'UTF-8'}" data-button-border="{$PH_SL_BUTTON_BORDER|escape:'html':'UTF-8'}">
        <h4 class="ph_social_preview_title"> {l s='Preview social buttons' mod='ph_social_links'}</h4>
        <h4 class="ph_social_link_title">{$PH_SL_LINK_TITLE|escape:'html':'UTF-8'}</h4>
        <ul class="ph_social_list">
            {foreach from=$socials key='key' item='social'}
                <li class="ph_social_item {$key|strtolower|escape:'html':'UTF-8'} {if !(isset($socials_link_enabled[$key]) && $socials_link_enabled[$key])} hide{/if}">
                    <a title="{$social.name|escape:'html':'UTF-8'}" href="{if isset($socials_link_value[$key])}{$socials_link_value[$key]|escape:'html':'UTF-8'}{/if}">
                        <i>{$social.svg nofilter}</i>
                        <span class="tooltip_title">{$social.name|escape:'html':'UTF-8'}</span>
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>