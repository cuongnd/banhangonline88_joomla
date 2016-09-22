EasySocial.module( 'admin/sidebar/sidebar' , function($) {

	var module = this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller(
				'Sidebar.Sidebar',
				{
					defaultOptions:
					{
						intervalPendingUsers 	: 5000,

						"{versionNotice}"		: "[data-easysocial-version]",
						"{usersBadge}"			: ".menu-user > a .badge",
						"{pendingUsersBadge}"	: ".menu-user .menu-ies-vcard > .badge",


					}
				},
				function( self )
				{
					return {

						init: function()
						{
							// Perform version checking
							self.versionChecks();

							// Check for pending users.
							self.checkPendingUsers();
						},

						versionChecks: function()
						{
							EasySocial.ajax( 'admin/controllers/easysocial/versionChecks' )
							.done(function( contents , outdated , local , latest )
							{
								if( outdated )
								{
									// Show sidebar menu to be outdated
									$( '[data-es-version-header]' )
										.removeClass( 'latest' )
										.addClass( 'outdated' );

									$( '[data-es-version-header]' )
										.find( '[data-es-outdated]' )
										.data( 'local-version' , local )
										.data( 'online-version' , latest );
								}

								self.versionNotice().html( contents ).show();
							});
						},

						monitorPendingUsers: function()
						{
							// Debug
							if( EasySocial.debug )
							{
								var seconds 	= self.options.intervalVersionChecks / 100;

								console.info( 'Start monitoring pending users with interval of ' + self.options.intervalPendingUsers + ' seconds.' );
							}

							self.options.state	= setTimeout( self.checkPendingUsers , self.options.intervalPendingUsers );
						},

						checkPendingUsers: function()
						{
							// Stop monitoring so that there wont be double calls at once.
							self.stopMonitorPendingUsers();

							// Needs to run in a loop since we need to keep checking for new notification items.
							setTimeout( function(){

								EasySocial.ajax('admin/controllers/users/getTotalPending')
								.done( function( total )
								{
									if( total > 0 )
									{
										self.usersBadge().html( total );
										self.pendingUsersBadge().html( total );
									}
									else
									{
										self.usersBadge().html( '' );
									}
									// Continue monitoring.
									self.monitorPendingUsers();
								});

							}, self.options.intervalPendingUsers );

						},
						stopMonitorPendingUsers: function()
						{
							// Debug
							if( EasySocial.debug )
							{
								// console.info( 'Stop monitoring conversation notifications.' );
							}

							clearTimeout( self.options.state );
						},
					}
				}
		);

		module.resolve();
	});

});
