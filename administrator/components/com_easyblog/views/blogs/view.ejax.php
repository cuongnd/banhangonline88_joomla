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

require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewBlogs extends EasyBlogAdminView
{
	public function changeCategory()
	{
		$ajax 	= new Ejax();

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_MOVE' );

		$filter[] = JHTML::_('select.option', '', '- '. JText::_( 'COM_EASYBLOG_SELECT_CATEGORY' ) .' -' );

 		$model 		= EasyBlogHelper::getModel( 'Categories' , true );
 		$categories = $model->getAllCategories();

 		foreach($categories as $cat)
 		{
 		    $filter[] = JHTML::_('select.option', $cat->id, $cat->title );
 		}

		$action		= EasyBlogHelper::getJoomlaVersion() >= '1.6' ? 'Joomla.submitbutton(\'moveCategory\');' : 'submitbutton(\'moveCategory\')';

		ob_start();
		?>
		<p><?php echo JText::_( 'COM_EASYBLOG_CHANGE_CATEGORY_DIALOG_INFO' );?></p>
		<div style="margin-top:10px;">
			<?php echo JHTML::_('select.genericlist', $filter, 'move_category', 'class="inputbox" size="1"', 'value', 'text', '' );?>
		</div>
		<div class="dialog-actions">
			<input type="button" onclick="ejax.closedlg();" name="edialog-cancel" id="edialog-cancel" class="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>">
			<input type="button" onclick="<?php echo $action;?>" class="button" value="<?php echo JText::_( 'COM_EASYBLOG_MOVE_POSTS_BUTTON' );?>">
		</div>
		<?php
		$options->content 	= ob_get_contents();
		ob_end_clean();
		$ajax->dialog( $options );

		$ajax->send();
	}

	public function confirmAutopost( $type , $id )
	{
		$ajax 	= new Ejax();

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_AUTOPOST_DIALOG_INFO' );

		$action				= EasyBlogHelper::getJoomlaVersion() >= '1.6' ? 'Joomla.submitbutton(\'autopost\');' : 'submitbutton(\'autopost\')';

		ob_start();
		?>
		<p><?php echo JText::sprintf( 'COM_EASYBLOG_AUTOPOST_DIALOG_DESC' , ucfirst( $type ) );?></p>
		<div class="dialog-actions">
			<input type="button" onclick="ejax.closedlg();" name="edialog-cancel" id="edialog-cancel" class="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>">
			<input type="button" class="button" value="<?php echo JText::_( 'COM_EASYBLOG_SHARE_BUTTON' );?>" onclick="<?php echo $action;?>">
		</div>
		<?php
		$options->content 	= ob_get_contents();
		ob_end_clean();
		$ajax->script( '$("#adminForm input[name=autopost_type]").val("' . $type . '");');
		$ajax->script( '$("#adminForm input[name=autopost_selected]").val("' . $id . '");');
		$ajax->dialog( $options );

		$ajax->send();
	}
}
