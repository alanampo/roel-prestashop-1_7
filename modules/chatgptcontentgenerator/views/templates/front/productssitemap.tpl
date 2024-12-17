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

{block name='page_title'}
    <span class="sitemap-title">{l s='Spin Off Sitemap' d='Modules.Chatgptcontentgenerator.Sitemap'}</span>
{/block}

{block name='page_content_container'}
    {if $spinOffLinks}
        {block name='page_content_top'}
            <div class="container-fluid page-content-top">
                <div class="row col-xs-12">
                    {foreach $spinOffLinks as $letter => $spinOffLetter}
                        {if $spinOffLetter}
                            <a class="page-content-top_link" href="#spin_off_letter_{$letter|escape:'html':'UTF-8'}" title="{$letter|escape:'html':'UTF-8'}">
                                {$letter|escape:'html':'UTF-8'}
                            </a>
                        {/if}
                    {/foreach}
                </div>
            </div>
        {/block}

        {block name='page_content'}
            <div class="container-fluid">
                <div class="row sitemap col-md-12">
                    {foreach $spinOffLinks as $letter => $spinOffLetter}
                        {if $spinOffLetter}
                            <div class="col-md-4" id="spin_off_letter_{$letter|escape:'html':'UTF-8'}">
                                <h2>{$letter|escape:'html':'UTF-8'}</h2>
                                <ul>
                                    {foreach $spinOffLetter as $spinOff}
                                        <li>
                                            <a id="{$spinOff.id_spinoff|escape:'html':'UTF-8'}" href="{$spinOff.url|escape:'html':'UTF-8'}" title="{$spinOff.name|escape:'html':'UTF-8'}">
                                                {$spinOff.name|escape:'html':'UTF-8'}
                                            </a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
        {/block}
    {/if}
{/block}
