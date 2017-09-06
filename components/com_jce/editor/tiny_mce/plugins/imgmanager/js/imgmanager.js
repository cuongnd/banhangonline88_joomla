/* JCE Editor - 2.5.30 | 17 October 2016 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2016 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
(function(){var ImageManagerDialog={settings:{},init:function(){var ed=tinyMCEPopup.editor,n=ed.selection.getNode(),self=this,br,el;$('#insert').click(function(e){self.insert();e.preventDefault();});tinyMCEPopup.resizeToInnerSize();tinyMCEPopup.restoreSelection();var src=decodeURIComponent(ed.dom.getAttrib(n,'src'));src=ed.convertURL(src);TinyMCE_Utils.fillClassList('classlist');$.each(this.settings.attributes,function(k,v){if(!parseFloat(v)){$('#attributes-'+k).hide();}});$('#onmouseover, #onmouseout').focus(function(){$('#onmouseover, #onmouseout').removeClass('focus');$(this).addClass('focus');});$('#width, #height').change(function(){var a=this,b=(this.id==="width")?'#height':'#width';$(this).addClass('edited');self.setDimensions(a,b,'#constrain');});$('#margin_top, #margin_right, #margin_bottom, #margin_left').change(function(){self.setMargins();});$('#margin_check').click(function(){self.setMargins();});$('#align, #clear, #border_width, #border_styles, #border_color, #dir').change(function(){self.updateStyles();});$('#border').click(function(){self.setBorder();});$('#style').change(function(){self.setStyles();});$('#classlist').change(function(){self.setClasses(this.value);});$.Plugin.init({selectChange:function(){ImageManagerDialog.updateStyles();}});if(n&&n.nodeName=='IMG'){$('#insert').button('option','label',tinyMCEPopup.getLang('update','Update',true));$('#src').val(src);$('#sample').attr({'src':n.src}).attr($.Plugin.sizeToFit(n,{width:80,height:60}));var w=this.getAttrib(n,'width'),h=this.getAttrib(n,'height');if(w||h){$('#width, #height').addClass('edited');}else{w=n.width,h=n.height;}
$('#width').val(w).data('tmp',w);$('#height').val(h).data('tmp',h);$('#constrain').prop('checked',w&&h);$('#alt').val(ed.dom.getAttrib(n,'alt'));$('#title').val(ed.dom.getAttrib(n,'title'));$.each(['top','right','bottom','left'],function(){$('#margin_'+this).val(ImageManagerDialog.getAttrib(n,'margin-'+this));});$('#border_width').val(function(){var v=self.getAttrib(n,'border-width');if($('option[value="'+v+'"]',this).length==0){$(this).append(new Option(v,v));}
return v;});$('#border_style').val(this.getAttrib(n,'border-style'));$('#border_color').val(this.getAttrib(n,'border-color')).change();if(!$('#border').is(':checked')){$.each(['border_width','border_style','border_color'],function(i,k){$('#'+k).val(self.settings.defaults[k]).change();});}
$('#align').val(this.getAttrib(n,'align'));$('#classes').val(ed.dom.getAttrib(n,'class'));$('#classlist').val(ed.dom.getAttrib(n,'class'));$('#style').val(ed.dom.getAttrib(n,'style'));$('#id').val(ed.dom.getAttrib(n,'id'));$('#dir').val(ed.dom.getAttrib(n,'dir'));$('#lang').val(ed.dom.getAttrib(n,'lang'));$('#usemap').val(ed.dom.getAttrib(n,'usemap'));$('#insert').button('option','label',ed.getLang('update','Update'));$('#longdesc').val(ed.convertURL(ed.dom.getAttrib(n,'longdesc')));$('#onmouseout').val(src);$.each(['onmouseover','onmouseout'],function(){v=ed.dom.getAttrib(n,this);v=$.trim(v);v=v.replace(/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/,'$1');v=ed.convertURL(v);$('#'+this).val(v);});br=n.nextSibling;if(br&&br.nodeName=='BR'&&br.style.clear){$('#clear').val(br.style.clear);}}else{$.Plugin.setDefaults(this.settings.defaults);}
WFFileBrowser.init($('#src'),{onFileClick:function(e,file){ImageManagerDialog.selectFile(file);},onFileInsert:function(e,file){ImageManagerDialog.selectFile(file);}});this.setBorder();this.setMargins(true);this.updateStyles();if(ed.settings.schema=='html5'&&ed.settings.validate){$('#longdesc').parent().parent().hide();}},insert:function(){var ed=tinyMCEPopup.editor,t=this;AutoValidator.validate(document);if($('#src').val()===''){$.Dialog.alert(tinyMCEPopup.getLang('imgmanager_dlg.no_src','Please enter a url for the image'));return false;}
if($('#alt').val()===''){$.Dialog.confirm(tinyMCEPopup.getLang('imgmanager_dlg.missing_alt'),function(state){if(state){t.insertAndClose();}},{width:300,height:200});}else{this.insertAndClose();}},insertAndClose:function(){var ed=tinyMCEPopup.editor,self=this,v,args={},el,br='';this.updateStyles();tinyMCEPopup.restoreSelection();if(tinymce.isWebKit){ed.getWin().focus();}
args={vspace:'',hspace:'',border:'',align:''};tinymce.each(['src','width','height','alt','title','classes','style','id','dir','lang','usemap','longdesc'],function(k){v=$('#'+k+':enabled').val();if(k=='src'){v=$.String.buildURI(v);}
if(k=='width'||k=='height'){if(self.settings.always_include_dimensions){v=$('#'+k).val();}else{v=$('#'+k+'.edited').val()||'';}}
if(k=='classes')
k='class';args[k]=v;});args.onmouseover=args.onmouseout='';var over=$('#onmouseover').val(),out=$('#onmouseout').val();if(over&&out){args.onmouseover="this.src='"+ed.convertURL(over)+"';";args.onmouseout="this.src='"+ed.convertURL(out)+"';";}
el=ed.selection.getNode();br=el.nextSibling;if(el&&el.nodeName=='IMG'){ed.dom.setAttribs(el,args);if(br&&br.nodeName=='BR'){if($('#clear').is(':disabled')||$('#clear').val()===''){ed.dom.remove(br);}
if(!$('#clear').is(':disabled')&&$('#clear').val()!==''){ed.dom.setStyle(br,'clear',$('#clear').val());}}else{if(!$('#clear').is(':disabled')&&$('#clear').val()!==''){br=ed.dom.create('br');ed.dom.setStyle(br,'clear',$('#clear').val());ed.dom.insertAfter(br,el);}}}else{ed.execCommand('mceInsertContent',false,'<img id="__mce_tmp" src="" />',{skip_undo:1});el=ed.dom.get('__mce_tmp');if(!$('#clear').is(':disabled')&&$('#clear').val()!==''){br=ed.dom.create('br');ed.dom.setStyle(br,'clear',$('#clear').val());ed.dom.insertAfter(br,el);}
ed.dom.setAttribs('__mce_tmp',args);ed.dom.setAttrib('__mce_tmp','id','');ed.undoManager.add();}
tinyMCEPopup.close();},getAttrib:function(e,at){var ed=tinyMCEPopup.editor,v,v2;switch(at){case'width':case'height':return ed.dom.getAttrib(e,at)||ed.dom.getStyle(e,at)||'';break;case'align':if(v=ed.dom.getAttrib(e,'align')){return v;}
if(v=ed.dom.getStyle(e,'float')){return v;}
if(v=ed.dom.getStyle(e,'vertical-align')){return v;}
if(e.style.display==="block"&&ed.dom.getStyle(e,'margin-left')==="auto"&&ed.dom.getStyle(e,'margin-right')==="auto"){return'center';}
break;case'margin-top':case'margin-bottom':if(v=ed.dom.getStyle(e,at)){if(/auto|inherit/.test(v)){return v;}
return parseInt(v.replace(/[^-0-9]/g,''));}
if(v=ed.dom.getAttrib(e,'vspace')){return parseInt(v.replace(/[^-0-9]/g,''));}
break;case'margin-left':case'margin-right':if(v=ed.dom.getStyle(e,at)){if(/auto|inherit/.test(v)){return v;}
return parseInt(v.replace(/[^-0-9]/g,''));}
if(v=ed.dom.getAttrib(e,'hspace')){return parseInt(v.replace(/[^-0-9]/g,''));}
break;case'border-width':case'border-style':case'border-color':v='';tinymce.each(['top','right','bottom','left'],function(n){s=at.replace(/-/,'-'+n+'-');sv=ed.dom.getStyle(e,s);if(sv!==''||(sv!=v&&v!=='')){v='';}
if(sv){v=sv;}});if(v!==''){$('#border').prop('checked',true);}
if((at=='border-width'||at=='border-style')&&v===''){v='inherit';}
if(at=='border-color'){v=$.String.toHex(v);}
if(at=='border-width'){if(/[0-9][a-z]/.test(v)){v=parseFloat(v);}}
return v;break;}},setMargins:function(e){var x=0,s=false;var v=$('#margin_top').val();var $elms=$('#margin_right, #margin_bottom, #margin_left');if(e){$elms.each(function(){if($(this).val()===v){x++;}});s=(x==$elms.length);$elms.prop('disabled',s).prev('label').toggleClass('disabled',s);$('#margin_check').prop('checked',s).prop('disabled',false).prev('label').removeClass('disabled');}else{s=$('#margin_check').is(':checked');$elms.each(function(){if(s){$(this).val(v);}
$(this).prop('disabled',s).prev('label').toggleClass('disabled',s);});$('#margin_top').val(v);this.updateStyles();}},setBorder:function(){var s=$('#border').is(':checked');$('#border~:input, #border~span, #border~label').attr('disabled',!s).toggleClass('disabled',!s);$('#border_color').change();this.updateStyles();},setClasses:function(v){return $.Plugin.setClasses(v);},setDimensions:function(a,b,c){var w=$(a).val(),h=$(b).val();if(w&&h){if($(c).is(':checked, .checked')){var tw=$(a).data('tmp');if(tw){var temp=((h/tw)*w).toFixed(0);$(b).val(temp).data('tmp',temp).addClass('edited');}}}
$(a).data('tmp',w);},setStyles:function(){var self=this,ed=tinyMCEPopup,$img=$('#sample');$img.attr('style',$('#style').val());$.each(['top','right','bottom','left'],function(i,k){var v=ed.dom.getStyle($img.get(0),'margin-'+k);if(v.indexOf('px')!=-1){v=parseInt(v);}
$('#margin_'+k).val(v);});this.setMargins(true);var border=false;$.each(['width','color','style'],function(i,k){var v=ed.dom.getStyle($img.get(0),'border-'+k);if(v==''){$.each(['top','right','bottom','left'],function(i,n){var sv=ed.dom.getStyle($img.get(0),'border-'+n+'-'+k);if(sv!==''||(sv!=v&&v!=='')){v='';}
if(sv){v=sv;}});}
if(v!==''){border=true;}
if(k=='width'){v=/[0-9][a-z]/.test(v)?parseInt(v):v;}
if(k=='color'){v=$.String.toHex(v);}
if(border){$('#border').attr('checked','checked');$('#border_'+k).val(v);$('#border~:input, #border~span, #border~label').attr('disabled',false).toggleClass('disabled',false);if(k=='color'){$('#border_'+k).trigger('pick');}}});$('#align').val(function(){var v=$img.css("float")||$img.css("vertical-align");if(v){return v;}
if($img.css('margin-left')==="auto"&&$img.css('margin-right')==="auto"&&$img.css('display')==="block"){return"center";}
return"";});},updateStyles:function(){var ed=tinyMCEPopup,st,v,br,img=$('#sample'),k;$(img).attr('style',$('#style').val());$(img).attr('dir',$('#dir').val());$(img).css('float','');v=$('#align').val();if(v=='center'){$(img).css({'display':'block','margin-left':'auto','margin-right':'auto'});$('#clear').attr('disabled',true);$('#margin_left, #margin_right').val('auto');}else{if(/(top|middle|bottom)/.test(v)){$(img).css("vertical-align",v);}
$(img).css('float',v).css('display',function(){if(this.style.display==="block"&&this.style.marginLeft==="auto"&&this.style.marginRight==="auto"){return"";}
return this.style.display;});$('#margin_left, #margin_right').val(function(){if(this.value==="auto"){return"";}
return this.value;});if($('#margin_check').is(':checked')){$('#margin_top').siblings('input[type="text"]').val($('#margin_top').val());}
$('#clear').attr('disabled',!v);}
v=$('#clear:enabled').val();if(v){if(!$('#sample-br').get(0)){$(img).after('<br id="sample-br" />');}
$('#sample-br').css('clear',v);}else{$('#sample-br').remove();}
$.each(['width','color','style'],function(i,k){if($('#border').is(':checked')){v=$('#border_'+k).val();}else{v='';}
if(v=='inherit'){v='';}
if(k=='width'&&/[^a-z]/i.test(v)){v+='px';}
$(img).css('border-'+k,v);});$.each(['top','right','bottom','left'],function(i,k){v=$('#margin_'+k).val();$(img).css('margin-'+k,/[^a-z]/i.test(v)?v+'px':v);});var styles=ed.dom.parseStyle($(img).attr('style'));function compressBorder(n){var s=[];$.each(n,function(i,k){k='border-'+k,v=styles[k];if(v=='none'){delete styles[k];return;}
if(v){s.push(styles[k]);delete styles[k];}});if(s.length){styles.border=s.join(' ');}}
compressBorder(['width','style','color','image']);for(k in styles){if(k.indexOf('-moz-')>=0||k.indexOf('-webkit-')>=0){delete styles[k];}}
$('#style').val(ed.dom.serializeStyle(styles));},_setRollover:function(src){$('input.focus','#rollover_tab').val(src);},selectFile:function(file){var self=this;var name=$(file).attr('title');var src=$(file).data('url');if(!$('#rollover_tab').is('.ui-tabs-hide')){this._setRollover(src);}else{name=$.String.stripExt(name);name=name.replace(/[-_]+/g,' ');$('#alt').val(name);$('#onmouseout').val(src);$('#src').val(src);if(!$(file).data('width')||!$(file).data('height')){var img=new Image();img.onload=function(){$.each(['width','height'],function(i,k){$('#'+k).val(img[k]).data('tmp',img[k]).removeClass('edited');});};img.src=src;}else{$.each(['width','height'],function(i,k){$('#'+k).val($(file).data(k)).data('tmp',$(file).data(k)).removeClass('edited');});}
$('#sample').attr({'src':$(file).data('preview')}).attr($.Plugin.sizeToFit({width:$(file).data('width'),height:$(file).data('height')},{width:80,height:60}));}}};window.ImageManagerDialog=ImageManagerDialog;tinyMCEPopup.onInit.add(ImageManagerDialog.init,ImageManagerDialog);})();