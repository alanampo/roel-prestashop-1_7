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

var GptPostContent = (function () {
    function GptPostContent () {

    }

    GptPostContent.postFormEventHandler = function (modal, getData, onGenerationCompleted) {
        var uploader = null;
        modal
            .setActions([])
            .addAction({
                    title: gptI18n.buttonCancel,
                    class: 'btn btn-default'
                }, function (cancelButton) {
                    cancelButton.getModal().destroy();
                })
            .addAction({
                    title: gptI18n.buttonGenerate,
                }, async function (actionInstance) {
                    // define bulk options
                    var options = {
                        replace: +$("#allow_gen_content_0").is(':checked'),
                        maxWords: +$("#gpt_description_length").val(),
                        languages: [],
                        idContentTemplate: 0,
                        images: [],
                    };

                    if (options.idContentTemplate > 0) {
                        options.languages = gptContentTemplates[options.idContentTemplate].langs;
                    } else {
                        // define selected languages
                        $('.gpt-languages-list').each(function () {
                            var value = +($(this).is(':checked') ? this.value : 0);
                            if (value != 0 && options.languages.indexOf(value) == -1) {
                                options.languages.push(value);
                            }
                        });
                    }

                    if (isNaN(options.maxWords) || options.maxWords < gptPageSettings[adminPageName].minWords) {
                        alert(gptI18n.maxWordsNotValid.replace('%min_words%', gptPageSettings[adminPageName].minWords));
                        return;
                    }

                    if (options.languages.length == 0) {
                        alert(gptI18n.pleaseSelectLanguages);
                        return;
                    }

                    var content = new ChatGptContent(),
                        postName = '',
                        gptWidgetSettings = false;

                    try {
                        var data = typeof getData == 'function' ? getData(actionInstance.getModal()) : {postName: ''};
                        gptWidgetSettings = data.gptWidgetSettings;
                        postName = data.postName;
                    } catch (err) {
                        alert(err);
                        return;
                    }

                    // console.log(postName);
                    // console.log(options);
                    // return;

                    ChatGptContent.renderLoaderlayer($('body'));

                    // upload selected files
                    var response = await uploader.upload(),
                        files = []; // store files list in the separate variable to remove them after generation process
                    if (response.success) {
                        for (var i in response.files) {
                            files.push(response.files[i]);
                            options.images.push({
                                id: i,
                                save_path: response.files[i].save_path,
                            });
                        }
                    }

                    function handlePostContent (postContent, idLang) {
                        var text = postContent.text;
                        if (options.replace == false) {
                            var existingContent = content.getContentFromEditor(document.getElementById(gptWidgetSettings.descriptionEditorPrefix + idLang), 'html');
                            text = (!!existingContent ? existingContent : '') + text;
                        }
                        content.setContentIntoEditor(
                            text,
                            {format: 'html'},
                            tinymce.get(gptWidgetSettings.descriptionEditorPrefix + idLang)
                        );

                        /* get first paragraph from content and set to short description */
                        try {
                            var shortContentEditor = document.getElementById('short_content_' + idLang);
                            shortContentText = content.getContentFromEditor(shortContentEditor, 'html');
                            if (shortContentText.trim() === '' || shortContentText.trim() === '<p></p>') {
                                var p = text.match(/<p>((.|\r\n|\r|\n)*?)<\/p>/);
                                if (!!p && typeof p[0] != 'undefined') {
                                    content.setContentIntoEditor(
                                        p[0],
                                        {format: 'html'},
                                        tinymce.get('short_content_' + idLang)
                                    );
                                }
                            }
                        } catch (err) {}
                        /******* END get first paragraph from content and set to short description */

                        window.showSuccessMessage('Language: ' + content.getLanguageById(idLang).iso_code.toUpperCase() + ': ' + gptI18n.successMessage.replace(/\%words\%/g, postContent.nbWords));
                    }

                    try {
                        var langIndex = 0;
                        // generate content for the first language
                        var postContent = await content.generatePostContent(
                                postName,
                                Object.assign({}, options, {idLang: options.languages[langIndex]}),
                                true
                            );
                        if (postContent && postContent.text) {
                            handlePostContent(postContent, options.languages[langIndex]);

                            // translate current content for other languages
                            for (langIndex = 1; langIndex < options.languages.length; langIndex++) {
                                var translatedContent = await content.translateText(postContent.text, {
                                    fromLangauge: content.getLanguageById(options.languages[0]).iso_code,
                                    toLanguage: content.getLanguageById(options.languages[langIndex]).iso_code,
                                    entity: 'page'
                                }, true);

                                handlePostContent(translatedContent, options.languages[langIndex]);
                            }
                        }
                        // delete temporary files
                        if (files.length) {
                            uploader.deleteFiles(files);
                        }
                        (typeof onGenerationCompleted == 'function') && onGenerationCompleted(modal);
                    } catch (err) {
                        ChatGptContent.removeLoaderLayer();
                        ChatGptForm.handleModalError(
                            actionInstance.getModal(),
                            'Language: ' + content.getLanguageById(options.languages[langIndex]).name + '<br/>' + err
                        );
                        return;
                    }

                    ChatGptContent.removeLoaderLayer();
                })
            .open(function (modal) {
                // init tooltips
                $(".gpt-tooltip").popover();

                uploader = new ChatGptFilesUploader({wraper: modal.find('#post-images')});
                uploader.init();
            });
    }

    /**
     * Handle error and print in modal body
     */
    GptPostContent.productPostEventHandler = function (modal, getData, onGenerationCompleted) {
        var uploader = null;
        modal
            .setBody(
                '<div>' + ChatGptForm.productPostForm() + '</div>'
            )
            .setActions([])
            .addAction({
                    title: gptI18n.buttonCancel,
                    class: 'btn btn-outline-secondary'
                }, function (cancelButton) {
                    cancelButton.getModal().destroy();
                })
            .addAction({
                    title: gptI18n.buttonGenerate,
                }, async function (actionInstance) {
                    var content = new ChatGptContent(),
                        modal = actionInstance.getModal(),
                        data = {},
                        products = [];
                    var options = {
                        languages: [],
                        idLang: 0,
                        maxWords: +modal.find("#gpt_description_length").val(),
                        useProductCategory: +modal.find("#use_product_category_1").is(':checked'),
                        useProductBrand: +modal.find("#use_product_brand_1").is(':checked'),
                        images: [],
                    };

                    try {
                        data = typeof getData == 'function' ? getData(actionInstance.getModal()) : {}
                    } catch (err) {
                        alert(err);
                        return;
                    }

                    if (typeof data[0] == 'undefined') {
                        products.push(data);
                    } else {
                        products = Object.assign([], data);
                    }

                    // data = Object.assign({}, {
                        
                    // }, data)

                    // define selected languages
                    modal.find('.gpt-languages-list').each(function () {
                        var value = +($(this).is(':checked') ? this.value : 0);
                        if (value != 0 && options.languages.indexOf(value) == -1) {
                            options.languages.push(value);
                        }
                    });

                    var posts = [];

                    ChatGptContent.renderLoaderlayer($('body'));

                    // upload selected files
                    var response = await uploader.upload(),
                        files = []; // store files list in the separate variable to remove them after generation process
                    if (response.success) {
                        for (var i in response.files) {
                            files.push(response.files[i]);
                            options.images.push({
                                id: i,
                                save_path: response.files[i].save_path,
                            });
                        }
                    }

                    try {
                        for (var i = 0; i < products.length; i++) {
                            // generate content for the first language
                            var postContent = await content.generateProductPost(
                                    products[i].productName,
                                    Object.assign({}, options, products[i], {idLang: options.languages[0]}),
                                    true
                                );
                            // add generate post to array
                            posts.push(postContent.post);
                            // translate current post for other languages
                            for (var langIndex = 1; langIndex < options.languages.length; langIndex++) {
                                var response = await content.translateProductPostById(
                                        postContent.post.id,
                                        {
                                            fromLangaugeId: options.languages[0],
                                            toLanguageId: options.languages[langIndex]
                                        },
                                        true
                                    );
                            }
                        }
                        // delete temporary files
                        if (files.length) {
                            uploader.deleteFiles(files);
                        }
                        (typeof onGenerationCompleted == 'function') && onGenerationCompleted(modal, posts, products);
                    } catch (err) {
                        ChatGptContent.removeLoaderLayer();
                        // handleModalError(
                        //     actionInstance.getModal(),
                        //     'Language: ' + content.getLanguageById(options.languages[langIndex]).name + '<br/>' + err
                        // );
                        console.error(err);
                        return;
                    }

                    ChatGptContent.removeLoaderLayer();
                })
            .open(function (modal) {
                // init tooltips
                $(".gpt-tooltip").popover();

                uploader = new ChatGptFilesUploader({wraper: modal.find('#post-images')});
                uploader.init();
            });
    }

    return GptPostContent;
})();

