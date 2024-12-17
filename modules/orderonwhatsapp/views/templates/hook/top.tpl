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

<div class="panel" id="fieldset_0" style="text-align:center; background-color: #A2DED0;">

	<div class="row">

		<div class="col-lg-12">

			<h1 style="color: #fff;"><img src="{$path|escape:'htmlall':'UTF-8'}views/img/logo.png" style="max-width: 100px; border: 3px solid white; border-radius: 58px; padding: 0; margin: 10px;">{l s='Order on WhatsApp'  mod='orderonwhatsapp'}</h1>

			<hr>

			<h1 style="color: #fff;">{l s='Allow visitors to place new orders using WhatsApp'  mod='orderonwhatsapp'}</h1>

			<div class="orderonwhatsapp"><a href="#" class="fb-msg-btn"><img src="{$path|escape:'htmlall':'UTF-8'}views/img/whatsapp.png"> Order on WhatsApp</a></div>

			<hr>

		</div>

	</div>

</div>

<div class="alert alert-info version-status" style="display: none"></div>


<style type="text/css">



.orderonwhatsapp a.fb-msg-btn{

	display: inline-block;

	font-family: Open Sans;

	font-size: 12px;

	font-weight: bold;

	color: #fff;

	text-align: center;

	padding: 7px;

	margin: 0;

	background-color: #67c15e;*

	border: 0;

	border-radius: 50px; 

	-moz-border-radius: 50px; 

	-webkit-border-radius: 50px;

	cursor: pointer;

	outline: none;

    text-decoration: none;

}

.orderonwhatsapp a:hover.fb-msg-btn { 

	background-color: #35713c; 

}



.orderonwhatsapp img {

    max-width: 20px;

    vertical-align: middle;

    margin-right: 3px;

}



</style>

<script>
setTimeout(
	function version_status()
	{
	  var api_check = "https://www.weblir.com/version/latest.php?shop={$shop|escape:'html':'UTF-8'}&ref={$ref|escape:'html':'UTF-8'}&module={$modulename|escape:'html':'UTF-8'}&version={$moduleversion|escape:'html':'UTF-8'}";
	  $.getJSON(api_check)
	    .done(function( data ) {
	    	if (typeof data.version_status === 'undefined') {
	    		/* do nothing */
	    	} else {
	    		$( ".version-status").html(data.version_status).show();
	    	}
	    	
	    }).error(function() {});
	},
1000);
</script>