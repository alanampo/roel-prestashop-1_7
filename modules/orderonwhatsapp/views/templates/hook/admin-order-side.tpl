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

<div class="card mt-2 d-print-none">
    <div class="card-header">
        <div class="row">
            <div class="col-md-12">
                <h3 class="card-header-title">
                    {l s='Send WhatsApp messsage' mod='orderonwhatsapp'}
                </h3>
            </div>
        </div>
    </div>
    <div class="card-body">

        <div class="form-group row type-text_with_length_counter js-text-with-length-counter">
            <label for="wa_message" class="form-control-label label-on-top col-12">
            <span class="text-danger">*</span>
            {l s='Message' mod='orderonwhatsapp'}
            </label>
            <div class="col-12">
                <div class="input-group js-text-with-length-counter">
                    <textarea id="wa_message" name="wa_message" cols="30" rows="3" class="form-control">{$wa_order_msg}</textarea>
                </div>
            </div>
        </div>

        <div class="form-group row type-text_with_length_counter js-text-with-length-counter">
            <label for="wa_message" class="form-control-label label-on-top col-12">
            <span class="text-danger">*</span>
            {l s='Mobile number' mod='orderonwhatsapp'}
            </label>
            <div class="col-12">
                <div class="input-group js-text-with-length-counter">
                    <input type="text" name="wa_mobile" id="wa_mobile" value="{$customer_mobile}"  class="form-control">
                </div>
            </div>
        </div>

        <a href="https://wa.me/{$customer_mobile}" class="btn btn-primary" id="wa_send_message" style="background-color: #25D366;" aria-label="WhatsApp messsage"><i class="material-icons">whatsapp</i> {l s='WhatsApp messsage' mod='orderonwhatsapp'}</a>
    </div>

</div>


<script type="text/javascript">

setTimeout(
  function() 
  {
        $('#wa_send_message').click(function() {
            var encoded_message = $("#wa_message").val();
            var wa_mobile = $("#wa_mobile").val();

            console.log("https://wa.me/"+wa_mobile+"?text=" + encoded_message, '_blank');
            var win = window.open("https://wa.me/"+wa_mobile+"?text=" + encoded_message, '_blank');
            

            if (win) {
                //Browser has allowed it to be opened
                win.focus();
            } else {
                //Browser has blocked it
                alert('Please allow popups for this website');
            }

            return false;
        }); 

  }, 1500);


</script>