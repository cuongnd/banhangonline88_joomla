/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){"use strict";CKEDITOR.plugins.add("stylesoverride",{init:function(){},afterInit:function(){CKEDITOR.style.prototype.buildPreview=function(n){var i=this._.definition,t=[],r=i.element,u,f,e;if(r=="bdo"&&(r="span"),t=["<",r],u=i.attributes,u)for(f in u)t.push(" ",f,'="',u[f],'"');return e=CKEDITOR.style.getStyleText(i),e?t.push(' style="',e,'"'):t.push(' style="position:static;float:left;"'),t.push(">",n||i.name,"<\/",r,">"),t.join("")}}})})()