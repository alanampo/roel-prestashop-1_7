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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

jQuery(document).ready(function($){
  $('.tvcmsbrandlist-slider .tvbrandlist-slider-content-box').owlCarousel({
    loop: false,
    dots: false,
    nav: false,
    responsive: {
      0: { items: 1},
      320:{ items: 1, slideBy: 2},
      400:{ items: 2, slideBy: 1},
      768:{ items: 3, slideBy: 1},
      992:{ items: 4, slideBy: 1},
      1200:{ items: 5, slideBy: 1},
      1600:{ items: 5, slideBy: 1},
      1800:{ items: 5, slideBy: 1}
    },
  });
  $('.tvbrandlist-slider-prev').click(function(e){
    e.preventDefault();
    $('.tvcmsbrandlist-slider .owl-nav .owl-prev').trigger('click');
  });
  $('.tvbrandlist-slider-next').click(function(e){
    e.preventDefault();
    $('.tvcmsbrandlist-slider .owl-nav .owl-next').trigger('click');
  });
  $('.tvcmsbrandlist-slider .tvcms-brandlist-pagination-wrapper').insertAfter('.tvcmsbrandlist-slider .tvbrandlist-slider-content-box');
});