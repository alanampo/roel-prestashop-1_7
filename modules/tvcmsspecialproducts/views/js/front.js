/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var storage;
var langId = document.getElementsByTagName("html")[0].getAttribute("lang");
var currentSpecialModule = tvthemename + "_special_" + langId;
jQuery(document).ready(function($) {
    storage = $.localStorage;

    function storageGet(key) {
        return "" + storage.get(currentSpecialModule + key);
    }

    function storageSet(key, value) {
        storage.set(currentSpecialModule + key, value);
    }

    function storageClear(key) {
        storage.remove(currentSpecialModule + key);
    }
    var gettvcmsspecialproductsajax = function() {
        if ($('.tvcmsspecial-product').length) {
            /*****Load Cache*****/
            var data = storageGet("");
            if (data != "null") {
                $('.tvcmsspecial-product').html(data);
                makeSpecialProductSlider();
                productTime(); //custom.js
            }
            /*****Load Cache*****/
            $.ajax({
                type: 'POST',
                url: gettvcmsspecialproductslink,
                success: function(data) {
                    var dataCache = storageGet("");
                    storageSet("", data);
                    if (dataCache === 'null') {
                        $('.tvcmsspecial-product').html(data);
                        makeSpecialProductSlider();
                        customImgLazyLoad('.tvcmsspecial-product');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //setTimeout(function(){gettvcmsspecialproductsajax()},500);
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }

    themevoltyCallEventsPush(gettvcmsspecialproductsajax, null);

    function makeSpecialProductSlider() {
        var swiperClass = [
            //['slider className','navigation nextClass','navigation prevClass','paging className']
            ['.tvcmsspecial-product .tvspecial-product-wrapper', '.tvcmsspecial-next', '.tvcmsspecial-prev', '.tvcmsspecial-product'],
        ];

        for (var i = 0; i < swiperClass.length; i++) {
            if ($(swiperClass[i][0]).attr('data-has-image') == 'true') {
                $(swiperClass[i][0]).owlCarousel({
                    loop: false,
                    dots: false,
                    nav: false,
                    responsive: {
                        0: { items: 1},
                        320:{ items: 1, slideBy: 1},
                        330:{ items: 1, slideBy: 1},
                        360:{ items: 1, slideBy: 1},
                        400:{ items: 1, slideBy: 1},
                        480:{ items: 1, slideBy: 1},
                        650:{ items: 2, slideBy: 1},
                        767:{ items: 2, slideBy: 1},
                        768:{ items: 1, slideBy: 1},
                        992:{ items: 1, slideBy: 1},
                        1023:{ items: 1, slideBy: 1},
                        1024:{ items: 1, slideBy: 1},
                        1200:{ items: 1, slideBy: 1},
                        1350:{ items: 1, slideBy: 1},
                        1660:{ items: 1, slideBy: 1},
                        1800:{ items: 1, slideBy: 1}
                    }
                });
            } else {
                $(swiperClass[i][0]).owlCarousel({
                    loop: false,
                    dots: false,
                    nav: false,
                    responsive: {
                         0: { items: 1},
                        320:{ items: 1, slideBy: 1},
                        330:{ items: 1, slideBy: 1},
                        400:{ items: 1, slideBy: 1},
                        480:{ items: 1, slideBy: 1},
                        650:{ items: 1, slideBy: 1},
                        767:{ items: 1, slideBy: 1},
                        768:{ items: 1, slideBy: 1},
                        992:{ items: 1, slideBy: 1},
                        1023:{ items: 1, slideBy: 1},
                        1024:{ items: 1, slideBy: 1},
                        1200:{ items: 1, slideBy: 1},
                        1350:{ items: 2, slideBy: 1},
                        1660:{ items: 2, slideBy: 1},
                        1800:{ items: 2, slideBy: 1}
                    }
                });
            }
            $(swiperClass[i][1]).on('click', function(e) {
                e.preventDefault();
                $('.' + $(this).attr('data-parent') + ' .owl-nav .owl-next').trigger('click');
            });
            $(swiperClass[i][2]).on('click', function(e) {
                e.preventDefault();
                $('.' + $(this).attr('data-parent') + ' .owl-nav .owl-prev').trigger('click');
            });
            $(swiperClass[i][3] + ' .tv-pagination-wrapper').insertAfter(swiperClass[i][3] + ' .tvcmsmain-title-wrapper');
        }
    }

    var gettvcmsspecialproductsajax = function() {
        if ($('.tvcmsspecial-product').length) {
            $.ajax({
                type: 'POST',
                url: gettvcmsspecialproductslink,
                success: function(data) {
                    $('.tvcmsspecial-product').html(data);
                    makeSpecialProductSlider();
                    customImgLazyLoad('.tvcmsspecial-product');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //setTimeout(function(){gettvcmsspecialproductsajax()},500);
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }
    // function ProductSpecialProdVerSlider(){
    
    // $(document).on('click','.tvcmsget-image',function(){
    //     var GetImagePath = $(this).attr('data-image-large-src');
    //      $(this).parents().eq(11).find('.tvproduct-image .tvproduct-defult-img').attr('src',GetImagePath);
    // });
    //     $("#index .tvspecial-product-content .tvvertical-slider .product-images").not('.slick-initialized').slick({
    //         dots: false,
    //         infinite: true,
    //         speed: 300,
    //         arrows: true,
    //         prevArrow:"<i class='tvvertical-slider-pre material-icons'>&#xe408;</i>",
    //         nextArrow:"<i class='tvvertical-slider-next material-icons'>&#xe409;</i>",
    //         slidesToShow: 3,
    //         slidesToScroll: 1,
    //         variableWidth: false,
    //         height: true,
    //         centerMode:false,
    //         focusOnSelect: true,
    //         autoplay: true,
    //         adaptiveHeight:true,
    //         vertical: true,
    //         responsive: [
    //             {
    //               breakpoint: 769,
    //               settings: {
    //                 dots: false,
    //                 infinite: true,
    //                 speed: 300,
    //                 arrows: true,
    //                 prevArrow:"<i class='tvvertical-slider-pre material-icons'>&#xe408;</i>",
    //                 nextArrow:"<i class='tvvertical-slider-next material-icons'>&#xe409;</i>",
    //                 slidesToShow: 3,
    //                 slidesToScroll: 1,
    //                 variableWidth: false,
    //                 height: true,
    //                 centerMode:false,
    //                 focusOnSelect: true,
    //                 autoplay: true,
    //                 adaptiveHeight:true,
    //                 vertical: true,
    //               }
    //             },
    //             {
    //               breakpoint: 576,
    //               settings: {
    //                 dots: true,
    //                 infinite: true,
    //                 speed: 300,
    //                 arrows: false,
    //                 prevArrow:"<i class='tvvertical-slider-pre material-icons'>&#xe408;</i>",
    //                 nextArrow:"<i class='tvvertical-slider-next material-icons'>&#xe409;</i>",
    //                 slidesToShow: 1,
    //                 slidesToScroll: 1,
    //                 variableWidth: false,
    //                 height: true,
    //                 centerMode:false,
    //                 focusOnSelect: true,
    //                 autoplay: true,
    //                 adaptiveHeight:true,
    //                 vertical: false,
    //                 horizontal: true,
    //               }
    //             }
    //           ]
    //     });
    // }
    // ProductSpecialProdVerSlider();
    // $(document).ajaxComplete(function(){
    //     ProductSpecialProdVerSlider();
    // });
    // $(window).on('resize', function() {
    //   ProductSpecialProdVerSlider();
    // });
    //setTimeout(function(){gettvcmsspecialproductsajax()},500);
    //gettvcmsspecialproductsajax();
    themevoltyCallEventsPush(gettvcmsspecialproductsajax, null);

});