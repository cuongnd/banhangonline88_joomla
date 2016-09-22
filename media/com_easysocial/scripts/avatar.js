EasySocial.module( 'avatar' , function($){

	var module = this;

	EasySocial.Controller( 'Avatar',
		{
			defaultOptions:
			{
				uid 			: null,
				type 			: null,
				redirectUrl 	: null,
				"{menu}"		: "[data-avatar-menu]",
				"{uploadButton}": "[data-avatar-upload-button]",
				"{selectButton}": "[data-avatar-select-button]",
				"{removeButton}": "[data-avatar-remove-button]"
			}
		},
		function( self )
		{
			return {

				init: function()
				{
				},

				"{uploadButton} click": function()
				{
					EasySocial.dialog(
					{
						content: EasySocial.ajax( 'site/views/avatar/upload' , { 'uid' : self.options.uid , 'type' : self.options.type })
					});
				},

				"{selectButton} click": function()
				{
					EasySocial.photos.selectPhoto(
					{
						uid		: self.options.uid,
						type 	: self.options.type,
						bindings:
						{
							"{self} photoSelected": function(el, event, photos)
							{

								// Photo selection dialog returns an array,
								// so just pick the first one.
								var photo = photos[0];

								// If no photo selected, stop.
								if (!photo)
								{
									return;
								}

								EasySocial.photos.createAvatar( photo.id , { "uid" : self.options.uid , "type" : self.options.type , "redirectUrl" : self.options.redirectUrl } );
							},

							"{cancelButton} click": function() {
								this.parent.close();
							}
						}
					});
				},

	            "{menu} shown.bs.dropdown": function()
	            {
	                 self.element.addClass("show-all");
	            },

	            "{menu} hidden.bs.dropdown": function()
	            {
	                 self.element.removeClass("show-all");
	            },

				"{removeButton} click": function()
				{

				}

			}
		}
	);

	module.resolve();
});
