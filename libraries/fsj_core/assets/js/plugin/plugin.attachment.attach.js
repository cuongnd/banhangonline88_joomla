/**
* Class that creates upload widget with drag-and-drop and file list
* @inherits qq.FileUploaderBasic
*/
qq.FileUploaderAttach = function (o) {
    // call parent constructor
    qq.FileUploaderBasic.apply(this, arguments);

    // additional options    
    qq.extend(this._options, {
        element: null,
        // if set, will be used instead of qq-upload-list in template
        listElement: null,

        classes: {
            // used to get elements from templates
            button: 'upload-button',
            drop: 'upload-drop',
            dropActive: 'upload-drop-active',
            list: 'list',
            /*list: 'fsj_file_upload_items',*/

            file: 'filename',
            spinner: 'spinner',
            size: 'filesize',
            cancel: 'cancel',

            // added to list item when upload completes
            // used in css to hide progress spinner
            success: 'success',
            fail: 'fail',
            progress: 'progress',
            progressinner: 'progress-inner',
            remove: 'remove',
            form: 'form'
        }
    });
    // overwrite options with user supplied    
    qq.extend(this._options, o);

    this._element = this._options.element;
    this._element.innerHTML = this._options.template;
    this._listElement = this._options.listElement || this._find(this._element, 'list');

    this._classes = this._options.classes;

    this._button = this._createUploadButton(this._find(this._element, 'button'));

    this._bindCancelEvent();
    this._setupDragDrop();

    mymyself = this;
};

// inherit from Basic Uploader
qq.extend(qq.FileUploaderAttach.prototype, qq.FileUploaderBasic.prototype);

