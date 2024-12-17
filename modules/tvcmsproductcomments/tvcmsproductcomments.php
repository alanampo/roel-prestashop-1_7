<?php
/**
* 2007-2022 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2022 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . '/tvcmsproductcomments/tvcmsproductcomment.php';
require_once _PS_MODULE_DIR_ . '/tvcmsproductcomments/tvcmsproductcommentcriterion.php';

class TvcmsProductComments extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    private $html = '';
    private $postErrors = array();
    private $filters = array();

    private $tvcmsproductCommentsCriterionTypes = array();
    private $baseUrl;

    public function __construct()
    {
        $this->name = 'tvcmsproductcomments';
        $this->tab = 'front_office_features';
        $this->version = '4.0.0';
        $this->author = 'JHP Template';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->setFilters();
        parent::__construct();

        $this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->l('JHP Template - Product Comments');
        $this->description = $this->l('Allows users to post reviews and rate products on specific criteria.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = '';

        $this->confirmUninstall = $this->l('Warning: all the data saved in your database will be deleted.'.
            ' Are you sure you want uninstall this module?');
    }

    public function install($keep = true)
    {
        if ($keep) {
            if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
                return false;
            } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
                return false;
            }
            $sql = str_replace(array(
                'PREFIX_',
                'ENGINE_TYPE'
            ), array(
                _DB_PREFIX_,
                _MYSQL_ENGINE_
            ), $sql);
            $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

            foreach ($sql as $query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        $this->installTab();
        if (parent::install() == false
            // || !$this->registerHook('productFooter')
            || !$this->registerHook('displayHeader')
            // || !$this->registerHook('displayRightColumnProduct')
            || !$this->registerHook('displayProductListReviews')
            || !$this->registerHook('displayProductListReviewsTab')
            || !$this->registerHook('displayProductListReviewsTabContent')
            || !$this->registerHook('top')
            || !$this->registerHook('displayReviewProductList')
            || !Configuration::updateValue('TVCMSPRODUCT_COMMENTS_MINIMAL_TIME', 30)
            || !Configuration::updateValue('TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS', 1)
            || !Configuration::updateValue('TVCMSPRODUCT_COMMENTS_LIST', 1)
            || !Configuration::updateValue('TVCMSPRODUCT_COMMENTS_MODERATE', 1)) {
            return false;
        }

        return true;
    }

    public function installTab()
    {
        $response = true;

        // First check for parent tab
        $parentTabID = Tab::getIdFromClassName('AdminJHPTemplate');

        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminJHPTemplate";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "JHPTemplate Extension";
            }
            $parentTab->id_parent = 0;
            $parentTab->module = $this->name;
            $response &= $parentTab->add();
        }
        
        // Check for parent tab2
        $parentTab_2ID = Tab::getIdFromClassName('AdminJHPTemplateModules');
        if ($parentTab_2ID) {
            $parentTab_2 = new Tab($parentTab_2ID);
        } else {
            $parentTab_2 = new Tab();
            $parentTab_2->active = 1;
            $parentTab_2->name = array();
            $parentTab_2->class_name = "AdminJHPTemplateModules";
            foreach (Language::getLanguages() as $lang) {
                $parentTab_2->name[$lang['id_lang']] = "JHPTemplate Configure";
            }
            $parentTab_2->id_parent = $parentTab->id;
            $parentTab_2->module = $this->name;
            $response &= $parentTab_2->add();
        }
        // Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Admin'.$this->name;
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Product Comment";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function uninstall($keep = true)
    {
        $this->uninstallTab();
        if (!parent::uninstall()
            || ($keep && !$this->deleteTables())
            || !Configuration::deleteByName('TVCMSPRODUCT_COMMENTS_MODERATE')
            || !Configuration::deleteByName('TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS')
            || !Configuration::deleteByName('TVCMSPRODUCT_COMMENTS_LIST')
            || !Configuration::deleteByName('TVCMSPRODUCT_COMMENTS_MINIMAL_TIME')
            // || !$this->unregisterHook('displayRightColumnProduct')
            || !$this->unregisterHook('header')
            // || !$this->unregisterHook('productFooter')
            || !$this->unregisterHook('displayReviewProductList')
            || !$this->unregisterHook('displayProductListReviewsTab')
            || !$this->unregisterHook('displayProductListReviewsTabContent')
            || !$this->unregisterHook('top')) {
            return false;
        }

        return true;
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('Admin'.$this->name);
        $tab = new Tab($id_tab);
        $tab->delete();
        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment`,
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment_criterion`,
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment_criterion_product`,
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment_criterion_lang`,
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment_criterion_category`,
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment_grade`,
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment_usefulness`,
            `' . _DB_PREFIX_ . 'tvcmsproduct_comment_report`');
    }

    public function getCacheId($id_product = null)
    {
        return parent::getCacheId() . '|' . (int)$id_product;
    }

    protected function postProcess()
    {
        $this->setFilters();

        if (Tools::isSubmit('submitModerate')) {
            $tmp = (int)Tools::getValue('TVCMSPRODUCT_COMMENTS_MODERATE');
            Configuration::updateValue('TVCMSPRODUCT_COMMENTS_MODERATE', $tmp);
            $tmp = (int)Tools::getValue('TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS');
            Configuration::updateValue('TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS', $tmp);
            $tmp = (int)Tools::getValue('TVCMSPRODUCT_COMMENTS_MINIMAL_TIME');
            Configuration::updateValue('TVCMSPRODUCT_COMMENTS_MINIMAL_TIME', $tmp);
            $tmp = (int)Tools::getValue('TVCMSPRODUCT_COMMENTS_LIST');
            Configuration::updateValue('TVCMSPRODUCT_COMMENTS_LIST', $tmp);
            $this->html .= '<div class="conf confirm alert alert-success">' . $this->l('Settings updated') . '</div>';
        } elseif (Tools::isSubmit('tvcmsproductcomments')) {
            $id_tvcmsproduct_comment = (int)Tools::getValue('id_tvcmsproduct_comment');
            $comment = new TvcmsProductComment($id_tvcmsproduct_comment);
            $comment->validate();
            TvcmsProductComment::deleteReports($id_tvcmsproduct_comment);
        } elseif (Tools::isSubmit('deletetvcmsproductcomments')) {
            $id_tvcmsproduct_comment = (int)Tools::getValue('id_tvcmsproduct_comment');
            $comment = new TvcmsProductComment($id_tvcmsproduct_comment);
            $comment->delete();
        } elseif (Tools::isSubmit('submitEditCriterion')) {
            $criterion = new TvcmsProductCommentCriterion((int)Tools::getValue('id_tvcmsproduct_comment_criterion'));
            $tmp = Tools::getValue('id_tvcmsproduct_comment_criterion_type');
            $criterion->id_tvcmsproduct_comment_criterion_type = $tmp;
            $criterion->active = Tools::getValue('active');

            $languages = Language::getLanguages();
            $name = array();
            foreach ($languages as $value) {
                $name[$value['id_lang']] = Tools::getValue('name_' . $value['id_lang']);
            }
            $criterion->name = $name;

            $criterion->save();

            // Clear before reinserting data
            $criterion->deleteCategories();
            $criterion->deleteProducts();
            if ($criterion->id_tvcmsproduct_comment_criterion_type == 2) {
                if ($categories = Tools::getValue('categoryBox')) {
                    if (count($categories)) {
                        foreach ($categories as $id_category) {
                            $criterion->addCategory((int)$id_category);
                        }
                    }
                }
            } elseif ($criterion->id_tvcmsproduct_comment_criterion_type == 3) {
                if ($products = Tools::getValue('ids_product')) {
                    if (count($products)) {
                        foreach ($products as $product) {
                            $criterion->addProduct((int)$product);
                        }
                    }
                }
            }
            if ($criterion->save()) {
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminModules').'&configure='
                    .$this->name.'&conf=4');
            } else {
                $this->html .= '<div class="conf confirm alert alert-danger">'
                    .$this->l('The criterion could not be saved').'</div>';
            }
        } elseif (Tools::isSubmit('deletetvcmsproductcommentscriterion')) {
            $tmp = (int)Tools::getValue('id_tvcmsproduct_comment_criterion');
            $tvcmsproductCommentCriterion = new TvcmsProductCommentCriterion($tmp);
            if ($tvcmsproductCommentCriterion->id) {
                if ($tvcmsproductCommentCriterion->delete()) {
                    $this->html .= '<div class="conf confirm alert alert-success">'
                        .$this->l('Criterion deleted').'</div>';
                }
            }
        } elseif (Tools::isSubmit('statustvcmsproductcommentscriterion')) {
            $tmp = (int)Tools::getValue('id_tvcmsproduct_comment_criterion');
            $criterion = new TvcmsProductCommentCriterion($tmp);
            if ($criterion->id) {
                $criterion->active = (int)(!$criterion->active);
                $criterion->save();
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name
                .'&tab_module='.$this->tab.'&conf=4&module_name='.$this->name);
        } elseif ($id_tvcmsproduct_comment = (int)Tools::getValue('approveComment')) {
            $comment = new TvcmsProductComment($id_tvcmsproduct_comment);
            $comment->validate();
        } elseif ($id_tvcmsproduct_comment = (int)Tools::getValue('noabuseComment')) {
            TvcmsProductComment::deleteReports($id_tvcmsproduct_comment);
        }

        $path = _MODULE_DIR_.$this->name."/views/img/";

        $this->context->smarty->assign('path', $path);


        $this->_clearcache('views/templates/front/tvcmsproductcomments_reviews.tpl');
    }

    
    public function getContent()
    {
        include_once dirname(__FILE__) . '/tvcmsproductcomment.php';
        include_once dirname(__FILE__) . '/tvcmsproductcommentcriterion.php';

        $this->html = '';
        if (Tools::isSubmit('updatetvcmsproductcommentscriterion')) {
            $this->html .= $this->renderCriterionForm((int)Tools::getValue('id_tvcmsproduct_comment_criterion'));
        } else {
            $this->postProcess();
            $this->html .= $this->renderConfigForm();
            $this->html .= $this->renderModerateLists();
            $this->html .= $this->renderCriterionList();
            $this->html .= $this->renderCommentsList();
        }

        $this->setBaseUrl();
        $this->tvcmsproductCommentsCriterionTypes = TvcmsProductCommentCriterion::getTypes();

        $this->context->controller->addJs($this->_path . 'views/js/moderate.js');

        return $this->html;
    }

    public function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode(".", $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    private function setBaseUrl()
    {
        $this->baseUrl = 'index.php?';
        foreach (Tools::getAllValues() as $k => $value) {
            if (!in_array($k, array(
                'deleteCriterion',
                'editCriterion'
            ))
            ) {
                $this->baseUrl .= $k . '=' . $value . '&';
            }
        }
        $this->baseUrl = rtrim($this->baseUrl, '&');
    }

    public function renderConfigForm()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        //retro compat 1.5
                        'label' => $this->l('All reviews must be validated by an employee'),
                        'name' => 'TVCMSPRODUCT_COMMENTS_MODERATE',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        //retro compat 1.5
                        'label' => $this->l('Allow guest reviews'),
                        'name' => 'TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum time between 2 reviews from the same user'),
                        'name' => 'TVCMSPRODUCT_COMMENTS_MINIMAL_TIME',
                        'class' => 'fixed-width-xs',
                        'suffix' => 'seconds',
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Show reviews counter and stars on list of products'),
                        'name' => 'TVCMSPRODUCT_COMMENTS_LIST',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitModerate',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->name;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $tmp = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->allow_employee_form_lang = $tmp ? $tmp : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitProducCommentsConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='
            .$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function renderModerateLists()
    {
        $return = null;

        if (Configuration::get('TVCMSPRODUCT_COMMENTS_MODERATE')) {
            $comments = TvcmsProductComment::getByValidate(0);

            $fields_list = $this->getStandardFieldList();

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $return .= '<h1>' . $this->l('Reviews waiting for approval') . '</h1>';
                $actions = array(
                    'enable',
                    'delete'
                );
            } else {
                $actions = array(
                    'approve',
                    'delete'
                );
            }

            $helper = new HelperList();
            $helper->shopLinkType = '';
            $helper->simple_header = true;
            $helper->actions = $actions;
            $helper->show_toolbar = false;
            $helper->module = $this;
            $helper->listTotal = count($comments);
            $helper->identifier = 'id_tvcmsproduct_comment';
            $helper->title = $this->l('Reviews waiting for approval');
            $helper->table = $this->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
            //$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

            $return .= $helper->generateList($comments, $fields_list);
        }

        $comments = TvcmsProductComment::getReportedComments();

        $fields_list = $this->getStandardFieldList();

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $return .= '<h1>' . $this->l('Reported Reviews') . '</h1>';
            $actions = array(
                'enable',
                'delete'
            );
        } else {
            $actions = array(
                'delete',
                'noabuse'
            );
        }

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = $actions;
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->listTotal = count($comments);
        $helper->identifier = 'id_tvcmsproduct_comment';
        $helper->title = $this->l('Reported Reviews');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        //$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

        $return .= $helper->generateList($comments, $fields_list);

        return $return;
    }

    public function renderCriterionList()
    {
        include_once dirname(__FILE__) . '/tvcmsproductcommentcriterion.php';

        $criterions = TvcmsProductCommentCriterion::getCriterions($this->context->language->id, false, false);

        $fields_list = array(
            'id_tvcmsproduct_comment_criterion' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
            ),
            'type_name' => array(
                'title' => $this->l('Type'),
                'type' => 'text',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array(
            'edit',
            'delete'
        );
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name
                .'&module_name='.$this->name.'&updatetvcmsproductcommentscriterion',
            'desc' => $this->l('Add New Criterion', null, null, false),
        );
        $helper->module = $this;
        $helper->identifier = 'id_tvcmsproduct_comment_criterion';
        $helper->title = $this->l('Review Criteria');
        $helper->table = $this->name . 'criterion';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        //$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

        return $helper->generateList($criterions, $fields_list);
    }

    public function renderCommentsList()
    {
        $comments = TvcmsProductComment::getByValidate(1);
        $moderate = Configuration::get('TVCMSPRODUCT_COMMENTS_MODERATE');
        if (empty($moderate)) {
            $comments = array_merge($comments, TvcmsProductComment::getByValidate(0));
        }

        $fields_list = $this->getStandardFieldList();

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = array('delete');
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->listTotal = count($comments);
        $helper->identifier = 'id_tvcmsproduct_comment';
        $helper->title = $this->l('Approved Reviews');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        //$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

        return $helper->generateList($comments, $fields_list);
    }

    public function getConfigFieldsValues()
    {
        return array(
            'TVCMSPRODUCT_COMMENTS_MODERATE' => Tools::getValue(
                'TVCMSPRODUCT_COMMENTS_MODERATE',
                Configuration::get('TVCMSPRODUCT_COMMENTS_MODERATE')
            ),
            'TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS' => Tools::getValue(
                'TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS',
                Configuration::get('TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS')
            ),
            'TVCMSPRODUCT_COMMENTS_MINIMAL_TIME' => Tools::getValue(
                'TVCMSPRODUCT_COMMENTS_MINIMAL_TIME',
                Configuration::get('TVCMSPRODUCT_COMMENTS_MINIMAL_TIME')
            ),
            'TVCMSPRODUCT_COMMENTS_LIST' => Tools::getValue(
                'TVCMSPRODUCT_COMMENTS_LIST',
                Configuration::get('TVCMSPRODUCT_COMMENTS_LIST')
            ),
        );
    }

    public function getCriterionFieldsValues($id = 0)
    {
        $criterion = new TvcmsProductCommentCriterion($id);

        return array(
            'name' => $criterion->name,
            'id_tvcmsproduct_comment_criterion_type' => $criterion->id_tvcmsproduct_comment_criterion_type,
            'active' => $criterion->active,
            'id_tvcmsproduct_comment_criterion' => $criterion->id,
        );
    }

    public function getStandardFieldList()
    {
        return array(
            'id_tvcmsproduct_comment' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'title' => array(
                'title' => $this->l('Review title'),
                'type' => 'text',
            ),
            'content' => array(
                'title' => $this->l('Review'),
                'type' => 'text',
            ),
            'grade' => array(
                'title' => $this->l('Rating'),
                'type' => 'text',
                'suffix' => '/5',
            ),
            'customer_name' => array(
                'title' => $this->l('Author'),
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Product'),
                'type' => 'text',
            ),
            'date_add' => array(
                'title' => $this->l('Time of publication'),
                'type' => 'date',
            ),
        );
    }

    public function renderCriterionForm($id_criterion = 0)
    {
        $types = TvcmsProductCommentCriterion::getTypes();
        $query = array();
        foreach ($types as $key => $value) {
            $query[] = array(
                'id' => $key,
                'label' => $value,
            );
        }

        $criterion = new TvcmsProductCommentCriterion((int)$id_criterion);
        $selected_categories = $criterion->getCategories();

        $product_table_values = Product::getSimpleProducts($this->context->language->id);
        $selected_products = $criterion->getProducts();
        foreach ($product_table_values as $key => $product) {
            if (false !== array_search($product['id_product'], $selected_products)) {
                $product_table_values[$key]['selected'] = 1;
            }
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $field_category_tree = array(
                'type' => 'categories_select',
                'name' => 'categoryBox',
                'label' => $this->l('Criterion will be restricted to the following categories'),
                'category_tree' => $this->initCategoriesAssociation(null, $id_criterion),
            );
        } else {
            $field_category_tree = array(
                'type' => 'categories',
                'label' => $this->l('Criterion will be restricted to the following categories'),
                'name' => 'categoryBox',
                'desc' => $this->l('Mark the boxes of categories to which this criterion applies.'),
                'tree' => array(
                    'use_search' => false,
                    'id' => 'categoryBox',
                    'use_checkbox' => true,
                    'selected_categories' => $selected_categories,
                ),
                //retro compat 1.5 for category tree
                'values' => array(
                    'trads' => array(
                        'Root' => Category::getTopCategory(),
                        'selected' => $this->l('Selected'),
                        'Collapse All' => $this->l('Collapse All'),
                        'Expand All' => $this->l('Expand All'),
                        'Check All' => $this->l('Check All'),
                        'Uncheck All' => $this->l('Uncheck All'),
                    ),
                    'selected_cat' => $selected_categories,
                    'input_name' => 'categoryBox[]',
                    'use_radio' => false,
                    'use_search' => false,
                    'disabled_categories' => array(),
                    'top_category' => Category::getTopCategory(),
                    'use_context' => true,
                ),
            );
        }

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add new criterion'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_tvcmsproduct_comment_criterion',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Criterion name'),
                        'name' => 'name',
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'id_tvcmsproduct_comment_criterion_type',
                        'label' => $this->l('Application scope of the criterion'),
                        'options' => array(
                            'query' => $query,
                            'id' => 'id',
                            'name' => 'label',
                        ),
                    ),
                    $field_category_tree,
                    array(
                        'type' => 'products',
                        'label' => $this->l('The criterion will be restricted to the following products'),
                        'name' => 'ids_product',
                        'values' => $product_table_values,
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        //retro compat 1.5
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitEditCriterion',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->name;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $tmp = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->allow_employee_form_lang = $tmp ? $tmp : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEditCriterion';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='
            .$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getCriterionFieldsValues($id_criterion),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form_1));
    }

    private function checkDeleteComment()
    {
        $action = Tools::getValue('delete_action');
        if (empty($action) === false) {
            $tvcmsproduct_comments = Tools::getValue('delete_id_tvcmsproduct_comment');

            if (count($tvcmsproduct_comments)) {
                if ($action == 'delete') {
                    foreach ($tvcmsproduct_comments as $id_tvcmsproduct_comment) {
                        if (!$id_tvcmsproduct_comment) {
                            continue;
                        }
                        $comment = new TvcmsProductComment((int)$id_tvcmsproduct_comment);
                        $comment->delete();
                        TvcmsProductComment::deleteGrades((int)$id_tvcmsproduct_comment);
                    }
                }
            }
        }
    }

    private function setFilters()
    {
        $this->filters = array(
            'page' => (string)Tools::getValue('submitFilter' . $this->name),
            'pagination' => (string)Tools::getValue($this->name . '_pagination'),
            'filter_id' => (string)Tools::getValue($this->name . 'Filter_id_tvcmsproduct_comment'),
            'filter_content' => (string)Tools::getValue($this->name . 'Filter_content'),
            'filter_customer_name' => (string)Tools::getValue($this->name . 'Filter_customer_name'),
            'filter_grade' => (string)Tools::getValue($this->name . 'Filter_grade'),
            'filter_name' => (string)Tools::getValue($this->name . 'Filter_name'),
            'filter_date_add' => (string)Tools::getValue($this->name . 'Filter_date_add'),
        );
    }

    public function displayApproveLink($token, $id, $name = null)
    {
        $this->smarty->assign(array(
            'href' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name
                .'&module_name='.$this->name.'&approveComment='.$id,
            'action' => $this->l('Approve'),
        ));

        return $this->display(__FILE__, 'views/templates/admin/list_action_approve.tpl');
    }

    public function displayNoabuseLink($token, $id, $name = null)
    {
        $this->smarty->assign(array(
            'href' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&module_name='
                .$this->name.'&noabuseComment='.$id,
            'action' => $this->l('Not abusive'),
        ));

        return $this->display(__FILE__, 'views/templates/admin/list_action_noabuse.tpl');
    }

    public function hookdisplayProductListReviewsTab()
    {
        return $this->display(
            __FILE__,
            'views/templates/front/tvcmsproductcomments_tab.tpl'
        );
    }

    public function hookdisplayReviewProductList($params)
    {
        $id_product = (int)$params['product']['id_product'];
        $productType = isset($params['productType'])?$params['productType']:'';
        $average = TvcmsProductComment::getAverageGrade($id_product);
        $path = _MODULE_DIR_.$this->name."/views/img/";

        $this->smarty->assign(array(
            'product' => $params['product'],
            'averageTotal' => round($average['grade']),
            'ratings' => TvcmsProductComment::getRatings($id_product),
            'total_comments' => (int)TvcmsProductComment::getCommentNumber($id_product),
            'path' => $path,
            'productType' => $productType
        ));

        return $this->display(
            __FILE__,
            'views/templates/front/tvcmsproductcomments_reviews.tpl'
        );
    }


    public function hookdisplayProductExtraContent($params)
    {
        $tabz = array();
        $tabz[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())->setTitle(
            $this->l('Reviews')
        )->setContent($this->hookProductFooter($params));
        return $tabz;
    }

    public function hookdisplayProductListReviewsTabContent($params)
    {
        return $this->hookProductFooter($params);
    }

    public function hookProductFooter($params)
    {
        $tmp = (int)$this->context->cookie->id_customer;
        $tmp_2 = (int)$this->context->cookie->id_guest;
        $id_guest = (!$tmp) ? $tmp_2 : false;
        $customerComment = TvcmsProductComment::getByCustomer(
            (int)(Tools::getValue('id_product')),
            (int)$this->context->cookie->id_customer,
            true,
            (int)$id_guest
        );

        $averages = TvcmsProductComment::getAveragesByProduct(
            (int)Tools::getValue('id_product'),
            $this->context->language->id
        );
        $averageTotal = 0;
        foreach ($averages as $average) {
            $averageTotal += (float)($average);
        }
        $averageTotal = count($averages) ? ($averageTotal / count($averages)) : 0;

        $product = $this->context->controller->getProduct();
        $image = Product::getCover((int)Tools::getValue('id_product'));
        $cover_image = $this->context->link->getImageLink(
            $product->link_rewrite,
            $image['id_image'],
            ImageType::getFormattedName('medium')
        );

        $tmp = Configuration::get('TVCMSPRODUCT_COMMENTS_MINIMAL_TIME');
        $this->context->smarty->assign(array(
            'logged' => $this->context->customer->isLogged(true),
            'action_url' => '',
            'product' => $product,
            'comments' => TvcmsProductComment::getByProduct(
                (int)Tools::getValue('id_product'),
                1,
                null,
                $this->context->cookie->id_customer
            ),
            'criterions' => TvcmsProductCommentCriterion::getByProduct(
                (int)Tools::getValue('id_product'),
                $this->context->language->id
            ),
            'averages' => $averages,
            'tvcmsproduct_comment_path' => $this->_path,
            'averageTotal' => $averageTotal,
            'allow_guests' => (int)Configuration::get('TVCMSPRODUCT_COMMENTS_ALLOW_GUESTS'),
            'too_early' => ($customerComment && (strtotime($customerComment['date_add']) + $tmp) > time()),
            'delay' => Configuration::get('TVCMSPRODUCT_COMMENTS_MINIMAL_TIME'),
            'id_tvcmsproduct_comment_form' => (int)Tools::getValue('id_product'),
            'secure_key' => $this->secure_key,
            'tvcmsproductcomment_cover' => (int)Tools::getValue('id_product') . '-' . (int)$image['id_image'],
            'tvcmsproductcomment_cover_image' => $cover_image,
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'nbComments' => (int)TvcmsProductComment::getCommentNumber((int)Tools::getValue('id_product')),
            'tvcmsproductcomments_controller_url' => $this->context->link->getModuleLink('tvcmsproductcomments'),
            'tvcmsproductcomments_url_rewriting_activated' => Configuration::get('PS_REWRITING_SETTINGS', 0),
            'moderation_active' => (int)Configuration::get('TVCMSPRODUCT_COMMENTS_MODERATE'),
        ));

        // $this->context->controller->pagination(
        //     (int) TvcmsProductComment::getCommentNumber((int) Tools::getValue('id_product'))
        // );

        return $this->display(__FILE__, 'views/templates/front/tvcmsproductcomments.tpl');
    }

    public function hookdisplayHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/jquery.rating.pack.js');
        $this->context->controller->addJS($this->_path . 'views/js/jquery.textareaCounter.plugin.js');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
        $this->context->controller->addjqueryPlugin('fancybox');
        $this->page_name = Dispatcher::getInstance()->getController();
    }

    public function initCategoriesAssociation($id_root = null, $id_criterion = 0)
    {
        if (is_null($id_root)) {
            $id_root = Configuration::get('PS_ROOT_CATEGORY');
        }
        $id_shop = (int)Tools::getValue('id_shop');
        $shop = new Shop($id_shop);
        if ($id_criterion == 0) {
            $selected_cat = array();
        } else {
            $pdc_object = new TvcmsProductCommentCriterion($id_criterion);
            $selected_cat = $pdc_object->getCategories();
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }
        $root_category = array(
            'id_category' => $root_category->id,
            'name' => $root_category->name[$this->context->language->id]
        );

        $helper = new Helper();

        return $helper->renderCategoryTree($root_category, $selected_cat, 'categoryBox', false, true);
    }

   /* public function inconsistency($return)
    {
        return;
    }*/
}
