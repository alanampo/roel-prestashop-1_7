{*
*  2012-2023 Weblir
*
*  @author    weblir <contact@weblir.com>
*  @copyright 2012-2023 weblir
*  @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
*  International Registered Trademark & Property of weblir.com
*
*  You are allowed to modify this copy for your own use only. You must not redistribute it. License
*  is permitted for one Prestashop instance only but you can install it on your test instances.
*}

<div class="modal fade" id="OrderOnWhatsAppModal" wa_page_type="{$wa_page_type}" tabindex="-1" role="dialog" aria-labelledby="OrderOnWhatsAppModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="OrderOnWhatsAppModalLabel">{l s='Order on WhatsApp' mod='orderonwhatsapp'}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- multistep form -->
                <form id="order-on-whatsapp-form">
                  <!-- progressbar -->
                  <ul id="progressbar">
                    <li class="active">{l s='Address' mod='orderonwhatsapp'}</li>
                    <li>{l s='Info' mod='orderonwhatsapp'}</li>
                    <li>{l s='Confirm' mod='orderonwhatsapp'}</li>
                  </ul>
                  <!-- fieldsets -->
                  <fieldset class="active-step">
                    <h2 class="fs-title">{l s='Address' mod='orderonwhatsapp'}</h2>
                    <h3 class="fs-subtitle">{l s='Step 1' mod='orderonwhatsapp'}</h3>

                    <div class="field form-group">
                        <label for="id_country">{l s='Country' mod='orderonwhatsapp'} {if $required_country == 1}*{/if}</label>
                        <select class="form-control form-control-select js-country {if $required_country == 1}required-field{/if}" name="id_country" id="id_country" required="">
                            <option value="" disabled="">-- {l s='Select your country' mod='orderonwhatsapp'} --</option>                        
                            {foreach from=$store_countries key=id_country item=country}
                                <option test="{$customer_address_data->id_country == $id_country}" value="{$id_country}"
                                    {if isset($customer_address_data->id_country)}
                                        {if $customer_address_data->id_country == $id_country} selected="selected"{/if}
                                    {else}
                                        {if $WL_COUNTRY_DEFAULT == $id_country} selected="selected"{/if}
                                    {/if}
                                >{$country}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div class="field form-group">
                        <select class="allstates" name="allstates" id="allstates" style="display: none !important;">
                            <option value="" disabled="" selected="">-- {l s='Select your state' mod='orderonwhatsapp'} --</option>
                            {foreach from=$store_states key=id_state item=state}
                                <option value="{$state.id_state}" country="{$state.id_country}"
                                    {if isset($customer_address_data->id_state) && $customer_address_data->id_state == $id_state}
                                        selected="selected"
                                    {/if}
                                >{$state.name}</option>
                            {/foreach}
                        </select>


                        <label for="id_state">{l s='State' mod='orderonwhatsapp'} {if $required_state == 1}*{/if}</label>
                        <select class="form-control form-control-select js-country {if $required_state == 1}required-field{/if}" name="id_state" id="id_state" required="">
                            <option value="" disabled="" selected="">-- {l s='Select your state' mod='orderonwhatsapp'} --</option>
                            {foreach from=$store_states key=id_state item=state}
                                <option value="{$state.id_state}" country="{$state.id_country}"
                                    {if isset($customer_address_data->id_state) && $customer_address_data->id_state == $state.id_state}
                                        selected="selected"
                                    {/if}
                                >{$state.name}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div class="field form-group">
                        <label for="city">{l s='City' mod='orderonwhatsapp'} {if $required_city == 1}*{/if}</label>
                        <input type="text" class="form-control {if $required_city == 1}required-field{/if}" id="city" name="city" value="{if isset($customer_address_data->city)}{$customer_address_data->city}{/if}" placeholder="{l s='Enter city...' mod='orderonwhatsapp'}">
                    </div>

                    <div class="field form-group">
                        <label for="address">{l s='Address' mod='orderonwhatsapp'} {if $required_address == 1}*{/if}</label>
                        <input type="text" class="form-control {if $required_address == 1}required-field{/if}" id="address" name="address" value="{if isset($customer_address_data->address1)}{$customer_address_data->address1}{/if}" placeholder="{l s='Street, number, etc...' mod='orderonwhatsapp'}">
                    </div>

                    <div class="field form-group">
                        <label for="postcode">{l s='Postal Code' mod='orderonwhatsapp'} {if $required_postcode == 1}*{/if}</label>
                        <input type="text" class="form-control {if $required_postcode == 1}required-field{/if}" id="postcode" name="postcode" value="{if isset($customer_address_data->postcode)}{$customer_address_data->postcode}{/if}" placeholder="{l s='Enter Post Code...' mod='orderonwhatsapp'}">
                    </div>

                    <input type="button" name="next" class="btn btn-primary next action-button" value="{l s='Next' mod='orderonwhatsapp'}" />
                  </fieldset>
                  <fieldset>
                    <h2 class="fs-title">{l s='Additional info' mod='orderonwhatsapp'}</h2>
                    <h3 class="fs-subtitle">{l s='Step 2' mod='orderonwhatsapp'}</h3>

                    <div class="field form-group">
                        <label for="first-na">{l s='First name' mod='orderonwhatsapp'} {if $required_first_name == 1}*{/if}</label>
                        <input type="text" class="form-control {if $required_first_name == 1}required-field{/if}" id="first-name" name="first-name" value="{if isset($customer_address_data->firstname)}{$customer_address_data->firstname}{/if}" placeholder="{l s='First name...' mod='orderonwhatsapp'}">
                    </div>

                    <div class="field form-group">
                        <label for="last-name">{l s='Last name' mod='orderonwhatsapp'} {if $required_last_name == 1}*{/if}</label>
                        <input type="text" class="form-control {if $required_last_name == 1}required-field{/if}" id="last-name" name="last-name" value="{if isset($customer_address_data->lastname)}{$customer_address_data->lastname}{/if}" placeholder="{l s='Last name...' mod='orderonwhatsapp'}">
                    </div>

                    <div class="field form-group">
                        <label for="email">{l s='Email' mod='orderonwhatsapp'} {if $required_last_name == 1}*{/if}</label>
                        <input type="text" class="form-control {if $required_email == 1}required-field{/if}" id="email" name="email" value="{if isset($customer_informations->email)}{$customer_informations->email}{/if}" placeholder="{l s='Email...' mod='orderonwhatsapp'}">
                    </div>

                    <div class="field form-group">
                        <label for="mobile-phone">{l s='Mobile phone' mod='orderonwhatsapp'} {if $required_mobile_phone == 1}*{/if}</label>
                        <input type="text" class="form-control {if $required_mobile_phone == 1}required-field{/if}" id="mobile-phone" name="mobile-phone" value="{if isset($customer_address_data->phone_mobile)}{$customer_address_data->phone_mobile}{else}{$customer_address_data->phone}{/if}" placeholder="{l s='Mobile phone...' mod='orderonwhatsapp'}">
                    </div>

                    <input type="button" name="previous" class="btn btn-secondary previous action-button" value="{l s='Previous' mod='orderonwhatsapp'}"  class="required-field"/>

                    <input type="button" name="next" class="btn btn-primary next action-button" value="{l s='Next' mod='orderonwhatsapp'}" />
                  </fieldset>
                  <fieldset>
                    <h2 class="fs-title">{l s='Review and order' mod='orderonwhatsapp'}</h2>
                    <h3 class="fs-subtitle">{l s='Last step' mod='orderonwhatsapp'}</h3>

                    <h5 class="card-title">{l s='We are almost done :)' mod='orderonwhatsapp'} </h5>
                    <p class="card-text">{l s='Check your order details and press' mod='orderonwhatsapp'} <strong>{l s='Place order' mod='orderonwhatsapp'}</strong> {l s='to confirm your order.' mod='orderonwhatsapp'}</p>
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <ul class="list-group" style="margin-top: 15px;">
                                <li class="list-group-item active">{l s='Address' mod='orderonwhatsapp'}:</li>
                                <li class="list-group-item">{l s='Country' mod='orderonwhatsapp'}: <span class="selected-country"></span></li>
                                <li class="list-group-item">{l s='State' mod='orderonwhatsapp'}: <span class="selected-state"></span></li>
                                <li class="list-group-item">{l s='City' mod='orderonwhatsapp'}: <span class="selected-city"></span></li>
                                <li class="list-group-item">{l s='Address' mod='orderonwhatsapp'}: <span class="selected-address"></span></li>
                                <li class="list-group-item">{l s='Post Code' mod='orderonwhatsapp'}: <span class="selected-postcode"></span></li>
                                <li class="list-group-item">{l s='First Name' mod='orderonwhatsapp'}: <span class="selected-firstname"></span></li>
                                <li class="list-group-item">{l s='Last Name' mod='orderonwhatsapp'}: <span class="selected-lastname"></span></li>
                                <li class="list-group-item">{l s='Email' mod='orderonwhatsapp'}: <span class="selected-email"></span></li>
                                <li class="list-group-item">{l s='Mobile Phone' mod='orderonwhatsapp'}: <span class="selected-phone"></span></li>
                            </ul>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <ul class="list-group whatsapp-ordered-products" style="margin-top: 15px;">
                                <li class="list-group-item active">{l s='Ordered product' mod='orderonwhatsapp'}:</li>
                                {if isset($wa_products)}
                                    {foreach from=$wa_products item=product}
                                        <li class="list-group-item d-flex justify-content-between">
                                            {$product.name}
                                            {if $wa_page_type=="product"}
                                                <span class="badge badge-primary badge-pill">
                                                    <input type="number" name="wa_product_qty" id="wa_product_qty" value="1">
                                                    <input type="hidden" name="wa_id_product" id="wa_id_product" value="{$product.id_product}">
                                                </span>
                                            {/if}
                                        </li>
                                    {/foreach}
                                {/if}
                            </ul>
                        </div>
                    </div>


                    


                    <input type="button" name="previous" class="btn btn-secondary previous action-button" value="{l s='Previous' mod='orderonwhatsapp'}" />
                    <button type="button" name="submit" class="btn btn-primary submit action-button" id="place-order">{l s='Place order' mod='orderonwhatsapp'}</button>
                  </fieldset>
                  <div class="clearfix"></div>
                </form>

                <div id="order-confirmation-message" style="display: none;">
                    <div class="alert alert-success" role="alert">
                        {l s='Order successfully sent! You will now be redirected to WhatsApp in 5 seconds so you can send us the message cotaining your order info...' mod='orderonwhatsapp'}
                    </div>

                    <p class="wa-loading">{l s='Redirecting to WhatsApp' mod='orderonwhatsapp'}</p>

                </div>
            </div>
        </div>
    </div>
</div>