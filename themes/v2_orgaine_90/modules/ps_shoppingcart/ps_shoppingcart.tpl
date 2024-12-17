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
    <div id="_desktop_cart">
        <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if} tv-header-cart" data-refresh-url="{$refresh_url}">
            <div class="tvheader-cart-wrapper {if Configuration::get('TVCMSCUSTOMSETTING_CART_VIEW') == 'pop-up'}tvheader-cart-wrapper-popup{/if}">
                <div class='tvheader-cart-btn-wrapper'>
                    <a rel="nofollow" href="JavaScript:void(0);" data-url='{$cart_url}' title='{l s="Cart" d="Shop.Theme.Checkout"}'>
                        <div class="tvcart-icon-text-wrapper">
                            <div class="tv-cart-icon tvheader-right-icon tv-cart-icon-main">
                                {* <i class="material-icons shopping-cart">&#xe8cb;</i> *}
                                <svg version="1.1" id="Layer_1" x="0px" y="0px" width="22px" height="22px" viewBox="0 0 228 200" style="enable-background:new 0 0 228 200;" xml:space="preserve">
                                    <path d="M1,7c3.3-4.1,7.5-5.3,12.7-5.1C21.5,2.3,29.3,2,37.1,2c7.6,0,9.6,1.5,11.7,8.8c10.7,37.2,21.5,74.4,32,111.7c1.1,4,2.8,5.4,7.1,5.4c27.8-0.2,55.6-0.2,83.4,0c3.7,0,5.6-1,7.1-4.5c8.8-19.9,17.9-39.7,26.8-59.6c0.7-1.5,1.2-3,2.1-5.1c-2.7,0-4.6,0-6.6,0c-31.1,0-62.3,0-93.4,0c-1.7,0-3.4,0.1-5-0.2c-4.4-0.8-7.6-4.4-7.1-8.3c0.5-4.3,3-7,7.4-7.4c1.7-0.2,3.3-0.2,5-0.2c36.3,0,72.6,0.1,108.9-0.1c5.2,0,9.4,1,12.4,5.5c0,1.7,0,3.3,0,5c-4,8.4-8,16.7-11.8,25.2c-8.8,19.4-17.6,38.8-26.2,58.2c-2.3,5.3-5.7,7.6-11.5,7.5c-33.5-0.1-67-0.1-100.5-0.1c-7.5,0-9.6-1.7-11.7-8.9c-10.7-37.2-21.5-74.5-32-111.8c-1.2-4.2-2.9-5.6-7.2-5.3c-5,0.4-10-0.1-15,0.2C8.1,18.4,4,17.2,1,13C1,11,1,9,1,7z"></path>
                                    <path d="M184,201c-1.2-0.5-2.4-1.1-3.6-1.6c-8.4-3.6-13-11.7-11.3-20.5c1.5-8.2,9.6-14.7,18.4-14.8c9-0.1,16.9,6.1,18.8,14.8c1.8,8.3-2.6,16.5-10.8,20.3c-1.2,0.5-2.3,1.2-3.4,1.8C189.3,201,186.7,201,184,201z"></path>
                                    <path d="M64,201c-5.8-2.6-11.3-5.6-13.7-12.1c-3.4-9.1,1-19.1,10.2-23c9.6-4,20.7,0.1,25,9.2c4.1,8.8,0.3,19.2-8.6,23.7c-1.6,0.8-3.3,1.5-4.9,2.2C69.3,201,66.7,201,64,201z"></path>
                                </svg>
                            </div>
                            <div class="tv-cart-cart-inner">
                                {* <span class="tvcart-lable">{l s='My Cart' d='Shop.Theme.Checkout'}</span>
                                <span class="tvcart-total-price">{$cart.totals.total_excluding_tax.value}</span>
                                <span class="tv-cart-title">{l s='Products' d='Shop.Theme.Checkout'}</span>*}
                                <span class="cart-products-count">{$cart.products_count}</span>
                            </div>
                        </div>
                    </a>
                </div>
                {if Configuration::get('TVCMSCUSTOMSETTING_CART_VIEW') == 'classic'}
                <div class="ttvcmscart-show-dropdown-right">
                    {if $cart.products_count > 0}
                    <div class="ttvcart-scroll-container">
                        <div class="ttvcart-close-title-count">
                            <button class="ttvclose-cart"></button>
                            <div class="ttvcart-top-title">
                                <h4>{l s='Shopping Cart' d='Shop.Theme.Checkout'}</h4>
                            </div>
                            <div class="ttvcart-counter">
                                <span class="ttvcart-products-count">{$cart.products_count}</span>
                            </div>
                        </div>
                        <div class="ttvcart-product-content-box ttvscroll-container">
                            {foreach from=$cart.products item=product}
                            <div class="ttvcart-product-wrapper items">
                                <div class="tvcart-product-list-img">
                                    <a href="{$product.url}" class="tvshoping-cart-dropdown-img-block">
                                        <img src="{$product.cover.bySize.cart_default.url}" width="{$product.cover.bySize.cart_default.width}" height="{$product.cover.bySize.cart_default.height}" loading="lazy">
                                    </a>
                                </div>
                                <div class="tvcart-product-content">
                                    <div class="tvshoping-cart-dropdown-title">
                                        <a href="{$product.url}" class="">
                                            <span class="product-name">{$product.name}</span>
                                        </a>
                                    </div>
                                    <div class="tvcart-product-list-box">
                                        <span class="tvshopping-cart-qty">{l s='QTY :' d='Shop.Theme.Actions'}</span>
                                        <span class="product-qty">{$product.quantity}</span>
                                    </div>
                                    <span class="product-price">{$product.price}</span>
                                    {if $product.has_discount}
                                    <span class="regular-price">{$product.regular_price}</span>
                                    {/if}
                                    {*<div class="tvcart-product-list-attribute">
                                        {foreach $product.attributes as $prod_attb=>$prod_val}
                                        <div class="tvcart-product-attr"><span>{$prod_attb}:</span> <span>{$prod_val}</span></div>
                                        {/foreach}
                                    </div>*}
                                    <div class="tvcart-product-remove">
                                        {$url = 'controller=cart&delete='|cat:$product.id_product}
                                        <a class="remove-from-cart tvcmsremove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url}" data-link-action="delete-from-cart" data-id-product="{$product.id_product|escape:'javascript':'UTF-8'}" data-id-product-attribute="{$product.id_product_attribute|escape:'javascript':'UTF-8'}" data-id-customization="{$product.id_customization|escape:'javascript':'UTF-8'}" title="{l s='remove from cart' d='Shop.Theme.Actions'}">
                                            <i class='material-icons'>&#xe872;</i>
                                        </a>
                                    </div>
                                    {if $product.customizations|count}
                                    <div class="customizations">
                                        <ul>
                                            {foreach from=$product.customizations item='customization'}
                                            <li>
                                                <span class="product-quantity">{$customization.quantity}</span>
                                                <a href="{$customization.remove_from_cart_url}" title="{l s='remove from cart' d='Shop.Theme.Actions'}" class="remove-from-cart" rel="nofollow">{l s='Remove' d='Shop.Theme.Actions'}</a>
                                                <ul>
                                                    {foreach from=$customization.fields item='field'}
                                                    <li>
                                                        <span>{$field.label}</span>
                                                        {if $field.type == 'text'}
                                                        <span>{$field.text nofilter}</span>
                                                        {else if $field.type == 'image'}
                                                        <img src="{$field.image.small.url}" loading="lazy">
                                                        {/if}
                                                    </li>
                                                    {/foreach}
                                                </ul>
                                            </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                    {/if}
                                </div>
                            </div>
                            {/foreach}
                        </div>
                    </div>
                    <div class="ttvcart-price-shipping-text">
                        {foreach from=$cart.subtotals item="subtotal"}
                        {if !empty($subtotal.value) && !empty($subtotal.type)}
                        {if $subtotal.type !== 'tax'}
                        <div class="ttvcart-product-label-value" id="tvcart-subtotal-{$subtotal.type}">
                            <span class="ttvshoping-cart-label label{if 'products' === $subtotal.type} js-subtotal{/if}">
                                {if 'products' == $subtotal.type}
                                {l s='Sub Total' d='Shop.Theme.Checkout'}
                                {else}
                                {$subtotal.label}
                                {/if}
                                {if $subtotal.type === 'shipping'}
                                <small class="value">{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small>
                                {/if}
                            </span>
                            <span class="ttvcart-product-value">{$subtotal.value}</span>
                        </div>
                        {/if}
                        {/if}
                        {/foreach}
                        {* <div class="ttvcart-product-label-value">
                            <span class="ttvshoping-cart-label">{l s='Subtotal' d='Shop.Theme.Checkout'}</span>
                            <span class="ttvcart-product-value">{$subtotal.value}</span>
                        </div>
                        <div class="ttvcart-product-label-value">
                            <span class="ttvshoping-cart-label">{l s='Shipping' d='Shop.Theme.Checkout'}</span>
                            <span class="ttvcart-product-value">{$cart.subtotals.shipping.value}</span>
                        </div>*}
                        <div class="ttvcart-product-label-value total">
                            <span class="ttvshoping-cart-label">{$cart.totals.total.label} {$cart.labels.tax_short}</span>
                            <span class="ttvcart-product-value">{$cart.totals.total.value}</span>
                        </div>
                        <div class="ttvcart-product-label-value tax">
                            <span class="ttvshoping-cart-label">{if !empty($cart.subtotals.tax.label)}{$cart.subtotals.tax.label}{/if}</span>
                            <span class="ttvcart-product-value">{if !empty($cart.subtotals.tax.value)}{$cart.subtotals.tax.value}{/if}</span>
                        </div>
                    </div>
                    <div class="ttvcart-product-list-btn-wrapper">
                        <button class="ttvcart-product-list-viewcart">
                            <a href="{$cart_url}">
                                {l s='View Cart' d='Shop.Theme.Actions'}
                            </a>
                        </button>
                        <button class="ttvcart-product-list-checkout">
                            <a href="{$link->getPageLink('order', null, $language.id)}">
                                {l s='CheckOut' d='Shop.Theme.Actions'}
                            </a>
                        </button>
                    </div>
                    {else}
                    <div class="ttvcart-no-product">
                        <div class="ttvcart-close-title-count tdclose-btn-wrap">
                            <button class="ttvclose-cart"></button>
                            <div class="ttvcart-top-title">
                                <h4>{l s='Shopping Cart' d='Shop.Theme.Checkout'}</h4>
                            </div>
                            <div class="ttvcart-counter">
                                <span class="ttvcart-products-count">{$cart.products_count}</span>
                            </div>
                        </div>
                        {*<div class='ttvcart-no-product-label'>{l s='No Product Add in Cart' d='Shop.Theme.Checkout'}</div>*}
                    </div>
                    {/if}
                </div>
                <!-- Start DropDown header cart -->
                {elseif Configuration::get('TVCMSCUSTOMSETTING_CART_VIEW') == 'pop-up'}
                <div class="tvcmscart-show-dropdown">
                    {if $cart.products_count > 0}
                    <div class="tvcart-product-list">
                        <div class="tvcart-product-totle">
                            {l s='Your Cart: ' d='Shop.Theme.Checkout'} {count($cart.products)} {if count($cart.products) == 1}{l s='Item' d='Shop.Theme.Checkout'}{else}{l s='Items' d='Shop.Theme.Checkout'}{/if}
                        </div>
                        <div class="tvcart-product-content-box tvscroll-container">
                            {foreach from=$cart.products item=product}
                            <div class="tvcart-product-wrapper items">
                                <div class="tvcart-product-list-img">
                                    <a href="{$product.url}" class="tvshoping-cart-dropdown-img-block">
                                        <img src="{$product.cover.bySize.cart_default.url}" width="{$product.cover.bySize.cart_default.width}" height="{$product.cover.bySize.cart_default.height}" loading="lazy">
                                    </a>
                                </div>
                                <div class="tvcart-product-content">
                                    <div class="tvcart-product-list-quentity">
                                        <div class="tvshoping-cart-dropdown-title">
                                            <a href="{$product.url}" class="">
                                                <span class="product-name">{$product.name}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="tvcart-product-list-price">
                                        <span class="product-quentity">{$product.quantity}</span>
                                        <span class="tvshopping-cart-quentity">X</span>
                                        <span class="product-price">{$product.price}</span>
                                    </div>
                                    <div class="tvcart-product-list-attribute">
                                        {foreach $product.attributes as $prod_attb=>$prod_val}
                                        <div class="tvcart-product-attr"><span>{$prod_attb}:</span> <span>{$prod_val}</span></div>
                                        {/foreach}
                                    </div>
                                    <div class="tvcart-product-remove">
                                        {$url = 'controller=cart&delete='|cat:$product.id_product}
                                        <a class="remove-from-cart tvcmsremove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url}" data-link-action="delete-from-cart" data-id-product="{$product.id_product|escape:'javascript':'UTF-8'}" data-id-product-attribute="{$product.id_product_attribute|escape:'javascript':'UTF-8'}" data-id-customization="{$product.id_customization|escape:'javascript':'UTF-8'}" title="{l s='remove from cart' d='Shop.Theme.Actions'}">
                                            <i class='material-icons'>&#xe872;</i>
                                        </a>
                                    </div>
                                    {if $product.customizations|count}
                                    <div class="customizations">
                                        <ul>
                                            {foreach from=$product.customizations item='customization'}
                                            <li>
                                                <span class="product-quantity">{$customization.quantity}</span>
                                                <a href="{$customization.remove_from_cart_url}" title="{l s='remove from cart' d='Shop.Theme.Actions'}" class="remove-from-cart" rel="nofollow">{l s='Remove' d='Shop.Theme.Actions'}</a>
                                                <ul>
                                                    {foreach from=$customization.fields item='field'}
                                                    <li>
                                                        <span>{$field.label}</span>
                                                        {if $field.type == 'text'}
                                                        <span>{$field.text nofilter}</span>
                                                        {else if $field.type == 'image'}
                                                        <img src="{$field.image.small.url}" loading="lazy">
                                                        {/if}
                                                    </li>
                                                    {/foreach}
                                                </ul>
                                            </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                    {/if}
                                </div>
                            </div>
                            {/foreach}
                        </div>
                        <div class="tvcart-product-list-total-info">
                            <div class="tvcart-product-list-subtotal-prod">
                                <span class="tvshoping-cart-subtotal">{l s='Sub Total' d='Shop.Theme.Checkout'}</span>
                                <span class="tvcart-product-price">{$cart.subtotals.products.value}</span>
                            </div>
                            {* <div class="tvcart-product-list-subtotal-shipping">
                                <span class="tvshoping-cart-shipping">{l s='Shipping' d='Shop.Theme.Checkout'}</span>
                                <span class="tvcart-product-price">{$cart.subtotals.shipping.value}</span>
                            </div>
                            <div class="tvcart-product-list-subtotal-tax">
                                <span class="tvshoping-cart-tax">{l s='Tax' d='Shop.Theme.Checkout'}</span>
                                <span class="tvcart-product-price">{$cart.subtotals.tax.value}</span>
                            </div>
                            <div class="tvcart-product-list-subtotal-excluding-text">
                                <span class="ttshoping-cart-total">{l s='Total' d='Shop.Theme.Checkout'}</span>
                                <span class="tvcart-product-price">{$cart.totals.total_excluding_tax.value}</span>
                            </div> *}
                        </div>
                    </div>
                    <div class="tvcart-product-list-btn-wrapper">
                        <div class="tvcart-product-list-viewcart">
                            <a href="{$cart_url}">{l s='View cart' d='Shop.Theme.Checkout'}</a>
                        </div>
                        <div class="tvcart-product-list-checkout">
                            <a href="javascript:void(0);" class="tvcart-product-list-checkout-link">{l s='Proceed to checkout' d='Shop.Theme.Checkout'}</a>
                        </div>
                    </div>
                    {else}
                    <div class="tvcart-no-product">
                        <div class='tvcart-no-product-label'>{l s='No product add in cart' d='Shop.Theme.Checkout'}</div>
                    </div>
                    {/if}
                </div>
                {/if}
            </div>
        </div>
    </div>
    {/strip}