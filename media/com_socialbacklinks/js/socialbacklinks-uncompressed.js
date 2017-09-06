/**
 * Describes all of used classes
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Create a new name space
 */
var SB = SB || {};
/**
 * @class Represents a request class.
 */
SB.Request = new Class(
/**
 * @lends SB.Request.prototype
 */
{
	Extends : Request.JSON,
	/** @constructs */
	initialize : function(options) {
		var def_options = {

			url : 'index.php?option=com_socialbacklinks&format=raw',

			method : 'post',

			object : null,
			block : null,
			parent : null,

			ajax_loader : '.ajax-loader',
			ajax_overlay : '.ajax-overlay',
			error_block : '.error-block',

			ajax_error_msg : null,
			callback : null,
			callbackParam : null,

			onRequest : function() {
				if(this.options.success_block) {
					this.options.success_block.get('tween').cancel();
					this.options.success_block.setStyle('opacity', 0);
				}

				this.options.block.getElement(this.options.ajax_loader).setStyle('display', 'block');
				this.options.block.getElement(this.options.ajax_overlay).setStyle('display', 'block');
			},
			onSuccess : function(data, responseText) {
				this.options.block.getElement(this.options.ajax_loader).setStyle('display', 'none');
				this.options.block.getElement(this.options.ajax_overlay).setStyle('display', 'none');

				if(data['error']) {
					this.options.object.addClass('error');
					this.options.parent.getElement(this.options.error_block).set('html', data['msg']).setStyle('display', 'block');
				} else {
					this.options.object.removeClass('error');
					this.options.parent.getElement(this.options.error_block).set('html', '').setStyle('display', 'none');

					if(this.options.show_success_msg && this.options.success_block && this.options.ajax_success_msg) {
						this.options.show_success_msg(this.options.success_block, this.options.ajax_success_msg);
					}
					if(this.options.callback != null) {
						if(this.options.callbackParam != null) {
							this.options.callback(this.options.object, data, this.options.data, this.options.callbackParam);
						} else {
							this.options.callback(this.options.object, data, this.options.data);
						}
					}
				}
			},
			onFailure : function(text) {
				this.options.parent.getElement(this.options.error_block).set('html', this.options.ajax_error_msg).setStyle('display', 'block');
				this.options.block.getElement(this.options.ajax_loader).setStyle('display', 'none');
				this.options.block.getElement(this.options.ajax_overlay).setStyle('display', 'none');
			}
		};

		if(options.onRequest) {
			def_options.onRequest = options.onRequest;
		}
		if(options.onSuccess) {
			def_options.onSuccess = options.onSuccess;
		}
		if(options.onFailure) {
			def_options.onFailure = options.onFailure;
		}
		this.setOptions(def_options);

		this.parent(options);
	}
});
/**
 * @class Represents a basic class.
 */
SB.Base = new Class(
/**
 * @lends SB.Base.prototype
 */
{
	Implements : [Options],
	/**
	 * Close the lightbox window
	 *
	 * @public
	 */
	closeLightbox : function() {
		parent.SqueezeBox.close();
	},
	/**
	 * Animate change switch from on to off and conversely
	 *
	 * @public
	 * @param {Object} object item which will be changed
	 * @param {Object} data additional information
	 */
	changeOnOff : function(object, data) {
		object.set( 'tween', {'duration': 500} );
		if(object.hasClass('on-button')) {
			object.get('tween').addEvent('complete', function() {
				object.removeClass('on-button');
				object.addClass('off-button');
			});
			object.tween('left', -30);
		} else {
			object.get('tween').addEvent('complete', function() {
				object.removeClass('off-button');
				object.addClass('on-button');
			});
			object.tween('left', 0);
		}
	},
	/**
	 * Shows the success message after ajax request
	 *
	 * @public
	 * @param {Object} object success message block
	 * @param {string} msg string with message
	 */
	showSuccessMsg : function(object, msg) {
		object.set('text', msg);
		object.tween('opacity', 1);

		window.parent.timer = setTimeout(function() {
			object.tween('opacity', 0);
		}, 2000);
	}
});
/**
 * @class Represents a dash board class.
 */
