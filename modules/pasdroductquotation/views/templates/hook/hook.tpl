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

<script type="text/javascript">
var baseDir = "{$base_dir}";

var pq_label_exists = "{l s='Product is already in your Quote.' mod='productquotation'}";
var hide_price = "{$hide_price|escape:'htmlall':'UTF-8'}";
var hide_cart = "{$hide_cart|escape:'htmlall':'UTF-8'}";
var id_product = "{$id_product|escape:'htmlall':'UTF-8'}";

var stock_status = "{$stock_status|escape:'htmlall':'UTF-8'}";
//check if hide_price enable
$(document).ready(function(){
    var token = $('#token').val();
    var controller_check_pro6 = baseDir+"?fc=module&module=productquotation&controller=ajax&action=check_16&token=44"+token;
    IDPreCombin = jQuery('#idCombination').val();
    if (!IDPreCombin || IDPreCombin == ''){
        IDPreCombin = 0;
    }
    
    setTimeout("jQuery('#idCombination').trigger('change');", 800);

    jQuery('.attribute_list select').change(function()
    {
        setTimeout("jQuery('#idCombination').trigger('change');", 800);
    });
    jQuery('ul#color_to_pick_list li a').click(function()
    {
        setTimeout("jQuery('#idCombination').trigger('change');", 800);
    });
    jQuery('#idCombination').on('change', function()
    {
        var id_comb = 0;
        id_comb = jQuery(this).val();

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
    <input type="number" min="1" id="fmm_quote_qty" class="text input-group form-control{if $ps_ver <= 0} ps_16_qty{/if}" style="max-width: 80px; display: inline-block" value="1" />
    <input type="hidden" id="token" name="token" value="{$token}" />

    <input type="hidden" id="p_id" name="p_id" value="{$id_product}">
    <input type="hidden" min="1" id="fmm_tax_price" value="{$tax_status|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" min="1" id="fmm_stock_status" value="{$stock_status|escape:'htmlall':'UTF-8'}" />
    <p id="p_add" class="alert alert-success" style="display: none; color: green;">{l s='Product Successfully added.' mod='productquotation'}</p>
    <button class="add_to_quote_button" type="button" onclick="addQuote('{$id_product}');" id="fmm_quote_button">
        <span>{if empty($button_text)}{l s='Add to Quote' mod='productquotation'}{else}{$button_text|escape:'htmlall':'UTF-8'}{/if}</span>
    </button>
</div>