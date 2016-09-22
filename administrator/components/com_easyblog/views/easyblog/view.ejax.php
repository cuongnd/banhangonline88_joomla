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

require_once( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php' );

class EasyBlogViewEasyblog extends EasyBlogAdminView
{
	public function purgeCache()
	{
		$ajax 	= new Ejax();

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_PURGE_CACHE_DIALOG_TITLE' );

		$content = '';
		$content .= '<p>' . JText::_( 'COM_EASYBLOG_PURGE_CACHE_DIALOG_CONTENT' ) . '</p>';
		$content .= '<form name="reject-post" id="reject-post" action="' . JRoute::_( 'index.php?option=com_easyblog&task=purgeCache' ) . '" method="post">';
		$content .= '<div class="dialog-actions">';
		$content .= JHTML::_( 'form.token' );
		$content .= '	<input type="button" value="' . JText::_( 'COM_EASYBLOG_PENDING_CANCEL_BUTTON' ) . '" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />';
		$content .= '	<input type="submit" value="' . JText::_( 'COM_EASYBLOG_PURGE_CACHE_BUTTON' ) . '" class="button" />';
		$content .= '</div>';

		$options->content	= $content;
		$ajax->dialog( $options );

		return $ajax->send();
	}

	function getVersion()
	{
		$version	= EasyBlogHelper::getLatestVersion();
		$local		= EasyBlogHelper::getLocalVersion();

		// Test build only since build will always be incremented regardless of version
		$localVersion	= explode( '.' , $local );
		$localBuild		= $localVersion[2];

		if( !$version )
			return JText::_('Unable to contact update servers');

		$remoteVersion	= explode( '.' , $version );
		$build			= $remoteVersion[ 2 ];

		$html			= '<span class="version_outdated">' . JText::sprintf( 'COM_EASYBLOG_VERSION_OUTDATED' , $local , JRoute::_( 'index.php?option=com_easyblog&view=updater') ) . '</span>';

		if( $localBuild >= $build )
		{
			$html		= '<span class="version_latest">' . JText::sprintf('COM_EASYBLOG_VERSION_LATEST' , $local ) . '</span>';
		}

		$ajax			= new Ejax();

		if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
		{
			$ajax->script( '$(\'#versionInfo\').append(\'' . $html . '\');' );
		}
		else
		{
			$ajax->script( '$(\'#submenu-box #submenu\').append(\'<li style="float: right; margin:5px 10px 0 0;">' . $html . '</li>\');');	
		}
		
		$ajax->send();
	}

	public function appendPending()
	{
		$db		= EasyBlogHelper::db();

		// Get total reported items
		$query	= 'SELECT COUNT(1) FROM #__easyblog_drafts';
		$query 	.= ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'pending_approval' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$totalPending 	= $db->loadResult();

		$ajax		= new Ejax();

		if( $totalPending > 0 )
		{
			$ajax->script( "$('#submenu li' ).eq( 4 ).find( 'a' ).append( '<b>" . $totalPending . "</b>' );" );
		}
		else
		{
			$ajax->script( '[]' );
		}
		$ajax->send();
	}

	public function getNews()
	{
		$news		= EasyBlogHelper::getRecentNews();
		$content	= '';

		ob_start();
		if( $news )
		{
			foreach( $news as $item )
			{
		?>
		<li>
			<b><span><?php echo $item->title . ' - ' . $item->date; ?></span></b>
			<div><?php echo $item->desc;?></div>
		</li>
		<?php
			}
		}
		else
		{
		?>
		<li><?php echo JText::_('Unable to contact news server');?></li>
		<?php
		}

		$content	= ob_get_contents();
		@ob_end_clean();

		$ajax			= new Ejax();
		$ajax->assign( 'news-items' , $content );
// 		$ajax->script( "$('#news-items").html(\'' . addslashes( $content ) . '\');');
		$ajax->send();
	}
}
