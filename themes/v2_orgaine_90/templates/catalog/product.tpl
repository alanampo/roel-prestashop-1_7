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
    {extends file=$layout}
    {block name='head_seo' prepend}
    <link rel="canonical" href="{$product.canonical_url}">
    {/block}
    {block name='head' append}
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta property="og:type" content="product">
    <meta property="og:url" content="{$urls.current_url}">
    <meta property="og:title" content="{$page.meta.title}">
    <meta property="og:site_name" content="{$shop.name}">
    <meta property="og:description" content="{$page.meta.description}">
    <meta property="og:image" content="{$product.cover.large.url}">
    <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
    <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
    <meta property="product:price:amount" content="{$product.price_amount}">
    <meta property="product:price:currency" content="{$currency.iso_code}">
    {if isset($product.weight) && ($product.weight != 0)}
    <meta property="product:weight:value" content="{$product.weight}">
    <meta property="product:weight:units" content="{$product.weight_unit}">
    {/if}
    {/block}
    {block name='head_microdata_special'}
        {include file='_partials/microdata/product-jsonld.tpl'}
    {/block}
    {block name='content'}
    <div id="main" itemscope itemtype="https://schema.org/Product">
        <meta itemprop="url" content="{$product.url}">
        <div class="tvproduct-page-wrapper">
            {assign var="prod_layout" value="../catalog/tv-{$TVCMSPRODUCTCUSTOM_LAYOUT}.tpl"}
            {include file="$prod_layout"}
            {block name='product_tabs'}
            <div class="tabs tvproduct-description-tab clearfix">
                <ul class="nav nav-tabs" role="tablist">
                    {if $product.description}
                    <li class="nav-item" role="presentation">
                        <a class="nav-link{if $product.description} active{/if}" data-toggle="tab" href="#description" role="tab" aria-controls="description" {if $product.description} aria-selected="true" {/if}> {l s='Description' d='Shop.Theme.Catalog' } </a> 
                    </li>
                    {/if}
                    <li class="nav-item" role="presentation">
                        <a class="nav-link{if !$product.description} active{/if}" data-toggle="tab" href="#product-details" role="tab" aria-controls="product-details" {if !$product.description} aria-selected="true" {/if}> {l s='Product Details' d='Shop.Theme.Catalog' } </a> 
                    </li> 
                    {if $product.attachments} 
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#attachments" role="tab" aria-controls="attachments">
                            {l s='Attachments' d='Shop.Theme.Catalog'}
                        </a>
                    </li>
                    {/if}
                    {foreach from=$product.extraContent item=extra key=extraKey}
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#extra-{$extraKey}" role="tab" aria-controls="extra-{$extraKey}">
                            {$extra.title}
                        </a>
                    </li>
                    {/foreach}
                    {* start product comment tab hook *}
                    {hook h='displayProductListReviewsTab'}
                    {* End product comment tab hook *}
                </ul>
                <div class="tab-content clearfix" id="tab-content">
                    <div class="tab-pane fade in {if $product.description} active {/if}" id="description" role="tabpanel">
                        {block name='product_description'}
                        <div class="product-description cms-description">{$product.description nofilter}</div>
                        {/block}
                    </div>
                    {block name='product_details'}
                    {include file='catalog/_partials/product-details.tpl'}
                    {/block}
                    {block name='product_attachments'}
                    {if $product.attachments}
                    <div class="tab-pane fade in" id="attachments" role="tabpanel">
                        <div class="product-attachments">
                            <p class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</p>
                            {foreach from=$product.attachments item=attachment}
                            <div class="attachment">
                                <h4><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
                                <p>{$attachment.description}</p>
                                <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                    {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                                </a>
                            </div>
                            {/foreach}
                        </div>
                    </div>
                    {/if}
                    {/block}
                    {foreach from=$product.extraContent item=extra key=extraKey}
                    <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" role="tabpanel" {foreach $extra.attr as $key=> $val} {$key}="{$val}"{/foreach}>
                        {$extra.content nofilter}
                    </div>
                    {/foreach}
                    {* start product comment tab content hook *}
                    {hook h='displayProductListReviewsTabContent' product=$product}
                    {* End product comment tab content hook *}
                </div>
            </div>
            {/block}
        </div>
        {block name='product_accessories'}
        {if $accessories}
        <div class="tvcmslike-product container-fluid">
            <div class='tvlike-product-wrapper-box container'>
                <div class='tvcmsmain-title-wrapper'>
                    <div class="tvcms-main-title">
                        <div class='tvmain-title'>
                            <h2>{l s='You might also like' d='Shop.Theme.Catalog'}</h2>
                        </div>
                    </div>
                </div>
                <div class="tvlike-product">
                    <div class="products owl-theme owl-carousel tvlike-product-wrapper tvproduct-wrapper-content-box">
                        {foreach $accessories as $product}
                        {include file="catalog/_partials/miniatures/product.tpl" product=$product tv_product_type="like_product"}
                        {/foreach}
                    </div>
                </div>
                <div class='tvlike-pagination-wrapper tv-pagination-wrapper'>
                    <div class="tvcmslike-next-pre-btn tvcms-next-pre-btn">
                        <div class="tvcmslike-prev tvcmsprev-btn" data-parent="tvcmslike-product"><i class='material-icons'>&#xe317;</i></div>
                        <div class="tvcmslike-next tvcmsnext-btn" data-parent="tvcmslike-product"><i class='material-icons'>&#xe317;</i></div>
                    </div>
                </div>
            </div>
        </div>
        {/if}
        {/block}
        {block name='product_footer'}
        {hook h='displayFooterProduct' product=$product category=$category}
        {/block}
        {block name='product_images_modal'}
        {include file='catalog/_partials/product-images-modal.tpl'}
        {/block}
        {block name='page_footer_container'}
        {if Configuration::get('TVCMSCUSTOMSETTING_PRODUCT_PAGE_BOTTOM_STICKY_STATUS')}
        <div class="tvfooter-product-sticky-bottom">
            <div class="container">
                <div class="tvflex-items">
                    <div class="tvproduct-image-title-price">
                        {if $product.cover}
                        <div class="product-image">
                            <img src="{$product.cover.bySize.large_default.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" itemprop="image" width="{$product.cover.bySize.large_default.width}" height="{$product.cover.bySize.large_default.height}" loading="lazy">
                        </div>
                        <div class="tvtitle-price">
                            {block name='page_header'}
                            <h1 class="h1" itemprop="name">{block name='page_title'}{$product.name}{/block}</h1>
                            {/block}
                            {block name='product_prices'}
                            {include file='catalog/_partials/product-prices.tpl'}
                            {/block}
                        </div>
                        {/if}
                    </div>
                    <div>
                        <div class="product-actions" id="bottom_sticky_data"></div>
                    </div>
                    {* <div class="product-information">
                        <div class="product-actions">
                            {block name='product_buy'}
                            <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                                <input type="hidden" name="token" value="{$static_token}">
                                <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                                <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id">
                                {block name='product_variants'}
                                {include file='catalog/_partials/product-variants.tpl'}
                                {/block}
                                {block name='product_discounts'}
                                {include file='catalog/_partials/product-discounts.tpl'}
                                {/block}
                                {block name='product_add_to_cart'}
                                {include file='catalog/_partials/product-add-to-cart.tpl'}
                                {block}
                                {block name='product_refresh'}{/block}
                            </form>
                            {/block}
                        </div>
                    </div> *}
                </div>
            </div>
        </div>
        {/if}
        <footer class="page-footer">
            {block name='page_footer'}
            <!-- Footer content -->
            {/block}
        </footer>
        {/block}
    </div>
    {/block}
{/strip}