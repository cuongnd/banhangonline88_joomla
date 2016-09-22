<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog.ready(function($){

	$.Joomla("submitbutton", function(task) {

		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});

		$('dl#subtabs').children().each(function(){
			if( $(this).hasClass( 'open' ) )
			{
				$( '#activechild' ).val( $(this).attr('class').split(" ")[0] );
			}
		});

		if( task == 'export' )
		{
			window.location.href 	= '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=settings&format=raw&layout=export&tmpl=component';
			return;
		}

		if( task == 'import' )
		{
			admin.settings.importSettings();
			return;
		}

		$.Joomla("submitform", [task]);
	});

	window.switchFBPosition = function()
	{
		if( $('#main_facebook_like_position').val() == '1' )
		{
		    $('#fb-likes-standard').hide();
		    if( $('#standard').attr('checked') == true)
		    	$('#button_count').attr('checked', true);
		}
		else
		{
		    $('#fb-likes-standard').show();
		}
	}

	window.insertMailboxDefaultUserId = function( id , name )
	{
		$('#main_remotepublishing_mailbox_userid').val(id);
		$('#remotePublishName').html(name);
		$.Joomla("squeezebox").close();

		$('input:text').each( function() {
			$(this).addClass('inputbox');
		});
	}

	window.testMailboxConnection = function()
	{
		var server	= $('#main_remotepublishing_mailbox_remotesystemname').val();
		var port	= $('#main_remotepublishing_mailbox_port').val();
		var service	= $('#main_remotepublishing_mailbox_service').val();
		var ssl		= $('#main_remotepublishing_mailbox_ssl').val();
		var mailbox	= $('#main_remotepublishing_mailbox_mailboxname').val();
		var user	= $('#main_remotepublishing_mailbox_username').val();
		var pass	= $('#main_remotepublishing_mailbox_password').val();

		$('#remote_test_result').html('<img src="<?php echo JURI::root(); ?>components/com_easyblog/assets/images/loader.gif" />');

		var result	= ejax.call('settings', 'testMailboxConnection', [server, port, service, ssl, mailbox, user, pass], {
			success: function()
			{

			},
			error: function()
			{

			}
		});
	}

	$( '#truncateType' ).bind( 'change' , function(){
		if( $( this ).val() == 'chars' || $( this ).val() == 'words' )
		{
			$( '#maxchars' ).show();
			$( '#maxtag' ).hide();
		}
		else
		{
			$( '#maxtag' ).show();
			$( '#maxchars' ).hide();
		}
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="active" id="active" value="" />
<input type="hidden" name="activechild" id="activechild" value="" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="settings" />
</form>
