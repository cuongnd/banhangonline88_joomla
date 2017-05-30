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

	$.Joomla("submitbutton", function( action ) {

	    if (action=='purge') {

			if (!confirm('<?php echo JText::_( 'COM_EASYBLOG_CONFIRM_PURGE');?>')) {
			return false;
		}
	}

		$.Joomla("submitform", [action]);
	});

});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="view" value="spools" />
<input type="hidden" name="c" value="spools" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>