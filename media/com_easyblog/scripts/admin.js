EasyBlog.module("admin", function($){

	var module = this;

	var admin = window.admin = {
		blog: {
			reject: function( blogId ) {
				ejax.load( 'Pending' , 'confirmRejectBlog' , blogId );
			    return;
			},
			approve: function(blogId , msg ) {
				if ( confirm( msg ) ) {
			    	window.location = eblog_site + '&c=pending&task=approve&cid[]=' + blogId;
			    }
			    return;
			}
		},
		settings: {
			importSettings: function()
			{
				ejax.load( 'settings' , 'import' );
			}
		},
		checkbox: {
			init: function(){
				// Transform checkboxes.
				$( '.option-enable' ).click( function(){
					var parent = $(this).parent();
					$( '.option-disable' , parent ).removeClass( 'selected' );
					$( this ).addClass( 'selected' );
					$( '.radiobox' , parent ).attr( 'value' , 1 );
				});

				$( '.option-disable' ).click( function(){
					var parent = $(this).parent();
					$( '.option-enable' , parent ).removeClass( 'selected' );
					$( this ).addClass( 'selected' );
					$( '.radiobox' , parent ).attr( 'value' , 0 );
				});
			}
		},
		pending:
		{
			reject: function()
			{

			}
		},
		spools: {
			preview: function( id ){
				ejax.load( 'Spools' , 'preview' , id );
			}
		},
		teamblog: {
		    markAdmin : function(teamid, userid , token ) {
	            window.location = eblog_site + '&c=teamblogs&task=markAdmin&teamid=' + teamid + '&userid=' + userid + '&' + token + '=1';
			},
		    removeAdmin : function(teamid, userid) {
				window.location = eblog_site + '&c=teamblogs&task=removeAdmin&teamid=' + teamid + '&userid=' + userid + '&' + token + '=1';
			}
		}
	}


	$(function(){
		
		var className	= $( '#submenu' ).attr( 'class' );

		if( $('#submenu li').eq( 4 ).length > 0 && className != 'settings' )
		{
			ejax.load( 'Easyblog' , 'appendPending' );
		}

		// move system message
		// TODO: This should be disabled in Joomla 3.0
		if ( $('#system-message').length > 0 && $('.eb-bootstrap').length == 0 )
		{
			var message = $('#system-message').html();

			$('#system-message').remove();

			$( '<dl id="system-message">' + message + '</dl>' ).insertAfter('#toolbar-box');
		}

		$('body #settingsForm .admintable tr:odd').addClass('tr-odd');

		$('.admintable tr').hover( function(){
			$(this).addClass('tr-hover');
		},
		function() {
			$(this).removeClass('tr-hover');
		});

		admin.checkbox.init();
	});

	module.resolve();

});