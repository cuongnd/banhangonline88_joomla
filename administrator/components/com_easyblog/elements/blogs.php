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

class JElementBlogs extends JElement
{
	var	$_name = 'Blogs';

	function fetchElement($name, $value, &$node, $control_name)
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

		$mainframe	= JFactory::getApplication();
		$doc 		= JFactory::getDocument();

		EasyBlogHelper::loadHeaders();

		JHTML::_( 'behavior.modal' );

		$id 		= '';
		if( $value == '0' )
		{
			$value	= JText::_( 'Select an entry' );
		}
		else
		{
			$blog	= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $value );
			$value	= $blog->title;
			$id 	= $blog->id;
		}
		ob_start();
		?>
<script type="text/javascript">
EasyBlog.ready(function($) {

	window.insertBlog = function(id, name) {
		$( '#item_id' ).val( id );
		$( '#item_value' ).val( name );
		$.Joomla("squeezebox").close();
	};

});
</script>
		<div style="float:left;">
			<input type="text" id="item_value" readonly="readonly" value="<?php echo $value; ?>" disabled="disabled" style="background: #ffffff;width: 200px;" />
		</div>
		<div class="button2-left">
			<div class="blank">
				<a rel="{handler: 'iframe', size: {x: 750, y: 475}}" href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=blogs&tmpl=component&browse=1&browsefunction=insertBlog' );?>" title="Select an Article" class="modal"><?php echo JText::_( 'Select' ); ?></a>
			</div>
		</div>
		<input type="hidden" id="item_id" name="<?php echo $control_name;?>[<?php echo $name;?>]" value="<?php echo $id;?>" />
		<?php
		$html	= ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
