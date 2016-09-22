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

	var checkOptions = function(type) {

		if (type=='enable') {

			$('#theme-params .option-enable').trigger( 'click' );
		} else {

			$('#theme-params .option-disable').trigger( 'click' );
		}
	}

	$.Joomla("submitbutton", function(action){
		$.Joomla("submitform", [action]);
	});
});
</script>




<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>


<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="themes" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="element" value="<?php echo $this->theme->element; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
