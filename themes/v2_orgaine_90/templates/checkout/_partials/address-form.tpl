{**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{strip}
{extends file='customer/_partials/address-form.tpl'}
{block name='form_field'}
{if $field.name eq "alias"}
{* we don't ask for alias here *}
{else}
{$smarty.block.parent}
{/if}
{/block}
{block name="address_form_url"}
<form method="POST" action="{url entity='order' params=['id_address' => $id_address]}" data-id-address="{$id_address}" data-refresh-url="{url entity='order' params=['ajax' => 1, 'action' => 'addressForm']}">
    {/block}
    {block name='form_fields' append}
    <input type="hidden" name="saveAddress" value="{$type}">
    {if $type === "delivery"}
    <div class="form-group row">
        <div class="col-md-9 col-md-offset-3">
            <input name="use_same_address" id="use_same_address" type="checkbox" value="1" {if $use_same_address} checked {/if}> 
            <label for="use_same_address">{l s='Use this address for invoice too' d='Shop.Theme.Checkout'}</label>
        </div>
    </div>
    {/if}
    {/block}
    {block name='form_buttons'}
    {if !$form_has_continue_button}
    <button type="submit" class="tvall-inner-btn float-xs-right">
        <span>{l s='Save' d='Shop.Theme.Actions'}</span>
    </button>
    <a class="js-cancel-address cancel-address tvall-inner-btn-cancel float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">
        <span>{l s='Cancel' d='Shop.Theme.Actions'}</span>
    </a>
    {else}
    <form>
        <button type="submit" class="continue tvall-inner-btn float-xs-right" name="confirm-addresses" value="1">
            <span>{l s='Continue' d='Shop.Theme.Actions'}</span>
        </button>
        {if $customer.addresses|count > 0}
        <a class="js-cancel-address cancel-address tvall-inner-btn-cancel float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">
            <span>{l s='Cancel' d='Shop.Theme.Actions'}</span>
        </a>
        {/if}
    </form>
    {/if}
    {/block}
    {/strip}