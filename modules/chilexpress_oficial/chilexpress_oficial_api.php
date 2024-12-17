<?php 

if ( ! class_exists( 'Chilexpress_Oficial_API' ) ) {
	class Chilexpress_Oficial_API {
		
		private function get_local_file_contents( $file_path ) { // NOSONAR
		    ob_start();
		    include $file_path;
		    $contents = ob_get_clean();
		    return $contents;
		}

		/**
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			$this->id  = 'chilexpress_oficial';
			$this->init();
			
			$this->api_staging_base_url = 'https://testservices.wschilexpress.com/';
			$this->api_production_base_url = 'https://services.wschilexpress.com/';

			$module_options["ambiente"] = Configuration::get('ambiente');
			$module_options["api_key_georeferencia_enabled"] = Configuration::get('api_key_georeferencia_enabled');
			$module_options["api_key_generacion_ot_enabled"] = Configuration::get('api_key_generacion_ot_enabled');

			if ($module_options['ambiente'] == 'production') {
				$this->api_base_url = $this->api_production_base_url;
			} else {
				$this->api_base_url = $this->api_staging_base_url;
			}

			$this->api_key_georeferencia = Configuration::get('api_key_cotizador_value');
			$this->api_key_cobertura = Configuration::get('api_key_georeferencia_value');
			$this->api_key_ot = Configuration::get('api_key_generacion_ot_value');

			$this->api_geo_enabled = isset($module_options['api_key_georeferencia_enabled']) ? $module_options['api_key_georeferencia_enabled'] : false;
			$this->api_ot_enabled  = isset($module_options['api_key_generacion_ot_enabled']) ? $module_options['api_key_generacion_ot_enabled'] : false;
		}

		/**
		 * Obtiene Categorias de Art'iculos
		 */
		public function obtener_categorias_articulos(){

			$data = $this->internalApiCallGET(
				"https://services.wschilexpress.com/agendadigital/api/v3/Cotizador/GetArticulos",
				"9c853753ce314c81934c4f966dad7755"
			);
			$descripciones = array();
			foreach ($data["ListArticulos"] as $entry) {
				$descripciones[$entry["Codigo"]] = $entry["Glosa"];
			}
			return $descripciones;
		}

		/**
		 * Obtiene las regiones de cobertura desde la API de cobertura
		 * Si no las encuentra ah'i, entonces utiliza un archivo local de respaldo
		 */
		public function obtener_regiones() {
			$url = $this->api_base_url . 'georeference/api/v1.0/regions';
			$regiones = array();
			// se pregunta al servicio
			$data = $this->internalApiCallGET( $url, $this->api_key_georeferencia );
			// Si el servicio no responde o es un array sin datos
			if( !isset( $data["regions"] ) || !$data )
			{
				$directory =  dirname( __FILE__ )  . '/data/regiones/' ;
				$file_path = $directory . 'regiones.json';
				if (file_exists($file_path)) {
			    	$data = $this->get_local_file_contents( $file_path );
			    	$data = json_decode($data, true);
				}
			}
			foreach ( $data["regions"] as $region ) 
			{
				$key = $region['regionId'];
				$regiones[$key] = $region['regionName'];
			}
			return $regiones;
		}

		/**
		 * Ontiene las comunas de cobertura de una regi'on, si no encuentra la respuesta
		 * o hay un error en la llamada a la API, se lee el archivo local de comunas
		 */
		public function obtener_comunas_desde_region($codigo_region = "R1") {
			$url = $this->api_base_url . 'georeference/api/v1.0/coverage-areas?RegionCode='.$codigo_region.'&type=1';
			$comunas = array();
			// se pregunta al servicio
			$data = $this->internalApiCallGET( $url, $this->api_key_georeferencia );
			if( !isset($data["coverageAreas"]) || !$data )
			{
				$directory =  dirname( __FILE__ )  . '/data/comunas/' ;
				$file_path = $directory . $codigo_region .'.json';
				if (file_exists($file_path)) {
			    	$data = $this->get_local_file_contents( $file_path );
			    	$data = json_decode($data, true);
				}
			}
			foreach ($data["coverageAreas"] as $comuna) 
			{
				$key = $comuna['countyCode'];
				$comunas[$key] = $comuna['coverageName'];
			}
			// se elimina la comuna cuando es SCOB, esto ocurre en RN
			unset($comunas["SCOB"]);

			return $comunas;
		}

		public function obtener_cotizacion($comuna_origen, $comuna_destino, $weight = 1, $height = 1, $width = 1, $depth = 1, $declaredWorth = 1000) {
			
			$payload = array(
				"originCountyCode" =>	$comuna_origen,
				"destinationCountyCode" => $comuna_destino,
				"package" => array(
					"weight" =>	$weight,
					"height" =>	$height,
					"width" =>	$width,
					"length" =>	$depth
				),
				"productType" => 3,
				"contentType" => 1,
				"declaredWorth" => $declaredWorth,
				"deliveryTime" => 0
			);

			$json_result = $this->internalApiCallPOST(
					json_encode($payload),
					"rating/api/v1.0/rates/courier",
					$this->api_key_georeferencia
				);

			return $json_result;
			
		}

		public function generar_ot($payload_str) {
			$json_result = $this->internalApiCallPOST(
					$payload_str,
					"transport-orders/api/v1.0/transport-orders",
					$this->api_key_ot
				);

			return $json_result;
		}



		public function obtener_estado_ot($trackingId, $reference, $rut ) {
			
			$payload = array(
				"reference"=> $reference,
				"transportOrderNumber"=> $trackingId,
				"rut"=> $rut,
				"showTrackingEvents" => 1
			);
			
			$json_result = $this->internalApiCallPOST(
					json_encode($payload),
					"transport-orders/api/v1.0/tracking",
					$this->api_key_ot
				);

			return $json_result;
		}

		/**
		 * Ejecuta la llamada a un servicio POST
		 */
		private function internalApiCallPOST($payload_str, $url_ws, $apiKey) {
			$url_ws = $this->api_base_url.$url_ws;
	        $curl = curl_init();
	        $headers = array();
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'Ocp-Apim-Subscription-Key: '.$apiKey;
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // NOSONAR
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // NOSONAR
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($curl, CURLOPT_HEADER, 0);
	        curl_setopt($curl, CURLOPT_URL, $url_ws);
	        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
	        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload_str);

	        $result = curl_exec($curl);
	        // echo 'Curl error: ' . curl_error($curl);
	        // echo '<br>url_ws: ' . $url_ws;
	        // echo '<br>apiKey: ' . $apiKey;
	        curl_close($curl);

	        // var_dump($result);
	        if (!$result){
	            return false;
	        } else { 
	            $jresult = json_decode($result,true);
	            return $jresult;
	        }
		}

		/**
		 * Ejecuta la ejecuci'on de un servicio GET
		 */
		private function internalApiCallGET( $url, $apiKey ) {
			$url_ws = $url;
	        $curl = curl_init();
	        $headers = array();
	        $headers[] = 'Content-Type: application/json';
	        $headers[] = 'Ocp-Apim-Subscription-Key: '.$apiKey;
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // NOSONAR
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // NOSONAR
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($curl, CURLOPT_HEADER, 0);
	        curl_setopt($curl, CURLOPT_URL, $url_ws);
	        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
	        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	        $result = curl_exec($curl);
	        curl_close($curl);

	        if (!$result){
	            return false;
	        } else { 
	            $jresult = json_decode( $result, true );
	            return $jresult;
	        }
		}


		/**
		 * Init your settings
		 *
		 * @access public
		 * @return void
		 */
		function init() {
			
		}

		

	}
}