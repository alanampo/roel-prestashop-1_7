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

class TvcmsVideoTabConfirmDeleteModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function run()
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $id_product=Tools::getValue('id');
        $id_lang = Tools::getValue('id_lang');
        $id_shop=($this->context->shop->id);
        $sql="SELECT text_url FROM "._DB_PREFIX_."url_video WHERE id_product='".$id_product."'";
        $sql .= "AND id_lang='".$id_lang."' AND id_store='".$id_shop."' AND type = 1 ";
        $text_url = $db->getValue($sql);
        if (empty($text_url)) {
            die("0");
        }
        $url_dele = _PS_ROOT_DIR_.'/media/'.$id_shop."/".$id_product."/".$id_lang."/";
        $file = $url_dele.$text_url;
        @unlink($file);
        $sql1 = "DELETE FROM "._DB_PREFIX_."url_video WHERE  id_product='".$id_product."'";
        $sql1 .= " AND id_lang=".$id_lang." AND id_store=".$id_shop." AND type = 1";
        $db->query($sql1);
        echo "1";
    }
}
