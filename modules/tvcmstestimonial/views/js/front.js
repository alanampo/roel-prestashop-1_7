/*
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
/****************** Start Testimonial list Slider Js *******************************************/
$(document).ready(function() {
    var rtlVal = false;
    if ($('body').hasClass('lang-rtl')) {
        var rtlVal = true;
    }
    /***************** Start Home page Slider *************************************************/
    $('.tvcmstestimonial .tvtestimonial-content-box').owlCarousel({
      loop: true,
      dots: true,
      nav: false,
      rtl: rtlVal,
      // autoplay:true,
      // autoplayTimeout:5000,
      // autoplayHoverPause:false,
      responsive: {
        0: { items: 1},
        320:{ items: 1, slideBy: 1},
        400:{ items: 1, slideBy: 1},
        768:{ items: 1, slideBy: 1},
        992:{ items: 1, slideBy: 1},
        1200:{ items: 1, slideBy: 1},
        1600:{ items: 1, slideBy: 1},
        1800:{ items: 1, slideBy: 1}
      },
    });
    $('.tvcmstestimonial .tvtestimonial-prev').on('click',function(){
      $('.tvcmstestimonial .owl-nav .owl-prev').trigger('click');
    });
    $('.tvcmstestimonial .tvtestimonial-next').on('click',function(){
      $('.tvcmstestimonial .owl-nav .owl-next').trigger('click');
    });

    // var swiperTestimonialHomePage = new Swiper('.tvcmstestimonial .tvtestimonial-content-box-wrapper', {
    //     slidesPerView: 1,
    //     slidesPerColumn: 1,
    //     autoplay: false,
    //     loop: false,
    //     navigation: {
    //         nextEl: '.tvtestimonial-next',
    //         prevEl: '.tvtestimonial-prev',
    //     },
    //     pagination: {
    //         el: '.tvcmstestimonial .tvcms-testimonial-pagination-dots',
    //         clickable: true,
    //         dynamicBullets: true,
    //     },
    //     breakpoints: {
    //         1024: {
    //             slidesPerView: 1,
    //         },
    //         768: {
    //             slidesPerView: 1,
    //         },
    //         640: {
    //             slidesPerView: 1,
    //         },
    //         320: {
    //             slidesPerView: 1,
    //         }
    //     }

    // });
    // $('.tvcmstestimonial .tvtestimonial-slider-inner').insertBefore('.tvcmstestimonial .tvcms-testimonial-pagination-dots');


    /***************** End Home page Slider *************************************************/
    sidePanelTestimonialSlider();
    leftRightTestimonialTitleToggle();
    $(window).resize(function() {
        $('.tvcms-left-testimonial .tvleft-right-title-toggle, .tvcms-left-testimonial .tvtestimonial-slider-button-wrapper').removeClass('open').removeAttr('style');
        sidePanelTestimonialSlider();
        leftRightTestimonialTitleToggle();
    });

    function sidePanelTestimonialSlider() {
        $('.tvcms-left-testimonial .tvtestimonial-content-box').owlCarousel({
            loop: true,
            dots: false,
            nav: false,
            responsive: {
                0: { items: 1 },
                320: { items: 1, slideBy: 1 },
                400: { items: 1, slideBy: 1 },
                768: { items: 1, slideBy: 1 },
                992: { items: 1, slideBy: 1 },
                1200: { items: 1, slideBy: 1 },
                1600: { items: 1, slideBy: 1 },
                1800: { items: 1, slideBy: 1 }
            },
        });
        $('.tvcms-left-testimonial .tvtestimonial-prev').on('click', function() {
            $('.tvcms-left-testimonial .owl-nav .owl-prev').trigger('click');
        });
        $('.tvcms-left-testimonial .tvtestimonial-next').on('click', function() {
            $('.tvcms-left-testimonial .owl-nav .owl-next').trigger('click');
        });
    }

    function leftRightTestimonialTitleToggle() {
        // $('.tvcms-left-testimonial .tvcms-testimonial-pagination-wrapper').insertAfter('.tvcms-left-testimonial .tvleft-right-title');
        if (document.body.clientWidth <= 1199) {
            $('.tvcms-left-testimonial .tvcms-testimonial-pagination-wrapper').insertAfter('.tvcms-left-testimonial .tvtestimonial-slider-inner');
        }
    }

    $('.tvcms-left-testimonial .tvleft-right-title-toggle').on('click', function(e) {
        e.preventDefault();
        if (document.body.clientWidth <= 1199) {
            if ($(this).hasClass('open')) {
                $(this).removeClass('open');
                $(this).parent().parent().find('.tvtestimonial-slider-button-wrapper').removeClass('open').stop(false).slideUp(500, "swing");
            } else {
                $(this).addClass('open');
                $(this).parent().parent().find('.tvtestimonial-slider-button-wrapper').addClass('open').stop(false).slideDown(500, "swing");
            }
        }
        e.stopPropagation();
    });
});