SB.Dashboard = new Class(
/**
 * @lends SB.Dashboard.prototype
 */
{
	Implements : [SB.Base],
	options : {
		wrapper : null,
		header : null,
		toggle_button : '.toggle-button',
		content_block : '.content-block',
		single_block : null,
		social_wrapper : 'social-wrapper',
		social_block : '.social-block',
		social_connect : null,

		config_on_off : '.config-on-off',
		social_on_off : '.social-on-off',
		icon_wrapper : '.iconwrapper',
		social_logout : '.logout',
		social_config : '.config',
		social_info : '.information',

		error_block : '.error-block',
		success_block : '.success-block',
		ajax_error_msg : null,
		ajax_success_msg : null,
		ajax_loader : '.ajax-loader',
		ajax_overlay : '.ajax-overlay'
	},
	/** @constructs */
	initialize : function(options) {
		this.setOptions(options);

		this.initBlockToggle();
		this.deleteLastBorder();
		this.initSyncUpdateCheck();
		this.initConfigOnOff();
		this.initSocialOnOff();
		this.initSocialConnect();
		this.initSocialConfig();
	},
	/**
	 * Add slide toggle to header element of configuration blocks
	 *
	 * @private
	 */
	initBlockToggle : function() {
		var _this = this;
		$$(this.options.header).addEvent('click', function() {
			var toggle = $(this).getParent(_this.options.wrapper).getElement(_this.options.toggle_button);
			toggle.toggleClass('close');

			var content = $(this).getParent(_this.options.wrapper).getElement(_this.options.content_block).slide('toggle');

			var my_cookie = Cookie.write('sb_' + content.get('id'), parseInt(content.getStyle('margin')), {
				duration : 30
			});
		});
		$$(this.options.header).each(function(item, index) {
			var content = item.getParent(_this.options.wrapper).getElement(_this.options.content_block);

			var my_cookie = Cookie.read('sb_' + content.get('id'));

			if( ((my_cookie == null) && (index > 0)) || (my_cookie == 0) ) {
				item.getParent(_this.options.wrapper).getElement(_this.options.toggle_button).addClass('close');
				content.slide('toggle');
			}
		});
	},
	/**
	 * Delete border in last block
	 *
	 * @private
	 */
	deleteLastBorder : function() {
		var _this = this;
		$$(this.options.content_block).each(function(block, key) {
			var blocks = $(block).getElements(_this.options.single_block);
			blocks.getLast().addClass('last-block');
		});

		var block = $$(this.options.social_block).getLast();
		if ( block )
			block.addClass('last-block');
	},
	/**
	 * Check for history of synchronization updated every 5 seconds
	 *
	 * @private
	 */
	initSyncUpdateCheck : function() {
		var _this = this;
		var check_history_update = setInterval(function() {
			_this.checkHistoryUpdate();
		}, 5000);
	},
	/**
	 * Check updates in history
	 *
	 * @public
	 */
	checkHistoryUpdate : function() {
		var last_id = Cookie.read('sb_history');

		var sbRequest = new SB.Request({
			data : {
				view : 'histories',
				task : 'checkhistoryupdate',
				'last_id' : last_id
			},

			onRequest : function() {
			},

			onFailure : function() {
			},

			onSuccess : function(data) {
				var loader = $('statistics').getElement('.ajax-loader');
				var update_time = $('statistics').getElement('.update-time');
				var wrapper = $('statistics').getElement('.text-wrapper');

				if(data.update_sync_date) {
					update_time.get('tween').addEvent('complete', function() {
						update_time.getElement('.day').set('text', data.last_sync.day);
						update_time.getElement('.date').set('text', data.last_sync.date);
						update_time.getElement('.time').set('text', data.last_sync['time']);

						update_time.fade('in');
					});

					update_time.tween('opacity', 0);
				}
				if(data.updated) {
					var my_cookie = Cookie.write('sb_history', data['last_id']);

					var fx = new Fx.Tween(wrapper);

					fx.addEvents({
						"start" : function() {
							loader.setStyle('display', 'block');
						},
						"complete" : function() {
							wrapper.set('html', data['html']);
							wrapper.fade('in');

							loader.setStyle('display', 'none');
						}
					});

					fx.start("opacity", 1, 0);
				}
			}
		}).send();
	},
	/**
	 * Add on/off function to configuration buttons
	 *
	 * @private
	 */
	initConfigOnOff : function() {
		var _this = this;
		
		// Redshop special
		try {
      $$("#block-redshop").getElement("#sync_updated").getParent().getParent().getParent().hide();
		} catch(err) { }

		$$(this.options.config_on_off).addEvent('click', function() {
			var param = this.getElement('*').get('text');
			var value = this.hasClass('off-button') + 0;
			var ajax_param = JSON.decode('{ value: ' + value + ', ' + param + ' }');

			var request_option = {

				object : this,
				block : this.getParent(_this.options.single_block),
				parent : this.getParent(_this.options.wrapper),
				success_block : this.getParent(_this.options.single_block).getElement(_this.options.success_block),

				ajax_error_msg : _this.options.ajax_error_msg,
				ajax_success_msg : _this.options.ajax_success_msg,
				show_success_msg : _this.showSuccessMsg,
				callback : _this.changeOnOff
			};

			var sbRequest = new SB.Request({
				data : ajax_param
			}).setOptions(request_option).send();
		});
	},
	/**
	 * Add on/off function to social networks buttons
	 *
	 * @private
	 */
	initSocialOnOff : function() {
		var _this = this;
		$$(this.options.social_on_off).addEvent('click', function() {
			var social = this.get('id');
			if(this.hasClass('off-button') && $(social + '-disconnect').hasClass('disabled') ) {
				$(social + '-connect').getElement('a').fireEvent('click');
			} else {
				var param_str = '{ ' + this.getElement('*').get('text') + ", 'value': '" + (this.hasClass('off-button') + 0) + "' }";
				var ajax_param = eval("(" + param_str + ")");

				var request_option = {

					object : this,
					block : this.getParent(_this.options.social_block),
					parent : this.getParent(_this.options.wrapper),
					success_block : this.getParent(_this.options.social_block).getElement(_this.options.success_block),

					ajax_error_msg : _this.options.ajax_error_msg,
					ajax_success_msg : _this.options.ajax_success_msg,
					show_success_msg : _this.showSuccessMsg,
					callback : _this.changeSocialOnOff
				};

				var sbRequest = new SB.Request({
					data : ajax_param
				}).setOptions(request_option).send();
			}
		});
	},
	/**
	 * Animate change switch from on to off and conversely in social buttons
	 *
	 * @public
	 * @param {Object} object item which will be changed
	 * @param {Object} data additional information
	 */
	changeSocialOnOff : function(object, data) {
		object.set('tween', {
			'duration' : 500
		});
		if(object.hasClass('on-button')) {
			if(object.get('id')) {
				$(object.get('id') + '-icon').addClass('off');
			}

			object.get('tween').addEvent('complete', function() {
				object.removeClass('on-button');
				object.addClass('off-button');
			});
			object.tween('left', -30);
		} else {
			if(object.get('id')) {
				$(object.get('id') + '-icon').removeClass('off');
			}

			object.get('tween').addEvent('complete', function() {
				object.removeClass('off-button');
				object.addClass('on-button');
			});
			object.tween('left', 0);
		}
	},
	/**
	 * Add connect function to social networks icons
	 *
	 * @private
	 */
	initSocialConnect : function() {
		var _this = this;
		$(this.options.social_wrapper).getElements(this.options.social_connect).addEvent('click', function() {
			var height = 600;
			var width = 600;
			var href = this.get('href');

			var type = this.getParent().get('id');
			if(type == 'facebook-connect') {
				height = 450;
				width = 400;
			} else if(type == 'twitter-connect') {
				height = 630;
				width = 600;
			} else if(type == 'linkedin-connect') {
				height = 400;
				width = 600;
			}

			window.open(href, 'popup', 'height=' + height + ',width=' + width + ',location=0,menubar=0,scrollbars=0,status=0,titlebar=0,toolbar=0');
		});
	},
	/**
	 * Add open configuration window after click on icon
	 *
	 * @private
	 */
	initSocialConfig : function() {
		var _this = this;
		$(this.options.social_wrapper).getElements(this.options.social_block).addEvents({
			'mouseenter' : function() {
				this.getElement(_this.options.social_logout).fade('in');
				this.getElement(_this.options.social_config).fade('in');
				this.getElement(_this.options.social_info).fade('in');
			},
			'mouseleave' : function() {
				this.getElement(_this.options.social_logout).fade('out');
				this.getElement(_this.options.social_config).fade('out');
				this.getElement(_this.options.social_info).fade('out');
			}
		});
	}
});
/**
 * @class Represents a content class.
 */
