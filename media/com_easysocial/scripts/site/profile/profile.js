EasySocial.module( 'site/profile/profile' , function($){

	var module 	= this;

	EasySocial.template('info/item', '<li data-profile-apps-item data-layout="custom"><a class="ml-20" href="[%= url %]" title="[%= title %]" data-info-item data-info-index="[%= index %]"><i class="ies-info ies-small mr-5"></i> [%= title %]</a></li>');

	EasySocial.require()
	.script( 'site/profile/header' )
	.library( 'history' )
	.done(function($){

		EasySocial.Controller(
			'Profile',
			{
				defaultOptions:
				{
					// The current user being viewed
					id 	: null,

					// Elements
					"{header}"	: "[data-profile-header]",

					// App item
					"{app}"		: "[data-profile-apps-item]",
					"{action}"	: "[data-profile-apps-menu]",

					// Contents
					"{contents}"	: "[data-profile-real-content]",

					// Sidebar
					"{sidebar}"      : "[data-sidebar]",
					"{sidebarToggle}": "[data-sidebar-toggle]",

					'{info}': '[data-info]',
					'{infoItem}': '[data-info-item]',

					view: {
						infoItem: 'info/item'
					}
				}
			},
			function(self) { return {

					init : function() {

						// Get the user's id.
						self.options.id = self.element.data('id');

						// Implement profile header.
						self.header().implement(EasySocial.Controller.Profile.Header, {
							"{parent}"	: self
						});

						// Set layout on document ready
						$(function(){
							self.setLayout();
						});

						// Set layout on responsive event
						$(".es-responsive").on("responsive", function(){
							self.setLayout();
						});
					},

					setLayout: function() {

						var sidebar = self.sidebar(),
							sidebarToggle = self.sidebarToggle();

						if (sidebarToggle.is(":visible")) {

							var container =
								$('<div class="es-container responsive">')
									.append(sidebar)
									.insertAfter(sidebarToggle);
						} else {
							$(".es-profile .es-container:not(.responsive)").prepend(sidebar);
							$(".es-profile .es-container.responsive").remove();
						}
					},

					"{sidebarToggle} sidebarToggle": function(sidebarToggle) {

						self.setLayout();
					},

					"{app} click" : function( el , event )
					{
						// Remove active class.
						self.app().removeClass( 'active' );

						// Add active class to this current item.
						$( el ).addClass( 'active' );

						// Prevent from bubbling up
						event.preventDefault();

						var data = el.data();

						if(data.layout === 'canvas')
						{
							window.location = data[data.layout + 'Url'];
							return;
						}

						// Since 1.3
						// Added support for custom items
						if (data.layout === 'custom') {
							return;
						}

						History.pushState({state: 1}, data.title, data[data.layout + 'Url'] );

						if(self.sidebarToggle().is(':visible'))
						{
							$.scrollTo(self.contents());
						}

						EasySocial.ajax(data.namespace, {
							id: data.id,
							view: 'profile',
							appId: data.appId
						}, {
							beforeSend: function() {
								self.loading();
							}
						}).done(function(contents) {
							self.updateContent(contents);
						}).fail(function(messageObj) {
							return messageObj;
						});
					},

					updateContent : function( content )
					{
						self.element.removeClass("loading");

						self.contents().html( content );
					},

					loading: function()
					{
						// self.contents().html( self.view.loading({}) );
						self.contents().html("");
						self.element.addClass("loading");
					},

					'{info} click': function(el, ev) {
						ev.preventDefault();

						el.route();

						self.loading();

						var loaded = el.data('loaded');

						if (loaded) {
							self.infoItem().eq(0).trigger('click');

							return;
						}

						EasySocial.ajax('site/controllers/profile/initInfo', {
							id: self.options.id
						}).done(function(steps) {
							el.data('loaded', 1);

							var parent = el.parent('[data-profile-apps-item]');

							// Append all the steps
							$.each(steps.reverse(), function(index, step) {
								if (!step.hide) {
									parent.after(self.view.infoItem({
										url: step.url,
										title: step.title,
										index: step.index
									}));
								}

								if (step.html) {
									self.updateContent(step.html);
									self.contents().find('[data-field]').trigger('onShow');
								}
							});

							var item = self.infoItem().eq(0).parent('[data-profile-apps-item]');

							self.app().removeClass('active');

							item.addClass('active');

							// Have to set the title
							$(document).prop('title', self.infoItem().eq(0).attr('title'));
						});
					},

					'{infoItem} click': function(el, ev) {
						ev.preventDefault();

						el.route();

						self.loading();

						var index = el.data('info-index');

						EasySocial.ajax('site/controllers/profile/getInfo', {
							id: self.options.id,
							index: index
						}).done(function(contents) {
							self.updateContent(contents);

							self.contents().find('[data-field]').trigger('onShow');

							self.app().removeClass('active');

							el.parent('[data-profile-apps-item]').addClass('active');
						}).fail(function(error) {
							self.updateContent(error.message);
						});
					}
				}
			}
		);

		module.resolve();
	});

});
