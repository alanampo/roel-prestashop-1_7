{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2022 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script>var fmm_label_fail = "{l s='Email incorrect or Empty' mod='productquotation'}"</script>
{capture name=path}{l s='Send a Quote' mod='productquotation'}{/capture}
{include file="$tpl_dir./errors.tpl"}
<input type="hidden" id="token" name="token" value="{$token}" />
{if $success > 0}
<p class="alert alert-success">{l s='Quotation submitted' mod='productquotation'}</p>
{/if}
<form action="{$form_action|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" method="post" id="fmm_quote_form">
{if isset($products) && $products}
	{if isset($forms) && $forms}
	<div id="pq_form_major">
		<h3 class="page-subheading">{l s='Please fill in form.' mod='productquotation'}</h3>
		<p class="form-group">
		<p>{l s='Email' mod='productquotation'}</p>
		<p><input type="text" value="" name="email" class="validate" id="fmm_email" required="required" /></p>
		</p>
			{foreach from=$forms key=i item=form}
			<input type="hidden" value="{$form.id_productquotation_templates|escape:'htmlall':'UTF-8'}" name="quote_template" />
				{$form.form}{*HTML Content*}
				{if $i>=0}
					{break}
				{/if}
			{/foreach}
	</div>
	{else}
	{l s='No Form avilable.' mod='productquotation'}
	{/if}
{/if}
<h1 class="page-heading bottom-indent">{l s='Products in Quote' mod='productquotation'}</h1>
<div id="fmm_quote_from">
	{if isset($products) && $products}
	<div class="fmm_content_holder">
		<ul>
			{foreach from=$products item=product name=products}
			<li>
				<div class="pq_img"><a href="{$product.link|escape:'htmlall':'UTF-8'}"><img width="60" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" /></a></div>
				<h6><a href="{$product.link|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a><br/><a class="fmm_quote_del" onclick="dropItemQuote({$product.id_quotes_products|escape:'htmlall':'UTF-8'})"></a><strong>{l s='Quantity' mod='productquotation'}: <input type="number" tabindex="{$product.id_quotes_products|escape:'htmlall':'UTF-8'}" value="{$product.qty|escape:'htmlall':'UTF-8'}" min="1" class="fmm_quote_row_qty" /> ({$product.price|escape:'htmlall':'UTF-8'})</strong></h6>
			</li>
			{/foreach}
		</ul>
	</div>
	<ul>
		<li class="fmm_total"> {l s='Total' mod='productquotation'}: <span id="fmm_quote_basetotal">{$total|escape:'htmlall':'UTF-8'}</span></li>
	</ul>
	{else}
	<p>{l s='No products in quote.' mod='productquotation'}</p>
	{/if}
</div>
{if isset($products) && $products}

<div class="submit_fmm_pq_submit">
	{if isset($forms) && $forms}
	<button class="button btn btn-default button-medium" id="submitQuote" onclick="return validateForm(this);" name="submitQuote" type="submit"><span>{l s='Submit' mod='productquotation'} <i class="icon-chevron-right right"></i></span></button>
	<button class="button btn btn-default button-medium ps_16_upd_btn" type="button" onclick="fmmQuoteUpdate(this);"><span>{l s='Update Quote' mod='productquotation'}</span></button>
	{/if}
</div>
{/if}
</form>