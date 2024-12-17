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
    <title>{$tvcmsblogpost.post_title}</title>
    <meta name="description" content="{block name='head_seo_description'}{$meta_description}{/block}">
    <meta name="keywords" content="{block name='head_seo_keywords'}{$meta_keywords}{/block}">
    {/block}
    {extends file='page.tpl'}
    {block name="page_content"}
    <div class="tv_blog_post_area single">
        <div class="tv_blog_post_inner">
            <article id="blog_post" class="blog_post blog_post_{$tvcmsblogpost.post_format} clearfix">
                <div class="blog_post_content">
                    <div class="post_content col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h3 class="post_title">{$tvcmsblogpost.post_title}</h3>
                        <div class="post_meta clearfix">
                            <p class="meta_author">
                                {* {l s='Posted by ' mod='tvcmsblog'} *}
                                <i class="material-icons">&#xe7fd;</i>
                                <span>{$tvcmsblogpost.post_author_arr.firstname} {$tvcmsblogpost.post_author_arr.lastname}</span>
                            </p>
                            <p class="meta_date">
                                <i class='material-icons'>&#xe916;</i>
                                {$tvcmsblogpost.post_date|date_format:"%b %d, %Y"}
                            </p>
                            <p class="meta_category">
                                <a href="{$tvcmsblogpost.category_arr.link}">{$tvcmsblogpost.category_arr.name}</a>
                            </p>
                            <span class="tvcmsblog-view-count">
                                <i class="material-icons">&#xe8f4;</i>
                                <span>{l s='Views :' mod='tvcmsblog'}</span>
                                <span class="post-view-count">&nbsp;{$blog_view}</span>
                            </span>
                            <div class="tv-author-comment">
                                <i class="material-icons">&#xe0b9;</i>
                                <p>
                                    {if $tvcmsblgpst.total_comments == 0 || $tvcmsblgpst.total_comments > 1}
                                    {l s='Comments : ' mod='tvcmsblog'}
                                    {else}
                                    {l s='Comment : ' mod='tvcmsblog'}
                                    {/if}
                                    {$tvcmsblog_total_commets}
                                </p>
                            </div>
                        </div>
                        <div class="blog_post_content_top {* col-xl-5 col-lg-12 col-md-12 col-sm-12 col-xs-12 *}">
                            <div class="post_thumbnail">
                                {if $tvcmsblogpost.post_format == 'video'}
                                {assign var="postvideos" value=','|explode:$tvcmsblogpost.video}
                                {include file="module:tvcmsblog/views/templates/front/default/post-video.tpl" postvideos=$postvideos width='870' height="482" blog_id=$tvcmsblogpost.id_tvcmsposts}
                                {elseif $tvcmsblogpost.post_format == 'gallery'}
                                {include file="module:tvcmsblog/views/templates/front/default/post-gallery.tpl" gallery_lists=$tvcmsblogpost.gallery_lists imagesize="large" blog_id=$tvcmsblogpost.id_tvcmsposts}
                                {else}
                                {* <img class="tvcmsblog_img img-responsive" src="{$tvcmsblogpost.post_img_large}" alt="{$tvcmsblogpost.post_title}" width="{$lagewidth}" height="{$lageheight}"> *}
                                <picture>
                                    <source srcset="{$tvcmsblogpost.post_img_large_res}" media="(min-width: 500px) and (max-width: 768px)">
                                    <img class="tvcmsblog_img img-responsive" src="{$tvcmsblogpost.post_img_large}" alt="{$tvcmsblogpost.post_title}" width="{$lagewidth}" height="{$lageheight}" loading="lazy">
                                </picture>
                                {/if}
                            </div>
                        </div>
                        <div class="post_description cms-description">
                            <p>{$tvcmsblogpost.post_content nofilter}</p>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
    {if ($tvcmsblogpost.comment_status == 'open') || ($tvcmsblogpost.comment_status == 'close')}
    {include file="module:tvcmsblog/views/templates/front/default/comment-list.tpl"}
    {/if}
    {if (isset($disable_blog_com) && $disable_blog_com == 1) && ($tvcmsblogpost.comment_status == 'open')}
    {include file="module:tvcmsblog/views/templates/front/default/comment.tpl"}
    {/if}
    {/block}
    {block name='breadcrumb'}
    <div class="breadcrumb_container">
        <div class="container">
            <nav data-depth="{$breadcrumb.count+1}" class="breadcrumb">
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
            </nav>
        </div>
    </div>
    {/block}
    {/strip}