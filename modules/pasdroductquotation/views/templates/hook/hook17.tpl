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
<script type="text/javascript" src="{$jQuery_path|escape:'htmlall':'UTF-8'}"></script>
<script type="text/javascript">
var token = $('#token').val();
var baseDir = prestashop.urls.base_url;
var pq_label_exists = "{l s='Product is already in your Quote.' mod='productquotation'}";
var hide_price = "{$hide_price|escape:'htmlall':'UTF-8'}";
var hide_cart = "{$hide_cart|escape:'htmlall':'UTF-8'}";
var id_product = "{$id_product|escape:'htmlall':'UTF-8'}";
var controller_check_pro6 = baseDir+"?fc=module&module=productquotation&controller=ajax&action=check_16&token="+token;
var stock_status = "{$stock_status|escape:'htmlall':'UTF-8'}";
//check if hide_price enable
$(document).ready(function(){
        var token = $('#token').val();
        var controller_check_pro6 = baseDir+"?fc=module&module=productquotation&controller=ajax&action=check_16&token="+token;

        var id_comb = 0;
        $.ajax({
            type    : "POST",
            cache   : false,
            url     : controller_check_pro6,
            data : {
                id_product:id_product,
                id_comb:id_comb
            },
            success: function(data) {

                if(data <= 0){
                    if(stock_status == 0){
                        $('.pq_container').hide();
                    }
                }else{
                    $('.pq_container').show();
                }
            }
        });

    if(hide_price == 1){
        $(".product-prices").hide();
        $(".content_prices").hide();
    }
    if (hide_cart == 1) {
        $(".product-add-to-cart").css("display", "none");
        $(".exclusive").css("display", "none");
    }
});
</script>

<div class="pq_container{if $ps_ver > 0} ps_rules_17{/if}">
    <img src="{$gify_url|escape:'htmlall':'UTF-8'}" id="gify_img" style="display: none" />
    <input type="hidden" id="token" name="" value="{$token}" />

    <input type="hidden" id="p_id" name="p_id" value="{$id_product}" />
    <input type="hidden" id="fmm_tax_price" value="{$tax_status|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" id="fmm_stock_status" value="{$stock_status|escape:'htmlall':'UTF-8'}" />

    <p id="p_add" class="alert alert-success" style="display: none; color: green;">{l s='Product Successfully added.' mod='productquotation'}</p>
    <div class="input-group bootstrap-touchspin" style="">
       <input type="number" value="1" class="js-cart-line-product-quantity form-control qty_pro_fmm63" min="1" aria-label="Quantity" style="display: block;" id="fmm_quote_qty">
       <input type="hidden" name="fmm_cartlink" id="fmm_cartlink" value="">
       <span class="input-group-btn-vertical">
        <button class="btn btn-touchspin js-touchspin js-increase-product-quantity bootstrap-touchspin-up" onclick="fmmup('63')" type="button">
           <i class="material-icons touchspin-up fmmup"></i>
           </button>
        <button onclick="fmmdown('63')" class="btn btn-touchspin js-touchspin js-decrease-product-quantity bootstrap-touchspin-down" type="button"><i class="material-icons touchspin-down"></i></button></span>
      </div>
    <button class="btn btn-primary" type="button" onclick="addQuote('{$id_product}');" id="fmm_quote_button" style="margin-left: 5px;">
        <i class="material-icons shopping-cart"></i>
        <span>{if empty($button_text)}{l s='Add to Quote' mod='productquotation'}{else}{$button_text|escape:'htmlall':'UTF-8'}{/if}</span>
    </button>

</div>

<script type="text/javascript">
  function fmmup(id_product) {

  var qty = $(".qty_pro_fmm"+id_product).val();
  var newVal = parseFloat(qty) + 1;
  if (newVal == 0) {
    newVal = 1;
  }
  $(".qty_pro_fmm"+id_product).val(newVal);

}
function fmmdown(id_product) {

  var qty = $(".qty_pro_fmm"+id_product).val();
  if (qty < 1) {
    return false;
  }
  var newVal = parseFloat(qty) - 1;
  if (newVal == 0) {
    newVal = 1;
  }
  $(".qty_pro_fmm"+id_product).val(newVal);

}
</script>