/**
 * SqueezeBox - Expandable Lightbox
 *
 * Allows to open various content as modal,
 * centered and animated box.
 *
 *Converted to use jQuery
 *
 * Inspired by
 *  ... Lokesh Dhakar	- The original Lightbox v2
 * Harald Kirschne,Rouven Weßling - The Mootools version of this plugin
 *
 * @version		1.1
 *
 * @license		MIT-style license
 * @author		Webxsolution Ltd
 * @copyright	Author
 */
if(typeof jQuery != 'undefined') 
{
 
	if(typeof ARK == 'undefined')  ARK = {};
	  
	 jQuery(function($)  {
		
		"use strict";
		
		if(typeof SqueezeBox == 'object')
			return;
		
		ARK.squeezeBox = {

			presets: {
				onOpen: function(){},
				onClose: function(){},
				onUpdate: function(){},
				onResize: function(){},
				onMove: function(){},
				onShow: function(){},
				onHide: function(){},
				size: {x: 600, y: 450},
				sizeLoading: {x: 200, y: 150},
				marginInner: {x: 20, y: 20},
				marginImage: {x: 50, y: 75},
				handler: false,
				target: null,
				closable: true,
				closeBtn: true,
				zIndex: 65555,
				overlayOpacity: 0.7,
				classWindow: '',
				classOverlay: '',
				overlayFx: {},
				resizeFx: {},
				contentFx: {},
				parse: false, // 'rel'
				parseSecure: false,
				shadow: true,
				overlay: true,
				document: null,
				ajaxOptions: {},
				animate: {overlay: 250, win: 75,content: 450},
				iframePreload:false  
			},

			initialize: function(presets) {
				if (this.options) return this;
				
				var options = $.extend({},this.presets, presets);
				this.doc = this.presets.document || document;
				this.options = {};
							
				this.setOptions(options)
				this.build();
				

				this.bound = {
					window: $.proxy(this.reposition,this,[null]),
					scroll:	$.proxy(this.checkTarget,this),
					close:	$.proxy(this.close,this),
					key: 	$.proxy(this.onKey.bind,this)
				}
				this.isOpen = this.isLoading = false;
				return this;
			},
			
			addEvents : function () {
				
				var me = this;
				$.each(this, function(prop, value) {
					if($.isFunction(value) && /^on/.test(prop))
					{
						$(me).on(prop,$.proxy(me.prop));
						delete me.options[prop];
					}	
				});
			},
			
			
			setOptions: function(options) {
				$.extend(this.options,this.presets, options || {});
				this.addEvents();
			},

			build: function() {
				
				this.overlay = $('<div></div>')
					.attr({
					id: 'sbox-overlay',
					'aria-hidden': 'true',
					tabindex: -1
					}).css('z-index',this.options.zIndex);
				
				this.win = $('<div></div>')
					.attr({
					id: 'sbox-window',
					role: 'dialog',
					'aria-hidden': 'true'
					}).css('z-index',this.options.zIndex+2);
					
				if(this.options.shadow) {	
					
					if (window.attachEvent && !window.addEventListener)
					{	
						//Do Nothing
					}	
					else
					{	
						this.win.addClass('shadow');
					}

					 var shadow = $('<div></div>').attr({
										'class': 'sbox-bg-wrap'	
									}).appendTo(this.win);
					var relay = $.proxy(function(e) {
						this.overlay.trigger('click', [e]);
					},this);
					$.each(['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'], function( i, dir) {
						$('<div></div>').attr({'class': 'sbox-bg sbox-bg-' + dir}).appendTo(shadow).click(relay);
					});
				}	

				this.content = $('<div></div>').attr({id: 'sbox-content'}).appendTo(this.win);
				this.closeBtn = $('<a></a>').attr({id: 'sbox-btn-close', href: '#', role: 'button'}).appendTo(this.win);
				$(this.closeBtn).attr('aria-controls', 'sbox-window');
							
				$(this.doc.body).append(this.overlay, this.win);
			},

			assign: function(to, options) {
				var element = getElement(to);
				return (element.length && element || $.type(to) === 'string' &&  $('.'+to) || $(null)).click(function(ev) {
					ev.preventDefault();
					ev.stopPropagation();
					return !ARK.squeezeBox.fromElement(this, options);				
				});
				
			},
			
			open: function(subject, options) {
				this.initialize();
				
				if (this.element != null) this.trash();
				this.element =  getElement(subject) || false;
				

				this.setOptions($.extend({}, this.presets, options || {}));

				if (this.element && this.options.parse) {
					var obj = $(this.element).attr(this.options.parse);
					if (obj && (obj = parseJson(obj, this.options.parseSecure))) this.setOptions(obj);
				}
				this.url = ((this.element) ? (this.element.attr('href')) : subject) || this.options.url || '';
				
				this.assignOptions();

				var handler = handler || this.options.handler;
				
				
				if (handler) return this.setContent(handler, this.parsers[handler].call(this, true));
				var ret = false;
				var me  = this;
				return $.each(this.parsers, function(key, parser) {
					var content = parser.call(me);
					if (content) {
						ret = me.setContent(key, content);
						return true;
					}
					return false;
				});
			},

			fromElement: function(from, options) {
				return this.open(from, options);
			},

			assignOptions: function() {
				$(this.overlay).addClass(this.options.classOverlay);
				$(this.win).addClass(this.options.classWindow);
			},

			close: function(e) {
				var stoppable = (typeof(e) == 'domevent');
				if (stoppable) {
					e.stopPropagation();
					e.preventDefault();
				}	
				if (!this.isOpen || (stoppable && !($.isFunction(this.options.closable) ? this.options.closable.call(this, e) :  (function(){this.options.closable}).call(this, e) )))  return this;
				$(this.overlay).fadeOut(this.options.animate.overlay, $.proxy(this.toggleOverlay,this));
				$(this.win).attr('aria-hidden', 'true');
				$(this).trigger('onClose', [this.content]);
				this.trash();
				this.toggleListeners();
				this.isOpen = false;
				return this;
			},

			trash: function() {
				this.element = this.asset = null;
				$(this.content).empty();
				this.options = {};
				$(this).unbind()[0].setOptions(this.presets);
				$(this.closeBtn)
				return this;
			},

			onError: function() {
				this.asset = null;
				this.setContent('string', this.options.errorMsg || 'An error occurred');
			},

			setContent: function(handler, content) {
				if (!this.handlers[handler]) return false;
				this.content[0].className = 'sbox-content-' + handler;
				this.hideContent();			
				this.applyTimer = setTimeout($.proxy(function() {
										var results = this.handlers[handler].call(this, content);
										results = $.isArray(results) ? results : [results];	
										this.applyContent.apply(this,results);
									},this),this.options.animate.overlay);
				if ($(this.overlay).data('opacity')) return this;
				this.toggleOverlay(true);
				$(this.overlay).fadeTo(this.options.animate.overlay, this.options.overlayOpacity);
				return this.reposition();
			},

			applyContent: function(content, size) {
				if (!this.isOpen && !this.applyTimer) return;
				this.applyTimer = clearTimeout(this.applyTimer);
		
				if (!content) {
					this.toggleLoading(true);
				} else {
					if (this.isLoading) this.toggleLoading(false);
					var me = this;
					setTimeout(function()
					{
						$(me).trigger('onUpdate', [me.content]);
					},20);	
				}
				if (content) {
					$(this.content).append(content);
				}
				if (!this.isOpen) {
					this.toggleListeners(true);
					this.resize(size, true);
					this.isOpen = true;
					$(this.win).attr('aria-hidden', 'false');
					$(this).trigger('onOpen', [this.content]);
				} else {
					this.resize(size);
				}
			},

			resize: function(size, instantly) {
				this.showTimer = clearTimeout(this.showTimer || null);
				var box =  getSize(this.doc), scroll = getScroll(this.doc);
				this.size = $.extend((this.isLoading) ? this.options.sizeLoading : this.options.size, size);
				var parentSize = getSize(self);
				if (this.size.x == parentSize.x) {
					this.size.y = this.size.y - 50;
					this.size.x = this.size.x - 20;
				}
				if (box.x > 979) {
					var to = {
						width: this.size.x,
						height: this.size.y,
						left: parseInt(scroll.x + (box.x - this.size.x - this.options.marginInner.x) / 2),
						top: parseInt(scroll.y + (box.y - this.size.y - this.options.marginInner.y) / 2)
					};
				} else {
					var to = {
						width: box.x - 40,
						height: box.y,
						left: parseInt(scroll.x + 10),
						top: parseInt(scroll.y + 20)
					};
				}
				this.hideContent();
				if (!instantly) {
					$(this.win).animate(to,this.options.animate.win, $.proxy(this.showContent,this));
				} else {
					$(this.win).css(to);
					this.showTimer =  setTimeout($.proxy( this.showContent,this),50);
				}
				return this.reposition();
			},

			toggleListeners: function(state) {
				var fn = (state) ? 'on' : 'off';
				$(this.closeBtn)[fn]('click', this.bound.close);
				$(this.overlay)[fn]('click', this.bound.close);
				$(this.doc)[fn]('keydown', this.bound.key)[fn]('mousewheel', this.bound.scroll);
				$(this.doc.parentWindow || this.doc.defaultView) [fn]('resize', this.bound.window)[fn]('scroll', this.bound.window);
			},

			toggleLoading: function(state) {
				this.isLoading = state;
				$(this.win)[(state) ? 'addClass' : 'removeClass']('sbox-loading');
				if (state) {
					$(this.win).attr('aria-busy', state);
					$(this).trigger('onLoading', [this.win]);
				}
			},

			toggleOverlay: function(state) {
				if (this.options.overlay) {
					var full = getSize(this.doc).x;
					$(this.overlay).attr('aria-hidden', (state) ? 'false' : 'true');
					$(this.doc.body)[(state) ? 'addClass' : 'removeClass']('body-overlayed');
					if (state) {
						this.scrollOffset = getSize(this.doc.parentWindow || this.doc.defaultView).x - full;
					} else {
						$(this.doc.body).css('margin-right', '');
					}
				}
			},

			showContent: function() {
				if ($(this.content).attr('opacity')) $(this).trigger('onShow', [this.win]);
				$(this.content).fadeIn(this.options.animate.content,'easeOutExpo');
			},

			hideContent: function() {
				if (!$(this.content).attr('opacity')) $(this).trigger('onHide', [this.win]);
				$(this.content).fadeOut(this.options.animate.content);
			},

			onKey: function(e) {
				switch (e.key) {
					case 'esc': this.close(e);
					case 'up': case 'down': return false;
				}
			},

			checkTarget: function(e) {
				return e.target !== this.content && $(this.content).find(e.target);
			},

			reposition: function() {
				var size = getSize(this.doc), scroll = getScroll(this.doc), ssize = getScrollSize(this.doc);
				var over = $(this.overlay).css('height');
				var j = parseInt(over);

				if (ssize.y > j && size.y >= j) {

					$(this.overlay).css({
						width: ssize.x + 'px',
						height: ssize.y + 'px'
					});
					$(this.win).css({
						left: parseInt(scroll.x + (size.x - this.win.offsetWidth) / 2 - this.scrollOffset) + 'px',
						top: parseInt(scroll.y + (size.y - this.win.offsetHeight) / 2) + 'px'
					});
				}
				return $(this).trigger('onMove', [this.overlay, this.win]);
			},

			handlers: {},

			parsers: {}
		};
		
		$.extend(ARK.squeezeBox.parsers,{

			image: function(preset) {
				return (preset || (/\.(?:jpg|png|gif)$/i).test(this.url)) ? this.url : false;
			},

			clone: function(preset) {
				if (getElement(this.options.target)) return getElement(this.options.target);
				if (this.element && !this.element.parentNode) return this.element;
				var bits = this.url.match(/#([\w-]+)$/);
				return (bits) ? getElement(bits[1]) : (preset ? this.element : false);
			},

			ajax: function(preset) {
				return (preset || (this.url && !(/^(?:javascript|#)/i).test(this.url))) ? this.url : false;
			},

			iframe: function(preset) {
				return (preset || this.url) ? this.url : false;
			},

			string: function(preset) {
				return true;
			}
		});

		$.extend(ARK.squeezeBox.handlers,{

			image: function(url) {
				var size, tmp = new Image();
				this.asset = null;
				tmp.onload = tmp.onabort = tmp.onerror = $.proxy(function() {
					tmp.onload = tmp.onabort = tmp.onerror = null;
					if (!tmp.width) {
						setTimeout($.proxy(this.onError,this),10);
						return;
					}
					var box = getSize(this.doc);
					box.x -= this.options.marginImage.x;
					box.y -= this.options.marginImage.y;
					size = {x: tmp.width, y: tmp.height};
				
					for (var i = 2; i--;) {
						if (size.x > box.x) {
							size.y *= box.x / size.x;
							size.x = box.x;
						} else if (size.y > box.y) {
							size.x *= box.y / size.y;
							size.y = box.y;
						}
					}
					size.x = parseInt(size.x);
					size.y = parseInt(size.y);
					this.asset = $(tmp);
					tmp = null;
					this.asset.attr('width',size.x);
					this.asset.attr('height',size.y);
					this.applyContent(this.asset, size);
				},this);
				tmp.src = url;
				if (tmp && tmp.onload && tmp.complete) tmp.onload();
				return (this.asset) ? [this.asset, size] : null;
			},

			clone: function(el) {
				if (el) return $(el).clone();
				return this.onError();
			},

			adopt: function(el) {
				if (el) return el;
				return this.onError();
			},

			ajax: function(url) {
				var options = this.options.ajaxOptions || {};
				
				var me = this;
				function processFn(html)
				{
					me.applyContent(html);
					$(me).trigger('onAjax', [html,me.asset]);
					me.asset = null;
				}
				
				this.asset = $.ajax($.extend({
							url: url,
							method: "GET",
							datatype: "html",
							cache: false,
							async: true,
							complete : function(resp) {
								if(resp.status != 200)
								{
									setTimeout(function(){me.onError.call(me);},10);
								}
								else							
									setTimeout(function(){processFn(resp.responseText);},setTimeout);
							}
						},options
					)
				)
			},

			iframe: function(url) {
				var box = getSize(this.doc);
				if (box.x > 979) {
					var modal_width = this.options.size.x;
					var modal_height = this.options.size.y;
				} else {
					var modal_width = box.x;
					var modal_height = box.y - 50;
				}
				this.asset = $('<iframe></iframe>')	
							.attr($.extend({
									src: url,
									frameBorder: 0,
									width: modal_width,
									height: modal_height	
								},
								this.options.iframeOptions || {})
							)
				if(this.options.iframePreload) {
					this.asset.on('load', $.proxy(function() {
							this.applyContent($(this.asset).css('display', ''));
							this.asset.off('load');
						},this)
					)
					$(this.asset).css('display', 'none').appendTo(this.content);
					return false;
				}
				return this.asset;
			},

			string: function(str) {
				return str;
			}
		});


				
		function getElement(subject)	
		{
			if($.type(subject) === 'string')
				return $('#'+subject)
			return subject.jquery && subject || $(subject) ;
		}
		
		function getCompatElement(element){
			var doc = element.ownerDocument || element.document || element;
			return (!doc.compatMode || doc.compatMode == 'CSS1Compat') ? doc.documentElement : doc.body;
		}
		
		function getSize(element){
			var doc = getCompatElement(element);
			return {x: doc.clientWidth, y: doc.clientHeight};
		}

		function getScroll(element){
			var win = $(element).is('window') ? element : (element.parentWindow || element.defaultView), doc = getCompatElement(element);
			return {x: win.pageXOffset || doc.scrollLeft, y: win.pageYOffset || doc.scrollTop};
		}

		function getScrollSize(element){
			var doc = getCompatElement(element),
				min = getSize(element),
				body = (element.ownerDocument || element.document || element).body;
		
			return {x: Math.max(doc.scrollWidth, body.scrollWidth, min.x), y: Math.max(doc.scrollHeight, body.scrollHeight, min.y)};
		}
		
		function parseJson(str, secure) {
			if(secure)
				return $.parseJSON(str);
			 return eval('(' + str + ')');
		}
				
		ARK.squeezeBox.handlers.url = ARK.squeezeBox.handlers.ajax;
		ARK.squeezeBox.parsers.url = ARK.squeezeBox.parsers.ajax;
		ARK.squeezeBox.parsers.adopt = ARK.squeezeBox.parsers.clone;
		
		//register jquery plugin
		$.fn.squeezeBox = function( options, now) {
			if(now)
				return ARK.squeezeBox.open(this,options);
			return  ARK.squeezeBox.assign(this,options);
		}
		
	 });/**
	 * SqueezeBox - Expandable Lightbox
	 *
	 * Allows to open various content as modal,
	 * centered and animated box.
	 *
	 *Converted to use jQuery
	 *
	 * Inspired by
	 *  ... Lokesh Dhakar	- The original Lightbox v2
	 * Harald Kirschne,Rouven Weßling - The Mootools version of this plugin
	 *
	 * @version		1.1
	 *
	 * @license		MIT-style license
	 * @author		Webxsolution Ltd
	 * @copyright	Author
	 */
	if(typeof ARK == 'undefined')  ARK = {};
	  
	 jQuery(function($)  {
		
		"use strict";
		
		if(typeof SqueezeBox == 'object')
			return;
		
		ARK.squeezeBox = {

			presets: {
				onOpen: function(){},
				onClose: function(){},
				onUpdate: function(){},
				onResize: function(){},
				onMove: function(){},
				onShow: function(){},
				onHide: function(){},
				size: {x: 600, y: 450},
				sizeLoading: {x: 200, y: 150},
				marginInner: {x: 20, y: 20},
				marginImage: {x: 50, y: 75},
				handler: false,
				target: null,
				closable: true,
				closeBtn: true,
				zIndex: 65555,
				overlayOpacity: 0.7,
				classWindow: '',
				classOverlay: '',
				overlayFx: {},
				resizeFx: {},
				contentFx: {},
				parse: false, // 'rel'
				parseSecure: false,
				shadow: true,
				overlay: true,
				document: null,
				ajaxOptions: {},
				animate: {overlay: 250, win: 75,content: 450},
				iframePreload:false  
			},

			initialize: function(presets) {
				if (this.options) return this;
				
				var options = $.extend({},this.presets, presets);
				this.doc = this.presets.document || document;
				this.options = {};
							
				this.setOptions(options)
				this.build();
				

				this.bound = {
					window: $.proxy(this.reposition,this,[null]),
					scroll:	$.proxy(this.checkTarget,this),
					close:	$.proxy(this.close,this),
					key: 	$.proxy(this.onKey.bind,this)
				}
				this.isOpen = this.isLoading = false;
				return this;
			},
			
			addEvents : function () {
				
				var me = this;
				$.each(this, function(prop, value) {
					if($.isFunction(value) && /^on/.test(prop))
					{
						$(me).on(prop,$.proxy(me.prop));
						delete me.options[prop];
					}	
				});
			},
			
			
			setOptions: function(options) {
				$.extend(this.options,this.presets, options || {});
				this.addEvents();
			},

			build: function() {
				
				this.overlay = $('<div></div>')
					.attr({
					id: 'sbox-overlay',
					'aria-hidden': 'true',
					tabindex: -1
					}).css('z-index',this.options.zIndex);
				
				this.win = $('<div></div>')
					.attr({
					id: 'sbox-window',
					role: 'dialog',
					'aria-hidden': 'true'
					}).css('z-index',this.options.zIndex+2);
					
				if(this.options.shadow) {	
					
					if (window.attachEvent && !window.addEventListener)
					{	
						//Do Nothing
					}	
					else
					{	
						this.win.addClass('shadow');
					}

					 var shadow = $('<div></div>').attr({
										'class': 'sbox-bg-wrap'	
									}).appendTo(this.win);
					var relay = $.proxy(function(e) {
						this.overlay.trigger('click', [e]);
					},this);
					$.each(['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'], function( i, dir) {
						$('<div></div>').attr({'class': 'sbox-bg sbox-bg-' + dir}).appendTo(shadow).click(relay);
					});
				}	

				this.content = $('<div></div>').attr({id: 'sbox-content'}).appendTo(this.win);
				this.closeBtn = $('<a></a>').attr({id: 'sbox-btn-close', href: '#', role: 'button'}).appendTo(this.win);
				$(this.closeBtn).attr('aria-controls', 'sbox-window');
							
				$(this.doc.body).append(this.overlay, this.win);
			},

			assign: function(to, options) {
				var element = getElement(to);
				return (element.length && element || $.type(to) === 'string' &&  $('.'+to) || $(null)).click(function(ev) {
					ev.preventDefault();
					ev.stopPropagation();
					return !ARK.squeezeBox.fromElement(this, options);				
				});
				
			},
			
			open: function(subject, options) {
				this.initialize();
				
				if (this.element != null) this.trash();
				this.element =  getElement(subject) || false;
				

				this.setOptions($.extend({}, this.presets, options || {}));

				if (this.element && this.options.parse) {
					var obj = $(this.element).attr(this.options.parse);
					if (obj && (obj = parseJson(obj, this.options.parseSecure))) this.setOptions(obj);
				}
				this.url = ((this.element) ? (this.element.attr('href')) : subject) || this.options.url || '';
				
				this.assignOptions();

				var handler = handler || this.options.handler;
				
				
				if (handler) return this.setContent(handler, this.parsers[handler].call(this, true));
				var ret = false;
				var me  = this;
				return $.each(this.parsers, function(key, parser) {
					var content = parser.call(me);
					if (content) {
						ret = me.setContent(key, content);
						return true;
					}
					return false;
				});
			},

			fromElement: function(from, options) {
				return this.open(from, options);
			},

			assignOptions: function() {
				$(this.overlay).addClass(this.options.classOverlay);
				$(this.win).addClass(this.options.classWindow);
			},

			close: function(e) {
				var stoppable = (typeof(e) == 'domevent');
				if (stoppable) {
					e.stopPropagation();
					e.preventDefault();
				}	
				if (!this.isOpen || (stoppable && !($.isFunction(this.options.closable) ? this.options.closable.call(this, e) :  (function(){this.options.closable}).call(this, e) )))  return this;
				$(this.overlay).fadeOut(this.options.animate.overlay, $.proxy(this.toggleOverlay,this));
				$(this.win).attr('aria-hidden', 'true');
				$(this).trigger('onClose', [this.content]);
				this.trash();
				this.toggleListeners();
				this.isOpen = false;
				return this;
			},

			trash: function() {
				this.element = this.asset = null;
				$(this.content).empty();
				this.options = {};
				$(this).unbind()[0].setOptions(this.presets);
				$(this.closeBtn)
				return this;
			},

			onError: function() {
				this.asset = null;
				this.setContent('string', this.options.errorMsg || 'An error occurred');
			},

			setContent: function(handler, content) {
				if (!this.handlers[handler]) return false;
				this.content[0].className = 'sbox-content-' + handler;
				this.hideContent();			
				this.applyTimer = setTimeout($.proxy(function() {
										var results = this.handlers[handler].call(this, content);
										results = $.isArray(results) ? results : [results];	
										this.applyContent.apply(this,results);
									},this),this.options.animate.overlay);
				if ($(this.overlay).data('opacity')) return this;
				this.toggleOverlay(true);
				$(this.overlay).fadeTo(this.options.animate.overlay, this.options.overlayOpacity);
				return this.reposition();
			},

			applyContent: function(content, size) {
				if (!this.isOpen && !this.applyTimer) return;
				this.applyTimer = clearTimeout(this.applyTimer);
		
				if (!content) {
					this.toggleLoading(true);
				} else {
					if (this.isLoading) this.toggleLoading(false);
					var me = this;
					setTimeout(function()
					{
						$(me).trigger('onUpdate', [me.content]);
					},20);	
				}
				if (content) {
					$(this.content).append(content);
				}
				if (!this.isOpen) {
					this.toggleListeners(true);
					this.resize(size, true);
					this.isOpen = true;
					$(this.win).attr('aria-hidden', 'false');
					$(this).trigger('onOpen', [this.content]);
				} else {
					this.resize(size);
				}
			},

			resize: function(size, instantly) {
				this.showTimer = clearTimeout(this.showTimer || null);
				var box =  getSize(this.doc), scroll = getScroll(this.doc);
				this.size = $.extend((this.isLoading) ? this.options.sizeLoading : this.options.size, size);
				var parentSize = getSize(self);
				if (this.size.x == parentSize.x) {
					this.size.y = this.size.y - 50;
					this.size.x = this.size.x - 20;
				}
				if (box.x > 979) {
					var to = {
						width: this.size.x,
						height: this.size.y,
						left: parseInt(scroll.x + (box.x - this.size.x - this.options.marginInner.x) / 2),
						top: parseInt(scroll.y + (box.y - this.size.y - this.options.marginInner.y) / 2)
					};
				} else {
					var to = {
						width: box.x - 40,
						height: box.y,
						left: parseInt(scroll.x + 10),
						top: parseInt(scroll.y + 20)
					};
				}
				this.hideContent();
				if (!instantly) {
					$(this.win).animate(to,this.options.animate.win, $.proxy(this.showContent,this));
				} else {
					$(this.win).css(to);
					this.showTimer =  setTimeout($.proxy( this.showContent,this),50);
				}
				return this.reposition();
			},

			toggleListeners: function(state) {
				var fn = (state) ? 'on' : 'off';
				$(this.closeBtn)[fn]('click', this.bound.close);
				$(this.overlay)[fn]('click', this.bound.close);
				$(this.doc)[fn]('keydown', this.bound.key)[fn]('mousewheel', this.bound.scroll);
				$(this.doc.parentWindow || this.doc.defaultView) [fn]('resize', this.bound.window)[fn]('scroll', this.bound.window);
			},

			toggleLoading: function(state) {
				this.isLoading = state;
				$(this.win)[(state) ? 'addClass' : 'removeClass']('sbox-loading');
				if (state) {
					$(this.win).attr('aria-busy', state);
					$(this).trigger('onLoading', [this.win]);
				}
			},

			toggleOverlay: function(state) {
				if (this.options.overlay) {
					var full = getSize(this.doc).x;
					$(this.overlay).attr('aria-hidden', (state) ? 'false' : 'true');
					$(this.doc.body)[(state) ? 'addClass' : 'removeClass']('body-overlayed');
					if (state) {
						this.scrollOffset = getSize(this.doc.parentWindow || this.doc.defaultView).x - full;
					} else {
						$(this.doc.body).css('margin-right', '');
					}
				}
			},

			showContent: function() {
				if ($(this.content).attr('opacity')) $(this).trigger('onShow', [this.win]);
				$(this.content).fadeIn(this.options.animate.content,'easeOutExpo');
			},

			hideContent: function() {
				if (!$(this.content).attr('opacity')) $(this).trigger('onHide', [this.win]);
				$(this.content).fadeOut(this.options.animate.content);
			},

			onKey: function(e) {
				switch (e.key) {
					case 'esc': this.close(e);
					case 'up': case 'down': return false;
				}
			},

			checkTarget: function(e) {
				return e.target !== this.content && $(this.content).find(e.target);
			},

			reposition: function() {
				var size = getSize(this.doc), scroll = getScroll(this.doc), ssize = getScrollSize(this.doc);
				var over = $(this.overlay).css('height');
				var j = parseInt(over);

				if (ssize.y > j && size.y >= j) {

					$(this.overlay).css({
						width: ssize.x + 'px',
						height: ssize.y + 'px'
					});
					$(this.win).css({
						left: parseInt(scroll.x + (size.x - this.win.offsetWidth) / 2 - this.scrollOffset) + 'px',
						top: parseInt(scroll.y + (size.y - this.win.offsetHeight) / 2) + 'px'
					});
				}
				return $(this).trigger('onMove', [this.overlay, this.win]);
			},

			handlers: {},

			parsers: {}
		};
		
		$.extend(ARK.squeezeBox.parsers,{

			image: function(preset) {
				return (preset || (/\.(?:jpg|png|gif)$/i).test(this.url)) ? this.url : false;
			},

			clone: function(preset) {
				if (getElement(this.options.target)) return getElement(this.options.target);
				if (this.element && !this.element.parentNode) return this.element;
				var bits = this.url.match(/#([\w-]+)$/);
				return (bits) ? getElement(bits[1]) : (preset ? this.element : false);
			},

			ajax: function(preset) {
				return (preset || (this.url && !(/^(?:javascript|#)/i).test(this.url))) ? this.url : false;
			},

			iframe: function(preset) {
				return (preset || this.url) ? this.url : false;
			},

			string: function(preset) {
				return true;
			}
		});

		$.extend(ARK.squeezeBox.handlers,{

			image: function(url) {
				var size, tmp = new Image();
				this.asset = null;
				tmp.onload = tmp.onabort = tmp.onerror = $.proxy(function() {
					tmp.onload = tmp.onabort = tmp.onerror = null;
					if (!tmp.width) {
						setTimeout($.proxy(this.onError,this),10);
						return;
					}
					var box = getSize(this.doc);
					box.x -= this.options.marginImage.x;
					box.y -= this.options.marginImage.y;
					size = {x: tmp.width, y: tmp.height};
				
					for (var i = 2; i--;) {
						if (size.x > box.x) {
							size.y *= box.x / size.x;
							size.x = box.x;
						} else if (size.y > box.y) {
							size.x *= box.y / size.y;
							size.y = box.y;
						}
					}
					size.x = parseInt(size.x);
					size.y = parseInt(size.y);
					this.asset = $(tmp);
					tmp = null;
					this.asset.attr('width',size.x);
					this.asset.attr('height',size.y);
					this.applyContent(this.asset, size);
				},this);
				tmp.src = url;
				if (tmp && tmp.onload && tmp.complete) tmp.onload();
				return (this.asset) ? [this.asset, size] : null;
			},

			clone: function(el) {
				if (el) return $(el).clone();
				return this.onError();
			},

			adopt: function(el) {
				if (el) return el;
				return this.onError();
			},

			ajax: function(url) {
				var options = this.options.ajaxOptions || {};
				
				var me = this;
				function processFn(html)
				{
					me.applyContent(html);
					$(me).trigger('onAjax', [html,me.asset]);
					me.asset = null;
				}
				
				this.asset = $.ajax($.extend({
							url: url,
							method: "GET",
							datatype: "html",
							cache: false,
							async: true,
							complete : function(resp) {
								if(resp.status != 200)
								{
									setTimeout(function(){me.onError.call(me);},10);
								}
								else							
									setTimeout(function(){processFn(resp.responseText);},setTimeout);
							}
						},options
					)
				)
			},

			iframe: function(url) {
				var box = getSize(this.doc);
				if (box.x > 979) {
					var modal_width = this.options.size.x;
					var modal_height = this.options.size.y;
				} else {
					var modal_width = box.x;
					var modal_height = box.y - 50;
				}
				this.asset = $('<iframe></iframe>')	
							.attr($.extend({
									src: url,
									frameBorder: 0,
									width: modal_width,
									height: modal_height	
								},
								this.options.iframeOptions || {})
							)
				if(this.options.iframePreload) {
					this.asset.on('load', $.proxy(function() {
							this.applyContent($(this.asset).css('display', ''));
							this.asset.off('load');
						},this)
					)
					$(this.asset).css('display', 'none').appendTo(this.content);
					return false;
				}
				return this.asset;
			},

			string: function(str) {
				return str;
			}
		});


				
		function getElement(subject)	
		{
			if($.type(subject) === 'string')
				return $('#'+subject)
			return subject.jquery && subject || $(subject) ;
		}
		
		function getCompatElement(element){
			var doc = element.ownerDocument || element.document || element;
			return (!doc.compatMode || doc.compatMode == 'CSS1Compat') ? doc.documentElement : doc.body;
		}
		
		function getSize(element){
			var doc = getCompatElement(element);
			return {x: doc.clientWidth, y: doc.clientHeight};
		}

		function getScroll(element){
			var win = $(element).is('window') ? element : (element.parentWindow || element.defaultView), doc = getCompatElement(element);
			return {x: win.pageXOffset || doc.scrollLeft, y: win.pageYOffset || doc.scrollTop};
		}

		function getScrollSize(element){
			var doc = getCompatElement(element),
				min = getSize(element),
				body = (element.ownerDocument || element.document || element).body;
		
			return {x: Math.max(doc.scrollWidth, body.scrollWidth, min.x), y: Math.max(doc.scrollHeight, body.scrollHeight, min.y)};
		}
		
		function parseJson(str, secure) {
			if(secure)
				return $.parseJSON(str);
			 return eval('(' + str + ')');
		}
				
		ARK.squeezeBox.handlers.url = ARK.squeezeBox.handlers.ajax;
		ARK.squeezeBox.parsers.url = ARK.squeezeBox.parsers.ajax;
		ARK.squeezeBox.parsers.adopt = ARK.squeezeBox.parsers.clone;
		
		//register jquery plugin
		$.fn.squeezeBox = function( options, now) {
			if(now)
				return ARK.squeezeBox.open(this,options);
			return  ARK.squeezeBox.assign(this,options);
		}
		
	 });
}