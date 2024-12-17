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
{if (isset($videos) && !empty($videos))}
{$tmp = 1}
	{foreach from=$videos item=videourl}
		{if $tmp == 1}

			{$youtube = preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videourl, $match)}
			{$youtube_id = $match[1]}

			<a href="{$videourl|escape:'htmlall':'UTF-8'}?autoplay=1" class="various fancybox fancybox.iframe img_content">
				<div class="tvnews-event-hoverbtn">	
					<div class="tvblog-content-img">
						<img src="https://img.youtube.com/vi/{$youtube_id}/0.jpg" alt='' class="tv-img-responsive">
				  	</div>
					<!-- <div class="tvnews-event-overly"></div> -->
				  	<div class="tvnews-event-buttons">
				  		<i class='material-icons'>&#xe038;</i>
				  	</div>
				</div>
				{* <div class="post_meta">
					<div class="meta-author tvnews-event-username">
				  		<i class='material-icons'>&#xe038;</i>
						<p>{$firstname} {$lastname}</p>
					</div>
				</div> *}
			</a>
				
		{$tmp = 0}
		{/if}
	{/foreach}
{/if}
{/strip}