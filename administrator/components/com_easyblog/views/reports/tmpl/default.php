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
EasyBlog.ready(function($) {

	window.deletePost = function( id )
	{
		if( confirm('<?php echo JText::_( 'COM_EASYBLOG_CONFIRM_DELETE_POST' , true );?>' ) )
		{
			// Change the tasks
			$( '#deletePost-' + id ).submit();
			return;
		}
	}

	window.unpublishPost = function( id )
	{
		if( confirm('<?php echo JText::_( 'COM_EASYBLOG_CONFIRM_UNPUBLISH_POST' , true );?>' ) )
		{
			// Change the tasks
			$( '#unpublishPost-' + id ).submit();
			return;
		}
	}

	$.Joomla("submitbutton", function(action){

		$.Joomla("submitform", [action]);
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate( $this->getTheme() ); ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="c" value="reports" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' );?>

</form>

<?php foreach( $this->reports as $report ){ ?>
<form id="deletePost-<?php echo $report->obj_id;?>" method="post">
	<input type="hidden" name="cid[]" value="<?php echo $report->obj_id;?>" />
	<input type="hidden" name="c" value="blogs" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="remove" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<form id="unpublishPost-<?php echo $report->obj_id;?>" method="post">
	<input type="hidden" name="cid[]" value="<?php echo $report->obj_id;?>" />
	<input type="hidden" name="c" value="blogs" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="unpublish" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php } ?>
