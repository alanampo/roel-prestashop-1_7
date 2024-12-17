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
<div class="container blog-related-product mt-2 mb-2">
  <div class="row m-0">
    <h4>{l s='Related product' d='Modules.Chatgptcontentgenerator.Blogpost'}</h4>
  </div>
  <div class="row m-0">
    <div class="col-md-6 col-sm-6 hidden-xs-down">
      {block name='product_cover_thumbnails'}
        {include file='catalog/_partials/product-cover-thumbnails.tpl'}
      {/block}
      {* <div class="arrows js-arrows">
        <i class="material-icons arrow-up js-arrow-up">&#xE316;</i>
        <i class="material-icons arrow-down js-arrow-down">&#xE313;</i>
      </div> *}
    </div>
    <div class="col-md-6 col-sm-6">
      <a href="{$product.url|escape:'html':'UTF-8'}">
        <h1 class="h1">{$product.name|escape:'html':'UTF-8'}</h1>
      </a>
      {block name='product_prices'}
        {include file='catalog/_partials/product-prices.tpl'}
      {/block}
      {block name='product_description_short'}
        <div id="product-description-short">{$product.description_short nofilter}</div>
      {/block}
      {block name='product_buy'}
        <div class="product-actions js-product-actions">
          <form action="{$urls.pages.cart|escape:'html':'UTF-8'}" method="post" id="add-to-cart-or-refresh">
            <input type="hidden" name="token" value="{$static_token|escape:'html':'UTF-8'}">
            <input type="hidden" name="id_product" value="{$product.id|intval}" id="product_page_product_id">
            <input type="hidden" name="id_customization" value="{$product.id_customization|intval}"
              id="product_customization_id" class="js-product-customization-id">
            {block name='product_variants'}
              {include file='catalog/_partials/product-variants.tpl'}
            {/block}

            {block name='product_add_to_cart'}
              {include file='catalog/_partials/product-add-to-cart.tpl'}
            {/block}
            <div class="addtocarterror"><div>
            {* Input to refresh product HTML removed, block kept for compatibility with themes *}
            {block name='product_refresh'}{/block}
          </form>
        </div>
      {/block}
    </div>
  </div>
</div>

<script type="application/ld+json">
  {
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{$product.name|escape:'html':'UTF-8'}",
    "image": [
      {foreach from=$product.images item=image name=productImages}
        "{$image.bySize.large_default.url|escape:'html':'UTF-8'}"{if $smarty.foreach.productImages.last == false},{/if}
      {/foreach}
    ],
    "description": "{$page.meta.description|regex_replace:"/[\r\n]/" : " "|escape:'html':'UTF-8'}",
    "category": "{$product.category_name|escape:'html':'UTF-8'}",
    "sku": "{$product.reference|escape:'html':'UTF-8'}",
    "mpn": "{if $product.mpn}{$product.mpn|escape:'html':'UTF-8'}{elseif $product.reference}{$product.reference|escape:'html':'UTF-8'}{else}{$product.id|intval}{/if}",
    {if $product.ean13}"gtin13": "{$product.ean13|escape:'html':'UTF-8'}",{else if $product.upc}"gtin13": "{$product.upc|escape:'html':'UTF-8'}",{/if}
    {if $product_manufacturer->name || $shop.name}
      "brand": {
        "@type": "Thing",
        "name": "{if $product_manufacturer->name}{$product_manufacturer->name|escape:'html':'UTF-8'}{else}{$shop.name|escape:'html':'UTF-8'}{/if}"
      },
    {/if}
    {if isset($product.weight) && ($product.weight != 0)}
      "weight": {
        "@context": "https://schema.org",
        "@type": "QuantitativeValue",
        "value": "{$product.weight|string_format:"%.2f"|escape:'html':'UTF-8'}",
        "unitCode": "{$product.weight_unit|escape:'html':'UTF-8'}"
      },
    {/if}
    {hook h='renderAggregateRatingForProduct' id_product=$product.id}
    "offers": {
      "@type": "Offer",
      "url": "{$product.url|escape:'html':'UTF-8'}",
      "name": "{$product.name|strip_tags|escape:'html':'UTF-8'}",
      "sku": "{if $product.reference}{$product.reference|escape:'html':'UTF-8'}{else}{$product.id|intval}{/if}",
      "mpn": "{if $product.mpn}{$product.mpn|escape:'html':'UTF-8'}{elseif $product.reference}{$product.reference|escape:'html':'UTF-8'}{else}{$product.id|intval}{/if}",
      {if $product.ean13}"gtin13": "{$product.ean13|escape:'html':'UTF-8'}",{else if $product.upc}"gtin13": "0{$product.upc|escape:'html':'UTF-8'}",{/if}
      {if $product.condition == 'new' || $product.condition == ''}
        "itemCondition": "https://schema.org/NewCondition",
      {elseif $product.condition == 'used'}
        "itemCondition": "https://schema.org/UsedCondition",
      {/if}
      "priceCurrency": "{$currency.iso_code|escape:'html':'UTF-8'}",
      "price": {$product.price_amount|escape:'html':'UTF-8'},
      "priceValidUntil": "{date('Y-m-d', strtotime('+15 days'))|escape:'html':'UTF-8'}",
      "availability": "{$product.seo_availability|escape:'html':'UTF-8'}",
      "seller": {
        "@type": "Organization",
        "name": "{$shop.name|escape:'html':'UTF-8'}"
      }
    }
  }
</script>