/**
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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function() {
    initSpinOffStock();

    $('[name="spinoff_stock"]').on('change', function() {
        spinOffProductStock = +$("#spinoff_stock_1").is(':checked');
        initSpinOffStock();
    })

    if(subscriptionExpiredAlertHtml) {
        $('.product-header').prepend(subscriptionExpiredAlertHtml);
        ChatGptModal.displayRenewLimitsModal(subscriptionExpiredAlertHtml, ' ');
    }

    $('.product-header').prepend( '<div class="edit-spin-off-message pb-1 pt-1 h1">'+ editSpinOffProduct + '</div>');

    $('#step1 .left-column').prepend(
        '<h2>'+ parentProductBlockTitle + '</h2>\n' +
        '<div class="parentBlockContainer mb-4 p-2">' +
        '<a href="' + parentProductLink + '"><i class="material-icons">open_in_new</i> ' + parentProductName + '</a>' +
        '</div>'
    );
})

function initSpinOffStock() {
    var spinOffQuantityText = gptI18n.spinOffQuantityIndividual;
    var $productQuantityElem = $('#product_qty_0_shortcut_div');
    var isCombination = +$('[name="show_variations"]:checked').val();

    if ($productQuantityElem.length > 0) {
        $productQuantityElem.addClass('spin-off-product-qty-stock');

        if ($('.spin-off-product-stock').length < 1) {
            $productQuantityElem.find('h2').after(getSpinOffStockSwitch());
        }

        if (isCombination) {
            $productQuantityElem.find('#form_step1_qty_0_shortcut').hide();
        }

        if (spinOffProductStock == gptPageSettings.productForm.spinOffStockCommon) {
            spinOffQuantityText = gptI18n.spinOffQuantityCommon;
            $productQuantityElem.find('#form_step1_qty_0_shortcut').attr('readonly', true);

            var quantityParentProductLink = parentProductLink;
            if (isCombination) {
                quantityParentProductLink += '#tab-step3';
            }
            $productQuantityElem.find('a.btn').attr('href', quantityParentProductLink);
            $('#tab_step3 >.nav-link').addClass('disabled');
        } else {
            $productQuantityElem.find('a.btn').attr('href', '#tab-step3');
            $('#tab_step3 >.nav-link').removeClass('disabled');
            $productQuantityElem.find('#form_step1_qty_0_shortcut').attr('readonly', false);
        }

        $productQuantityElem.find('span.small')[0].childNodes[0].textContent = spinOffQuantityText;
        $productQuantityElem.find('a.btn')[0].childNodes[1].textContent = gptI18n.spinOffUpdateQuantities;
    }
}

function getSpinOffStockSwitch() {
    return '<div class="row mb-3 spin-off-product-stock">' +
                '<div class="col-xl-12 col-lg-12">' +
                    '<span class="ps-switch">' +
                        '<input id="spinoff_stock_0" class="ps-switch" name="spinoff_stock" value="' + gptPageSettings.productForm.spinOffStockIndividual + '" ' + (spinOffProductStock == gptPageSettings.productForm.spinOffStockIndividual ? 'checked="checked"' : '') + ' type="radio" aria-label="No">' +
                        '<label for="spinoff_stock_0">' + gptI18n.spinOffIndividual + '</label>' +
                        '<input id="spinoff_stock_1" class="ps-switch" name="spinoff_stock" value="' + gptPageSettings.productForm.spinOffStockCommon + '" ' + (spinOffProductStock == gptPageSettings.productForm.spinOffStockCommon ? 'checked="checked"' : '') + ' type="radio" aria-label="Yes">' +
                        '<label for="spinoff_stock_1">' + gptI18n.spinOffCommon + '</label>' +
                        '<span class="slide-button"></span>' +
                    '</span>' +
                '</div>' +
            '</div>'
    ;
}
