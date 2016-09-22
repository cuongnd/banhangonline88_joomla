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

	window.purgeHistory = function(){
		if( !confirm('<?php echo JText::_( 'COM_EASYBLOG_CONFIRM_PURGE_HISTORY' );?>' ) )
		{
			return false;
		}
		$( '#purgeForm' ).submit();
	}

	$.Joomla("submitbutton", function(action){
		purgeHistory();
	});
});
</script>
<form id="purgeForm" method="post">
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="1" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="task" value="purge" />
<input type="hidden" name="c" value="migrators" />
</form>

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

