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

var ChatGptFilesUploader = (function () {
    function ChatGptFilesUploader (options) {
        var settings = Object.assign({}, {
                wraper: null,
            }, options);
        var files = {};

        this.getSettings = function () {
            return Object.assign({}, settings);
        }

        this.getFiles = function () {
            return files;
        }

        this.addFile = function (file, id) {
            files[id] = file;
            return this;
        }

        this.deleteFile = function (id) {
            if (typeof files[id] != 'undefined') {
                delete files[id];
            }
            return this;
        }
    }

    ChatGptFilesUploader.prototype.openDialog = function () {
        var settings = this.getSettings();
        settings.wraper.find('input[type="file"]').trigger('click');
    }

    ChatGptFilesUploader.prototype.init = function() {
        var settings = this.getSettings();
        var self = this;
        settings.wraper.find('.btn-dialog').on('click', function (e) {
            e.preventDefault();
            self.openDialog();
        });

        function handleFile (file) {
            console.log(file.type);
            var fileTypes = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
            if (fileTypes.indexOf(file.type) == -1) {
                window.showErrorMessage(`File "${file.name}" is not valid`);
                return;
            }
            var reader = new FileReader();
            reader.onload = function() {
                var fileIndex = 'f' + (new Date()).getTime();
                var obj = $('<div><img src=""/><span class="remove" data-id="' + fileIndex + '">&times;</span></div>');
                settings.wraper.find('.images-list').append(obj);
                self.addFile(file, fileIndex);
                obj.find('img').attr('src', reader.result);

                obj.find('.remove').on('click', function () {
                    $(this).closest('div').remove();
                    var id = $(this).data('id');
                    self.deleteFile(id);
                });
            };
            reader.readAsDataURL(file);
        };

        settings.wraper.find('input[type="file"]').on('change', function () {
            const files = this.files;
            $(this).attr('type', 'text').attr('type', 'file');

            for (var i = 0; i < files.length; i ++) {
                // images-list
                handleFile(files[i]);
            }
        });

        function dropHandler(ev) {
            console.log("File(s) dropped");

            // Prevent default behavior (Prevent file from being opened)
            // ev.preventDefault();

            if (ev.dataTransfer.items) {
                // Use DataTransferItemList interface to access the file(s)
                [...ev.dataTransfer.items].forEach(function (item, i) {
                // If dropped items aren't files, reject them
                    if (item.kind === "file") {
                        const file = item.getAsFile();
                        console.log(`… file[${i}].name = ${file.name}`);
                        handleFile(file);
                    }
                });
            } else {
                // Use DataTransfer interface to access the file(s)
                [...ev.dataTransfer.files].forEach(function (file, i) {
                    console.log(`… file[${i}].name = ${file.name}`);
                    handleFile(file);
                });
            }
        }

        /* events fired on the drop targets */
        var dragWraper = settings.wraper.find('.drag-zone').get(0);
        dragWraper.addEventListener('dragover', function (event) {
            event.preventDefault();
            dragWraper.classList.add('drag-enter');
        }, false);

        dragWraper.addEventListener('dragenter', function (event) {
            dragWraper.classList.add('drag-enter');
        });

        settings.wraper.find('.drag-layer').get(0).addEventListener('dragleave', function (event) {
            setTimeout(function () {dragWraper.classList.remove('drag-enter');}, 100);
        });

        dragWraper.addEventListener('drop', function (event) {
            event.preventDefault();
            dragWraper.classList.remove('drag-enter');
            dropHandler(event);
        });
    };

    ChatGptFilesUploader.prototype.upload = async function(onSuccess, onError) {
        var formdata = new FormData;
        var files = this.getFiles();
        
        var hasFiles = false;
        for (var id in files) {
            var ext = (/[.]/.exec(files[id].name)) ? /[^.]+$/.exec(files[id].name) : undefined;
            formdata.append('file[' + id + ']', files[id], (!!ext ? id + '.' + ext : files[id].name));
            formdata.append('files_map[]', id);
            hasFiles = true;
        }
        if (hasFiles == false) {
            return {
                success: true,
                files: [],
            };
        }

        formdata.append('action', 'saveFiles');
        formdata.append('ajax', 1);

        var response = await request({
            type: 'post',
            body: formdata
        });

        return response;
    };

    ChatGptFilesUploader.prototype.deleteFiles = async function(files) {
        var formdata = new FormData;
        formdata.append('action', 'deleteFiles');
        formdata.append('ajax', 1);
        for (var i = 0; i < files.length; i++) {
            formdata.append('file[]', files[i].save_path);
        }

        var response = await request({
            type: 'post',
            body: formdata
        });

        return response;
    };

    async function request(options, onSuccess, onError) {
        function doRequest (options) {
            return new Promise(function (resolve, reject) {
                    // $.ajax(Object.assign({}, options, {
                    //     success: function (data) {
                    //         resolve(data);
                    //     },
                    //     error: function (jqXHR, textStatus, errorThrown) {
                    //         reject(errorThrown);
                    //     }
                    // }));
                    const xhr = new XMLHttpRequest();

                    xhr.open(options.type, options.url);

                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json, text/javascript, */*');
                    
                    if ((options.type == 'post' || options.type == 'POST') && options.body) {
                        xhr.send(options.body);
                    }
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                var response = JSON.parse(xhr.response);
                                resolve(response);
                                return;
                            }

                            reject(xhr.response);
                        }
                    };
                });
        }

        options = Object.assign({}, {
                type: 'POST',
                url: gptFilesAjaxUrl,
            }, options);

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

    return ChatGptFilesUploader;
})();