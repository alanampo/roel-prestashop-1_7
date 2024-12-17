{*
* quote
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FME Modules
*  @copyright 2020 fmemodules All right reserved
*  @license   FMM Modules
*  @package   quote
*}
{include file="$tpl_dir./errors.tpl"}

{block name="page_content"}

<input type="hidden" id="token" name="token" value="{$token}" />
<script>var fmm_label_fail = "{l s='Email incorrect or Empty' mod='productquotation'}"</script>
{if $success > 0}
<p class="alert alert-success">{l s='Quotation submitted' mod='productquotation'}</p>
{/if}

<form action="{$form_action|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" method="post" id="fmm_quote_form">


{if isset($products) && $products}
    {if isset($summary_fields) && $summary_fields}
    <div id="">
        <h3 class="page-subheading">{l s='Please fill in form.' mod='productquotation'}</h3>
        

        {if $ps_17 <= 0}<script src="{if $force_ssl == 1}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}modules/quotefields/views/js/quotefields_ps16.js"></script>{/if}
<section>
<fieldset id="quote_fields" class="account_creation form-group" style="border: none;">



    <div class="form-group text form-group row">
        <label class="col-md-3 form-control-label">
        {l s='Email' mod='productquotation'}
        </label>

        <div class="col-md-6">
            <input type="text" value="{if isset($email)}{$email}{/if}" name="email" class="validate form-control" id="fmm_email" required="required" />

        </div>
        <div class="clearfix"></div>
    </div>

    {foreach from=$summary_fields item=field}
    {if !empty($field['sub_heading'])}<h3 class="page-subheading">{$field['sub_heading']|escape:'htmlall':'UTF-8'}</h3>{/if}
        <div class="clearfix"></div>
        <div class="rf_input_wrapper rf_only_f_{$field['dependant_field']|escape:'htmlall':'UTF-8'} required form-group text form-group row{if $field['dependant'] > 0} rf_no_display rf_no_display_{$field['dependant_field']|escape:'htmlall':'UTF-8'}_{$field['dependant_value']|escape:'htmlall':'UTF-8'}{/if}"
        data-id="{$field.id_fmm_quote_fields|escape:'htmlall':'UTF-8'}"
        data-f="{$field['dependant_field']|escape:'htmlall':'UTF-8'}"
        data-v="{$field['dependant_value']|escape:'htmlall':'UTF-8'}">
            <label class="rf_input_label {if $field['value_required']} required {/if}{if $version >= 1.7}col-md-3{/if} form-control-label">
                {if $field['value_required'] AND $version >= 1.7}<span style="color: #FF5555!important">*</span>{/if}
                {$field['field_name']|escape:'htmlall':'UTF-8'}</label>
            <div class="{if $version >= 1.7}col-md-6{/if}">
            {assign var='field_value' value=''}
            {if $field['field_type'] eq 'text'}
                {assign var="text_default_value" value=$field['default_value']}
                {if $field.editable == 0}
                    {if isset($field_value) AND $field_value}
                        <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        <input type="text"
                        name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        class="text {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if} form-control"
                        {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}/>
                    {/if}
                {else}
                    <input type="text"
                    name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                    value="{if !empty($field_value) AND $field_value}{$field_value|escape:'htmlall':'UTF-8'}{elseif !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{/if}"
                    class="text {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if} form-control" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}/>
                {/if}

            {elseif $field['field_type'] eq 'textarea'}
                {assign var="texta_default_value" value=$field['default_value']}
                {if $field.editable == 0}

                        {if isset($field_value) AND $field_value}
                            <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                        {else}
                            <textarea name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                            class="form-control {if $field['value_required']}is_required{/if}"
                            data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                            {if isset($field['field_validation']) AND $field['field_validation']}data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}></textarea>
                        
                        {/if}
                {else}
                    <textarea name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                    class="form-control {if $field['value_required']}is_required{/if}"
                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                    {if isset($field['field_validation']) AND $field['field_validation']}data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}>{if !empty($field_value) AND $field_value}{$field_value|escape:'htmlall':'UTF-8'}{elseif !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{/if}</textarea>
                {/if}

            {elseif $field['field_type'] eq 'date'}
                {if $field.editable == 0}

                    {if isset($field_value) AND $field_value}
                        <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        <input type="text"
                        class="fields_datapicker form-control {if $field['value_required']} is_required {/if} validate_field"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                        data-validate="isDate"/>
                    {/if}
                {else}
                    <input class="fields_datapicker form-control {if $field['value_required']} is_required {/if} validate_field"
                    type="text"
                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                    name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                    value="{if !empty($field_value) AND $field_value}{$field_value|escape:'htmlall':'UTF-8'}{/if}"
                    data-validate="isDate"/>
                {/if}

            {elseif $field['field_type'] eq 'boolean'}

                {if $field.editable == 0}
                    {if isset($field_value) AND $field_value}
                        <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        <select class="select form-control {if $field['value_required']}is_required {/if}"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                        data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                            <option value="No">{l s='No' mod='productquotation'}</option>
                            <option value="Yes">{l s='Yes' mod='productquotation'}</option>
                        </select>
                    {/if}
                {else}
                    <select class="select form-control {if $field['value_required']}is_required {/if}"
                    name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                    data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                        <option value="No" {if !empty($field_value) AND $field_value == 'No'}selected="selected"{/if}>{l s='No' mod='productquotation'}</option>
                        <option value="Yes" {if !empty($field_value) AND $field_value == 'Yes'}selected="selected"{/if}>{l s='Yes' mod='productquotation'}</option>
                    </select>
                {/if}

            {elseif $field.field_type eq 'select'}

                {if $field.editable == 0}
                    {if isset($field_value) AND $field_value}
                        {$field_value = FieldsQuote::getFieldsValueById($field_value)}
                        <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        <select class="select form-control {if $field['value_required']}is_required {/if}"
                        name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                        <option value="">{l s='Select Option' mod='productquotation'}</option>
                        {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}
                            <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                    {/if}
                {else}
                    <select class="select form-control {if $field['value_required']}is_required {/if}"
                    name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]"
                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                    data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                        <option value="">{l s='Select Option' mod='productquotation'}</option>
                        {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}
                            <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                            {if isset($field_value) AND $summary_fields_value.field_value_id == $field_value}selected="selected"{/if}>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                {/if}

            {elseif $field['field_type'] eq 'radio'}
                <input class="rf_checkboxes" type="hidden" data-required="{$field['value_required']}" value="{if $field['dependant'] > 0}1{else}{count($field_value)}{/if}"{if $field['dependant'] > 0} data-depend="1"{else} data-depend="0"{/if}>
                {if $field.editable == 0}
                    {if isset($field_value) AND $field_value}
                        <span class="form-control">
                            {if isset($field_value) AND is_array($field_value)}
                                {FieldsQuote::getOptionValue(implode(',',$field_value))|escape:'htmlall':'UTF-8'}
                            {/if}
                        </span>
                    {else}
                        {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}
                            <div class="type_multiboxes" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                <input type="radio"
                                data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}"
                                data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                                id="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                class="form-control {if $field['value_required']}is_required {/if}"
                                name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}][]"
                                value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"/>
                                <label class="type_multiboxes top" for="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <span><span></span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </label>
                            </div>
                            <div class="clearfix"></div>
                        {/foreach}
                    {/if}
                {else}
                    {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}
                        <div class="type_multiboxes" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                            <input type="radio"
                            data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}"
                            data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                            id="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                            class="form-control {if $field['value_required']}is_required {/if}"
                            name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}][]"
                            value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                            {if isset($field_value) AND is_array($field_value) && in_array($summary_fields_value.field_value_id, $field_value)}checked="checked"{/if}
                            />
                            <label class="type_multiboxes top" for="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                <span><span></span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                            </label>
                        </div>
                        <div class="clearfix"></div>
                    {/foreach}
                {/if}

            {elseif $field['field_type'] eq 'checkbox'}
                <input class="rf_checkboxes" type="hidden" data-required="{$field['value_required']}" value="{if $field['dependant'] > 0}1{else}{count($field_value)}{/if}"{if $field['dependant'] > 0} data-depend="1"{else} data-depend="0"{/if}>
                {if $field.editable == 0}
                    {if isset($field_value) AND $field_value}
                        <span class="form-control">
                            {if isset($field_value) AND is_array($field_value)}
                                {FieldsQuote::getOptionValue(implode(',',$field_value))|escape:'htmlall':'UTF-8'}
                            {/if}
                        </span>
                    {else}
                        {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}
                            <div class="type_multiboxes checker" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                <input type="checkbox"
                                data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}"
                                data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                                value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}][]" id="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                class="{if $field['value_required']}is_required{/if}"/>
                                <label class="type_multiboxes" for="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </label>
                            </div>
                            <div class="clearfix"></div>
                        {/foreach}
                    {/if}
                {else}
                    {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}

                            <div class="type_multiboxes" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                <input type="checkbox"
                                data-field="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}"
                                data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                                value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}][]" id="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                class="{if $field['value_required']}is_required{/if} form-control"
                                {if isset($field_value) AND is_array($field_value) AND in_array($summary_fields_value.field_value_id, $field_value)}checked="checked"{/if}
                                />
                                <label class="type_multiboxes" for="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </label>
                            </div>
                            <div class="clearfix"></div>

                    {/foreach}
                {/if}

            {elseif $field['field_type'] eq 'multiselect'}
                <input class="rf_checkboxes" type="hidden" data-required="{$field['value_required']}" value="{count($field_value)}">
                {if $field.editable == 0}
                    {if isset($field_value) AND $field_value}
                        <span class="form-control">
                            {if isset($field_value) AND is_array($field_value)}
                                {FieldsQuote::getOptionValue(implode(',',$field_value))|escape:'htmlall':'UTF-8'}
                            {/if}
                        </span>
                    {else}
                        <select name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}][]"
                        multiple="multiple" 
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        class="type_multiboxes multiselect form-control {if $field['value_required']}is_required {/if}">
                            {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}
                                <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                        <p><small>{l s='Hold CTRL/Command key to select multiple values.' mod='productquotation'}</small></p>
                    {/if}
                {else}
                    <select name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}][]"
                    multiple="multiple"
                    class="type_multiboxes multiselect form-control {if $field['value_required']}is_required {/if}">
                        {foreach from=$summary_fields_values[$field['id_fmm_quote_fields']] item=summary_fields_value}
                            <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}" {if isset($field_value) AND is_array($field_value) AND in_array($summary_fields_value.field_value_id, $field_value)}selected="selected"{/if}>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                    <p><small>{l s='Hold CTRL/Command key to select multiple values.' mod='productquotation'}</small></p>
                {/if}

            {elseif $field['field_type'] eq 'image'}
                <div id="field_image_{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                    {assign var='root_dir' value=($smarty.const._PS_ROOT_DIR_|cat:'/')}
                    {if $field.editable == 0}
                        {assign var='field_value' value=''}
                        {if isset($value_reg_fields) AND $value_reg_fields}
                            {foreach from=$value_reg_fields item=field_edit}
                            
                                {if !empty($field_edit) AND $field_edit AND $field_edit['id_fmm_quote_fields'] == $field['id_fmm_quote_fields'] AND !empty($field_edit['value'])}
                                    {assign var='field_value' value=$field_edit['value']|replace:$root_dir:''}
                                {/if}
                            
                            {/foreach}
                        {else}
                            {assign var='field_value' value=''}
                        {/if}

                        {if isset($field_value) AND $field_value}
                            <img id="preview-image-{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}" class="image_container" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}{$field_value|escape:'htmlall':'UTF-8'}">
                        {else}
                            <img id="preview-{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}" class="image_container" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/quotefields/views/img/empty.png">
                            <input type="file" name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]" id="image_{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}" class="image_input {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if} data-id="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                           
                        {/if}
                    {else}
                        
                        <input type="file" name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}]" id="image_{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}" class="image_input {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if} data-id="{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                        {if isset($field.extensions) AND $field.extensions} <p>{l s='Allowed file types' mod='productquotation'}: {$field.extensions|escape:'htmlall':'UTF-8'}</p>{/if}
                        
                    {/if}
                </div>

            {elseif $field['field_type'] eq 'attachment'}
                    <div id="field_attachment_{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}">
                    {assign var='root_dir' value=($smarty.const._PS_ROOT_DIR_|cat:'/')}
                    {if $field.editable == 0}
                        {assign var='field_value' value=''}
                        {if isset($value_reg_fields) AND $value_reg_fields}
                            {foreach from=$value_reg_fields item=field_edit}
                            
                                {if !empty($field_edit) AND $field_edit AND $field_edit['id_fmm_quote_fields'] == $field['id_fmm_quote_fields'] AND !empty($field_edit['value'])}
                                    {assign var='field_value' value=$field_edit['value']|replace:$root_dir:''}
                                {/if}
                            
                            {/foreach}
                        {else}
                            {assign var='field_value' value=''}
                        {/if}

                        {if isset($field_value) AND $field_value}
                            <a class="btn button btn-primary" href="{$actionLink|escape:'htmlall':'UTF-8'}&field={base64_encode({$field.id_fmm_quote_fields|escape:'htmlall':'UTF-8'})}">{pathinfo($field_value|escape:'htmlall':'UTF-8', $smarty.const.PATHINFO_FILENAME)}
                            </a>
                            <br>
                        {else}
                            <img id="preview-{$field.id_fmm_quote_fields|escape:'htmlall':'UTF-8'}" class="image_container" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/quotefields/views/img/empty.png">
                            <input type="file" name="fields[{$field.id_fmm_quote_fields|escape:'htmlall':'UTF-8'}]" value="{if !empty($value_reg_fields) AND $value_reg_fields}{foreach from=$value_reg_fields item=field_edit}{if !empty($field_edit) AND $field_edit AND $field_edit.id_fmm_quote_fields == $field.id_fmm_quote_fields AND !empty($field_edit['value'])}{$field_edit['value']|escape:'htmlall':'UTF-8'}{/if}{/foreach}{elseif empty($value_reg_fields) AND !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{/if}"  class="form-control attachment {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if}>
                            {if isset($field.extensions) AND $field.extensions} <p>{l s='Allowed file types' mod='productquotation'}: {$field.extensions|escape:'htmlall':'UTF-8'}</p>{/if}
                            
                        {/if}
                    {else}
                        
                        <input type="file" name="fields[{$field.id_fmm_quote_fields|escape:'htmlall':'UTF-8'}]" value="{if !empty($value_reg_fields) AND $value_reg_fields}{foreach from=$value_reg_fields item=field_edit}{if !empty($field_edit) AND $field_edit AND $field_edit.id_fmm_quote_fields == $field.id_fmm_quote_fields AND !empty($field_edit['value'])}{$field_edit['value']|escape:'htmlall':'UTF-8'}{/if}{/foreach}{elseif empty($value_reg_fields) AND !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{/if}"  class="form-control attachment {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if}>
                        {if isset($field.extensions) AND $field.extensions} <p>{l s='Allowed file types' mod='productquotation'}: {$field.extensions|escape:'htmlall':'UTF-8'}</p>{/if}
                        
                        {/if}
                    </div>

            {elseif $field['field_type'] eq 'message'}
                <div class="alert alert-{if isset($field['alert_type']) && $field['alert_type'] && $field['alert_type'] == 'error'}danger {$field['alert_type']|escape:'htmlall':'UTF-8'}{else}{$field['alert_type']|escape:'htmlall':'UTF-8'}{/if}">
                    {$field['default_value']|escape:'htmlall':'UTF-8'}
                </div>
                <input type="hidden" name="fields[{$field['id_fmm_quote_fields']|escape:'htmlall':'UTF-8'}][]" value="{$field['default_value']|escape:'htmlall':'UTF-8'}" />
            {/if}
            </div>
            <div class="clearfix"></div>
        </div>
    {/foreach}
