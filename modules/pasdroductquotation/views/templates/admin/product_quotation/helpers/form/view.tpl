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
<script src="//cdn.ckeditor.com/4.5.4/full/ckeditor.js"></script>
<form class="form-horizontal" enctype="multipart/form-data" method="post" id="configuration_form" action="{$action|escape:'htmlall':'UTF-8'}">
    {if $id > 0}<input type="hidden" value="{$id|escape:'htmlall':'UTF-8'}" name="id_productquotation_templates" />{/if}
<div class="panel">
    <div class="panel-heading"><i class="icon-cogs"></i> {l s='Create/Edit Template' mod='productquotation'}</div>
    <div class="form-wrapper">
        <div class="form-group">
        <div>
            <label class="control-label col-lg-3 required">Title</label>
            <div class="col-lg-9">
                <input type="text" value="{if !empty($data)}{$data.title|escape:'htmlall':'UTF-8'}{/if}" name="title" size="5" class="form-control">
            </div>
        </div>
        </div>
        <div class="form-group">
        <div>
            <label class="control-label col-lg-3 required">{l s='Status' mod='productquotation'}</label>
            <div class="col-lg-9">      
                <p class="radio">
                    <label for="PQ_STAT_1"><input type="radio" value="1"{if $data.status > 0} checked="checked"{/if} id="PQ_STAT_1" name="status">{l s='Enable' mod='productquotation'}</label>
                </p>
                <p class="radio">
                    <label for="PQ_STAT_2"><input type="radio" value="0"{if $data.status <= 0} checked="checked"{/if} id="PQ_STAT_2" name="status">{l s='Disable' mod='productquotation'}</label>
                </p>
            </div>
        </div>
        </div>
        <div class="form-group">
            {if $id > 9990}<div class="col-lg-10"><div class="alert alert-info">{l s='You can not edit shop or language for which template was made.' mod='productquotation'}</div></div>{/if}
        <div>
            <label class="control-label col-lg-3">{l s='Select Language' mod='productquotation'}</label>
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="fixed-width-xs"> </th>
                            <th class="fixed-width-xs"><span class="title_box">ID</span></th>
                            <th>
                                <span class="title_box">
                                    {l s='Language name' mod='productquotation'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" value="0" id="groupBox_1" onclick="selectAllLangs(this);" class="groupBox" name="langs[]">
                            </td>
                            <td>0</td>
                            <td>
                                <label for="groupBox_1">{l s='All' mod='productquotation'}</label>
                            </td>
                        </tr>
                        {foreach from=$languages key=k item=_item}
                        <tr>
                            <td>
                                <input type="checkbox" value="{$_item.id_lang|escape:'htmlall':'UTF-8'}"{if in_array($_item.id_lang, $lang_data)} checked="checked"{/if} id="groupBox_2" class="groupBox sub_l" name="langs[]">
                            </td>
                            <td>{$_item.id_lang|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                <label for="groupBox_2">{$_item.name|escape:'htmlall':'UTF-8'}</label>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        <div class="form-group">
        <div>
            <label class="control-label col-lg-3">{l s='Select Shop' mod='productquotation'}</label>
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="fixed-width-xs"> </th>
                            <th class="fixed-width-xs"><span class="title_box">ID</span></th>
                            <th>
                                <span class="title_box">
                                    {l s='Store name' mod='productquotation'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" value="0" id="groupBox_1" onclick="selectAllShops(this);" class="groupBox" name="shops[]">
                            </td>
                            <td>0</td>
                            <td>
                                <label for="groupBox_1">{l s='All' mod='productquotation'}</label>
                            </td>
                        </tr>
                        {foreach from=$shops item=_item}
                        <tr>
                            <td>
                                <input type="checkbox" value="{$_item.id_shop|escape:'htmlall':'UTF-8'}"{if in_array($_item.id_shop, $shop_data)} checked="checked"{/if} id="groupBox_2" class="groupBox sub_sp" name="shops[]">
                            </td>
                            <td>{$_item.id_shop|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                <label for="groupBox_2">{$_item.name|escape:'htmlall':'UTF-8'}</label>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        <div class="form-group">
            <div class="panel-heading" style="clear:both"><i class="icon-cogs"></i> {l s='Template' mod='productquotation'}</div>
            <div class="col-lg-12">
                <textarea id="fmm_pq_area" name="template_content">{if !empty($data)}{$data.form}{*HTML Content*}{/if}</textarea>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <button name="submitTemplates" class="btn btn-default pull-right" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='productquotation'}</button>
    </div>  
</div>

</form>
<script>{literal}
    CKEDITOR.replace("fmm_pq_area",
    {
        customConfig : "{/literal}{$pq_url|escape:'htmlall':'UTF-8'}{literal}views/js/ckeditor_config.js"
    });
     
    function selectAllLangs(e) {
        if (jQuery(e).is(":checked")) {
            jQuery('.sub_l').attr('disabled','disabled');
        }
        else
        {
            jQuery('.sub_l').removeAttr('disabled');
        }
    }
    function selectAllShops(g) {
        if (jQuery(g).is(":checked")) {
            jQuery('.sub_sp').attr('disabled','disabled');
        }
        else
        {
            jQuery('.sub_sp').removeAttr('disabled');
        }
    }
    {/literal}
</script>
