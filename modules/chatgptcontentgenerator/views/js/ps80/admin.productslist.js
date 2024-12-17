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

$(function () {

    if(subscriptionExpiredAlertHtml) {
        $('.products-catalog').prepend(subscriptionExpiredAlertHtml);
        ChatGptModal.displayRenewLimitsModal(subscriptionExpiredAlertHtml, ' ');
    }

    var listTable = $("#product_catalog_list").find('table.table.product');
    var filterColumn = 9,
        headerColumn = 9,
        headerRowCellTagName = 'td';

    // add head could
    $(listTable.find('thead').find('tr.column-headers').find('th').get(headerColumn))
        .after($('<th scope="col" class="text-center" style="width: 9%">' +
                    '<div class="custom-sortable-column" data-sort-direction="" data-sort-col-name="count_spinoffs" data-sort-prefix="">\n' +
                        '<span role="columnheader">Spin-offs</span>\n' +
                        '<span role="button" class="ps-sort" aria-label="Sort by"></span>\n' +
                    '</div>' +
                '</th>' +
                '<th scope="col" class="text-center" style="width: 9%">' +
                    '<div class="custom-sortable-column" data-sort-direction="" data-sort-col-name="is_spinoff" data-sort-prefix="">\n' +
                        '<span role="columnheader">is Spin-off</span>\n' +
                        '<span role="button" class="ps-sort" aria-label="Sort by"></span>\n' +
                    '</div>' +
                '</th>'));
    // add filter cell
    $(listTable.find('thead').find('tr.column-filters').find(headerRowCellTagName).get(filterColumn))
        .after(
            $(
                '<th class="text-center">' +
                    '<div id="filter_column_spinofs_count_div">' +
                        '<input type="hidden" id="filter_column_spinofs_count" name="filter_column_spinofs_count" value="" sql="">' +
                        '<input class="form-control form-min-max" type="text" id="filter_column_spinofs_count_min" name="filter_column_spinofs_count_min" value="" placeholder="Min" aria-label="filter_column_spinofs_count Minimum Input"> ' +
                        '<input class="form-control form-min-max" type="text" id="filter_column_spinofs_count_max" name="filter_column_spinofs_count_max" value="" placeholder="Max" aria-label="filter_column_spinofs_count Maximum Input">' +
                    '</div>' +
                '</th>' +
                '<th id="product_filter_column_isspinoff" class="text-center">' +
                    '<div class = "form-select">' +
                        '<select class="custom-select" name="filter_column_isspinoff" aria-label="filter_column_isspinoff select">' +
                            '<option value=""> </option>' +
                            '<option value="yes">Yes</option>' +
                            '<option value="no">No</option>' +
                        '</select>' +
                    '</div>' +
                '</th>'
            )
        );

    $('#product_filter_column_isspinoff .custom-select').val(isSpinofSelectValue);

    if (sortSpinoffsCount) {
        $('.custom-sortable-column[data-sort-col-name="count_spinoffs"]').attr('data-sort-direction', sortSpinoffsCount);
    }

    if (sortIsSpinoff) {
        $('.custom-sortable-column[data-sort-col-name="is_spinoff"]').attr('data-sort-direction', sortIsSpinoff);
    }

    $(".custom-sortable-column").on('click', function (){
        var sortColumnName = $(this).data('sort-col-name');

        if (sortColumnName == 'count_spinoffs') {
            if (sortSpinoffsCount == 'desc' || sortSpinoffsCount == '') {
                var newSortSpinoffsCount = 'asc';
            } else {
                var newSortSpinoffsCount = 'desc';
            }
            $(this).append('<input type="hidden" name="sort_spinoffs_count" value="' + newSortSpinoffsCount + '">');
        } else if (sortColumnName == 'is_spinoff') {
            if (sortIsSpinoff == 'desc' || sortIsSpinoff == '') {
                var newSortIsSpinoff = 'asc';
            } else {
                var newSortIsSpinoff = 'desc';
            }

            $(this).append('<input type="hidden" name="sort_is_spinoff" value="' + newSortIsSpinoff + '">');
        }

        $("#product_catalog_list").submit();
    });

    if (catalogProductsList.length) {
        listTable.find('tbody > tr').each(function (i) {
            var count = catalogProductsList[i].spinoffsCount;
            $($(this).find('td').get(headerColumn)).after($('<td class="text-center">' + (count ? count : '---') + '</td>'));

            $($(this).find('td').get(headerColumn+1)).after($('<td class="text-center">' + (catalogProductsList[i].isSpinoff ? 'Yes' : 'No') + '</td>'));

            if (catalogProductsList[i].isSpinoff) {
                $($(this).find('td').get(headerColumn+3)).find("a.dropdown-item.product-edit[onclick='unitProductAction(this, \\'duplicate\\');']").remove();
            }

        });
    }

    var sliderInput = $('#filter_column_spinofs_count');
    var minInput = $('#filter_column_spinofs_count_min');
    var maxInput = $('#filter_column_spinofs_count_max');

    // parse and fix init value
    var value = sliderInput.attr('sql');
    if (value != '') {
        value = value.replace('BETWEEN ', '');
        value = value.replace(' AND ', ',');
        value = value.replace('<=', '0,');
        value = value.replace('>=', '1000000,');
        value = value.split(',');
        value[0] = Number(value[0]);
        value[1] = Number(value[1]);
    } else {
        value = [0, 1000000];
    }
    value = value.sort(function sortNumber(a,b) {
        return a - b;
    });

    // Init inputs
    if (value[0] > 0)
        minInput.val(value[0]);
    if (value[1] < 1000000)
        maxInput.val(value[1]);

    // Change events
    var inputFlasher = function(input) {
        // animate input to highlight it (like a pulsate effect on jqueryUI)
        $(input).stop().delay(100)
            .fadeIn(100).fadeOut(100)
            .queue(function() { $(this).css("background-color", "#FF5555").dequeue(); })
            .fadeIn(160).fadeOut(160).fadeIn(160).fadeOut(160).fadeIn(160)
            .animate({ backgroundColor: "#FFFFFF"}, 800);
    };
    var updater = function(srcElement) {
        var isMinModified = (srcElement.attr('id') == minInput.attr('id'));

        // retrieve values, replace ',' by '.', cast them into numbers (float/int)
        var newValues = [(minInput.val()!='')?Number(minInput.val().replace(',', '.')):0, (maxInput.val()!='')?Number(maxInput.val().replace(',', '.')):1000000];

        // if newValues are out of bounds, or not valid, fix the element.
        if (isMinModified && !(newValues[0] >= 0 && newValues[0] <= 1000000)) {
            newValues[0] = 0;
            minInput.val('');
            inputFlasher(minInput);
        }
        if (!isMinModified && !(newValues[1] >= 0 && newValues[1] <= 1000000)) {
            newValues[1] = 1000000;
            maxInput.val('');
            inputFlasher(maxInput);
        }

        // if newValues are not ordered, fix the opposite input.
        if (isMinModified && newValues[0] > newValues[1]) {
            newValues[1] = newValues[0];
            maxInput.val(newValues[0]);
            inputFlasher(maxInput);
        }
        if (!isMinModified && newValues[0] > newValues[1]) {
            newValues[0] = newValues[1];
            minInput.val(newValues[0]);
            inputFlasher(minInput);
        }

        if (newValues[0] == 0 && newValues[1] == 1000000) {
            sliderInput.attr('sql', '');
        } else if (newValues[0] == 0) {
            sliderInput.attr('sql', '<='+newValues[1]);
        } else if (newValues[1] == 1000000) {
            sliderInput.attr('sql', '>='+newValues[0]);
        } else {
            sliderInput.attr('sql', 'BETWEEN ' + newValues[0] + ' AND ' + newValues[1]);
        }

    }
    minInput.on('change', function(event) {
        updater($(event.srcElement));
    });
    maxInput.on('change', function(event) {
        updater($(event.srcElement));
    });

    if (filter_column_spinofs_count_min) {
        $('#filter_column_spinofs_count_min').val(filter_column_spinofs_count_min);
        updater($('#filter_column_spinofs_count_min'));
    }

    if (filter_column_spinofs_count_max) {
        $('#filter_column_spinofs_count_max').val(filter_column_spinofs_count_max);
        updater($('#filter_column_spinofs_count_max'));
    }
});
