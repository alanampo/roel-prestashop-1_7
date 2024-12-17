/**
* 2012-2023 Weblir
*
*  @author    weblir <hello@weblir.com>
*  @copyright 2012-2023 weblir
*  @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
*  International Registered Trademark & Property of weblir.com
*
*  You are allowed to modify this copy for your own use only. You must not redistribute it. License
*  is permitted for one Prestashop instance only but you can install it on your test instances.
*/

//jQuery time
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

$(document).load(function(){
	setActiveStates();
	$('#order-on-whatsapp-form #id_country').change(function(){
	    setActiveStates();
	});
});

setActiveStates();

$('#order-on-whatsapp-form #id_country').change(function(){
    setActiveStates();
});

function setActiveStates() {
	var active_country = $('#order-on-whatsapp-form #id_country').find(":selected").val();

	$('#order-on-whatsapp-form #id_state').find('option')
			    .remove()
			    .end();

	$("#allstates option").each(function() {
		if ($(this).attr('country') == active_country) {
			$($(this)).clone().appendTo('#order-on-whatsapp-form #id_state');
	    }
	});


	// $("#order-on-whatsapp-form #id_state option").attr('disabled', 'disabled').hide();
	// $('#order-on-whatsapp-form #id_state option').each(function(){
	// 	if ($(this).attr('country') == active_country) {
	// 		$(this).removeAttr('disabled');
	// 		$(this).show();
	// 	}
	// });
}

function initiateWhatsAppOrder() {
	var wa_page_type = $("#OrderOnWhatsAppModal").attr("wa_page_type");
	var id_country = $("#order-on-whatsapp-form #id_country").val();
	var id_state = $("#order-on-whatsapp-form #id_state").val();
	var city = $("#order-on-whatsapp-form #city").val();
	var first_name = $("#order-on-whatsapp-form #first-name").val();
	var last_name = $("#order-on-whatsapp-form #last-name").val();
	var email = $("#order-on-whatsapp-form #email").val();
	var mobile_phone = $("#order-on-whatsapp-form #mobile-phone").val();
	var address = $("#order-on-whatsapp-form #address").val();
	var postcode = $("#order-on-whatsapp-form #postcode").val();
	var product_qty = $("#wa_product_qty").val();
	var product_id = $("#wa_id_product").val();

	$.ajax({
		method: "POST",
		url: whatsapp_order_link,
		data: {
			action:"initOrder",
			id_country: id_country,
			id_state: id_state,
			city: city,
			email: email,
			first_name: first_name,
			last_name: last_name,
			mobile_phone: mobile_phone,
			address: address,
			postcode: postcode,
			product_id: product_id,
			product_qty: product_qty,
			wa_page_type: wa_page_type
		}
	}).done(function( msg ) {
		var data = jQuery.parseJSON( msg );

		$("form#order-on-whatsapp-form").hide();
		$("#order-confirmation-message").show();

		setTimeout(
			function() {
				location.href = data.data.whatsapp_link;
			},
			5000
		);

	});
}

$("#order-on-whatsapp-form .next").click(function(){
	var valid_inputs = true;

	$("#order-on-whatsapp-form .active-step .required-field").each(function(){
		if ($(this).val() == '' || $(this).val() == null) {
			$(this).closest('.field').find('.warning-message').remove();
			$(this).addClass('is-empty').closest('.field').append('<span class="warning-message">Required field!</span>');
			$(this)
			valid_inputs = false;
		} else {
			$(this).removeClass('is-empty').closest('.field').find('.warning-message').remove();
		}

		if ($(this).attr('name') == 'mobile-phone') {
			var phone = $(this).val();
			var phoneNum = phone.replace(/[^\d]/g, '');
			if (phoneNum.length > 7 && phoneNum.length < 14) {
				$(this).removeClass('is-empty').closest('.field').find('.warning-message').remove();
			} else {
				valid_inputs = false;
				$(this).closest('.field').find('.warning-message').remove();
				$(this).addClass('is-empty').closest('.field').append('<span class="warning-message">Invalid mobile phone number!</span>');
			}
		}
	});

	if (valid_inputs == true) {

		$(".selected-country").html($("#id_country option:selected").text());
        $(".selected-state").html($("#id_state option:selected").text());
        $(".selected-city").html($("#city").val());
        $(".selected-email").html($("#email").val());
        $(".selected-address").html($("#address").val());
        $(".selected-postcode").html($("#postcode").val());
        $(".selected-firstname").html($("#first-name").val());
        $(".selected-lastname").html($("#last-name").val());
        $(".selected-phone").html($("#mobile-phone").val());

		if(animating) return false;
		animating = true;
		
		current_fs = $(this).parent();
		next_fs = $(this).parent().next();
		
		//activate next step on progressbar using the index of next_fs
		$("#order-on-whatsapp-form #progressbar li").eq($("fieldset").index(next_fs)).addClass("active").addClass("active-step");
		$("#order-on-whatsapp-form fieldset").eq($("fieldset").index(next_fs)).addClass("active-step");
		
		//show the next fieldset
		next_fs.show(); 
		//hide the current fieldset with style
		current_fs.animate({opacity: 0}, {
			step: function(now, mx) {
				//as the opacity of current_fs reduces to 0 - stored in "now"
				//1. scale current_fs down to 80%
				scale = 1 - (1 - now) * 0.2;
				//2. bring next_fs from the right(50%)
				left = (now * 50)+"%";
				//3. increase opacity of next_fs to 1 as it moves in
				opacity = 1 - now;
				current_fs.css({
	        'transform': 'scale('+scale+')',
	        'position': 'absolute'
	      });
				next_fs.css({'left': left, 'opacity': opacity});
			}, 
			duration: 800, 
			complete: function(){
				current_fs.hide();
				animating = false;
			}
		});
	}

		
});

$("#order-on-whatsapp-form .previous").click(function(){
	if(animating) return false;
	animating = true;
	
	current_fs = $(this).parent();
	previous_fs = $(this).parent().prev();
	
	//de-activate current step on progressbar
	$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active").removeClass("active-step");
	$("#order-on-whatsapp-form fieldset").eq($("fieldset").index(current_fs)).removeClass("active-step");
	
	//show the previous fieldset
	previous_fs.show(); 
	//hide the current fieldset with style
	current_fs.animate({opacity: 0}, {
		step: function(now, mx) {
			//as the opacity of current_fs reduces to 0 - stored in "now"
			//1. scale previous_fs from 80% to 100%
			scale = 0.8 + (1 - now) * 0.2;
			//2. take current_fs to the right(50%) - from 0%
			left = ((1-now) * 50)+"%";
			//3. increase opacity of previous_fs to 1 as it moves in
			opacity = 1 - now;
			current_fs.css({'left': left});
			previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
		}, 
		duration: 800, 
		complete: function(){
			current_fs.hide();
			animating = false;
		}
	});
});

$("#order-on-whatsapp-form .submit").click(function(){
	var valid_inputs = true;

	$("#order-on-whatsapp-form .active-step .required-field").each(function(){
		if ($(this).val() == '' || $(this).val() == null) {
			$(this).closest('.field').find('.warning-message').remove();
			$(this).addClass('is-empty').closest('.field').append('<span class="warning-message">Required field!</span>');
			$(this)
			valid_inputs = false;
		} else {
			$(this).removeClass('is-empty').closest('.field').find('.warning-message').remove();
		}
	});

	if (valid_inputs == true) {
		initiateWhatsAppOrder();
	} else {
		alert("Please fill the required fields!");
		return false;
	}

	return false;
})

