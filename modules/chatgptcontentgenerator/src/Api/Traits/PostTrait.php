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

trait PostTrait
{
    /**
     * Generate post for product
     *
     * @param $product array - Product options (name, category, brand and etc)
     *
     * @return array
     * @throws Exception
     */
    public function generatePostForProduct(array $product)
    {
        $response = $this->sendRequest(
            '/post/product-post',
            'POST',
            [
                'productName' => (isset($product['name']) ? $product['name'] : ''),
                'maxLength' => (isset($product['maxLength']) ? $product['maxLength'] : ''),
                'langIsoCode' => (isset($product['langIsoCode']) ? $product['langIsoCode'] : ''),
                'categoryName' => (isset($product['category']) ? $product['category'] : ''),
                'brandName' => (isset($product['brand']) ? $product['brand'] : ''),
            ]
        );

        if (!isset($response['text'])) {
            $code = (isset($response['error']['code']) ? $response['error']['code'] : 0);
            throw new \Exception($response['error']['message'], $code);
        }

        return $response;
    }

    /**
     * Translate post content
     *
     * @param string $text Post content (HTML text)
     *
     * @return array
     * @throws Exception
     */
    public function translatePostContent($text, $fromLanguageIso, $toLanguageIso)
    {
        if (empty($fromLanguageIso) || empty($toLanguageIso)) {
            throw new \Exception('The language iso is not valid');
        }

        if (!is_string($text)) {
            throw new \Exception('The text is not valid');
        }

        if (trim($text) === '') {
            throw new \Exception('The text could not be empty');
        }

        $response = $this->sendRequest(
            '/post/translate-content',
            'POST',
            [
                'text' => trim($text),
                'fromLangauge' => $fromLanguageIso,
                'toLanguage' => $toLanguageIso,
                'entityType' => 'page',
            ]
        );

        if (!isset($response['text'])) {
            $code = (isset($response['error']['code']) ? $response['error']['code'] : 0);
            throw new \Exception($response['error']['message'], $code);
        }

        return $response;
    }

    /**
     * Generate post by name
     *
     * @param string $postName
     *
     * @return array
     * @throws Exception
     */
    public function postContent($postName, $textLength = 0, $langIsoCode = null)
    {
        if (!is_numeric($textLength)) {
            throw new \Exception('The text length is not valid');
        }

        $response = $this->sendRequest(
            '/post/post-content',
            'POST',
            [
                'postName' => $postName,
                'maxLength' => $textLength,
                'langIsoCode' => $langIsoCode,
            ]
        );

        if (!isset($response['text'])) {
            $code = (isset($response['error']['code']) ? $response['error']['code'] : 0);
            throw new \Exception($response['error']['message'], $code);
        }

        return $response;
    }
}
