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
var pq_label_exists = "{l s='Product is already in your Quote.' mod='productquotation' js=1}";
var hide_price = "{$hide_price|escape:'htmlall':'UTF-8'}";
var hide_cart = "{$hide_cart|escape:'htmlall':'UTF-8'}";
$(document).ready(function(){
    if (hide_cart == 1) {
        $(".ajax_add_to_cart_button").css("display", "none");
    }
});

</script>
<div class="pq_container{if $ps_ver > 0} ps_rules_17{/if}">
    <img src="{$gify_url|escape:'htmlall':'UTF-8'}" id="gify_img_{$p_id}" style="display: none" />
    <input type="hidden" min="1" id="fmm_quote_qty" class="text input-group form-control{if $ps_ver <= 0} ps_16_qty{/if}" style="max-width: 80px; display: inline-block" value="1" />
    <input type="hidden" id="p_id" name="p_id" value="{$p_id}">
    <input type="hidden" id="token" name="token" value="{$token}" />
    
    <a class="button lnk_view btn btn-default fmm_quote_button_{$p_id}" type="button" onclick="addQuote2('{$p_id}');" id="fmm_quote_button" style="color: white;">
        <span style="background: #3478aa; text-shadow:none;">{if empty($button_text)}{l s='Add to Quote' mod='productquotation'}{else}{$button_text|escape:'htmlall':'UTF-8'}{/if}</span>
    </a>
</div>