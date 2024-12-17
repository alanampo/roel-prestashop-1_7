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

use PrestaShop\Module\Chatgptcontentgenerator\Api\Client as ApiClient;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentPost;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentTemplate;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptSpinoffConnections;
use PrestaShop\Module\Chatgptcontentgenerator\Hooks\AdapterHooks;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

require_once dirname(__FILE__) . '/vendor/autoload.php';

class Chatgptcontentgenerator extends Module
{
    protected $config_form = false;

    private $container;
    private $config_prefix;

    public static $isUpdateStock = true;

    public function __construct()
    {
        $this->name = 'chatgptcontentgenerator';
        $this->tab = 'administration';
        $this->version = '1.1.3';
        $this->author = 'SoftSprint';
        $this->need_instance = 0;

        $this->bootstrap = true;

        $this->module_key = '1f440eb08736d74b883b0e891da486d9';

        parent::__construct();

        $this->displayName = $this->l('ChatGPT Content Generator + Blog');
        $this->description = $this->l('ChatGPT Content Generator + Blog');

        $this->confirmUninstall = $this->l('Are you sure wand uninstall this module ?');

        $this->ps_versions_compliancy = [
            'min' => '1.7.5',
            'max' => _PS_VERSION_,
        ];

        if ($this->container === null) {
            $this->container = new \PrestaShop\Module\Chatgptcontentgenerator\Service\ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }

        $this->config_prefix = Tools::strtoupper($this->name) . '_';
    }

