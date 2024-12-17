{*
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
*}

<nav class="pagination">
{if $start!=$stop}
	{if isset($is_search) && $is_search == true}
		{assign var="searchParam" value=['blog_search' => $blog_search]}
	{else}
		{assign var="searchParam" value=[]}
	{/if}
	<ul class="page-list clearfix text-center">
	{if $p != 1}
		{assign var='p_previous' value=$p-1}
		<li id="pagination_previous" class="pagination_previous"><a href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', array_merge(['p' => $p_previous], $searchParam))|escape:'html':'UTF-8'}" rel="prev">{l s='Previous' d='Modules.Chatgptcontentgenerator.Bloghome'}</a></li>
	{/if}
	{if $start>3}
		<li><a href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', array_merge(['p' => 1], $searchParam))|escape:'html':'UTF-8'}">1</a></li>
		<li class="truncate">...</li>
	{/if}
	{section name=pagination start=$start loop=$stop+1 step=1}
		{if $p == $smarty.section.pagination.index}
			<li class="current"><a><span>{$p|escape:'htmlall':'UTF-8'}</span></a></li>
		{else}
			<li><a href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', array_merge(['p' => $smarty.section.pagination.index], $searchParam))|escape:'html':'UTF-8'}">{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}</a></li>
		{/if}
	{/section}
	{if $pages_nb>$stop+2}
		<li class="truncate">...</li>
		<li><a href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', array_merge(['p' => $pages_nb], $searchParam))|escape:'html':'UTF-8'}">{$pages_nb|intval}</a></li>
	{/if}
	{if $pages_nb > 1 AND $p != $pages_nb}
		{assign var='p_next' value=$p+1}
		<li id="pagination_next" class="pagination_next"><a href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', array_merge(['p' => $p_next], $searchParam))|escape:'html':'UTF-8'}" rel="next">{l s='Next' d='Modules.Chatgptcontentgenerator.Bloghome'}</a></li>
	{/if}
	</ul>
{/if}
</nav>











<!-- /Pagination -->		