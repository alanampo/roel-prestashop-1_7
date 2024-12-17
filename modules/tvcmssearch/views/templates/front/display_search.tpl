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
    <div class="search-widget tvcmsheader-search" data-search-controller-url="{$search_controller_url|escape:'htmlall':'UTF-8'}">
        <div class="tvsearch-top-wrapper">
            <div class="tvheader-sarch-display">
                <div class="tvheader-search-display-icon">
                    <div class="tvsearch-open">
                        <i class='material-icons'>&#xe8b6;</i>
                        {* <svg version="1.1" id="Layer_1" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 30 30" xml:space="preserve">
                            <g>
                                <polygon points="29.245,30 21.475,22.32 22.23,21.552 30,29.232  " />
                                <circle style="fill:#FFD741;" cx="13" cy="13" r="12.1" />
                                <circle style="fill:none;stroke:#000000;stroke-miterlimit:10;" cx="13" cy="13" r="12.5" />
                            </g>
                        </svg> *}
                    </div>
                    <div class="tvsearch-close">
                        <i class='material-icons'>&#xe5cd;</i>
                        {* <svg version="1.1" id="Layer_1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 20 20" xml:space="preserve">
                            <g>
                                <rect x="9.63" y="-3.82" transform="matrix(0.7064 -0.7078 0.7078 0.7064 -4.1427 10.0132)" width="1" height="27.641"></rect>
                            </g>
                            <g>
                                <rect x="9.63" y="-3.82" transform="matrix(-0.7064 -0.7078 0.7078 -0.7064 9.9859 24.1432)" width="1" height="27.641"></rect>
                            </g>
                        </svg> *}
                    </div>
                </div>
            </div>
            <div class="tvsearch-header-display-wrappper tvsearch-header-display-full">
                <form method="get" action="{$search_controller_url|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" name="controller" value="search" />
                    {* <select class="tvcms-select-category">
                        <option value="0">{l s='All' mod='tvcmssearch'}</option>
                        {foreach $options as $option}
                        <option value="{$option['id_category']|escape:'htmlall':'UTF-8'}">{$option['name'] nofilter}</option>
                        {/foreach}
                    </select> *}
                    <div class="tvheader-top-search">
                        <div class="tvheader-top-search-wrapper-info-box">
                            <input type="text" name="s" class='tvcmssearch-words' {* value="{$search_string|escape:'htmlall':'UTF-8'}" *} placeholder="{if Configuration::get('TVCMSCUSTOMSETTING_SEARCH_PLACEHOLDER_TEXT', $language.id)}{Configuration::get('TVCMSCUSTOMSETTING_SEARCH_PLACEHOLDER_TEXT', $language.id)}{/if}" aria-label="{l s='Search' mod='tvcmssearch'}" autocomplete="off" />
                        </div>
                    </div>
                    <div class="tvheader-top-search-wrapper">
                        <button type="submit" class="tvheader-search-btn" aria-label="Search">
                            <i class='material-icons'>&#xe8b6;</i>
                            {* <svg version="1.1" id="Layer_1" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 15 15" style="enable-background:new 0 0 14.5 14.5;" xml:space="preserve">
                                <g id="XMLID_3_">
                                    <g id="XMLID_1_">
                                        <path id="XMLID_15_" style="fill:#222222;" d="M6,1.5c2.5,0,4.5,2,4.5,4.5s-2,4.5-4.5,4.5S1.5,8.5,1.5,6S3.5,1.5,6,1.5 M6,0
                                                C2.7,0,0,2.7,0,6s2.7,6,6,6s6-2.7,6-6S9.3,0,6,0L6,0z" />
                                    </g>
                                    <rect id="XMLID_2_" x="9.3" y="11.3" transform="matrix(0.7071 0.7071 -0.7071 0.7071 12.0186 -5.0156)" style="fill:#222222;" width="5.5" height="1.5" />
                                </g>
                            </svg> 
                            <svg version="1.1" id="Layer_1" x="0px" y="0px" width="22px" height="22px" viewBox="0 0 30 30" xml:space="preserve">
                                <g>
                                    <polygon points="29.245,30 21.475,22.32 22.23,21.552 30,29.232  " />
                                    <circle style="fill:#FFD741;" cx="13" cy="13" r="12.1" />
                                    <circle style="fill:none;stroke:#000000;stroke-miterlimit:10;" cx="13" cy="13" r="12.5" />
                                </g>
                            </svg>*}
                            {* <span class="tvserach-name">{l s='Search' mod='tvcmssearch'}</span> *}
                        </button>
                    </div>
                </form>
                <div class='tvsearch-result'></div>
            </div>
        </div>
    </div>
    {/strip}