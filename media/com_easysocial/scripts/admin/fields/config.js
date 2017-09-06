EasySocial.module('admin/fields/config', function($) {

var module = this;

EasySocial.Controller('Fields.Config', {
	defaultOptions: {
		'{config}': '[data-fields-config]',
		'{header}': '[data-fields-config-header]',
		'{close}': '[data-fields-config-close]',
		'{form}': '[data-fields-config-form]',
		'{param}': '[data-fields-config-param]',
		'{tabnav}': '[data-fields-config-tab-nav]',
		'{tabcontent}': '[data-fields-config-tab-content]',
		'{done}': '[data-fields-config-done]'
	}
}, function(self, opts, base) { return {
	
	state: false,

	load: function() {

		// If this field is being activated, hide the rest of the configs
		self.config().addClass('t-hidden');
		self.element.removeClass('t-hidden');

		// Set state to true to indicate editting mode
		self.state = true;

		// Apply multi choices
		self.element.find('[data-fields-config-param-choices]').addController('EasySocial.Controller.Config.Choices', {
			"controller": {
				"item": self.item
			}
		});

		// Carry out necessary actions after config has been loaded if this is a new field
		if (self.item.options.newfield) {

			// Disable the unique key field if it is a new field
			self.param('[data-fields-config-param-field-unique_key]')
				.attr('disabled', true);
		}

		// Load the first tab as active
		if (self.tabnav().length > 0) {
			self.tabnav().find('a:first').tab('show');
			self.tabcontent().children(':first').addClass('active');
		}

		// Populate configuration
		self.populateConfig();

		// Get the config height for css fix
		var configHeight = self.element.height();
		$Parent.wrap().css('padding-bottom', configHeight + 'px');

		// Show the close button now
		self.close().show();
	},

	'{close} click': function(el, ev) {
		self.closeConfig();
	},

	'{done} click': function(el, ev) {
		self.closeConfig();
	},

	closeConfig: function() {
		var values = self.populateConfig();

		// Check through the values
		var state = self.checkConfig(values);

		if (state) {
			self.item.updateHtml(self.form().html());

			self.item.content().trigger('onConfigSave', [values]);

			self.element.remove();

			$Config = null;

			$Parent.trigger('doneConfiguring');
		} else {
			EasySocial.dialog({
				"content": EasySocial.ajax('site/views/fields/getSaveError', {"message": "COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_VALUES"})
			});
		}
	},

	'{parent} configLoaded': function(el, ev) {
		self.close().show();
	},

	'{param} change': function(el) {
		self.paramChanged(el);
	},

	'{param} keyup': function(el) {
		self.paramChanged(el);
	},

	paramChanged: function(element) {
		var name = element.data('name');
		var value = self.getConfigValue(name);
		var field = self.item.appParams[name];

		// Manually convert boolean field into boolean value for toggle to work properly
		if (field.type === 'boolean') {
			value = element.is(':checked') ? "1" : "0";
		}		

		self.item.fieldItem().trigger('onConfigChange', [name, value]);
		

		$Parent.customFieldChanged();
	},

	getConfigValue: function(name) {

		var field = self.item.appParams[name];
		var element = self.param().filterBy('name', name);

		if (element.length === 0) {
			return undefined;
		}

		var values = '';

		switch (field.type) {
			
			case 'choices':
				values = [];

				$.each(element.find('li'), function(i, choice) {
					choice = $(choice);

					var titleField = choice.find('[data-fields-config-param-choice-title]'),
						valueField = choice.find('[data-fields-config-param-choice-value]'),
						defaultField = choice.find('[data-fields-config-param-choice-default]');

					values.push({
						'id': choice.data('id'),
						'title': titleField.val(),
						'value': valueField.val(),
						'default': defaultField.val()
					});

					titleField.attr('value', titleField.val());
					valueField.attr('value', valueField.val());
					defaultField.attr('value', defaultField.val());
				});
			break;

			case 'boolean':
				values = element.is(':checked') ? 1 : 0;

				element.attr('value', values);
			break;

			case 'checkbox':
				values = [];
				$.each(field.option, function(k, option) {
					var checkbox = element.filter('[data-fields-config-param-option-' + option.value + ']');

					if(checkbox.length > 0 && checkbox.is(':checked')) {
						values.push(option.value);

						checkbox.attr('checked', 'checked');
					} else {
						checkbox.removeAttr('checked');
					}
				});
			break;

			case 'list':
			case 'select':
			case 'dropdown':
				values = element.length > 0 ? element.val() : field["default"] || '';

				element.find('option').prop('selected', false);

				element.find('option[value="' + values + '"]').prop('selected', true);
			break;

			case 'input':
		case 'text':
			default:
				values = element.length > 0 ? element.val() : field["default"] || '';

				element.attr('value', values);
			break;
		}

		return values;
	},

	populateConfig: function() {
		var data = {};

		$.each(self.item.appParams, function(name, field) {
			var value = self.getConfigValue(name);

			if(value === undefined) {
				// If getConfigValue returns undefined, means this field is not found, then skip to the next field
				return false;
			}

			data[name] = value;
		});

		self.item.trigger('onPopulateConfig', [data]);

		return data;
	},

	checkConfig: function(values) {
		
		if (values === undefined) {
			values = self.populateConfig();
		}

		// Perform custom checks here
		var state = true;

		$.each(values, function(name, value) {
			var field = self.item.appParams[name];

			switch(field.type) {
				// custom check for choices
				case 'choices':
					// Get all the values first
					var choiceValues = [];

					$.each(value, function(i, choice) {
						if($.isEmpty(choice.value) && !$.isEmpty(choice.title)) {
							choice.value = choice.title.toLowerCase().replace(' ', '');
						}

						if(!$.isEmpty(choice.value) && $.inArray(choice.value, choiceValues) > -1) {
							state = false;
							return false;
						}

						choiceValues.push(choice.value);

						// if((!$.isEmpty(choice.title) && $.isEmpty(choice.value)) || ($.isEmpty(choice.title) && !$.isEmpty(choice.value))) {
						// 	state = false;
						// 	return false;
						// }
					});
				break;
			}

			if(state === false) {
				return false;
			}
		});

		return state;
	},

	'{parent} fieldDeleted': function() {
		if(self.state) {
			$Parent.trigger('doneConfiguring');
		}
	},

	'{parent} pageDeleted': function() {
		if(self.state) {
			$Parent.trigger('doneConfiguring');
		}
	},

	'{parent} pageAdded': function() {
		if(self.state) {
			$Parent.trigger('doneConfiguring');
		}
	},

	'{parent} pageChanged': function() {
		if(self.state) {
			$Parent.trigger('doneConfiguring');
		}
	}
}});

EasySocial.Controller('Config.Choices', {
	defaultOptions: {
		'{choiceItems}'	: '[data-fields-config-param-choice]',

		unique			: 1
	}
}, function(self, opts, base) { return {
	init: function() {
		self.options.unique = self.element.data('unique') !== undefined ? self.element.data('unique') : 1;

		self.choiceItems().implement( EasySocial.Controller.Config.Choices.Choice, {
			controller: {
				'item': self.item,
				'choices': self
			}
		});

		self.initSortable();
	},

	initSortable: function() {
		self.element.sortable({
			items: self.choiceItems.selector,
			placeholder: 'ui-state-highlight',
			cursor: 'move',
			forceHelperSize: true,
			handle: '[data-fields-config-param-choice-drag]',
			stop: function() {
				// Manually remove all the freezing tooltip due to conflict between bootstrap tooltip and jquery sortable
				$('.tooltip-es').remove();

				// Mark change
				$Parent.customFieldChanged();
			}
		});
	}
}});

/* Config Choices Choice Controller */
EasySocial.Controller( 'Config.Choices.Choice', {
	defaultOptions: {
		'{choiceValue}'		: '[data-fields-config-param-choice-value]',
		'{choiceTitle}'		: '[data-fields-config-param-choice-title]',
		'{choiceDefault}'	: '[data-fields-config-param-choice-default]',
		'{addChoice}'		: '[data-fields-config-param-choice-add]',
		'{removeChoice}'	: '[data-fields-config-param-choice-remove]',
		'{setDefault}'		: '[data-fields-config-param-choice-setdefault]',

		'{defaultIcon}'		: '[data-fields-config-param-choice-defaulticon]'
	}
}, function(self, opts, base) { return {

	init: function() {
	},

	'{choiceTitle} keyup': $._.debounce(function(el, event) {
		var index = self.element.index();

		self.item.fieldItem().trigger('onChoiceTitleChanged', [index, el.val()]);

		$Parent.customFieldChanged();
	}, 500),

	'{choiceValue} keyup': $._.debounce(function(el, event) {
		var index = self.element.index();

		self.item.fieldItem().trigger('onChoiceValueChanged', [index, el.val()]);

		$Parent.customFieldChanged();
	}, 500),

	'{addChoice} click' : function() {
		// Clone a new item from current clicked element
		var newItem = self.element.clone();

		// Let's leave the value blank by default.
		var inputElement = newItem.find('input[type="text"]');

		inputElement.attr('value', '');

		inputElement.val('');

		// Set the default as 0 and the icon to unfeatured
		var inputDefault = newItem.find('input[type="hidden"]');

		inputDefault.attr('value', 0);

		inputDefault.val(0);

		// set id = 0
		newItem.attr('data-id', 0);
		newItem.data('id', 0);

		// Implement the controller for this choice
		newItem.implement(EasySocial.Controller.Config.Choices.Choice, {
			controller: {
				'item': self.item,
				'choices': self.choices
			}
		});

		// Append this item
		self.element.after(newItem);

		// Get the index of the new item
		var index = newItem.index();

		self.item.fieldItem().trigger('onChoiceAdded', [index]);

		$Parent.customFieldChanged();
	},

	'{removeChoice} click' : function() {
		// We need to minus one because we're trying to remove ourself also.
		var remaining = self.choices.choiceItems().length - 1;

		// If this is the last item, we wouldn't want to allow the last item to be removed.
		if( remaining >= 1 ) {
			// Get the index of the new item
			var index = self.element.index();

			self.item.fieldItem().trigger('onChoiceRemoved', [index]);

			self.element.remove();

			// Manually remove the tooltip generated on the remove button
			$('.tooltip-es').remove();
		}

		$Parent.customFieldChanged();
	},

	'{setDefault} click': function() {
		var index = self.element.index(),
			title = self.choiceTitle().val(),
			value = self.choiceValue().val();

		self.choices.choiceItems().trigger( 'toggleDefault', [index] );

		self.item.fieldItem().trigger('onChoiceToggleDefault', [index, parseInt(self.choiceDefault().val())]);

		$Parent.customFieldChanged();
	},

	'{self} toggleDefault': function(el, ev, i) {
		var index = self.element.index(),
			value = parseInt(self.choiceDefault().val());

		if(index === i) {
			if(value) {
				self.defaultIcon()
					.removeClass('es-state-featured')
					.addClass('es-state-default');

				self.choiceDefault().val(0);
			} else {
				self.defaultIcon()
					.removeClass('es-state-default')
					.addClass('es-state-featured');

				self.choiceDefault().val(1);
			}
		} else {
			if(self.choices.options.unique) {
				self.defaultIcon()
					.removeClass('es-state-featured')
					.addClass('es-state-default');

				self.choiceDefault().val(0);
			}
		}
	}
}});

module.resolve();
});
