/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.plugins.add("arkabout",{requires:"dialog",lang:"en",icons:"arkabout",hidpi:!0,init:function(n){var t=n.addCommand("arkabout",new CKEDITOR.dialogCommand("arkabout"));t.modes={wysiwyg:1,source:1};t.canUndo=!1;t.readOnly=1;n.ui.addButton&&n.ui.addButton("About",{label:n.lang.arkabout.title,command:"arkabout",toolbar:"about"});CKEDITOR.dialog.add("arkabout",this.path+"dialogs/arkabout.js")}})