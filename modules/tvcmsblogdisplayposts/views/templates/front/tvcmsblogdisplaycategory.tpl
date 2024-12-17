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
{if ($page.page_name == 'module-tvcmsblog-archive' || $page.page_name == 'module-tvcmsblog-single') && !empty($categories)}
    <div id="search_filters_blog_category">
        <div class="title hidden-md-up" data-target="#facet_blog_category" data-toggle="collapse">
            <div class="tvmobile-view-blog-title facet-title">{l s='Blog Category' mod='tvcmsblogdisplayposts'}</div>
            <span class="float-xs-right">
                <span class="navbar-toggler collapse-icons">
                    <i class='material-icons add'>&#xe313;</i>
                    <i class='material-icons remove'>&#xe316;</i>
                </span>
            </span>
        </div>
        <section class="facet collapse" id="facet_blog_category">
            <div class="tvblog-category-title facet-label hidden-sm">
                <div class="left-penal-tvblog-category-wrapper">
                    <span>{l s='Blog Category' mod='tvcmsblogdisplayposts'}</span>
                </div>
            </div>
            <div class="tvleft-penal-blog-category">
                <ul>
                    {foreach $categories as $category}
                        <li class="facet-label">
                            <a href="{$category.path}" title="{$category.title}">{$category.title}</small></a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </section>
    </div>
{/if}
{/strip}