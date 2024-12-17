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
    {block name='head_seo'}
    <title>{$meta_title}</title>
    <meta name="description" content="{block name='head_seo_description'}{$meta_description}{/block}">
    <meta name="keywords" content="{block name='head_seo_keywords'}{$meta_keywords}{/block}">
    {/block}
    {extends file='page.tpl'}
    {block name="page_content"}
    <div class="tv-blog-content-wrapper clearfix">
        {* {block name='page_header_container'}
        <div class="tvall-page-top-title">
            <h1 class="tvpage-header-title">{$meta_title}</h1>
        </div>
        {/block} *}
        {if isset($tvcmsblogpost) && !empty($tvcmsblogpost)}
        <section class="tv_blog_post_area">
            <div class="tv_blog_post_inner products row">
                {foreach from=$tvcmsblogpost item=tvcmsblgpst}
                <article id="blog_post" class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12 tvblog_post blog_post_{$tvcmsblgpst.post_format}">
                    <div class="blog_post_content tvblog-event-all-content-block">
                        <div class="post_thumbnail blog_post_content_top">
                            {block name="tvcmsblog_post_thumbnail"}
                            {if $tvcmsblgpst.post_format == 'video'}
                            {assign var="postvideos" value=','|explode:$tvcmsblgpst.video}
                            {if $postvideos|@count > 1 }
                            {assign var="class" value='carousel'}
                            {else}
                            {assign var="class" value=''}
                            {/if}
                            {include file="module:tvcmsblog/views/templates/front/default/post-video.tpl" postvideos=$postvideos width='870' height="482" class=$class blog_id=$tvcmsblgpst.id_tvcmsposts}
                            {elseif $tvcmsblgpst.post_format == 'audio'}
                            {assign var="postaudio" value=','|explode:$tvcmsblgpst.audio}
                            {if $postaudio|@count > 1 }
                            {assign var="class" value='carousel'}
                            {else}
                            {assign var="class" value=''}
                            {/if}
                            {include file="module:tvcmsblog/views/templates/front/default/post-audio.tpl" postaudio=$postaudio class=$class blog_id=$tvcmsblgpst.id_tvcmsposts}
                            {elseif $tvcmsblgpst.post_format == 'gallery'}
                            {if $tvcmsblgpst.gallery_lists|@count > 1 }
                            {assign var="class" value='carousel'}
                            {else}
                            {assign var="class" value=''}
                            {/if}
                            {include file="module:tvcmsblog/views/templates/front/default/post-gallery.tpl" gallery_lists=$tvcmsblgpst.gallery_lists imagesize="medium" class=$class blog_id=$tvcmsblgpst.id_tvcmsposts}
                            {else}
                            <div class="tvnews-event-hoverbtn ">
                                {* <img class="img-responsive tvblog-balance-height" src="{$tvcmsblgpst.post_img_medium}" alt="{$tvcmsblgpst.post_title}" width="{$mediumwidth}" height="{$mediumheight}"> *}
                                <picture>
                                    <source srcset="{$tvcmsblgpst.post_img_medium_res}" media="(min-width: 500px) and (max-width: 768px)">
                                    <img class="img-responsive tvblog-balance-height" src="{$tvcmsblgpst.post_img_medium}" alt="{$tvcmsblgpst.post_title}" width="{$mediumwidth}" height="{$mediumheight}" loading="lazy">
                                </picture>
                                <div class="tvnews-event-overly"></div>
                                <div class="tvnews-event-buttons">
                                    <a href="{$tvcmsblgpst.link}">
                                        <i class='material-icons'>&#xe8b6;</i>
                                    </a>
                                </div>
                            </div>
                            {* <div class="blog_mask">
                                <div class="blog_mask_content">
                                    <a class="thumbnail_lightbox" href="{$tvcmsblgpst.post_img_medium}">
                                        <i class='material-icons'>&#xe145;</i>
                                    </a>
                                </div>
                            </div> *}
                            {/if}
                            {/block}
                        </div>
                        <div class="post_content tvnews-event-content-wrapper">
                            <div class="clearfix tvblog-date-username">
                                <div class="post-meta">
                                    <div class="meta-author tvnews-event-username">
                                        <i class='material-icons'>&#xe7fd;</i>
                                        <p>{l s='' mod='tvcmsblog'} {$tvcmsblgpst.post_author_arr.firstname} {$tvcmsblgpst.post_author_arr.lastname}</p>
                                    </div>
                                    <div class="date_time tvdate-time">
                                        {* <span class="tvdate-time-icon"></span> *}
                                        <i class='material-icons'>&#xe878;</i>
                                        {$tvcmsblgpst.post_date|date_format:'<p class="tvmonth-time">%b</p><p class="day_time tvday-time">%d,</p><p class="tvyear-time">%Y</p>' nofilter}
                                    </div>
                                    {* <div class="tv-author-comment">
                                        <i class="material-icons">&#xe0b9;</i>
                                        <p>{$tvcmsblgpst.total_comments}&nbsp;
                                            {if $tvcmsblgpst.total_comments == 0 || $tvcmsblgpst.total_comments > 1}
                                            {l s='Comments' mod='tvcmsblog'}
                                            {else}
                                            {l s='Comment' mod='tvcmsblog'}
                                            {/if}
                                        </p>
                                    </div> *}
                                    {* <p class="meta_category">
                                        <a href="{$tvcmsblgpst.category_arr.link}">{$tvcmsblgpst.category_arr.name}</a>
                                    </p> *}
                                </div>
                            </div>
                            <div class="tvnews-event-titel">
                                <a href="{$tvcmsblgpst.link}" class="post_title">
                                    <h3>{$tvcmsblgpst.post_title}</h3>
                                </a>
                            </div>
                            <div class="tvnews-event-description">
                                {if isset($tvcmsblgpst.post_excerpt) && !empty($tvcmsblgpst.post_excerpt)}
                                {$tvcmsblgpst.post_excerpt|truncate:500:'...'|escape:'html':'UTF-8'}
                                {else}
                                {$tvcmsblgpst.post_content|truncate:400:'...'|escape:'html':'UTF-8'}
                                {/if}
                            </div>
                            {*<div class='tvnews-event-read-more'>
                                <div class='tvnews-event-read-more-link'>
                                    <a href="{$tvcmsblgpst.link}">
                                        {l s='Read More' mod='tvcmsblog'}
                                        <i class='material-icons'>&#xe5cc;</i>
                                    </a>
                                </div>
                            </div>*}
                        </div>
                    </div>
                </article>
                {/foreach}
            </div>
        </section>
        {/if}
    </div>
    {include file="module:tvcmsblog/views/templates/front/default/pagination.tpl"}
    {/block}
    {block name='breadcrumb'}
    <div class="breadcrumb_container">
        <nav data-depth="{$breadcrumb.count+2}" class="breadcrumb">
            <div class="container">
                <ol itemscope itemtype="http://schema.org/BreadcrumbList">
                    {foreach from=$breadcrumb.links item=path name=breadcrumb}
                    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                        <a itemprop="item" href="{$path.url}">
                            <span itemprop="name">{$path.title}</span>
                        </a>
                        <meta itemprop="position" content="{$smarty.foreach.breadcrumb.iteration}">
                    </li>
                    {/foreach}
                </ol>
            </div>
        </nav>
    </div>
    {/block}
    {/strip}