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

var ChatGptContent = (function() {
    function ChatGptContent () {
        var processStatus = true;
        var cancelAllProcessStatus = false;

        this.setProcessStatus = function (status) {
            processStatus = status;
            return this;
        }

        this.getProcessStatus = function () {
            return processStatus;
        }

        this.setCancelAllProcessStatus = function (status) {
            cancelAllProcessStatus = status;
            return this;
        }

        this.getCancelAllProcessStatus = function () {
            return cancelAllProcessStatus;
        }
    }

    ChatGptContent.j = $;

    ChatGptContent.prototype.stopCurrentProcess = function() {
        return this.setProcessStatus(false);
    };

    ChatGptContent.prototype.stopAllProcess = function() {
        return this.setCancelAllProcessStatus(true);
    };

    ChatGptContent.prototype.activateAllProcess = function() {
        return this.setCancelAllProcessStatus(false);
    };

    ChatGptContent.prototype.getOptions = function() {
        return {
            endPoint: gptAjaxUrl,
            postEndPoint: gptPostAjaxUrl,
        };
    };

    ChatGptContent.prototype.getLanguageByIsoCode = function(isoCode) {
        for (var i = 0; i < gptLanguages.length; i++) {
            if (isoCode == gptLanguages[i].iso_code) {
                return gptLanguages[i];
            }
        }

        return false;
    };

    ChatGptContent.prototype.getLanguageById = function(idlang) {
        for (var i = 0; i < gptLanguages.length; i++) {
            if (gptLanguages[i].id_lang == idlang) {
                return gptLanguages[i];
            }
        }

        return this.getPageLanguage();
    };

    ChatGptContent.prototype.getPageLanguageId = function() {
        if (typeof adminPageName == 'undefined') {
            throw new Error('The admin page is not defined');
        }

        if (adminPageName == 'productForm') {
            if (gptIsNewVersion) {
                var isoCode = document.getElementById(gptVarVersion.selectors.pfIsoCodeId)
                    ? document.getElementById(gptVarVersion.selectors.pfIsoCodeId).innerText
                    : '';
            } else {
                var isoCode = document.getElementById(gptVarVersion.selectors.pfIsoCodeId)
                    ? document.getElementById(gptVarVersion.selectors.pfIsoCodeId).value
                    : '';
            }
            var lang = this.getLanguageByIsoCode(isoCode);
            if (lang) {
                return +lang.id_lang;
            }
        } else if (adminPageName == 'categoryForm' || adminPageName == 'cmsForm' || adminPageName == 'postForm') {
            if (isLegacyController) {
                if (typeof id_language != 'undefined') {
                    return id_language;
                }
            }
            var isoCode = (typeof prestashop != 'undefined' && !!prestashop.instance)
                ? (!!prestashop.instance.translatableField ? ( typeof prestashop.instance.translatableField.getSelectedLocale == 'function'
                        ? prestashop.instance.translatableField.getSelectedLocale()
                        : prestashop.instance.translatableField.selectedLocale
                    ) : false)
                : $('.translationsLocales.nav .nav-item a.active[data-toggle="tab"]').data('locale');

            if (isoCode && (lang = this.getLanguageByIsoCode(isoCode))) {
                return +lang.id_lang;
            }
        }

        return (typeof gptLanguageId != 'undefined')
            ? gptLanguageId
            : ((typeof default_language != 'undefined') ? default_language : 1);
    };

    ChatGptContent.prototype.getPageLanguage = function() {
        var idLang = this.getPageLanguageId();
        for (var i = 0; i < gptLanguages.length; i++) {
            if (gptLanguages[i].id_lang == idLang) {
                return gptLanguages[i];
            }
        }

        return gptLanguages[0];
    };

    ChatGptContent.prototype.setContentIntoEditor = function(content, options, input) {
        var editor = false;

        if (typeof input.setContent == 'function') {
            editor = input;
        } else {
            if (typeof content == 'undefined') {
                throw new Error('The content is not defined');
            }

            if (typeof input == 'string') {
                input = document.getElementById(input);
            }

            if (input.tagName == 'INPUT') {
                input.value = content;
                return;
            }

            if (input.tagName == 'TEXTAREA') {
                var editorId = input.getAttribute('id');

                if (!!tinymce.get(editorId) != false) {
                    editor = tinymce.get(editorId);
                } else {
                    // update content in the textarea
                    document.getElementById(editorId).innerHTML = content;
                    document.getElementById(editorId).value = content;
                    return;
                }
            }
        }

        if (!!editor === false || typeof editor.setContent != 'function') {
            throw new Error('The editor is not defined');
        }

        options = (typeof options == 'undefined') ? {} : options;

        try {
            // update content in the tinyMCE
            editor.setContent(content, options);
            // update content in the textarea
            document.getElementById(editor.id).innerHTML = content;
            document.getElementById(editor.id).value = content;
            // hide placeholder id exists
            blocks = editor.getContentAreaContainer().getElementsByTagName('div')
            if (blocks.length) {
                // call trigger click
                blocks[0].click();
            }
        } catch (e) {}
    };

    ChatGptContent.prototype.textIsHtml = function (text) {
        if (!!text === false) {
            return false;
        }
        return (new RegExp('<\\/?[a-z][\\s\\S]*>')).test(text);
    }

    ChatGptContent.prototype.convertTextToHtml = function(text) {
        if (this.textIsHtml(text)) {
            return '<p>' + text + '</p>';
        }
        return text.split("\n").map(function (textPart) {
                                        return '<p>' + textPart + '</p>';
                                    }).join('');
    };

    ChatGptContent.prototype.getContentFromEditor = function(editor, format) {
        if (typeof editor == 'string') {
            editor = document.getElementById(editor);
        }

        if (!!editor == false) {
            return false;
        }

        if (editor.tagName == 'INPUT') {
            return editor.value;
        }

        if (editor.tagName == 'TEXTAREA') {
            var editorId = editor.getAttribute('id');

            if (!!tinymce.get(editorId) != false) {
                return tinymce.get(editorId)
                    .getContent({format: format})
                    .trim();
            }

            return document.getElementById(editorId).value;
        }

        return false;
    };

    ChatGptContent.renderLoaderlayer = function(el) {
        el.length > 0 && el.css('position', 'relative')
                .append('<div id="gpt_description_loader" class="content-loader-layer"><div class="loader-wrapper"><div class="content-loader"></div></div></div>');
    }

    ChatGptContent.removeLoaderLayer = function () {
        $("#gpt_description_loader").remove();
    }

    ChatGptContent.sendRequest = async function (options, onSuccess, onError) {
        return await request(options, onSuccess, onError);
    }

    async function request(options, onSuccess, onError) {
        function doRequest (options) {
            return new Promise(function (resolve, reject) {
                    $.ajax(Object.assign({}, options, {
                        success: function (data) {
                            resolve(data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            reject(errorThrown);
                        }
                    }));
                });
        }

        options = Object.assign({}, {
            type: 'POST',
            url: '#',
            dataType: 'json',
        }, options, {
            cache: false,
            async: true
        });

        return await doRequest(options)
                            .then(function (response) {
                                typeof onSuccess == 'function' && onSuccess(response);
                                return response;
                            })
                            .catch(function (reason) {
                                typeof onError == 'function' && onError(reason);
                                return {
                                    success: false,
                                    error: {
                                        code: 500,
                                        message: reason
                                    }
                                }
                            });
    }

    ChatGptContent.prototype.bulkProductDescription = async function(products, options, callback, contentType = 'description') {
        if (typeof products == 'undefined' && products.length == 0) {
            return;
        }

        var idLang = this.getPageLanguageId(),
            result = [],
            _options = Object.assign({}, {
                useProductCategory: 1,
                useProductBrand: 1,
                useProductEan: 0,
                contentType: contentType,
                replace: gptPageSettings.productsList.replaceContent,
                skipExistingContent: gptPageSettings.productsList.generateIfEmpty,
                maxWords: gptPageSettings.productsList,
                languages: [idLang],
                idContentTemplate: 0,
            }, options);

        for (var i = 0; i < products.length; i++) {
            for (var langIndex = 0; langIndex < _options.languages.length; langIndex ++) {
                if (this.getProcessStatus() === false) {
                    console.warn('The process has been stopped');
                    this.setProcessStatus(true); // allow to run the another process
                    return result;
                    break;
                }

                if (this.getCancelAllProcessStatus() === true) {
                    console.warn('All processes are stopped');
                    return;
                }

                var response = await request({
                    url: this.getOptions().endPoint,
                    data: {
                        ajax: 1,
                        action: 'bulkProductDescription',
                        replace: +_options.replace,
                        skip_existing_description: +_options.skipExistingContent,
                        ids: products[i],
                        id_language: +_options.languages[langIndex],
                        length: _options.maxWords,
                        use_category: _options.useProductCategory,
                        use_brand: _options.useProductBrand,
                        use_ean: _options.useProductEan,
                        content_type: _options.contentType,
                        id_content_template: _options.idContentTemplate,
                    }
                });

                if (typeof callback == 'function') {
                    await callback(products[i], i, response, +_options.languages[langIndex], langIndex, this);
                }
            }

            if (typeof response.success != 'undefined' && response.success) {
                result.push({
                    idProduct: products[i],
                    error: false,
                    requestId: (typeof response.products[0].requestId != 'undefined' ? response.products[0].requestId : false),
                    inQueue: (typeof response.products[0].inQueue != 'undefined' ? response.products[0].inQueue : false),
                    text: response.products[0].text,
                    length: response.products[0].text.length,
                    nbWords: response.products[0].nbWords
                });
            } else if (typeof response.success != 'undefined' && !response.success) {
                var errorMessage = ChatGptModule.renderErrorMessage(response, 'bulkProductDescription');
                if (typeof response.error.code != 'undefined' && response.error.code == 18) {
                    errorMessage = gptI18n.subscriptionLimitЕxceeded + ' ' + gptI18n.renewOrOrderSubscription;
                }
                result.push({
                    idProduct: products[i],
                    error: true,
                    message: errorMessage
                });
            }
        }

        return result;
    };

    ChatGptContent.prototype.getProductDescription = async function(productName, options, throwError) {
        var _options = Object.assign({}, {
            productCategory: '',
            idDefaultCategory: 0,
            idBrand: 0,
            idLang: 0,
            maxWords: 0,
            idProduct: 0,
            useProductEan: 0,
            contentType: 'description',
            useProductCategory: 1,
            useProductBrand: 1,
            idContentTemplate: 0,
        }, options);
        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'productDescription',
                id: (_options.idProduct ? _options.idProduct : idProduct),
                name: productName,
                category: _options.productCategory,
                id_language: _options.idLang,
                length: _options.maxWords,
                id_category_default: _options.idDefaultCategory,
                id_manufacturer: _options.idBrand,
                use_category: _options.useProductCategory,
                use_brand: _options.useProductBrand,
                use_ean: _options.useProductEan,
                content_type: _options.contentType,
                id_content_template: _options.idContentTemplate,
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
            var message = ChatGptModule.renderErrorMessage(response, 'productDescription');
            if (!!throwError === true) {
                throw new Error(message);
                return false;
            }
            window.showErrorMessage(message);
        }

        return false;
    };

    ChatGptContent.prototype.bulkTranslateObjects = async function(objectIds, options, callback) {
        if (typeof objectIds == 'undefined' && objectIds.length == 0) {
            return;
        }

        var idLang = this.getPageLanguageId(),
            result = [],
            _options = Object.assign({}, {
                replace: 1,
                skipExistingContent: 1,
                originLanguageId: +idLang,
                targetLanguages: [],
                entity: '',
                field: 'description', // set description field as default
            }, options);

        if (typeof _options.entity == 'undefined' || !!_options.entity === false || _options.entity.trim() === '') {
            throw new Error('Entity is not defined');
        }

        if (typeof _options.targetLanguages == 'undefined' || _options.targetLanguages.length == 0) {
            throw new Error('Target languages is not defined');
        }

        for (var i = 0; i < objectIds.length; i++) {
            for (var langIndex = 0; langIndex < _options.targetLanguages.length; langIndex ++) {
                if (this.getProcessStatus() === false) {
                    console.warn('The process has been stopped');
                    this.setProcessStatus(true); // allow to run the another process
                    return result;
                    break;
                }

                if (this.getCancelAllProcessStatus() === true) {
                    console.warn('All processes are stopped');
                    return;
                }

                var response = await request({
                    url: this.getOptions().endPoint,
                    data: {
                        ajax: 1,
                        action: 'bulkTranslateText',
                        replace: +_options.replace,
                        skip_existing_description: +_options.skipExistingContent,
                        ids: objectIds[i],
                        fromLangauge: +_options.originLanguageId,
                        toLanguages: [+_options.targetLanguages[langIndex]],
                        length: _options.maxWords,
                        entity: _options.entity,
                        field: _options.field,
                    }
                });

                if (typeof callback == 'function') {
                    await callback(objectIds[i], i, response, +_options.targetLanguages[langIndex], langIndex, this);
                }
            }

            if (typeof response.success != 'undefined' && response.success) {
                result.push({
                    idObject: objectIds[i],
                    error: false,
                    requestId: (typeof response.objects[0].requestId != 'undefined' ? response.objects[0].requestId : false),
                    inQueue: (typeof response.objects[0].inQueue != 'undefined' ? response.objects[0].inQueue : false),
                    text: response.objects[0].text,
                    length: response.objects[0].text.length,
                    nbWords: response.objects[0].nbWords
                });
            } else if (typeof response.success != 'undefined' && !response.success) {
                result.push({
                    idObject: objectIds[i],
                    error: true,
                    message: ChatGptModule.renderErrorMessage(response, 'bulkTranslateText'),
                });
            }
        }

        return result;
    };

    ChatGptContent.prototype.bulkRewriteObjects = async function(objectIds, options, callback) {
        if (typeof objectIds == 'undefined' && objectIds.length == 0) {
            return;
        }

        var idLang = this.getPageLanguageId(),
            result = [],
            _options = Object.assign({}, {
                replace: 1,
                languages: [idLang],
                entity: '',
                fields: [],
            }, options);

        if (adminPageName == 'productsList') {
            _options.entity = 'product';
        } else if (adminPageName == 'categoriesList') {
            _options.entity = 'category';
        }

        if (typeof _options.entity == 'undefined' || !!_options.entity === false || _options.entity.trim() === '') {
            throw new Error('Entity is not defined');
        }

        if (_options.fields.length == 0) {
            throw new Error('Target fields is not defined');
        }

        if (typeof _options.languages == 'undefined' || _options.languages.length == 0) {
            throw new Error('Target languages is not defined');
        }

        for (var i = 0; i < objectIds.length; i++) {
            for (var langIndex = 0; langIndex < _options.languages.length; langIndex ++) {
                for (var fieldIndex = 0; fieldIndex < _options.fields.length; fieldIndex++) {
                    if (this.getProcessStatus() === false) {
                        console.warn('The process has been stopped');
                        this.setProcessStatus(true); // allow to run the another process
                        return result;
                        break;
                    }
                    if (this.getCancelAllProcessStatus() === true) {
                        console.warn('All processes are stopped');
                        return;
                    }
                    var response = await request({
                        url: this.getOptions().endPoint,
                        data: {
                            ajax: 1,
                            action: 'bulkRewriteText',
                            replace: +_options.replace,
                            ids: objectIds[i],
                            id_language: +_options.languages[langIndex],
                            entity: _options.entity,
                            field: _options.fields[fieldIndex],
                        }
                    });

                    if (typeof callback == 'function') {
                        await callback(objectIds[i], i, response, +_options.languages[langIndex], langIndex, fieldIndex, this);
                    }
                }
            }

            if (typeof response.success != 'undefined' && response.success) {
                result.push({
                    idObject: objectIds[i],
                    error: false,
                    requestId: (typeof response.objects[0].requestId != 'undefined' ? response.objects[0].requestId : false),
                    inQueue: (typeof response.objects[0].inQueue != 'undefined' ? response.objects[0].inQueue : false),
                    text: response.objects[0].text,
                    length: response.objects[0].text.length,
                    nbWords: response.objects[0].nbWords
                });
            } else if (typeof response.success != 'undefined' && !response.success) {
                result.push({
                    idObject: objectIds[i],
                    error: true,
                    message: response.error.message
                });
            }
        }

        return result;
    };

    ChatGptContent.prototype.bulkCategoryDescription = async function(categories, options, callback) {
        if (typeof categories == 'undefined' && categories.length == 0) {
            return;
        }

        var idLang = this.getPageLanguageId(),
            result = [],
            _options = Object.assign({}, {
                useProductCategory: 1,
                useProductBrand: 1,
                useProductEan: 0,
                contentType: 'description',
                replace: gptPageSettings.productsList.replaceContent,
                skipExistingContent: gptPageSettings.productsList.generateIfEmpty,
                maxWords: gptPageSettings.productsList,
                languages: [idLang],
                idContentTemplate: 0,
            }, options);

        for (var i = 0; i < categories.length; i++) {
            for (var langIndex = 0; langIndex < _options.languages.length; langIndex ++) {
                if (this.getProcessStatus() === false) {
                    // console.warn('The process has been stopped');
                    this.setProcessStatus(true); // allow to run the another process
                    break;
                }
                if (this.getCancelAllProcessStatus() === true) {
                    console.warn('All processes are stopped');
                    return;
                }
                var response = await request({
                    url: this.getOptions().endPoint,
                    data: {
                        ajax: 1,
                        action: 'bulkCategoryDescription',
                        replace: +_options.replace,
                        ids: categories[i],
                        id_language: +_options.languages[langIndex],
                        length: _options.maxWords,
                        skip_existing_description: +_options.skipExistingContent,
                        id_content_template: _options.idContentTemplate,
                    }
                });

                if (typeof callback == 'function') {
                    await callback(categories[i], i, response, +_options.languages[langIndex], langIndex, this);
                }
            }

            if (typeof response.success != 'undefined' && response.success) {
                result.push({
                    idCategory: response.categories[0].idCategory,
                    error: false,
                    requestId: (typeof response.categories[0].requestId != 'undefined' ? response.categories[0].requestId : 0),
                    inQueue: (typeof response.categories[0].inQueue != 'undefined' ? response.categories[0].inQueue : false),
                    text: response.categories[0].text,
                    length: response.categories[0].text.length,
                    nbWords: response.categories[0].nbWords
                });
            } else if (typeof response.success != 'undefined' && !response.success) {
                result.push({
                    idCategory: categories[i],
                    error: true,
                    message: ChatGptModule.renderErrorMessage(response, 'bulkCategoryDescription')
                });
            }
        }

        return result;
    };

    ChatGptContent.prototype.getCategoryDescription  = async function(categoryName, options, throwError) {
        options = Object.assign({}, {idLang: 0, maxWords: 0, idContentTemplate: 0}, options);

        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'categoryDescription',
                id: idCategory,
                name: categoryName,
                id_language: options.idLang,
                length: options.maxWords,
                id_content_template: options.idContentTemplate
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
            var message = ChatGptModule.renderErrorMessage(response, 'categoryDescription');
            if (!!throwError === true) {
                throw new Error(message);
                return false;
            }
            window.showErrorMessage(message);
        }

        return false;
    };

    ChatGptContent.prototype.getPageContent  = async function(pageName, options, throwError) {
        options = Object.assign({}, {idLang: 0, maxWords: 0, idContentTemplate: 0}, options);

        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'pageContent',
                id: idCms,
                name: pageName,
                id_language: options.idLang,
                length: options.maxWords,
                id_content_template: options.idContentTemplate
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
            var message = ChatGptModule.renderErrorMessage(response, 'pageContent');
            if (!!throwError === true) {
                throw new Error(message);
                return false;
            }
            window.showErrorMessage(message);
        }

        return false;
    };

    ChatGptContent.prototype.translateText  = async function(text, options, throwError) {
        options = Object.assign({}, {fromLangauge: '', toLanguage: '', entity: ''}, options);

        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'translateText',
                text: text,
                fromLangauge: options.fromLangauge,
                toLanguage: options.toLanguage,
                entity: options.entity
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
            var message = ChatGptModule.renderErrorMessage(response, 'translateText');

            if (!!throwError === true) {
                throw new Error(message);
                return false;
            }
            window.showErrorMessage(message);
        }

        return false;
    };

    ChatGptContent.prototype.rewriteText  = async function(text, options, throwError) {
        options = Object.assign({}, {idLang: 0, entity: ''}, options);

        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'rewriteText',
                id_language: options.idLang,
                text: text,
                entity: options.entity,
                fieldName: options.fieldName,
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
            if (!!throwError === true) {
                throw new Error(response.error.message);
                return false;
            }
            window.showErrorMessage(response.error.message);
        }

        return false;
    };

    /**
     * send custom request from administrator
     *
     * avaialable entities: [product, category, page]
     *
     * @param string text
     * @param object options
     */
    ChatGptContent.prototype.customRequest  = async function(text, options) {
        options = Object.assign({}, {entity: ''}, options);

        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'customRequest',
                text: text,
                entity: options.entity
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
            window.showErrorMessage(response.error.message);
        }

        return false;
    };

    /**
     * Await request response if the request was set in queue
     *
     * @param int requestId
     */
    ChatGptContent.prototype.awaitRequestResponse = async function(requestId) {
        if (!!requestId === false) {
            throw new Error('requestId is not defined');
        }

        var self = this;

        const sleep = function (ms) {
            return new Promise(function (resolve) { setTimeout(resolve, ms); });
        }

        var iterator = 0;
        while (true) {
            if (this.getProcessStatus() === false) {
                console.warn('The process has been stopped');
                this.setProcessStatus(true); // allow to run the another process
                break;
            }
            if (this.getCancelAllProcessStatus() === true) {
                console.warn('All processes are stopped');
                return;
            }
            iterator ++;
            var response = await request({
                url: self.getOptions().endPoint,
                data: {
                    ajax: 1,
                    action: 'getRequestInfo',
                    id: requestId,
                }
            });

            if (typeof response.success != 'undefined' && response.success) {
                if (response.inQueue == false) {
                    return response;
                }
            } else if (typeof response.success != 'undefined' && !response.success) {
                window.showErrorMessage(response.error.message);
            }

            await sleep(1200).then(function () {
            });

            if ((iterator % 16) == 0) { // 16 * 1.2s = 20s approx
                window.showSuccessMessage(gptI18n.awaitingRequestResponse);
            }
        }
        return false;
    };

    /**
     * Set description for product or categpry
     *
     * @param int $id Entity id
     * @param entityType
     * @param description
     *
     */
    ChatGptContent.prototype.setDescription  = async function(id, entityType, description, replace, translate, idLang) {
        idLang = (typeof idLang == 'undefined') ? this.getPageLanguageId() : idLang;

        replace = (typeof replace == 'undefined' ? 1 : replace);
        replace = !!replace;

        translate = (typeof translate == 'undefined' ? 0 : translate);
        translate = !!translate;

        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'setDescription',
                description: description,
                id: id,
                entity: entityType,
                id_language: idLang,
                replace: +!!replace,
                translate: +!!translate
            }
        });

        if (typeof response.success != 'undefined' && response.success) {
            return response;
        } else if (typeof response.success != 'undefined' && !response.success) {
            window.showErrorMessage(response.error.message);
        }

        return false;
    };

    /**
     * Set content for entity
     *
     * @param int $id Entity id
     * @param entityType
     * @param content
     *
     */
    ChatGptContent.prototype.setContent  = async function(id, entityType, field, content, replace, translate, idLang) {
        idLang = (typeof idLang == 'undefined') ? this.getPageLanguageId() : idLang;

        replace = (typeof replace == 'undefined' ? 1 : replace);
        replace = !!replace;

        translate = (typeof translate == 'undefined' ? 0 : translate);
        translate = !!translate;

        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'setDescription',
                content: content,
                field: field,
                id: id,
                entity: entityType,
                id_language: idLang,
                replace: +!!replace,
                translate: +!!translate
            }
        });

        if (typeof response.success != 'undefined' && response.success) {
            return response;
        } else if (typeof response.success != 'undefined' && !response.success) {
            window.showErrorMessage(response.error.message);
        }

        return false;
    };

    ChatGptContent.prototype.setCookieValue = async function(name, value) {
        var response = await request({
            url: this.getOptions().endPoint,
            data: {
                ajax: 1,
                action: 'setCookieValue',
                name: name,
                value: value,
            }
        });

        if (typeof response.success != 'undefined' && response.success) {
            return response;
        } else if (typeof response.success != 'undefined' && !response.success) {
            window.showErrorMessage(response.error.message);
        }

        return true;
    };

    /**
     * Get html of language chooser
     *
     * @return string
     */
    ChatGptContent.getLanguageChooserHtml  = function(languages) {
        if (typeof languages == 'undefined') {
            languages = gptLanguages;
        }

        var _class = 'checkbox-' + (new Date()).getTime();
        var idLang = (new ChatGptContent()).getPageLanguageId();
        var languagesList = '';
        for (var i = 0; i < languages.length; i++) {
            languagesList +=
                '<tr>' +
                    '<td>' +
                        '<div class="checkbox">' +
                            '<div class="md-checkbox md-checkbox-inline"><label><input type="checkbox" class="gpt-languages-list ' + _class + '" name="" value="' + languages[i].id_lang + '" ' + (languages[i].id_lang == idLang ? 'checked="checked"' : '') +'><i class="md-checkbox-control"></i>' + languages[i].name + '</label></div>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
        }

        return '<div class="row type-choice gpt-modal-languages">' +
                        '<label class="control-label col-md-6 justify-content-end text-right">' + gptI18n.languages + ':</label>' +
                        '<div class="col-md-6">' +
                            '<div class="choice-table" style="max-height:170px">' +
                                '<table class="table table-bordered mb-0">' +
                                    '<thead>' +
                                        '<tr>' +
                                            '<th class="checkbox">' +
                                                '<div class="md-checkbox"><label><input type="checkbox" class="js-choice-table-select-all" onchange="$(\'.' + _class + '\').prop(\'checked\', $(this).prop(\'checked\'));"><i class="md-checkbox-control"></i> ' + gptI18n.selectAll + '</label></div>' +
                                            '</th>' +
                                        '</tr>' +
                                    '</thead>' +
                                    '<tbody>' +
                                        languagesList +
                                    '</tbody>' +
                                '</table>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
    };

    /**
     * Get html of fields chooser
     *
     * @return string
     */
    ChatGptContent.getFieldsChooserHtml  = function() {
        if (typeof adminPageName == 'undefined') {
            throw new Error('The admin page is not defined');
        }

        var _class = 'checkbox-fileds-' + (new Date()).getTime(),
            fieldsList = '';

        if (adminPageName == 'productsList') {
            var fields = {
                description_short: gptI18n.shortDescription,
                description: gptI18n.description,
            };
        } else {
            var fields = {
                description: gptI18n.description,
            };
        }

        for (var name in fields) {
            fieldsList +=
                '<tr>' +
                    '<td>' +
                        '<div class="checkbox">' +
                            '<div class="md-checkbox md-checkbox-inline"><label><input type="checkbox" class="gpt-fields-list ' + _class + '" name="" value="' + name + '"><i class="md-checkbox-control"></i>' + fields[name] + '</label></div>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
        }

        return '<div class="choice-table">' +
                    '<table class="table table-bordered mb-0">' +
                        '<thead>' +
                            '<tr>' +
                                '<th class="checkbox">' +
                                    '<div class="md-checkbox"><label><input type="checkbox" class="js-choice-table-select-all" onchange="$(\'.' + _class + '\').prop(\'checked\', $(this).prop(\'checked\'));"><i class="md-checkbox-control"></i> ' + gptI18n.selectAll + '</label></div>' +
                                '</th>' +
                            '</tr>' +
                        '</thead>' +
                        '<tbody>' +
                            fieldsList +
                        '</tbody>' +
                    '</table>' +
                '</div>';
    };

    return ChatGptContent;
})();
