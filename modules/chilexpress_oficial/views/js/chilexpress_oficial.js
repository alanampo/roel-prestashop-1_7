$(document).ready(function () { // NOSONAR

	function actualizarComunaDesdeRegion($region, $comuna) {
		var region = $region.val();
		$comuna.html('<option>CARGANDO...</option>')
		$.ajax({
			type: 'POST',
			url: chilexpress_oficial_ajax_url,
			cache: false,
			dataType: 'json',
			data: {
				action: 'obtener_comunas',
				region: region,
				ajax: true
			},
			success: function (result) {
				var html = '';
				Object.keys(result.comunas).forEach(function (key) {
					html += '<option value="' + key + '">' + result.comunas[key] + '</option>';
				})
				$comuna.html(html);
			}
		});
	}

	$("#configuration_form.chilexpress_oficial #region_origen").on('change', function (ev) {
		actualizarComunaDesdeRegion($(ev.currentTarget), $("#configuration_form.chilexpress_oficial #comuna_origen"));
	});

	$("#configuration_form.chilexpress_oficial #region_devolucion").on('change', function (ev) {
		actualizarComunaDesdeRegion($(ev.currentTarget), $("#configuration_form.chilexpress_oficial #comuna_devolucion"));
	});


	if ($("body.adminaddresses #address_form #id_state").length) {
		var $city = $("body.adminaddresses #address_form #city");
		var $state = $("body.adminaddresses #address_form #id_state");
		$city.parent().siblings('label').text('Comuna'); // Esto podría ser una opción del Módulo
		$state.parent().siblings('label').text('Región'); // Esto podría ser una opción del Módulo

		var city_val = $city.val();
		var $city_parent = $city.parent('div');
		var $new_city = $('<select name="city" class="form-control form-control-select"><option value="' + city_val + '"  selected>Esperando que carguen las regiones...</select>')
		$city.remove();
		$city_parent.append($new_city);
		$city = $new_city;

		$state.on('change', function (ev) {
			updateCity();
		});

		// No hay forma de saber cuando cambiara el estado debido a que funciona via ajax
		// Y no tiene ningun callback, asi que solo nos queda esperar que cambie
		var stateWasUpdated = false;
		var initHTMLLength = $state.html().length;
		for (var i = 100; i < 8000; i += 100) {

			setTimeout(function () {
				if ($state.html().length !== initHTMLLength && !stateWasUpdated) {
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
				url: chilexpress_oficial_ajax_url.replace(/&amp;/gi, '&'),
				cache: false,
				dataType: 'json',
				data: {
					action: 'obtener_comunas_con_id',
					region: state_val,
					ajax: true
				},
				success: function (result) {
					var html = '';
					Object.keys(result.comunas).forEach(function (key) {
						html += '<option value="' + key + '" ' + ((key === city_val) ? 'selected="selected"' : '') + '>' + result.comunas[key] + '</option>';
					});
					if ($city) {
						$city.html(html);
					}
				}
			});
		}
	}

	/** Orders table **/
	var vdot1 = parseInt(_PS_VERSION_.split(".")[0]);
	var vdot2 = parseInt(_PS_VERSION_.split(".")[1]);
	var vdot3 = parseInt(_PS_VERSION_.split(".")[2]);
	var vdot4 = parseInt(_PS_VERSION_.split(".")[3]);
	var intVersion = ((vdot1 * 1000) + (vdot2 * 100) + (vdot3 * 10) + vdot4);
	if (intVersion >= 1773) {
		$('td.column-carrier_reference, td.column-tracking_number').each(function () {
			if ($(this).has('a').length === 0) {
				function decodeHtml(str) {
					var map =
					{
						'&amp;': '&',
						'&lt;': '<',
						'&gt;': '>',
						'&quot;': '"',
						'&#039;': "'"
					};
					return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function (m) {
						return map[m];
					});
				}
				$(this).html(decodeHtml($(this).text()));
			}
		});
	}

	if ($("a[data-tracking-code]").length) {
		$(document.body).append($("#trackingModal"));

		$(".with-tooltip").tooltip();
		setTimeout(function () {
			$(".with-tooltip").tooltip();
		}, 1000);
		$("a[data-tracking-code]").each(function (index, el) {

			$(el).on('click', function (ev) { // NOSONAR
				ev.preventDefault();
				var $el = $(ev.currentTarget);
				if ($el.text() === 'Cargando...') { return false; }
				var oldText = $el.text();
				$el.text('Cargando...');

				jQuery.ajax({
					type: "post",
					url: chilexpress_oficial_ajax_url,
					dataType: 'json',
					data: {
						action: 'track_order',
						order_id: $el.data('orderid'),
						tracking_code: $el.data('tracking-code'),
						ajax: true
					},
					error: function (result) {
						$el.text(oldText);
						alert('Hubo un error al cargar la información de la Orden de Transporte');
					},
					success: function (result) {

						$el.text(oldText);
						var data = {};
						if (result.error) {
							alert(result.error);
							return;
						}
						if (result.response && result.response.data) {
							data = result.response;
						} else {
							data = result;
						}

						var dimensions = data.data.transportOrderData.dimensions.split("x");
						var dimension_1 = parseInt(dimensions[0]) * 100;
						var dimension_2 = parseInt(dimensions[1]) * 100;
						var dimension_3 = parseInt(dimensions[2]) * 100;

						var truncated_dimensions = "" + dimension_1 + "x" + dimension_2 + "x" + dimension_3;

						$("#trackingModal").modal('show');
						$("#trackingModal .orden_transporte").text(data.data.transportOrderData.transportOrderNumber);
						$("#trackingModal .producto").text(data.data.transportOrderData.product);
						$("#trackingModal .servicio").text(data.data.transportOrderData.service);
						$("#trackingModal .status").text(data.data.transportOrderData.status);

						$("#trackingModal .dimensiones").text(truncated_dimensions);
						$("#trackingModal .peso").text(data.data.transportOrderData.weight);

						$("#trackingModal .rut_receptor").text(data.data.deliveryData.receptorRut);
						$("#trackingModal .nombre_receptor").text(data.data.deliveryData.receptorName);
						$("#trackingModal .fecha_entrega").text(data.data.deliveryData.receptorDate);
						$("#trackingModal .hora_entrega").text(data.data.deliveryData.receptorHour);

						if (data.data.trackingEvents.length) {
							var html = '';
							$.each(data.data.trackingEvents, function (index, item) {
								html += '<tr><td align="center">' + item.eventDate + '</td><td align="center">' + item.eventHour.split('.')[0] + '</td><td align="center">' + item.description + '</td></tr>'
							});
							$("#wc-chilexpress-events > tbody").html(html);
						} else {
							$("#wc-chilexpress-events > tbody").html('<tr><td colspan="3" style="text-align:center;">No existen eventos aún para este envio.</td></tr><tr></tr>');
						}
					}
				});

				return false;
			})
		});
	}


});
