<?php
/**
* 2007-2022PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Buy-Addons <contact@buy-addons.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* @since 1.6
*/

class TvcmsVideoTabEditVideoModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function run()
    {
        $url =Tools::getShopProtocol() . Tools::getServerName() . __PS_BASE_URI__;
        $id_product=Tools::getValue('id');
        $this->context->smarty->assign('url', $url);
        $this->context->smarty->assign("id_product", $id_product);
        $a = _PS_MODULE_DIR_ . '/tvcmsvideotab/views/templates/admin/videotab.tpl';
        $tb_p = $this->context->smarty->fetch($a);
        echo $tb_p;
    }
}
