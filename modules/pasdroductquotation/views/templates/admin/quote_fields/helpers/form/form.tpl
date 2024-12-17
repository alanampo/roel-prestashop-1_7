{*
* Registration
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FME Modules
*  @copyright 2019 fmemodules All right reserved
*  @license   FMM Modules
*  @package   Registration
*}

{if $version < 1.6}{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}{/if}
<div class="leadin">{block name="leadin"}{/block}</div>

<form class="panel form-horizontal" action="{$currentIndex|escape:'htmlall':'UTF-8'}&token={$currentToken|escape:'htmlall':'UTF-8'}" name="fmm_quote_fields_form" id="fmm_quote_fields_form"   method="post" enctype="multipart/form-data">
	{if $currentObject->id}<input type="hidden" name="id_fmm_quote_fields" value="{$currentObject->id|intval}" />{/if}
	<input type="hidden" id="currentFormTab" name="currentFormTab" value="informations" />
	<div id="advance_blog_informations" class="cart_rule_tab">
		{include file=$fieldinfo}
	</div>
	<div class="separation"></div>
	{if $version >= 1.6}
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminQuoteFields')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='productquotation'}</a>
			<button type="submit" name="submitAddfmm_quote_fields" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='productquotation'}</button>
			<button type="submit" name="submitAddfmm_quote_fieldsAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='productquotation'}</button>
		</div>
		{else}
		    <div style="text-align:center">
				<input type="submit" value="{l s='Save' mod='productquotation'}" class="button" name="submitAddfmm_quote_fields" id="{$table|escape:'htmlall':'UTF-8'}_form_submit_btn" />
			</div>
		{/if}
</form>

<script language="javascript">
	var editableFields = ['message'];
	var currentToken = "{$currentToken|escape:'htmlall':'UTF-8'}";
	var currentFormTab = "{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'htmlall':'UTF-8'}{else}informations{/if}";

	var languages = new Array();
	{foreach from=$languages item=language key=k}
		languages[{$k|escape:'htmlall':'UTF-8'}] = {
			id_lang: "{$language.id_lang|escape:'htmlall':'UTF-8'}",
			iso_code: "{$language.iso_code|escape:'htmlall':'UTF-8'}",
			name: "{$language.name|escape:'htmlall':'UTF-8'}"
		};
	{/foreach}
	displayFlags(languages, {$id_lang_default|escape:'htmlall':'UTF-8'});

	function displayCartRuleTab(tab) {
		$('.cart_rule_tab').hide();
		$('.tab-page').removeClass('selected');
		$('#advance_blog_' + tab).show();
		$('#advance_blog_link_' + tab).addClass('selected');
		$('#currentFormTab').val(tab);
	}

	$('.cart_rule_tab').hide();
	$('.tab-page').removeClass('selected');
	$('#advance_blog_' + currentFormTab).show();
	$('#advance_blog_link_' + currentFormTab).addClass('selected'); 


	function checkOptions(){
		var field_type = $('#field_type').val();

		if (jQuery.inArray(field_type, editableFields) === -1) {
			$('#field-editable').show();
		} else {
			$('#field-editable').hide();
		}

		if (field_type == 'image' || field_type == 'attachment') {
			$('#setting-attachment').show();
		} else {
			$('#setting-attachment').hide();
		}

		if (field_type == 'message') {
			$('#alert-types').show();
		} else {
			$('#alert-types').hide();
		}

		if ( field_type == "multiselect" || field_type == "select" ||  field_type == "checkbox" || field_type == "radio"){
			$("#option_container").show();
			//$("#show_options").show();
		} else {
			$("#option_container").hide();
			//$("#show_options").hide();
		}

		if ( field_type == "text" || field_type == "textarea" ||  field_type == "message"){
			$("#default_value_holder").show();
		} else {
			$("#default_value_holder").hide();
		}

		if ( field_type == "text" || field_type == "textarea"){
			$("#field_validation_holder").show();
		} else {
			$("#field_validation_holder").hide();
		}
	}

	$("#field_type").change(function() {
		checkOptions();
		if($('.option_field').length > 1) {
			$('.remove_option').show();
		}
	});
	checkOptions();
</script>
