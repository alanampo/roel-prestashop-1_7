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

namespace PrestaShop\Module\Chatgptcontentgenerator\Hooks;

use PrestaShop\Module\Chatgptcontentgenerator\Api\Client as ApiClient;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentTemplate;
use PrestaShop\Module\Chatgptcontentgenerator\Entity\GptSpinoffConnections;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractHooks
{
    protected $isNewVersion = true;
    protected $translator;
    protected $context;
    protected $module;
    private $request;

    protected static $entities = [];

    public function __construct(\Chatgptcontentgenerator $module)
    {
        $this->module = $module;
        $this->context = \Context::getContext();
        $this->translator = $this->context->getTranslator();
    }

    abstract protected function getAdminPageName(): array;

    abstract protected function getProductId(): int;

    abstract protected function getMediaPatchForVersion(): string;

    public function getRegisterHooks(): array
    {
        return [
            'actionAdminControllerSetMedia',
            'actionCategoryGridQueryBuilderModifier',
            'actionCategoryGridDefinitionModifier',
            'moduleRoutes',
            'actionProductDelete',
            'actionObjectProductAddAfter',
            'actionObjectProductUpdateAfter',
            'displayFooterProduct',
            'actionFrontControllerSetMedia',
            'actionUpdateQuantity',
        ];
    }

    protected function getRequest()
    {
        if (empty($this->request)) {
            $this->request = $this->module->get('request_stack')->getMasterRequest();
        }

        return $this->request;
    }

    public function _hookActionAdminControllerSetMedia()
    {
        $module = $this->module;

        \Media::addJsDef([
            'gptApiHost' => ApiClient::getApiHostUrl(),
            'gptModuleVersion' => $this->module->version,
            'gptSiteVersion' => _PS_VERSION_,
            'gptServerIp' => ApiClient::getServerIp(),
            'gptPatchVersion' => $this->getMediaPatchForVersion(),
        ]);

        if ($this->context->controller && $this->context->controller instanceof \AdminModulesController) {
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.content.js');
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.modal.js');
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.forms.js');
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.module.js');
            $this->context->controller->addCss($module->getPathUri() . 'views/css/back.css');

            return;
        }

        $isLegacyController = false;
        list($adminPageName, $adminPageId) = $this->getAdminPageName();

        $buttonName = '';

        if ($adminPageName == 'productsList') {
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.bulkactions.js');
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.entities.list.js');
            $this->context->controller->addJs($module->getPathUri() . 'views/js/' . $this->getMediaPatchForVersion() . '/admin.entities.list.js');
            $this->context->controller->addCss($module->getPathUri() . 'views/css/' . $this->getMediaPatchForVersion() . '/admin.entities.list.css');
            \Media::addJsDef([
                'catalogProductsList' => self::$entities,
                'gptHomeCategory' => (int) \Configuration::get('PS_HOME_CATEGORY'),
                'columnGeneratedLangs' => isset($this->context->cookie->filter_column_product_generated_langs)
                    ? explode(',', (string) $this->context->cookie->filter_column_product_generated_langs)
                    : false,
                'columnTranslatedLangs' => isset($this->context->cookie->filter_column_product_translated_langs)
                    ? explode(',', (string) $this->context->cookie->filter_column_product_translated_langs)
                    : false,
            ]);
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.content.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.files.uploader.js');
        } elseif ($adminPageName == 'categoriesList') {
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.bulkactions.js');
            $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.entities.list.js');
            $this->context->controller->addJs($module->getPathUri() . 'views/js/' . $this->getMediaPatchForVersion() . '/admin.entities.list.js');
            $this->context->controller->addCss($module->getPathUri() . 'views/css/' . $this->getMediaPatchForVersion() . '/admin.entities.list.css');
            \Media::addJsDef([
                'catalogCategoriesList' => self::$entities,
                'columnGeneratedLangs' => isset($this->context->cookie->filter_column_category_generated_description)
                    ? explode(',', (string) $this->context->cookie->filter_column_category_generated_description)
                    : false,
                'columnTranslatedLangs' => isset($this->context->cookie->filter_column_category_translated_description)
                    ? explode(',', (string) $this->context->cookie->filter_column_category_translated_description)
                    : false,
            ]);
        } elseif ($adminPageName == 'productForm') {
            $productId = $this->getProductId();

            if ($productId) {
                $buttonName = $this->translator->trans(
                    'Generate description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                );

                \Media::addJsDef([
                    'idProduct' => (int) $productId,
                ]);
            }

            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.content.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.files.uploader.js');
        } elseif ($adminPageName == 'categoryForm') {
            if ($adminPageId) {
                $buttonName = $this->translator->trans(
                    'Generate description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                );

                \Media::addJsDef([
                    'idCategory' => (int) $adminPageId,
                ]);
            }
        } elseif ($adminPageName == 'cmsForm') {
            $buttonName = $this->translator->trans(
                'Generate content',
                [],
                'Modules.Chatgptcontentgenerator.Admin'
            );

            \Media::addJsDef([
                'idCms' => (int) $adminPageId,
            ]);
        } elseif ($this->context->controller && $this->context->controller instanceof \AdminChatGtpContentBlogPostController) {
            $isLegacyController = true;
            $adminPageName = 'postForm';
        }

        if ($adminPageName == '') {
            return;
        }

        $shopInfo = $this->module->getShopInfo();

        if (isset($shopInfo['subscription']['manageSpinOffs'])) {
            \Configuration::updateGlobalValue('CHATGPTSPINOFF_MANAGE', $shopInfo['subscription']['manageSpinOffs']);
        } else {
            \Configuration::updateGlobalValue('CHATGPTSPINOFF_MANAGE', false);
        }

        $renewUrl = $this->context->link->getAdminLink(
            'AdminModules',
            true,
            [],
            ['configure' => $this->module->name, 'openplans' => 1]
        );

        \Media::addJsDef([
            'gptLanguages' => \Language::getLanguages(),
            'gptLanguagesIds' => \Language::getLanguages(false, false, true),
            'gptLanguageId' => (int) $this->context->language->id,
            'gptAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpContentAjax'),
            'gptSpinOffAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpSpinOffAjax'),
            'gptPostAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpPostAjax'),
            'gptFilesAjaxUrl' => $this->context->link->getAdminLink('AdminChatGtpFilesAjax'),
            'gptPostEditUrl' => $this->context->link->getAdminLink(
                'AdminChatGtpContentBlogPost',
                true,
                [],
                ['update' . \PrestaShop\Module\Chatgptcontentgenerator\Entity\GptContentPost::$definition['table'] => 1]
            ),
            'gptRenewUrl' => $renewUrl,
            'adminPageName' => $adminPageName,
            'gptShopInfo' => $shopInfo,
            'gptShopUrl' => $this->context->shop->getBaseURL(true),
            'gptShopEmail' => $this->context->employee->email,
            'gptFullName' => trim($this->context->employee->firstname . ' ' . $this->context->employee->lastname),
            'gptServerParams' => $this->getServerParams(),
            'isLegacyController' => $isLegacyController,

            'gptUseProductCategory' => (int) $module->getConfigGlobal('USE_PRODUCT_CATEGORY', null, 1),
            'gptUseProductBrand' => (int) $module->getConfigGlobal('USE_PRODUCT_BRAND', null, 1),

            'gptContentTemplates' => GptContentTemplate::getContentTemplatesByPage($adminPageName, true),

            // cookie
            'cookieQuotaLimit' => (int) $this->context->cookie->gptc_quota_limit,

            'spinOffStockCommon' => GptSpinoffConnections::SPINOFF_STOCK_COMMON,
            'spinOffStockIndividual' => GptSpinoffConnections::SPINOFF_STOCK_INDIVIDUAL,
            'spinOffStock' => (int) \Configuration::get('CHATGPTSPINOFF_STOCK'),
            'subscriptionExpiredAlertHtml' => $this->module->getSubscriptionExpiredAlertHtml($shopInfo),
        ]);

        $this->setGptJsVariables($buttonName, $renewUrl);

        $this->context->controller->addCss($module->getPathUri() . 'views/css/admin.css');
        if ($isLegacyController) {
            $this->context->controller->addCss($this->module->getPathUri() . 'views/css/admin.legacy.css');
        }
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.js');
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.forms.js');
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.content.js');
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.modal.js');
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.module.js');
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.actions.js');
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.custom.request.js');
        $this->context->controller->addJs($module->getPathUri() . 'views/js/admin.translate.js');

        if ($adminPageName == 'productForm' && $productId) {
            $spinOffConection = GptSpinoffConnections::getConectionsBySpinOffId($productId);

            if (!$spinOffConection) {
                $this->context->controller->addJS($module->getPathUri() . '/views/js/admin.spinoff.product.tab.js');
            } else {
                $parentProduct = new \Product($spinOffConection['id_product'], false, $this->context->language->id);

                \Media::addJsDef([
                    'parentProductLink' => $this->context->link->getAdminLink(
                        'AdminProducts',
                        true,
                        ['id_product' => $spinOffConection['id_product']]
                    ),
                    'spinOffProductStock' => $spinOffConection['stock'],
                    'editSpinOffProduct' => $this->translator->trans(
                        'Edit spin-off product',
                        [],
                        'Modules.Chatgptspinoff.Admin'
                    ),
                    'parentProductBlockTitle' => $this->translator->trans(
                        'Parent product',
                        [],
                        'Modules.Chatgptspinoff.Admin'
                    ),
                    'parentProductName' => $parentProduct->name,
                ]);
                $this->context->controller->addJS($module->getPathUri() . '/views/js/' . $this->getMediaPatchForVersion() . '/admin.product.spinoff.js');
            }
        } elseif ($adminPageName == 'productsList') {
            $this->context->controller->addJS($module->getPathUri() . '/views/js/' . $this->getMediaPatchForVersion() . '/admin.productslist.js');
        } elseif ($adminPageName == 'postForm') {
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.content.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.post.js');
            $this->context->controller->addJs($this->module->getPathUri() . 'views/js/admin.files.uploader.js');
        }
    }

    public function getRenewUrl()
    {
        return $this->context->link->getAdminLink(
            'AdminModules',
            true,
            [],
            ['configure' => $this->module->name, 'openplans' => 1]
        );
    }

    public function setGptJsVariables($buttonName, $renewUrl = null)
    {
        if (is_null($renewUrl)) {
            $renewUrl = $this->getRenewUrl();
        }
        $this->setGptI18nJsVariables($buttonName, $renewUrl);

        \Media::addJsDef([
            'gptIsNewVersion' => $this->isNewVersion,
            'gptVarVersion' => [
                'selectors' => $this->getGptVersionSelectors(),
                'contentEditorPreffix' => $this->getContentEditorPreffix(),
            ],
        ]);
    }

    protected function setGptI18nJsVariables($buttonName, $renewUrl = null)
    {
        if (is_null($renewUrl)) {
            $renewUrl = $this->getRenewUrl();
        }

        \Media::addJsDef([
            'gptRenewUrl' => $renewUrl,
            'gptI18n' => [
                'yes' => $this->translator->trans('Yes', [], 'Admin.Global'),
                'no' => $this->translator->trans('No', [], 'Admin.Global'),
                'name' => $this->translator->trans('Name', [], 'Admin.Global'),
                'shortDescription' => $this->translator->trans('Short description', [], 'Admin.Global'),
                'description' => $this->translator->trans('Description', [], 'Admin.Global'),
                'selectAll' => $this->translator->trans('Select all', [], 'Admin.Actions'),
                'languages' => $this->translator->trans('Languages', [], 'Admin.Navigation.Menu'),
                'successMessage' => $this->translator->trans(
                    'A text of %words% words was generated',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'maxLength' => $this->translator->trans(
                    'Max length',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'maxNumberWords' => $this->translator->trans(
                    'Maximum number of words',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'buttonName' => $buttonName,
                'buttonRegenerate' => $this->translator->trans('Regenerate', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'buttonTranslate' => $this->translator->trans('Translate', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'buttonRewrite' => $this->translator->trans('Rewrite', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'buttonSend' => $this->translator->trans('Send', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'buttonCancel' => $this->translator->trans('Cancel', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'buttonGenerate' => $this->translator->trans('Generate', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'buttonClose' => $this->translator->trans('Close', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'modalTitle' => $this->translator->trans('Content', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'textCanceled' => $this->translator->trans('Canceled', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'textFields' => $this->translator->trans('Fields', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'regenerateQuestion' => $this->translator->trans(
                    'Are you sure want to regenerate this content?',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'translateQuestion' => $this->translator->trans(
                    'Are you sure want to translate this content ? The current content will be lost',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'translatingSettings' => $this->translator->trans(
                    'Translation settings',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'confirmCustomRequest' => $this->translator->trans(
                    'Would you like to replace existing content?',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'customRequest' => $this->translator->trans(
                    'Custom request to ChatGPT',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'words' => $this->translator->trans('words', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'productTooltipMessage' => $this->translator->trans(
                    'Please, select the main category and brand for the product to get a more accurate result.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'subscriptionNotAvaialable' => $this->translator->trans(
                    'You need to order the subscription plan to use this feature.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'subscriptionPlanNoFeature' => $this->translator->trans(
                    'Your current subscription plan does not allow you to use this feature! Please order the new plan or renew the current one!',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'subscriptionLimitЕxceeded' => $this->translator->trans(
                    'The subscription plan limit has been reached! Please order the new plan or renew the current one!',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'renewOrderTitle' => $this->translator->trans(
                    'Renew or Order the subscription plan',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'renewOrOrderSubscription' => $this->translator->trans(
                    '%opentag%Renew / Order new subscription plan%closetag%',
                    [
                        '%opentag%' => '<a href="' . $renewUrl . '">',
                        '%closetag%' => '</a>',
                    ],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'renewOrOrderBtn' => $this->translator->trans('Renew / Order', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'bulkButtonName' => $this->translator->trans('Generate description', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'bulkGeneratePostButtonName' => $this->translator->trans('Generate post', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'bulkTranslateButtonName' => $this->translator->trans(
                    'Translate description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkTitleTranslateButtonName' => $this->translator->trans(
                    'Translate title',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkRewriteButtonName' => $this->translator->trans(
                    'Rewrite',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkConfirmAddOrRaplace' => $this->translator->trans(
                    'Add or Replace',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkConfirmGenerateDescription' => $this->translator->trans(
                    'Add or Replace the description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkConfirmGenerateContent' => $this->translator->trans(
                    'Add or Replace the existing content',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkGeneratingSkipExistingDescription' => $this->translator->trans(
                    'Skip products with the existing description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkGeneratingSkipExistingTitle' => $this->translator->trans(
                    'Skip products with the existing title',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkGeneratingSkipExistingCategoryDescription' => $this->translator->trans(
                    'Skip category with the existing description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkGeneratingSkipExistingCategoryTitle' => $this->translator->trans(
                    'Skip category with the existing title',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkGeneratingDescription' => $this->translator->trans(
                    'Description generation settings',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkTranslatingDescription' => $this->translator->trans(
                    'Translating description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkTranslatingTitle' => $this->translator->trans(
                    'Translating title',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkRewriteTitle' => $this->translator->trans(
                    'Rewrite settings',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkGenerationProcessFail' => $this->translator->trans(
                    'Generating failed.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkTranslationProcessFail' => $this->translator->trans(
                    'Translating failed.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkRewriteProcessFail' => $this->translator->trans(
                    'Rewriting failed.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkGenerationProcessCompleted' => $this->translator->trans(
                    'The generation process has been completed.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkTranslationProcessCompleted' => $this->translator->trans(
                    'The traslation process has been completed.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkRewriteProcessCompleted' => $this->translator->trans(
                    'The rewriting process has been completed.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkRewriteFYI' => $this->translator->trans(
                    'FYI: If the field was empty before the rewrite, it will remain empty afterward!',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'awaitingRequestResponse' => $this->translator->trans(
                    'Your request has been added to the queue. Wait for completion and stay on the page.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'maxWordsNotValid' => $this->translator->trans(
                    'The maximum number of words is not valid. The value should be more than %min_words% words',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'pleaseSelectLanguages' => $this->translator->trans(
                    'No languages were selected. Choose at least one language.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'pleaseSelectFields' => $this->translator->trans(
                    'No fields were selected. Choose at least one field.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'useProductCategory' => $this->translator->trans(
                    'Use product category',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'useProductBrand' => $this->translator->trans('Use product brand', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'useProductEan' => $this->translator->trans('Use product EAN', [], 'Modules.Chatgptcontentgenerator.Admin'),
                'descriptionOrCharacteristics' => $this->translator->trans(
                    'Generate description or characteristics',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'titlePageConentGeneration' => $this->translator->trans(
                    'Content generation settings',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'pleaseSelectItems' => $this->translator->trans(
                    'Select at least one item',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'defaultOptions' => $this->translator->trans(
                    '-- Use default options --',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'requestTemplate' => $this->translator->trans(
                    'Request template',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'warningRewrite' => $this->translator->trans(
                    'WARNING! Your HTML code will be lost after rewriting - you\'ll get plain text as a result.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'productPostTitle' => $this->translator->trans(
                    'Generate blog post for product',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),

                'createNewSpinOff' => $this->translator->trans(
                    'Create new spin-off',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'NumberOfSpinOffs' => $this->translator->trans(
                    'Number of spin-offs',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'SpinOffs' => $this->translator->trans(
                    'spin-offs',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffIndividual' => $this->translator->trans(
                    'Individual',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffCommon' => $this->translator->trans(
                    'Common',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkSpinOffButtonStock' => $this->translator->trans(
                    'New spin-off products stock',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'bulkCreationProcessCompleted' => $this->translator->trans(
                    'The creation process has been completed.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffCreateUseChatGPT' => $this->translator->trans(
                    'Use ChatGPT',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffCreateUseChatGptTip' => $this->translator->trans(
                    'If the ChatGPT option is set to YES, new spin-off products will receive unique content generated by ChatGPT AI',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffCreateUseChatGptYes' => $this->translator->trans(
                    'Yes',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffCreateUseChatGptNo' => $this->translator->trans(
                    'No',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffRewriteNo' => $this->translator->trans(
                    'Generate',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffRewriteYes' => $this->translator->trans(
                    'Rewrite',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffButtonDescription' => $this->translator->trans(
                    'Product description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffButtonShortDescription' => $this->translator->trans(
                    'Product short description',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffButtonToolTipStock' => $this->translator->trans(
                    'Management stock of the new Spin Offs',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffButtonToolTipRewrite' => $this->translator->trans(
                    'Rewrite or generate new content',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffQuantityCommon' => $this->translator->trans(
                    'Update quantities of parent product',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffQuantityIndividual' => $this->translator->trans(
                    'Update individual quantities of spin-off product',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinOffUpdateQuantities' => $this->translator->trans(
                    'Update Quantities',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'deleteSpinOff' => $this->translator->trans(
                    'Delete spin-off ID: ',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinoffDeletedSuccessfully' => $this->translator->trans(
                    'Spin-off deleted successfully.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinoffCreateNumberError' => $this->translator->trans(
                    'The value must be greater than 0.',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinoffSubscriptionExpired' => $this->translator->trans(
                    'Expired!',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinoffSubscriptionDaysLeft' => $this->translator->trans(
                    'days left',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'spinoffButtonCreate' => $this->translator->trans(
                    'Create',
                    [],
                    'Modules.Chatgptcontentgenerator.Admin'
                ),
                'buttonDeleteNow' => $this->translator->trans('Delete now', [], 'Modules.Chatgptcontentgenerator.Admin'),
            ],
        ]);
    }

    protected function getGptVersionSelectors()
    {
        return [
            'pfLiTabs6Id' => 'product_options-tab-nav',
            'pfContent6Id' => 'product_options-tab',
            'pfTabsContentId' => 'product-tabs-content',
            'pfIsoCodeId' => 'product_header_name_dropdown',
            'pfName' => '#product_header_name_',
            'pfDescription' => '#product_description_description',
            'pfDescriptionShort' => '#product_description_description_short',
            'pfManufacturerId' => '#product_description_manufacturer',

            'plProductFormId' => 'product_filter_form',
            'plBulkMenu' => '#product_grid >.btn-group .dropdown-menu',
            'plBulkSelectedName' => 'product_bulk[]',
        ];
    }

    protected function getContentEditorPreffix()
    {
        return [
            'description' => 'product_description_description_',
            'descriptionShort' => 'product_description_description_short_',
            'name' => 'product_header_name_',
        ];
    }

    protected function getLanguages()
    {
        $languages = [];

        foreach (\Language::getLanguages() as $language) {
            $languages[strtoupper($language['iso_code'])] = $language['id_lang'];
        }

        return $languages;
    }

    public function getServerParams()
    {
        $curl = curl_version();
        $serverParams = [
            'max_time' => [
                'name' => 'Max time',
                'value' => ini_get('max_execution_time'),
            ],
            'curl' => [
                'name' => 'Curl',
                'value' => !empty($curl['version']) ? 'Enabled' : 'Disabled',
            ],
            'php_version' => [
                'name' => 'PHP Version',
                'value' => phpversion(),
            ],
        ];

        return $serverParams;
    }
}
