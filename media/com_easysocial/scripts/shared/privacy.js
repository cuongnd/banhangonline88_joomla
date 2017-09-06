EasySocial.module("shared/privacy", function($){

var module	= this;

EasySocial.require()
.library("textboxlist")
.done(function($) {

	// Implement privacy button upon clicking on the button
	$(document).on('click.es.privacy',  '[data-es-privacy-form]', function() {

		var button = $(this);
		var controller = "EasySocial.Controller.Privacy.Form";

		// If controller is already implemented on the button, just skip implementation
		if (button.hasController(controller)) {
			return;
		}

		// Run the toggle.
		button.addController(controller).toggle();
	});

	EasySocial.Controller('Privacy.Form', {
		defaultOptions: {
			// The anchor button
			"{button}": "[data-privacy-toggle]",

			"{menu}": "[data-privacy-menu]",
			"{item}": "[data-privacy-menu] > [data-item]",

			// Display
			"{icon}": "[data-privacy-toggle] > [data-privacy-icon]",
			"{label}": "[data-privacy-toggle] > [data-label]",
			"{tooltip}": "[data-original-title]",
			"{key}": "[data-privacy-hidden]"
		}
	}, function(self, opts) { return {

		init: function() {

			// Get the save mode
			opts.mode = self.element.data('mode');

			self.instanceId = $.uid();
			self.addPlugin("custom");
		},

		getData: function(item) {
			var data = $._.pick(item.data(), "uid", "type", "value", "pid", "streamid", "pitemid", "userid");

			data.icon = item.data('privacy-icon');
			data.label = item.find('[data-label]').text();

			return data;
		},


		toggle: function() {

			var isActive = self.element.hasClass("active");
			self[(isActive) ? "deactivate" : "activate"]();
		},

		activate: function() {

			self.element.addClass("active");

			self.trigger("activate", [self]);
			$(window).trigger("activatePrivacy", [self]);

			var windowClick = "click.privacy." + self.instanceId;

			$(document).on(windowClick, function(event){

				var clickedTarget = $(event.target);

				// Don't do anything if we're clicking ourself
				if (clickedTarget.parents().andSelf().filter(self.element).length > 0
					|| clickedTarget.parents('[data-textboxlist-autocomplete]').length > 0
					|| clickedTarget.parents('[data-textboxlist-item]').length > 0 )
				{
					return;
				}

				$(document).off(windowClick);
				self.deactivate();
			});
		},

		deactivate: function() {
			// Remove active class
			self.element.removeClass("active");

			self.trigger("deactivateAllPrivacy", [self]);
			$(window).trigger("deactivatePrivacy", [self]);
		},

		save: function(data) {

			// Set privacy value
			self.key().val(data.value);

			// Update the display
			self.icon().attr("class", data.icon);
			self.label().html(data.label);

			// Trigger save event
			self.trigger("privacySave", [data]);

			// If saving is done via ajax, save now.
			if (opts.mode == "ajax") {
				EasySocial.ajax("site/controllers/privacy/update", {
					uid 	: data.uid,
					utype	: data.type,
					value 	: data.value,
					pid 	: data.pid,
					custom 	: data.custom,
					streamid: data.streamid,
					userid	: data.userid,
					pitemid	: data.pitemid
				}).done(function(){
					// Update the tooltip for ajax mode
				});
			}
		},

		"{self} click" : function(el, event) {

			var target = $(event.target);
			var button = self.button();

			// If the area being clicked is within the toggle button, we should display the options
			if (target.parents().andSelf().filter(button).length > 0) {
				self.toggle();
			}
		},

		"{item} click" : function(item) {

			// Retrieve data from this privacy item
			var data = self.getData(item);

			// Trigger privacy changed event
			self.trigger("privacyChange", [data]);

			if (!data.preventSave) {

				self.save(data);
				self.deactivate();
			}
		},

		"{self} privacyChange": function(el, event, data) {

			// Deactivate other privacy item
			self.item().removeClass("active");
			self.item('[data-value=' + data.value + ']').addClass('active');
		},

		"{window} activatePrivacy": function(el, event, instance) {
			if (instance!==self) {
				self.deactivate();
			}
		}
	}});


	EasySocial.Controller("Privacy.Form.Custom", {
		defaultOptions: {
			"{textField}" : "[data-textfield]",
			"{saveButton}": "[data-save-button]",
			"{cancelButton}": "[data-cancel-button]",
			"{customItem}": "[data-item][data-value=custom]",
			"{customKey}": "[data-privacy-custom-hidden]",
			"{notice}": "[data-privacy-custom-notice]"
		}
	}, function(self) { return {

		init: function() {

			self.textField()
				.textboxlist({
					component: 'es',
					unique: true,
					plugin: {
						autocomplete: {
							exclusive: true,
							minLength: 1,
							cache: false,
							query: function(keyword) {
								var users = self.getIds();
								var ajax = EasySocial.ajax("site/views/privacy/getfriends", {
															"q": keyword,
															"exclude": users
											});

								return ajax;
							}
						}
					}
				});

			self.textboxlist = self.textField().controller("TextboxList");
		},

		getIds: function() {

			var items = self.textField().textboxlist("controller").getAddedItems();

			return $.map(items, function(item, idx) {
				return item.id;
			});
		},

		updateIds: function() {

			// lets update the notice message.
			if (!self.notice().hasClass('t-hidden')) {
				self.notice().addClass('t-hidden');
			}

			var ids = self.getIds();
			self.customKey().val(ids.join(","));
		},

		"{parent} privacyChange": function(el, event, data) {

			var isCustomPrivacy = (data.value=="custom");

			self.element.toggleClass("custom-privacy", isCustomPrivacy);

			// If user no longer selects custom privacy
			if (!isCustomPrivacy) {

				// Clear any existing custom privacy
				self.textField()
					.textboxlist("controller")
					.clearItems();
			} else {

				// Prevent privacy from saving
				data.preventSave = true;
			}
		},

		"{parent} privacySave": function(el, event, data) {
			// for now do nothing.
		},

		"{parent} deactivateAllPrivacy": function(el, event) {
			self.textboxlist.autocomplete.hide();
		},

		"{cancelButton} click" : function(){
			self.element.removeClass("custom-privacy");
			self.textboxlist.autocomplete.hide();
		},

		"{saveButton} click" : function(){

			var parent = self.parent;
			var customItem = self.customItem();
			var data = parent.getData(customItem);
			var value = self.customKey().val().trim();

			if (value == "") {
				self.notice().removeClass('t-hidden');
				return false;
			}

			data.custom = value;

			self.parent.save(data);
			self.parent.deactivate();
		},

		// event listener for adding new name
		"{textField} addItem": function() {
			self.updateIds();
		},

		// event listener for removing name
		"{textField} removeItem": function() {
			self.updateIds();
		}
	}});



	module.resolve();
});

});
