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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );

class EasyBlogTablePostReject extends EasyBlogTable
{
	var $id 			= null;
	var $draft_id		= null;
	var $message		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_post_rejected' , 'id' , $db );
	}

	public function clear( $draft_id )
	{
		// Delete any existing rejected messages
		$db		= EasyBlogHelper::db();
		$query	= 'DELETE FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post_rejected' ) . ' WHERE '
				. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'draft_id' ) . '=' . $db->Quote( $draft_id );

		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	public function store($updateNulls = false)
	{
		// @task: Load language file from the front end.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$this->clear( $this->draft_id );

		// @rule: Send notification to the author of the post.
		$draft 		= EasyBlogHelper::getTable( 'Draft' );
		$draft->load( $this->draft_id );

		$author		= EasyBlogHelper::getTable( 'Profile' );
		$author->load( $draft->created_by );

		$data[ 'blogTitle']				= $draft->title;
		$data[ 'blogAuthor']			= $author->getName();
		$data[ 'blogAuthorAvatar' ]		= $author->getAvatar();
		$data[ 'blogEditLink' ]			= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=dashboard&layout=write&draft_id='. $draft->id, false, true);
		$data[ 'blogAuthorEmail' ]		= $author->user->email;
		$data[ 'rejectMessage' ]		= $this->message;

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		if( JFactory::getApplication()->isAdmin() && $sh404exists )
		{
			$data[ 'blogEditLink' ]			= JURI::root() . 'index.php?option=com_easyblog&view=dashboard&layout=write&draft_id='. $draft->id;
		}

		$emailTitle 	= JText::_( 'COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_REJECTED' );

		$obj 				= new StdClass();
		$obj->unsubscribe	= false;
		$obj->email 		= $author->user->email;;

		$emails 			= array( $obj );

		$notification	= EasyBlogHelper::getHelper( 'Notification' );
		$notification->send( $emails , $emailTitle , 'email.blog.rejected' , $data );
		return parent::store();
	}
}
