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

{extends file='page.tpl'}


{block name='page_title'}
{if $is_search}{l s='Search results: %blog_search%' sprintf=['%blog_search%' => $blog_search] d='Modules.Chatgptcontentgenerator.Bloghome'}{else}{$blogMainTitle|escape:'html':'UTF-8'}{/if}
{/block}


{block name='head_seo_title'}
	{$blogMainTitle|escape:'html':'UTF-8'} - {$page.meta.title|escape:'html':'UTF-8'}
{/block}


{block name='head_pagination_seo'}
	{if $start!=$stop}
			{if $p != 1}
				{assign var='p_previous' value=$p-1}
				<link rel="prev" href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', ['p' => $p_previous])|escape:'html':'UTF-8'}">
			{/if}
			{* {section name=pagination start=$start loop=$stop+1 step=1}
				{if $p == $smarty.section.pagination.index}
					<link rel="canonical" href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', ['p' => $smarty.section.pagination.index])|escape:'html':'UTF-8'}">
				{/if}
			{/section} *}
			{if $pages_nb > 1 AND $p != $pages_nb}
				{assign var='p_next' value=$p+1}
				<link rel="next" href="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghomepage', ['p' => $p_next])|escape:'html':'UTF-8'}">
			{/if}
	{/if}
{/block}


{block name="left_column"}
	{include file="module:chatgptcontentgenerator/views/templates/front/sidebar.tpl"}
{/block}

{block name='page_content'}
<div class="gptblog">
	{if isset($posts) && count($posts)}
		<div class="gpt-blog-list">
			{foreach from=$posts item=post}
				{include file="module:chatgptcontentgenerator/views/templates/front/post.tpl" post=$post}
			{/foreach}
		</div>

		{include file="module:chatgptcontentgenerator/views/templates/front/pagination.tpl" rewrite=false type=false}

	{else}
		<p class="warning alert alert-warning">{l s='There are no posts' d='Modules.Chatgptcontentgenerator.Bloghome'}</p>
	{/if}
</div>
{/block}