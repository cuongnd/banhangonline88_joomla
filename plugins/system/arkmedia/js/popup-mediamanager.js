/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * MediaManager behavior for media component
 *
 * @package		Joomla.Extensions
 * @subpackage	ArkMedia
 * @since		3.2
 */

(function ($) {
    var MediaManager = this.MediaManager = {
        initialize: function () {
            o = this._getUriObject(window.self.location.href);
            q = this._getQueryObject(o.query);
            this.editor = decodeURIComponent(q['e_name']);

            // Setup image manager fields object
            this.fields = new Object();
            this.fields.url = document.getElementById("f_url");
            this.fields.target = document.getElementById("f_target");
            this.fields.rel = document.getElementById("f_rel");
            this.fields.title = document.getElementById("f_title");
            this.fields.text = document.getElementById("f_text");
            this.html = '';

            // Setup image listing objects
            this.folderlist = document.getElementById('folderlist');

            this.frame = window.frames['imageframe'];
            this.frameurl = this.frame.location.href;

            // Setup imave listing frame
            this.imageframe = document.getElementById('imageframe');
            this.imageframe.manager = this;
            $(this.imageframe).on('load', function () { MediaManager.onloadimageview(); });

            // Setup folder up button
            this.upbutton = document.getElementById('upbutton');
            $(this.upbutton).off('click');
            $(this.upbutton).on('click', function () { MediaManager.upFolder(); });
        },

        onloadimageview: function () {
            // Update the frame url
            this.frameurl = this.frame.location.href;

            var folder = this.getImageFolder();
            for (var i = 0; i < this.folderlist.length; i++) {
                if (folder == this.folderlist.options[i].value) {
                    this.folderlist.selectedIndex = i;
                    if (this.folderlist.className.test(/\bchzn-done\b/)) {
                        $(this.folderlist).trigger('liszt:updated');
                    }
                    break;
                }
            }

            a = this._getUriObject($('#uploadForm').attr('action'));
            q = this._getQueryObject(a.query);
            q['folder'] = folder;
            var query = [];
            for (var k in q) {
                var v = q[k];
                if (q.hasOwnProperty(k) && v !== null) {
                    query.push(k + '=' + v);
                }
            }
            a.query = query.join('&');
            var portString = '';
            if (typeof (a.port) !== 'undefined' && a.port != 80) {
                portString = ':' + a.port;
            }
            $('#uploadForm').attr('action', a.scheme + '://' + a.domain + portString + a.path + '?' + a.query);
        },

        getImageFolder: function () {
            var url = this.frame.location.search.substring(1);
            var args = this.parseQuery(url);

            return args['folder'];
        },

        onok: function () {
            var tag = '';
            var extra = '';

            // Get the link tag field information
            var url = this.fields.url.value;
            var target = this.fields.target.value;
            var rel = this.fields.rel.value;
            var title = this.fields.title.value;
            var text = this.fields.text.value;

            if (url != '') {
                // Set alt attribute
                if (target != '') {
                    extra = extra + 'target="' + target + '" ';
                } else {
                    extra = extra + 'target="" ';
                }
                // Set rel attribute
                if (rel != '') {
                    extra = extra + 'rel="' + rel + '" ';
                }
                // Set title attribute
                if (title != '') {
                    extra = extra + 'title="' + title + '" ';
                }

               this.html = /<[a-z\][\s\S]*>/i.test(this.html) ? this.html : '';
               var tag = "<a href=\"" + url + "\" " + extra + "/>" + (this.html || text || url) + "</a>";
            }

            window.parent.jInsertEditorText(tag, this.editor);
            return false;
        },

        setFolder: function (folder, asset, author) {
            for (var i = 0; i < this.folderlist.length; i++) {
                if (folder == this.folderlist.options[i].value) {
                    this.folderlist.selectedIndex = i;
                    if (this.folderlist.className.test(/\bchzn-done\b/)) {
                        $(this.folderlist).trigger('liszt:updated');
                    }
                    break;
                }
            }
            //Joomla have made a change that has broken a string being passed as the asset. So we have reverted to 0 to resolve the issue.
            this.frame.location.href = 'index.php?option=com_media&view=imagesList&tmpl=component&folder=' + folder + '&asset=0&author=' + author;
            //Original
            //this.frame.location.href='index.php?option=com_media&view=imagesList&tmpl=component&folder=' + folder + '&asset=' + asset + '&author=' + author;
        },

        getFolder: function () {
            return this.folderlist.value;
        },

        upFolder: function () {
            var currentFolder = this.getFolder();

            if (currentFolder.length < 2) {
                return false;
            }

            var folders = currentFolder.split('/');
            var search = '';

            for (var i = 0; i < folders.length - 1; i++) {
                search += folders[i];
                search += '/';
            }

            // remove the trailing slash
            search = search.substring(0, search.length - 1);

            for (var i = 0; i < this.folderlist.length; i++) {
                var thisFolder = this.folderlist.options[i].value;

                if (thisFolder == search) {
                    this.folderlist.selectedIndex = i;
                    var newFolder = this.folderlist.options[i].value;
                    this.setFolder(newFolder);
                    break;
                }
            }
        },

        populateFields: function (file) {
            $("#f_url").val(image_base_path + file);
        },

        showMessage: function (text) {
            var message = document.getElementById('message');
            var messages = document.getElementById('messages');

            if (message.firstChild)
                message.removeChild(message.firstChild);

            message.appendChild(document.createTextNode(text));
            messages.style.display = "block";
        },

        parseQuery: function (query) {
            var params = new Object();
            if (!query) {
                return params;
            }
            var pairs = query.split(/[;&]/);
            for (var i = 0; i < pairs.length; i++) {
                var KeyVal = pairs[i].split('=');
                if (!KeyVal || KeyVal.length != 2) {
                    continue;
                }
                var key = unescape(KeyVal[0]);
                var val = unescape(KeyVal[1]).replace(/\+ /g, ' ');
                params[key] = val;
            }
            return params;
        },

        refreshFrame: function () {
            this._setFrameUrl();
        },

        _setFrameUrl: function (url) {
            if (url != null) {
                this.frameurl = url;
            }
            this.frame.location.href = this.frameurl;
        },

        _getQueryObject: function (q) {
            var vars = q.split(/[&;]/);
            var rs = {};
            if (vars.length) vars.forEach(function (val) {
                var keys = val.split('=');
                if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
            });
            return rs;
        },

        _getUriObject: function (u) {
            var bitsAssociate = {}, bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
            ['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'].forEach(function (key, index) {
                bitsAssociate[key] = bits[index];
            });

            return (bits)
			? bitsAssociate
			: null;
        }
    };
})(jQuery);

jQuery(function(){
	MediaManager.initialize();
});