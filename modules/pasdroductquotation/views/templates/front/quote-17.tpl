{*
* Product Quotation
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules All right reserved
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category  front_office_features
* @package   productquotation
*}
{extends file='page.tpl'}

{block name="page_content"}
<input type="hidden" id="token" name="token" value="{$token}" />
<script>var fmm_label_fail = "{l s='Email incorrect or Empty' mod='productquotation'}"</script>
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
		<p><input type="text" value="{if isset($email)}{$email}{/if}" name="email" class="validate" id="fmm_email" required="required" /></p>
		</p>
			{foreach from=$forms key=i item=form}
			<input type="hidden" value="{$form.id_productquotation_templates|escape:'htmlall':'UTF-8'}" name="quote_template" />
				{$form.form nofilter}{*HTML Content*}
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
				<div class="pq_img"><a href="{$product.link|escape:'htmlall':'UTF-8'}"><img width="60" src="{$product.img_id}" /></a></div>
				<h6><a href="{$product.link|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}{if isset($product.reference) AND $product.reference}<br><small class="form-control-comment text-muted hint">({$product.reference|escape:'htmlall':'UTF-8'})</small>{/if}</a><br/><a class="fmm_quote_del" onclick="dropItemQuote({$product.id_quotes_products|escape:'htmlall':'UTF-8'})"></a><strong>{l s='Quantity' mod='productquotation'}: <input type="number" tabindex="{$product.id_quotes_products|escape:'htmlall':'UTF-8'}" value="{$product.qty|escape:'htmlall':'UTF-8'}" min="1" class="fmm_quote_row_qty" /> ({$product.price|escape:'htmlall':'UTF-8'})</strong></h6>
				{if !empty($product.customizations)}
				<div class="pq_customized_data">
					{foreach from=$product.customizations item=cs name=cs}
						<i>{$cs.label}:{if $cs.type > 0}{$cs.value}{else}<img src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}upload/{$cs.value}_small" width="20" />{/if}</i><br />
					{/foreach}
				</div>
				{/if}
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
	<footer class="form-footer text-sm-right">
		{if isset($forms) && $forms}
	<button class="button btn btn-primary fmm_quote_sb_btn" id="submitQuote" onclick="return validateForm(this);" name="submitQuote" type="submit"><span>{l s='Submit' mod='productquotation'} <i class="icon-chevron-right right"></i></span></button>
	{hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
	{/if}
		<button class="button btn btn-primary" type="button" onclick="fmmQuoteUpdate(this);">{l s='Update Quote' mod='productquotation'}</button>
	</footer>
</div>
{/if}
</form>
{/block}
