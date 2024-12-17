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

{block name='head_seo_title'}
    {if isset($meta_title) && $meta_title != ''}
        {$meta_title|escape:'html':'UTF-8'} - {$page.meta.title|escape:'html':'UTF-8'}
    {else}
        {$page.meta.title|escape:'html':'UTF-8'}
    {/if}
{/block}

{if isset($meta_description) && $meta_description != ''}
    {block name='head_seo_description'}{$meta_description|escape:'html':'UTF-8'}{/block}
{/if}

{if isset($post->meta_keywords) && $post->meta_keywords != ''}
    {block name='head_seo_keywords'}{$post->meta_keywords|escape:'html':'UTF-8'}{/block}
{/if}

{block name='head_open_graph'}
    {if isset($meta_title) && $meta_title != ''}
        <meta property="og:title" content="{$meta_title|escape:'html':'UTF-8'}">
    {else}
        <meta property="og:title" content="{$page.meta.title|escape:'html':'UTF-8'}">
    {/if}

    <meta property="og:url" content="https://{$smarty.server.HTTP_HOST|escape:'html':'UTF-8'}{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" />
    <meta property="og:site_name" content="{$shop.name|escape:'html':'UTF-8'}" />
    <meta property="og:type" content="article" />

    {if isset({$post->cover}) && !empty($post->cover)}
        <meta property="og:image" content="{$post->getCoverThumbnailLink()|escape:'html':'UTF-8'}" />
    {else}
        <meta property="og:image" content="{$shop.logo|escape:'html':'UTF-8'}" />
    {/if}

    {if isset($meta_description) && $meta_description != ''}
        <meta property="og:description" content="{$meta_description|escape:'html':'UTF-8'}">
    {else}
        <meta property="og:description" content="{$page.meta.description|escape:'html':'UTF-8'}">
    {/if}
{/block}

{block name="left_column"}
    {include file="module:chatgptcontentgenerator/views/templates/front/sidebar.tpl"}
{/block}

{block name='page_content_container'}

    <header class="blog-post-title page-header">
        <h1>{$post->title|escape:'html':'UTF-8'}</h1>
    </header>
    <div class="blog-single {if !empty($post->cover)}with-cover{else}without-cover{/if} blog-single-{$post->id|intval}">
        <div class="blog-content-hold">
            <div class="blog-extra">
                <div class="blog-latest-toolbar">
                    <span title="Page views" class="blog-latest-toolbar-views">
                        {l s='%count% Views' sprintf=['%count%' => $post->views] d='Modules.Chatgptcontentgenerator.Blogpost'}
                    </span>
                    {if $is_display_time}
                        <span class="post-date">
                            <span class="be-label">{l s='Posted: ' d='Modules.Chatgptcontentgenerator.Blogpost'}</span>
                            <time
                                datetime="{$post->date_add|date_format:'c'|escape:'html':'UTF-8'}">{$post->date_add|date_format:$language.date_format_full|escape:'html':'UTF-8'}</time>
                        </span>
                    {/if}
                </div>
            </div>

            {block name='blog_header_banner'}
                {if $is_cover && $post->cover}
                    <div class="post-featured-image">
                        <img src="{$post->getCoverLink()|escape:'html':'UTF-8'}" alt="{$post->title|escape:'html':'UTF-8'}"
                            class="img-fluid" />
                    </div>
                {/if}
            {/block}

        </div>

        <div class="post-content">
            {$post->content nofilter}
        </div>

        <div id="displayPrestaHomeBlogAfterPostContent">
            {hook h='displayPrestaHomeBlogAfterPostContent'}
        </div>
    </div>



    {if isset($product)}
        <div class="blog-content-relatedproduct">
            {include file="module:chatgptcontentgenerator/views/templates/front/related-product.tpl"}
        </div>
    {/if}

    {if isset($category_products)}
        <div class="container blog-category-products mt-2 mb-2">
            <div class="row m-0">
                <h4>{l s='Related products' d='Modules.Chatgptcontentgenerator.Blogpost'}</h4>
            </div>
            <div class="row m-0">
                <div class="gpt-blog-list">
                {foreach from=$category_products item="product"}
                    {block name='product_miniature'}
                        {include file='catalog/_partials/miniatures/product.tpl' product=$product carousel=true}
                    {/block}
                {/foreach}
                </div>
            </div>
		</div>
    {/if}

    {if isset($recents_posts) && !empty($recents_posts)}
        {include file="module:chatgptcontentgenerator/views/templates/front/recents-posts.tpl" title={l s='Recents posts' d='Modules.Chatgptcontentgenerator.Blogpost'}}
    {/if}

    <script type="application/ld+json">
        {
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => {$post->title|escape:'html':'UTF-8'},
            'image' => [
                {$post->getCoverLink()|escape:'html':'UTF-8'},
            ],
            'datePublished' => "{$post->date_add|date_format:'c'|escape:'html':'UTF-8'}",
            'dateModified' => "{$post->date_upd|date_format:'c'|escape:'html':'UTF-8'}",
            'abstract' => "{$post->short_content|strip_tags|escape:'html':'UTF-8'}",
            'author' => [
                '@type' => 'Organization',
                'name' => {$shop.name|escape:'html':'UTF-8'},
                'url' => {Tools::getShopDomain()|escape:'html':'UTF-8'},
            ],
        }
    </script>

{/block}