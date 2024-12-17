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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{strip}
{assign var='num_of_rec' value='1'}
{assign var='TVCMSMULTIBANNER2_IMAGE_NAME_1' value='TVCMSMULTIBANNER2_IMAGE_NAME_'|cat:$num_of_rec}
{assign var='num_of_rec' value='2'}
{assign var='TVCMSMULTIBANNER2_IMAGE_NAME_2' value='TVCMSMULTIBANNER2_IMAGE_NAME_'|cat:$num_of_rec}
{assign var='num_of_rec' value='3'}
{assign var='TVCMSMULTIBANNER2_IMAGE_NAME_3' value='TVCMSMULTIBANNER2_IMAGE_NAME_'|cat:$num_of_rec}
{if !empty($data[$TVCMSMULTIBANNER2_IMAGE_NAME_1][$language_id]) || !empty($data[$TVCMSMULTIBANNER2_IMAGE_NAME_2][$language_id]) || !empty($data[$TVCMSMULTIBANNER2_IMAGE_NAME_3][$language_id])}
    <div class="tvcmsmultibanners tvmultibanner-second container-fluid">
        <div class="container">
            <div class="tvmultibanners row">
            {assign var='num_of_rec' value='1'}
            {assign var='TVCMSMULTIBANNER2_IMAGE_NAME' value='TVCMSMULTIBANNER2_IMAGE_NAME_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION' value='TVCMSMULTIBANNER2_CAPTION_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_WIDTH' value='TVCMSMULTIBANNER2_IMAGE_WIDTH_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_HEIGHT' value='TVCMSMULTIBANNER2_IMAGE_HEIGHT_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION_SIDE' value='TVCMSMULTIBANNER2_CAPTION_SIDE_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_LINK' value='TVCMSMULTIBANNER2_LINK_'|cat:$num_of_rec}
            {if $data[$TVCMSMULTIBANNER2_IMAGE_NAME]}
            <div class="tvmultibanner1-wrapper col-xl-4 col-lg-4 col-md-4 col-xs-12 tvmultibanner-{$num_of_rec|escape:'htmlall':'UTF-8'}">
                <a class="tvbanner-hover-wrapper" href="{$data[$TVCMSMULTIBANNER2_LINK][$language_id]|escape:'htmlall':'UTF-8'}" title="{l s='Multibanner' mod='tvcmsmultibanner2'}{$num_of_rec|escape:'htmlall':'UTF-8'}">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/{$data[$TVCMSMULTIBANNER2_IMAGE_NAME][$language_id]|escape:'htmlall':'UTF-8'}"  class="img-responsive tv-img-responsive" alt="" width="{$data[$TVCMSMULTIBANNER2_IMAGE_WIDTH][$language_id]|escape:'htmlall':'UTF-8'}" height="{$data[$TVCMSMULTIBANNER2_IMAGE_HEIGHT][$language_id]|escape:'htmlall':'UTF-8'}" loading="lazy"/>
                {if $data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id] != 'none'}
                <div class="{$data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id]|escape:'htmlall':'UTF-8'} tvmultibanners-content">
                    {$data[$TVCMSMULTIBANNER2_CAPTION][$language_id] nofilter}
                </div>
                {/if}
                </a>
            </div>
            {/if}

            <div class="col-xl-8 col-md-8 col-xs-12 tvmultibanner2-center-wrapper">
            {assign var='num_of_rec' value='2'}
            {assign var='TVCMSMULTIBANNER2_IMAGE_NAME' value='TVCMSMULTIBANNER2_IMAGE_NAME_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION' value='TVCMSMULTIBANNER2_CAPTION_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_WIDTH' value='TVCMSMULTIBANNER2_IMAGE_WIDTH_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_HEIGHT' value='TVCMSMULTIBANNER2_IMAGE_HEIGHT_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION_SIDE' value='TVCMSMULTIBANNER2_CAPTION_SIDE_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_LINK' value='TVCMSMULTIBANNER2_LINK_'|cat:$num_of_rec}
            {if $data[$TVCMSMULTIBANNER2_IMAGE_NAME]}
                <div class="tvmultibanner2-wrapper col-xl-6 col-md-6 col-xs-12 tvmultibanner-{$num_of_rec|escape:'htmlall':'UTF-8'}">
                    <a class='tvbanner-hover-wrapper' href="{$data[$TVCMSMULTIBANNER2_LINK][$language_id]|escape:'htmlall':'UTF-8'}" title="{l s='Multibanner' mod='tvcmsmultibanner2'}{$num_of_rec|escape:'htmlall':'UTF-8'}">
                        <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/{$data[$TVCMSMULTIBANNER2_IMAGE_NAME][$language_id]|escape:'htmlall':'UTF-8'}" class="img-responsive tv-img-responsive" alt="" width="{$data[$TVCMSMULTIBANNER2_IMAGE_WIDTH][$language_id]|escape:'htmlall':'UTF-8'}" height="{$data[$TVCMSMULTIBANNER2_IMAGE_HEIGHT][$language_id]|escape:'htmlall':'UTF-8'}" loading="lazy"/>
                    {if $data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id] != 'none'}
                    <div class="{$data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id]|escape:'htmlall':'UTF-8'} tvmultibanners-content">
                        {$data[$TVCMSMULTIBANNER2_CAPTION][$language_id] nofilter}
                    </div>
                    {/if}
                    </a>
                </div>
            {assign var='num_of_rec' value='3'}
            {assign var='TVCMSMULTIBANNER2_IMAGE_NAME' value='TVCMSMULTIBANNER2_IMAGE_NAME_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION' value='TVCMSMULTIBANNER2_CAPTION_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_WIDTH' value='TVCMSMULTIBANNER2_IMAGE_WIDTH_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_HEIGHT' value='TVCMSMULTIBANNER2_IMAGE_HEIGHT_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION_SIDE' value='TVCMSMULTIBANNER2_CAPTION_SIDE_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_LINK' value='TVCMSMULTIBANNER2_LINK_'|cat:$num_of_rec}
            {if $data[$TVCMSMULTIBANNER2_IMAGE_NAME]}
            <div class="tvmultibanner3-wrapper col-xl-6 col-md-6 col-xs-12 tvmultibanner-{$num_of_rec|escape:'htmlall':'UTF-8'}">
                <a class="tvbanner-hover-wrapper" href="{$data[$TVCMSMULTIBANNER2_LINK][$language_id]|escape:'htmlall':'UTF-8'}" title="{l s='Multibanner' mod='tvcmsmultibanner2'}{$num_of_rec|escape:'htmlall':'UTF-8'}">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/{$data[$TVCMSMULTIBANNER2_IMAGE_NAME][$language_id]|escape:'htmlall':'UTF-8'}"  class="img-responsive tv-img-responsive" alt="" width="{$data[$TVCMSMULTIBANNER2_IMAGE_WIDTH][$language_id]|escape:'htmlall':'UTF-8'}" height="{$data[$TVCMSMULTIBANNER2_IMAGE_HEIGHT][$language_id]|escape:'htmlall':'UTF-8'}" loading="lazy"/>  
                {if $data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id] != 'none'}
                <div class="{$data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id]|escape:'htmlall':'UTF-8'} tvmultibanners-content">
                    {$data[$TVCMSMULTIBANNER2_CAPTION][$language_id] nofilter}
                </div>
                {/if}
                </a>
            </div>
            {/if}
            {/if}

            {assign var='num_of_rec' value='4'}
            {assign var='TVCMSMULTIBANNER2_IMAGE_NAME' value='TVCMSMULTIBANNER2_IMAGE_NAME_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION' value='TVCMSMULTIBANNER2_CAPTION_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_WIDTH' value='TVCMSMULTIBANNER2_IMAGE_WIDTH_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_IMAGE_HEIGHT' value='TVCMSMULTIBANNER2_IMAGE_HEIGHT_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_CAPTION_SIDE' value='TVCMSMULTIBANNER2_CAPTION_SIDE_'|cat:$num_of_rec}
            {assign var='TVCMSMULTIBANNER2_LINK' value='TVCMSMULTIBANNER2_LINK_'|cat:$num_of_rec}
            {if $data[$TVCMSMULTIBANNER2_IMAGE_NAME]}
            <div class="tvmultibanner4-wrapper col-xl-12 col-md-12 col-xs-12  tvmultibanner-{$num_of_rec|escape:'htmlall':'UTF-8'}">
                <a class="tvbanner-hover-wrapper" href="{$data[$TVCMSMULTIBANNER2_LINK][$language_id]|escape:'htmlall':'UTF-8'}" title="{l s='Multibanner' mod='tvcmsmultibanner2'}{$num_of_rec|escape:'htmlall':'UTF-8'}">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/{$data[$TVCMSMULTIBANNER2_IMAGE_NAME][$language_id]|escape:'htmlall':'UTF-8'}"  class="img-responsive tv-img-responsive" alt="" width="{$data[$TVCMSMULTIBANNER2_IMAGE_WIDTH][$language_id]|escape:'htmlall':'UTF-8'}" height="{$data[$TVCMSMULTIBANNER2_IMAGE_HEIGHT][$language_id]|escape:'htmlall':'UTF-8'}" loading="lazy"/>	
                {if $data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id] != 'none'}
                <div class="{$data[$TVCMSMULTIBANNER2_CAPTION_SIDE][$language_id]|escape:'htmlall':'UTF-8'} tvmultibanners-content">
                    {$data[$TVCMSMULTIBANNER2_CAPTION][$language_id] nofilter}
                </div>
                {/if}
                </a>
            </div>
            {/if}
            </div>
            </div>
        </div>
    </div>  
{/if}
{/strip}