SB.Content = new Class(
/**
 * @lends SB.Content.prototype
 */
{
	Implements : [SB.Base],
	options : {
		wrapper : null,
		single_block : null,
		overlay_text : '.overlay-text',
		content_body : 'content-body',
		plugin : 'content',
		pluginSuffix : '-config',
		categories_body : 'categories-body',

		save_button : '.save-button',
		selected_content : '.selected_content',
		row_id : '.row-id',
		delete_row_button : '.delete-row-button',
		
		parental_row: '.parental-row',
		control_buttons: '.control-buttons',
		check_child: '.check-child',
		uncheck_child: '.uncheck-child',

		delete_msg : null,
		ajax_error_msg : null,
		ajax_success_msg : null,

		success_block : '.success-block',
		error_block : '.error-block',
		ajax_loader : '.ajax-loader',
		ajax_overlay : '.ajax-overlay',

		block_fx : null,
		initAll : true
	},
	/** @constructs */
	initialize : function(options) {
		this.setOptions(options);
		if(this.options.initAll) {
			this.initSelectItems();
			this.initSaveButton();
			this.initDeleteRowButton();
			this.initCheckChildCategories();
			this.initRowChecked();
		}
	},
	/**
	 * Check enabled or disabled select items
	 *
	 * @private
	 */
	initSelectItems : function() {
		this.checkSelectItems();
		var _this = this;
		document.getElements(this.options.selected_content).addEvent('click', function() {
			_this.checkSelectItems();
		});
	},
	/**
	 * Display or hide overlay to disabled select items
	 *
	 * @public
	 * @param {Object} object checkbox
	 */
	checkSelectItems : function() {
		if($$('input[name=selected_content]:checked').length == 0) {
			return true;
		}

		if(this.options.block_fx) {
			this.options.block_fx.stop();
		}

		if($$('input[name=selected_content]:checked')[0].getProperty('value') == 0) {
			var _this = this;

			this.options.block_fx = new Fx.Tween($(this.options.content_body), {
				'property' : 'opacity',
				'duration' : 200
			});

			this.options.block_fx.start(0).chain(function() {
				this.element.setStyle('display', 'none');

				var overlay = $(_this.options.content_body).getParent().getElement(_this.options.overlay_text);

				overlay.setStyle('display', 'block');

				_this.options.block_fx = new Fx.Tween(overlay, {
					'property' : 'opacity',
					'duration' : 200
				});

				_this.options.block_fx.start(1);
			});
		} else {
			var _this = this;

			var overlay = $(_this.options.content_body).getParent().getElement(_this.options.overlay_text);

			this.options.block_fx = new Fx.Tween(overlay, {
				'property' : 'opacity',
				'duration' : 200
			});

			this.options.block_fx.start(0).chain(function() {
				this.element.setStyle('display', 'none');

				$(_this.options.content_body).setStyle('display', 'block');

				_this.options.block_fx = new Fx.Tween($(_this.options.content_body), {
					'property' : 'opacity',
					'duration' : 200
				});

				_this.options.block_fx.start(1);
			});
		}
	},
	/**
	 * Add save function to buttons
	 *
	 * @private
	 */
	initSaveButton : function() {
		var _this = this;
		document.getElement(this.options.save_button).addEvent('click', function() {
			_this.submitForm();
		});
	},
	/**
	 * Add delete function to button
	 *
	 * @private
	 */
	initDeleteRowButton : function() {
		var _this = this;
		$$(this.options.delete_row_button).addEvent("click", function() {
			var id = this.getParent('tr').getElement(_this.options.row_id).get('text').toInt();
			_this.deleteRow(id);
		});
	},
	/**
	 * Delete row from articles table
	 *
	 * @public
	 * @param id {Number} identifier of row
	 */
	deleteRow : function(id) {
		var _this = this;
		if(window.confirm(this.options.delete_msg)) {
			var row = $('row-' + id);
			var fx = new Fx.Tween(row);

			fx.addEvent("complete", function() {
				if(row.getParent('tbody').getElements('tr').length == 1) {
					var td = new Element('td', {
						'colspan' : 7,
						'align' : 'center',
						'text' : _this.options.empty_table_msg
					});
					var new_row = new Element('tr', {
						'styles' : {
							'opacity' : '0'
						}
					});
					new_row.grab(td);

					new_row.inject(row, 'after').fade('in');
				}

				row.dispose();
			});

			fx.start("opacity", 1, 0);
		}
	},
	/**
	 * Check/uncheck child categories
	 * 
	 * @private
	 */
	initCheckChildCategories: function()
	{
		var _this = this;
		$( this.options.categories_body )
			.getElements( _this.options.parental_row )
			.addEvents(
			{
				'mouseenter': function()
				{
					this.getElement( _this.options.control_buttons )
						.fade('in');
				},
				'mouseleave': function(event)
				{
					this.getElement( _this.options.control_buttons )
						.fade('out');
				}
			});
		$$( _this.options.check_child ).addEvent('click', function(event)
		{
			event.stop();
			var checkbox = this.getParent('tr')
				.getElement( 'input[type=checkbox]' )
				.set('checked', 'checked');
			_this.updateChildCategories(checkbox)
		});
		$$( _this.options.uncheck_child ).addEvent('click', function(event)
		{
			event.stop();
			var checkbox = this.getParent('tr')
				.getElement( 'input[type=checkbox]' )
				.set('checked', '');
			_this.updateChildCategories(checkbox)
		})
	},
	/**
	 * Check or uncheck children categories
	 * 
	 * @param {object} category which was checked/unchecked
	 */
	updateChildCategories: function( category )
	{
		var _this = this;
		
		var level_reg = /level-(\d+)/;
		var level = level_reg.exec( category.get('class') );
		if ( (level != null) && level[1] )
		{
			var id = category.get('id');
			$$('input[type=checkbox]').filter( '.parent-'+id ).filter( '.level-'+ (level[1].toInt() + 1) ).each(function(item, index)
				{
					if ( category.checked )
					{
						item.set('checked', 'checked')
					}
					else {
						item.set('checked', '')
					}
					_this.updateChildCategories(item)
				})
		}
	},
	/**
	 * Add event: when click on row, check or uncheck item
	 *
	 * @private
	 */
	initRowChecked : function() {
		$(this.options.categories_body).getElement('tbody').getElements('tr').addEvent('click', function(event) {
			if(event.target.tagName.toLowerCase() == 'input') {
				return true;
			}
			var checkbox = $(this).getElement('input[type=checkbox]');
			if( checkbox && !$(checkbox).get('disabled') ) {
				if(checkbox.checked) {
					checkbox.setProperty('checked', '');
				} else {
					checkbox.setProperty('checked', 'checked');
				}
				checkbox.fireEvent('click');
			}
		});
	},
	/**
	 * Add row into articles table
	 *
	 * @private
	 * @param {Object} object with data of the article
	 * @param {Object} base object of class with needed data
	 */
	addRow : function(object, base) {
		var _this = base;
		var body = $('articles-body').getElement('.adminlist').getElement('tbody');

		var td1 = new Element('td', {
			text : object.title
		});
		var td2 = new Element('td', {
			text : object.cctitle
		});
		var td3 = new Element('td', {
			text : object.author
		});
		var td4 = new Element('td', {
			text : object.created
		});
		var td5 = new Element('td', {
			'class' : _this.options.row_id.substr(1),
			text : object.id
		});
		var td6 = new Element('td', {
			align : 'center'
		});
		var del = new Element('div', {
			'class' : _this.options.delete_row_button.substr(1),

			events : {
				click : function() {
					_this.deleteRow(object.id);
				}
			}
		});
		td6.grab(del);

		if(body.getElements('tr').getLast().hasClass('row0')) {
			var class_name = 'row1';
		} else {
			var class_name = 'row0';
		}
		var row = new Element('tr', {
			id : 'row-' + object.id,
			'class' : class_name
		});
		row.grab(td1).grab(td2).grab(td3).grab(td4).grab(td5).grab(td6);

		if((body.getChildren().length == 1) && (body.getElement('td').getProperty('colspan') > 1)) {
			body.set('html', '');
			body.grab(row);
		} else {
			body.grab(row);
		}
	},
	/**
	 * Get data from tables and send it for save
	 *
	 * @public
	 */
	submitForm : function() {
		if($$('input[name=selected_content]:checked').length == 0) {
			var selected_content = 0;
		} else {
			var selected_content = $$('input[name=selected_content]:checked')[0].getProperty('value');
		}

		var articles_id = Array();
		$('articles-body').getElements('td.row-id').each(function(item, index) {
			var id = parseInt(item.get('text'));
			if(id != 0) {
				articles_id.include(id);
			}
		});
		if(articles_id.length == 0) {
			articles_id.include(-1);
		}
		
		var categories_id = Array();
		var categories = $$('input[name=categories]:checked');
		for(var i = 0; i < categories.length; i++) {
			categories_id[i] = categories[i].value;
		}
		if(categories_id.length == 0) {
			categories_id.include(-1);
		}
		
		var data = {
			'selected_content' : selected_content,
			'categories' : categories_id,
			items : articles_id
		};

		this.saveContent(data);
	},
	/**
	 * Send information to server for save it
	 *
	 * @private
	 * @param {Array} data list of data to save
	 */
	saveContent : function(data) {
		var object = $('selected_content0');

		var ajax_param = {
			view : 'plugin',
			task : 'save',
			plugintype : 'content',
			plugin : this.options.plugin,
			value : data,
			status_msg : this.options.status_msg
		};

		var request_option = {
			object : object,
			block : object.getParent(this.options.single_block),
			parent : object.getParent(this.options.wrapper),
			success_block : parent.$(this.options.plugin + this.options.pluginSuffix).getElement(this.options.success_block),

			ajax_error_msg : this.options.ajax_error_msg,
			ajax_success_msg : this.options.ajax_success_msg,

			show_success_msg : window.parent.showSuccessMsg,
			callback : this.updateStatusText,
			callbackParam : this.options.plugin + this.options.pluginSuffix
		};

		sbRequest = new SB.Request({
			data : ajax_param
		}).setOptions(request_option).send();
	},
	/**
	 * Updates status block on dashboard
	 *
	 * @public
	 * @param {Object} object item which will be changed
	 * @param {Object} data result data after request
	 * @param {Object} input_data input data to request
	 */
	updateStatusText : function(object, data, input_data, plugin) {
		var text_object = parent.$(plugin).getElement('.block').getElement('.text-block');
		if(text_object) {
			text_object.get('tween')
				.addEvent( 'complete', function() {
					if(input_data.status_msg[input_data.value.selected_content]) {
						text_object.set('text', input_data.status_msg[input_data.value.selected_content]);
					} else {
						text_object.set('text', '');
					}
					text_object.fade('in');
				});
			text_object.tween( 'opacity', 0 );
		}
		parent.SqueezeBox.close();
	}
});
/**
 * @class Represents a configuration class.
 */
