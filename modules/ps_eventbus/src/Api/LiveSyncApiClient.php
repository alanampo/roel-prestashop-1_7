<?php

namespace PrestaShop\Module\PsEventbus\Api;

use GuzzleHttp\Psr7\Request;
use PrestaShop\Module\PsEventbus\Config\Config;
use PrestaShop\Module\PsEventbus\Service\PsAccountsAdapterService;
use ps_eventbus_v3_0_7\Prestashop\ModuleLibGuzzleAdapter\ClientFactory;
use ps_eventbus_v3_0_7\Prestashop\ModuleLibGuzzleAdapter\Interfaces\HttpClientInterface;
class LiveSyncApiClient
{
    /**
     * @var string
     */
    private $liveSyncApiUrl;
    /**
     * @var \Ps_eventbus
     */
    private $module;
    /**
     * Accounts JSON Web token
     *
     * @var string
     */
    private $jwt;
    /**
     * Accounts Shop UUID
     *
     * @var string
     */
    private $shopId;
    /**
     * @param string $liveSyncApiUrl
     * @param \Ps_eventbus $module
     * @param PsAccountsAdapterService $psAccountsAdapterService
     */
    public function __construct(string $liveSyncApiUrl, \Ps_eventbus $module, PsAccountsAdapterService $psAccountsAdapterService)
    {
        $this->module = $module;
        $this->jwt = $psAccountsAdapterService->getOrRefreshToken();
        $this->shopId = $psAccountsAdapterService->getShopUuid();
        $this->liveSyncApiUrl = $liveSyncApiUrl;
    }
    /**
     * @see https://docs.guzzlephp.org/en/stable/quickstart.html-
     *
     * @param int $timeout
     *
     * @return HttpClientInterface
     */
    private function getClient($timeout = Config::SYNC_API_MAX_TIMEOUT)
    {
        return (new ClientFactory())->getClient(['allow_redirects' => \true, 'connect_timeout' => 10, 'http_errors' => \false, 'timeout' => $timeout]);
    }
    /**
     * @param string $shopContent
     * @param int $shopContentId
     * @param string $action
     *
     * @return array
     */
    public function liveSync(string $shopContent, int $shopContentId, string $action)
    {
        $response = $this->getClient(3)->sendRequest(new Request('POST', $this->liveSyncApiUrl . '/notify/' . $this->shopId, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->jwt, 'User-Agent' => 'ps-eventbus/' . $this->module->version, 'Content-Type' => 'application/json'], '{"shopContents": ["' . $shopContent . '"], "shopContentId": ' . $shopContentId . ', "action": "' . $action . '"}'));
        return ['status' => \substr((string) $response->getStatusCode(), 0, 1) === '2', 'httpCode' => $response->getStatusCode(), 'body' => $response->getBody()];
    }
}
