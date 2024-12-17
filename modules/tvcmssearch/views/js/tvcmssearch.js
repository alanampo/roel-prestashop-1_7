/**
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
 */

/* global $ */
$(document).ready(function() {
    var $searchWidget = $('.tvcmsheader-search');
    var $searchBox = $searchWidget.find('input[type=text]');
    // var searchURL     = $searchWidget.attr('data-search-controller-url');
    var searchURL = baseDir + 'modules/tvcmsproductcompare/ajax.php';

    $(document).on('change', '.tvcms-select-category', function() {
        $(this).find('option').removeClass('selected');
        $(this).find('option:selected').addClass('selected');
    });
    $(document).on('focusout', '.tvsearch-top-wrapper .tvheader-top-search .tvcmssearch-words', function() {
        var obj = $(this).parent('tvsearch-header-display-wrappper').find('.tvsearch-result');
    });

    $(document).on('keyup', '.tvcmsheader-search .tvsearch-header-display-wrappper .tvheader-top-search .tvheader-top-search-wrapper-info-box .tvcmssearch-words', function() {
        var obj = $(this).parent().parent().parent().parent().find('.tvsearch-result');
        obj.html('');
        obj.show();

        var search_words = $(this).val();
        var cat_id = $('.tvcms-select-category').find('.selected').val();

        if (search_words.length != 0) {
            $.ajax({
                type: 'POST',
                url: baseDir + 'modules/tvcmssearch/ajax.php?',
                cache: false,
                data: 'search_words=' + search_words + '&category_id=' + cat_id + ' &token=' + static_token,
                success: function(data) {
                    obj.html('');
                    obj.append(data);
                    if (data != '') {
                        $('body').addClass('search-open');
                    } else {
                        $('body').removeClass('search-open');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        } else {
            $('body').removeClass('search-open');
        }
    });
    $(document).on('click', '.tvsearch-result .tvsearch-dropdown-close , .half-wrapper-backdrop', function() {
        $('.tvsearch-result').html('');
        $('.tvcmssearch-words').val('');
        $('body').removeClass('search-open');
    });
    $('body').keydown(function(e) {
        if (e.which == 27) {
            $('.tvsearch-result').html('');
            $('.tvcmssearch-words').val('');
            $('body').removeClass('search-open');
        }
    });
    $(document).on('click', '.tvsearch-result .tvsearch-more-search', function() {
        $(this).parent().parent().parent().parent().find('.tvheader-top-search-wrapper button').click();
    });


    /********************* Start Search DropDown js *****************************************/
    $('#header .tvheader-sarch-display .tvsearch-close').hide();
    $(document).on('click', '#header .tvheader-sarch-display .tvsearch-open', function() {
        removeDefaultDropdownSearch();
        $('#header .tvheader-sarch-display .tvsearch-open').hide();
        $('#header .tvheader-sarch-display .tvsearch-close').show();
        $('#header .tvsearch-header-display-wrappper').addClass('open');
        $('body').addClass('tvactive-search');
    });
    $(document).on('click', '#header .tvsearch-close', function() {
        $('#header .tvheader-sarch-display .tvsearch-open').show();
        $('#header .tvheader-sarch-display .tvsearch-close').hide();

        $('#header .tvcmssearch-words').val('');
        $(this).parent().parent().parent().find('.tvsearch-result').html('');

        $('#header .tvsearch-header-display-wrappper').removeClass('open');
        $('body').removeClass('tvactive-search');
    });
    /********************* End Search DropDown js *****************************************/


    // close dropdown When open other dropdown in mobile view
    function removeDefaultDropdownSearch() {
        // Header My Account Dropdown
        $('#header .tv-account-dropdown').removeClass('open');
        $('#header').find('.tvcms-header-myaccount .tv-myaccount-btn').removeClass('open');
        $('#header').find('.tvcms-header-myaccount .tv-account-dropdown').removeClass('open').hide();

        // Header Search Dropdown
        // $('#header .tvcmsheader-search .tvsearch-open').show();
        // $('#header .tvcmsheader-search .tvsearch-close').hide();
        // $('#header .tvcmsheader-search .tvsearch-header-display-wrappper').removeClass('open');
        // $('body').removeClass('tvactive-search');

        // Header My Account Dropdown
        $('#header .tv-account-dropdown').removeClass('open');
        $('#header').find('.tvcms-header-myaccount .tv-myaccount-btn').removeClass('open');
        $('#header').find('.tvcms-header-myaccount .tv-account-dropdown').removeClass('open').hide();

        if (document.body.clientWidth <= mobileViewSize) {
            // horizontal menu
            $('#tvcms-mobile-view-header .tvmenu-button').removeClass('open');
            $('#tvcmsmobile-horizontal-menu #tv-top-menu').removeClass('open');

            // Cart Dropdown
            $('.hexcms-header-cart .tvcmscart-show-dropdown').removeClass('open');

            // Vertical Menu DropDown
            // $('.tvcmsvertical-menu .tvallcategories-wrapper .tvleft-right-title-toggle, .tvcmsvertical-menu .tvverticalmenu-dropdown').removeClass('open');
        } else {
            // Vertical Menu DropDown
            $('.tvcmsvertical-menu .tvallcategories-wrapper').removeClass('open');
            $('.tvcmsvertical-menu .tvverticalmenu-dropdown').removeClass('open').removeAttr('style');
        }
    }


});