    public function install()
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $mboStatus = (new \Prestashop\ModuleLibMboInstaller\Presenter())->present();
            if (!$mboStatus['isInstalled']) {
                try {
                    $mboInstaller = new \Prestashop\ModuleLibMboInstaller\Installer(_PS_VERSION_);
                    $mboInstaller->installModule();
                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                    return false;
                }
            }
        }

        $result = parent::install()
            && $this->installSql();

        if ($result) {
            $registerHooks = AdapterHooks::getHooks($this)->getRegisterHooks();
            foreach ($registerHooks as $hookName) {
                $this->registerHook($hookName);
            }
        }

        if ($result) {
            Configuration::updateValue('CHATGPTSPINOFF_VISIBILITY', 'none');
            Configuration::updateValue('CHATGPTSPINOFF_SITEMAP_INSERT', false);
            Configuration::updateValue('CHATGPTSPINOFF_STOCK', 1);
            Configuration::updateValue('CHATGPTSPINOFF_SHOW_SPINOFF_PRODUCTS', true);
            Configuration::updateValue('CHATGPTSPINOFF_HIDE_PERIOD_DAYS', 10);

            $idParentCatalog = (int) Db::getInstance()->getValue(
                'SELECT id_tab FROM ' . _DB_PREFIX_ . 'tab WHERE class_name =\'AdminCatalog\''
            );

            $this->installTabs(
                [
                    [
                        'visible' => true,
                        'class_name' => 'AdminChatGtpContentAjax',
                        'name' => $this->trans(
                            'ChatGPT Content Ajax',
                            [],
                            'Modules.Chatgptcontentgenerator.Admin'
                        ),
                        'id_parent' => -1,
                        'icon' => null,
                    ],
                    [
                        'visible' => false,
                        'class_name' => 'AdminChatGtpPostAjax',
                        'name' => 'ChatGPT Post Ajax',
                        'id_parent' => -1,
                        'icon' => null,
                    ],
                    [
                        'visible' => false,
                        'class_name' => 'AdminChatGtpFilesAjax',
                        'name' => 'ChatGPT Files Ajax',
                        'id_parent' => -1,
                        'icon' => null,
                    ],
                    [
                        'visible' => true,
                        'class_name' => 'AdminChatGtpContentBlog',
                        'name' => $this->trans(
                            'ChatGPT blog',
                            [],
                            'Modules.Chatgptcontentgenerator.Admin'
                        ),
                        'id_parent' => 0,
                        'icon' => null,
                    ],
                    [
                        'visible' => true,
                        'class_name' => 'AdminChatGtpContentBlogPost',
                        'name' => $this->trans(
                            'Posts by ChatGPT',
                            [],
                            'Modules.Chatgptcontentgenerator.Admin'
                        ),
                        'parent_class_name' => 'AdminChatGtpContentBlog',
                        'icon' => null,
                    ],
                    [
                        'visible' => true,
                        'class_name' => 'AdminChatGtpContentBlogSettings',
                        'name' => $this->trans(
                            'Blog settings',
                            [],
                            'Modules.Chatgptcontentgenerator.Admin'
                        ),
                        'parent_class_name' => 'AdminChatGtpContentBlog',
                        'icon' => null,
                    ],
                    [
                        'visible' => false,
                        'class_name' => 'AdminChatGtpTemplate',
                        'name' => $this->trans(
                            'ChatGPT Templates',
                            [],
                            'Modules.Chatgptcontentgenerator.Admin'
                        ),
                        'id_parent' => $idParentCatalog,
                        'icon' => null,
                    ],
                    [
                        'visible' => false,
                        'class_name' => 'AdminChatGtpSpinOffAjax',
                        'name' => 'ChatGPT Spin Off',
                        'id_parent' => -1,
                        'icon' => null,
                    ],
                    [
                        'visible' => true,
                        'class_name' => 'AdminChatGtpSpinOff',
                        'name' => $this->trans(
                            'Spin-offs',
                            [],
                            'Modules.Chatgptcontentgenerator.Admin'
                        ),
                        'id_parent' => $idParentCatalog,
                        'icon' => null,
                    ],
                ]
            );

            $this->setConfigGlobal('BLOG_POSTS_PER_PAGE', 9);
            $this->setConfigGlobal('BLOG_MAIN_TITLE', 'Blog');
            $this->setConfigGlobal('BLOG_DISPLAY_DATE', true);
            $this->setConfigGlobal('BLOG_DISPLAY_FEATURED', true);
            $this->setConfigGlobal('BLOG_DISPLAY_RELATED', true);
            $this->setConfigGlobal('BLOG_DISPLAY_THUMBNAIL', true);
            $this->setConfigGlobal('BLOG_DISPLAY_DESCRIPTION', true);
            $this->setConfigGlobal('BLOG_DISPLAY_POST_ON_PRODUCT_PAGE', true);

            $this->setConfigGlobal('BLOG_THUMB_X', 600);
            $this->setConfigGlobal('BLOG_THUMB_Y', 300);
        }

        if ($result) {
            $result = $this->installDependencies();
        }

        return $result;
    }

    public function uninstall()
    {
        $result = parent::uninstall();

        if ($result) {
            $this->deleteSpinOffProducts();
            $this->uninstallSql();

            $this->deleteConfig('SHOP_ASSOCIATED');
            $this->deleteConfig('SHOP_UID');
            $this->deleteConfig('SHOP_TOKEN');
            $this->deleteConfig('USE_PRODUCT_BRAND');
            $this->deleteConfig('USE_PRODUCT_CATEGORY');

            (new Tab(Tab::getIdFromClassName('AdminChatGtpSpinOff')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpSpinOffAjax')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpTemplate')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpContentBlogSettings')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpContentBlogPost')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpContentBlog')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpFilesAjax')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpPostAjax')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpContentAjax')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpSpinOff')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpSpinOff')))->delete();
            (new Tab(Tab::getIdFromClassName('AdminChatGtpSpinOff')))->delete();
        }

        return $result;
    }

    private function installDependencies()
    {
        try {
            $moduleManager = ModuleManagerBuilder::getInstance()->build();
            // CloudSync
            if (!$moduleManager->isInstalled('ps_eventbus')) {
                $moduleManager->install('ps_eventbus');
            } elseif (!$moduleManager->isEnabled('ps_eventbus')) {
                $moduleManager->enable('ps_eventbus');
                $moduleManager->upgrade('ps_eventbus');
            } else {
                $moduleManager->upgrade('ps_eventbus');
            }

            // install ps accounts module
            $this->getService('chatgptcontentgenerator.ps_accounts.installer')->install();
        } catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            return false;
        }

        return true;
    }

    protected function deleteSpinOffProducts()
    {
        $allConections = GptSpinoffConnections::getAllConections();

        if ($allConections) {
            foreach ($allConections as $conection) {
                $product = new Product((int) $conection['id_spinoff']);
                if (Validate::isLoadedObject($product)) {
                    if (!$product->delete()) {
                        $this->_errors = 'The product ' . $product->id . ' can not be removed';

                        return false;
                    }
                }
            }
        }

        return true;
    }

    protected function controlSpinOffsVisibility(bool $show)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'product` AS p
                JOIN
                    `' . _DB_PREFIX_ . 'spinoff_connections` AS sp ON p.`id_product` = sp.`id_spinoff`
                SET
                    p.`state` = ' . ($show ? 1 : 0) . ',
                    p.`active` = ' . ($show ? 1 : 0) . ';';
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_shop` AS p
            JOIN
                `' . _DB_PREFIX_ . 'spinoff_connections` AS sp ON p.`id_product` = sp.`id_spinoff`
            SET
                p.`active` = ' . ($show ? 1 : 0) . ';';

        return Db::getInstance()->execute($sql);
    }

    public function hookActionAdminCategoriesListingFieldsModifier($params)
    {
        return $this->getHooksByName('actionAdminCategoriesListingFieldsModifier', $params);
    }

    public function printGeneratedLangs($value, $row)
    {
        return $this->printLangIso($row['gl2']);
    }

    public function printTranslatedLangs($value, $row)
    {
        return $this->printLangIso($row['tl2']);
    }

    private function printLangIso($value)
    {
        $languages = Language::getLanguages();
        $languages = array_combine(array_column($languages, 'id_lang'), array_column($languages, 'iso_code'));
        $langs = explode(',', $value);
        $outout = [];
        foreach ($langs as $id) {
            if (isset($languages[$id])) {
                $outout[] = strtoupper($languages[$id]);
            }
        }
        return implode(', ', $outout);
    }

    public function hookActionCategoryGridDefinitionModifier($params)
    {
        return $this->getHooksByName('actionCategoryGridDefinitionModifier', $params);
    }

    public function hookActionCategoryGridDataModifier($params)
    {
        return $this->getHooksByName('actionCategoryGridDataModifier', $params);
    }

    public function hookActionCategoryGridQueryBuilderModifier($params)
    {
        return $this->getHooksByName('actionCategoryGridQueryBuilderModifier', $params);
    }

    public function hookActionAdminProductsListingResultsModifier($params)
    {
        return $this->getHooksByName('actionAdminProductsListingResultsModifier', $params);
    }

    public function hookActionAdminProductsListingFieldsModifier($params)
    {
        return $this->getHooksByName('actionAdminProductsListingFieldsModifier', $params);
    }

    public function hookActionAdminControllerSetMedia()
    {
        return $this->getHooksByName('actionAdminControllerSetMedia');
    }

    public function hookActionProductGridDefinitionModifier($params)
    {
        return $this->getHooksByName('actionProductGridDefinitionModifier', $params);
    }

    public function hookActionProductGridQueryBuilderModifier($params)
    {
        return $this->getHooksByName('actionProductGridQueryBuilderModifier', $params);
    }

    public function hookActionAfterUpdateCombinationListFormHandler(array $params)
    {
        $this->getHooksByName('actionAfterUpdateCombinationListFormHandler', $params);
    }

    public function getContent()
    {
        $this->postProcess();

        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        $accountsService = null;

        $accountsInstaller = $this->getService('chatgptcontentgenerator.ps_accounts.installer');

        if ($accountsInstaller->checkModuleVersion() == false) {
            $modulesInstaller = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()
                ->get('prestashop.module.manager');
            if (!$modulesInstaller) {
                $modulesInstaller = $moduleManager;
            }

            try {
                if (!$modulesInstaller->isInstalled('ps_accounts')) {
                    $this->installDependencies();
                } elseif (!$modulesInstaller->isEnabled('ps_accounts')) {
                    $modulesInstaller->enable('ps_accounts');
                    $modulesInstaller->upgrade('ps_accounts');
                } else {
                    $modulesInstaller->upgrade('ps_accounts');
                }

                if ($modulesInstaller->isInstalled('ps_accounts') == false) {
                    $this->context->smarty->assign(
                        'error_message',
                        'The module "PrestaShop Account" is not installed please install this module manually.'
                    );
                    return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/errors.tpl');
                }

                Tools::redirect(
                    $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name])
                );
            } catch (\PrestaShop\PrestaShop\Core\Addon\Module\Exception\UnconfirmedModuleActionException $e) {
                $this->context->smarty->assign(
                    'error_message',
                    'Please upgrade the module PrestaShop Account to use the "' . $this->displayName . '"'
                );
                return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/errors.tpl');
            } catch (\PrestaShop\PsAccountsInstaller\Installer\Exception\InstallerException $e) {
                $this->context->smarty->assign('error_message', $e->getMessage());
                return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/errors.tpl');
            } catch (\Throwable $e) {
                $this->context->smarty->assign(
                    'error_message',
                    'Please upgrade the module "PrestaShop Account" to use the "' . $this->displayName . '"'
                );
                return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/errors.tpl');
            }
        }

        try {
            $accountsFacade = $this->getService('chatgptcontentgenerator.ps_accounts.facade');
            $accountsService = $accountsFacade->getPsAccountsService();
        } catch (\PrestaShop\PsAccountsInstaller\Installer\Exception\ModuleVersionException $e) {
            $this->context->smarty->assign('error_message', $e->getMessage() . ' 2');
            return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/errors.tpl');
        } catch (\PrestaShop\PsAccountsInstaller\Installer\Exception\InstallerException $e) {
            $accountsInstaller = $this->getService('chatgptcontentgenerator.ps_accounts.installer');
            $accountsInstaller->install();
            $accountsFacade = $this->getService('chatgptcontentgenerator.ps_accounts.facade');
            $accountsService = $accountsFacade->getPsAccountsService();
        }

        try {
            Media::addJsDef([
                'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                    ->present($this->name),
            ]);

            $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();
            return '';
        }

        if ($moduleManager->isInstalled('ps_eventbus')) {
            $eventbusModule = Module::getInstanceByName('ps_eventbus');
            if (version_compare($eventbusModule->version, '1.9.0', '>=')) {
                $eventbusPresenterService = $eventbusModule
                    ->getService('PrestaShop\Module\PsEventbus\Service\PresenterService');

                $this->context->smarty->assign(
                    'urlCloudsync',
                    'https://assets.prestashop3.com/ext/cloudsync-merchant-sync-consent/latest/cloudsync-cdc.js'
                );

                $entitites = ['info', 'modules', 'themes', 'orders'];
                Media::addJsDef([
                    'contextPsEventbus' => $eventbusPresenterService->expose($this, $entitites),
                ]);
            }
        }

        $billingFacade = $this->getService('chatgptcontentgenerator.ps_billings.facade');
        $partnerLogo = $this->getLocalPath() . 'logo.png';

        Media::addJsDef($billingFacade->present([
            'logo' => $partnerLogo,
            'tosLink' => 'https://saas.softsprint.net/terms-and-conditions-of-use.html',
            'privacyLink' => 'https://saas.softsprint.net/terms-and-conditions-of-use.html',
            'emailSupport' => 'support@softsprint.net',
        ]));

        $shopId = $this->getShopKeyId();

        $isShopAssociated = (bool) $this->getConfigGlobal('SHOP_ASSOCIATED');
        if ($isShopAssociated) {
            $isShopAssociated = $shopId == $this->getConfigGlobal('SHOP_UID');
        }

        $subscription = $this->getService('chatgptcontentgenerator.ps_billings.service')->getCurrentSubscription();

        $countSpinOffs = GptSpinoffConnections::countAllSpinOffs();
        $shopInfo = $this->getShopInfo();

        Media::addJsDef([
            'hasSubscription' => ($subscription && $subscription['success'] == true),
            'shopInfo' => false,
            'isShopAssociated' => $isShopAssociated,
            'gptAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpContentAjax'),
            'backendEndpointUrl' => $this->context->link->getAdminLink('AdminChatGtpContentAjax'),
            'noRecordsText' => $this->trans('No records found', [], 'Modules.Chatgptcontentgenerator.Admin'),
            // cookie
            'cookieQuotaLimit' => (int) $this->context->cookie->gptc_quota_limit,

            'gptAutoOpenPlans' => (int) Tools::getValue('openplans', 0),

            'countSpinOffs' => $countSpinOffs,
            'alowSpinOffsManagment' => $this->trans('<p><b>Allow spin-offs management:</b></p>
                <ul>
                    <li>allows you to keep the spin-off product pages active;</li>
                    <li>show spin-off pages to store visitors on the front-end
                    (otherwise they will be hidden and redirected to the parent product page);</li>
                    <li>display spin-offs in the XML sitemap;</li>
                    <li>allows you to create new spin-off products <b>manually</b> (without ChatGPT generation);</li>
                    <li>ChatGPT generation is prohibited (word limit set to zero);</li>
                </ul>',
                [],
                'Modules.Chatgptcontentgenerator.Admin'
            ),
            'gptShopInfo' => $shopInfo,
            'subscriptionExpiredAlertHtml' => $this->getSubscriptionExpiredAlertHtml($shopInfo),
            'renewOrOrderSubscription' => $this->getRenewOrOrderSubscriptionLink(),
            'curentLanguageLocale' => $this->context->language->locale,
        ]);

        $this->setGptI18nJsVariables();

        $this->context->smarty->assign('urlBilling', 'https://unpkg.com/@prestashopcorp/billing-cdc/dist/bundle.js');
        $this->context->smarty->assign('urlContentTemplate', $this->context->link->getAdminLink('AdminChatGtpTemplate'));
        $this->context->controller->addCss($this->getPathUri() . 'views/css/admin.legacy.css');

        $this->context->smarty->assign('serverParams', AdapterHooks::getHooks($this)->getServerParams());

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl') .
            $this->getConfigurationForm() .
            $this->renderStatisticsList() .
            $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.actions.tpl') .
            $this->getTemplateList() .
            $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.params.tpl');
    }

    public function getShopKeyId()
    {
        $psService = \Module::getInstanceByName('ps_accounts')
            ->getService('PrestaShop\Module\PsAccounts\Service\PsAccountsService');

        if (method_exists($psService, 'getShopUuid')) {
            return $psService->getShopUuid();
        }

        return $psService->getShopUuidV4();
    }

    private function renderStatisticsList()
    {
        $fields_list = [
            'name' => [
                'title' => $this->trans('Plan name', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'text',
            ],
            'status' => [
                'title' => $this->trans('Status', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'text',
                'class' => 'text-center',
            ],
            'deadline' => [
                'title' => $this->trans('Deadline', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'text',
                'class' => 'text-center',
            ],
            'productWords' => [
                'title' => $this->trans('Product words', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'text',
                'class' => 'text-center',
            ],
            'categoryWords' => [
                'title' => $this->trans('Category words', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'text',
                'class' => 'text-center',
            ],
            'pageWords' => [
                'title' => $this->trans('Pages, Spin-offs & Blog words', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'text',
                'class' => 'text-center',
            ],
            'spinoffs' => [
                'title' => $this->trans('Spin-offs', [], 'Modules.Chatgptcontentgenerator.Admin') .
                    '<span class="spin-offs_management_tooltip"><i class="spin-offs_management"></i></span>',
                'type' => 'text',
                'class' => 'text-center',
            ],
            'customRequest' => [
                'title' => $this->trans('Allow custom requests', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'type' => 'bool',
                'class' => 'text-center',
            ],
        ];

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->no_link = true;
        $helper->actions = [];
        $helper->show_toolbar = true;
        $helper->toolbar_btn = [];
        $helper->module = $this;
        $helper->identifier = 'id_plan';
        $helper->title = $this->trans('Use of tariff features', [], 'Modules.Chatgptcontentgenerator.Admin');
        $helper->table = 'subscription-plan-used-limits';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateList([], $fields_list);
    }

    private function getTemplateList()
    {
        $fields_list = [
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
                'type' => 'text',
                'search' => false,
            ],
            'type' => [
                'title' => $this->trans('Type', [], 'Admin.Global'),
                'type' => 'text',
                'search' => false,
            ],
            'langs' => [
                'title' => $this->trans('Languages', [], 'Admin.Global'),
                'type' => 'text',
                'search' => false,
            ],
            'active' => [
                'title' => $this->trans('Status', [], 'Admin.Global'),
                'type' => 'bool',
                'active' => 'status',
                'class' => 'text-center',
                'search' => false,
            ],
        ];

        $token = Tools::getAdminTokenLite('AdminChatGtpTemplate');

        $helper = new HelperList();
        $helper->list_id = 'content_template';
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->identifier = 'id_content_template';
        $helper->title = $this->trans('Templates', [], 'Modules.Chatgptcontentgenerator.Admin');
        $helper->table = 'content_template';
        $helper->table_id = 'table-content_template';
        $helper->token = $token;
        $helper->currentIndex = 'index.php?controller=AdminChatGtpTemplate';
        $helper->no_link = true;

        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminChatGtpTemplate', true, [], ['add' . $helper->table => true]),
            'desc' => $this->trans('Add new', [], 'Admin.Actions'),
        ];

        $page = ($page = Tools::getValue('submitFilter' . $helper->list_id)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper->list_id . '_pagination')) ? $pagination : $helper->_default_pagination;

        $templates = GptContentTemplate::getTemplates((int) $page, (int) $pagination);
        $helper->listTotal = (int) GptContentTemplate::getTemplatesTotal();

        return $helper->generateList($templates, $fields_list);
    }

    public function getConfigurationForm()
    {
        $visibilityOptions = [
            ['id_option' => 'both', 'name' => $this->trans('Everywhere', [], 'Modules.Chatgptcontentgenerator.Admin')],
            ['id_option' => 'catalog', 'name' => $this->trans('Catalog only', [], 'Modules.Chatgptcontentgenerator.Admin')],
            ['id_option' => 'search', 'name' => $this->trans('Search only', [], 'Modules.Chatgptcontentgenerator.Admin')],
            ['id_option' => 'none', 'name' => $this->trans('Nowhere', [], 'Modules.Chatgptcontentgenerator.Admin')],
        ];

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Spin-off General settings', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->trans('Spin-off products visibility:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'desc' => $this->trans('Where spin-off product pages will be displayed to store visitors', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'name' => 'spioffs_visibility_settings',
                        'required' => true,
                        'options' => [
                            'query' => $visibilityOptions,
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('New spin-off products stock:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                        'name' => 'spinoffs_stock_setting',
                        'values' => [
                            [
                                'id' => 'spioffs_stock_on',
                                'value' => GptSpinoffConnections::SPINOFF_STOCK_COMMON,
                                'label' => $this->trans('Common', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            ],
                            [
                                'id' => 'spioffs_stock_off',
                                'value' => GptSpinoffConnections::SPINOFF_STOCK_INDIVIDUAL,
                                'label' => $this->trans('Individual', [], 'Modules.Chatgptcontentgenerator.Admin'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Global'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitConfigurations',
                ],
            ],
        ];

        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        if ($moduleManager->isInstalled('gsitemap') && Module::isEnabled('gsitemap')) {
            $fields_form['form']['input'][] = [
                'type' => 'switch',
                'label' => $this->trans('Add spin-offs to sitemap XML:', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'name' => 'spinoffs_sitemap_setting',
                'is_bool' => true,
                'desc' => $this->trans('If disabled, the spin-offs will not be added to the sitemap XML.', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'values' => [
                    [
                        'id' => 'sitemap_spinoffs_on',
                        'value' => 1,
                        'label' => $this->trans('Enabled', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    ],
                    [
                        'id' => 'sitemap_spinoffs_off',
                        'value' => 0,
                        'label' => $this->trans('Disabled', [], 'Modules.Chatgptcontentgenerator.Admin'),
                    ],
                ],
            ];
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'gpt_configuration';
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
            ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
            : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfigurations';
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false,
            [],
            ['configure' => $this->name, 'tab_module' => $this->tab, 'module_name' => $this->name]
        );
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => [
                'spioffs_visibility_settings' => Configuration::get('CHATGPTSPINOFF_VISIBILITY'),
                'spinoffs_sitemap_setting' => Configuration::get('CHATGPTSPINOFF_SITEMAP_INSERT'),
                'spinoffs_stock_setting' => Configuration::get('CHATGPTSPINOFF_STOCK'),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    private function postProcess()
    {
        if (Tools::isSubmit('submitConfigurations')) {
            $this->setConfigGlobal('USE_PRODUCT_CATEGORY', (int) $this->getValue('USE_PRODUCT_CATEGORY'));
            $this->setConfigGlobal('USE_PRODUCT_BRAND', (int) $this->getValue('USE_PRODUCT_BRAND'));

            $spioffs_visibility_settings = Tools::getValue('spioffs_visibility_settings');

            if ($spioffs_visibility_settings && Validate::isProductVisibility($spioffs_visibility_settings)) {
                Configuration::updateValue('CHATGPTSPINOFF_VISIBILITY', $spioffs_visibility_settings);

                $spinoffsIds = GptSpinoffConnections::getAllConections();

                $tables = [
                    'product',
                    'product_shop',
                ];

                foreach ($tables as $table) {
                    $sql = 'UPDATE `' . _DB_PREFIX_ . $table . '`
                        SET visibility = \'' . $spioffs_visibility_settings . '\'
                        WHERE id_product
                        IN (' . implode(', ', array_column($spinoffsIds, 'id_spinoff')) . ');';

                    Db::getInstance()->execute($sql);
                }
            } else {
                $this->errors[] = $this->trans('Error - visibility setting not set.', [], 'Modules.Chatgptcontentgenerator.Admin');
            }

            if (Tools::isSubmit('spinoffs_sitemap_setting')) {
                Configuration::updateValue('CHATGPTSPINOFF_SITEMAP_INSERT', Tools::getValue('spinoffs_sitemap_setting'));
            }

            if (Tools::isSubmit('spinoffs_stock_setting')) {
                Configuration::updateValue('CHATGPTSPINOFF_STOCK', Tools::getValue('spinoffs_stock_setting'));
            }

            $this->_confirmations[] = $this->trans('The settings has been updated successfully', [], 'Modules.Chatgptcontentgenerator.Admin');
        }
    }

    public function getService($serviceName)
    {
        return $this->container->getService($serviceName);
    }

    public function installTabs($tabs)
    {
        $install_success = true;
        $obj = new Tab();

        foreach ($tabs as $tab_config) {
            if ($obj->getIdFromClassName($tab_config['class_name'])) {
                continue;
            }

            $tab = new Tab();
            $tab->class_name = $tab_config['class_name'];
            $tab->active = isset($tab_config['visible']) ? $tab_config['visible'] : true;

            foreach (Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = $tab_config['name'];
            }

            if (isset($tab_config['id_parent'])) {
                $tab->id_parent = (int) $tab_config['id_parent'];
            } else {
                $tab->id_parent = $obj->getIdFromClassName($tab_config['parent_class_name']);
            }

            $tab->module = $this->name;
            $tab->icon = isset($tab_config['icon']) ? $tab_config['icon'] : '';

            Db::getInstance()->execute(
                'DELETE FROM ' . _DB_PREFIX_ . 'authorization_role ' .
                'WHERE `slug` LIKE \'%' . pSql(strtoupper($tab_config['class_name'])) . '%\''
            );

            if (!$tab->add()) {
                $install_success = false;
            }
        }

        return $install_success;
    }

    public function hookModuleRoutes($params)
    {
        $base = 'blog';

        $routes = [
            'module-' . $this->name . '-bloghome' => [
                'controller' => 'bloghome',
                'rule' => $base,
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
            'module-' . $this->name . '-bloghomepage' => [
                'controller' => 'bloghome',
                'rule' => $base . '/page/{p}',
                'keywords' => [
                    'p' => ['regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'p'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
            'module-' . $this->name . '-blogpost' => [
                'controller' => 'blogpost',
                'rule' => $base . '/{rewrite}',
                'keywords' => [
                    'rewrite' => ['regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'rewrite'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
        ];

        return $routes;
    }

    public function jsonResponse($data = null)
    {
        if (is_array($data) || is_null($data)) {
            $data = array_merge(['success' => true], $data ?? []);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function errorResponse($code = 500, $message = 'Error message default')
    {
        $error = ['code' => $code, 'message' => $message, 'status' => ''];

        if ($code == 18) {
            $error['status'] = 'quota_over';
        }
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $error,
        ]);
        exit;
    }

    public function jsonExeptionResponse(Exception $e)
    {
        return $this->errorResponse($e->getCode(), $e->getMessage());
    }

    private function installSql()
    {
        $sqlInstaller = new \PrestaShop\Module\Chatgptcontentgenerator\Sql\Installer();
        return $sqlInstaller->install();
    }

    private function uninstallSql()
    {
        $sqlInstaller = new \PrestaShop\Module\Chatgptcontentgenerator\Sql\Installer();
        return $sqlInstaller->uninstall();
    }

    public function getConfig($key, $id_lang = null, $id_shop_group = null, $id_shop = null, $default = false)
    {
        return Configuration::get($this->config_prefix . $key, $id_lang, $id_shop_group, $id_shop, $default);
    }

    public function getConfigGlobal($key, $idLang = null, $default = false)
    {
        return Configuration::get($this->config_prefix . $key, $idLang, 0, 0, $default);
    }

    public function setConfig($key, $values, $html = false, $id_shop_group = null, $id_shop = null)
    {
        return Configuration::updateValue($this->config_prefix . $key, $values, $html, $id_shop_group, $id_shop);
    }

    public function setConfigGlobal($key, $value, $html = false)
    {
        return Configuration::updateGlobalValue($this->config_prefix . $key, $value, $html);
    }

    public function deleteConfig($key)
    {
        return Configuration::deleteByName($this->config_prefix . $key);
    }

    public function getValue($key, $default_value = false)
    {
        return Tools::getValue($this->config_prefix . $key, $default_value);
    }

    public function isSubmit($key)
    {
        return Tools::isSubmit($this->config_prefix . $key);
    }

    public function hookActionProductDelete($params)
    {
        $id_spinoff = $params['id_product'];

        GptSpinoffConnections::deleteConectionsBySpinOffId($id_spinoff);
    }

    public function hookActionObjectProductAddAfter($params)
    {
        $this->hookActionObjectProductUpdateAfter($params);
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        if (isset($params['object']) && !empty($params['object']->id) && Tools::isSubmit('spinoff_stock')) {
            $spinOffConnections = null;
            $spinOff = GptSpinoffConnections::getConectionsBySpinOffId($params['object']->id);

            if (!empty($spinOff['id'])) {
                $spinOffConnections = new GptSpinoffConnections($spinOff['id']);
            }

            if (Validate::isLoadedObject($spinOffConnections)) {
                $spinOffConnections->stock = (int) Tools::getValue('spinoff_stock');
                $spinOffConnections->update();
            }
        }
    }

    public function hookActionUpdateQuantity($params)
    {
        if (self::$isUpdateStock) {
            $product_list = [];

            if (
                Tools::isSubmit('spinoff_stock')
                && (int) Tools::getValue('spinoff_stock') == GptSpinoffConnections::SPINOFF_STOCK_COMMON
            ) {
                $spinoffConnections = GptSpinoffConnections::getConectionsBySpinOffId($params['id_product']);
                if ($spinoffConnections && !empty($spinoffConnections['id_product'])) {
                    $product = new Product($spinoffConnections['id_product']);
                    if ($productCombinations = $product->getAttributeCombinations()) {
                        foreach ($productCombinations as $combination) {
                            $product_list[] = [
                                'product_id' => $combination['id_product'],
                                'product_attribute_id' => $combination['id_product_attribute'],
                                'quantity' => $combination['quantity'],
                            ];
                        }
                    } else {
                        $product_list[] = [
                            'product_id' => $product->id,
                            'product_attribute_id' => 0,
                        ];
                    }
                }
            } else {
                $product_list[] = [
                    'product_id' => $params['id_product'],
                    'product_attribute_id' => $params['id_product_attribute'],
                    'quantity' => $params['quantity'],
                ];
            }

            if ($product_list) {
                GptSpinoffConnections::updateStockByProductList($product_list, $this->context->language->id);
            }
        }
    }

    public function getShopInfo()
    {
        try {
            $shopInfo = (new ApiClient($this->getConfigGlobal('SHOP_UID')))
                ->setToken($this->getConfigGlobal('SHOP_TOKEN'))
                ->setModule($this)
                ->getShopInfo()
            ;
        } catch (Exception $e) {
            $shopInfo = [];
        }

        return $shopInfo;
    }

    public function getRenewOrOrderSubscriptionLink()
    {
        return $this->trans(
            '%opentag%Renew / Order plan%closetag%',
            [
                '%opentag%' => '<a class="gpt-reneworder-link" href="' . $this->getRenewUrl() . '">',
                '%closetag%' => '</a>',
            ],
            'Modules.Chatgptcontentgenerator.Admin'
        );
    }

    public function getSubscriptionExpiredMessage($shopInfo = null, $showAlertAfterDeadline = false)
    {
        if (!$shopInfo) {
            $shopInfo = $this->getShopInfo();
        }

        $deadLine = $this->checkSubscriptionDeadline($shopInfo);

        if ($deadLine
            && isset($deadLine['daysLeft'])
            && $deadLine['daysLeft'] >= 0
            && $deadLine['daysLeft'] <= Configuration::get('CHATGPTSPINOFF_HIDE_PERIOD_DAYS')
        ) {
            return $this->trans(
                '<b>Attention!</b><br>
                If you do not order any subscription plan
                (even "Basic" tariff is enough), the spin-off products and posts blog will be automatically
                disabled after:<br>
                <b>%s days</b>',
                ['%s' => $deadLine['daysLeft']],
                'Modules.Chatgptcontentgenerator.Admin'
            );
        } elseif ($deadLine
            && isset($deadLine['daysLeft']) && $deadLine['daysLeft'] < 0
            && ($this->context->controller && $this->context->controller instanceof AdminModulesController
            || $showAlertAfterDeadline === true)
        ) {
            return $this->trans(
                '<b>Attention!</b><br>
                Your subscription plan has expired!',
                [],
                'Modules.Chatgptcontentgenerator.Admin'
            );
        } elseif (
            (empty($shopInfo) && $this->getConfigGlobal('SHOP_UID'))
            || (
                $shopInfo
                && $shopInfo['subscription'] == false
                && empty($shopInfo['lastSubscription'])
                && $showAlertAfterDeadline
            )
        ) {
            return $this->trans(
                '<b>Attention!</b><br>
                Please order subscription plan!',
                [],
                'Modules.Chatgptcontentgenerator.Admin'
            );
        }

        return false;
    }

    public function getSubscriptionExpiredAlertHtml($shopInfo = null, $showAlertAfterDeadline = false, $subscriptionExpiredMessage = false)
    {
        if (!$subscriptionExpiredMessage) {
            $subscriptionExpiredMessage = $this->getSubscriptionExpiredMessage($shopInfo, $showAlertAfterDeadline);
        }

        if ($subscriptionExpiredMessage) {
            $this->context->smarty->assign([
                'subscriptionExpiredMessage' => $subscriptionExpiredMessage,
                'renewOrOrderSubscription' => $this->getRenewOrOrderSubscriptionLink(),
            ]);

            return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/admin/alert.deadline.tpl');
        }

        return false;
    }

    public function checkSubscriptionDeadline($shopInfo = null)
    {
        if (!$shopInfo) {
            $shopInfo = $this->getShopInfo();
        }

        if (
            isset($shopInfo['lastSubscription'])
            && $shopInfo['lastSubscription']
        ) {
            $curentDate = new DateTime();
            $endDate = new DateTime($shopInfo['lastSubscription']['endDate']);

            if ($curentDate < $endDate) {
                return false;
            }

            $interval = $curentDate->diff($endDate);
            $daysLeft = 0;

            if ($interval->d > Configuration::get('CHATGPTSPINOFF_HIDE_PERIOD_DAYS')) {
                if (Configuration::get('CHATGPTSPINOFF_SHOW_SPINOFF_PRODUCTS') == true) {
                    if ($this->controlSpinOffsVisibility(false)) {
                        Configuration::updateValue('CHATGPTSPINOFF_SHOW_SPINOFF_PRODUCTS', false);
                    }
                }
            } else {
                if (Configuration::get('CHATGPTSPINOFF_SHOW_SPINOFF_PRODUCTS') == false) {
                    if ($this->controlSpinOffsVisibility(true)) {
                        Configuration::updateValue('CHATGPTSPINOFF_SHOW_SPINOFF_PRODUCTS', true);
                    }
                }

                $daysLeft = Configuration::get('CHATGPTSPINOFF_HIDE_PERIOD_DAYS') - $interval->d;
            }

            return [
                'daysLeft' => $daysLeft,
            ];
        }

        return false;
    }

    public function setGptI18nJsVariables($buttonName = '')
    {
        return AdapterHooks::getHooks($this)->setGptJsVariables($buttonName);
    }

    public function getRenewUrl()
    {
        return AdapterHooks::getHooks($this)->getRenewUrl();
    }

    public function hookdisplayFooterProduct($params)
    {
        if ((bool) $this->getConfig('BLOG_DISPLAY_POST_ON_PRODUCT_PAGE', null, null, null, false)) {
            $id_category_product = $params['product']['id_category_default'];

            $result = GptContentPost::getPosts($this->context->language->id, 3, null, null, true, false, false, null, false, null, 'id_product = ', [$params['product']['id']], "id_gptcontent_post_category = $id_category_product");

            if (empty($result['posts'])) {
                return;
            }

            $this->smarty->assign([
                'recents_posts' => $result['posts'],
                'is_shortdescription' => (bool) $this->getConfig('BLOG_DISPLAY_DESCRIPTION', null, null, null, false),
                'is_display_time' => (bool) $this->getConfig('BLOG_DISPLAY_DATE', null, null, null, false),
                'is_cover' => (bool) $this->getConfig('BLOG_DISPLAY_FEATURED', null, null, null, false),
                'title' => $this->trans('Related posts', [], 'Modules.Chatgptcontentgenerator.Blogpost'),
            ]);

            return $this->fetch('module:chatgptcontentgenerator/views/templates/front/recents-posts.tpl');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ($this->context->controller instanceof ProductController) {
            $this->context->controller->registerStylesheet(
                'front-' . $this->name,
                '/modules/' . $this->name . '/views/css/front.css',
                [
                    'media' => 'all',
                    'priority' => 990,
                ]
            );
        }
    }

    private function getHooksByName($hookName, $params = [])
    {
        $chatGptHooks = AdapterHooks::getHooks($this);
        $methodName = '_hook' . ucfirst($hookName);

        if (method_exists($chatGptHooks, $methodName)) {
            return $chatGptHooks->{$methodName}($params);
        } else {
            return null;
        }
    }
}
