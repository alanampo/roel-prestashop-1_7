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
    <div class='container-fluid tvcmstestimonial tvcms-all-testimonial wow fadeInUp' data-bg-url="{$dis_arr_result['path']}{$main_heading.data.image}demo_main_img.jpg">
        <div class='container'>
            <div class='tvtestimonial'>
                <div class='tvcmstestimonial-main-title-wrapper'>
                    {include file='_partials/tvcms-main-title.tpl' main_heading=$main_heading path=$dis_arr_result['path']}
                </div>
                <div class="tvtestimonial-main-wrapper col-lg-6 col-xs-12">
                    {* <div class='tvcms-testimonial-pagination-dots'></div> *}
                    <div class="tvtestimonial-slider-inner">
                        <div class='tvtestimonial-content-box-wrapper'>
                            <div class='tvtestimonial-content-box owl-theme owl-carousel'>
                                {$count = 1}
                                {assign var='styleSheet' value=''}
                                {assign var='index' value=1}
                                {foreach $dis_arr_result['data'] as $key=>$data}
                                {assign var='styleSheet' value=$styleSheet|cat:'.tvcmstestimonial .tvcms-testimonial-pagination-dots .swiper-pagination-bullet:nth-child('|cat:$index|cat:'){background-image:url('|cat:$dis_arr_result.path|cat:$data.image|cat:');}'}
                                {assign var='index' value=$index+1}
                                <div class="item tvtestimonial-wrapper-info tvtestimonial-count-{$count}" data-key="{$key}">
                                    <div class="tvtestimonial-inner-content-box">
                                        <div class="tvtestimonial-img-block">
                                            <div class="tvtestimonial-img-wrapper">
                                                <img src='{$dis_arr_result["path"]}{$data["image"]}' width="100" height="100" alt="" />
                                            </div>
                                        </div>
                                        <div class='tvtestimonial-info-box'>
                                             <div class="tvrattings">
                                                {assign var="rate" value="{$data['rattings']}"}
                                                {$count_review = 0}
                                                {section name="i" start=0 loop=5 step=1}
                                                {if {$rate} le $smarty.section.i.index}
                                                <div class="star"><i class='material-icons'>&#xe838;</i></div>
                                                {else}
                                                <div class="star star_on"><i class='material-icons'>&#xe838;</i></div>
                                                {$count_review = $count_review + 1}
                                                {/if}
                                                {/section}
                                            </div> 
                                            <div class="tvtestimonial-dec">{$data['description']}</div>
                                            <div class="tvtestimonial-title-des">
                                                <div class="tvtestimonial-title"><a href='{$data["link"]}'>{$data['title']}</a></div>
                                                <div class="tvtestimonial-designation">{$data['designation']}</div>
                                                {* <i class='material-icons'>&#xe244;</i> *}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {$count = $count + 1}
                                {/foreach}
                            </div>
                            <style type="text/css">
                            {
                                $styleSheet
                            }
                            </style>
                        </div>
                    </div>
                    {* <div class='tvcms-testimonial-pagination-wrapper'>
                        <div class="tvcms-testimonial-pagination">
                            <div class="tvcms-testimonial-next-pre-btn tvcms-next-pre-btn">
                                <div class="tvtestimonial-prev tvcmsprev-btn"><i class='material-icons'>&#xe5cb;</i></div>
                                <div class="tvtestimonial-next tvcmsnext-btn"><i class='material-icons'>&#xe5cc;</i></div>
                            </div>
                        </div>
                    </div> *}
                </div>
            </div>
        </div>
    </div>
    {/if}
    {/strip}