SB.Config = new Class(
/**
 * @lends SB.prototype
 */
{
	Implements : [SB.Base],
	options : {

		parent_block : null,
		hidden_block : '.hidden-block',
		single_block : null,

		save_button : '.save-button',
		config_on_off : '.config-on-off',

		ajax_error_msg : null,
		ajax_success_msg : null,

		error_block : '.error-block',
		success_block : '.success-block',
		ajax_loader : '.ajax-loader',
		ajax_overlay : '.ajax-overlay'
	},
	/** @constructs */
	initialize : function(options) {
		this.setOptions(options);

		this.initSaveButton();
		this.initSyncOnOff();
		this.initChangeEmailType();
		this.initLinks();
		this.initChangeCleanHistoryStatus();
	},
	/**
	 * Add save function to buttons
	 *
	 * @private
	 */
	initSaveButton : function() {
		var _this = this;
		$('configuration').getElement(this.options.save_button).addEvent('click', function() {
			_this.submitForm();
		});
	},
	/**
	 * Adds on/off function to sync configuration button
	 *
	 * @private
	 */
	initSyncOnOff : function() {
		var _this = this;
		$('sbsynchronizer').addEvent('click', function() {
			var element = this;
			if(this.hasClass('on-button')) {
				var object = this.getParent(_this.options.parent_block).getElement(_this.options.hidden_block);

				object.get('tween').removeEvents('complete').addEvent('complete', function() {
					object.setStyle('display', 'none');
					$('configuration').getElement('input[name=' + element.get('id') + ']').set('value', 0);
					_this.changeOnOff(element);
				});
				object.tween('opacity', 0);
			} else {
				var object = this.getParent(_this.options.parent_block).getElement(_this.options.hidden_block);

				object.get('tween').removeEvents('complete').addEvent('complete', function() {
					$('configuration').getElement('input[name=' + element.get('id') + ']').set('value', 1);
					_this.changeOnOff(element);
				});
				object.setStyle('display', 'block').tween('opacity', 1);
			}
		});
	},
	/**
	 * Hides and shows input box for email address
	 *
	 * @private
	 */
	initChangeEmailType : function() {
		var _this = this;
		$('configuration').getElements('.errors_recipient_type').addEvent('click', function() {
			var radio_btn = this.getElement('input');
			if(!radio_btn || (radio_btn.get('type') != 'radio')) {
				return true;
			}
			if(radio_btn.get('value') == 1) {
				var object = radio_btn.getParent(_this.options.single_block).getNext(_this.options.hidden_block);

				object.get('tween').removeEvents('complete');
				object.setStyle('display', 'block').tween('opacity', 1);
			} else {
				var object = radio_btn.getParent(_this.options.single_block).getNext(_this.options.hidden_block);

				object.get('tween').removeEvents('complete').addEvent('complete', function() {
					object.setStyle('display', 'none');
				});
				object.tween('opacity', 0);
			}
		});
	},
	/**
	 * Updates parent page after click on link
	 * 
	 * @private 
	 */
	initLinks : function() {
		document.getElements('a').addEvent( 'click', function() {
			window.parent.location.href = this.get('href');
		});
	},
	/**
	 * Hides and shows input box for clean history periodicity
	 *
	 * @private
	 */
	initChangeCleanHistoryStatus : function() {
		var _this = this;
		$('configuration').getElements('.clean_history').addEvent('click', function() {
			var radio_btn = this.getElement('input');
			if(!radio_btn || (radio_btn.get('type') != 'radio')) {
				return true;
			}
			if(radio_btn.get('value') == 1) {
				var object = radio_btn.getParent(_this.options.single_block).getNext(_this.options.hidden_block);

				object.get('tween').removeEvents('complete');
				object.setStyle('display', 'block').tween('opacity', 1);
			} else {
				var object = radio_btn.getParent(_this.options.single_block).getNext(_this.options.hidden_block);

				object.get('tween').removeEvents('complete').addEvent('complete', function() {
					object.setStyle('display', 'none');
				});
				object.tween('opacity', 0);
			}
		});
	},
	/**
	 * Get data from tables and send it for save
	 *
	 * @public
	 */
	submitForm : function() {
		var data = {};
		var name, value;

		if($$('input').length > 0) {
			$$('input').each(function(item) {
				if(Array('checkbox', 'radio').indexOf(item.get('type')) == -1) {
					name = item.get('name');
					value = item.get('value');

					data[name] = value;
				}
			});
		}

		if($$('input:checked').length > 0) {
			$$('input:checked').each(function(item) {
				name = item.get('name');
				value = item.get('value');

				data[name] = value;
			});
		}

		var cid = {};
		var i;
		for(i in data) {
			if(Array('option', 'task', 'view').indexOf(i) == -1) {
				cid[i] = data[i];
				delete data[i];
			}
		}
		data.cid = cid;

		this.saveConfig(data);
	},
	/**
	 * Saves selected configuration
	 *
	 * @private
	 * @param {Array} data list of data to save
	 */
	saveConfig : function(data) {
		var object = $('configuration');

		var request_option = {

			object : object.getElement('table'),
			block : object,
			parent : object,
			success_block : parent.$('settings').getElement(this.options.success_block),

			ajax_error_msg : this.options.ajax_error_msg,
			ajax_success_msg : this.options.ajax_success_msg,

			show_success_msg : window.parent.showSuccessMsg,
			callback : this.updateStatusText
		};

		var sbRequest = new SB.Request({
			'data' : data
		}).setOptions(request_option).send();
	},
	/**
	 * Updates status block on dashboard
	 *
	 * @public
	 * @param {Object} object item which will be changed
	 * @param {Object} data result data after request
	 * @param {Object} input_data input data to request
	 */
	updateStatusText : function(object, data) {
		var text_object = parent.$('settings').getElement('.block').getElement('.text-block');
		if(text_object) {
			text_object.get('tween').addEvent( 'complete', function() {
				if(data.msg) {
					text_object.set('html', data.msg);
				} else {
					text_object.set('text', '');
				}
				text_object.fade('in');
			} );
			text_object.tween( 'opacity', 0 );
		}
		parent.SqueezeBox.close();
	}
});
/**
 * @class Represents a synchronization class.
 */
