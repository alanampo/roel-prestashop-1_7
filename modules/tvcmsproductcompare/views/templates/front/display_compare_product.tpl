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
* @author PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2022 PrestaShop SA
* @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{strip}<div class="tvcompare-wrapper product_id_{$product.id_product}">
    <div class="tvproduct-compare tvcmsproduct-compare-btn tvproduct-compare-icon" data-product-id='{$product.id_product}' data-comp-val='{if $prod_1 == $product.id_product || $prod_2 == $product.id_product || $prod_3 == $product.id_product || $prod_4 == $product.id_product}remove{else}add{/if}' data-toggle="tvtooltip" data-placement="top" data-html="true" title="{l s='Add To Compare' mod='tvcmsproductcompare'}">
        {* <i class='material-icons remove {if $prod_1 == $product.id_product || $prod_2 == $product.id_product || $prod_3 == $product.id_product || $prod_4 == $product.id_product}{else}hide{/if}'>&#xe15c;</i>
        <i class='material-icons add {if $prod_1 == $product.id_product || $prod_2 == $product.id_product || $prod_3 == $product.id_product || $prod_4 == $product.id_product}hide{/if}'>&#xe043;</i> *}
        <svg class="remove {if $prod_1 == $product.id_product || $prod_2 == $product.id_product || $prod_3 == $product.id_product || $prod_4 == $product.id_product}{else}hide{/if}" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="16px" height="16px" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1024 1024">
            <path d="M512 0q212 0 362 150t150 362t-150 362t-362 150t-362-150T0 512t150-362T512 0zm0 961q185 0 316.5-132T960 512T828.5 195.5T512 64T195.5 195.5T64 512t131.5 317T512 961zm-35-417H288q-14 0-23-9t-9-23t9-23t23-9h448q14 0 23 9t9 23t-9 23t-23 9H477z" fill="#000000"></path>
        </svg>
        <svg class="add {if $prod_1 == $product.id_product || $prod_2 == $product.id_product || $prod_3 == $product.id_product || $prod_4 == $product.id_product}hide{/if}" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="16px" height="16px" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1024 960">
            <path d="M402 442l40-55l-135-184H3v64h271zm517-175l-78 70q-7 7-8 16t5 16l6 8q7 7 17 7t17-7l127-115h.5l.5-1l8-8q7-6 7-15.5t-7-16.5l-8-8h-1v-1L876 94q-4-3-8.5-5t-9-2t-9 2t-7.5 5l-6 8q-6 7-5 16t8 16l76 69H684L274 779H3v64h304l410-576h202zm87 522h-1v-1L876 670q-5-4-11-6t-12.5 0t-10.5 6l-6 8q-4 4-5 10t1.5 12t6.5 10l77 70l-199-1l-115-177l-39 56l121 185l234 1l-77 69q-7 7-8 16t5 16l6 8q3 4 7.5 5.5t9 1.5t9-1.5t8.5-5.5l127-115h.5l.5-1l8-8q7-6 7-15.5t-7-16.5z" fill="#000000"></path>
        </svg>
        {* <span>{l s='Add To Compare' mod='tvcmsproductcompare'}</span> *}
    </div>
</div>
{/strip}