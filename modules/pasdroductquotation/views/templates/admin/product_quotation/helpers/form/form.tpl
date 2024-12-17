{*
* 2007-2019 PrestaShop
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
*  @author    FMM Modules
*  @copyright 2019 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}



<form class="form-horizontal" enctype="multipart/form-data" method="post" id="configuration_form" action="{$action|escape:'htmlall':'UTF-8'}">
	<div class="panel col-lg-12">
		<input type="hidden" id="id_productquotation" name="id_productquotation" value="{$id_productquotation}">
		<input type="hidden" id="id_quote" name="id_quote" value="{$id_quote}">
		
		<div class="panel-heading">{l s='Quotation Details' mod='productquotation'}</div>
		<table class="table">
			<tr></tr>
			<tr><td style="border-right: 1px solid #EEEEEE; font-weight: bold">Client</td> <td>{$quotation_details_basic.client|escape:'htmlall':'UTF-8'}</td></tr>
			<tr><td style="border-right: 1px solid #EEEEEE; font-weight: bold">Status</td> <td><select onchange="setStatusTrigger({$quotation_details_basic.id_productquotation|escape:'htmlall':'UTF-8'}, this.value, this);">
							<option value="{$quotation_details_basic.status|escape:'htmlall':'UTF-8'}" selected="selected">{$quotation_details_basic.status_name|escape:'htmlall':'UTF-8'}</option>
							<option value="0">{l s='Set As Pending' mod='productquotation'}</option>
							<option value="1">{l s='In study' mod='productquotation'}</option>
							<option value="2">{l s='Validated By Client' mod='productquotation'}</option>
							<option value="3">{l s='Rejected' mod='productquotation'}</option>
							<option value="4">{l s='Cancelled' mod='productquotation'}</option>
							<option value="5">{l s='Ordered' mod='productquotation'}</option>
							<option value="7">{l s='Approved' mod='productquotation'}</option>
						</select><img src="{$gify_url|escape:'htmlall':'UTF-8'}" class="gify_img" style="display: none" /></td>
			<tr><td style="border-right: 1px solid #EEEEEE; font-weight: bold">{l s='Email' mod='productquotation'}</td> <td>{$quotation_details_basic.email|escape:'htmlall':'UTF-8'}</td></tr></tr>
			<input type="hidden" name="emaill" value="{$quotation_details_basic.email|escape:'htmlall':'UTF-8'}" />

			<tr><td style="border-right: 1px solid #EEEEEE; font-weight: bold">{l s='Currency' mod='productquotation'}</td> <td id="currency_sign">{$currency_sign|escape:'htmlall':'UTF-8'}</td></tr></tr>
			<tr><td style="border-right: 1px solid #EEEEEE; font-weight: bold">{l s='Coupon Sent' mod='productquotation'}</td> <td>{if $quotation_details_basic.coupon_sent > 0}Yes{else}No{/if}</td></tr></tr>
			<tr><td style="border-right: 1px solid #EEEEEE; font-weight: bold">{l s='Date' mod='productquotation'}</td> <td>{$quotation_details_basic.date|escape:'htmlall':'UTF-8'}</td></tr></tr>
			{if $quotation_details_basic.coupon_sent > 0}<tr><td style="border-right: 1px solid #EEEEEE; font-weight: bold">{l s='Coupon ID' mod='productquotation'}</td> <td>{$quotation_details_basic.coupon_id|escape:'htmlall':'UTF-8'}</td>
			</tr>
		</tr>{/if}
		</table><br />
		<div class="panel" style="margin-top: 20px; min-height: 68px">
			<a href="{$message_link|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right"><i class="icon-envelope"></i> {l s='View/Send messages' mod='productquotation'}</a>
		</div>
	</div>
	<div class="panel col-lg-12">
	<div class="panel-heading">{l s='Client Answers' mod='productquotation'}</div>
			<table class="table">
			<thead>
				<tr>
					<th>{l s='Title' mod='productquotation'}</th>
					<th>{l s='Answer' mod='productquotation'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$get_quotation_data key=i item=quote}
					<tr>
						<td><strong>{$quote.title|escape:'htmlall':'UTF-8'}</strong></td>
						<td>{$quote.value|escape:'htmlall':'UTF-8'}</td>
					</tr>
				{/foreach}
				
				{foreach from=$user_data key=i item=user}
            <tr>
            	
                <td>{$user.field|escape:'htmlall':'UTF-8'}</td>

                {if $user.callback == 'downloadFile'}
                    <td style="margin-top: 15px;">
                        <a class="btn btn-default button" href="{$link->getModuleLink('productquotation','quotations')|escape:'htmlall':'UTF-8'}?downloadFile&l={base64_encode($user.value)|escape:'htmlall':'UTF-8'}" target="_blank">
                            
                        <img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productquotation/views/img/download.png" alt="{l s='Download Attachment' mod='productquotation'}" title="{l s='Download Attachment' mod='productquotation'}"/>
                    </a>
                    </td>
                 {elseif $user.callback == 'image'}
                    <td>
                    	<img id="preview-image" class="image_container" src="{$user.value|escape:'htmlall':'UTF-8'}"  height="88" width="88">
                    
                    </td>
                 {else}
                        {if isset($user.value) AND is_array($user.value)}
                           <td style="display: flex;margin-top: 15px;">   
                           {if $user.field_type eq 'select'}
                        		<select class="select form-control"
                                name="fields[{$user.id}]"
                                data-type="{$user.field_type|escape:'htmlall':'UTF-8'}">
                                    <option value="{$user.value}">{FieldsQuote::getOptionValue(implode(',',$user.value))|escape:'htmlall':'UTF-8'}-{l s='Selected' mod='productquotation'}
                                    </option>
                                    {foreach from=$user.allval item=summary_fields_value}
			                            <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
			                            </option>
			                        {/foreach}
                           		</select>
                           {/if}
                           {if $user.field_type eq 'radio'}
                           		
                           		<span style="margin-right: 22px;">
                           			<label class="type_multiboxes top" for="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">

                           			{FieldsQuote::getOptionValue(implode(',',$user.value))|escape:'htmlall':'UTF-8'} - {l s='Selected' mod='productquotation'}
                           			</label>
                           		<input type="radio" value="1" checked />
                           		
                           		

                           		</span>

                            	{foreach from=$user.allval item=summary_fields_value}
                            	<label class="type_multiboxes top" for="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <span><span></span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </label>

	                            <div class="type_multiboxes" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
	                                <input style="margin-right: 5px;" type="radio" name="fields[{$user.id}][]"
	                                id="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
	                                
	                                value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"/>
	                                
	                            </div>
                            <div class="clearfix"></div>
                       		 {/foreach}
                       		 
                           {/if}
                           {if $user.field_type eq 'multiselect'}

                        <select name="fieldsss[{$user.id}]"
                        multiple="multiple" 
                        class="type_multiboxes multiselect form-control ">
                            {foreach from=$user.value item=summary_fields_value}
                                <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}" selected="selected">{FieldsQuote::getOptionValue($summary_fields_value)|escape:'htmlall':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>

                        <select name="fields[{$user.id}][]"
                        multiple="multiple" 
                        class="type_multiboxes multiselect form-control ">
                            {foreach from=$user.allval item=summary_fields_value}
                                <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}" >{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>

                           {/if}
                           {if $user.field_type eq 'checkbox'}
                            	{foreach from=$user.value item=val}
                            		{FieldsQuote::getOptionValue($val)|escape:'htmlall':'UTF-8'}
                            		<input type="checkbox" name="fields[{$user.id}][]" value="{$val}" checked>
                        		{/foreach}
                           {/if}
                       </td>
                        {else}
                        <td style="display: flex;">
                        	{if $user.field_type eq 'text'}
                        		 <input type="text" name="fields[{$user.id}]" value="{$user.value|escape:'htmlall':'UTF-8'}">
                        		 
                        	{else}
                        		{$user.value|escape:'htmlall':'UTF-8'}
								{if is_array($user.value)}
									{FieldsQuote::getOptionValue(implode(',',$user.value))|escape:'htmlall':'UTF-8'}
								{/if}
                        	{/if}
                        </td>
                        {/if}
                    {/if}
            </tr>
        {/foreach}

			</tbody>
			</table>

			<div class="panel-footer">
				<button type="submit" name="submitAddfmm_quote_fields" value="1" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Update' mod='productquotation'}</button>
			</div>

	</div>


	<div class="panel col-lg-12">
	<div class="panel-heading">{l s='Products' mod='productquotation'}</div>
			<table class="table">
				{foreach from=$products item=product name=products}
					<tr>
						<td><a target="_blank" href="{$product.link|escape:'htmlall':'UTF-8'}"><img width="60" src="{$product.img_id}" /></a></td>
						<td><a target="_blank" href="{$product.link|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a>
						{if !empty($product.customizations)}
						<div class="pq_customized_data">
							{foreach from=$product.customizations item=cs name=cs}
								<i>{$cs.label}:{if $cs.type > 0}{$cs.value}{else}<img src="{$upload_directory}{$cs.value}_small" width="20" />{/if}</i><br />
							{/foreach}
						</div>
						{/if}
						</td>
						<td class="text-right"><strong>{$product.qty|escape:'htmlall':'UTF-8'}</strong> ({$product.price|escape:'htmlall':'UTF-8'})</td>
					</tr>
				{/foreach}
			</table>
			<div class="panel" style="width: 40%; float: right; margin-top: 20px;">
				<div class="table-responsive">
					<table class="table">
						<tbody>
							<tr><td class="text-right"><strong>{l s='Total' mod='productquotation'}</strong></td><td class="text-right" id="base_total">{$total|escape:'htmlall':'UTF-8'}</td></tr>
							<tr><td class="text-right"><strong>{l s='Discount' mod='productquotation'}</strong></td> <td class="text-right" id="fmm_discount">0.00</td></tr>
							<tr><td class="text-right"><strong>{l s='Calculated Total' mod='productquotation'}</strong></td> <td class="text-right" id="final_discount">-</td></tr>
						</tbody>
					</table>
				</div>
			</div>
			




			
			

			
			<div class="row panel" style="margin-top: 20px; clear: both; min-height: 70px">
			   <div class="col-xs-6">
			   	<input type="hidden" name="id_quotation" value="{$quotation_details_basic.id_productquotation|escape:'htmlall':'UTF-8'}" />

			<input type="hidden" name="total_quote_amount" value="{$total|escape:'htmlall':'UTF-8'}" />
			      <div class="panel panel-charges clearfix">
			         <div class="panel-heading">
			            {l s='Charges' mod='productquotation'}
			            <span class="panel-heading-action">
			            	{if $fee_price < 1}
			            	<button id="quote_charges"  type="button" class="btn btn-primary btn-sm">{l s='Add Charges' mod='productquotation'}</button>
			            	{/if}
			            </span>
			         </div>
			         <div class="" id="form_charges" style="display:none;">
			            <div class="form-horizontal well">
			               <div class="form-group">
			                  <label class="control-label col-lg-3">
			                  {l s='Type' mod='productquotation'}
			                  </label>
			                  <div class="col-lg-9">
			                     <select class="form-control" name="charge_type">
			                        <option value="general">General</option>
			                     </select>
			                  </div>
			               </div>
			               <div class="form-group" id="charge-name-block">
			                  <label class="control-label col-lg-3">
			                  {l s='Name' mod='productquotation'}
			                  </label>
			                  <div class="col-lg-9">
			                     <input class="form-control" type="text" name="charge_name" id="charge_name" >
			                  </div>
			               </div>
			              
			               <div class="form-group" id="charge-method-block">
			                  <label class="control-label col-lg-3">
			                  {l s='Method' mod='productquotation'}
			                  </label>
			                  <div class="col-lg-9">
			                     <select class="form-control" name="method_quote" id="method_quote">
			                     	<option value="2">{l s='Amount' mod='productquotation'}</option>
			                        
			                     </select>
			                  </div>
			               </div>
			               
			               <div id="charge_field_val" class="form-group">
			                  <label class="control-label col-lg-3">
			                  {l s='Value' mod='productquotation'}
			                  </label>
			                  <div class="col-lg-9">
			                     <div class="input-group">
			                        <div class="input-group-addon">
			                           <span id="charge_currency" style="">{$currency_sign|escape:'htmlall':'UTF-8'}</span>
			                           
			                        </div>
			                        <input id="charge_value" class="form-control disabled" type="number" name="charge_value" >
			                     </div>
			                  </div>
			               </div>
			               <div class="row">
			                  <div class="col-lg-9 col-lg-offset-3">
			                     <button class="btn btn-default" type="button" id="cancel_charges">
			                     <i class="icon-remove text-danger"></i>
			                     {l s='Cancel' mod='productquotation'}
			                     </button>
			                     <button class="btn btn-default" type="submit" name="submitNewFee">
			                     <i class="icon-ok text-success"></i>
			                     {l s='Add' mod='productquotation'}
			                     </button>
			                  </div>
			               </div>
			            </div>
			         </div>
			         <div id="charges_table">
			            <div class="table-responsive" style="overflow: auto;">
			               <table class="table">
			                  <thead>
			                     <tr>
			                        <th><span class="title_box ">{l s='Charge' mod='productquotation'}</span></th>
			                        <th><span class="title_box ">{l s='Type' mod='productquotation'}</span></th>
			                        <th><span class="title_box ">{l s='Value' mod='productquotation'}</span></th>
			                        <th></th>
			                     </tr>
			                  </thead>
			                  {if $fee_price > 0}
									<tbody>
									   <tr class="charge_row">
									      <td>{$fee_name}</td>
									      <td>
									         <span>{l s='General' mod='productquotation'}</span>
									      </td>
									      <td>
									         <span>{$currency_sign|escape:'htmlall':'UTF-8'} {$fee_price}</span>
									      </td>
									      <td>
									         

									         <a id="quote_charges" style="cursor: pointer;">
									           <i class="icon-refresh"></i>
									         {l s='Update' mod='productquotation'}
									         </a>

									      </td>
									   </tr>
									</tbody>
									{/if}
			               </table>
			            </div>
			         </div>
			      </div>
			   </div>



			   <div class="col-xs-6">
			      <div class="panel panel-vouchers clearfix">
			         <div class="panel-heading">
			            {l s='Discounts' mod='productquotation'}
			            <span class="panel-heading-action">
			            	{if $quotation_details_basic.coupon_sent <= 0}
			            	<button id="quote_discount"  type="button" class="btn btn-primary btn-sm">{l s='Add Discounts' mod='productquotation'}</button>
			            	{/if}

			            </span>
			         </div>
			         <div class="current-edit" id="form_discount" style="display:none;">
			            <div class="form-horizontal well">
			               <div class="form-group">
			                  <label class="control-label col-lg-3">
			                  {l s='Name' mod='productquotation'}
			                  </label>
			                  <div class="col-lg-9">
			                     <input class="form-control" type="text" name="discount_nam" id="discount_nam" >
			                  </div>
			               </div>
			               <div class="form-group">
			                  <label class="control-label col-lg-3">
			                  {l s='Type' mod='productquotation'}
			                  </label>
			                  <div class="col-lg-9">
			                     <select class="form-control" name="discount_type" id="discount_type">
			                        <option value="2">{l s='Amount' mod='productquotation'}</option>
			                     </select>
			                  </div>
			               </div>
			               <div id="discount_value_field" class="form-group">
			                  <label class="control-label col-lg-3">
			                  {l s='Value' mod='productquotation'}
			                  </label>
			                  <div class="col-lg-9">
			                     <div class="input-group">
			                        <div class="input-group-addon">
			                           <span>{$currency_sign|escape:'htmlall':'UTF-8'}</span>
			                        </div>
			                        <input id="discount_value" onkeyup="throwDiscount(this);" class="form-control" type="text" name="discount_value" >
			                     </div>
			                  </div>
			               </div>
			               <div class="row">
			                  <div class="col-lg-9 col-lg-offset-3">
			                     <button class="btn btn-default" type="button" id="cancel_discount">
			                     <i class="icon-remove text-danger"></i>
			                     {l s='Cancel' mod='productquotation'}
			                     </button>
			                     <button class="btn btn-default" type="submit" name="submitNewVoucher">
			                     <i class="icon-ok text-success"></i>
			                     {l s='Add' mod='productquotation'}
			                     </button>
			                  </div>
			               </div>
			            </div>
			         </div>
			         <div id="discount_table">
			            <div class="table-responsive">
			               <table class="table">
			                  <thead>
			                     <tr>
			                        <th><span class="title_box ">{l s='Discount Code' mod='productquotation'}</span></th>
			                        <th><span class="title_box "> {l s='Value' mod='productquotation'}</span></th>
			                        <th><span class="title_box "> {l s='Valid' mod='productquotation'}</span></th>
			                        <th></th>
			                     </tr>
			                  </thead>
			                  
			                  {if $quotation_details_basic.coupon_sent > 0}
								<tbody>
								   <tr class="discount_row">
								      <td>{$discoutcode|escape:'htmlall':'UTF-8'}</td>
								      <td>
								         {$currency_sign|escape:'htmlall':'UTF-8'}{$reduction_amount|escape:'htmlall':'UTF-8'}
								      </td>
								      <td>
								        {$date_to|escape:'htmlall':'UTF-8'}
								      </td>
								      <td>
								      	<a id="quote_discount" style="cursor: pointer;">
									         <i class="icon-refresh"></i>
									         {l s='Update' mod='productquotation'}</a>

								      </td>
								   </tr>
								</tbody>
								{/if}
			               </table>
			            </div>
			         </div>
			      </div>
			   </div>
			   <div class="col-lg-12">
			   	<button type="button" data-toggle="modal" data-target="#mailModal" class="btn btn-primary pull-right" id="">
                  {l s='Review & Send' mod='productquotation'}
                </button>
                </div>
			</div>

			<div class="modal fade" id="mailModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title">{l s='Quote Mail' mod='productquotation'}</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        
					





            <table class="table" style="" bgcolor="#ffffff">
               <tbody>
                  <tr>
                  </tr>
                  <tr>
                     <td class="titleblock" style="" align="center">
                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                        <span class="title title_q_dis">{l s='Voucher received against your quote' mod='productquotation'}</span>
                        </font>
                     </td>
                  </tr>
                  <tr>
                     <td class="box_quo">
                        <table class="table">
                           <tbody>
                              <tr>
                                 <td>
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                    <span >
                                    <span><strong >{l s='You have received a voucher of' mod='productquotation'} <span style="font-size: 18px">{$currency_sign|escape:'htmlall':'UTF-8'}{$reduction_amount|escape:'htmlall':'UTF-8'}      </span></strong></span><br>
                                    <input type="hidden" name="discount_price" value="{$currency_sign|escape:'htmlall':'UTF-8'}{$reduction_amount|escape:'htmlall':'UTF-8'} ">
                                    </span>
                                    </font><br>
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                    <span ><strong>{l s='Use this link to access your cart:' mod='productquotation'} </strong></span> <a href="#"><strong>{l s='Cart Link' mod='productquotation'}</strong></a><br>
                                    </font><br><br>
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                    <span ><strong>{l s='Use Voucher Code:' mod='productquotation'} </strong></span> <span>{$discoutcode|escape:'htmlall':'UTF-8'}</span><br>
                                    </font>
                                    <input type="hidden" name="discount_code" value="{$discoutcode|escape:'htmlall':'UTF-8'}">
                                    <font class="fontvalid" size="2" face="Open-sans, sans-serif" color="#333333"><span  >{l s='Valid upto: ' mod='productquotation'} {$date_to|escape:'htmlall':'UTF-8'}</span></font>

                                    <input type="hidden" name="valid_too" value="{$date_to|escape:'htmlall':'UTF-8'}">
                                    <br>
                                    
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         
					<div id="discount_total" class="panel">
					   <div class="table-responsive">
					      <table class="table">
					         <tbody>
					            <tr>
					               <td class="text-right">{l s='Total Products' mod='productquotation'}</td>
					               <td class="amount text-right nowrap">
					                 {$total|escape:'htmlall':'UTF-8'}            
					               </td>
					            </tr>
					            <tr>
					               <td class="text-right">{l s='Discounts' mod='productquotation'}</td>
					               <td class="amount text-right nowrap">
					                 {$currency_sign|escape:'htmlall':'UTF-8'}{$reduction_amount|escape:'htmlall':'UTF-8'}         
					               </td>
					            </tr>
					            <tr>
					               <td class="text-right" style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">{l s='Sub-Total' mod='productquotation'}</td>
					               <td class="amount text-right nowrap" style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
					                  {assign var=subtotal value=$totalwithout-$reduction_amount}
					                  {$currency_sign|escape:'htmlall':'UTF-8'}{$subtotal}
					               </td>
					            </tr>
					            <tr>
					               <td class="text-right">{l s='Charges' mod='productquotation'}</td>
					               <td class="amount text-right nowrap">
					                  {$currency_sign|escape:'htmlall':'UTF-8'}{$fee_price}         
					               </td>
					            </tr>
					            
					            <tr>
					               <td class="text-right" style="background-color:#f4f8fb;">
					                  <strong>{l s='Total' mod='productquotation'}</strong>
					               </td>
					               <td class="amount text-right nowrap" style="background-color:#f4f8fb;">
					                  <strong>
					                  	{assign var=ttotal value=$subtotal+$fee_price}
					                  	{$currency_sign|escape:'htmlall':'UTF-8'}{$ttotal} 
					                  </strong>
					               </td>
					            </tr>
					         </tbody>
					      </table>
					   </div>
					</div>
					<font size="2" face="Open-sans, sans-serif" color="#C93B2A">
						<span style="font-size:10px;">{l s='*Note: Your Cart total must be greater or equal to' mod='productquotation'} {$total|escape:'htmlall':'UTF-8'}</span>
					</font>
					<input type="hidden" name="total_quot" value="{$total|escape:'htmlall':'UTF-8'}">

			      	</div>
			      	<div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='productquotation'}</button>
			        <button type="submit" name="send_mail_quote" class="btn btn-primary" {if !$reduction_amount} disabled="true" {/if}>{l s='SEND' mod='productquotation'}</button>
			      </div>
			    </div>
			  </div>
			</div>


			
	</div>
	

