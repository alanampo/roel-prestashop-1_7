{**
* 2007-2022 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2022 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{strip}
<div class="tvproduct-wishlist">
    <input type="hidden" class="wishlist_prod_id" value="{$id_product|escape:'htmlall':'UTF-8'}">
    {if isset($wishlists) && !empty($wishlists) && count($wishlists) > 1}
        <div class="buttons_bottom_block no-print panel-product-line panel-product-actions" data-toggle="tvtooltip" data-placement="top" data-html="true" title="{l s='Add To Wishlist' mod='tvcmswishlist'}">
            <div id="wishlist_button">
                {foreach $wishlists as $wishlist}
                    {if $wishlist.default == '1'}
                        <a class="wishlist_button_extra" onclick="WishlistCart('wishlist_block_list', 'add', '{$id_product|intval|escape:"htmlall":"UTF-8"}', $('#idCombination').val(), 1, {$wishlist.id_wishlist}); return false;">
                            <div class="panel-product-line panel-product-actions tvproduct-wishlist-icon">
                                {* <i class='material-icons'>&#xe87e;</i> *}
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="21" height="21" preserveAspectRatio="xMidYMid meet" viewBox="0 0 20 20"><path fill="#000" d="m10.496 16.803l6.245-6.304a4.408 4.408 0 0 0-.017-6.187a4.306 4.306 0 0 0-6.135-.015l-.596.603l-.605-.61a4.301 4.301 0 0 0-6.127-.016c-1.688 1.705-1.68 4.476.016 6.189l6.277 6.34c.26.263.682.263.942 0ZM11.3 5a3.306 3.306 0 0 1 4.713.016a3.408 3.408 0 0 1 .016 4.78v.002l-6.004 6.06l-6.038-6.099c-1.313-1.326-1.314-3.47-.015-4.782a3.302 3.302 0 0 1 4.706.016l.96.97a.5.5 0 0 0 .711 0L11.3 5Z"></path></svg>
                                {* <span>{l s='Add To Wishlist' mod='tvcmswishlist'}</span> *}
                            </div>
                        </a>
                    {/if}
                {/foreach}

                {* <select id="idWishlist">
                    {foreach $wishlists as $wishlist}
                        <option value="{$wishlist.id_wishlist|escape:'htmlall':'UTF-8'}">{$wishlist.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select> *}
            </div>
        </div> 
    {else}
        <a href="#" class="tvquick-view-popup-wishlist wishlist_button" onclick="WishlistCart('wishlist_block_list', 'add', '{$id_product|intval|escape:"htmlall":"UTF-8"}', $('#idCombination').val(), 1, 1); return false;" rel="nofollow" data-toggle="tvtooltip" data-placement="top" data-html="true" title="{l s='Add To Wishlist' mod='tvcmswishlist'}">
            <div class="panel-product-line panel-product-actions tvproduct-wishlist-icon">
                {* <i class='material-icons'>&#xe87d;</i> *}
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="21" height="21" preserveAspectRatio="xMidYMid meet" viewBox="0 0 20 20"><path fill="#000" d="m10.496 16.803l6.245-6.304a4.408 4.408 0 0 0-.017-6.187a4.306 4.306 0 0 0-6.135-.015l-.596.603l-.605-.61a4.301 4.301 0 0 0-6.127-.016c-1.688 1.705-1.68 4.476.016 6.189l6.277 6.34c.26.263.682.263.942 0ZM11.3 5a3.306 3.306 0 0 1 4.713.016a3.408 3.408 0 0 1 .016 4.78v.002l-6.004 6.06l-6.038-6.099c-1.313-1.326-1.314-3.47-.015-4.782a3.302 3.302 0 0 1 4.706.016l.96.97a.5.5 0 0 0 .711 0L11.3 5Z"></path></svg>
                {* <span>{l s='Add To Wishlist' mod='tvcmswishlist'}</span> *}
            </div>
        </a>
    {/if}
</div>
{/strip}