</fieldset>
{if $is_psgdpr}
    <div class="form-group row ">
        <label class="col-md-3 form-control-label required"></label>
        <div class="col-md-6">
            {hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
        </div>
    </div>
{/if}
{literal}
<style>
.rf_no_display { display: none;}
#quote_fields .radio-inline, #quote_fields .checkbox { display: inline-block; margin-right: 3%}
#quote_fields .radio-inline .radio, #quote_fields .checkbox .checker { display:inline-block; padding-right: 3px; vertical-align: middle}
</style>
{/literal}
</section>




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
                <div class="pq_img"><a href="{$product.link|escape:'htmlall':'UTF-8'}"><img width="60" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" /></a></div>
                <h6><a href="{$product.link|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a><br/><a class="fmm_quote_del" onclick="dropItemQuote({$product.id_quotes_products|escape:'htmlall':'UTF-8'})"></a><strong>{l s='Quantity' mod='productquotation'}: <input type="number" tabindex="{$product.id_quotes_products|escape:'htmlall':'UTF-8'}" value="{$product.qty|escape:'htmlall':'UTF-8'}" min="1" class="fmm_quote_row_qty" /> ({$product.price|escape:'htmlall':'UTF-8'})</strong></h6>
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
        {if isset($summary_fields) && $summary_fields}
    <button class="button btn btn-primary fmm_quote_sb_btn" id="submitQuote" onclick="return validateForm(this);" name="submitQuote" type="submit"><span>{l s='Submit' mod='productquotation'} <i class="icon-chevron-right right"></i></span></button>
    {hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
    {/if}
        <button class="button btn btn-primary" type="button" onclick="fmmQuoteUpdate(this);">{l s='Update Quote' mod='productquotation'}</button>
    </footer>
</div>
{/if}
</form>
{/block}
