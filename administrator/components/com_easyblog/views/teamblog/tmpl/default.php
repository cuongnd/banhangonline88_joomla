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

	window.insertMember = function( id , name )
	{
		var elementId	= 'member-' + id;

		if( $('#' + elementId).html() == null )
		{
			$('#members-container').append('<span id="' + elementId + '" class="members-item"><a class="remove_item" href="javascript:void(0);" onclick="removeMember(\'' + elementId + '\');">X</a><input type="hidden" name="members[]" value="' + id + '" /><span class="normal-member">' + name + '</span></span>');
			$.Joomla("squeezebox").close();
		}
		else
		{
			alert('User is already added');
		}
	}

	window.insertGroup = function( id , name )
	{
		var elementId	= 'member-' + id;

		if( $('#' + elementId).html() == null )
		{
			$('#groups-container').append('<span id="' + elementId + '" class="group-item"><a class="remove_item" href="javascript:void(0);" onclick="removeGroup(\'' + elementId + '\');">X</a><input type="hidden" name="groups[]" value="' + id + '" /><span class="normal-member">' + name + '</span></span>');
			$.Joomla("squeezebox").close();
		}
		else
		{
			alert('User is already added');
		}
	}

	window.removeGroup = function( elementId, groupId )
	{
		$('#'+elementId).remove();

		if($('#deletegroups').val() == '')
		{
		    $('#deletegroups').val( groupId );
		}
		else
		{
			var groups = $('#deletegroups').val();
			$('#deletegroups').val( groups  + ',' + groupId );
		}
	}

	window.removeMember = function( elementId, userId )
	{
		$('#'+elementId).remove();

		if($('#deletemembers').val() == '')
		{
		    $('#deletemembers').val(userId);
		}
		else
		{
			var members = $('#deletemembers').val();
			$('#deletemembers').val(members + ',' + userId);
		}
	}

	window.submitbutton = function( action )
	{
		if ( typeof( tinyMCE ) == 'object' ) {
			if ( $('#write_description').is(":visible") ) {
				tinyMCE.execCommand('mceToggleEditor', false, 'write_description');
			}
		}

		submitform( action );
	}

});

</script>
<form name="adminForm" id="adminForm" action="index.php?option=com_easyblog" method="post" enctype="multipart/form-data">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="teamblogs" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->team->id;?>" />
<input type="hidden" name="deletemembers" id="deletemembers" value="" />
<input type="hidden" name="deletegroups" id="deletegroups" value="" />
</form>
