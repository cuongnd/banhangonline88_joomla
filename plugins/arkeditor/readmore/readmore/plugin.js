/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){var t={readOnly:1,exec:function(n){var t=n.getData();if(t.match(/<hr\s+id=("|')system-readmore("|')\s*\/*>/i))return alert("There is already a Read more... link that has been inserted. Only one such link is permitted. Use {pagebreak} to split the page up further."),!1;n.insertHtml('<hr id="system-readmore" />')}},n="readmore";CKEDITOR.plugins.add(n,{lang:"en",icons:"readmore",hidpi:!1,init:function(i){var r=i.addCommand(n,t);r.modes={wysiwyg:1};i.ui.addButton&&i.ui.addButton("Readmore",{label:i.lang.readmore.label,command:n})}})})();CKEDITOR.addCss("hr#system-readmore { border: 1px dashed #FF0000; color: #FF0000;}")