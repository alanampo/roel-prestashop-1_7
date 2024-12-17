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
{capture name=path}{l s='My Quotations' mod='productquotation'}{/capture}
{include file="$tpl_dir./errors.tpl"}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<h1 class="page-heading">{l s='Your Quotations' mod='productquotation'}</h1>
{if empty($quotes)}
<p>{l s='You do not have any quotes yet.' mod='productquotation'}</p>
{else}
<table class="table table-bordered">
    <thead>
        <tr>
           <th>{l s='S.No' mod='productquotation'}</th>
            <th>{l s='Products' mod='productquotation'}</th>
            <th>{l s='Status' mod='productquotation'}</th>
            <th></th>
            <th>{l s='Quote Attach File' mod='productquotation'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$quotes key=i item=quote}
        {assign var='msgs' value=(Quote::countMessage($quote.id_productquotation))|intval}
            <tr>
                <td>{$quote.id_productquotation|escape:'htmlall':'UTF-8'}</td>
                <td>{$quote.product_count|escape:'htmlall':'UTF-8'}</td>
                <td>{$quote.status_name|escape:'htmlall':'UTF-8'}</td>
                <td>
                    <a href="{$link->getModuleLink('productquotation', 'quotations')|escape:'htmlall':'UTF-8'}?id_quote={$quote.id_quote|escape:'htmlall':'UTF-8'}&id_quotation={$quote.id_productquotation|escape:'htmlall':'UTF-8'}">{l s='More Details' mod='productquotation'}</a>
                    <a style="margin-top: -5px;" class="btn btn-default" href="{$link->getModuleLink('productquotation', 'quotations', ['pdf' => 1, 'id_quote' => $quote.id_quote, 'id_quotation' => $quote.id_productquotation], true)|escape:'htmlall':'UTF-8'}"><i class="icon-download"></i></a>
                    {if $msgs}
                        <a href="{$link->getModuleLink('productquotation', 'messages', ['key' => Quote::getKey($quote.id_productquotation), 'id_quotation' => $quote.id_productquotation], true)|escape:'htmlall':'UTF-8'}" title="{l s='Notification' mod='productquotation'}">
                            <div class="quote-messages">
                                <div class="quote-badge">
                                    <div class="quote-message-count">{$msgs|intval|escape:'htmlall':'UTF-8'}</div>
                                </div>
                            </div>
                        </a>
                    {/if}
                    

                 <a style="margin-top: -5px;" class="btn btn-default" href="{$link->getModuleLink('productquotation', 'quotations')|escape:'htmlall':'UTF-8'}?pdf=1&id_quote={$quote.id_quote|escape:'htmlall':'UTF-8'}&id_quotation={$quote.id_productquotation|escape:'htmlall':'UTF-8'}"><i class="material-icons">cloud_download</i></a>
             </td>
             <td>
                {if $quote.file_name}
                    <a style="margin-top: -5px;" class="btn btn-default" target="_blank" href="{$quote.file_path}"><i class="material-icons">cloud_download</i></a>
                {/if}
                     
                </td>
            </tr>
        {/foreach}
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
</ul>