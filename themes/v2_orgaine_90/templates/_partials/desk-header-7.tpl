{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
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
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
    {strip}
    <div class="tvcmsdesktop-top-header-wrapper header-7" data-header-layout="7">
        <div class='container-fluid tvcmsdesktop-top-header'>
            <div class="container tvcmsdesktop-top-wrapper">
                <div class='tvheader-language-currency-wrapper col-xl-6 col-lg-6 col-md-6 col-sm-12'>
                    <div class="tvheader-language">{if $withData}{hook h='displayNavLanguageBlock'}{/if}</div>
                    <div class="tvheader-currency">{if $withData}{hook h='displayNavCurrencyBlock'}{/if}</div>
                </div>
                <div class='tvheader-offer-wrapper col-xl-6 col-lg-6 col-md-6 col-sm-12 tvforce-hide'>
                    {hook h='displayTopOfferText'}
                </div>
                <div class="tvheader-wishlist-compare-wrapper col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="tvheader-compare">{if $withData}{hook h='displayNavProductCompareBlock'}{/if}</div>
                    <div class="ttvcms-wishlist-icon">{if $withData}{hook h='displayNavWishlistBlock'}{/if}</div>
                    <div id='tvcmsdesktop-account-button'>
                        {if $withData}
                        <div class="tv-header-account tv-account-wrapper tvcms-header-myaccount">
                            <button class="btn-unstyle tv-myaccount-btn tv-myaccount-btn-desktop" name="User Icon" aria-label="User Icon">
                                <i class="material-icons person">&#xe7fd;</i>
                                {if $customer.is_logged }
                                <span class="tvcms_customer_name">{$customer.gender.name[$customer.gender.id]} {$customer.firstname} {$customer.lastname}</span>
                                {else}
                                <span>{l s='Login Or Register' d='Shop.Theme.Catalog'}</span>
                                {/if}
                            </button>
                            <ul class="dropdown-menu tv-account-dropdown tv-dropdown">
                                {if $customer.is_logged}
                                <li class="tvcms-signin"><a href="{$urls.pages.my_account}" class="tvmyccount"><i class="material-icons">&#xe7fd;</i>{l s='My Account' d='Shop.Theme.Catalog'}</a></li>
                                {/if}
                                {* <li>{hook h='displayNavWishlistBlock'}</li>
                                <li>{hook h='displayNavProductCompareBlock'}</li> *}
                                <li>{hook h='displayNavCustomerSignInBlock'}</li>
                                <li class="ttvcms-wishlist-icon hidden-lg-up">{if $withData}{hook h='displayNavWishlistBlock'}{/if}</li>
                                <li class="tvheader-compare hidden-lg-up">{if $withData}{hook h='displayNavProductCompareBlock'}{/if}</li>
                                <li class="tvheader-language hidden-lg-up">{if $withData}{hook h='displayNavLanguageBlock'}{/if}</li>
                                <li class="tvheader-currency hidden-lg-up">{if $withData}{hook h='displayNavCurrencyBlock'}{/if}</li>
                            </ul>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        <div class='container-fluid tvcmsdesktop-top-second hidden-md-down'>
            <div class="container">
                <div class="row tvcmsdesktop-top-header-box">
                    <div class='col-sm-12 col-md-4 col-lg-4 col-xl-4 tvcms-header-logo-wrapper'>
                        <div class="hidden-sm-down tvcms-header-logo" id="tvcmsdesktop-logo">
                            {if $withData}
                            <div class="tv-header-logo">
                                <a href="{$urls.base_url}">
                                    <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}" loading="lazy" height="35" width="201">
                                </a>
                            </div>
                            {/if}
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 tvcmsheader-search">
                        <div class='tvcmssearch-wrapper' id="_desktop_search">
                            {if $withData}
                            {hook h='displayNavSearchBlock'}
                            {/if}
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 tvcmsheader-nav-right">
                        <div class="tvcmsdesktop-contact ">{if $withData}{hook h='displayNav1'}{/if}</div>
                        <div class="tv-contact-account-cart-wrapper">
                            <div id="_desktop_cart_manage" class="tvcms-header-cart">
                                {if $withData}
                                {hook h='displayNavShoppingCartBlock'}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tvcms-header-menu-offer-wrapper tvcmsheader-sticky">
            <div class="position-static tvcms-header-menu">
                <div class='tvcmsmain-menu-wrapper container'>
                    <div id='tvdesktop-megamenu'>
                        {if $withData}
                        {hook h='displayMegamenu'}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/strip}