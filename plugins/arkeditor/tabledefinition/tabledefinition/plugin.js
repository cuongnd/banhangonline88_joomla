/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){CKEDITOR.plugins.add("tabledefinition",{init:function(){CKEDITOR.on("dialogDefinition",function(n){var i=n.data.name,r=n.data.definition,t;i=="table"&&(t=r.getContents("info"),t.get("txtWidth")["default"]="100%")})}})})()