document.addEventListener('DOMContentLoaded', function() {
    // extend the ChatGptContent to add the methods to generate the product posts
    ChatGptContent.prototype.generateProductPost = async function(productName, options, throwError) {
        var _options = Object.assign({}, {
            productCategory: '',
            idDefaultCategory: 0,
            idBrand: 0,
            idLang: this.getPageLanguageId(),
            maxWords: 0,
            idProduct: 0,
            // useProductEan: 0,
            useProductCategory: 1,
            useProductBrand: 1,
            // idContentTemplate: 0,
            images: [],
        }, options);
        var response = await ChatGptContent.sendRequest({
            url: this.getOptions().postEndPoint,
            data: {
                ajax: 1,
                action: 'generateProductPost',
                id: (_options.idProduct ? _options.idProduct : idProduct),
                name: productName,
                category: _options.productCategory,
                id_language: _options.idLang,
                length: _options.maxWords,
                id_category_default: _options.idDefaultCategory,
                id_manufacturer: _options.idBrand,
                use_category: _options.useProductCategory,
                use_brand: _options.useProductBrand,
                images: _options.images,
                // use_ean: _options.useProductEan,
                // id_content_template: _options.idContentTemplate,
            }
        });

        if (typeof response.success != 'undefined' && response.success) {
            return {
                requestId: (typeof response.requestId != 'undefined' ? response.requestId : 0),
                inQueue: (typeof response.inQueue != 'undefined' ? response.inQueue : false),
                post: response.post,
                text: response.text,
                length: response.text.length,
                nbWords: response.nbWords
            }
        } else if (typeof response.success != 'undefined' && !response.success) {
            var message = ChatGptModule.renderErrorMessage(response, 'generateProductPost');
            if (!!throwError === true) {
                throw new Error(message);
                return false;
            }
            window.showErrorMessage(message);
        }

        return false;
    };

    // extend the ChatGptContent to add the methods to translate the product posts
    ChatGptContent.prototype.translateProductPostById = async function(postId, options, throwError) {
        var _options = Object.assign({}, {
            fromLangaugeId: 0,
            toLanguageId: 0,
        }, options);
        var response = await ChatGptContent.sendRequest({
            url: this.getOptions().postEndPoint,
            data: {
                ajax: 1,
                action: 'translateProductPostById',
                id: postId,
                fromLangaugeId: options.fromLangaugeId,
                toLanguageId: options.toLanguageId,
            }
        });

        if (typeof response.success != 'undefined' && response.success) {
            return {
                requestId: (typeof response.requestId != 'undefined' ? response.requestId : 0),
                inQueue: (typeof response.inQueue != 'undefined' ? response.inQueue : false),
                text: response.text,
                length: response.text.length,
                nbWords: response.nbWords
            }
        } else if (typeof response.success != 'undefined' && !response.success) {
            var message = ChatGptModule.renderErrorMessage(response, 'translateProductPostById');
            if (!!throwError === true) {
                throw new Error(message);
                return false;
            }
            window.showErrorMessage(message);
        }

        return false;
    };

    // extend the ChatGptContent to add the methods to generate posts
    ChatGptContent.prototype.generatePostContent  = async function(postName, options, throwError) {
        var _options = Object.assign({}, {
                idLang: 0,
                maxWords: 0,
                idContentTemplate: 0,
                images: []
            }, options);

        var response = await ChatGptContent.sendRequest({
            url: this.getOptions().postEndPoint,
            data: {
                ajax: 1,
                action: 'generatePostContent',
                name: postName,
                id_language: _options.idLang,
                length: _options.maxWords,
                id_content_template: _options.idContentTemplate,
                images: _options.images,
            }
        });

        if (typeof response.success != 'undefined' && response.success) {
            return {
                requestId: (typeof response.requestId != 'undefined' ? response.requestId : false),
                inQueue: (typeof response.inQueue != 'undefined' ? response.inQueue : false),
                text: response.text,
                length: response.text.length,
                nbWords: response.nbWords
            }
        } else if (typeof response.success != 'undefined' && !response.success) {
            var message = ChatGptModule.renderErrorMessage(response, 'generatePostContent');
            if (!!throwError === true) {
                throw new Error(message);
                return false;
            }
            window.showErrorMessage(message);
        }

        return false;
    };
});
