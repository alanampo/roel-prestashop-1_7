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
{capture name=path}{l s='Quotation Details' mod='productquotation'}{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading">{l s='Your Quotations Details' mod='productquotation'}</h1>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>{l s='ID' mod='productquotation'}</th>
            <th>{l s='Title' mod='productquotation'}</th>
            <th>{l s='Answer' mod='productquotation'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$quotes_data key=i item=quote}
            <tr>
                <td>{$quote.id_quote_data|escape:'htmlall':'UTF-8'}</td>
                <td>{$quote.title|escape:'htmlall':'UTF-8'}</td>
                <td>{$quote.value|escape:'htmlall':'UTF-8'}</td>
            </tr>
        {/foreach}

        {foreach from=$user_data key=i item=user}
            <tr>
                <td>{$user.id|escape:'htmlall':'UTF-8'}</td>
                <td>{$user.field|escape:'htmlall':'UTF-8'}</td>

                {if $user.callback == 'downloadFile'}
                    <td>
                        <a class="btn btn-default button" href="{$link->getModuleLink('productquotation','quotations')|escape:'htmlall':'UTF-8'}?downloadFile&l={base64_encode($user.value)|escape:'htmlall':'UTF-8'}" target="_blank">

                        <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productquotation/views/img/download.png" alt="{l s='Download Attachment' mod='productquotation'}" title="{l s='Download Attachment' mod='productquotation'}"/>
                    </a>
                    </td>
                    {elseif $user.callback == 'image'}
                    <td>
                        <img id="preview-image" class="image_container" src="{$user.value|escape:'htmlall':'UTF-8'}" height="88" width="88">
                    
                    </td>
                    {else}
                        {if isset($user.value) AND is_array($user.value)}
                           <td> {FieldsQuote::getOptionValue(implode(',',$user.value))|escape:'htmlall':'UTF-8'}</td>
                        
                        {else}
                         <td>{$user.value|escape:'htmlall':'UTF-8'}</td>
                         {/if}
                        
                    {/if}

            </tr>
        {/foreach}

    </tbody>
</table>
<br />
<h3 class="page-subheading">{l s='Products in Quotation' mod='productquotation'}</h3>
<div class="fmm_content_holder" id="fmm_account_quotation_products">
		<ul>
			{foreach from=$products item=product name=products}
			<li>
				<div class="pq_img"><a href="{$product.link|escape:'htmlall':'UTF-8'}"><img width="60" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" /></a></div>
				<h6>
                    
                    <a href="{$product.link|escape:'htmlall':'UTF-8'}">
                    {$product.name|escape:'htmlall':'UTF-8'}
                    {if isset($product.reference) AND $product.reference}
                        <br><small class="form-control-comment text-muted hint">({$product.reference|escape:'htmlall':'UTF-8'})</small>
                    {/if}
                </a>
                
                    <br/><strong>{l s='Quantity' mod='productquotation'}: {$product.qty|escape:'htmlall':'UTF-8'} ({$product.price|escape:'htmlall':'UTF-8'})</strong></h6>
				<a class="fmm_quote_del" onclick="dropItemQuote({$product.id_quotes_products|escape:'htmlall':'UTF-8'})"></a>
			</li>
			{/foreach}
		</ul>

    <ul>
        <li class="fmm_total"> {l s='Total' mod='productquotation'}: {$total|escape:'htmlall':'UTF-8'}</li>
    </ul>
</div>
{if $voucher.coupon_id > 0}
<br />
<h3 class="page-subheading">{l s='Your Voucher' mod='productquotation'}</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>{l s='ID' mod='productquotation'}</th>
            <th>{l s='Valid Till' mod='productquotation'}</th>
            <th>{l s='Code' mod='productquotation'}</th>
        </tr>
    </thead>
    <tbody>
            <tr>
                <td>{$voucher.coupon_id|escape:'htmlall':'UTF-8'}</td>
                <td>{$voucher.date_to|escape:'htmlall':'UTF-8'}</td>
                <td>{$voucher.code|escape:'htmlall':'UTF-8'}</td>
            </tr>
    </tbody>
</table>
{/if}
<ul class="footer_links clearfix">
    <li>
        <a href="{$link->getPageLink('my-account')|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='productquotation'}
            </span>
        </a>
    </li>
    <li>
        <a href="{$link->getModuleLink('productquotation', 'quotations')|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to Quotations' mod='productquotation'}
            </span>
        </a>
    </li>
	
	 <li style="float: right">
        <a href="{$link->getModuleLink('productquotation', 'messages')|escape:'htmlall':'UTF-8'}?id_quotation={$id_quotation|escape:'htmlall':'UTF-8'}&key={$key|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span>
                 {l s='View or Send massage' mod='productquotation'} <i class="icon-chevron-right"></i>
            </span>
        </a>
    </li>
    <li style="float: right">
        <a class="btn btn-default button button-small btn-info" href="{$link->getModuleLink('productquotation', 'quotations', ['pdf' => 1, 'id_quote' => $id_quote, 'id_quotation' => $id_quotation], true)|escape:'htmlall':'UTF-8'}">{l s='Download PDF' mod='productquotation'} <i class="icon-download"></i>
        </a>
    </li>
</ul>