qq.extend(qq.FileUploaderAttach.prototype, {
    /**
    * Gets one of the elements listed in this._options.classes
    **/
    successCount: 0,

    _find: function (parent, type) {
        var element = qq.getByClass(parent, this._options.classes[type])[0];
        if (!element) {
            throw new Error('element not found ' + type);
        }

        return element;
    },

    _setupDragDrop: function () {
        /*var myself = this,
        dropArea = this._find(this._element, 'drop');

        var dz = new qq.UploadDropZone({
            element: dropArea,
            onEnter: function (e) {
                qq.addClass(dropArea, myself._classes.dropActive);
                e.stopPropagation();
            },
            onLeave: function (e) {
                e.stopPropagation();
            },
            onLeaveNotDescendants: function (e) {
                qq.removeClass(dropArea, myself._classes.dropActive);
            },
            onDrop: function (e) {
                dropArea.style.display = 'none';
                qq.removeClass(dropArea, myself._classes.dropActive);
                myself._uploadFileList(e.dataTransfer.files);
            }
        });

        dropArea.style.display = 'none';

        qq.attach(document, 'dragenter', function (e) {
            if (!dz._isValidFileDrag(e)) return;

            dropArea.style.display = 'block';
        });
        qq.attach(document, 'dragleave', function (e) {
            if (!dz._isValidFileDrag(e)) return;

            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // only fire when leaving document out
            if (!relatedTarget || relatedTarget.nodeName == "HTML") {
                dropArea.style.display = 'none';
            }
        });*/
    },
    _onSubmit: function (id, fileName) {
        qq.FileUploaderBasic.prototype._onSubmit.apply(this, arguments);
        this._addToList(id, fileName);
    },
    _onProgress: function (id, fileName, loaded, total) {
        qq.FileUploaderBasic.prototype._onProgress.apply(this, arguments);

        var item = this._getItemByFileId(id);
        var size = this._find(item, 'size');
        size.style.display = 'inline';

        var pbar = this._find(item, 'progress');

        var text;
        if (loaded != total) {
            text = Math.round(loaded / total * 100) + '% from ' + this._formatSize(total);

            var width = jQuery(pbar).innerWidth();
            var pbarinner = pbar.childNodes[0];
            var innerwidth = loaded / total * width;
            jQuery(pbarinner).css('width', innerwidth + 'px');
            jQuery(pbar).css('display', 'block');
        } else {
            text = this._formatSize(total);
        }

        qq.setText(size, text);
    },
    _onComplete: function (id, fileName, result) {
        //qq.FileUploaderBasic.prototype._onComplete.apply(this, arguments);
        this._filesInProgress--;
        var item = this._getItemByFileId(id);

        if (result.error) {
            var fail = this._find(item, 'fail');
            var text = this._options.failText + result.error;
            qq.setText(fail, text);
            //this._options.showMessage(result.error);
        }
        var val = "&upload=" + jQuery(item).attr('id').split('_')[2];
        jQuery('#fsj_attachment_values').val(jQuery('#fsj_attachment_values').val() + val);

        // mark completed
        qq.remove(this._find(item, 'cancel'));
        //qq.remove(this._find(item, 'spinner'));

        var pbar = this._find(item, 'progress');
        jQuery(pbar).css('display', 'none');

        var removeicon = this._find(item, 'remove');
        jQuery(removeicon).css('display', 'block');
        if (result.success) {
            qq.addClass(item, this._classes.success);
            var params = "";
            for (param in result) {
                if (param == "success") continue;
                if (param == "plugin") continue;
                var value = result[param];
                params += param + '=' + value + '\n';
            }
            var form = this._find(item, 'form');
            jQuery(form).find('textarea').val(params);
            this.successCount++;

            jQuery('#max_file_id').val(id + 1);
        } else {
            qq.addClass(item, this._classes.fail);
            qq.remove(this._find(item, 'form'));
        }

        //this.enableSubmit();
        fsj_attachment_remove_events();
    },
    _addToList: function (id, fileName) {
        var tmpl = this._options.fileTemplate;
        tmpl = tmpl.replace("{filename}", fileName);
        var fieldid = Math.floor(Math.random() * 10000000);
        tmpl = tmpl.replace("{id}", fieldid);
        tmpl = tmpl.replace("{id}", fieldid);
        tmpl = tmpl.replace("{id}", fieldid);
        tmpl = tmpl.replace("{id}", fieldid);
        tmpl = tmpl.replace("{id}", fieldid);
        var item = qq.toElement(tmpl);
        item.qqFileId = id;

        var fileElement = this._find(item, 'file');
        qq.appendText(fileElement, "<span>" + this._formatFileName(fileName) + "</span>");
        this._find(item, 'size').style.display = 'none';

        this._listElement.appendChild(item);

        fsj_attachment_SetSort();
        //this.resizePopup();
    },
    _getItemByFileId: function (id) {
        var item = this._listElement.firstChild;

        // there can't be txt nodes in dynamically created list
        // and we can  use nextSibling
        while (item) {
            if (item.qqFileId == id) return item;
            item = item.nextSibling;
        }
    },
    /**
    * delegate click event for cancel link 
    **/
    _bindCancelEvent: function () {
        var myself = this,
            list = this._listElement;

        qq.attach(list, 'click', function (e) {
            e = e || window.event;
            var target = e.target || e.srcElement;

            target = target.parentNode;
            if (qq.hasClass(target, myself._classes.cancel)) {
                qq.preventDefault(e);

                var item = target.parentNode;
                myself._handler.cancel(item.qqFileId);
                qq.remove(item);

                //myself.enableSubmit();

                myself.removeFromList(item);
            }
            if (qq.hasClass(target, myself._classes.remove)) {
                qq.preventDefault(e);

                myself.successCount--;
                var item = target.parentNode;
                qq.remove(item);

                //myself.enableSubmit();

                myself.removeFromList(item);
            }
        });
    },

    removeFromList: function (item) {

        var value = jQuery('#fsj_attachment_values').val();
        var parts = value.split('&');
        var find = "upload=" + jQuery(item).attr('id').split('_')[1]; ;
        value = "";
        for (var i = 0; i < parts.length; i++) {
            if (parts[i] == find)
                continue;

            value += parts[i] + "&";
        }
        if (value.length > 0)
            value = value.substr(0, value.length - 1);
        jQuery('#fsj_attachment_values').val(value);
    }
});

