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
{extends file="helpers/form/form.tpl"}
{block name="input"}
{if $input.name == 'products'}
<div class="radio">
    <label><input type="radio" name='PQUOTE_SELECTION_CRITEREA' onclick="checkMate(this);" value='0'{if $fields_value.PQUOTE_SELECTION_CRITEREA <= 0} checked="checked"{/if}>{l s='All Products' mod='productquotation'}</label>
</div>
<div class="radio">
    <label><input type="radio" name='PQUOTE_SELECTION_CRITEREA' onclick="checkMate(this);" value='1'{if $fields_value.PQUOTE_SELECTION_CRITEREA == 1} checked="checked"{/if}>{l s="Selected Products" mod="productquotation"}</label>
</div>
<div class="radio">
    <label><input type="radio" name='PQUOTE_SELECTION_CRITEREA' onclick="checkMate(this);" value='2'{if $fields_value.PQUOTE_SELECTION_CRITEREA == 2} checked="checked"{/if}>{l s='Selected Categories' mod='productquotation'}</label>
</div>
<div class="form-group col-lg-8" id="pquote_category_list"{if $fields_value.PQUOTE_SELECTION_CRITEREA == 2} style="display: block"{else} style="display: none"{/if}>
    <br/>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th> </th>
                <th>
                    <span class="title_box">
                        {l s='ID' mod='productquotation'}
                    </span>
                </th>
                <th>
                    <span class="title_box">
                        {l s='Name' mod='productquotation'}
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
            {if !isset($categories) || empty($categories)}
            <tr>
                <td>{l s='No brands found.' mod='productquotation'}</td>
            </tr>
            {else}
            {foreach from=$categories item=category}
                <tr>
                <td>
                    <input type="checkbox" name="category[]" value="{$category.id_category}"{if isset($fields_value.selection) && in_array($category.id_category, $fields_value.selection)} checked="checked"{/if} />
                </td>
                <td>
                    {$category.id_category|escape:'htmlall':'UTF-8'}
                </td>
                <td>
                    {$category.name|escape:'htmlall':'UTF-8'}
                </td>
                </tr>
            {/foreach}
            {/if}
        </tbody>
    </table>
</div>
<div class="form-group col-lg-12{if $ps_17 <= 0} ps_16_specific{/if}" id="pquote_product_list"{if $fields_value.PQUOTE_SELECTION_CRITEREA == 1} style="display: block"{else} style="display: none"{/if}>
    <br/>
    <label class="control-label col-lg-3 col-md-pull-3">{l s='Find Product' mod='productquotation'}</label>
    <div class="col-lg-9 col-md-pull-3">
        <div class="col-lg-8 placeholder_holder">
            <input type="text" placeholder="{l s='Example' mod='productquotation'}: Blue XL shirt" onkeyup="getRelProducts(this);" />
            <div id="rel_holder"></div>
            <div id="rel_holder_temp">
                <ul>
                    {if (!empty($products))}
                        {foreach from=$products item=product}
                            <li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media"><div class="media-left"><img src="{Context::getContext()->link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}" class="media-object image"></div><div class="media-body media-middle"><span class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}" name="product[]"></li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    </div>
</div>
{/if}
{$smarty.block.parent}
<script>
var mod_url = "{$action_url nofilter}";//html content
var error_msg = "{l s='Product is already in selection list' mod='productquotation'}";
{literal}
function checkMate(_e) {
	var e_val = parseInt(jQuery(_e).val());
    if (e_val == 2) {
        $('#pquote_category_list').show();
        $('#pquote_product_list').hide();
    }
    else if (e_val == 1) {
        $('#pquote_category_list').hide();
        $('#pquote_product_list').show();
    }
    else {
        $('#pquote_category_list, #pquote_product_list').hide();
    }
    console.log('selected value: '+e_val);
}
function getRelProducts(e) {
	var search_q_val = $(e).val();
	//controller_url = controller_url+'&q='+search_q_val;
	if (typeof search_q_val !== 'undefined' && search_q_val) {
		$.ajax({
			type: 'GET',
			dataType: 'json',
			url: mod_url + '&q=' + search_q_val,
			success: function(data)
			{
				var quicklink_list ='<li class="rel_breaker" onclick="relClearData();"><i class="material-icons">&#xE14C;</i></li>';
				$.each(data, function(index,value){
					if (typeof data[index]['id'] !== 'undefined')
						quicklink_list += '<li onclick="relSelectThis('+data[index]['id']+','+data[index]['id_product_attribute']+',\''+data[index]['name']+'\',\''+data[index]['image']+'\');"><img src="' + data[index]['image'] + '" width="60"> ' + data[index]['name'] + '</li>';
				});
				if (data.length == 0) {
					quicklink_list = '';
				}
				$('#rel_holder').html('<ul>'+quicklink_list+'</ul>');
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				console.log(textStatus);
			}
		});
	}
	else {
		$('#rel_holder').html('');
	}
}
function relSelectThis(id, ipa, name, img) {
	if ($('#row_' + id).length > 0) {
		showErrorMessage(error_msg);
	} else {
	  var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="'+img+'" class="media-object image"></div><div class="media-body media-middle"><span class="label">'+name+'&nbsp;(ID:'+id+')</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="'+id+'" name="product[]"></li>'
	  $('#rel_holder_temp ul').append(draw_html);
	}
}
function relClearData() {
    $('#rel_holder').html('');
}
function relDropThis(e) {
    $(e).parent().parent().remove();
}
</script>
<style type="text/css">
#pquote_category_list { max-height: 600px; overflow-y: scroll}
#rel_holder ul { position: absolute; left: 12px; border-radius: 4px; top: 40px; margin: 0px 0 20%; padding: 0; background: #fff;
border: 1px solid #BBCDD2; z-index: 999}
#rel_holder ul li { list-style: none; padding: 5px 10px; display: block; margin: 0px}
#rel_holder ul li:hover { cursor: pointer; background: #25B9D7}
#rel_holder ul li.rel_breaker { padding: 0px; margin: -1px -22px 0 0; background: #fff; float: right;border: 1px solid #BBCDD2;
 border-left: 0px; height: 24px;}
#rel_holder ul li.rel_breaker:hover { background: #fff;}
.rel_breaker i {font-size: 22px; color: #E50B70; cursor: pointer}
#rel_holder_temp { clear: both; padding: 10px 0}
#rel_holder_temp ul { padding: 0; margin: 0}
#rel_holder_temp ul li { list-style: none; padding: 3px 5px; border-radius: 5px; margin: 6px 0; border: 1px solid #E5E5E5;
display: block}
#rel_holder_temp ul li div { display: inline-block; vertical-align: middle}
#rel_holder_temp ul li .media-left { width: 8%}
#rel_holder_temp ul li .media-left img { max-width: 100%}
#rel_holder_temp ul li .media-body { width: 86%; margin-left: 5%}
#rel_holder_temp ul li .media-body span { float: left; font-size: 13px; color: #6c868e; font-weight: normal; white-space: normal !important;
text-align: left; width: 92%}
#rel_holder_temp ul li .media-body i { float: right; cursor: pointer}
.placeholder_holder { position: relative}
.ps_16_specific .material-icons {font-size: 1px;color: #fff;}
.ps_16_specific .material-icons::before {content: "\f00d"; font-family: "FontAwesome"; font-size: 25px;text-align: center;
color: red;font-style: normal; text-indent: -9999px; font-weight: normal; line-height: 20px;}
</style>{/literal}
{/block}