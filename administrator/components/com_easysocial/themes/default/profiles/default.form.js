EasySocial.require()
.script( 'admin/profiles/profile' )
.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_SAVE_ERROR')
.done(function($){

	$('.profileForm').addController('EasySocial.Controller.Profiles.Profile', {
		id: <?php echo !empty( $profile->id ) ? $profile->id : 0; ?>
	});

	// Add active tab state
	$( '[data-form-tabs]' ).on( 'click' , function(){

		// Check to see if there's any data-tab-active input
		var currentInput 	= $( '[data-tab-active]' );

		if( currentInput )
		{
			var selected 	= $( this ).data( 'item' );

			currentInput.val( selected );
		}

	});

	$.Joomla( 'submitbutton' , function( task )
	{

		<?php if( $profile->id ){ ?>
		var performSave = function(id)
		{
			var result = [];

			// Define all custom saving process here

			// Prepare data to save fields
			result.push($('.profileFieldForm').controller().save(task));

			if(result.length > 0)
			{
				$.when.apply(null, result).done(function() {
					$.Joomla('submitform', [task]);
				});

				return;
			}

			$.Joomla('submitform', [task]);

			return;
		}

		if( task == 'save' || task == 'savenew' || task == 'apply' )
		{
			performSave(<?php echo $profile->id; ?>);

			return false;
		}

		if( task == 'savecopy' )
		{
			// Make ajax call to create copy of profile
			EasySocial.ajax( 'admin/controllers/profiles/createBlankProfile' )
				.done( function( id ) {

					// lets update the form element cid value.
					var input = $('input[name="cid"]');
					input.attr( 'value', id );

					performSave( id );
				});

			return false;
		}
		<?php } ?>

		if( task == 'cancel' )
		{
			window.location.href	= '<?php echo JURI::root();?>administrator/index.php?option=com_easysocial&view=profiles';

			return;
		}

		$.Joomla( 'submitform' , [ task ] );
	});
});