SB.Synchronization = new Class(
/**
 * @lends SB.Synchronization.prototype
 */
{
	Implements : [SB.Base],
	options : {
		wrapper : null,
		content_block : '.content-block',
		single_block : null,

		error_block : '.error-block',
		ajax_error_msg : null,
		ajax_loader : '.ajax-loader',
		ajax_overlay : '.ajax-overlay'
	},
	/** @constructs */
	initialize : function(options) {
		this.setOptions(options);
	},
	/**
	 * Start process synchronization
	 *
	 * @public
	 * @param {Object} object item from where we take data
	 * @param {Object} params data which will be send
	 * @param {String} callback name of function called after send request
	 */
	startSynchronization : function(object, params, callback) {
		var request_option = {

			'object' : object,
			block : object.getParent(this.options.single_block),
			parent : object.getParent(this.options.wrapper),

			ajax_error_msg : this.options.ajax_error_msg,
			'callback' : callback
		};

		var sbRequest = new SB.Request({
			data : params
		}).setOptions(request_option).send();
	},
	/**
	 * Send request to server for information about progress
	 *
	 * @public
	 * @param {Object} object item from where we take data
	 * @param {Object} params data which will be send
	 * @param {String} callback name of function called after send request
	 */
	checkProgress : function(object, params, callback) {
		var request_option = {

			'object' : object,

			'callback' : callback
		};

		var sbRequest = new SB.Request({
			data : params,

			onRequest : function() {
			},

			onFailure : function() {
			},

			onSuccess : function(data) {
				if(this.options.callback != null) {
					this.options.callback(this.options.object, data);
				}
			}
		}).setOptions(request_option).send();
	},
	/**
	 * Updates the progress bar for synchronization progress
	 *
	 * @private
	 * @param {Object} object item which will be changed
	 * @param {Object} data additional information
	 */
	updateProgressBar : function(object, data) {
		if(data && data.sync_status) {
			var parent_width = object.getParent().getStyle('width');
			parent_width = parseInt(parent_width);

			object.getParent().getElement('.progress-text').set('text', parseInt(data.sync_status * 100) + '%');

			var width = parent_width * data.sync_status;
			object.tween('width', width + 'px');
		}
	},
	/**
	 * Animate display result of synchronization
	 *
	 * @private
	 * @param {Object} object item which will be changed
	 * @param {Object} data additional information
	 */
	showSyncResult : function(object, data) {
		clearTimeout(parent.sync_check_interval);

		var body = object.getParent('body');
		body.setStyle('opacity', '0');
		body.set('html', data.html);
		body.fade('in');
	}
});
/**
 * @class Represents a errors class.
 */
SB.Errors = new Class(
/**
 * @lends SB.Errors.prototype
 */
{
	Implements : [SB.Base],
	options : {
		wrapper : null,

		sync_all_button : '.sync-all-button',
		delete_button : '.delete-button',
		disabled_button : '.disabled',

		no_item_error_msg : null,
		empty_table_msg : null,

		error_block : '.error-block',
		ajax_error_msg : null,
		ajax_loader : '.ajax-loader',
		ajax_overlay : '.ajax-overlay'
	},
	/** @constructs */
	initialize : function(options) {
		this.setOptions(options);

		this.initSyncAllButton();
		this.initDeleteButton();
		this.initRowChecked();
	},
	/**
	 * Add synchronization function to button
	 *
	 * @private
	 */
	initSyncAllButton : function() {
		$$(this.options.sync_all_button).addEvent("click", function() {
			var synchronization = window.parent.document.getElementById("synchronization");
			var modal_link = window.parent.$(synchronization).getElement(".modal");
			modal_link.fireEvent("click", modal_link);
		});
	},
	/**
	 * Add delete function to button
	 *
	 * @private
	 */
	initDeleteButton : function() {
		var _this = this;
		$$(this.options.delete_button).addEvent("click", function() {
			if(this.hasClass(_this.options.disabled_button.substring(1))) {
				return true;
			}
			if($$("input[name=boxchecked]")[0].getProperty("value") == 0) {
				alert(_this.options.no_item_error_msg);
				return true;
			}

			var errors_id = Array();
			var errors = $$("input[type=checkbox]:checked");
			errors.each(function(item, index) {
				if(item.value.length) {
					errors_id.include(item.value);
				}
			});
			_this.deleteErrors(this, errors_id);
		});
	},
	/**
	 * Add check/uncheck checkbox after click on row
	 *
	 * @private
	 */
	initRowChecked : function() {
		document.getElement("tbody").getElements("tr").addEvent("click", function(event) {
			if(event.target.tagName.toLowerCase() == "input") {
				return true;
			}

			var checkbox = this.getElement("input[type=checkbox]");
			if(checkbox != null) {
				if(checkbox.checked) {
					checkbox.setProperty("checked", "");
				} else {
					checkbox.setProperty("checked", "checked");
				}
				Joomla.isChecked(checkbox.checked);
			}
		});
	},
	/**
	 * Delete selected errors from database
	 *
	 * @public
	 * @param {Object} object delete button
	 * @param {Object} errors_id array of errors identifiers
	 */
	deleteErrors : function(object, errors_id) {
		var ajax_param = {

			view : 'errors',
			task : 'remove',
			'errors_id' : errors_id,
			empty_table_msg : this.options.empty_table_msg,

			buttons : {
				sync_all_button : this.options.sync_all_button,
				delete_button : this.options.delete_button,
				disabled_button : this.options.disabled_button
			}
		};
		var request_option = {

			object : object,
			block : object.getParent(this.options.wrapper),
			parent : object.getParent(this.options.wrapper),

			ajax_error_msg : this.options.ajax_error_msg,
			callback : this.removeRows
		};
		var sbRequest = new SB.Request({
			data : ajax_param
		}).setOptions(request_option).send();
	},
	/**
	 * Removes deleted rows from the table
	 *
	 * @public
	 * @param {Object} object item which will be changed
	 * @param {Object} data result data after request
	 * @param {Object} input_data input data to request
	 */
	removeRows : function(object, data, input_data) {
		var rows = input_data.errors_id;

		if(!rows.length) {
			return true;
		}
		var body = $('errors').getElement('tbody');

		if(body) {
			var _this = this;
			rows.each(function(row) {
				var row_object = body.getElement('#row-' + row);

				row_object.get('tween').addEvent('complete', function() {
					if(row_object.getParent('tbody').getElements('tr').length == 1) {
						// Add no items message
						var td = new Element('td', {
							'colspan' : 7,
							'align' : 'center',
							'text' : input_data.empty_table_msg
						});
						var new_row = new Element('tr', {
							'styles' : {
								'opacity' : '0'
							}
						});
						new_row.grab(td);

						new_row.inject(row_object, 'after').fade('in');

						// Hide sync all button and disable delete button
						if(input_data.buttons) {
							if(input_data.buttons.sync_all_button) {
								$('errors').getElement(input_data.buttons.sync_all_button).fade('out');
							}
							if(input_data.buttons.delete_button && input_data.buttons.disabled_button) {
								$('errors').getElement(input_data.buttons.delete_button).removeEvents('click').addClass(input_data.buttons.disabled_button.substring(1));
							}
						}
					}

					row_object.dispose();
				});
				row_object.tween('opacity', 0);
			});
		}
	}
});
/**
 * @class Represents a network class.
 */
