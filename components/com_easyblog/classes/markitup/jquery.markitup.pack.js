EasyBlog(function($){$.fn.markItUp=function(settings,extraSettings){var options,ctrlKey,shiftKey,altKey;return ctrlKey=shiftKey=altKey=!1,options={id:"",nameSpace:"",root:"",previewInWindow:"",previewAutoRefresh:!0,previewPosition:"after",previewTemplatePath:"~/templates/preview.html",previewParserPath:"",previewParserVar:"data",resizeHandle:!0,beforeInsert:"",afterInsert:"",onEnter:{},onShiftEnter:{},onCtrlEnter:{},onTab:{},markupSet:[{}]},$.extend(options,settings,extraSettings),options.root||$("script").each(function(a,b){miuScript=$(b).get(0).src.match(/(.*)jquery\.markitup(\.pack)?\.js$/),miuScript!==null&&(options.root=miuScript[1])}),this.each(function(){function localize(a,b){return b?a.replace(/("|')~\//g,"$1"+options.root):a.replace(/^~\//,options.root)}function init(){id="",nameSpace="",options.id?id='id="'+options.id+'"':$$.attr("id")&&(id='id="markItUp'+$$.attr("id").substr(0,1).toUpperCase()+$$.attr("id").substr(1)+'"'),options.nameSpace&&(nameSpace='class="'+options.nameSpace+'"'),$$.wrap("<div "+nameSpace+"></div>"),$$.wrap("<div "+id+' class="markItUp"></div>'),$$.wrap('<div class="markItUpContainer"></div>'),$$.addClass("markItUpEditor"),header=$('<div class="markItUpHeader"></div>').insertBefore($$),$(dropMenus(options.markupSet)).appendTo(header),footer=$('<div class="markItUpFooter"></div>').insertAfter($$),options.resizeHandle===!0&&$.browser.safari!==!0&&(resizeHandle=$('<div class="markItUpResizeHandle"></div>').insertAfter($$).bind("mousedown",function(a){var b=$$.height(),c=a.clientY,d,e;d=function(a){return $$.css("height",Math.max(20,a.clientY+b-c)+"px"),!1},e=function(a){return $("html").unbind("mousemove",d).unbind("mouseup",e),!1},$("html").bind("mousemove",d).bind("mouseup",e)}),footer.append(resizeHandle)),$$.keydown(keyPressed).keyup(keyPressed),$$.bind("insertion",function(a,b){b.target!==!1&&get(),textarea===$.markItUp.focused&&markup(b)}),$$.focus(function(){$.markItUp.focused=this})}function dropMenus(markupSet){var ul=$("<ul></ul>"),i=0;return $("li:hover > ul",ul).css("display","block"),$.each(markupSet,function(){var button=this,t="",title,li,j;title=button.key?(button.name||"")+" [Ctrl+"+button.key+"]":button.name||"",key=button.key?'accesskey="'+button.key+'"':"";if(button.separator)li=$('<li class="markItUpSeparator">'+(button.separator||"")+"</li>").appendTo(ul);else{i++;for(j=levels.length-1;j>=0;j--)t+=levels[j]+"-";li=$('<li class="markItUpButton markItUpButton'+t+i+" "+(button.className||"")+'"><a href="" '+key+' title="'+title+'">'+(button.name||"")+"</a></li>").bind("contextmenu",function(){return!1}).click(function(){return!1}).mousedown(function(){return button.call&&eval(button.call)(),setTimeout(function(){markup(button)},1),!1}).hover(function(){$("> ul",this).show(),$(document).one("click",function(){$("ul ul",header).hide()})},function(){$("> ul",this).hide()}).appendTo(ul),button.dropMenu&&(levels.push(i),$(li).addClass("markItUpDropMenu").append(dropMenus(button.dropMenu)))}}),levels.pop(),ul}function magicMarkups(a){return a?(a=a.toString(),a=a.replace(/\(\!\(([\s\S]*?)\)\!\)/g,function(a,b){var c=b.split("|!|");return altKey===!0?c[1]!==undefined?c[1]:c[0]:c[1]===undefined?"":c[0]}),a=a.replace(/\[\!\[([\s\S]*?)\]\!\]/g,function(a,b){var c=b.split(":!:");return abort===!0?!1:(value=prompt(c[0],c[1]?c[1]:""),value===null&&(abort=!0),value)}),a):""}function prepare(a){return $.isFunction(a)&&(a=a(hash)),magicMarkups(a)}function build(a){return openWith=prepare(clicked.openWith),placeHolder=prepare(clicked.placeHolder),replaceWith=prepare(clicked.replaceWith),closeWith=prepare(clicked.closeWith),replaceWith!==""?block=openWith+replaceWith+closeWith:selection===""&&placeHolder!==""?block=openWith+placeHolder+closeWith:block=openWith+(a||selection)+closeWith,{block:block,openWith:openWith,replaceWith:replaceWith,placeHolder:placeHolder,closeWith:closeWith}}function markup(a){var b,c,d,e;hash=clicked=a,get(),$.extend(hash,{line:"",root:options.root,textarea:textarea,selection:selection||"",caretPosition:caretPosition,ctrlKey:ctrlKey,shiftKey:shiftKey,altKey:altKey}),prepare(options.beforeInsert),prepare(clicked.beforeInsert),ctrlKey===!0&&shiftKey===!0&&prepare(clicked.beforeMultiInsert),$.extend(hash,{line:1});if(ctrlKey===!0&&shiftKey===!0){lines=selection.split(/\r?\n/);for(c=0,d=lines.length,e=0;e<d;e++)$.trim(lines[e])!==""?($.extend(hash,{line:++c,selection:lines[e]}),lines[e]=build(lines[e]).block):lines[e]="";string={block:lines.join("\n")},start=caretPosition,b=string.block.length+($.browser.opera?d:0)}else ctrlKey===!0?(string=build(selection),start=caretPosition+string.openWith.length,b=string.block.length-string.openWith.length-string.closeWith.length,b-=fixIeBug(string.block)):shiftKey===!0?(string=build(selection),start=caretPosition,b=string.block.length,b-=fixIeBug(string.block)):(string=build(selection),start=caretPosition+string.block.length,b=0,start-=fixIeBug(string.block));selection===""&&string.replaceWith===""&&(caretOffset+=fixOperaBug(string.block),start=caretPosition+string.openWith.length,b=string.block.length-string.openWith.length-string.closeWith.length,caretOffset=$$.val().substring(caretPosition,$$.val().length).length,caretOffset-=fixOperaBug($$.val().substring(0,caretPosition))),$.extend(hash,{caretPosition:caretPosition,scrollPosition:scrollPosition}),string.block!==selection&&abort===!1?(insert(string.block),set(start,b)):caretOffset=-1,get(),$.extend(hash,{line:"",selection:selection}),ctrlKey===!0&&shiftKey===!0&&prepare(clicked.afterMultiInsert),prepare(clicked.afterInsert),prepare(options.afterInsert),previewWindow&&options.previewAutoRefresh&&refreshPreview(),shiftKey=altKey=ctrlKey=abort=!1}function fixOperaBug(a){return $.browser.opera?a.length-a.replace(/\n*/g,"").length:0}function fixIeBug(a){return $.browser.msie?a.length-a.replace(/\r*/g,"").length:0}function insert(a){if(document.selection){var b=document.selection.createRange();b.text=a}else $$.val($$.val().substring(0,caretPosition)+a+$$.val().substring(caretPosition+selection.length,$$.val().length))}function set(a,b){if(textarea.createTextRange){if($.browser.opera&&$.browser.version>=9.5&&b==0)return!1;range=textarea.createTextRange(),range.collapse(!0),range.moveStart("character",a),range.moveEnd("character",b),range.select()}else textarea.setSelectionRange&&textarea.setSelectionRange(a,a+b);textarea.scrollTop=scrollPosition,textarea.focus()}function get(){textarea.focus(),scrollPosition=textarea.scrollTop;if(document.selection){selection=document.selection.createRange().text;if($.browser.msie){var a=document.selection.createRange(),b=a.duplicate();b.moveToElementText(textarea),caretPosition=-1;while(b.inRange(a))b.moveStart("character"),caretPosition++}else caretPosition=textarea.selectionStart}else caretPosition=textarea.selectionStart,selection=$$.val().substring(caretPosition,textarea.selectionEnd);return selection}function preview(){!previewWindow||previewWindow.closed?options.previewInWindow?previewWindow=window.open("","preview",options.previewInWindow):(iFrame=$('<iframe class="markItUpPreviewFrame"></iframe>'),options.previewPosition=="after"?iFrame.insertAfter(footer):iFrame.insertBefore(header),previewWindow=iFrame[iFrame.length-1].contentWindow||frame[iFrame.length-1]):altKey===!0&&(iFrame?iFrame.remove():previewWindow.close(),previewWindow=iFrame=!1),options.previewAutoRefresh||refreshPreview()}function refreshPreview(){renderPreview()}function renderPreview(){var a;return options.previewParserPath!==""?$.ajax({type:"POST",url:options.previewParserPath,data:options.previewParserVar+"="+encodeURIComponent($$.val()),success:function(a){writeInPreview(localize(a,1))}}):template||$.ajax({url:options.previewTemplatePath,success:function(a){writeInPreview(localize(a,1).replace(/<!-- content -->/g,$$.val()))}}),!1}function writeInPreview(a){if(previewWindow.document){try{sp=previewWindow.document.documentElement.scrollTop}catch(b){sp=0}previewWindow.document.open(),previewWindow.document.write(a),previewWindow.document.close(),previewWindow.document.documentElement.scrollTop=sp}options.previewInWindow&&previewWindow.focus()}function keyPressed(a){shiftKey=a.shiftKey,altKey=a.altKey,ctrlKey=!a.altKey||!a.ctrlKey?a.ctrlKey:!1;if(a.type==="keydown"){if(ctrlKey===!0){li=$("a[accesskey="+String.fromCharCode(a.keyCode)+"]",header).parent("li");if(li.length!==0)return ctrlKey=!1,setTimeout(function(){li.triggerHandler("mousedown")},1),!1}if(a.keyCode===13||a.keyCode===10)return ctrlKey===!0?(ctrlKey=!1,markup(options.onCtrlEnter),options.onCtrlEnter.keepDefault):shiftKey===!0?(shiftKey=!1,markup(options.onShiftEnter),options.onShiftEnter.keepDefault):(markup(options.onEnter),options.onEnter.keepDefault);if(a.keyCode===9)return shiftKey==1||ctrlKey==1||altKey==1?!1:caretOffset!==-1?(get(),caretOffset=$$.val().length-caretOffset,set(caretOffset,0),caretOffset=-1,!1):(markup(options.onTab),options.onTab.keepDefault)}}var $$,textarea,levels,scrollPosition,caretPosition,caretOffset,clicked,hash,header,footer,previewWindow,template,iFrame,abort;$$=$(this),textarea=this,levels=[],abort=!1,scrollPosition=caretPosition=0,caretOffset=-1,options.previewParserPath=localize(options.previewParserPath),options.previewTemplatePath=localize(options.previewTemplatePath),init()})},$.fn.markItUpRemove=function(){return this.each(function(){var a=$(this).unbind().removeClass("markItUpEditor");a.parent("div").parent("div.markItUp").parent("div").replaceWith(a)})},$.markItUp=function(a){var b={target:!1};$.extend(b,a);if(b.target)return $(b.target).each(function(){$(this).focus(),$(this).trigger("insertion",[b])});$("textarea").trigger("insertion",[b])}});
