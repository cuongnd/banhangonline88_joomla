EasyBlog.module('ratings', function($){

	var module = this;

	EasyBlog
		.require()
		.library(
			'ui/stars'
		)
		.script(
			'legacy'
		)
		.done(function(){

			/**
			 * Ratings
			 **/
			eblog.ratings = {
				setup: function( elementId , disabled , ratingType ){
					$("#" + elementId ).stars({
						split: 2,
						disabled: disabled,
						oneVoteOnly: true,
						cancelShow: false,
						callback: function( element ){
							eblog.loader.loading( elementId + '-command .rating-text' );
							ejax.load( 'ratings' , 'vote' , element.value() , $( '#' + elementId ).children( 'input:hidden' ).val() , ratingType , elementId );
						}
					});
				},
				showVoters: function( elementId , elementType ){
					ejax.load( 'ratings' , 'showvoters' , elementId , elementType );
				},
				update: function( elementId , ratingType , value , resultCommand ){
					$( '#' + elementId ).children( '.ui-stars-star' ).removeClass( 'ui-stars-star-on' );
					value	= parseInt( value );

					// Hide command
					$( '#' + elementId + '-command' ).hide();

					$( '#' + elementId ).addClass( 'voted' );

					$( '#' + elementId ).children( '.ui-stars-star' ).each( function( index ){
						if( index < value )
						{
							$( this ).addClass( 'ui-stars-star-on' );
						}
						else
						{
							$( this ).removeClass( 'ui-stars-star-on' );
						}
					});
				}
			};

			module.resolve();
		});

});