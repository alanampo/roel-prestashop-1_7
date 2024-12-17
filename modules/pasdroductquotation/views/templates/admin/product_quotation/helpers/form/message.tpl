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
{if $success > 0}
    <div class="bootstrap"><div class="alert alert-success">{l s='Message successfully sent.' mod='productquotation'}</div></div>
{/if}
<form class="form-horizontal" enctype="multipart/form-data" method="post" id="configuration_form" action="{$action|escape:'htmlall':'UTF-8'}">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs"></i> {l s='Threads related to Quotation ID:' mod='productquotation'} {$id_quotation|escape:'htmlall':'UTF-8'}</div>
        {if !empty($threads)}
            <div class="col-lg-12" id="fmm_message_tree">
                
                <ul>
                {foreach from=$threads key=i item=thread}
                    <li class="fmm_level_thread_{$thread.author|escape:'htmlall':'UTF-8'}">{$thread.message}{*HTML Content*}<span class="date">{l s='Posted' mod='productquotation'}: {$thread.date|escape:'htmlall':'UTF-8'}</span></li>
                {/foreach}
                </ul>
            </div>
        {/if}
        <div class="form-group">
            <label class="control-label col-lg-2 required">{l s='Send Message' mod='productquotation'}</label>
            <div class="col-lg-9">
                <textarea class="textarea-autosize" rows="6" name="message"></textarea>
            </div>
        </div>
        <div class="alert alert-info">
        {l s='Predefined variable {first_name} {last_name} {customer_mail} {id_quotation}' mod='productquotation'}
        </div>
        <div class="panel" style="margin-top: 20px; min-height: 68px">
            <a href="{$quote_link|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-left"><i class="icon-arrow"></i> {l s='Go to quotation' mod='productquotation'}</a>
        </div>
        <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminQuotationMessages')|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-left" type="submit">
                <i class="process-icon-back"></i> {l s='Back to List' mod='productquotation'}
            </a>
            <button name="submitMessage" class="btn btn-default pull-right" type="submit">
                <i class="process-icon-envelope"></i> {l s='Send Message' mod='productquotation'}
            </button>
        </div>
    </div>
</form>
<style>{literal}
#fmm_message_tree { clear: both; padding: 0 0 40px; float: none !important;}

#fmm_message_tree ul { padding: 0px; list-style: none;}

#fmm_message_tree ul li { padding: 15px 20px 0px; font-size: 13px; color: #333333; border-left: 2px solid #333333;
margin-top: 10px; margin-bottom: 12px; clear: both}

#fmm_message_tree ul li span { display:block; background: #333333; color: #fff; padding: 2px 10px;
font-size: 11px; line-height: 18px; margin-left: -20px; margin-right: -20px; margin-top: 10px}

#fmm_message_tree ul li.fmm_level_thread_0 { margin-left: 2%; background: #F6F6F6}

#fmm_message_tree ul li.fmm_level_thread_1 { margin-left: 12%; background: #E2FFE4}
{/literal}
</style>