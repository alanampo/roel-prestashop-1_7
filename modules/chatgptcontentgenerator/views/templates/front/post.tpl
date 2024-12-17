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

<div class="blog-list-post post-wrapper">
    {if $is_cover}
        <a class="blog_item_img" href="{$post->getLink()|escape:'html':'UTF-8'}">
            <img title="{$post->title|escape:'html':'UTF-8'}" src="{$post->getCoverThumbnailLink()|escape:'html':'UTF-8'}" alt="{$post->title|escape:'html':'UTF-8'}">
        </a>
    {/if}
    <div class="blog-wrapper-content">
        <div class="blog-wrapper-content-main">
            <a class="blog-title-block" href="{$post->getLink()|escape:'html':'UTF-8'}">{$post->title|truncate|escape:'html':'UTF-8'}</a>
            <div class="blog-latest-toolbar">
                <span class="blog-latest-toolbar-views"
                    title="{l s='Page views' d='Modules.Chatgptcontentgenerator.Post'}">
                    {l s='%count% Views' sprintf=['%count%' => $post->views] d='Modules.Chatgptcontentgenerator.Post'}
                </span>
                {if $is_display_time}
                    <span class="post-date">
                        <span class="be-label"></span>
                        <time
                            datetime="{$post->date_add|date_format:'c'|escape:'html':'UTF-8'}">{$post->date_add|date_format:$language.date_format_full|escape:'html':'UTF-8'}</time>
                    </span>
                {/if}
            </div>
            {if $is_shortdescription}
                <div class="blog_description">
                    <p>{$post->short_content|strip_tags|truncate:140|escape:'html':'UTF-8'}</p>
                </div>
            {/if}
        </div>
    </div>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Article",
            "headline": "{$post->title|escape:'html':'UTF-8'}",
            "image": [
                "{$post->getCoverLink()|escape:'html':'UTF-8'}"
            ],
            "datePublished": "{$post->date_add|date_format:'c'|escape:'html':'UTF-8'}",
            "dateModified": "{$post->date_upd|date_format:'c'|escape:'html':'UTF-8'}",
            "abstract": "{$post->short_content|strip_tags|escape:'html':'UTF-8'}",
            "author": [{
                "@type": "Organization",
                "name": "{$shop.name|escape:'html':'UTF-8'}",
                "url": "{Tools::getShopDomain()|escape:'html':'UTF-8'}"
            }]
        }
    </script>
</div>