SB.Network = new Class(
/**
 * @lends SB.Network.prototype
 */
{
	Implements : [SB.Base],
	options : {
		type : null,
		task : null,
		error : null
	},
	/** @constructs */
	initialize : function(options) {
		this.setOptions(options);

		var _this = this;
		if((this.options.task == 'connect') && (this.options.error == 0)) {
			$(window.opener.document.getElementById(this.options.type + '-disconnect')).removeClass('disabled');

			var on_off_button = window.opener.document.getElementById(this.options.type);
			if((on_off_button != null) && on_off_button.hasClass('off-button')) {
				on_off_button.fireEvent('click');
			}

			var t = setTimeout('window.close()', 2000);
		} else if((this.options.task == 'disconnect') && (this.options.error == 0)) {
			$(window.parent.document.getElementById(this.options.type + '-disconnect')).addClass('disabled');

			var on_off_button = window.parent.document.getElementById(this.options.type);
			if((on_off_button != null) && on_off_button.hasClass('on-button')) {
				on_off_button.fireEvent('click');
			}

			var t = setTimeout(function() {
				_this.closeLightbox();
			}, 2000);
		}
	}
});
/**
 * @class Represents a configuration of social class.
 */
SB.SocialConfig = new Class(
/**
 * @lends SB.Socialprototype
 */
{
	Implements : [SB.Base],
	options : {
		wrapper : null,
		save_button : '.save-button',

		fields : null,

		section : null,
		no_value_error_msg : null,

		ajax_error_msg : null,
		ajax_success_msg : null,

		error_block : '.error-block',
		success_block : '.success-block',

		ajax_loader : '.ajax-loader',
		ajax_overlay : '.ajax-overlay'
	},
	/** @constructs */
	initialize : function(options) {
		this.setOptions(options);

		this.initSaveButton();
	},
	/**
	 * Add save function to buttons
	 *
	 * @private
	 */
	initSaveButton : function() {
		var _this = this;
		document.getElement(this.options.save_button).addEvent('click', function() {
			_this.submitForm();
		});
	},
	/**
	 * Validate data in form and save it
	 *
	 * @public
	 */
	submitForm : function() {
		$$(this.options.error_block).set("text", "").setStyle("display", "none");
		$$("input").removeClass('error');
		var succes = true;
		var _this = this;

		this.options.fields.each(function(field, index) {
			var object = $$("input[name=" + field + "]");
			if((object.length > 0) && (object[0].getProperty("value") == "")) {
				object.addClass('error');
				$$(_this.options.error_block).set("text", _this.options.no_value_error_msg).setStyle("display", "block");
				succes = false;
			}
		});

		if(!succes) {
			return true;
		}

		var cid = {};
		this.options.fields.each(function(field, index) {
			var object = $$("input[name=" + field + "]");

			if(object.length > 0) {
				if((object[0].get('type') == 'radio') || (object[0].get('type') == 'checkbox')) {
					object = $$("input[name=" + field + "]:checked");
				}

				cid[field] = object[0].get("value");
			}
		});

		var ajax_param = {

			view : 'plugin',
			task : 'save',
			plugin : this.options.section,
			'cid' : cid
		};

		var object = document.getElement('.account-settings-wrapper').getElement('input');

		var request_option = {

			object : object,
			block : object.getParent(_this.options.single_block),
			parent : object.getParent(_this.options.wrapper),
			success_block : parent.$('social-wrapper').getElement('.' + this.options.section).getElement(this.options.success_block),

			ajax_error_msg : this.options.ajax_error_msg,
			ajax_success_msg : this.options.ajax_success_msg,

			show_success_msg : window.parent.showSuccessMsg,
			callback : this.closeLightbox
		};

		var sbRequest = new SB.Request({
			'data' : ajax_param
		}).setOptions(request_option).send();
	}
});

/**
 * ProgressBar
 */
