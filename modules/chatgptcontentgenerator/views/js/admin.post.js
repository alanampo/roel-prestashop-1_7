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

function productPostEventHandler(modal, getData, onGenerationCompleted) {
    throw Error('this function is deprecated. use GptPostContent.productPostEventHandler');
}

$(function () {
    $(".generate-link-rewrite").on('click', function (e) {
        e.preventDefault();
        var id_language = (new ChatGptContent).getPageLanguageId();
        $('#link_rewrite_' + id_language).val(str2url($('#name_' + id_language).val().trim(), 'UTF-8'));
    });
});

$(function () {
    if (typeof adminPageName == 'undefined' || adminPageName != 'postForm') {
        return;
    }

    var gptWidgetSettings = {
        descriptionWrapper: false,
        descriptionEditorPrefix: false,
        descriptionWrapperSelector: false,
        nameWrapper: false,
        nameWrapperSelector: false,
        nameEditorPrefix: false,
    };
    if (isLegacyController) {
        gptWidgetSettings.descriptionWrapper = $("#content_" + (new ChatGptContent).getPageLanguageId())
            .closest('.translatable-field')
            .parent()
            .addClass('post-content-wrapper');
        gptWidgetSettings.shortContentWrapper = $("#short_content_" + (new ChatGptContent).getPageLanguageId())
            .closest('.translatable-field')
            .parent()
            .addClass('post-content-wrapper');
        gptWidgetSettings.descriptionWrapperSelector = '.post-content-wrapper';
        gptWidgetSettings.descriptionEditorPrefix = 'content_';
        gptWidgetSettings.nameWrapper = $("#name_" + (new ChatGptContent).getPageLanguageId())
            .closest('.translatable-field')
            .parent()
            .addClass('post-name-wrapper');
        gptWidgetSettings.nameWrapperSelector = '.post-name-wrapper';
        gptWidgetSettings.nameEditorPrefix = 'name_';
    }

    // ChatGptForm.displaySubscriptionAlertMessage(gptWidgetSettings.descriptionWrapper, undefined, 'availablePageWords');
    if (!gptShopInfo.subscription || gptShopInfo.subscription.availablePageWords == 0) {
        return;
    }

    var contentAction = (new ChatGptAction({
            id: "gpt_description_button",
            title: gptI18n.buttonGenerate,
            type: 'single'
        })).renderInto(gptWidgetSettings.descriptionWrapper);

    contentAction.getButton().on('click', function (e) {
            e.preventDefault();

            // init post form //////////////////////////////////////
            var postModal = new ChatGptModal({
                closable: false,
                keyboard: false,
                backdrop: false,
                isLegacy: true,
                class: 'black-modal modal-with-tabs post-modal'
            });

            postModal
                .setHeader(gptI18n.titlePageConentGeneration)
                .setBody(
                    '<div>' + ChatGptForm.postForm() + '</div>'
                )
            ;
            // END init post form //////////////////////////////////////

            GptPostContent.postFormEventHandler(postModal, function () {
                    return {
                        gptWidgetSettings: gptWidgetSettings,
                        postName: $("#" + gptWidgetSettings.nameEditorPrefix + (new ChatGptContent()).getPageLanguageId())
                            .val().trim(),
                    }
                }, function (modal) {
                    // close modal
                    modal.destroy();
                });
        });

    // define wrapper for translate action
    var nameTranslateWrapper = $('<div />');
    gptWidgetSettings.nameWrapper.after(nameTranslateWrapper);
    console.log(gptWidgetSettings);

    var rewritePostNameObject = (new ChatGptAction({
            entity: 'blog',
            id: "gpt_rewrite_name_button",
            title: gptI18n.buttonRewrite,
            type: 'single',
            class: isLegacyController ? 'btn btn-primary ' + (gptLanguages.length > 1 ? 'ml-2' : '') : 'btn btn-primary',
            contentEditorPreffix: gptWidgetSettings.nameEditorPrefix,
            fieldName: 'name'
        }))
            .renderInto(nameTranslateWrapper);
    var rewritePostShortContentObject = (new ChatGptAction({
            entity: 'blog',
            id: "gpt_rewrite_short_content_button",
            title: gptI18n.buttonRewrite,
            type: 'single',
            class: isLegacyController ? 'btn btn-primary ' + (gptLanguages.length > 1 ? 'ml-2' : '') : 'btn btn-primary',
            contentEditorPreffix: 'short_content_',
            fieldName: 'short_content'
        }))
            .renderInto(gptWidgetSettings.shortContentWrapper);
    var rewritePostDescriptionObject = (new ChatGptAction({
            entity: 'blog',
            id: "gpt_rewrite_description_button",
            title: gptI18n.buttonRewrite,
            type: 'button',
            class: isLegacyController ? 'btn btn-primary ml-2' : 'btn btn-primary',
            contentEditorPreffix: gptWidgetSettings.descriptionEditorPrefix,
            fieldName: 'content'
        }))
            .renderInto(gptWidgetSettings.descriptionWrapper.find('.gpt-button-wraper'));

    rewritePostNameObject.getButton().on('click', GptRewriteEventHandler);
    rewritePostShortContentObject.getButton().on('click', GptRewriteEventHandler);
    rewritePostDescriptionObject.getButton().on('click', GptRewriteEventHandler);

    if (gptLanguages.length > 1) {
        // translate post title button
        var translateTitleObject = new ChatGptTranslateAction({
            entity: 'blog',
            languages: gptLanguages,
            contentEditorPreffix: gptWidgetSettings.nameEditorPrefix,
            buttonClass: 'btn btn-primary mr-2',
            title: gptI18n.buttonTranslate,
            type: 'button'
        })
            .renderInto(nameTranslateWrapper.find('.gpt-button-wraper'), 'prepend');

        translateTitleObject.getButton().on('click', GptTranslateEventHandler);

        // translate post short content button
        var translateShortContentObject = new ChatGptTranslateAction({
            entity: 'blog',
            languages: gptLanguages,
            contentEditorPreffix: 'short_content_',
            buttonClass: 'btn btn-primary mr-2',
            title: gptI18n.buttonTranslate,
            type: 'button'
        })
            .renderInto(gptWidgetSettings.shortContentWrapper.find('.gpt-button-wraper'), 'prepend');

        translateShortContentObject.getButton().on('click', GptTranslateEventHandler);

        // translate post full content button
        var translatePostContentObject = new ChatGptTranslateAction({
            entity: 'blog',
            languages: gptLanguages,
            wrapperSelector: gptWidgetSettings.descriptionWrapperSelector,
            contentEditorPreffix: gptWidgetSettings.descriptionEditorPrefix,
            buttonClass: 'btn btn-primary ml-2 mr-2',
            type: 'button',
            title: gptI18n.buttonTranslate
        })
            .renderInto(gptWidgetSettings.descriptionWrapper.find('#gpt_description_button'), 'after');

        translatePostContentObject.getButton().on('click', GptTranslateEventHandler);
    }
});
