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
<form class="form-horizontal" enctype="multipart/form-data" method="post" id="configuration_form" action="{$action|escape:'htmlall':'UTF-8'}">
	<div class="panel col-lg-12">
		<div class="panel-heading">{l s='Quotes by Users' mod='productquotation'} <span class="badge">{$count|escape:'htmlall':'UTF-8'}</span></div>
		<div class="table-responsive-row clearfix">
			<table class="table tag">
				<thead>
					<tr class="nodrag nodrop">
					<th class="fixed-width-xs center"><span class="title_box active">ID</span></th>
					<th class=""><span class="title_box">{l s='Client' mod='productquotation'}</span></th>
					<th class="center"><span class="title_box">{l s='Status' mod='productquotation'}</span></th>
					<th class="center"><span class="title_box">{l s='Date' mod='productquotation'}</span></th>
					<th class="fixed-width-xs center"><span class="title_box">{l s='Action' mod='productquotation'}</span></th>
					</tr>
				</thead>

				<tbody>
					{if empty($quotes)}
						<tr>
							<td colspan="5" class="list-empty">
								<div class="list-empty-msg">
								<i class="icon-warning-sign list-empty-icon"></i>
									{l s='No records found' mod='productquotation'}
								</div>
							</td>
						</tr>
					{else}
					{foreach from=$quotes item=quote name=quotes}
					<tr>
						<td class="fixed-width-xs center">{$quote.id_productquotation|escape:'htmlall':'UTF-8'}</td>
						
						<td>{$quote.client|escape:'htmlall':'UTF-8'}</td>
						<td class="center">
							<select onchange="setStatusTrigger({$quote.id_productquotation|escape:'htmlall':'UTF-8'}, this.value, this);">
							<option value="{$quote.status|escape:'htmlall':'UTF-8'}" selected="selected">{$quote.status_name|escape:'htmlall':'UTF-8'}</option>
							<option value="0">{l s='Set As Pending' mod='productquotation'}</option>
							<option value="1">{l s='In study' mod='productquotation'}</option>
							<option value="2">{l s='Validated By Client' mod='productquotation'}</option>
							<option value="3">{l s='Rejected' mod='productquotation'}</option>
							<option value="4">{l s='Cancelled' mod='productquotation'}</option>
							<option value="5">{l s='Ordered' mod='productquotation'}</option>
							<option value="7">{l s='Approved' mod='productquotation'}</option>
							<option style="color:red" value="6">{l s='DELETE' mod='productquotation'}</option>
						</select><img src="{$gify_url|escape:'htmlall':'UTF-8'}" class="gify_img" style="display: none" /></td>
						<td class="center">{$quote.date|escape:'htmlall':'UTF-8'}</td>
						<td class="text-right">
							<div class="btn-group pull-right">
								<a title="View" class="btn btn-default" href="{$action|escape:'htmlall':'UTF-8'}&viewproductquotation&id_productquotation={$quote.id_productquotation|escape:'htmlall':'UTF-8'}"><i class="icon-search-plus"></i> {l s='View' mod='productquotation'}</a>
							</div>
						</td>
					</tr>
					{/foreach}
					{/if}
				</tbody>
			</table>
		</div>
	</div>
		
</form>
<script>{literal}
	function setStatusTrigger(x, y, z) {
		z = $(z).parent().find('.gify_img');
		z.show();
		var controller_url = "{/literal}{$action|escape:'htmlall':'UTF-8'}{literal}&updatestatus=1";
		controller_url = htmlEncode(controller_url);
		$.ajax({
				type	: "POST",
				cache	: false,
				url		: controller_url,
				data : {quote_id:x, value:y},
				success: function(data) {
					z.hide();
					result = parseInt(data);
					if (result == -1) {
                        window.location.reload(false);
                    }
				},
				error : function(XMLHttpRequest, textStatus, errorThrown) {
					z.hide();
				}
		});
    }
	function htmlEncode(input) {
    return String(input)
        .replace(/&amp;/g, '&');
	}{/literal}
</script>