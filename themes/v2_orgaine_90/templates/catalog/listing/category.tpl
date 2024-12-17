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
    {extends file='catalog/listing/product-list.tpl'}
    {block name='product_list_header'}
    {if Configuration::get('TVCMSCAT_BANNER_STATUS')}
    <div class="block-category card card-block clearfix tv-category-block-wrapper">
        {if $category.image.large.url}
        <div class="tv-category-cover">
            <img src="{$category.image.large.url}" width="{$category.image.large.width}" height="{$category.image.large.height}" alt="{if !empty($category.image.legend)}{$category.image.legend}{else}{$category.name}{/if}" class="tv-img-responsive" loading="lazy" />
        </div>
        {/if}
        {if !empty($category.image.large.url)}
        <div class="tv-all-page-main-title-wrapper">
            <div class="tv-all-page-main-title">{$category.name}</div>
        </div>
        {/if}
        {if $category.description}
        <div id="category-description" class="text-muted">{$category.description nofilter}</div>
        {/if}
    </div>
    {/if}
    {if isset($subcategories) && count($subcategories) > 0}
    {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories) }
    <div class='tv-category-main-div clearfix'>
        <div class="tv-sub-category-title-wrapper">
            <div class="tv-sub-category-title">{l s='Subcategory' d='Shop.Theme.Catalog'}</div>
        </div>
        <div class="tvcategory-name-image">
            {foreach from=$subcategories item=subcategory}
            <div class="tv-sub-category-wrapper">
                <div class="tv-sub-category-inner">
                    <div class="tv-category-image">
                        <a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
                            {if $subcategory.image}
                            <img class="replace-2x tv-img-responsive" width="{$subcategory.image.bySize.small_default.width}" height="{$subcategory.image.bySize.small_default.height}" src="{$urls.img_ps_url}c/{$subcategory.id_category}_thumb.jpg" alt="{$subcategory.name|escape:'html':'UTF-8'}" loading="lazy" />
                            {else}
                            <img class="replace-2x tv-img-responsive" src="{$urls.img_cat_url}{$language.iso_code}-default-sub_category_default.jpg" alt="{$subcategory.name|escape:'html':'UTF-8'}" loading="lazy" />
                            {/if}
                        </a>
                    </div>
                    <div class="tvcategory-name">
                        <a class="category-name" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|escape:'html':'UTF-8'}</a>
                        {*{if $subcategory.description}
                        <div class="cat_desc">{$subcategory.description}</div>
                        {/if}*}
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
    {/if}
    {/if}
    {/block}
    {/strip}