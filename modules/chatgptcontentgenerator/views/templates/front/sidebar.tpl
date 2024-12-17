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

<div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
    <div class="blog_block_search">
        <form
            action="{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghome')|escape:'html':'UTF-8'}"
            method="post">
            <input class="form-control" type="text" name="blog_search"
                placeholder="{l s='Blog search' d='Modules.Chatgptcontentgenerator.Bloghome'}"
                value="{if isset($is_search) && $is_search}{$blog_search|escape:'html':'UTF-8'}{/if}">
            <input class="button" type="submit" value="{l s='Search' d='Modules.Chatgptcontentgenerator.Bloghome'}">
            <span class="icon_search"><i class="material-icons search" aria-hidden="true"></i></span>
        </form>

        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "WebSite",
                "url": "{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghome')|escape:'html':'UTF-8'}",
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "{Context::getContext()->link->getModuleLink('chatgptcontentgenerator', 'bloghome')|escape:'html':'UTF-8'}?blog_search={literal}{search_term_string}{/literal}"
                    },
                    "query-input": "required name=search_term_string"
                }
            }
        </script>
    </div>

    {if isset($popular_posts) && count($popular_posts)}
        <div class="blog_block_popural mt-2">
            <div class="blog-column">
                {foreach from=$popular_posts item=post}
                    {include file="module:chatgptcontentgenerator/views/templates/front/post.tpl" post=$post is_cover=false}
                {/foreach}
            </div>
        </div>
    {/if}
</div>