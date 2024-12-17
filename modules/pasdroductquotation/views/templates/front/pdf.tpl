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

{assign var=color_header value="#F0F0F0"}
{assign var=color_border value="#000000"}
{assign var=color_border_lighter value="#CCCCCC"}
{assign var=color_line_even value="#FFFFFF"}
{assign var=color_line_odd value="#F9F9F9"}
{assign var=font_size_text value="9pt"}
{assign var=font_size_header value="9pt"}
{assign var=font_size_product value="9pt"}
{assign var=height_header value="20px"}
{assign var=table_padding value="4px"}

<style>
    table, th, td {
        margin: 0!important;
        padding: 0!important;
        vertical-align: middle;
        font-size: {$font_size_text|escape:'htmlall':'UTF-8'};
        white-space: nowrap;
    }

    table.product {
        border: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
        border-collapse: collapse;
    }

    table#addresses-tab tr td {
        font-size: large;
    }

    table#summary-tab {
        padding: {$table_padding|escape:'htmlall':'UTF-8'};
        border: 1pt solid {$color_border|escape:'htmlall':'UTF-8'};
    }

    table#payment-tab {
        padding: {$table_padding|escape:'htmlall':'UTF-8'};
        border: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
    }

    th.product {
        border-bottom: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
    }

    tr.discount th.header {
        border-top: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
    }

    tr.product td {
        border-bottom: 1px solid {$color_border_lighter|escape:'htmlall':'UTF-8'};
    }

    tr.color_line_even {
        background-color: {$color_line_even|escape:'htmlall':'UTF-8'};
    }

    tr.color_line_odd {
        background-color: {$color_line_odd|escape:'htmlall':'UTF-8'};
    }

    tr.customization_data td {
    }

    td.product {
        vertical-align: middle;
        font-size: {$font_size_product|escape:'htmlall':'UTF-8'};
    }

    th.header {
        font-size: {$font_size_header|escape:'htmlall':'UTF-8'};
        height: {$height_header|escape:'htmlall':'UTF-8'};
        background-color: {$color_header|escape:'htmlall':'UTF-8'};
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
    }

    th.header-right {
        font-size: {$font_size_header|escape:'htmlall':'UTF-8'};
        height: {$height_header|escape:'htmlall':'UTF-8'};
        background-color: {$color_header|escape:'htmlall':'UTF-8'};
        vertical-align: middle;
        text-align: right;
        font-weight: bold;
    }

    th.payment {
        background-color: {$color_header|escape:'htmlall':'UTF-8'};
        vertical-align: middle;
        font-weight: bold;
    }

    tr.separator td {
        border-top: 1px solid #000000;
    }

    .left {
        text-align: left;
    }

    .fright {
        float: right;
    }

    .right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }

    .no_top_border {
        border-top:hidden;
        border-bottom:1px solid black;
        border-left:1px solid black;
        border-right:1px solid black;
    }

    .grey {
        background-color: {$color_header|escape:'htmlall':'UTF-8'};

    }

    /* This is used for the border size */
    .white {
        background-color: #FFFFFF;
    }

    .big,
    tr.big td{
        font-size: 110%;
    }
    .small {
        font-size:small;
    }

    #fmm_account_quotation_products {}
#fmm_account_quotation_products ul li { padding: 10px; background: #F6F6F6; border-bottom: 2px solid #E9E9E9;}

#fmm_account_quotation_products ul li div.pq_img { float: left; padding-right: 20px;}

#fmm_account_quotation_products ul li div.pq_img img { border: 2px solid #D6D6D6}

#fmm_account_quotation_products ul li h6 { margin: 2px 0; font-size: 14px;}

#fmm_account_quotation_products ul li h6 strong { float: right; font-size: 13px;}

#fmm_account_quotation_products ul li.fmm_total { font-weight: bold; text-align: right}

