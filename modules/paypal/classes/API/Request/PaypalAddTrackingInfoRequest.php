<?php
/*
 * 2007-2024 PayPal
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
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2024 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  @copyright PayPal
 *
 */

namespace PaypalAddons\classes\API\Request;

use Exception;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Client\HttpClient;
use PaypalAddons\classes\API\ExtensionSDK\AddTrackingInfo;
use PaypalAddons\classes\API\HttpAdoptedResponse;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\Response;
use PaypalAddons\classes\PaypalException;
use PaypalAddons\services\Builder\AddTrackingInfoRequestBuilder;
use Throwable;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaypalAddTrackingInfoRequest extends RequestAbstract
{
    protected $paypalOrder;

    public function __construct(HttpClient $client, AbstractMethodPaypal $method, \PaypalOrder $paypalOrder)
    {
        parent::__construct($client, $method);

        $this->paypalOrder = $paypalOrder;
    }

    public function execute()
    {
        $response = $this->initResponse();

        try {
            $sendTrackingInfoRequest = new AddTrackingInfo($this->initBuilder());
            $exec = $this->client->execute($sendTrackingInfoRequest);

            if ($exec instanceof HttpAdoptedResponse) {
                $exec = $exec->getAdoptedResponse();
            }

            if ($exec->statusCode >= 200 && $exec->statusCode < 300) {
                $response->setSuccess(true);
            } else {
                $response->setSuccess(false);
            }

            $response->setData($exec);
        } catch (PaypalException $e) {
            $error = new Error();
            $resultDecoded = json_decode($e->getMessage(), true);

            if (false == empty($resultDecoded['name'])) {
                $error->setErrorCode($resultDecoded['name']);
            }

            if (false == empty($resultDecoded['message'])) {
                $error->setMessage($resultDecoded['message']);
            } else {
                $error->setMessage($e->getMessage());
            }

            $response->setSuccess(false)
                ->setError($error);
        } catch (Throwable $e) {
            $error = new Error();
            $error->setErrorCode($e->getCode())->setMessage($e->getMessage());
            $response->setError($error)->setSuccess(false);
        } catch (Exception $e) {
            $error = new Error();
            $error->setErrorCode($e->getCode())->setMessage($e->getMessage());
            $response->setError($error)->setSuccess(false);
        }

        return $response;
    }

    protected function initResponse()
    {
        return new Response();
    }

    protected function initBuilder()
    {
        return new AddTrackingInfoRequestBuilder($this->paypalOrder);
    }
}
