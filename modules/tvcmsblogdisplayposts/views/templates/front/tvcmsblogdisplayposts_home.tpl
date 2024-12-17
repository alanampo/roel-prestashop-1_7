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
    <div class="tvcmsblog-event tvcmsblog-event-home container-fluid wow fadeInUp">
        <div class="tvblog-event container">
            <div class="home_blog_post_area product_block_container tvblog-full-width tvnews-event wow zoomIn">
                <div class="tvnews-event-wrapper tvall-block-box-shadows">
                    {include file='_partials/tvcms-main-title.tpl' main_heading=$main_heading path=$dis_arr_result['path']}
                    <div class="tvblog-event-all-block">
                        <div class="tvblog-event-inner-block">
                            <div class="tvnews-wrapper-info-box {* owl-theme owl-carousel *}">
                                {$count = 1}
                                {if $dis_arr_result['status']}
                                {foreach from=$dis_arr_result['data'] item=tvcmsblgpst}
                                {if $count <= 3} <div class="{* item *} tvblog-event-all-content-block col-xl-4 col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <article class="blog_pos tvblog-img-content-wrapper clearfix">
                                        <div class="blog_post_content_top tvblog-img-block">
                                            <div class="post_thumbnail">
                                                {if $tvcmsblgpst.post_format == 'video'}
                                                {assign var="postvideos" value=','|explode:$tvcmsblgpst.video}
                                                {include file="module:tvcmsblogdisplayposts/views/templates/front/post-video.tpl" videos=$postvideos width='370' height="256" firstname=$tvcmsblgpst.post_author_arr.firstname lastname=$tvcmsblgpst.post_author_arr.lastname}
                                                {elseif $tvcmsblgpst.post_format == 'gallery'}
                                                {include file="module:tvcmsblogdisplayposts/views/templates/front/post-gallery.tpl" gallery=$tvcmsblgpst.gallery_lists firstname=$tvcmsblgpst.post_author_arr.firstname lastname=$tvcmsblgpst.post_author_arr.lastname imagesize='medium' link=$tvcmsblgpst.link}
                                                {else}
                                                <a href="{$tvcmsblgpst.link}" class="img_content">
                                                    <div class="tvnews-event-hoverbtn">
                                                        <div class="tvblog-content-img">
                                                            {* <img src="{$tvcmsblgpst.post_img_small}" alt="{$tvcmsblgpst.post_title}" class="tv-img-responsive"> *}
                                                            <picture>
                                                                <source srcset="{$tvcmsblgpst.post_img_small_res}" media="(min-width: 500px) and (max-width: 768px)">
                                                                <img src="{$tvcmsblgpst.post_img_small}" alt="{$tvcmsblgpst.post_title}" width="{$smallwidthLeft}" height="{$smallheightLeft}" loading="lazy">
                                                            </picture>
                                                        </div>
                                                        <!-- <div class="tvnews-event-overly"></div> -->
                                                        <div class="tvnews-event-buttons">
                                                            <i class='material-icons'>&#xe8b6;</i>
                                                        </div>
                                                    </div>
                                                </a>
                                                {*<div class="blog_mask">
                                                    <div class="blog_mask_content">
                                                        <a class="thumbnail_lightbox" href="{$tvcmsblgpst.post_img_small}" target="_blank">
                                                        </a>
                                                    </div>
                                                </div>*}
                                                {/if}
                                            </div>
                                        </div>
                                        <div class="post_content tvnews-event-content-wrapper">
                                            <div class="tv-event-content">
                                                <div class='tvblog-date-username'>
                                                    <div class="post_meta">
                                                        <div class="meta-author tvnews-event-username">
                                                            {* <i class='material-icons'>&#xe7fd;</i> *}
                                                            <p>{l s='By' mod='tvcmsblogdisplayposts'} {$tvcmsblgpst.post_author_arr.firstname} {$tvcmsblgpst.post_author_arr.lastname}</p>
                                                        </div>
                                                        {* <div class="tv-author-comment">
                                                            <i class="material-icons">&#xe0b9;</i>
                                                            
                                                            <p>{$tvcmsblgpst.total_comments}&nbsp;
                                                                {if $tvcmsblgpst.total_comments == 0 || $tvcmsblgpst.total_comments > 1}
                                                                {l s='Comments' mod='tvcmsblogdisplayposts'}
                                                                {else}
                                                                {l s='Comment' mod='tvcmsblogdisplayposts'}
                                                                {/if}
                                                            </p>
                                                        </div> *}
                                                        <div class="date_time tvdate-time">
                                                            {* <span class="tvdate-time-icon"></span> *}
                                                            <i class='material-icons'>&#xe878;</i>
                                                            {$tvcmsblgpst.post_date|date_format:'<p class="tvmonth-time">%b</p><p class="day_time tvday-time">%d,</p><p class="tvyear-time">%Y</p>' nofilter}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tvnews-event-titel">
                                                    <h3><a href="{$tvcmsblgpst.link}" class="post_title">{$tvcmsblgpst.post_title}</a></h3>
                                                </div>
                                                <p class="post_description tvnews-event-description">
                                                    {if isset($tvcmsblgpst.post_excerpt) && !empty($tvcmsblgpst.post_excerpt)} {$tvcmsblgpst.post_excerpt|truncate:150:' ...'|escape:'html':'UTF-8'} {else} {$tvcmsblgpst.post_content|truncate:150:' ...'|escape:'html':'UTF-8'} {/if}
                                                </p>
                                                {* <div class='tvnews-event-read-more'>
                                                    <div class='tvnews-event-read-more-link'>
                                                        <a href="{$tvcmsblgpst.link}">
                                                            {l s='Read More' mod='tvcmsblogdisplayposts'}
                                                             <i class='material-icons'>&#xe5cc;</i>
                                                        </a>
                                                    </div>
                                                </div> *}
                                            </div>
                                        </div>
                                    </article>
                            </div>
                            {/if}
                            {$count = $count + 1}
                            {/foreach}
                            {else}
                            <p>{l s='No Blog Post Found' mod='tvcmsblogdisplayposts'}</p>
                            {/if}
                        </div>
                    </div>
                    {* <div class='tvcmsblog-event-home-pagination-wrapper'>
                        <div class="tvcmsblog-event-home-pagination">
                            <div class="tvcmsblog-event-home-next-pre-btn">
                                <div class="tvcmsblog-event-home-slider-prev tvcmsprev-btn">
                                    <i class='material-icons'>&#xe314;</i>
                                    <span>{l s='Prev' mod='tvcmsblogdisplayposts'}</span>
                                </div>
                                <div class="tvcmsblog-event-home-slider-next tvcmsnext-btn">
                                    <i class='material-icons'>&#xe315;</i>
                                    <span>{l s='Next' mod='tvcmsblogdisplayposts'}</span>
                                </div>
                            </div>
                        </div>
                    </div> *}
                </div>
            </div>
            <div class="tvnews-event-link">
                <a href="{TvcmsBlog::tvcmsBlogLink()}" class="">{l s='All blogs' mod='tvcmsblogdisplayposts'}<i class='material-icons'>&#xe315;</i></a>
            </div>
        </div>
    </div>
    </div>
    {/if}
    {/strip}