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
{if $ps_ver > 0}
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="{$link->getModuleLink('productquotation', 'quotations')|escape:'htmlall':'UTF-8'}">
<span class="link-item">
<i class="material-icons">&#xE8EE;</i>
  {l s='My Quotations' mod='productquotation'}
</span>
</a>
{else}
<li class="advanceblog">
    <a href="{$link->getModuleLink('productquotation', 'quotations')|escape:'htmlall':'UTF-8'}" title="{l s='My Quotations' mod='productquotation'}">
    <i class="icon-productquotation"></i>
        <span>{l s='My Quotations' mod='productquotation'}</span>
    </a>
</li>
<style>
.icon-productquotation:before { content: "\f044"; }
</style>
{/if}