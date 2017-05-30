/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){CKEDITOR.plugins.add("autosave",{init:function(n){if(n.elementMode==CKEDITOR.ELEMENT_MODE_INLINE&&n.config.enableInlineAutoSave)n.on("blur",function(){var t,i;if(!this.doNotSave){var n=this.editable(),r=n.getCustomData("itemId"),u=n.getCustomData("type"),f=n.getCustomData("context"),e=n.getCustomData("itemType");CKEDITOR.showSaveAlertReloadMessage=!0;t=n.getData();i={itemId:r,type:u,context:f,itemType:e,content:t};this.fire("saveContent",i,this)}},null,null,9)}})})()