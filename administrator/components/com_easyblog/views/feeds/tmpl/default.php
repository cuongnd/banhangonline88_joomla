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

	window.selectedItem = function() {

		var inputs  = [];

		if( $( 'input:checked[name="cid[]"]' ).length > 0 )
		{
			$( 'input:checked[name="cid[]"]' ).each( function(){
				inputs.push( this.name + '=' + this.value );
			});
		}

		return inputs;
	}

	$.Joomla("submitbutton", function(action) {

	    if( action == 'download')
	    {
			cid = selectedItem();

			if(cid.length > 0)
			{
				$('#progress-bar').show();
				$("#bar-progress").css("width" , "1%");
				$('#feeds-msg').html("<?php echo JText::_('COM_EASYBLOG_FEEDS_MIGRATE_NOTES'); ?>");
				$('#feeds-msg').show();
				ejax.load('Feeds', 'download', cid);
			}

			return false;
	    }

		$.Joomla("submitform", [action]);
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="view" value="feeds" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="feeds" />
</form>
