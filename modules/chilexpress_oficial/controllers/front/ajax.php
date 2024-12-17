<?php
include_once(dirname( __FILE__ )  . '/../../chilexpress_oficial_api.php');
class Chilexpress_OficialAjaxModuleFrontController extends ModuleFrontController
{
 
    public function initContent()
    {
        $this->ajax = true;
        $this->api = new Chilexpress_Oficial_API();
        // your code here
        parent::initContent();
    }

    public function displayAjax() // NOSONAR
    {   
        $CONSTANT_ACTION = "action";
        $CONSTANT_ORDER_ID = "order_id";
    	header('Content-Type: application/json');
    	$action = 'obtener_regiones';
    	if (isset($_POST[$CONSTANT_ACTION])) {
    		$action = $_POST[$CONSTANT_ACTION];
    	}
        if (isset($_GET[$CONSTANT_ACTION])) {
            $action = $_GET[$CONSTANT_ACTION];
        }
    	switch ($action) {

            case 'get_tracking_code_from_reference':

                $references = array();
                if (isset($_POST['references'])) {
                    $references = $_POST['references'];
                }

                $orders = new PrestaShopCollection('Order');
                $orders->where('reference', 'in', $references);

                $response = array();

                foreach ($orders as $order) {
                    if ($this->context->customer->id == $order->id_customer) {
                        $response[$order->reference] = array('tracking' => $order->shipping_number, $CONSTANT_ORDER_ID => $order->id);
                    }
                }
                


                die(Tools::jsonEncode( array( $CONSTANT_ACTION => $action, 'response' => $response, "default" => "ok")));

            break;
            case 'track_order':
                $order_id = "0";
                $tracking_code = "";
                if (isset($_POST[$CONSTANT_ORDER_ID])) {
                    $order_id = $_POST[$CONSTANT_ORDER_ID];
                }
                if (isset($_POST["tracking_code"])) {
                    $tracking_code = $_POST["tracking_code"];
                }


                $order = new Order($order_id);
                $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
                $transportOrderNumbers = explode(',', $order_carrier->tracking_number);

               
               
                if (!in_array($tracking_code, $transportOrderNumbers )) {
                    die(Tools::jsonEncode( array( $CONSTANT_ACTION => $action, "tracking_code" => $tracking_code, 'error'=> "Tracking number Inválido" )));
                }

                

                
                $response = $this->api->obtener_estado_ot($tracking_code, "ORDEN-".$order_id, intval(Configuration::get('rut_marketplace_remitente')) ); 
                
                die(Tools::jsonEncode( array( $CONSTANT_ACTION => $action, "tracking_code" => $tracking_code, "response"=>$response )));
                
            break;
    		case 'obtener_regiones':
				die(Tools::jsonEncode( array( $CONSTANT_ACTION => $action, "regiones" => $this->api->obtener_regiones() )));
    		break;
            case 'obtener_comunas_con_id':
                $region = "0";
                if (isset($_POST["region"])) {
                    $region = $_POST["region"];
                }
                $sql = 'SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.intval($region);
                // echo $sql;die();
                $result = Db::getInstance()->getRow($sql);
                $region_iso = $result["iso_code"];
                
                die(Tools::jsonEncode( array( $CONSTANT_ACTION => $action, "result" => $region_iso,"comunas" => $this->api->obtener_comunas_desde_region($region_iso) )));
            break;
    		case 'obtener_comunas':

				$region = "R1";
                $regiones = array(
                    "R1","R2","R3","R4","R5","R6","R7","R8","R9","R10","R11","R12","R13","R14","R15","R16","RM"
                );

				if (isset($_POST["region"])) {
		    		$region = $_POST["region"];
                    if (in_array($region,$regiones)){
                        $region = $regiones[array_search($region, $regiones)];
                    }
		    	}

				die(Tools::jsonEncode( array( $CONSTANT_ACTION => $action, "comunas" => $this->api->obtener_comunas_desde_region($region) )));
    		break;
            case 'print_ot':

            $order_id = $_GET['order_id'];
            $query = new DbQuery();
            $query->from('chilexpress_oficial_ordermeta', 'com');
            $query->where('com.id_order = "'.$order_id.'"');
            $metasArray = Db::getInstance()->executeS($query);

            $html = "";
            $otrackings = array();
            foreach ($metasArray as $ot) {
                $otrackings[] = $ot['transportOrderNumber'];
                $html .= '<img src="@'.$ot['labelData'].'" width="400" /><br /><br />';
            }

            $pdf = new PDFLabel($this->context->smarty, 'P');
            $pdf->setFilename('chilexpress-ot-'.implode('-', $otrackings).'.pdf');
            $pdf->setLabelsHTML($html);
            $pdf->render(true);
            exit();

    		default;
    			die(Tools::jsonEncode( array( $CONSTANT_ACTION => $action, "default" => "ok")));
    		break;
    	}        
    }
}