</form>


<form class="form-horizontal" enctype="multipart/form-data" method="post" action="{$action|escape:'htmlall':'UTF-8'}">
<div class="panel" style="margin-top: 20px; clear: both; min-height: 70px">
	<div class="panel-heading">{l s='Quote File' mod='productquotation'}</div>
	<input type="hidden" id="id_productquotation" name="id_productquotation" value="{$id_productquotation}">
	<div class="col-lg-4">
		<input type="file" name="file_quote" />
	</div>
	<input type="hidden" name="id_customer" value="{$quotation_details_basic.id_customer}">
	<div class="col-lg-4">
		<button name="submitNewFile" type="submit" style="background: #009BD3; color: #fff; border-color: #007AA3" class="btn btn-default">
			<i class="icon-ok text-success"></i>
			{l s='Submit File' mod='productquotation'}
		</button>
	</div>

	<div class="col-lg-4">
		<a href="{$file_data}" target="_blank">{$file_namee} </a>
	</div>
	<div class="clearfix"></div>
</div>
</form>
<div class="clearfix"></div>
<script>{literal}


$("#quote_charges").click(function() {
	$("#form_charges").css("display","block");
	$("#charge_name").attr("required","true");
	$("#charge_value").attr("required","true");
});

$("#cancel_charges").click(function() {
	$("#form_charges").css("display","none");
	$("#charge_name").removeAttr("required");
	$("#charge_value").removeAttr("required");
});
$("#cancel_discount").click(function() {
	$("#form_discount").css("display","none");
	$("#discount_nam").removeAttr("required");
	$("#discount_value").removeAttr("required");
});

$( "#method_quote" ).change(function() {
  // Percent type
  if ($(this).val() == 1) {
    $('charge_field_val').show();
    $('#charge_currency').hide();
    $('#charge_percent').show();
  }
  // Amount type
  else if ($(this).val() == 2) {
    $('#charge_field_val').show();
    $('#charge_percent').hide();
    $('#charge_currency').show();
  }
});


$("#quote_discount").click(function() {
  $('.panel-vouchers,#form_discount').slideDown();
  $("#discount_nam").attr("required","true");
	$("#discount_value").attr("required","true");
});

function throwDiscount(e) {
	var discount_value = $(e).val();
	var currency = $('#currency_sign').text();
	var base_total = $('#base_total').text().replace(',', '.');
	base_total = base_total.replace(/[^a-zA-Z0-9.]/g, '');
	base_total = base_total-discount_value;
	$('#fmm_discount').text(currency+discount_value);
	base_total = parseFloat(base_total).toFixed(2);
	$('#final_discount').text(currency+base_total);
}
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