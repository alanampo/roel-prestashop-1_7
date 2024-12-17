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

$(document).ready(function(){

    /* new tab */
    var liTabs6 = document.getElementById(gptVarVersion.selectors.pfLiTabs6Id);
    var newTabs7 = document.createElement('li');
    var newA = document.createElement('a');
    newTabs7.className = 'nav-item';
    newTabs7.id = 'tab_step7';
    newA.setAttribute('role', 'tab');
    newA.setAttribute('data-toogle', 'tab');
    newA.className = 'nav-link';
    newA.href = '#step7';
    newTabs7.appendChild(newA);
    newA.innerHTML = 'Spin-Off';
    newA.id = '_step7';
    liTabs6.parentNode.insertBefore(newTabs7, liTabs6.nextSibling);
    newTabs7.onclick = function () {
        removeActiveTab();
    };

    /* new tab content and add custom hook */
    var content6 = document.getElementById(gptVarVersion.selectors.pfContent6Id); /* content options */
    var contentNewTab7 = document.createElement('div');
    contentNewTab7.className = 'form-contenttab tab-pane';
    contentNewTab7.setAttribute('role', 'tabpanel');
    contentNewTab7.setAttribute('wfd-invisible', 'true');
    contentNewTab7.id = 'step7';
    content6.parentNode.insertBefore(contentNewTab7, content6.nextSibling);

    readAjaxContent();

    $('#' + gptVarVersion.selectors.pfTabsContentId).on('click', '#gpt_spinoff_button', function (){
        if (!!gptShopInfo == false || typeof gptShopInfo.subscription == 'undefined' || gptShopInfo.subscription == false) {
            ChatGptModal.displayRenewLimitsModal(gptI18n.subscriptionNotAvaialable, gptI18n.renewOrOrderBtn);
            return;
        }

        // init spin-off form //////////////////////////////////////
        var spinOffModal = new ChatGptModal({
            closable: false,
            keyboard: false,
            backdrop: false,
            class: 'black-modal modal-with-tabs'
        });

        spinOffModal
            .setHeader(gptI18n.createNewSpinOff)
            .setBody(
                '<div>' + ChatGptForm.spinOffForm() + '</div>'
            )
        ;
        // END init spin-off form //////////////////////////////////////

        spinOffModal.setActions([])
            .addAction({
                title: gptI18n.buttonCancel,
                class: 'btn btn-outline-secondary'
            }, function (cancelButton) {
                cancelButton.getModal().destroy();
            }).addAction({
            title: gptI18n.spinoffButtonCreate,
        }, async function (actionInstance) {

            var numberOfSpinOff = $('input#gpt_spinoff_number').val();

            if (numberOfSpinOff < 1) {
                $('#spinoffCreateNumberError').remove();
                $('#gpt_spinoff_number').parent().after('<p id="spinoffCreateNumberError" class="text-danger">' + gptI18n.spinoffCreateNumberError + '</p>');
                return;
            }

            var isRewriteTitle = true;
            var isRewriteDescription = +$("#rewrite_description_1").is(':checked');
            var isRewriteShortDescription = +$("#rewrite_short_description_1").is(':checked');
            var spinOffStock = +$("#spinoff_stock_1").is(':checked');
            var isSpinOffUseChatGpt = +$("#spinoff_usechatgpt_1").is(':checked');

            var isUseChatGPT = (gptShopInfo && gptShopInfo.subscription && gptShopInfo.subscription.availablePageWords > 0
                && isSpinOffUseChatGpt);

            spinOffModal.find('body').html('<div>' + ChatGptForm.spinOffCreationProcess() + '</div>');

            var contentInstance = new ChatGptContent();

            spinOffModal
                .setCancelButton(function (cancelButton) {
                    contentInstance.stopAllProcess();
                    cancelButton.getModal().find('#process_spinoff_status').text(gptI18n.textCanceled);
                });

            function handleRewritingError (errorMessage) {
                progressBar = spinOffModal.find('.progress-bar');
                progressBar.removeClass('progress-bar-success');
                progressBar.addClass('progress-bar-danger');
                spinOffModal.find('#process_spinoff_status').text(gptI18n.bulkRewriteProcessFail);
                spinOffModal.find("#process_spinoff_error_log").show().text(errorMessage);

                // render close button
                spinOffModal.setCloseButton();
            }

            var options = {};

            options.entity = 'spinoff';
            options.languages = gptLanguagesIds;
            options.fields = [];

            var contentGenerate = [];

            if (isRewriteTitle) {
                options.fields.push('name');
            }
            if (isRewriteDescription) {
                options.fields.push('description');
            } else {
                contentGenerate.push('description');
            }
            if (isRewriteShortDescription) {
                options.fields.push('description_short');
            } else {
                contentGenerate.push('description_short');
            }

            var fieldsCount = 3;
            var requestCounter = 0;

            if (isUseChatGPT) {
                var totalRequest = fieldsCount * numberOfSpinOff * options.languages.length;
            } else {
                var totalRequest = numberOfSpinOff;
            }

            var shouldBreak = false;
            for (var i = 0; i < numberOfSpinOff && !shouldBreak; i++) {
                try {
                    var resultCreateSpinOff = await sendAjaxCreateSpinOff(1, spinOffStock);

                    if (!resultCreateSpinOff.success) {
                        handleModalError(actionInstance.getModal(), resultCreateSpinOff.error.message);
                        return;
                    }

                } catch (err) {
                    ChatGptContent.removeLoaderLayer();
                    handleModalError(actionInstance.getModal(), err);
                    return;
                }

                readAjaxContent();

                if (options.fields.length > 0 && isUseChatGPT) {
                    try {
                        await contentInstance.bulkRewriteObjects(resultCreateSpinOff.spinoff_ids, options, async function (idObject, itemIndex, response, languageId, languageIndex, fieldIndex, instance) {
                            requestCounter++;

                            if (typeof response.success != 'undefined' && !response.success) {
                                instance.stopCurrentProcess();

                                handleRewritingError(response.error.message);
                                shouldBreak = true;
                                return;
                            } else if (typeof response.success != 'undefined' && response.success) {
                                var inQueue = response.objects[0].inQueue;

                                if (inQueue) {
                                    try {
                                        // await complete response
                                        requestInfo = await instance.awaitRequestResponse(response.objects[0].requestId);
                                    } catch (err) {
                                        window.showErrorMessage(err);
                                        shouldBreak = true;
                                        return;
                                    };

                                    // check request info
                                    if (requestInfo) {
                                        if (requestInfo.status != 'success') {
                                            // if the request sattus is not success then display the error
                                            instance.stopCurrentProcess();

                                            if (requestInfo.status == 'quota_over') {
                                                handleRewritingError(gptI18n.subscriptionLimitЕxceeded);
                                            } else {
                                                handleRewritingError(requestInfo.text);
                                            }
                                            shouldBreak = true;
                                            return;
                                        }
                                    }
                                }
                            }

                            var progressBar = spinOffModal.find('.progress-bar');

                            progressBar.css('width', `${requestCounter * 100 / totalRequest}%`);
                            progressBar.find('span').html(`${i+1} / ${numberOfSpinOff} (${(requestCounter * 100 / totalRequest).toFixed(2)}%)`);

                            readAjaxContent();
                        });
                    } catch (err) {
                        handleRewritingError(err);
                    }
                }

                if (contentGenerate.length > 0 && isUseChatGPT) {
                    var optionsDescription = {};
                    optionsDescription.languages = gptLanguagesIds;
                    optionsDescription.replace = true;
                    optionsDescription.skipExistingContent = 0;

                    for (var j = 0; j < contentGenerate.length && !shouldBreak; j++) {
                        if (contentGenerate[j] == 'description_short') {
                            optionsDescription.maxWords = gptPageSettings.productsList.minWords;
                        } else {
                            optionsDescription.maxWords = gptPageSettings.productsList.maxWords;
                        }

                        await contentInstance.bulkProductDescription(resultCreateSpinOff.spinoff_ids, optionsDescription, async function (idObject, itemIndex, response, languageId, languageIndex, instance) {
                            requestCounter++;

                            var progressBar = spinOffModal.find('.progress-bar');

                            if (typeof response.success != 'undefined' && !response.success) {
                                instance.stopCurrentProcess();
                                progressBar.removeClass('progress-bar-success');
                                progressBar.addClass('progress-bar-danger');
                                spinOffModal.find('#process_spinoff_status').text(gptI18n.bulkGenerationProcessFail);
                                spinOffModal.find("#process_spinoff_error_log").show().html(ChatGptModule.renderErrorMessage(response, 'spinOffModal contentInstance.bulkProductDescription callback'));

                                // render close button
                                spinOffModal
                                    .setActions([])
                                    .addAction({
                                        title: gptI18n.buttonClose,
                                        class: 'btn btn-outline-secondary'
                                    }, function (actionInstance) {
                                        actionInstance.getModal().destroy();
                                    })
                                    .renderActions();

                                shouldBreak = true;
                                return;
                            } else if (typeof response.success != 'undefined' && response.success) {
                                var inQueue = false;
                                inQueue = response.products[0].inQueue;

                                if (inQueue) {
                                    var requestId = 0;
                                    requestId = response.products[0].requestId;

                                    // await complete response
                                    requestInfo = await instance.awaitRequestResponse(requestId);

                                    // check request info
                                    if (requestInfo) {
                                        if (requestInfo.status != 'success') {
                                            // if the request sattus is not success then display the error
                                            instance.stopCurrentProcess();
                                            progressBar.removeClass('progress-bar-success');
                                            progressBar.addClass('progress-bar-danger');
                                            modal.find('#process_generate_status').text(gptI18n.bulkGenerationProcessFail);
                                            // modal.find("#process_generate_error_log").show().text(requestInfo.text);
                                            if (requestInfo.status == 'quota_over') {
                                                modal.find("#process_generate_error_log").show().text(gptI18n.subscriptionLimitЕxceeded);
                                            } else {
                                                window.showErrorMessage(requestInfo.text);
                                                modal.find("#process_generate_error_log").show().text(requestInfo.text);
                                            }

                                            // render close button
                                            modal
                                                .setActions([])
                                                .addAction({
                                                        title: gptI18n.buttonClose,
                                                        class: 'btn btn-outline-secondary'
                                                    }, function (actionInstance) {
                                                        actionInstance.getModal().destroy();
                                                    })
                                                .renderActions();

                                            return;
                                        } else if (requestInfo.text) {
                                            // set description
                                            await instance.setContent(
                                                response.products[0].idProduct,
                                                'product',
                                                contentGenerate[j],
                                                requestInfo.text,
                                                options.replace,
                                                false, // translate
                                                languageId
                                            );
                                        }
                                    }
                                }
                            }

                            progressBar.css('width', `${requestCounter * 100 / totalRequest}%`);
                            progressBar.find('span').html(`${i+1} / ${numberOfSpinOff} (${(requestCounter * 100 / totalRequest).toFixed(2)}%)`);
                        }, contentGenerate[j]);
                    }
                }

                if (!isUseChatGPT) {
                    requestCounter++;

                    var progressBar = spinOffModal.find('.progress-bar');
                    progressBar.css('width', `${requestCounter * 100 / totalRequest}%`);
                    progressBar.find('span').html(`${i+1} / ${numberOfSpinOff} (${(requestCounter * 100 / totalRequest).toFixed(2)}%)`);
                }

                if ((requestCounter * 100 / totalRequest) >= 100) {
                    var progressBar = spinOffModal.find('.progress-bar');

                    spinOffModal
                        .setActions([])
                        .addAction({
                            title: gptI18n.buttonClose,
                            class: 'btn btn-outline-secondary'
                        }, function (actionInstance) {
                            actionInstance.getModal().destroy();
                        })
                        .renderActions();

                    spinOffModal.find('#process_spinoff_success_log').show().text(gptI18n.bulkCreationProcessCompleted);
                    progressBar.removeClass('progress-bar-animated');

                    readAjaxContent();
                }
            }

            contentInstance.activateAllProcess();
        }).open(function () {
            // init tooltips
            $(".gpt-tooltip").popover();
        });

        spinOffModal.open();

        $('input[name="spinoff_usechatgpt"]').on('change', function() {
            var selectedValue = $(this).val();

            if (selectedValue === '0') {
                $('#spinoff_rewrite_description').hide(300);
                $('#spinoff_rewrite_short_description').hide(300);
            } else if (selectedValue === '1') {
                $('#spinoff_rewrite_description').show(300);
                $('#spinoff_rewrite_short_description').show(300);
            }
        });
    });

    /**
     * Handle error and print in modal body
     */
    function handleModalError (modal, errorMessage) {
        if (!!modal.find('.progress-bar') && modal.find('.progress-bar').length) {
            progressBar = modal.find('.progress-bar');
            progressBar.removeClass('progress-bar-success');
            progressBar.addClass('progress-bar-danger');
            modal.find('#process_spinoff_status').text(gptI18n.bulkRewriteProcessFail);
            modal.find("#process_spinoff_error_log").show().text(errorMessage);
        } else {
            // clean body
            modal.find('body').html('');
            // render message
            renderAlertMessage(errorMessage, modal.find('body'));
        }

        // render close button
        modal
            .setActions([])
            .addAction({
                title: gptI18n.buttonClose,
                class: 'btn btn-outline-secondary'
            }, function (closeButton) {
                closeButton.getModal().destroy();
            })
            .renderActions();
    }

    function renderAlertMessage(messageText, element) {
        var object = $('<div class="alert alert-danger mt-2" role="alert">' +
            '<p class="alert-text">' + messageText + '</p>' +
            '</div>');
        if (!!element && element.length) {
            element.append(object);
        }
    }

    function removeActiveTab(){
        const ul = document.getElementById('form-nav');
        const listItems = ul.getElementsByTagName('a');
        for (let i = 0; i <= listItems.length - 1; i++) {
            listItems[i].classList.remove('active');
        }
        document.getElementById('_step7').classList.add('active');
        removeActiveTabContent();
    }

    function removeActiveTabContent(){
        const div = document.getElementById(gptVarVersion.selectors.pfTabsContentId);
        const listItems = div.getElementsByClassName('form-contenttab');
        for (let i = 0; i <= listItems.length - 1; i++) {
            listItems[i].classList.remove('active');
        }
        document.getElementById('step7').classList.add('active');

        readAjaxContent();
    }

    function readAjaxContent(){
        $.ajax({
            type: "POST",
            url: gptSpinOffAjaxUrl,
            data: {
                ajax: 1,
                action: 'ProductSettingsSpinOffTabContent',
                idProduct: idProduct,
            },
            dataType: "json",
            crossDomain: true,
            async: true,
            success: function(response){
                if (response.success) {
                    $('#step7').html(response.tab_content);
                }
            },
        });
    }

    function sendAjaxCreateSpinOff(numberOfSpinOff, spinOffStock){
        return new Promise(function(resolve) {
            $.ajax({
                type: "POST",
                url: gptSpinOffAjaxUrl,
                data: {
                    ajax: 1,
                    action: 'ProductSettingsSpinOffCreate',
                    idProduct: idProduct,
                    numberOfSpinOff: numberOfSpinOff,
                    spinOffStock: spinOffStock,
                },
                dataType: "json",
                crossDomain: true,
                async: true,
                success: function (response) {
                    resolve(response);
                },
            });
        });
    }

    $('#' + gptVarVersion.selectors.pfTabsContentId).on('click', '.delete-spin-off', function (){

        var spinoffId = $(this).data('spinoffid');

        // init spin-off form //////////////////////////////////////
        var spinOffModal = new ChatGptModal({
            closable: false,
            keyboard: false,
            backdrop: false,
        });

        spinOffModal
            .setHeader(gptI18n.deleteSpinOff + spinoffId)
            .setBody(
                '<div>' + ChatGptForm.spinOffDeleteConfirmation() + '</div>'
            )
        ;
        // END init spin-off form //////////////////////////////////////

        spinOffModal.setActions([])
            .addAction({
                title: gptI18n.buttonCancel,
                class: 'btn btn-outline-secondary'
            }, function (cancelButton) {
                cancelButton.getModal().destroy();
            }).addAction({
            title: gptI18n.buttonDeleteNow,
        }, async function (actionInstance) {
            try {
                var resultDeleteSpinOff = await sendAjaxDeleteSpinOff(spinoffId);

                if (!resultDeleteSpinOff.success) {
                    handleModalError(actionInstance.getModal(), resultDeleteSpinOff.error.message);
                    return;
                }

            } catch (err) {
                handleModalError(actionInstance.getModal(), err);
                return;
            }

            spinOffModal
                .setActions([])
                .addAction({
                    title: gptI18n.buttonClose,
                    class: 'btn btn-outline-secondary'
                }, function (actionInstance) {
                    actionInstance.getModal().destroy();
                })
                .renderActions();

            spinOffModal.find('#process_spinoff_success_log').show().text(gptI18n.spinoffDeletedSuccessfully);

            readAjaxContent();
        });

        spinOffModal.open();
    });

    function sendAjaxDeleteSpinOff(id_product){
        return new Promise(function(resolve) {
            $.ajax({
                type: "POST",
                url: gptSpinOffAjaxUrl,
                data: {
                    ajax: 1,
                    action: 'DeleteSpinOff',
                    id_product: id_product,
                },
                dataType: "json",
                crossDomain: true,
                async: true,
                success: function (response) {
                    resolve(response);
                },
            });
        });
    }
});

