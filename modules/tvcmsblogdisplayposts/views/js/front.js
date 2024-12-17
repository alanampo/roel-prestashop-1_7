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

var mobileViewSize  = 991;
var blogHomePageSlider = 767;

$(document).ready(function(){

  // blogHomepageSliderShow();
  // function blogHomepageSliderShow()
  // {
  //   if (blogHomePageSlider >= document.body.clientWidth) {
  //     $('.tvcmsblog-event-home .tvnews-wrapper-info-box').owlCarousel({
  //       loop: false,
  //       dots: false,
  //       nav: false,
  //       responsive: {
  //         0: { items: 1},
  //         320:{ items: 1, slideBy: 1},
  //         400:{ items: 1, slideBy: 1},
  //         768:{ items: 1, slideBy: 1},
  //         992:{ items: 1, slideBy: 1},
  //         1200:{ items: 1, slideBy: 1},
  //         1600:{ items: 1, slideBy: 1},
  //         1800:{ items: 1, slideBy: 1}
  //       },
  //     });
  //     $('.tvcmsblog-event-home-slider-prev').click(function(e){
  //       e.preventDefault();
  //       $('.tvcmsblog-event-home .owl-nav .owl-prev').trigger('click');
  //     });

  //     $('.tvcmsblog-event-home-slider-next').click(function(e){
  //       e.preventDefault();
  //       $('.tvcmsblog-event-home .owl-nav .owl-next').trigger('click');
  //     });
  //   }
  // }

  leftRightSideSlider();
  function leftRightSideSlider()
  {
    $('.tvcmsblog-left-side .tvnews-wrapper-info-box').owlCarousel({
      loop: false,
      dots: false,
      nav: false,
      responsive: {
        0: { items: 1},
        320:{ items: 1, slideBy: 1},
        400:{ items: 1, slideBy: 1},
        768:{ items: 2, slideBy: 1},
        992:{ items: 2, slideBy: 1},
        1000:{ items: 3, slideBy: 1},
        1199:{ items: 3, slideBy: 1},
        1200:{ items: 1, slideBy: 1},
        1600:{ items: 1, slideBy: 1},
        1800:{ items: 1, slideBy: 1}
      },
    });
    $('.tvblog-left-side-prev').click(function(e){
      e.preventDefault();
      $('.tvnews-wrapper-info-box .owl-nav .owl-prev').trigger('click');
    });

    $('.tvblog-left-side-next').click(function(e){
      e.preventDefault();
      $('.tvnews-wrapper-info-box .owl-nav .owl-next').trigger('click');
    });
  }





   /*************** Start left Right  Product Toggle in Mobile Size ***************************************************/
  // leftRightBlogTitleToggle();
  // $(window).resize(function(){
  //   $('.tvcmsblog-left-side .tvleft-right-title-toggle, .tvleft-right-penal-all-block .tvblog-event-all-block').removeClass('open');
  //   leftRightBlogTitleToggle();
  // });
  // function leftRightBlogTitleToggle()
  // {
  //   $('.tvcmsblog-left-side .tvleft-right-title-toggle').on('click', function(e){
  //     e.preventDefault();
  //     if(document.body.clientWidth <= mobileViewSize){
  //       if($(this).hasClass('open')) {
  //         $(this).removeClass('open');
  //         $(this).parent().parent().find('.tvblog-event-all-block').removeClass('open').stop(false).slideUp(500, "swing");
  //       } else {
  //         $(this).addClass('open');
  //         $(this).parent().parent().find('.tvblog-event-all-block').addClass('open').stop(false).slideDown(500, "swing");
  //       }
  //     }
  //     e.stopPropagation();
  //   });
  // }
  /*************** End left Right  Product Toggle in Mobile Size ***************************************************/



  $('.tvcmsblog-gallery-slider').each(function(){
    var slider_id = $(this).attr('data-slider-id');
    var parent_class = '.tvcmsblog-gallery-slider.'+slider_id;
    $(parent_class +' .tvblog-wrapper-slider').owlCarousel({
      loop: false,
      dots: false,
      nav: false,
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
    $(parent_class +' .tvcmsblog-gallery-slider-prev').click(function(e){
      e.preventDefault();
      $(parent_class +' .owl-nav .owl-prev').trigger('click');
    });

    $(parent_class + ' .tvcmsblog-gallery-slider-next').click(function(e){
      e.preventDefault();
      $(parent_class +' .owl-nav .owl-next').trigger('click');
    });
  });



  $('.tvcmsblog-video-slider').each(function(){
    var slider_id = $(this).attr('data-slider-id');
    var parent_class = '.tvcmsblog-video-slider.'+slider_id;
    $(parent_class + ' .tvblog-wrapper-slider').owlCarousel({
      loop: false,
      dots: false,
      nav: false,
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
    $(parent_class + ' .tvblog-video-slider-prev').click(function(e){
      e.preventDefault();
      $(parent_class + ' .owl-nav .owl-prev').trigger('click');
    });

    $(parent_class + ' .tvblog-video-slider-next').click(function(e){
      e.preventDefault();
      $(parent_class + ' .owl-nav .owl-next').trigger('click');
    });
  });

  $(window).resize(function(){
    blogHomePage();
  });

  blogHomePage();
  function blogHomePage()
  {
    if (mobileViewSize <= document.body.clientWidth) {
      $('.tvnews-event-wrapper .tvblog-odd').each(function(){
        $(this).find('.tvnews-event-content-wrapper').insertBefore($(this).find('.tvblog-img-block'));
      });
    } else {
      $('.tvnews-event-wrapper .tvblog-odd').each(function(){
        $(this).find('.tvnews-event-content-wrapper').insertAfter($(this).find('.tvblog-img-block'));
      });
    }
  }
});
