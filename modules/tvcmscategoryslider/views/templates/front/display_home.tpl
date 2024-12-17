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
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2022 PrestaShop SA
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
    {strip}
    {if $dis_arr_result['status']}
    <div class='tvcmscategory-slider container-fluid' data-bg-url="{$dis_arr_result['path']}{$main_heading.data.image}">
        <div class='tvcategory-slider container'>
            <div class="tvcategory-slider-title-content">
                {if $main_heading['main_status']}
                <div class='tvcategory-slider-main-title-wrapper'>
                    {include file='_partials/tvcms-main-title.tpl' main_heading=$main_heading path=$dis_arr_result['path']}
                </div>
                {/if}
                <div class="tvcategory-slider-inner-info-box">
                    <div class='tvcategory-slider-content-box owl-theme owl-carousel'>
                        {foreach $dis_arr_result['data'] as $data}
                        <div class="item tvcategory-slider-wrapper-info">
                            <a href="{$link->getCategoryLink($data['id_category'])}" class="tvcategory-slider-title">
                                <div class="tvcategory-img-block">
                                    <div class='tvcategory-slider-info-box'>
                                        <div class="tvcategory-slider-info-title">{$data['title']}</div>
                                        {* <div class="tvcategory-slider-short-desc">{$data['short_description']}</div> *}
                                        {* <span class="tvcategory-slider-all-link">{l s='Shop Now' mod='tvcmscategoryslider'}<i class='material-icons'>&#xe315;</i></span> *}
                                    <img src="{$dis_arr_result['path']}{$data['image']}" alt="{$data['title']}" width="{$data['width']}" height="{$data['height']}" class="tv-img-responsive" />
                                    </div>
                                </div>
                            </a>
                        </div>
                        {/foreach}
                    </div>
                </div>
                {* <div class='tvcategory-slider-pagination-wrapper tv-pagination-wrapper'>
                    <div class="tvcategory-slider-pagination">
                        <div class="tvcategory-slider-next-pre-btn tvcms-next-pre-btn">
                            <div class="tvcategory-slider-prev tvcmsprev-btn"><i class='material-icons'>&#xe5c4;</i></div>
                            <div class="tvcategory-slider-next tvcmsnext-btn"><i class='material-icons'>&#xe5c8;</i></div>
                        </div>
                    </div>
                </div> *}
            </div>
        </div>
    </div>
    {/if}
    {/strip}