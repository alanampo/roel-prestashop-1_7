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

class ProductQuotationMessagesModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->context = Context::getContext();

        if (null === $this->context->customer || !isset($this->context->customer->id)) {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function initContent()
    {
        parent::initContent();
        $obj = new Quote;
        $key = Tools::getValue('key');
        $id_quotation = (int)Tools::getValue('id_quotation');
        $success = (int)Tools::getValue('success');
        $authorize = 0;
        $authorize = $obj->checkAuthorizeLevel($id_quotation, $key);

        $this->context->smarty->assign(array(
            'authorize' => (int)$authorize,
        ));

        if (empty($key)) {
            $this->errors[] = $this->module->l('Invalid key, you are not authorized to view this.');
        } elseif ($id_quotation <= 0) {
            $this->errors[] = $this->module->l('Invalid ID, you are not authorized to view this.');
        } else {
            $form_action = $this->context->link->getModuleLink(
                'productquotation',
                'messages',
                [
                    'key' => $key,
                    'id_quotation' => $id_quotation,
                ],
                true,
                $this->context->language->id,
                $this->context->shop->id
            );

            $obj->setMessageAsRead($id_quotation);
            $threads = $obj->getUserThreads($id_quotation);

            $firstname = $this->context->customer->firstname;
            $lastname = $this->context->customer->lastname;
            $email = $this->context->customer->email;
            
            foreach ($threads as $key => $value) {
                $message = $value['message'];
                $message = str_replace("{first_name}", $firstname, $message);
                $message = str_replace("{last_name}", $lastname, $message);
                $message = str_replace("{customer_mail}", $email, $message);
                $message = str_replace("{id_quotation}", $id_quotation, $message);
                $threads[$key]['message'] = $message;
            }

            $this->context->smarty->assign(array(
                'success' => $success,
                'threads' => $threads,
                'form_action' => $form_action
            ));
        }

        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $this->setTemplate('module:productquotation/views/templates/front/messages-17.tpl');
        } else {
            $this->setTemplate('messages.tpl');
        }
    }

    public function postProcess()
    {
        $obj = new Quote;
        $id_quotation = (int)Tools::getValue('id_quotation');
        $key = Tools::getValue('key');

        if (Tools::isSubmit('submitMessage')) {
            $message = Tools::getValue('message');
            $new_link = $this->context->link->getModuleLink(
                'productquotation',
                'messages',
                [
                    'key' => $key,
                    'success' => 1,
                    'id_quotation' => $id_quotation,
                ],
                true,
                $this->context->language->id,
                $this->context->shop->id
            );

            $authorize = $obj->checkAuthorizeLevel($id_quotation, $key);

            if (empty($key)) {
                $this->errors[] = $this->module->l('Invalid key, you are not authorized to submit message.');
            } elseif ($id_quotation <= 0) {
                $this->errors[] = $this->module->l('Invalid ID, you are not authorized to submit message.');
            } elseif ($authorize <= 0) {
                $this->errors[] = $this->module->l('Error: You are not authorized to submit message.');
            } elseif (empty($message)) {
                $this->errors[] = $this->module->l('Error: Message cannot be empty.');
            } else {
                $obj->saveMessage($id_quotation, $message);
                $send_mail_admin = (int)Configuration::get('PQUOTE_SENDMAIL_ADMIN');
                if ($send_mail_admin > 0) {
                    $this->sendMailToAdmin($id_quotation, $message);
                }
                Tools::redirect($new_link);
            }
        }
    }

    private function sendMailToAdmin($id, $user_message)
    {
        $employee = new Employee(1);
        $admin_email = Configuration::get('PQUOTE_ADMIN_EMAIL');
        $admin_email = (empty($admin_email)) ? $employee->email : $admin_email;
        $id_lang = (int)$this->context->language->id;
        $vars = array('{id}' => $id, '{text}' => $user_message);
        return Mail::Send(
            (int) $id_lang,
            'notifyadminmessage',
            Mail::l('Message Received', (int)$id_lang),
            $vars,
            $admin_email,
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

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $page_meta = Meta::getMetaByPage('my-account', $this->context->language->id);
        $page_meta = $page_meta['title'];
        $id = (int)Tools::getValue('id_quotation');
        $key = Tools::getValue('key');
        $meta_title = Configuration::get('PQUOTE_HEAD', $this->context->language->id);
        $meta_title = empty($meta_title) ? 'My Quotations' : $meta_title;
        $breadcrumb['links'][] = array(
        'title' => $page_meta,
        'url' => $this->context->link->getPageLink('my-account'),
        );
        $breadcrumb['links'][] = array(
            'title' => $meta_title,
            'url' => $this->context->link->getModuleLink('productquotation', 'quotations'),
        );
        if ($id > 0 && !empty($key)) {
            $breadcrumb['links'][] = array(
                'title' => $id,
                'url' => $this->context->link->getModuleLink(
                    'productquotation',
                    'quotations',
                    array('id_quotation' => $id, 'key' => $key)
                ),
            );
        }
        return $breadcrumb;
    }
}
