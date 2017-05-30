EasySocial.ready(function($)
{
	$.Joomla( 'submitbutton' , function(task)
	{
		if( task == 'cancel' )
		{
			window.location	= 'index.php?option=com_easysocial&view=groups&layout=categories';

			return false;
		}

		<?php if( $category->id ) { ?>
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

		if( task == 'applyCategory' || task == 'saveCategory' || task == 'saveCategoryNew' )
		{
			performSave(<?php echo $category->id; ?>);

			return false;
		}

		<?php } ?>

		$.Joomla( 'submitform' , [task] );
	});

	$( '[data-category-avatar-remove-button]' ).on( 'click' , function()
	{
		var id 		= $( this ).data( 'id' ),
			button	= $( this );

		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/groups/confirmRemoveCategoryAvatar' , { "id" : id }),
			bindings 	:
			{
				"{deleteButton} click" : function()
				{
					EasySocial.ajax( 'admin/controllers/groups/removeCategoryAvatar' ,
					{ 
						"id" : id
					})
					.done(function()
					{
						$( '[data-category-avatar-image]' ).remove();

						button.remove();

						EasySocial.dialog().close();
					});
				}
			}
		});
	});
});
