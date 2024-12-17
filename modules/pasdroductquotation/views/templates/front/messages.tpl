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
{capture name=path}{l s='Messages' mod='productquotation'}{/capture}
{include file="$tpl_dir./errors.tpl"}
{if $success > 0}
<p class="alert alert-success">{l s='Message Sent' mod='productquotation'}</p>
{/if}
<h1 class="page-heading bottom-indent">{l s='Messages' mod='productquotation'}</h1>
{if $authorize > 0}
{if !empty($threads)}
    <div class="col-xs-12 col-md-12" id="fmm_message_tree">
        <ul>
        {foreach from=$threads key=i item=thread}
            <li class="fmm_level_thread_{$thread.author|escape:'htmlall':'UTF-8'}">{$thread.message}{*HTML Content*}<span class="date">{l s='Posted' mod='productquotation'}: {$thread.date|escape:'htmlall':'UTF-8'}</span></li>
        {/foreach}
        </ul>
    </div>
{/if}
    <form action="{$form_action|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" method="post">
        <div class="col-xs-12 col-md-12">
            <div class="form-group">
                <label for="message">{l s='Message' mod='productquotation'}</label>
                <textarea name="message" id="message" class="form-control"></textarea>
            </div>
        </div>
        <div class="submit col-md-12" style="clear: both">
            <button class="button btn btn-default button-medium" id="submitMessage" name="submitMessage" type="submit"><span>{l s='Send' mod='productquotation'} <i class="icon-chevron-right right"></i></span></button>
        </div>
    </form>
{/if}

<ul class="footer_links clearfix">
    <li>
        <a href="{$link->getModuleLink('productquotation', 'quotations', [], true)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to Quotations' mod='productquotation'}
            </span>
        </a>
    </li>
    <li>
        <a href="{$link->getPageLink('my-account')|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='productquotation'}
            </span>
        </a>
    </li>
</ul>