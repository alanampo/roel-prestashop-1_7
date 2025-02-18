{**
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
*}
{strip}
<div class="tvcmsdesktop-view-compare">
  	<a class="link_wishlist tvdesktop-view-compare tvcmscount-compare-product" href="{$link->getModuleLink('tvcmsproductcompare', 'productcomparelist')|escape:'html':'UTF-8'}" title="{l s='Product Compare' mod='tvcmsproductcompare'}">
  		<div class="tvdesktop-compare-icon">
     		<i class='material-icons'>&#xe043;</i>
    	</div>
    	<div class="tvdesktop-view-compare-name"><span> {l s='Compare' mod='tvcmsproductcompare'}</span>  <span class="count-product"><span>(</span>{$tot_comp_prod}<span>)</span></span>
      	</div>
  	</a>
</div>
{/strip}