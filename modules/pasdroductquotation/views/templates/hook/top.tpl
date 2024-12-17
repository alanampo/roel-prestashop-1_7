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

<div class="col-sm-4 clearfix{if $ps_ver > 0} ps_ver_17_only{/if}" id="pq_top_block">
    <div class="product_quotation">
        <a rel="nofollow" class="pq_top_anchor" title="View Quotes" href="{$link->getModuleLink('productquotation', 'quote')|escape:'htmlall':'UTF-8'}">
        <b>{l s='Quotation' mod='productquotation'}</b>
        <span class="product_quotation_quantity_wrap">&nbsp;(<span id="product_quotation_quantity">{$count|escape:'htmlall':'UTF-8'}</span>)</span>
        </a>
        <span id="fmm_pq_dropper" onclick="fmmDropIt(this);"></span>
    </div>
    <div id="fmm_quote_content"{if $ps_ver > 0} class="fmm_pq_ps17"{/if}>
        <div class="fmm_content">
            <ul>
                {foreach from=$products item=product name=products}
                <li>
                    {if $ps_ver > 0}
                        <div class="pq_img"><a href="{$product.link|escape:'htmlall':'UTF-8'}"><img width="60" src="{$product.img_id}" /></a></div>
                    {else}
                        <div class="pq_img"><a href="{$product.link|escape:'htmlall':'UTF-8'}"><img width="60"  src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" /></a></div>
                    {/if}
                    <h6><a href="{$product.link|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a><br/><strong>{l s='Quantity' mod='productquotation'}: {$product.qty|escape:'htmlall':'UTF-8'} ({$product.price|escape:'htmlall':'UTF-8'})</strong></h6>
                    <a class="fmm_quote_del" onclick="dropItemQuote({$product.id_quotes_products|escape:'htmlall':'UTF-8'})"></a>
                </li>
                {/foreach}
            </ul>
        </div>
        <a href="{$link->getModuleLink('productquotation', 'quote')|escape:'htmlall':'UTF-8'}" class="fmm_green_btn">{l s='Quotations' mod='productquotation'}</a>
    </div>
</div>