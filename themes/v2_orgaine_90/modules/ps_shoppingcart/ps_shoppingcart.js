/*
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @version  Release: $Revision$
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function () {
  prestashop.blockcart = prestashop.blockcart || {};

  var showModal = prestashop.blockcart.showModal || function (modal) {
    var $body = $('body');
    $body.append(modal);
    $body.one('click', '#blockcart-modal', function (event) {
      if (event.target.id === 'blockcart-modal') {
        $(event.target).remove();
      }
    });
  };

  prestashop.on(
    'updateCart',
    function (event) {
      var refreshURL = $('.blockcart').data('refresh-url');
      var requestData = {};
      if (event && event.reason) {
        requestData = {
          id_customization: event.reason.idCustomization,
          id_product_attribute: event.reason.idProductAttribute,
          id_product: event.reason.idProduct,
          action: event.reason.linkAction
        };
      //   $('.tvproduct-add-to-cart').addClass("loading-wake");
      // $('.tvproduct-add-to-cart').find('.add-cart').addClass('tvcms-cart-loading');
      // $('.tvproduct-add-to-cart').find('.add-cart').html('&#xe863;');
        $.post(refreshURL, requestData).then(function (resp) {
          $('.blockcart').replaceWith($(resp.preview).find('.blockcart'));
          if (resp.modal) {
            showModal(resp.modal);
            $('.tvproduct-add-to-cart').html('<svg class="add-cart" version="1.1" id="Layer_1" x="0px" y="0px" width="18" height="18" viewBox="0 0 228 200" style="enable-background:new 0 0 228 200;" xml:space="preserve"><path d="M1,7c3.3-4.1,7.5-5.3,12.7-5.1C21.5,2.3,29.3,2,37.1,2c7.6,0,9.6,1.5,11.7,8.8c10.7,37.2,21.5,74.4,32,111.7c1.1,4,2.8,5.4,7.1,5.4c27.8-0.2,55.6-0.2,83.4,0c3.7,0,5.6-1,7.1-4.5c8.8-19.9,17.9-39.7,26.8-59.6c0.7-1.5,1.2-3,2.1-5.1c-2.7,0-4.6,0-6.6,0c-31.1,0-62.3,0-93.4,0c-1.7,0-3.4,0.1-5-0.2c-4.4-0.8-7.6-4.4-7.1-8.3c0.5-4.3,3-7,7.4-7.4c1.7-0.2,3.3-0.2,5-0.2c36.3,0,72.6,0.1,108.9-0.1c5.2,0,9.4,1,12.4,5.5c0,1.7,0,3.3,0,5c-4,8.4-8,16.7-11.8,25.2c-8.8,19.4-17.6,38.8-26.2,58.2c-2.3,5.3-5.7,7.6-11.5,7.5c-33.5-0.1-67-0.1-100.5-0.1c-7.5,0-9.6-1.7-11.7-8.9c-10.7-37.2-21.5-74.5-32-111.8c-1.2-4.2-2.9-5.6-7.2-5.3c-5,0.4-10-0.1-15,0.2C8.1,18.4,4,17.2,1,13C1,11,1,9,1,7z"></path><path d="M184,201c-1.2-0.5-2.4-1.1-3.6-1.6c-8.4-3.6-13-11.7-11.3-20.5c1.5-8.2,9.6-14.7,18.4-14.8c9-0.1,16.9,6.1,18.8,14.8c1.8,8.3-2.6,16.5-10.8,20.3c-1.2,0.5-2.3,1.2-3.4,1.8C189.3,201,186.7,201,184,201z"></path><path d="M64,201c-5.8-2.6-11.3-5.6-13.7-12.1c-3.4-9.1,1-19.1,10.2-23c9.6-4,20.7,0.1,25,9.2c4.1,8.8,0.3,19.2-8.6,23.7c-1.6,0.8-3.3,1.5-4.9,2.2C69.3,201,66.7,201,64,201z"></path></svg><svg class="out-of-stock hide" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="16" height="16" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1024 1024"><path d="M512 0Q373 0 255 68.5T68.5 255T0 512t68.5 257T255 955.5t257 68.5t257-68.5T955.5 769t68.5-257t-68.5-257T769 68.5T512 0zM64 512q0-167 110-294l632 632Q679 961 512 961q-73 0-141.5-22.5T247 874t-96.5-97t-64-123.5T64 512zm787 293L219 173q61-52 136-80.5T512 64q73 0 141.5 22.5t123.5 64t96.5 96.5t64 123.5T960 512q0 166-109 293z" fill="#888888"></path></svg>');
                        $('.tvproduct-add-to-cart').removeClass('tvcms-cart-loading');
                        $('.tvproduct-add-to-cart').removeClass('loading-wake');
          }
        }).fail(function (resp) {
          prestashop.emit('handleError', { eventType: 'updateShoppingCart', resp: resp });
        });
      }
      if (event && event.resp && event.resp.hasError) {
        prestashop.emit('showErrorNextToAddtoCartButton', { errorMessage: event.resp.errors.join('<br/>')});
      }
    }
  );
});
