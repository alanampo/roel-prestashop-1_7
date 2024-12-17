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

var productType = 'standard';

$(document).ready(function() {
    productType = $('#product_header_type').val();

    if ('combinations' == productType) {
        $('#product_combinations_combination_manager').before(getSpinOffStockSwitch());
    } else {
        $('#product_stock_quantities').prepend(getSpinOffStockSwitch());
    }

    changeSpinoffStock();

    $('[name="spinoff_stock"]').on('change', function() {
        changeSpinoffStock();
    })

    if(subscriptionExpiredAlertHtml) {
        $('#product_header').prepend(subscriptionExpiredAlertHtml);
        ChatGptModal.displayRenewLimitsModal(subscriptionExpiredAlertHtml, ' ');
    }

    $('#product_header').prepend( '<div class="edit-spin-off-message pb-1 pt-1 h1">'+ editSpinOffProduct + '</div>');

    $('#product-tabs-content #product_description').prepend(
        '<h2>'+ parentProductBlockTitle + '</h2>\n' +
        '<div class="parentBlockContainer mb-4 p-2">' +
        '<a href="' + parentProductLink + '"><i class="material-icons">open_in_new</i> ' + parentProductName + '</a>' +
        '</div>'
    );
})

function changeSpinoffStock() {
    var spinOffStock = +$("#spinoff_stock_1").is(':checked'),
        $productDisplayElem = null;

    $('.spin-off-product-stock_message').replaceWith(getMessageStockSwitch(spinOffStock))

    if ('combinations' == productType) {
        $productDisplayElem = $('#product_combinations_combination_manager');
    } else {
        $productDisplayElem = $('#product_stock_quantities_delta_quantity').parent('.form-group');
    }

    if (spinOffStock == gptPageSettings.productForm.spinOffStockCommon) {
        $productDisplayElem.slideUp();
        $('#product_combinations .combination-availability').slideUp();
    } else {
        $productDisplayElem.slideDown();
        $('#product_combinations .combination-availability').slideDown();
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
                    getMessageStockSwitch(spinOffProductStock) +
                '</div>' +
            '</div>'
    ;
}

function getMessageStockSwitch(spinOffStock) {
    return  '<span class="small font-secondary spin-off-product-stock_message">' +
                (spinOffStock == gptPageSettings.productForm.spinOffStockCommon
                    ? (
                        gptI18n.spinOffQuantityCommon +
                        '<div><a href="' + parentProductLink + '" class="btn sensitive px-0"><i class="material-icons">open_in_new</i>' +
                            gptI18n.spinOffEditQuantity + '</a>' +
                        '</div>'
                    ) : gptI18n.spinOffQuantityIndividual
                ) +
            '</span>'
    ;
}