SB.Progressbar = new Class({
	Implements : Options,
	/**
	 * options
	 */
	options : {
		progressbar : '/media/lib_cinx/images/common/progressbar/progressbar2.gif',
		color : "#000",
		opacity : '0.7',
		minHeight : 300,
		minWidth : 500,
		left : 'auto',
		top : 'auto'
	},

	element : null,
	mask : null,
	shadow : null,
	image : null,

	/**
	 * Constructor
	 *
	 * @param {Element} Element to be masked
	 * @options {Object} Options of progressbar
	 * @returns {void}
	 */
	initialize : function(element, options) {
		this.element = element;
		this.setOptions(options);
	},

	/**
	 * Sets a progressbar
	 *
	 * @returns void
	 */
	set : function() {
		var mask = this.getMask();
		if(this.element.getStyle('position') !== 'relative') {
			this.element.setStyle('position', 'relative');
		}
		mask.inject(this.element);
	},

	/**
	 * Returns a size of box
	 *
	 * @param {string} A type of size, can be Width, height
	 * @returns {int}
	 */
	getBoxSize : function(type) {
		var result;
		if(type == 'width') {
			result = this.element.offsetWidth;
			if(!result.toInt()) {
				result = this.options.minWidth;
			}
		} else {
			result = this.element.offsetHeight;
			if(!result.toInt()) {
				result = this.options.minHeight;
			}
		}
		return result;
	},

	/**
	 * Returns a position of progressbar
	 *
	 * @param {string} position Can be left, right etc..
	 * @returns {int}
	 */
	getProgressbarPosition : function(position) {
		var result;
		if(position == 'left') {
			if(this.options.left == 'auto' || this.options.left == 'center') {
				var boxWidth = this.getBoxSize('width');
				var imageWidth = this.image.width;
				result = (boxWidth - imageWidth) / 2;
			} else {
				result = this.options.left;
			}
		} else {
			if(this.options.top == 'auto' || this.options.top == 'center') {
				var boxHeight = this.getBoxSize('height');
				var imageHeight = this.image.height;
				result = (boxHeight - imageHeight) / 2;
			} else {
				result = this.options.top;
			}
		}
		return result;
	},

	/**
	 * Returns mask element
	 *
	 * @returns {Element}
	 */
	getMask : function() {
		var mask, shadow, progressbar, maskWidth = this.getBoxSize('width'), maskHeight = this.getBoxSize('height'), marginLeft, marginTop;

		if(!this.mask) {
			mask = new Element('div', {
				styles : {
					position : 'absolute',
					left : '0px',
					top : '0px'
				}
			});

			shadow = new Element('div', {
				styles : {
					display : 'block',
					position : 'absolute',
					'z-index' : '9999',
					width : maskWidth,
					height : maskHeight,
					opacity : this.options.opacity,
					'background-color' : this.options.color
				}
			});

			progressbar = new Element('img', {
				src : this.options.progressbar,
				styles : {
					position : 'absolute',
					display : 'block',
					'z-index' : '99999',
					left : '50%',
					top : '50%'
				}
			});
			progressbar.inject(mask);
			shadow.inject(mask);
			this.mask = mask;
			this.shadow = shadow;
			this.image = progressbar;
		}

		this.shadow.setStyles({
			width : maskWidth,
			height : maskHeight
		});
		this.mask.setStyles({
			width : maskWidth,
			height : maskHeight,
			display : 'block'
		});

		marginLeft = this.image.width / 2;
		marginTop = this.image.height / 2;
		this.image.setStyles({
			'margin-left' : '-' + marginLeft + 'px',
			'margin-top' : '-' + marginTop + 'px'
		});
		return this.mask;
	},

	/**
	 * Removes progressbar
	 *
	 * @returns void
	 */
	remove : function() {
		this.mask.destroy();
	}
});

