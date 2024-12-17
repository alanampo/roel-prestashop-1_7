<!-- Supercheckout Custom  block -->
<div class="tab-pane" id="custom_fields_etiqueta">

    <h4 class="visible-print">{l s='Etiqueta Chilexpress' mod='supercheckout'} <span class="badge"></span></h4>
    <div class="form-horizontal">
        <div class="form-group">
            {if $out != ''}
                <div class="col-lg-9">
                    
                    {$out}
                </div>
            {else}
                <div class="list-empty-msg">
                    <i class="icon-warning-sign list-empty-icon"></i>
                    
                    {l s='No hay etiquetas en este pedido' mod='supercheckout'}
                </div>
            {/if}

    
        </div>
    </div>
</div>
