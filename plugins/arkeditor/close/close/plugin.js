/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){var t={readOnly:1,exec:function(n){if(n.focusManager.blur(),n.editable().$.blur(),CKEDITOR.env.iOS){n.editable().getParent.appendHtml('<input id="editableFix" style="width:1px;height:1px;border:none;margin:0;padding:0;" tabIndex="-1">');var t=CKEDITOR.getById("editableFix");t.focus();t.$.setSelectionRange(0,0);t.$.blur();t.remove()}CKEDITOR.env.gecko&&(CKEDITOR.document.focused=!0)}},n="close";CKEDITOR.plugins.add(n,{icons:"close",hidpi:!1,init:function(i){if(i.elementMode==CKEDITOR.ELEMENT_MODE_INLINE){var r=i.addCommand(n,t);r.modes={wysiwyg:1};i.ui.addButton&&i.ui.addButton("Close",{label:"Close",command:n})}}})})()