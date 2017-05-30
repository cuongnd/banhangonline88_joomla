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

class EasyBlogTableDraft extends EasyBlogTable
{
	var $id						= null;
	var $entry_id				= null;
	var $created_by				= null;
	var $modified				= null;
	var $created				= null;
	var $publish_up				= null;
	var $publish_down			= null;
	var $title					= null;
	var $permalink				= null;
	var $content				= null;
	var $intro					= null;
	var $category_id			= null;
	var $published				= null;
	var $ordering				= null;
	var $vote					= null;
	var $hits					= null;
	var $private				= null;
	var $allowcomment			= null;
	var $subscription			= null;
	var $frontpage				= null;
	var $isnew					= null;
	var $ispending				= null;
	var $issitewide				= null;
	var $tags					= null;
	var $metakey				= null;
	var $metadesc				= null;
	var $trackbacks				= null;
	var $blog_contribute		= null;
	var $autopost				= null;
	var $autopost_centralized	= null;
	var $pending_approval		= null;
	var $address				= null;
	var $latitude				= null;
	var $longitude				= null;
	var $external_source		= null;
	var $external_group_id		= null;
	var $robots					= null;
	var $copyrights				= null;
	var $language				= null;
	var $source					= null;
	var $image					= null;
	var $send_notification_emails = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_drafts' , 'id' , $db );
	}

	function loadByEntry( $id )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_drafts' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'entry_id' ) . '=' . $db->Quote( $id );

		$db->setQuery( $query );
		return parent::bind( $db->loadObject() );
	}

	/**
	 * Must only be bind when using POST data
	 **/
	function bind( $data , $post = false )
	{
		if( !$post )
		{
			return parent::bind( $data );
		}

		parent::bind( $data );
		$acl		= EasyBlogACLHelper::getRuleSet();
		$my			= JFactory::getUser();

		// Some properties needs to be overriden.
		$content	= $this->content;

		//remove unclean editor code.
		$pattern	= array('/<p><br _mce_bogus="1"><\/p>/i',
							'/<p><br mce_bogus="1"><\/p>/i',
							'/<br _mce_bogus="1">/i',
							'/<br mce_bogus="1">/i',
							'/<p><br><\/p>/i');
		$replace	= array('','','','','');
		$content	= preg_replace($pattern, $replace, $content);

		// Search for readmore tags using Joomla's mechanism
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$pos	= preg_match( $pattern, $content );

		if( $pos == 0 )
		{
			$this->intro	= $content;
			$this->content	= '';

		}
		else
		{
			list( $intro , $main ) = preg_split( $pattern , $content, 2 );

			$this->intro	= $intro;
			$this->content	= $main;
		}

		$intro          = $this->intro;
		$content        = $this->content;

		$publish_up		= '';
		$publish_down 	= '';
		$created_date   = '';
		$tzoffset       = EasyBlogDateHelper::getOffSet();

		if(!empty( $this->created ))
		{
			$date 			= EasyBlogHelper::getDate( $this->created, $tzoffset );
			$created_date   = $date->toMySQL();
		}

		if($this->publish_down == '0000-00-00 00:00:00')
		{
			$publish_down   = $this->publish_down;
		}
		else if(!empty( $this->publish_down ))
		{
			$date = EasyBlogHelper::getDate( $this->publish_down, $tzoffset );
			$publish_down   = $date->toMySQL();
		}


		if(!empty( $this->publish_up ))
		{
			$date = EasyBlogHelper::getDate( $this->publish_up,  $tzoffset);
			$publish_up   = $date->toMySQL();
		}

		//default joomla date obj
		$date		= EasyBlogHelper::getDate();

		$this->created		= !empty( $created_date ) ? $created_date : $date->toMySQL();
		$this->intro		= $intro;
		$this->content		= $content;
		$this->modified		= $date->toMySQL();
		$this->publish_up 	= (!empty( $publish_up)) ? $publish_up : $date->toMySQL();
		$this->publish_down	= (empty( $publish_down ) ) ? '0000-00-00 00:00:00' : $publish_down;
		$this->ispending	= (empty($acl->rules->publish_entry)) ? 1 : 0;
		$this->issitewide	= ( empty( $this->blog_contribute ) ) ? 1 : 0;

		// Bind necessary stuffs for the next load
		if( isset( $data[ 'tags' ] ) && !empty( $data[ 'tags' ] ) && is_array( $data[ 'tags' ] ) )
		{
			$this->set( 'tags'	, implode( ',' , $data[ 'tags' ] ) );
		}

		if( isset( $data[ 'keywords' ] ) && !empty( $data[ 'keywords' ] ) )
		{
			$this->set( 'metakey'	, $data[ 'keywords' ] );
		}

		if( isset( $data[ 'description' ] ) && !empty( $data[ 'description' ] ) )
		{
			$this->set( 'metadesc'	, $data[ 'description' ] );
		}

		if( isset( $data[ 'trackback' ] ) && !empty( $data[ 'trackback' ] ) )
		{
			$this->set( 'trackbacks'	, $data[ 'trackback' ] );
		}

		if( isset( $data[ 'blogpassword' ] ) && !empty( $data[ 'blogpassword' ] ) )
		{
			$this->set( 'blogpassword'	, $data[ 'blogpassword' ] );
		}

		// @task: Try to detect autoposting for centralized sites
		if( isset( $data[ 'centralized' ] ) && !empty( $data['centralized'] ) )
		{
			$this->set( 'autopost_centralized' , implode( ',' , $data[ 'centralized' ] ) );
		}

		if( isset( $data[ 'socialshare' ] ) && !empty( $data[ 'socialshare' ] ) )
		{
			$this->set( 'autopost'	, implode( ',' , $data[ 'socialshare' ] ) );
		}

		if( isset( $data[ 'blog_contribute_source' ] ) && $data[ 'blog_contribute_source' ] != 'easyblog' )
		{
			$this->set( 'external_source'	,  $data[ 'blog_contribute_source' ]);
			$this->set( 'external_group_id'	,  $data[ 'blog_contribute' ]);
		}

		if( isset( $data['image']) && $data['image'] )
		{
			$this->set( 'image'	, $data['image'] );
		}

		return true;
	}

	public function getRejected()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post_rejected' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'draft_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( !$result )
		{
			return false;
		}

		$result->author	= EasyBlogHelper::getTable( 'Profile' , 'Table' )->load( $result->created_by );
		return $result;
	}

	public function store($updateNulls = false)
	{
		$state		= parent::store();

		// @rule: Process notifications for admins when the blog post is pending approvals
		if( $this->pending_approval )
		{
			$user 	= EasyBlogHelper::getTable( 'Profile' );
			$user->load( $this->created_by );

			$date	= EasyBlogDateHelper::dateWithOffSet( $this->created );

			$data	= array(
						'blogTitle'			=> $this->title,
						'blogAuthor'		=> $user->getName(),
						'blogAuthorLink'	=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id , false , true ),
						'blogIntro'			=> $this->intro,
						'blogContent'		=> $this->content,
						'blogAuthorAvatar'	=> $user->getAvatar(),
						'blogDate'			=> EasyBlogDateHelper::toFormat( $date , '%A, %B %e, %Y' ),
						'reviewLink'		=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=dashboard&layout=pending&draft_id=' . $this->id , false , true )
			);

			// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
			$sh404exists	= EasyBlogRouter::isSh404Enabled();

			if( JFactory::getApplication()->isAdmin() && $sh404exists )
			{
				$data[ 'blogAuthorLink' ]	= JURI::root() . 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id;
				$data[ 'reviewLink' ]		= JURI::root() . 'index.php?option=com_easyblog&view=dashboard&layout=pending&draft_id' . $this->id;
			}

			$emailTitle 	= JText::_( 'COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_PENDING_REVIEW' );
			$emails			= array();
			$notification 	= EasyBlogHelper::getHelper( 'Notification' );

			$config 		= EasyBlogHelper::getConfig();

			// @rule: if custom_email_as_admin is enabled, use custom email as admin email

			if( $config->get( 'custom_email_as_admin' ) )
			{
				// @rule: Send to custom email addresses
				$notification->getCustomEmails( $emails );
			}
			else
			{
				// @rule: Send to administrator's on the site.
				$notification->getAdminEmails( $emails );
			}

			$notification->send( $emails , $emailTitle , 'email.blog.review' , $data );
		}

		return $state;
	}
}
