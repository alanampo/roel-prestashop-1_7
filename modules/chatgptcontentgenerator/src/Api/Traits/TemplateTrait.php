<?php
/**
 * 2007-2023 PrestaShop
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
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\Module\Chatgptcontentgenerator\Api\Traits;

if (!defined('_PS_VERSION_')) {
    exit;
}

trait TemplateTrait
{
    public function descriptionByPrompt($prompt, $entity)
    {
        if (!is_string($prompt) && trim($prompt) === '') {
            throw new \Exception('The prompt request message is not valid');
        }

        if (!is_string($entity) || trim($entity) === '') {
            throw new \Exception('The entity is not set or not valid');
        }

        $response = $this->sendRequest(
            '/text/prompt-description',
            'POST',
            [
                'prompt' => $prompt,
                'entityType' => $entity,
            ]
        );

        if (isset($response['inQueue']) && $response['inQueue']) {
            return [
                'text' => '',
                'nbWords' => 0,
                'inQueue' => $response['inQueue'],
                'requestId' => (int) $response['requestId'],
            ];
        }

        if (!isset($response['text'])) {
            $code = (isset($response['error']['code']) ? $response['error']['code'] : 0);
            throw new \Exception($response['error']['message'], $code);
        }

        return $response;
    }
}
