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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{strip}
<div id="blockcart-modal" class="modal fade tv-addtocart-msg-wrapper" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close tv-addtocart-close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title h6 text-sm-center" id="myModalLabel"><i class="material-icons rtl-no-flip">&#xE876;</i>{l s='Product successfully added to your shopping cart' d='Shop.Theme.Checkout'}</h4>
			</div>
			<div class="modal-body tv-addtocart-content-part">
				<div class="row">
					<div class="col-md-6 divide-right">
						<div class="row tv-addtocart-image-name-wrapper">
							<div class="col-md-6 tv-addtocart-product-image">
								<img class="product-image" src="{$product.cover.bySize['add_cart_def']['url']}" alt="{$product.name}" title="{$product.name}" width="{$product.cover.bySize['add_cart_def']['width']}" height="{$product.cover.bySize['add_cart_def']['height']}" itemprop="image">
							</div>
							<div class="col-md-6 tv-addtocart-product-name">
								<h6 class="h6 product-name">{$product.name}</h6>
								<p class="tv-addtocart-price">{$product.price}</p>
								{hook h='displayProductPriceBlock' product=$product type="unit_price"}
								{foreach from=$product.attributes item="property_value" key="property"}
									<span><strong>{$property}</strong>: {$property_value}</span><br>
								{/foreach}
									<span><strong>{l s='Quantity:' d='Shop.Theme.Checkout'}</strong>&nbsp;{$product.cart_quantity}</span
										>
							</div>
						</div>
					</div>
					<div class="col-md-6 tv-addtocart-content">
						<div class="cart-content">
							{if $cart.products_count > 1}
								<p class="cart-products-count">{l s='There are %products_count% items in your cart.' sprintf=['%products_count%' => $cart.products_count] d='Shop.Theme.Checkout'}</p>
							{else}
								<p class="cart-products-count">{l s='There is %product_count% item in your cart.' sprintf=['%product_count%' =>$cart.products_count] d='Shop.Theme.Checkout'}</p>
							{/if}
							<p><strong>{l s='Total products:' d='Shop.Theme.Checkout'}</strong>&nbsp;{$cart.subtotals.products.value}</p>
							<p><strong>{l s='Total shipping:' d='Shop.Theme.Checkout'}</strong>&nbsp;{$cart.subtotals.shipping.value} {hook h='displayCheckoutSubtotalDetails' subtotal=$cart.subtotals.shipping}</p>
							{if $cart.subtotals.tax}
								<p><strong>{$cart.subtotals.tax.label}</strong>&nbsp;{$cart.subtotals.tax.value}</p>
							{/if}
							<p><strong>{l s='Total:' d='Shop.Theme.Checkout'}</strong>&nbsp;{$cart.totals.total.value} {$cart.labels.tax_short}</p>
							<div class="cart-content-btn">
								<button type="button" class="tvall-inner-btn" data-dismiss="modal">
									<span>{l s='Continue shopping' d='Shop.Theme.Actions'}</span>
								</button>
								<a href="{$cart_url}" class="tvall-inner-btn">
									<i class="material-icons rtl-no-flip">&#xE876;</i>
									<span>{l s='Proceed to checkout' d='Shop.Theme.Actions'}</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{/strip}