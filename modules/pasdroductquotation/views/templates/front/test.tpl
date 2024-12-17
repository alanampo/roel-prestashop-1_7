{*
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

{if $action eq 'delete'}
    <ul>

        {foreach $products as $product}
            <li>
                <div class="pq_img"><a href="{$product['link']|escape:'htmlall':'UTF-8'}"><img width="60" src="{$product.img_id}" /> </a></div>

                <h6> <a href="{$product['link']|escape:'htmlall':'UTF-8'}" > {$product['name']|escape:'htmlall':'UTF-8'}</a><br/></h6>
         
                <div style="font-size: 12px;font-weight: bold;text-align: left;">  {l s='Quantity:' mod='productquotation'} {$product['qty']|escape:'htmlall':'UTF-8'} {$product['price']|escape:'htmlall':'UTF-8'} 

                <a class="fmm_quote_del" onclick="dropItemQuote({$product['id_quotes_products']|escape:'htmlall':'UTF-8'})"></a>

                </div>
      
            </li>
        {/foreach}

    </ul>


{elseif $action eq 'html'}
    
    <ul>

        {foreach $products as $product}
            <li>
                <div class="pq_img"><a href="{$product['link']|escape:'htmlall':'UTF-8'}"><img width="60" src="{$product.img_id}" /> </a></div>

                <h6> <a href="{$product['link']|escape:'htmlall':'UTF-8'}" > {$product['name']|escape:'htmlall':'UTF-8'}</a><br/></h6>
         
                <div style="font-size: 12px;font-weight: bold;text-align: left;">  {l s='Quantity:' mod='productquotation'} {$product['qty']|escape:'htmlall':'UTF-8'} {$product['price']|escape:'htmlall':'UTF-8'} 

                <a class="fmm_quote_del" onclick="dropItemQuote({$product['id_quotes_products']|escape:'htmlall':'UTF-8'})"></a>

                </div>
      
            </li>
        {/foreach}

    </ul>

{elseif $action eq 'add'}

    <ul>

        {foreach $products as $product}
            <li>
                <div class="pq_img"><a href="{$product['link']|escape:'htmlall':'UTF-8'}"><img width="60" src="{$product.img_id}" /> </a></div>

                <h6> <a href="{$product['link']|escape:'htmlall':'UTF-8'}" > {$product['name']|escape:'htmlall':'UTF-8'}</a><br/></h6>
         
                <div style="font-size: 12px;font-weight: bold;text-align: left;">  {l s='Quantity:' mod='productquotation'} {$product['qty']|escape:'htmlall':'UTF-8'} {$product['price']|escape:'htmlall':'UTF-8'} 

                <a class="fmm_quote_del" onclick="dropItemQuote({$product['id_quotes_products']|escape:'htmlall':'UTF-8'})"></a>

                </div>
      
            </li>
        {/foreach}

    </ul>


{/if}


