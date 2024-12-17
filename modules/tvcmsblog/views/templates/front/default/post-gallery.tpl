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
<div class="post_format_items tvcmsblog-gallery-slider tvcmsblog-gallery-slider-{$blog_id}" data-slider-id='tvcmsblog-gallery-slider-{$blog_id}'>
	<div class="{if count($gallery_lists) > 1}owl-theme owl-carousel{/if} tvblog-wrapper-slider">

	{if isset($gallery_lists) && $gallery_lists}
	{foreach from=$gallery_lists item=galleryimg}
		<div class="item post_video tvblog-slider swiper-slide">
			<div class="post_gallery_img item">
				<img class="tvcmsblog_img img-responsive tvblog-balance-height" src="{$galleryimg.$imagesize}" alt="">
			</div>
		</div>
	{/foreach}
	{/if}
	</div>
	{if count($gallery_lists) > 1}
	<div class="tvcmsblog-gallery-slider-pagination">
        <div class="tvcmsblog-gallery-next-pre-btn">
          	<div class="tvcmsblog-gallery-slider-prev"><i class='material-icons'>&#xe314;</i></div>
          	<div class="tvcmsblog-gallery-slider-next"><i class='material-icons'>&#xe315;</i></div>
        </div>
    </div>
    {/if}
</div>

{/strip}