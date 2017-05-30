
EasySocial.module('utilities/alias', function($) {

	var module = this;

	EasySocial.Controller(
		'Utilities.Alias',
		{
			defaultOptions:
			{
				// Should be overriden by the caller.
				"{target}"	: "",
				"{source}"	: ""
			}
		},function( self ){ 
			return {

				init: function()
				{
				},

				convertToPermalink: function( title )
				{
					return title.replace(/\s/g, '-').replace(/[^\w/-]/g, '').toLowerCase();
				},

				"{source} keyup" : function()
				{
					// Update the target when the source is change.
					self.target().val( self.convertToPermalink( self.source().val() ) );
				},

				"{target} keyup" : function()
				{
					self.target().val( self.convertToPermalink( self.target().val() ) );
				}
			}
		});

	module.resolve();
});
