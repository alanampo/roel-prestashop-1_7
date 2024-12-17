$(document).ready(function() { // NOSONAR
    if ( $("body.adminaddresses #address_form #id_state").length ) {
		var $city = $("body.adminaddresses #address_form #city");
		var $state = $("body.adminaddresses #address_form #id_state");
		$city.parent().siblings('label').text('Comuna'); // Esto podría ser una opción del Módulo
		$state.parent().siblings('label').text('Región'); // Esto podría ser una opción del Módulo

		var city_val = $city.val();
		var $city_parent = $city.parent('div');
		var $new_city = $('<select name="city" class="form-control form-control-select"><option value="'+city_val+'"  selected>Esperando que carguen las regiones...</select>')
		$city.remove();
		$city_parent.append($new_city);
		$city = $new_city;

		$state.on('change', function(ev){
			updateCity();
		});

		// No hay forma de saber cuando cambiara el estado debido a que funciona via ajax
		// Y no tiene ningun callback, asi que solo nos queda esperar que cambie
		var stateWasUpdated = false;
		var initHTMLLength = $state.html().length;
		for (var i=100; i< 8000; i+=100) {

			setTimeout(function(){
				if($state.html().length !== initHTMLLength && !stateWasUpdated) {
					stateWasUpdated = true;
					updateCity();
				}
			}, i);
		}

		function updateCity() {

			var state_val = $state.val();
			city_val = $city.val();
			$city.html('<option value="" disabled="" selected="selected">Cargando...</selected>');
	        $.ajax({
		    	type: 'POST',
			    url: chilexpress_oficial_ajax_url.replace(/&amp;/gi,'&'),
			    cache: false,
			    dataType: 'json',
			    data: {
			        action : 'obtener_comunas_con_id',
			        region : state_val,
			        ajax: true
			    },
			    success: function (result) {
			        var html = '';
			        Object.keys(result.comunas).forEach( function(key) {
			        	html += '<option value="' + key + '" '+( (key === city_val)?'selected="selected"':'' )+'>' + result.comunas[key] + '</option>';
			        });
			        if ($city) {
			        	$city.html(html);
			        }
			    }
			});
		}
	}
});