SB.Selectbox = new Class({
	Implements : [Options, Events],
	options : {
		component : null,
		classes : {
			/**
			 * Wrapper for selectbox
			 */
			wrapper : 'select-wrapper',
			/**
			 * The container with lists of categories and items
			 */
			box : 'selectbox',
			boxWrapper : 'selectbox-wrapper',
			closedFlag : 'closed',
			button : 'select-article',
			buttonWrapper : 'controls-wrapper',
			hiddenFlag : 'hidden',
			itembox : 'articles-wrapper',
			categories : 'categories-wrapper',
			activeFlag : 'active',
			filter : 'filter',
			saveButton : 'save-button',
			infoFlag : 'info',
			emptyFlag : 'empty' 
		},
		table_classes : {
			id : 'id',
			title : 'title',
			idTitle : 'idTitle'
		},
		messages : {
			no_unique_items : ''
		}
	},
	timer : {
		run : false,
		element : null,
		value : null
	},

	/**
	 * Constructor
	 * @returns void
	 */
	initialize : function(options) {
		this.setOptions(options);
		this.initSlider();
		this.initSelect();
		this.initFilter();
		this.initRowChecked();
		this.initSave();
	},

	/**
	 * Initializes slider that is responsible for categories list
	 * @returns void
	 */
	initSlider : function() {
		var _this = this;
		$$('.' + this.options.classes.box).each(function(item) {
			var buttons = item.getParent('.' + _this.options.classes.wrapper).getElements('.' + _this.options.classes.button);
			var slide = new Fx.Slide(item);
			var boxWrapper = item.getParent('.' + _this.options.classes.boxWrapper), closed = _this.options.classes.closedFlag;
			slide.hide();

			buttons.each(function(button) {
				button.addEvent('click', function(event) {
					var hidden = _this.options.classes.hiddenFlag;
					if(boxWrapper.hasClass(closed)) {
						item.removeClass(hidden);
						boxWrapper.removeClass(closed);
					} else {
						boxWrapper.addClass(closed);
					}
					event.stop();
					slide.toggle();
					
					_this.toggleButtonWrapper();
				});
			});
		});

		// SlideOut when user clicks anywhere on html page
		document.addEvent('click', function(event) {
			var element = event.target;
			if(!$(element).getParent('.' + _this.options.classes.box)) {
				_this.hideSlider();
			}
		});
	},
	
	/**
	 * Toggles the block with select articles button
	 * @returns void 
	 */
	toggleButtonWrapper : function( hide ) {
		var _this = this;
		$$('.' + this.options.classes.button).each(function(item) {
			var wrapper = item.getParent('.' + _this.options.classes.buttonWrapper);
			if ( wrapper )
			{
				$$('.' + _this.options.classes.box).each(function(item) {
					var show = item.getParent('.' + _this.options.classes.boxWrapper).hasClass( _this.options.classes.closedFlag );
					if ( show && wrapper.hasClass( _this.options.classes.hiddenFlag ) ) {
						wrapper.tween('opacity', 1).removeClass(_this.options.classes.hiddenFlag);
					}
					else if ( !show && !wrapper.hasClass( _this.options.classes.hiddenFlag ) ) {
						wrapper.tween('opacity', 0).addClass(_this.options.classes.hiddenFlag);
					}
				});
			}
		});
	},

	/**
	 * Initializes selection any category by user
	 * @returns void
	 */
	initSelect : function() {
		var _this = this;
		$$('.' + this.options.classes.categories).each(function(item) {
			item.getElements('td').each(function(td) {
				td.addEvent('click', function() {
					var level, wrapper, classes = td.getProperty('class'), catid = parseInt(td.getProperty('rel'), '10'), 
						reg = /level\-(\d+)/, tbody = td.getParent('tbody'), active = _this.options.classes.activeFlag, activeTr;

					activeTr = tbody.getElement('.' + active);
					if(activeTr) {
						activeTr.removeClass(active);
					}

					td.getParent('tr').addClass(active);

					level = classes.match(reg);
					if(level.length) {
						level = parseInt(level[1], '10');
					}
					wrapper = td.getParent('.' + _this.options.classes.wrapper);
					_this.resetFilter(wrapper);
					_this.showItems(catid, level, wrapper);
				});
			});
		});
	},

	/**
	 * SlideOut the list of categories
	 * @returns void
	 */
	hideSlider : function() {
		var _this = this;
		$$('.' + this.options.classes.box).each(function(item) {
			var slide = new Fx.Slide(item), boxWrapper = item.getParent('.' + _this.options.classes.boxWrapper), 
				closed = _this.options.classes.closedFlag;
			boxWrapper.addClass(closed);
			slide.slideOut();
			_this.reset(item);
			
			_this.toggleButtonWrapper();
		});
	},

	/**
	 * Resets all user's selection to default states
	 * @param Element The selectbox to be reset
	 * @returns void
	 */
	reset : function(box) {
		var wrapper = box.getParent('.' + this.options.classes.wrapper), 
			tbody = wrapper.getElement('.' + this.options.classes.itembox).getElement('tbody'), _this = this, 
			activeTr = box.getElement('.' + this.options.classes.categories).getElement('.' + _this.options.classes.activeFlag);
		this.resetFilter(wrapper);

		tbody.getChildren().each(function(tr) {
			if(!tr.hasClass(_this.options.classes.hiddenFlag) && !tr.hasClass(_this.options.classes.infoFlag)) {
				tr.destroy();
			}
		});

		if(activeTr) {
			activeTr.removeClass(_this.options.classes.activeFlag);
		}
	},

	/**
	 * Resets items table
	 * @param {Element} The selectbox wrapper
	 * @returns void
	 */
	resetItems : function(wrapper) {
		var tbody = wrapper.getElement('.' + this.options.classes.itembox).getElement('tbody'), _this = this;
		tbody.getChildren().each(function(tr) {
			if(!tr.hasClass(_this.options.classes.hiddenFlag)) {
				tr.destroy();
			}
		});
	},

	/**
	 * Shows items of the category
	 * @param int Id of category
	 * @param int Level of category
	 * @param Element The wrapper of selected box
	 * @param {String} Filter by item's title
	 * @returns void
	 */
	showItems : function(catid, level, wrapper, filter) {
		var items = this.getItems(catid, level, filter);
		this.resetItems(wrapper);
		var box = wrapper.getElement('.' + this.options.classes.itembox);
		var tr = box.getElement('.' + this.options.classes.hiddenFlag);
		var newTr, tdId, tdIdTitle, tdTitle, _this = this, tbody = tr.getParent();
		
		if (items.length == 0) {
			box.getElement('.' + _this.options.classes.emptyFlag).clone().removeClass(_this.options.classes.hiddenFlag).inject(tbody);
		}
		
		items.each(function(item) {
			newTr = tr.clone().cloneEvents(tr);
			newTr.removeClass(_this.options.classes.hiddenFlag);
			tdId = newTr.getElement('.' + _this.options.table_classes.id);
			tdIdTitle = newTr.getElement('.' + _this.options.table_classes.idTitle);
			tdTitle = newTr.getElement('.' + _this.options.table_classes.title);
			tdId.set('value', item.id);
			tdTitle.set('text', item.title);
			tdIdTitle.set('text', item.id);
			newTr.inject(tbody);
		});
	},

	/**
	 * Returns the list of items of the category
	 * @param int Id of category
	 * @param int Level of category
	 * @param {String} Filter by title
	 * @returns Array
	 */
	getItems : function(catid, level, filter) {
		var result = [], _this = this;
		filter = typeof filter !== 'undefined' ? filter : '';
		var sbRequest = new SB.Request({
			data : {
				view : 'plugin',
				task : 'getItems',
				plugin : this.options.component,
				'catid' : catid,
				'level' : level,
				'filter' : filter
			},

			onRequest : function() {

			},

			onFailure : function() {

			},

			onSuccess : function(data) {
				result = data;
			},
			async : false
		}).send();

		return result;
	},

	/**
	 * Sends the list of selected ids to a server
	 * @param {Element} The button being clicking
	 * @return {Void}
	 */
	submit : function(button) {
		var ids = [], _this = this;
		button.getParent('.' + this.options.classes.itembox).getElements('.' + this.options.table_classes.id).each(function(item) {
			if(item.checked) {
				ids.push(item.value);
			}
		});
		var uniqueIds = ids.filter(function(item) {
			return !$( 'row-' + item );
		});
		if (!uniqueIds.length)
		{
			window.alert( _this.options.messages.no_unique_items );
		}
		else {
			_this.insertRows(uniqueIds);
		}
	},

	/**
	 * Inserts rows to the table
	 * @param {Array} The list of ids
	 * @return {Void}
	 */
	insertRows : function(ids) {
		var _this = this;
		var sbRequest = new SB.Request({
			data : {
				view : 'plugin',
				task : 'getItemsById',
				plugin : this.options.component,
				ids : ids
			},

			onRequest : function() {
			},
			onFailure : function() {
			},
			onSuccess : function(data) {
				_this.hideSlider();
				var content = new SB.Content({
					initAll : false
				});
				data.each(function(item) {
					content.addRow(item, content);
				});
			},
			async : false
		}).send();
	},

	/**
	 * Filters the list of ids by entered keyword
	 * @param {String} filter
	 */
	filter : function() {
		var wrapper, tr, td, level, classes, catid, reg;

		if(this.timer.element.value != this.timer.value) {
			this.timer.value = this.timer.element.value;
			wrapper = this.timer.element.getParent('.' + this.options.classes.wrapper);
			tr = wrapper.getElement('.' + this.options.classes.categories).getElement('.' + this.options.classes.activeFlag);
			if(tr) {
				td = tr.getElement('td');
				classes = td.getProperty('class');
				catid = parseInt(td.getProperty('rel'), '10');
				reg = /level\-(\d+)/;
				level = classes.match(reg);
				if(level.length) {
					level = parseInt(level[1], '10');
				}

				this.showItems(catid, level, wrapper, this.timer.value);
			}
		}
	},

	/**
	 * Resets filter value
	 * @param {Element} Wrapper
	 * @returns {void}
	 */
	resetFilter : function(wrapper) {
		var filter = wrapper.getElement('.' + this.options.classes.itembox).getElement('.' + this.options.classes.filter);
		filter.value = 'Begin typing here..';
	},

	/**
	 * Starts timer for autocompliete
	 * @param {Object} Input object with filter
	 * @returns {void}
	 */
	startTimer : function(element) {
		var _this = this;
		this.timer.value = element.value;
		this.timer.element = element;
		
		this.timer.run = setInterval(function(){
			_this.filter();
		}, 1500);
	},

	/**
	 * Stops timer for autocomplete
	 * @returns {Void}
	 */
	stopTimer : function() {
		this.timer.run = null;
	},

	/**
	 * Initializes autocomplite filter
	 * @returns {Void}
	 */
	initFilter : function() {
		var _this = this;
		$$('.' + this.options.classes.filter).each(function(element) {

			element.addEvent('focus', function() {
				if(this.value == 'Begin typing here..') {
					this.value = '';
				}
				_this.startTimer(this);
			});
			element.addEvent('blur', function() {
				_this.stopTimer();
				if(!this.value.length) {
					this.value = 'Begin typing here..';
				}
			});
		});
	},
	
	/**
	 * Initializes check/uncheck row after click on it
	 *
	 * @private
	 * @returns {Void}
	 */
	initRowChecked : function() {
		var _this = this;
		$$('.' + this.options.classes.itembox).each(function(item) {
			item.getElement('tbody').getElements('tr').addEvent('click', function(event) {
				if(event.target.tagName.toLowerCase() == 'input') {
					return true;
				}
				var checkbox = $(this).getElement('input[type=checkbox]');
				if(checkbox) {
					if(checkbox.checked) {
						checkbox.setProperty('checked', '');
					} else {
						checkbox.setProperty('checked', 'checked');
					}
					checkbox.fireEvent('click');
				}
			});
		});
	},

	/**
	 * Initializes save buttons
	 * @returns {Void}
	 */
	initSave : function() {
		var _this = this;
		$$('.' + this.options.classes.itembox).each(function(item) {
			item.getElement('.' + _this.options.classes.saveButton).addEvent('click', function() {
				_this.submit(this);
			});
		});
	}
});
