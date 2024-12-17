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
var storage;
var langId = document.getElementsByTagName("html")[0].getAttribute("lang");
var currentMegaMenuModule = currentThemeName + "_mega_menu_" + langId;
var dataCachem;
jQuery(document).ready(function($) {
    storage = $.localStorage;
    function storageGet(key) {
        return "" + storage.get(currentMegaMenuModule + key);
    }
    function storageSet(key, value) {
        storage.set(currentMegaMenuModule + key, value);
    }
    function storageClear(key) {
        storage.remove(currentMegaMenuModule + key);
    }
    var isCallMenu = false;
    var getMegaMenuAjax = function() {
        if (!isCallMenu) {
            /*****Load Cache*****/
            var data = storageGet("");
            dataCachem = data;
            storageClear("");
            if (data != '' && data != 'null'  && data !== 'undefined') {
                if (document.body.clientWidth <= 991) {
                    $('#tvmobile-megamenu').html(data);
                } else {
                    $('#tvdesktop-megamenu').html(data);
                }
                megaMenuSlider();
            }
            /*****Load Cache*****/
            $.ajax({
                type: 'POST',
                url: gettvcmsmegamenulink,
                success: function(data) {
                    //var dataCachem = storageGet("");
                    //storageSet("", data);
                    if (dataCachem === '' || dataCachem === 'null' || dataCachem === 'undefined') {
                        if (document.body.clientWidth <= 991) {
                            $('#tvmobile-megamenu').html(data);
                            //customImgLazyLoad('#tvmobile-megamenu');
                        } else {
                            $('#tvdesktop-megamenu').html(data);
                            //customImgLazyLoad('#tvdesktop-megamenu');
                        }
                        megaMenuSlider();
                    }
                    dataCachem = data;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
        isCallMenu = true;
    }
    $(document).on('click', '.tv-menu-horizontal .icon-drop-mobile', function() {
        $(this).next().toggle(100);
        $(this).toggleClass('opened');
        //customImgLazyLoad('#tvdesktop-megamenu');
    });
    $(window).resize(function() {
        megaMenuSlider();
    });
    if (document.body.clientWidth > 991) {
        getMegaMenuAjax();
    }
    // megaMenuSlider();
    window.addEventListener("beforeunload", function (e) {
      storageSet("", dataCachem);
      return '';
    });
    //*************************start top-menu js *************************/
    function responsiveMenuPopup($this) {
        if (document.body.clientWidth > 991) {
            var wrapWidthPopup = $($this).find('.tv-sub-menu').outerWidth(true);
            if (wrapWidthPopup !== null && wrapWidthPopup !== undefined) {
                var posliWidth = $($this).offset();
                var menuLeft = posliWidth.left;
                var menuWidth = $('#header').width();//.tvcms-header-menu
                if ($('body').hasClass('lang-rtl')) {
                    menuLeft = (menuWidth - (menuLeft)); //make right offset
                }
                var posWraper = $('.tvcms-header-menu').offset();
                var pos = $($this).offset();
                var xLeft = 0;
                if ((menuLeft + wrapWidthPopup) > menuWidth) {
                    xLeft = (menuLeft + wrapWidthPopup) - menuWidth;
                    if (menuLeft > ((menuLeft - xLeft) - 152)) {
                        xLeft = ((menuLeft - xLeft) - 152);
                    }
                    /*if(xLeft > 0){
                        xLeft = 0;
                    }*/
                    if(menuWidth => 991 && menuWidth <= 1024 && xLeft < 0){//small device
                        xLeft = 0;
                        if ($('body').hasClass('lang-rtl')) {
                            $($this).find('.tv-sub-menu.menu-dropdown').css('left', xLeft);
                        } else {
                            $($this).find('.tv-sub-menu.menu-dropdown').css('right', xLeft);
                        }
                    }else{
                        if ($('body').hasClass('lang-rtl')) {
                            $($this).find('.tv-sub-menu.menu-dropdown').css('right', xLeft);
                        } else {
                            $($this).find('.tv-sub-menu.menu-dropdown').css('left', xLeft);
                        }
                    }
                }
            }
        }
    }
    $(document).on('touchstart mouseover', '.container_tv_megamenu ul.menu-content li.level-1', function(e) {
        responsiveMenuPopup(this);
    });
    //************************************end Top-menu js******************************************************/
    function megaMenuSlider() {
        $('.tv-mega-menu-slider .tv-megamenu-slider-wrapper').trigger('destroy.owl.carousel');
        $('.tv-mega-menu-slider .tv-megamenu-slider-wrapper').owlCarousel({
            loop: false,
            dots: false,
            autoplay: false,
            autoplayTimeout: 5000,
            autoplayHoverPause: false,
            nav: false,
            items: 1, // THIS IS IMPORTANT
            singleItem: true,
            responsive: {
                0: { items: 1, slideBy: 1 },
                320: { items: 1, slideBy: 1 },
                400: { items: 1, slideBy: 1 },
                768: { items: 1, slideBy: 1 },
                992: { items: 2, slideBy: 1 },
                1200: { items: 3, slideBy: 1 },
                1600: { items: 3, slideBy: 1 },
                1800: { items: 3, slideBy: 1 }
            },
        });
    }
    $(document).on('click', '.tvmega-menu-slider-prev', function() {
        $(this).parent().parent().parent().parent().find('.tv-megamenu-slider-wrapper .owl-nav .owl-prev').trigger('click');
    });

    $(document).on('click', '.tvmega-menu-slider-next', function() {
        $(this).parent().parent().parent().parent().find('.tv-megamenu-slider-wrapper .owl-nav .owl-next').trigger('click');
    });
    $(document).on('click', '.tvmobile-sliderbar-btn a', function() {
        if (document.body.clientWidth <= 991) {
            getMegaMenuAjax();
        }
    });
});