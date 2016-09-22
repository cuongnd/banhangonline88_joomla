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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldModal_TeamBlogs extends JFormField
{

	protected $type = 'Modal_TeamBlogs';
	
	protected function getInput()
	{
		JHTML::_( 'behavior.modal' );

		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR  . 'helper.php' );

		$app 		= JFactory::getApplication();

		if( !$this->value )
		{
			$value 	= JText::_( 'COM_EASYBLOG_SELECT_A_TEAM' );
		}
		else
		{
			$team 		= EasyBlogHelper::getTable( 'TeamBlog' );
			$team->load( $this->value );
			$value 			= $team->title;
		}

		ob_start();
		?>
		<script type="text/javascript">
		function insertTeam( id , name )
		{
			document.id('<?php echo $this->id;?>_id' ).value 	= id;
			document.id('<?php echo $this->id;?>_name' ).value	= name;
			SqueezeBox.close();
		}
		</script>

		<div style="float:left;">
			<input type="text" id="<?php echo $this->id;?>_name" readonly="readonly" value="<?php echo $value; ?>" disabled="disabled" style="background: #ffffff;width: 200px;" />
		</div>
		<div class="button2-left">
			<div class="blank">
				<a rel="{handler: 'iframe', size: {x: 750, y: 475}}" href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=teamblogs&tmpl=component&browse=1&browsefunction=insertTeam' );?>" title="<?php echo JText::_( 'COM_EASYBLOG_SELECT_A_TEAM'); ?>" class="modal">
					<?php echo JText::_( 'COM_EASYBLOG_SELECT_OR_CHANGE' ); ?>
				</a>
			</div>
		</div>
		<input type="hidden" id="<?php echo $this->id;?>_id" name="<?php echo $this->name;?>" value="<?php echo $this->value;?>" />
		
		<?php
		$output		= ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
