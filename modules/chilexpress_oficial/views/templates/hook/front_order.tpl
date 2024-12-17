{**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

 {if $out != ''}
    <div class="" style="overflow-x:auto; overflow-y:hidden; background-color:#fff;">
        <div class='ov-s-head' style="padding-left:0 !important; background-color:transparent;"> <img src="{$module_dir|escape:'htmlall':'UTF-8'}logo.png" style="width: 32px;height: 32px;" /> 
            {l s='Etiqueta Chilexpress' mod='kbmarketplace'}
        </div>
        <div class="kb-vspacer5"></div>    
        {if isset($metasArray) && $metasArray}
            <div class="col-md-6">
                <img src="{$src}" style="display:block; margin:0 auto;"/> <br>
                <a href="{$url_imprimir}" class="btn btn-primary btn-block"><i class="icon-print"></i>
                    Imprimir Etiquetas
                </a>
            </div>
            
        
        {else}
            {if $can_handle_order}
                
                
                <form action="" id="form_chilexpress" method="post">
                    <input type="hidden" name="call_chilexpress" id="call_chilexpress" value="1">
                    <input type="hidden" name="orderid" id="orderid" value="{$orderid}">
                    <button type="button" id="btn_chilexpress" name="btn_chilexpress" value="1" class="btn 
                    btn-primary">Crear etiqueta</button>
                </form>
                <script>
                    jQuery('#form_chilexpress').click(function(){
                        jQuery("#btn_chilexpress").attr("disabled", true);
                        jQuery('#form_chilexpress').submit();
                    });
                </script>                
            {/if}
        {/if} 
    </div>  
{else}

    <div class="" style="overflow-x:auto; overflow-y:hidden; background-color:#fff;">
        
            <div class='ov-s-head' style="padding-left:0 !important; background-color:transparent;"> <img src="{$module_dir|escape:'htmlall':'UTF-8'}logo.png" style="width: 32px;height: 32px;" /> {l s='Etiquetas Chilexpress' mod='kbmarketplace'}</div>

            <div class="kb-vspacer5"></div>

        {if $can_handle_order}
            <div class="alert alert-danger">Generación de etiqueta solo disponible con metodo de envío de Chilexpress</div>
            
        {/if}
    </div>

{/if}
