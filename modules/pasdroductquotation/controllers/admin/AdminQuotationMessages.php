<?php
/**
* Product Quotation
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules All right reserved
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category  front_office_features
* @package   productquotation
*/

class AdminQuotationMessagesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'Mess';
        $this->table = 'quote_messages';
        $this->identifier = 'id_quote_messages';
        $this->lang = false;
        $this->bootstrap = true;
        $this->context = Context::getContext();
        parent::__construct();

        $this->_select = 'a.*, b.email';
        $this->_join = ' LEFT JOIN `'._DB_PREFIX_.'productquotation` b ON (a.id_quotation = b.id_productquotation)';
        $this->_group = 'GROUP BY a.id_quotation';

        $this->fields_list = array(
            'id_quote_messages' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'id_quotation' => array('title' => $this->l('ID Quotation'), 'align' => 'center'),
            'email' => array('title' => $this->l('Email'), 'align' => 'center',),
            'read' => array('title' => $this->l('Unread Messages'), 'align' => 'center', 'callback' => 'getUnreadMsg',),
        );
    }

    public function renderList()
    {
        $this->addRowAction('view');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function renderView()
    {
        parent::renderView();
        $class = new Mess;
        $id = (int)Tools::getValue('id_quote_messages');
        $success = (int)Tools::getValue('success');
        $id_quotation = (int)$class->getQuotationIdByMessage($id);
        $id_quotation = ($id_quotation <= 0) ? (int)Tools::getValue('id_productquotation') : $id_quotation;
        $id_quotation = ($id_quotation <= 0) ? (int)Tools::getValue('id_quotation') : $id_quotation;
        $threads = $class->getUserThreads($id_quotation);

        Quote::setMessageAsRead($id_quotation, 0);

        if ($id <= 0) {
            $id = (int)$class->getNextMessageId($id);
        }

        $quote_link = $this->context->link->getAdminLink('AdminProductQuotes').
        '&viewproductquotation&id_productquotation='.$id_quotation;
        
        $this->context->smarty->assign(array(
            'action' => self::$currentIndex.'&token='.$this->token.
            '&viewquote_messages&id_quotation='.$id_quotation.'&id_quote_messages='.$id,
            'id_quotation' => $id_quotation,
            'threads' => $threads,
            'success' => $success,
            'quote_link' => $quote_link
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).
        '/../../views/templates/admin/product_quotation/helpers/form/message.tpl');
    }

    public function postProcess()
    {
        $class = new Mess;
        $id = (int)Tools::getValue('id_quote_messages');
        $id_quotation = (int)Tools::getValue('id_quotation');
        $message = Tools::getValue('message');
        $action = self::$currentIndex.'&token='.$this->token.
        '&viewquote_messages&id_quotation='.$id_quotation.'&id_quote_messages='.$id.'&success=1';
        if (Tools::isSubmit('submitMessage')) {
            if (empty($message)) {
                $this->errors[] = $this->l('Not Sent: Please fill message.');
            } else {
                $user_data = $class->getUserInfo($id_quotation);
                $class->saveMessage($id_quotation, $message);
                $this->sendMailToUser($id_quotation, $user_data['email'], $user_data['key'], $message);
                Tools::redirectAdmin($action);
            }
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            if ($class->deleteByIdQuote($class->getQuotationIdByMessage(Tools::getValue($this->identifier)))) {
                $this->confirmations[] = $this->l('Messages deleted successfully.');
            }
        }
    }

    private function sendMailToUser($id_quotation, $email, $key, $message)
    {
        $id_lang = (int)$this->context->language->id;
        $message_link = $this->context->link->getModuleLink(
            'productquotation',
            'messages'
        ).'?id_quotation='.$id_quotation.'&key='.$key;
        $vars = array('{link_message}' => $message_link, '{message}' => $message);
        return Mail::Send(
            (int)$id_lang,
            'notifyusernewmessage',
            Mail::l('New message received', (int)$id_lang),
            $vars,
            $email,
            null,
            null,
            $this->context->shop->name,
            null,
            null,
            _PS_MODULE_DIR_.'productquotation/mails/',
            false,
            1
        );
    }

    public static function getAuthorName($id)
    {
        if ($id <= 0) {
            return 'User';
        } else {
            return 'Administrator';
        }
    }

    public static function getUnreadMsg($read, $row)
    {
        $unread = (int) Quote::countMessage($row['id_quotation'], 0);
        
        if ($unread) {
            return sprintf('<span class="badge badge-success">%d</span', $unread);
        }
        return sprintf('<span class="badge badge-danger">%d</span', $unread);
    }
}