</style>
<h1 class="page-heading">{l s='Quotations Details' mod='productquotation'}</h1>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
    <!-- Addresses -->
    
    <tr>
        <td colspan="12">

            <table id="summary-tab" width="100%">
                <tr>
                    <th class="header small" valign="middle">{l s='Title'  mod='productquotation'}</th>
                    <th class="header small" valign="middle">{l s='Answer'  mod='productquotation'}</th>
                </tr>
                {foreach from=$quotes_data key=i item=quote}
                <tr>
                    <td class="center small white">{$quote.title|escape:'htmlall':'UTF-8'}</td>
                    <td class="center small white">{$quote.value|escape:'htmlall':'UTF-8'}</td>
                </tr>
                {/foreach}
                {foreach from=$user_data key=i item=user}
                <tr>
                    <td class="center small white">{$user.field|escape:'htmlall':'UTF-8'}</td>
                    {if $version < '1.7'}
                        {if $user.callback == 'downloadFile'}
                            <td class="center small white">
                               
                            </td>
                            {elseif $user.callback == 'image'}
                            <td>
                            
                            </td>
                            {else}
                            {if isset($user.value) AND is_array($user.value)}
                               <td class="center small white"> {FieldsQuote::getOptionValue(implode(',',$user.value))|escape:'htmlall':'UTF-8'}</td>
                            
                            {else}
                             <td class="center small white">{$user.value|escape:'htmlall':'UTF-8'}</td>
                             {/if}
                            
                        {/if}
                      {else}

                      {if $user.callback == 'downloadFile'}
                            <td class="center small white">
                                <a class="btn btn-default button" href="{$link->getModuleLink('productquotation','quotations')|escape:'htmlall':'UTF-8'}?downloadFile&l={base64_encode($user.value)|escape:'htmlall':'UTF-8'}" target="_blank">
                                <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productquotation/views/img/download.png" alt="{l s='Download Attachment' mod='productquotation'}" title="{l s='Download Attachment' mod='productquotation'}"/>
                            </a>
                            </td>
                            {elseif $user.callback == 'image'}
                            <td>
                                <img src="{$user.value|escape:'htmlall':'UTF-8'}" height="24" width="24">
                            
                            </td>
                            {else}
                            {if isset($user.value) AND is_array($user.value)}
                               <td class="center small white"> {FieldsQuote::getOptionValue(implode(',',$user.value))|escape:'htmlall':'UTF-8'}</td>
                            
                            {else}
                             <td class="center small white">{$user.value|escape:'htmlall':'UTF-8'}</td>
                             {/if}
                            
                        {/if}

                      {/if}

                </tr>
                {/foreach}

            </table>


        </td>
    </tr>

    <tr>
        <td colspan="12" height="20">&nbsp;</td>
    </tr>

    <!-- Products -->
    <tr>
        <td colspan="12">
        </td>
    </tr>

    <tr>
        <td colspan="12" height="20">&nbsp;</td>
    </tr>
    
    <tr>
        <td colspan="12" class="left">

            <table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
                <tr>
                    <td class="payment center small grey bold" width="44%">{l s='Total' mod='productquotation'}</td>
                    <td class="payment left white" width="56%">
                        <table width="100%" border="0">
                                <tr>
                                    <td class="small center">{$total|escape:'htmlall':'UTF-8'}</td>
                                </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table id="payment-tab" width="100%" cellpadding="4" cellspacing="0">
                <tr>
                    <td class="payment center small grey bold" width="44%">{l s='Products in Quotation'  mod='productquotation'}</td>
                    <td class="payment left white" width="56%">
                        <table width="100%" border="0">
                            <tr>
                                {foreach from=$products item=product name=products}
                                    <td class="center">
                                        <div class="pq_img">
                                            <a href="{$product.link|escape:'htmlall':'UTF-8'}">
                                                <img width="60" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" />
                                            </a>
                                        </div>
                                        <h6>
                                            <a href="{$product.link|escape:'htmlall':'UTF-8'}">
                                                {$product.name|escape:'htmlall':'UTF-8'}
                                            </a>
                                            {if isset($product.reference) AND $product.reference}
                                                <br><small class="form-control-comment text-muted hint">({$product.reference|escape:'htmlall':'UTF-8'})</small>
                                            {/if}
                                            <br/>
                                            <strong>
                                                {l s='Quantity' mod='productquotation'}: {$product.qty|escape:'htmlall':'UTF-8'} ({$product.price|escape:'htmlall':'UTF-8'})
                                            </strong>
                                        </h6>
                                        <a class="fmm_quote_del" onclick="dropItemQuote({$product.id_quotes_products|escape:'htmlall':'UTF-8'})"></a>
                                    </td>
                                {/foreach}
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </td>
        <td colspan="5">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="12" height="20">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="12">
        

        </td>
    </tr>
</table>


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