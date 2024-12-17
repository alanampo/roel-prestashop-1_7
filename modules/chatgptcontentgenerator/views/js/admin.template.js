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

$(document).on('ready', function() {
    $('.shortcod-value').on('click', function() {
        var shortCode = $(this).text();
        var textarea = document.getElementById('short_code_' + id_language);
        var start_position = textarea.selectionStart;
        var end_position = textarea.selectionEnd;

        textarea.value = `${textarea.value.substring(
            0,
            start_position
        )}${shortCode}${textarea.value.substring(
            end_position,
            textarea.value.length
        )}`;

        displayLanguagesPompt();
    })

    $('.prompt-short-codes textarea').on('change', function(){
        displayLanguagesPompt();
    })

    displayLanguagesPompt();
})

function displayLanguagesPompt() {
    var languagesPompt = [];

    $('.prompt-short-codes textarea').each(function(){
        if ($(this).val()) {
            var id_lang = parseInt($(this).attr('name').match(/\d+/));
            languagesPompt.push(getLanguageNameById(id_lang));
        }
    });

    $('.help-block-prompt-languages').remove();

    if (languagesPompt.length > 0) {
        var htmlLanguages =
        '<div class="help-block-prompt-languages">' +
            '<span class="shortcodes-name">' + textLanguagesPompt + ': </span>' +
            '<span class="shortcodes-lang">' + languagesPompt.join(', ') + '</span>' +
        '</div>';

        $('.prompt-short-codes .help-block').prepend(htmlLanguages);
    }
}

function getLanguageNameById(id_lang) {
    var langName = '';
    languages.forEach(function(el) {
        if (el.id_lang == id_lang) {
            langName = el.name;
            return;
        }
    });
    return langName;
}
