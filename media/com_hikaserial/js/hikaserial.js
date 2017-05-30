/**
 * @package    HikaSerials for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
(function() {
	function preventDefault() { this.returnValue = false; }
	function stopPropagation() { this.cancelBubble = true; }

	var Oby = {
		version: 20120930,
		ajaxEvents : {},

		hasClass : function(o,n) {
			if(o.className == '' ) return false;
			var reg = new RegExp("(^|\\s+)"+n+"(\\s+|$)");
			return reg.test(o.className);
		},
		addClass : function(o,n) {
			if( !this.hasClass(o,n) ) {
				if( o.className == '' ) {
					o.className = n;
				} else {
					o.className += ' '+n;
				}
			}
		},
		trim : function(s) {
			return (s ? '' + s : '').replace(/^\s*|\s*$/g, '');
		},
		removeClass : function(e, c) {
			var t = this;
			if( t.hasClass(e,c) ) {
				var cn = ' ' + e.className + ' ';
				e.className = t.trim(cn.replace(' '+c+' ',''));
			}
		},
		addEvent : function(d,e,f) {
			if( d.attachEvent )
				d.attachEvent('on' + e, f);
			else if (d.addEventListener)
				d.addEventListener(e, f, false);
			else
				d['on' + e] = f;
			return f;
		},
		removeEvent : function(d,e,f) {
			try {
				if( d.detachEvent )
					d.detachEvent('on' + e, f);
				else if( d.removeEventListener)
					d.removeEventListener(e, f, false);
				else
					d['on' + e] = null;
			} catch(e) {}
		},
		cancelEvent : function(e) {
			if( !e )
				return false;
			if(e.stopPropagation)
				e.stopPropagation();
			else
				 e.cancelBubble = true;
			if( e.preventDefault )
				e.preventDefault();
			else
				e.returnValue = false;
			return false;
		},
		evalJSON : function(text, secure) {
			if( typeof(text) != "string" || !text.length) return null;
			if( secure && !(/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(text.replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, ''))) return null;
			return eval('(' + text + ')');
		},
		getXHR : function() {
			var xhr = null, w = window;
			if (w.XMLHttpRequest || w.ActiveXObject) {
				if (w.ActiveXObject) {
					try {
						xhr = new ActiveXObject("Microsoft.XMLHTTP");
					} catch(e) {}
				} else
					xhr = new w.XMLHttpRequest();
			}
			return xhr;
		},
		xRequest: function(url, options, cb, cbError) {
			var t = this, xhr = t.getXHR();
			if(!options) options = {};
			if(!cb) cb = function(){};
			options.mode = options.mode || 'GET';
			options.update = options.update || false;
			xhr.onreadystatechange = function() {
				if( xhr.readyState == 4 ) {
					if( xhr.status == 200 || (xhr.status == 0 && xhr.responseText > 0) || !cbError ) {
						cb(xhr,options.params);
						if(options.update) {
							t.updateElem(options.update, xhr.responseText);
						}
					} else {
						cbError(xhr,options.params);
					}
				}
			};
			xhr.open(options.mode, url, true);
			if( options.mode.toUpperCase() == 'POST' ) {
				xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			}
			xhr.send( options.data );
		},
		getFormData : function(target) {
			var d = document, ret = '';
			if( typeof(target) == 'string' )
				target = d.getElementById(target);
			if( target === undefined )
				target = d;
			var typelist = ['input','select','textarea'];
			for(var t in typelist ) {
				t = typelist[t];
				var inputs = target.getElementsByTagName(t);
				for(var i = inputs.length - 1; i >= 0; i--) {
					if( inputs[i].name && !inputs[i].disabled ) {
						var evalue = inputs[i].value, etype = '';
						if( t == 'input' )
							etype = inputs[i].type.toLowerCase();
						if( etype == 'radio' && !inputs[i].checked )
							evalue = null;
						if( (etype != 'file' && etype != 'submit') && evalue != null ) {
							if( ret != '' ) ret += '&';
							ret += encodeURI(inputs[i].name) + '=' + encodeURIComponent(evalue);
						}
					}
				}
			}
			return ret;
		},
		updateElem : function(elem, data) {
			var d = document;
			if( typeof(elem) == 'string' )
				elem = d.getElementById(elem);

			var scripts = '';
			var text = data.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(all, code){
				scripts += code + '\n';
				return '';
			});
			elem.innerHTML = text;

			if( scripts != '' ) {
				var script = d.createElement('script');
				script.setAttribute('type', 'text/javascript');
				script.text = scripts;
				d.head.appendChild(script);
				d.head.removeChild(script);
			}
		}
	};

	var hikaserial = {
		submitFct: null,
		submitBox: function(data) {
			var t = this;
			if( t.submitFct ) {
				try {
					t.submitFct(data);
				} catch(err) {}
			}
			t.closeBox();
		},
		deleteId: function(id) { return window.hikashop.deleteId(id); },
		dup: function(tplName, htmlblocks, id, extraData, appendTo) { return window.hikashop.dup(tplName, htmlblocks, id, extraData, appendTo); },
		deleteRow: function(id) { return window.hikashop.deleteRow(id); },
		dupRow: function(tplName, htmlblocks, id, extraData) { return window.hikashop.dupRow(tplName, htmlblocks, id, extraData); },
		cleanTableRows: function(id) { return window.hikashop.cleanTableRows(id); },
		checkRow: function(id) { return window.hikashop.checkRow(id); },
		isChecked: function(id,cancel) { return window.hikashop.isChecked(id,cancel); },
		checkAll: function(checkbox, stub) { return window.hikashop.checkAll(checkbox, stub); },
		submitform: function(task, form, extra) { return window.hikashop.submitform(task, form, extra); },
		get: function(elem, target) { return window.hikashop.get(elem, target); },
		form: function(elem, target) { return window.hikashop.form(elem, target); },
		openBox: function(elem, url, jqmodal) { return window.hikashop.openBox(elem, url, jqmodal); },
		closeBox: function(parent) { return window.hikashop.closeBox(parent); },
		tabSelect: function(m,c,id) { return window.hikashop.tabSelect(m,c,id); },
		noChzn: function() {
			if(!window.jQuery)
				return false;
			jQuery('.no-chzn').each(function(i,el) {
				var id = el.getAttribute('id');
				id = id.replace('{','_').replace('}','_');
				var chzn = jQuery('#'+id+'_chzn');
				if(chzn) chzn.remove();
				jQuery(el).removeClass('chzn-done').show();
			});
			return true;
		}
	};

	if((typeof(window.Oby) == 'undefined') || window.Oby.version < Oby.version) {
		window.Oby = Oby;
		window.obscurelighty = Oby;
	}
	window.hikaserial = hikaserial;
})();
