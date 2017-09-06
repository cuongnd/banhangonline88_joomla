EasySocial.module('site/dashboard/default' , function($){

	var module = this;

	EasySocial.require()
	.library('history')
	.done(function($){

		EasySocial.Controller('Dashboard', {
			defaultOptions: {
				title: null,

				"{sidebar}": "[data-sidebar]",

				// Wrapper and content
				"{wrapper}": "[data-wrapper]",
				"{contents}": "[data-contents]",

				// Show all clusters
				"{clustersSection}": "[data-section-clusters]",
				"{showAllClusters}": "[data-clusters-showall]",

				// Sections
				'{section}': '[data-section]',
				'{sectionLists}': '[data-section-lists]',
				'{showAllSection}': '[data-section-showall]',

				// Filter item on the side bar
				"{filterItem}": "[data-filter-item]",

				// Applications.
				"{app}": "[data-app]",

				// Custom filters
				"{createFilter}": "[data-create-filter]",

				// Edit custom filters
				"{editFilter}": "[data-edit-filter]",

				// hashtag filter save
				"{saveHashTag}"		: "[data-hashtag-filter-save]"
			}
		}, function(self, opts) { return{

			clicked: false,

			resetFilterCounters: function(filterItem) {
				// Clear the new feed notification counter.
				var counter = filterItem.find('[data-counter]');

				// Update the counter to 0
				counter.html("0");

				// Clear new feed counter
				filterItem.removeClass('has-notice');
			},

			setActiveFilter: function(filterItem) {

				// Set active state
				self.filterItem().removeClass('active');
				self.app().removeClass('active');
				filterItem.addClass('active');

				// Add loading indicator
				filterItem.addClass('is-loading');
			},

			"{showAllClusters} click": function(link, event) {

				var section = link.parents(self.clustersSection.selector);
				var type = section.data('type');

				link.addClass('is-loading');

				EasySocial.ajax('site/views/dashboard/getMoreClusters', {
					"type": type
				}).done(function(contents) {
					section.find('ul').replaceWith(contents)
				}).always(function() {
					link.remove();
				});
			},

			"{editFilter} click": function(editFilter, event) {

				if (self.clicked) {
					return false;
				}

				// Prevent bubbling of event
				event.preventDefault();
				event.stopPropagation();

				self.clicked = true;

				// Update the browser's url
				editFilter.attr('title', opts.title);
				editFilter.route();

				// Get the filter attributes
				var id = editFilter.data('id');
				var type = editFilter.data('type');

				// Notify the dashboard that it's starting to fetch the contents.
				self.updatingContents();

				EasySocial.ajax('site/views/stream/getFilterForm', {
					"id": id,
					"type": type
				}).done(function(contents) {
					self.updateContents(contents);
				}).fail(function(messageObj) {
					return messageObj;
				}).always(function() {
					self.clicked = false;
				});
			},

			"{createFilter} click": function(button, event) {

				if (self.clicked) {
					return;
				}

				// Stop event from propagating
				event.preventDefault();
				event.stopPropagation();

				self.clicked = true;

				// Update the url
				button.attr('title', opts.title);
				button.route();

				// Notify the dashboard that it's starting to fetch the contents.
				self.updatingContents();

				EasySocial.ajax('site/views/stream/getFilterForm', {
					"type": "user"
				}).done(function(contents) {
					self.updateContents(contents);
				}).fail(function(messageObj) {
					return messageObj;
				}).always(function() {
					self.clicked = false;
				});
			},

			"{filterItem} click": function(filterItem, event) {

				// Prevent event from bubbling up
				event.preventDefault();
				event.stopPropagation();

				// Get the attributes of the item
				var type = filterItem.data('type');
				var id = filterItem.data('id');

				// Prevent clicking any items more than once
				if (self.clicked) {
					return;
				}

				self.clicked = true;

				// Route the anchor links embedded
				var anchor = filterItem.find('> .o-tabs__link');
				anchor.attr('title', opts.title);
				anchor.route();

				// Notify the dashboard that it's starting to fetch the contents.
				self.updatingContents();

				// Set the active filter
				self.setActiveFilter(filterItem);

				// Remove empty state
				self.wrapper().removeClass('is-empty');

				EasySocial.ajax( 'site/controllers/dashboard/getStream', {
					"type": type,
					"id": id,
					"view": 'dashboard',
				}).done(function(contents, count) {

					if (count == 0) {
						self.wrapper().addClass('is-empty');
					}

					// Trigger change for the stream
					self.trigger('onStreamUpdate', [type]);

					// // trigger sidebar toggle for responsive view.
					self.trigger('onEasySocialFilterClick');

					window.streamFilter = type;

					// Update the contents of the dashboard area
					self.updateContents(contents);

					// 3PD FIX: Kunena [text] replacement
					try {
						MathJax && MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
					} catch(err) {};

				}).fail(function(message) {
					return message;
				}).always(function() {

					self.clicked = false;
					filterItem.removeClass('is-loading');
				});
			},

			"{app} click" : function(app, event) {

				if (self.clicked) {
					return false;
				}

				// Prevent from bubbling up.
				event.preventDefault();
				event.stopPropagation();

				self.clicked = true;

				// Set the active filter
				self.setActiveFilter(app);

				// Get the layout meta.
				var layout = app.data('layout');
				var url = app.data(layout + '-url');
				var id = app.data('id');

				// If this is a canvas layout, redirect the user to the canvas view.
				if (layout == 'canvas') {
					window.location = url;
					return;
				}

				// Get the anchor and route it
				var anchor = app.find('> a');
				anchor.attr('href', url);
				anchor.attr('title', opts.title);
				anchor.route();

				// Notify the dashboard that it's starting to fetch the contents.
				self.updatingContents();

				// Send a request to the dashboard to update the content from the specific app.
				EasySocial.ajax('site/controllers/dashboard/getAppContents', {
					"appId": id
				}).done(function(html) {
					self.updateContents(html);

				}).fail(function(message) {
					return message;

				}).always(function(){
					self.clicked = false;
					app.removeClass('is-loading');
				});

			},

			addCustomFilter: function(feed) {
				var sectionContent = self.section('[data-type="custom-filters"]').find(self.sectionLists.selector);

				if (!$.trim(sectionContent.html()).length) {
					var emptyList = self.section('[data-type="custom-filters"]').find('[data-filter-empty]');
					emptyList.hide();
				}

				feed.appendTo(sectionContent);
			},

			'{showAllSection} click': function(button, event) {
				// Hide the button
				button.hide();

				button.parents(self.section.selector)
					.find(self.filterItem.selector)
					.removeClass('t-hidden');
			},

			"{showAllFilters} click" : function(el, event) {
				$(el).hide();

				self.appFilters().removeClass('t-hidden');
			},

			updatingContents: function() {

				// When this method is invoked, clear the contents and add a loading indication
				self.contents().empty();
				self.wrapper().addClass('is-loading');
			},

			updateContents: function(contents) {
				self.wrapper().removeClass("is-loading");

				$('body').trigger('beforeUpdatingContents');

				// Hide the content first.
				$.buildHTML(contents)
					.appendTo(self.contents());

				$('body').trigger('afterUpdatingContents');
			},

			"{saveHashTag} click": function(el) {
				var hashtag = el.data('tag');

				EasySocial.dialog({
					content: EasySocial.ajax('site/views/stream/confirmSaveFilter', { "tag": hashtag } ),
					bindings: {

						"{saveButton} click" : function() {
							this.inputWarning().hide();

							filterName = this.inputTitle().val();

							if (filterName == '') {
								this.inputWarning().show();
								return;
							}

							EasySocial.ajax( 'site/controllers/stream/addFilter', {
								"title": filterName,
								"tag": hashtag,
							}).done(function(html, msg) {

								var item = $.buildHTML(html);

								// Add a new custom filter to the sidebar
								self.addCustomFilter(item);

								// show message
								EasySocial.dialog(msg);

								// auto close the dialog
								setTimeout(function() {
									EasySocial.dialog().close();
								}, 2000);

							});
						}
					}
				});
			}
		}});

		module.resolve();
	});

});
