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

require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'views.php' );

class EasyBlogViewEasyblog extends EasyBlogViewParent
{
	function display($tpl = null)
	{
		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		
		$this->assignRef( 'user'		, $user );

		parent::display($tpl);

	}

	function addButton( $link, $image, $text, $description = '' , $newWindow = false , $acl = '' )
	{
		$db 	= EasyBlogHelper::db();
		$count 	= 0;

		if( !empty( $acl ) && EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.' . $acl , 'com_easyblog') )
			{
				return '';
			}
		}
		// Add some notification icons here.
		if( $image == 'reports.png' )
		{
			// Get total reported items
			$query	= 'SELECT COUNT(1) FROM #__easyblog_reports';
			$db->setQuery( $query );
			$count 	= $db->loadResult();
		}

		if( $image == 'pending.png' )
		{
			// Get total reported items
			$query	= 'SELECT COUNT(1) FROM #__easyblog_drafts';
			$query 	.= ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'pending_approval' ) . '=' . $db->Quote( 1 );
			$db->setQuery( $query );
			$count 	= $db->loadResult();
		}

		$target	= "";

		if( $newWindow )
		{
			$target	= ' target="_blank"';
		}
?>
	<li>
		<a href="<?php echo $link;?>"<?php if( $count > 0 ){ ?> class="has-notification"<?php }?><?php echo $target;?>>
			<?php echo JHTML::_('image', 'administrator/components/com_easyblog/assets/images/'.$image, $text );?>
			<span class="item-title">
				<span><?php echo $text;?></span>
				<?php if( $count > 0 ){ ?>
				<b><?php echo $count; ?></b>
				<?php } ?>
			</span>
		</a>
		<div class="item-description">
			<div class="tipsArrow"></div>
			<div class="tipsBody"><?php echo $description;?></div>
		</div>
	</li>
<?php
	}

	function getTotalEntries()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_post';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getTotalComments()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_comment';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getTotalUnpublishedEntries()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_post where `published`=' . $db->Quote( 0 );
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getTotalTags()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_tag';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getTotalCategories()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_category';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getRecentNews()
	{
		return EasyBlogHelper::getRecentNews();
	}

	function registerToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG' ), 'home');

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			JToolBarHelper::preferences('com_easyblog');
		}
	}
}
