"use strict";
/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.plugins.add("dndhandler",{init:function(n){if(typeof n.plugins.uploadwidget=="undefined")n.on("paste",function(n){var t=n.data.target;return n.data.dataValue&&!n.data.dataTransfer.getFilesCount()?!0:n.data.method=="drop"?(this.showNotification("Drag and Drop upload is not available in this version","warning",5e3),n.stop(),this.fire("focus"),!1):void 0},null,null,5)}})