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
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptSpinoffConnections;

class ChatgptcontentgeneratorProductsSitemapModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

        if (!Configuration::getGlobalValue('CHATGPTSPINOFF_MANAGE')) {
            Tools::redirect('index.php?controller=404');
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->registerStylesheet(
            'spinoff_sitemap',
            '/modules/chatgptcontentgenerator/views/css/back.sitemap.css',
            ['media' => 'all', 'priority' => 100]
        );
    }

    public function initContent()
    {
        $this->context->smarty->assign(
            [
                'spinOffLinks' => $this->getSpinOffLinks(),
            ]
        );

        parent::initContent();
        $this->setTemplate('module:chatgptcontentgenerator/views/templates/front/productssitemap.tpl');
    }

    protected function getSpinOffLinks()
    {
        $spinOffLinks = [];

        if ($allSpinofs = GptSpinoffConnections::getAllConectionsInfoByLang($this->context->language->id)) {
            foreach ($allSpinofs as $spinof) {
                $spinofName = trim($spinof['name']);
                $firstLetter = strtoupper(mb_substr($spinofName, 0, 1));

                $spinOffLinks[$firstLetter][] = [
                    'id_spinoff' => $spinof['id_spinoff'],
                    'name' => $spinofName,
                    'url' => $this->context->link->getProductLink((int) $spinof['id_spinoff']),
                ];
            }
        }

        return $spinOffLinks;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Spin Off Sitemap', [], 'Modules.Chatgptcontentgenerator.Sitemap'),
            'url' => $this->context->link->getModuleLink('chatgptcontentgenerator', 'productssitemap'),
        ];

        return $breadcrumb;
    }
}
