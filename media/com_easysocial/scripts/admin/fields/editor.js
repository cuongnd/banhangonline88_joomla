// Controller instance
var $Parent;
var $Browser;
var $Editor;
var $Steps;
var $Config;

// Data registry
var $Apps = {}, $Core = {}, $Check = {}, $Fields = {}, $Pages = {};

// Delete registry
var $Deleted = {
	pages: [],
	fields: []
}

EasySocial.module('admin/fields/editor', function($) {

var module = this;

EasySocial.require()
.library('ui/draggable', 'ui/sortable', 'ui/droppable', 'scrollTo')
.script('shared/fields/base', 'admin/fields/browser', 'admin/fields/steps', 'admin/fields/config')
.done(function($) {

EasySocial.Controller('Fields', {
	defaultOptions: {
		id: 0,
		group: null,

		'{wrap}': '[data-fields-wrap]',
		'{browser}': '[data-fields-browser]',
		'{editor}': '[data-fields-editor]',
		'{steps}': '[data-fields-steps]',
		'{config}': '[data-fields-config]',
		'{saveForm}': '[data-fields-save]'
	}
}, function(self, opts, base) { return {

	init: function() {
		$Parent = self;

		opts.id = self.element.data('id');

		// Get the controller for field browser.
		$Browser = self.addPlugin('browser');

		// Get the controller for field editor.
		$Editor = self.addPlugin('editor');

		// Get the controller for steps.
		$Steps = self.addPlugin('steps');

		var controllers = [$Browser.state, $Editor.state, $Steps.state];

		// Only trigger when all of the states is resolved
		$.when.apply(null, controllers).done(function() {
			$Parent.trigger('controllersReady');
		});

		// Listen to save event on profileForm to perform the save
		$('[data-profiles-fields-form]').on('save', function(ev, task, result) {
			var data = self.save(task);

			result.push(data);
		});
	},

	changed: false,

	customFieldChanged: function() {
		self.changed = true;
	},

	'{window} beforeunload': function(el, ev) {

		// If there are changes, return some error
		if (self.changed) {
			return false;
		}
	},

	'{saveForm} click': function() {
		self.save();
	},

	save: function(task) {

		var dfd = $.Deferred();

		// Disable all input and select within this form to prevent them from getting through POST
		self.element.find('input,select').not(self.saveForm()).prop('disabled', true);

		// Reset saveform value first
		self.saveForm().val('');

		if (task === 'savecopy') {
			self.changed = true;
		}

		if (task === 'saveCategoryCopy') {
			self.changed = true;
		}

		// If no changes, then skip this saving
		if (!self.changed) {
			dfd.resolve();

			return dfd;
		}

		// Trigger saving event
		$Parent.trigger('saving');


		// If config is open, we run a internal populate first on the config
		if ($Config && $Config.state && !$Config.checkConfig()) {

			EasySocial.dialog({
				"content": EasySocial.ajax('admin/views/fields/getSaveError', {"message": "COM_EASYSOCIAL_FIELDS_INVALID_VALUES"})
			});

			dfd.reject();

			return dfd;
		}



		// Clone a non-referenced $Core object into $Check
		$Check = $.extend(true, {}, $Core);

		var data = [];



		// Loop through each step
		$.each($Steps.step(), function(i, step) {
			step = $(step);

			// Get the step's page controller
			var page = $Editor.getPage(step.data('id'));

			// Call the page's export function to get the data of the page
			data.push(page._export());
		});




		// Check if all core apps has been used
		if ($._.keys($Check).length > 0) {
			// Trigger saved event and pass in false to indicate error
			$Parent.trigger('saved', [false]);

			EasySocial.dialog({
				"content": EasySocial.ajax('admin/views/fields/getSaveError', {"message": "COM_EASYSOCIAL_FIELDS_REQUIRE_MANDATORY_FIELDS"})
			});

			dfd.reject();

			return dfd;
		}

		$Parent.changed = false;

		var saveData = {
			data: data,
			deleted: $Deleted
		};

		self.injectSaveData(saveData);

		dfd.resolve();

		return dfd;
	},

	/**
	 * Responsible to inject the data object into the hidden input for POST processing
	 */
	injectSaveData: function(data) {
		data = JSON.stringify(data);

		self.saveForm().val(data);
	},

	/**
	 * Update the form based on the returned data
	 */
	updateResult: function(data) {
		// It has the same format as the data
		$.each(data, function(i, dataStep) {
			// Get the step based on index (sequence)
			var step = $Steps.step().eq(i);

			// Assign step id first
			var stepid = step.data('id');

			// Get the page
			var page = $Editor.getPage(stepid);

			// Update the step id
			$Steps.updateResult(i, dataStep.id);

			// Update the page id
			page.updateResult(stepid, dataStep);
		});
	},

	'{self} doneConfiguring': function() {
		self.element.removeClass('editting');
	},

	loadConfiguration: function(item, type) {
		self.element.addClass('editting');

		var config = self.config().clone();

		$Config = config.addController('EasySocial.Controller.Fields.Config', {
			controller: {
				item: item
			}
		});

		if (type === 'page') {
			item.pageHeader().append(config);
		} else {
			item.element.append(config);
		}

		self.element.trigger('loadingConfig', [type]);
	},

	getConfiguration: function(item, type, callback) {

		self.element.addClass('editting');

		var options = {
			"type": type
		};

		if (type != 'page') {
			$.extend(options, {
				"appId": item.field.appid || item.app.id,
				"fieldId": item.field.id
			});
		}

		if (type == 'page') {
			$.extend(options, {
				"pageid": item.options.pageid
			});
		}

		EasySocial.ajax('admin/views/fields/loadConfiguration', options)
			.done(function(contents, values, params) {

				var html = $(contents);

				// if content loaded previously, we will need to use this loaded copy so that whatever changes made will be loaded. #380
				if (item.field && item.field.html) {
					html.find('[data-fields-config-form]').html(item.field.html);
					values = item.field.params;
					params = item.app.params;
				}

				// Assign the controller to the global namespace
				$Config = html.addController('EasySocial.Controller.Fields.Config', {
					"controller": {
						"item": item
					}
				});

				if (type == 'field') {
					item.element.append(html);
				}

				if (type == 'page') {
					item.pageHeader().append(html);
				}

				callback.apply(item, [html, values, params]);
			});

		// self.element.trigger('loadingConfig', [type]);
	}
}});

EasySocial.Controller('Fields.Editor', {
	defaultOptions: {
		'{editor}': '[data-fields-editor]',
		'{page}': '[data-fields-editor-page]',
		'{items}': '[data-fields-editor-page-items]',
		'{item}': '[data-fields-editor-page-item]'
	}
}, function(self, opts, base) { return {
		state: $.Deferred(),

		init: function() {
			self.ready();
		},

		ready: function() {
			self.state.resolve();
		},

		'{parent} controllersReady': function() {
			// Implements page controller to all pages
			self.page().addController('EasySocial.Controller.Fields.Editor.Page');
		},

		currentPage: function() {
			return self.page('.active').controller();
		},

		addPage: function(uid, callback) {

			// Create a new page item
			EasySocial.ajax('admin/views/fields/getPage', {
				"uid": uid
			}).done(function(contents) {

				var newPage = $(contents);

				// Initialize the page controller
				newPage.addController('EasySocial.Controller.Fields.Editor.Page', {
					"uid": uid,
					"newpage": true,
				});

				// Append the new page
				self.editor().append(newPage);

				self.page().trigger('pageAdded', [newPage, uid]);

				$Parent.customFieldChanged();

				callback.apply();
			});
		},

		getPage: function(uid) {
			return self.page().filterBy('id', uid).controller();
		},

		'{parent} saving': function(el, event) {
			self.element.addClass('saving');
		},

		'{parent} saved': function(el, event, state) {
			self.element.removeClass('saving');
		}
	}
});

EasySocial.Controller('Fields.Editor.Page', {
	defaultOptions: {
		// This is the stepid stored in the db
		pageid: 0,
		uid: 0,
		newpage: false,

		'{items}': '[data-fields-editor-page-items]',
		'{item}': '[data-fields-editor-page-item]',
		'{pageHeader}': '[data-fields-editor-page-header]',

		// $Config compatibility
		'{content}': '[data-fields-editor-page-header]',
		'{fieldItem}': '[data-fields-editor-page-header]',

		'{pageTitle}': '[data-fields-editor-page-title]',
		'{pageDescription}': '[data-fields-editor-page-description]',

		'{inputTitle}': '[data-fields-editor-page-title-input]',
		'{inputDescription}': '[data-fields-editor-page-description-input]',

		// Actions
		'{pageDelete}': '[data-page-delete]',
		'{pageEdit}': '[data-page-edit]',

		'{pageVisibleRegistration}'	: '[data-fields-editor-page-visible-registration]',
		'{pageVisibleEdit}': '[data-fields-editor-page-visible-edit]',
		'{pageVisibleView}': '[data-fields-editor-page-visible-view]',
		'{pageInfo}': '[data-fields-editor-page-info]',
		'{pageInfoDone}': '[data-fields-editor-page-done]'
	}
}, function(self ,opts, base) { return {
	init: function() {

		// Assign uid as pageid if this is not a new page
		if (!self.options.newpage) {
			self.options.uid = self.options.pageid = self.element.data('id');
		}

		// Register self into Pages registry
		self.registerPage();

		self.item().addController('EasySocial.Controller.Fields.Editor.Item', {
			pageid: self.options.uid
		});

		// Check for delete button state
		self.checkPageDeleteButton();

		// Init the sorting
		self.initSort();
	},

	// Keep a registry of current page's fields
	fields: {},

	getStep: function() {
		return $Steps.getStep(self.options.uid);
	},

	registerPage: function() {
		$Pages[self.options.uid] = self;
	},

	initSort: function() {

		self.items().sortable({
			"items": self.item.selector,
			"handle": '[data-fields-editor-page-item-handle]',
			"placeholder": 'ui-state-highlight',
			"cursor": 'move',
			"helper": 'clone',
			"forceHelperSize": true,

			stop: function(event, ui) {

				if (ui.item.is($Browser.item.selector)) {

					var appid = ui.item.data('id');

					// Create a placeholder first
					self.createPlaceholder()
						.done(function(placeholder) {
							var placeholder = $(placeholder);

							ui.item.replaceWith(placeholder);

							self.createNewField(appid, placeholder);
						});
				}

				// Mark change
				$Parent.customFieldChanged();
			}
		});
	},

	addNewField: function(appid) {

		// Append a placeholder first
		self.createPlaceholder()
			.done(function(contents) {
				var placeholder = $(contents);

				self.items().append(placeholder);

				$.scrollTo(placeholder, 200);

				// Create new field and let new field replace the placeholder
				self.createNewField(appid, placeholder);

				$Parent.customFieldChanged();
			});
	},

	createPlaceholder: function() {
		// Generate a uid first
		var uid = $.uid('newfield');

		// Generate a placeholder
		return EasySocial.ajax('admin/views/fields/getPlaceholder', {
					"uid": uid
				});
	},

	createNewField: function(appId, placeholder) {

		// Trigger fieldAdded event
		$Parent.trigger('fieldAdded', [appId]);

		// get the html asyncly
		self.getFieldHtml(appId)
			.done(function(html) {
				// Third parameter set to true to preserve script tags
				html = $.parseHTML(html, document, true);

				// Wrap the whole parsed html as jquery object
				html = $(html);

				// Replace the original loading placeholder with the html object
				placeholder.replaceWith(html);

				// Retrieve the main div to implement item controller
				var div = html.filter('[data-appid="' + appId + '"]');

				// Implement the item controller
				div.addController('EasySocial.Controller.Fields.Editor.Item', {
					controller: {
						page: self
					},

					"appid": appId,
					"pageid": self.options.uid,
					"newfield": true,
				});
			});
	},

	getFieldHtml: function(appId) {
		var state = $.Deferred();

		// Render a sample
		if ($Apps[appId].html === undefined) {

			EasySocial.ajax('admin/views/fields/renderSample', {
				"appid": appId,
				"profileid": $Parent.options.id,
				"group": $Parent.options.group
			}).done(function(html) {
				$Apps[appId].html = html;

				state.resolve(html);
			}).fail(function(msg) {
				state.reject(msg);
			});
		} else {
			state.resolve($Apps[appId].html);
		}

		return state;
	},

	'{pageHeader} click': function(el, event) {
		var clickedTarget = $(event.target);

		if (clickedTarget.not('[data-fields-editor-page-delete]') && !el.hasClass('editting')) {

			if ($Config && $Config.state) {

				var state = $Config.checkConfig();

				// Remove itself from other field
				if (state) {
					$Config.closeConfig();
				} else {
					EasySocial.dialog({
						"content": EasySocial.ajax('admin/views/fields/getSaveError', {"message": "COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_VALUES"})
					});

					return;
				}
			}

			self.loadConfiguration();
		}
	},

	loadConfiguration: function() {

		self.pageHeader().addClass('editting');

		$Parent.getConfiguration(self, 'page', function(html, values, params) {
			self.params = params;
			self.values = values;
			self.html = html;

			// Compatibility with $Config
			self.appParams = params;

			var form = $(self.html);

			$Config.load(form);
		});
	},

	updateHtml: function(html) {
		self.html = html;
	},

	getConfigValues: function() {
		return self.values;
	},

	'{fieldItem} onConfigChange': function(el, ev, name, value) {

		self.values[name] = value;

		var step = $Steps.getStepLink(self.options.uid);

		if (name === 'title') {
			step.text(value);

			self.pageTitle().html(value);
		}

		if (name === 'description') {
			// Used attr('data-original-title') instead of data('original-title') because the tooltip reads the attribute directly while data() adds the value back as a jQuery data on to the element
			step.attr('data-original-title', value);

			self.pageDescription().html(value);
		}

		$Parent.customFieldChanged();
	},

	'{pageDelete} click': function(button, event) {
		event.preventDefault();
		event.stopPropagation();

		// If it is the last page, then it shouldn't delete.
		if ($Editor.page().length == 1) {
			return false;
		}

		// Perform an ajax call to render the delete dialog
		EasySocial.dialog({
			"content": EasySocial.ajax('admin/views/fields/confirmDeletePage'),
			"bindings": {
				"{deleteButton} click": function() {

					// Delete the page
					self.deletePage();

					// Close the dialog
					EasySocial.dialog().close();
				}
			}
		});
	},

	deletePage: function() {

		// Trigger pageDeleted event
		self.item().trigger('pageDeleted');
		$Parent.trigger('pageDeleted', [self.options.uid]);

		// Remove self from $Pages registry
		delete $Pages[self.options.uid];

		// Add self into $DeletedPages registry
		if(!self.options.newpage) {
			$Deleted.pages.push(self.options.uid);
		}

		// Removed current page
		self.element.remove();

		// Check for delete button
		$.each($Editor.page(), function(i, page) {
			$(page).controller().checkPageDeleteButton();
		});

		$Parent.customFieldChanged();
	},

	_export: function() {
		var fields = [];

		$.each(self.item(), function(j, item) {
			var item = $(item).controller();

			if (item !== undefined) {
				fields.push(item._export());
			}
		});

		var data = {
			fields: fields,
			newpage: self.options.newpage,
			id: self.options.uid
		}

		if(self.values !== undefined) {
			var data = $.extend(data, self.values);
		}

		return data;
	},

	updateResult: function(oldid, data) {
		if(self.options.newpage) {

			// Update the page element id attribute (to correspond with the step tab structure)
			self.element.attr('id', 'formStep_' + data.id);

			// Remove the old selector and add in the new selector
			self.element.removeAttr('data-fields-editor-page-' + oldid);
			self.element.attr('data-fields-editor-page-' + data.id, true);

			// Assign pageid to self.options
			self.options.pageid = self.options.uid = data.id;

			// Update the $Pages registry
			$Pages[data.id] = $.extend(true, {}, $Pages[oldid]);
			delete $Pages[oldid];

			// Since the page has been saved, then it should not be a new page anymore
			self.options.newpage = false;
		}

		if(data.fields !== undefined) {
			$.each(data.fields, function(i, field) {
				// Go by sequence
				var item = self.item().eq(i).controller();

				item.updateResult(field);
			});
		}
	},

	/**
	 * Carry out necessary action when a new page is added
	 */
	'{self} pageAdded': function(el, event, page) {
		self.checkPageDeleteButton();

		$Parent.customFieldChanged();
	},

	checkPageDeleteButton: function() {
		if($Editor.page().length > 1) {
			self.pageDelete().show();
		} else {
			self.pageDelete().hide();
		}
	},

	'{parent} loadingConfig': function() {
		self.pageHeader().removeClass('editting');
		self.item().removeClass('editting');
	},

	'{parent} doneConfiguring': function() {
		self.pageHeader().removeClass('editting');
		self.item().removeClass('editting');
	}
}});

EasySocial.Controller('Fields.Editor.Item', {
	defaultOptions: {
		appid: 0,
		fieldid: 0,
		pageid: 0,
		newfield: false,

		'{edit}': '[data-edit]',
		'{deleteButton}': '[data-delete]',
		'{moveButton}': '[data-move]',
		'{content}': '[data-fields-editor-page-item-content]',
		'{fieldItem}': '[data-field]',
		'{config}': '[data-fields-config]',
		'{closeConfig}': '[data-fields-config-close]'
	}
}, function(self, opts, base) { return {
	app: {},

	field: {
		id: 0,
		appid: 0,
		params: {}
	},

	state: $.Deferred(),

	appParams: {},

	init: function() {

		// Check if it has a valid appid or not
		if (opts.appid == 0 && self.element.data('appid') !== undefined) {
			opts.appid = self.element.data('appid');
		}

		// Check if this field's app is a valid app or not
		if ($Apps[opts.appid] !== undefined) {

			// Link the reference copy to self.app from $Apps registry
			self.app = $Apps[opts.appid];
		}

		// Check if it has fieldid or not
		if (opts.fieldid == 0 && self.element.data('id') !== undefined) {
			opts.fieldid = self.element.data('id');
		}

		// Register $Fields
		self.registerFields();

		// Generate a unique id to identify configuration tabs
		self.uniqueid = $.uid(self.app.id + '_');

		self.loadedInit();
	},

	registerFields: function() {
		if(self.options.fieldid != 0) {
			$Fields[self.options.fieldid] = {
				id: self.options.fieldid,
				appid: self.options.appid,
				params: self.field.params || {}
			}

			// Link the reference copy to self.field if this is an existing field
			self.field = $Fields[self.options.fieldid];
		}
	},

	loadedInit: function() {

		// Implement field base controller
		self.element.addController('EasySocial.Controller.Field.Base', {
			mode: 'sample',
			element: self.app.element
		});

		// Implement a common config controller on the item
		self.content().addController('EasySocial.Controller.Fields.Editor.Item.Config');
	},

	// export data during save
	_export: function() {


		// Call checkout function from browser to check if all core apps has been used
		$Browser.checkout(opts.appid);

		// Initialise export data with appid and fieldid
		// If fieldid == 0, means it is a new field
		// If appid == 0, means it is a non valid application
		var exportData 	= {
			"fieldid": opts.fieldid,
			"appid": opts.appid,
			"newfield": opts.newfield
		};

		// Add in parameter values into export data
		exportData = $.extend(exportData, self.expandConfig(self.field.params));

		return exportData;
	},

	'{self} click': function(el, event) {
		var clickedElement = $(event.target);

		// Click on anywhere of the element except the delete button to load the configuration panel
		if (!clickedElement.is(self.deleteButton.selector) && !clickedElement.is(self.moveButton.selector) && !clickedElement.is(self.config.selector) && !clickedElement.is(self.closeConfig.selector) && !el.hasClass('editting')) {

			// If config state is true, means it is editting other field
			if($Config && $Config.state) {

				var state = $Config.checkConfig();

				// Remove itself from other field
				if (state) {
					$Config.closeConfig();
				} else {
					EasySocial.dialog({
						"content": EasySocial.ajax('admin/views/fields/getSaveError', {"message": "COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_VALUES"})
					});

					return;
				}
			}

			self.loadConfiguration();
		}
	},

	// Renders the configuration panel
	loadConfiguration: function() {
		self.element.addClass('editting');

		// Once the contents are returned, we should load the config
		$Parent.getConfiguration(self, 'field', function(html, values, params) {

			self.field.params = values;
			self.app.params = params;
			self.field.html = html;

			// This will keep a flat list of the available parameters
			self.populateAppParams();

			var form = $(self.field.html);
			$Config.load(form);

		});
	},

	updateHtml: function(html) {
		self.field.html = html;
	},

	populateAppParams: function() {

		$.each(self.app.params, function(i, property) {

			$.each(property.fields, function(name, field) {

				if (field.subfields) {
					$.each(field.subfields, function(subname, subfield) {
						self.appParams[name + '_' + subname] = subfield;
					});
				} else {
					self.appParams[name] = field;
				}
			});
		});
	},

	getConfigValues: function() {
		return self.field.params;
	},

	expandConfig: function() {
		var newData = {
			params: {},
			choices: {}
		};

		$.each(self.field.params, function(name, value) {

			var field = self.appParams[name];

			if (!field) {
				return false;
			}

			var type = field.type == 'choices' ? 'choices' : 'params';

			newData[type][name] = value;
		});

		if(self.options.newfield) {
			newData.params.unique_key = '';
		}

		return newData;
	},

	'{moveButton} click': function(button, event) {
		event.preventDefault();
		event.stopPropagation();

		// Get the profile id
		var profileId = $Parent.options.id;
		var group = $Parent.options.group;
		var pages = $Steps.toObject();
		var currentPageId = button.parents($Editor.page.selector).data('id');
		var newPages = [];

		$.each(pages, function(i, page) {
			if(page.uid != currentPageId) {
				newPages.push(page);
			}
		});

		EasySocial.dialog({
			"content": EasySocial.ajax('admin/views/fields/confirmMoveField', {"id": profileId, "group": group}),
			"bindings": {
				"{confirmButton} click": function() {
					var id = this.selection().val();
					var page = $Editor.getPage(id);

					// Append the current item to the new page
					page.items().append(self.element);

					$Parent.customFieldChanged();

					EasySocial.dialog().close();
				}
			}
		});
	},

	'{deleteButton} click': function(button, event) {
		event.preventDefault();
		event.stopPropagation();

		EasySocial.dialog({
			"content": EasySocial.ajax('admin/views/fields/confirmDeleteField'),
			"bindings": {
				"{deleteButton} click": function() {

					// Start deleting the field
					self.deleteField();

					// Close the dialog
					EasySocial.dialog().close();
				}
			}
		});
	},

	deleteField: function() {
		// Trigger fieldDeleted event
		$Parent.trigger('fieldDeleted', [self.options.appid, self.options.fieldid]);

		if(!self.options.newfield) {

			// Delete fields in registry
			delete $Fields[self.options.fieldid];

			// Add this field into the deleted registry
			$Deleted.fields.push(self.options.fieldid);
		}

		// Remove field element
		self.element.remove();

		// There is situation where the browser is hidden
		$Parent.browser().removeClass('t-hidden');

		$Parent.customFieldChanged();
	},

	'{self} pageDeleted': function() {
		self.deleteField();
	},

	'{content} onConfigChange': function(el, event, name, value) {
		self.field.params[name] = value;
	},

	'{self} onPopulateConfig': function(el, event, values) {
		self.field.params = values;
	},

	// Unused
	updateResult: function(data) {
		// Update the unique key
		self.field.params.unique_key = data.unique_key;
		self.itemParam('[data-fields-config-param-field-unique_key]').val(data.unique_key);

		// If this is a new field, the some things need to be updated
		if(self.options.newfield) {
			// Set newfield to false because post-save, this will no longer be a new field
			self.options.newfield = false;

			// Set the fieldid
			self.options.fieldid = data.fieldid;
			self.element.data('id', data.fieldid);

			// Enable the unique key field
			self.itemParam('[data-fields-editor-page-item-param-field-unique_key]').removeAttr('disabled');

			// Register into $Fields registry
			self.registerFields();
		}

		if(data.choices !== undefined) {
			$.each(data.choices, function(name, choices) {
				var element = self.itemParam('[data-fields-config-param-field-' + name + ']');

				$.each(choices, function(i, choice) {
					// Go by sequence
					var item = element.find('li').eq(i);

					if(!item.data('id')) {
						item.attr('data-id', choice.id);
						item.data('id', choice.id);
					}
				});
			});
		}
	}
}});

/* Editor Item Common Controller */
// This is the common item config controller to implement on item
EasySocial.Controller('Fields.Editor.Item.Config', {
	defaultOptions: {
		'{required}': '[data-required]',

		'{title}': '[data-title]',
		'{description}': '[data-description]',

		'{displayTitle}': '[data-display-title]',
		'{displayDescription}': '[data-display-description]'
	}
}, function(self, opts, base) { return {

	'{self} onConfigChange': function(el, event, name, value) {

		if (name == 'display_title') {
			self.displayTitle().toggle(value);
		}

		if (name == 'display_description') {
			self.displayDescription().toggle(value);
		}

		if (name == 'title') {
			self.title().text(value);
		}

		if (name == 'description') {
			self.description().text(value);
		}

		if (name == 'required') {
			self.required().toggle(value);
		}
	}
}});

module.resolve();

});
});
