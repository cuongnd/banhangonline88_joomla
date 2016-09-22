/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){var t={readOnly:1,exec:function(n){var t=n.editable(),i=n.config.pageBreakURL||t.getCustomData("pageBreakURL"),r=function(n){n.cancel()};n.editable().once("blur",r,null,null,-100);SqueezeBox.open(null,{handler:"iframe",size:{x:500,y:300},url:i})}},n="pagebreak2";CKEDITOR.plugins.add(n,{lang:"en",icons:"pagebreak2",hidpi:!1,init:function(i){if(i.elementMode==CKEDITOR.ELEMENT_MODE_INLINE){var r=i.addCommand(n,t);r.modes={wysiwyg:1};i.ui.addButton&&i.ui.addButton("Pagebreak2",{label:i.lang.pagebreak2.label,command:n})}}})})()