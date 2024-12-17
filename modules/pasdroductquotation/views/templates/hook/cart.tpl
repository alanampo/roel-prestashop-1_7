{*
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
*}


<div class="pq_cartpage_container">
    <a class="button btn btn-default standard-checkout button-medium btn-primary" href="{$link->getModuleLink('productquotation', 'quote')|escape:'htmlall':'UTF-8'}?asquote=1" style="">
        <span>{if empty($button_text)}{l s='Continue as Quotation' mod='productquotation'}{else}{$button_text|escape:'htmlall':'UTF-8'}{/if} <i class="icon-chevron-right right"></i></span>
    </a>
</div>