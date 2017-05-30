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

class EasyBlogTableBlogs extends EasyBlogTable
{

	var $id 				= null;
	var $created_by			= null;
	var $modified			= null;
	var $created			= null;
	var $publish_up			= null;
	var $publish_down		= null;
	var $title				= null;
	var $permalink			= null;
	var $content			= null;
	var $intro				= null;
	var $category_id		= null;
	var $published			= null;
	var $ordering			= null;
	var $vote				= null;
	var $hits				= null;
	var $private			= null;
	var $allowcomment		= null;
	var $subscription		= null;
	var $frontpage			= null;
	var $isnew				= null;
	var $ispending			= null;
	var $issitewide			= null;
	var $blogpassword		= null;
	var $latitude			= null;
	var $longitude			= null;
	var $address			= null;
	var $source				= null;
	var $robots				= null;
	var $copyrights			= null;

	var $image 				= null;
	var $language 			= null;

	var $send_notification_emails = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_post' , 'id' , $db );
	}

	function load( $key = null , $permalink = false )
	{
		if( !$permalink )
		{
			return parent::load( $key );
		}

		$db		= $this->getDBO();

		$query	= 'SELECT a.`id` FROM ' . $this->_tbl . ' as a '
				. 'WHERE a.`permalink` = ' . $db->Quote( $key );
		$db->setQuery( $query );


		$id		= $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if( !$id )
		{
			$query	= 'SELECT a.`id` FROM ' . $this->_tbl . ' as a '
					. 'WHERE a.`permalink` = ' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );
			$db->setQuery( $query );

			$id		= $db->loadResult();
		}
		return parent::load( $id );
	}

	/**
	 * This method returns registered users that are subscribed to the following channels
	 *
	 * - Site wide
	 * - Category
	 *
	 * @param	boolean		$userOnly	If true, only fetches user's that are registered in the system.
	 * @return	Array
	 */
	public function getRegisteredSubscribers( $type = 'new' , $excludedUsers = array() )
	{
		JModel::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' );

		// @task: Get subscribers who subcribed to the site wide blog.
		if( $type == 'new' )
		{
			$model			= JModel::getInstance( 'Subscription' , 'EasyBlogModel' );
			$subscribers	= $model->getSiteSubscribers();
		}

		if( $type == 'new' )
		{
			// @task: Get subscribers who subscribed to
			$model 			= JModel::getInstance( 'Category' , 'EasyBlogModel' );
			$subscribers	= array_merge( $subscribers , $model->getCategorySubscribers( $this->category_id ) );
		}

		$result 		= array();

		if( !$subscribers )
		{
			return false;
		}

		foreach( $subscribers as $subscriber )
		{
			if( $subscriber->user_id && !in_array( $subscriber->user_id , $excludedUsers ) )
			{
				$result[]	= $subscriber->user_id;
			}

		}

		// @rule: Ensure that there's no duplicates
		$result 	= array_unique( $result );

		return $result;
	}

	function getSubscribers( $excludeEmails = array() )
	{
		$db = $this->_db;

		$excludeEmailsList  = '';

		if( !empty( $excludeEmails ) )
		{
			for( $i = 0; $i < count($excludeEmails); $i++ )
			{
				$excludeEmailsList  = ( empty( $excludeEmailsList ) ) ? $db->Quote( $excludeEmails[$i] ) : ', ' . $db->Quote( $excludeEmails[$i] );
			}
		}

		$query  = 'SELECT * FROM `#__easyblog_post_subscription` WHERE `post_id` = ' . $db->Quote($this->id);

		if( !empty( $excludeEmailsList ) )
		{
			$query  .= ' and `email` NOT IN (' . $excludeEmailsList . ')';
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	function getCategoryName()
	{
		if($this->category_id == 0)
		{
			return JText::_('UNCATEGORIZED');
		}

		static $loaded	= array();

		if( !isset( $loaded[ $this->category_id ] ) )
		{
			$db		= EasyBlogHelper::db();

			$query  = 'SELECT `title` FROM `#__easyblog_category` WHERE `id` = ' . $db->Quote($this->category_id);
			$db->setQuery($query);

			$loaded[ $this->category_id ]	= JText::_( $db->loadResult() );
		}
		return $loaded[ $this->category_id ];
	}

	/**
	 * Override functionality of JTable's hit method as we want to limit the hits based on the session.
	 *
	 **/
	public function hit($pk = null)
	{
		$config 	= EasyBlogHelper::getConfig();

		if( $config->get( 'main_hits_session') )
		{
			$ip			= JRequest::getVar( 'REMOTE_ADDR' , '' , 'SERVER' );

			if( !empty( $ip ) && !empty($this->id) )
			{
				$token		= md5( $ip . $this->id );

				$session	= JFactory::getSession();
				$exists		= $session->get( $token , false );

				if( $exists )
				{
					return true;
				}

				$my = JFactory::getUser();

				JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

				if($my->id > 0 && EasyBlogHelper::isAUPEnabled() )
				{
					$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $my->id );
					AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_read_blog', $aupid, '', JText::sprintf('COM_EASYBLOG_AUP_READ_BLOG', $this->title) );
				}

				// Deduct points from respective systems
				// @rule: Integrations with EasyDiscuss
				EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.view.blog' , $my->id , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_VIEW_BLOG' , $this->title ) );
				EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.view.blog' , $my->id );
				EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.view.blog' , $my->id );

				// Only give points if the viewer is viewing another person's blog post.
				if( $my->id != $this->created_by )
				{
					EasyBlogHelper::getHelper( 'EasySocial' )->assignBadge( 'blog.read' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_READ_BLOG' ) );	
				}
				

				$session->set( $token , 1 );
			}
		}

		return parent::hit($pk);
	}

	/**
	 * Must only be bind when using POST data
	 **/
	function bind( $data , $post = false )
	{
		parent::bind( $data );

		if( $post )
		{
			$acl		= EasyBlogACLHelper::getRuleSet();
			$my			= JFactory::getUser();

			// Some properties needs to be overriden.
			$content	= JRequest::getVar('write_content_hidden', '', 'post', 'string', JREQUEST_ALLOWRAW );

			if($this->id == 0)
			{
				// this is to check if superadmin assign blog author during blog creation.
				if(empty($this->created_by))
				{
					$this->created_by	= $my->id;
				}
			}

			//remove unclean editor code.
			$pattern    = array('/<p><br _mce_bogus="1"><\/p>/i',
								'/<p><br mce_bogus="1"><\/p>/i',
								'/<br _mce_bogus="1">/i',
								'/<br mce_bogus="1">/i',
								'/<p><br><\/p>/i');
			$replace    = array('','','','','');
			$content    = preg_replace($pattern, $replace, $content);

			// Search for readmore tags using Joomla's mechanism
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$pos	= preg_match( $pattern, $content );

			if( $pos == 0 )
			{
				$this->intro	= $content;

				// @rule: Since someone might update this post, we need to clear out the content
				// if it doesn't contain anything.
				$this->content	= '';
			}
			else
			{
				list( $intro , $main ) = preg_split( $pattern , $content, 2 );

				$this->intro	= $intro;
				$this->content	= $main;
			}

			$publish_up		= '';
			$publish_down 	= '';
			$created_date   = '';

			$tzoffset       = EasyBlogDateHelper::getOffSet();
			if(!empty( $this->created ))
			{
				$date = EasyBlogHelper::getDate( $this->created,  $tzoffset);
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
				$date = EasyBlogHelper::getDate($this->publish_up, $tzoffset);
				$publish_up   = $date->toMySQL();
			}

			//default joomla date obj
			$date		= EasyBlogHelper::getDate();

			$this->created 		= !empty( $created_date ) ? $created_date : $date->toMySQL();
			$this->modified		= $date->toMySQL();
			$this->publish_up 	= (!empty( $publish_up)) ? $publish_up : $date->toMySQL();
			$this->publish_down	= (empty( $publish_down ) ) ? '0000-00-00 00:00:00' : $publish_down;
			$this->ispending 	= (empty($acl->rules->publish_entry)) ? 1 : 0;
			$this->title		= trim($this->title);
			$this->permalink	= trim($this->permalink);

		}

		return true;
	}

	/**
	 * Trashing a blog simply updates the state of the blog post.
	 */
	public function trash()
	{
		$this->published	= POST_ID_TRASHED;
		return parent::store();
	}

	/**
	 * Override delete routine from the parent as we need to run
	 * some custom routines in our deletion
	 */
	public function delete($pk = null)
	{
		// @rule: Delete reports that are associated to this group since the blog post is deleted, it shouldn't be tied to any reports.
		$this->deleteReports();

		// @rule: Delete relationships from jomsocial stream
		$this->removeStream();

		$status		= parent::delete($pk);
		$config 	= EasyBlogHelper::getConfig();

		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();

		// Run some cleanup for our own plugins
		$dispatcher->trigger('onAfterEasyBlogDelete', array(&$this ));

		// Delete all relations
		$this->deleteBlogTags();
		$this->deleteMetas();
		$this->deleteComments();
		$this->deleteTeamContribution();

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		// Deduct points from respective systems
		// @rule: Integrations with EasyDiscuss
		EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.delete.blog' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_BLOG' , $this->title ) );
		EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.delete.blog' , $this->created_by );
		EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.delete.blog' , $this->created_by );

		// Assign EasySocial points
		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
		$easysocial->assignPoints( 'blog.remove' , $this->created_by );
		
		// @task: Integrations with Jomsocial. Remove points necessarily.
		if( $config->get( 'main_jomsocial_userpoint' ) )
		{
			$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
			if( JFile::exists( $path ) )
			{
				require_once( $path );
				CUserPoints::assignPoint( 'com_easyblog.blog.remove' , $this->created_by );
			}
		}

		// @rule: Mighty Touch karma points
		EasyBlogHelper::getHelper( 'MightyTouch' )->setKarma( $this->created_by , 'remove_blog' );

		// @since 1.2
		// AlphaUserPoints integrations. Remove points necessarily
		if ( EasyBlogHelper::isAUPEnabled() )
		{
			AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_blog', AlphaUserPointsHelper::getAnyUserReferreID( $this->created_by ) , '', JText::sprintf('COM_EASYBLOG_AUP_BLOG_DELETED', $this->title) );
		}

		// Delete group relations
		EasyBlogHelper::getHelper( 'Groups' )->deleteContribution( $this->id );


		if( $status )
		{
			//activity logging.
			$activity   = new stdClass();
			$activity->actor_id		= $this->created_by;
			$activity->target_id	= '0';
			$activity->context_type	= 'post';
			$activity->context_id	= $this->id;
			$activity->verb         = 'delete';
			$activity->uuid         = $this->title;
			EasyBlogHelper::activityLog( $activity );
		}

		return $status;
	}

	public function deleteReports()
	{
		$db		= EasyBlogHelper::db();
		$query	= 'DELETE FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_reports' );
		$query	.= ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'obj_id' ) . '=' . $db->Quote( $this->id );
		$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'obj_type' ) . '=' . $db->Quote( EBLOG_REPORTING_POST );

		$db->setQuery( $query );
		$db->Query();
	}

	/**
	 * When executed, remove any 3rd party integration records.
	 */
	public function removeStream()
	{
		jimport( 'joomla.filesystem.file' );

		$config 	= EasyBlogHelper::getConfig();

		// @rule: Detect if jomsocial exists.
		$file 		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		if( JFile::exists( $file ) && $config->get( 'integrations_jomsocial_blog_new_activity' ) )
		{
			// @rule: Test if record exists first.
			$db 	= EasyBlogHelper::db();
			$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__community_activities' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'app' ) . '=' . $db->Quote( 'com_easyblog' ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'cid' ) . '=' . $db->Quote( $this->id );

			$db->setQuery( $query );
			$exists	= $db->loadResult();

			if( $exists )
			{
				$query	= 'DELETE FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__community_activities' ) . ' '
						. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'app' ) . '=' . $db->Quote( 'com_easyblog' ) . ' '
						. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'cid' ) . '=' . $db->Quote( $this->id );

				$db->setQuery( $query );
				$db->Query();
			}
		}
	}

	public function deleteTeamContribution()
	{
		$db 	= EasyBlogHelper::db();

		$query	= 'DELETE FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );

		$db->setQuery( $query );
		$db->Query();
		return true;
	}

	function deleteBlogTags()
	{
		$db = $this->_db;

		if($this->id == 0)
			return false;

		$query  = 'DELETE FROM `#__easyblog_post_tag` WHERE `post_id` = ' . $db->Quote($this->id);
		$db->setQuery($query);
		$db->query();

		return true;
	}

	function deleteMetas()
	{
		$db = $this->_db;

		if($this->id == 0)
			return false;

		$query  = 'DELETE FROM `#__easyblog_meta` WHERE `content_id` = ' . $db->Quote($this->id);
		$query  .= ' AND `type` = ' . $db->Quote('post');

		$db->setQuery($query);
		$db->query();

		return true;
	}

	function deleteComments()
	{
		$db = $this->_db;

		if($this->id == 0)
			return false;

		$query  = 'DELETE FROM `#__easyblog_comment` WHERE `post_id` = ' . $db->Quote($this->id);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function getBlogContribution()
	{
		$db		= EasyBlogHelper::db();

		// @task: Test for external entries
		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_external' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$result	= $db->loadObject();

		if( $result )
		{
			$table	= EasyBlogHelper::getTable( 'External' );
			$table->bind( $result );

			return $table;
		}

		// @task: Legacy support for prior to 3.5 since old tables uses #__easyblog_external_groups
		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_external_groups' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$result	= $db->loadObject();

		if( !$result )
		{
			return false;
		}

		$table	= EasyBlogHelper::getTable( 'ExternalGroup' );
		$table->bind( $result );

		return $table;
	}

	/**
	 * Updates a blog contribution
	 */
	function updateBlogContribution( $blogContribution , $contributionSource = 'easyblog' )
	{
		// If there's no data passed in here, we shouldn't process anything
		if( empty( $blogContribution ) )
		{
			if($contributionSource == 'easyblog')
			{
				// Delete any existing contribution
				$this->deleteTeamContribution();

				// Delete any contributions from jomsocial event or group
				if( EasyBlogHelper::getHelper( 'Event')->isEnabled() )
				{
					EasyBlogHelper::getHelper( 'Event' )->deleteContribution( $this->id );
				}

				if( EasyBlogHelper::getHelper( 'Groups')->useGroups() )
				{
					EasyBlogHelper::getHelper( 'Groups' )->deleteContribution( $this->id );
				}
			}
			return false;
		}

		if( $contributionSource == 'easyblog' )
		{
			// Delete any existing contribution
			$this->deleteTeamContribution();

			if( !is_array( $blogContribution ) )
			{
				$blogContribution	= array( $blogContribution );
			}

			foreach($blogContribution as $bc)
			{
				$post   = array();
				$post['team_id'] = $bc;
				$post['post_id'] = $this->id;

				$teamBlogPost		= EasyBlogHelper::getTable( 'TeamBlogPost', 'Table' );
				$teamBlogPost->bind($post);

				// we supress the error here. its okay, it safe to suppress it here.
				@$teamBlogPost->store();
			}

			// Delete any contributions from jomsocial event or group
			if( EasyBlogHelper::getHelper( 'Event')->isEnabled() )
			{
				EasyBlogHelper::getHelper( 'Event' )->deleteContribution( $this->id );
			}

			if( EasyBlogHelper::getHelper( 'Groups')->useGroups() )
			{
				EasyBlogHelper::getHelper( 'Groups' )->deleteContribution( $this->id );
			}
		}
		else
		{
			if( $contributionSource == 'jomsocial.event' )
			{
				EasyBlogHelper::getHelper( 'Event' )->updateContribution( $this->id , $blogContribution , $contributionSource );
			}
			else
			{
				// Other than team, there's other 3rd party integrations. We leave it to the helper to process this.
				EasyBlogHelper::getHelper( 'Groups' )->updateContribution( $this->id , $blogContribution , $contributionSource );
			}
		}
		return true;
	}

	function getTeamContributed()
	{
		$db = $this->_db;

		$query  = 'SELECT a.`team_id` FROM `#__easyblog_team_post` AS a';
		$query  .= ' INNER JOIN `#__easyblog_team` AS b';
		$query  .= '   ON a.team_id = b.id';
		$query  .= ' WHERE a.`post_id` = ' . $db->Quote($this->id);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Determines whether the current blog is accessible to
	 * the current browser.
	 *
	 * @param	JUser	$my		Optional user object.
	 * @return	boolean		True if accessible and false otherwise.
	 **/
	public function isAccessible()
	{
		$isAllowed = EasyBlogHelper::getHelper( 'Privacy' )->checkPrivacy( $this );

		if( $isAllowed )
		{
			//now we check the category id
			$catId  	= $this->category_id;
			$category   = EasyBlogHelper::getTable('Category', 'Table' );
			$category->load( $catId );

			if( $category->private != '0')
			{
				$isAllowed = $category->checkPrivacy();
			}
		}

		return $isAllowed;
	}

	/**
	 * Determines whether the current blog is featured or not.
	 *
	 * @return	boolean		True if featured false otherwise
	 **/
	public function isFeatured()
	{
		if( $this->id == 0 )
		{
			return false;
		}

		static $loaded	= array();

		if( !isset( $loaded[ $this->id ] ) )
		{
			$loaded[ $this->id ]	= EasyBlogHelper::isFeatured( 'post' , $this->id );
		}
		return $loaded[ $this->id ];
	}

	public function getMetaId()
	{
		$db = $this->_db;

		$query  = 'SELECT a.`id` FROM `#__easyblog_meta` AS a';
		$query  .= ' WHERE a.`content_id` = ' . $db->Quote($this->id);
		$query  .= ' AND a.`type` = ' . $db->Quote( 'post' );

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/*
	 * Process neccessary replacements here.
	 *
	 */
	public function store( $log = true )
	{
		// @rule: Load language file from the front end.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$config 		= EasyBlogHelper::getConfig();

		$under_approval = false;
		if( isset( $this->under_approval ) )
		{
			$under_approval = true;

			// now we need to reset this variable from the blog object.
			unset($this->under_approval);
		}

		// @trigger: onBeforeSave
		$this->triggerBeforeSave();

		// @rule: Determine if this record is new or not.
		if( empty( $this->isnew ) )
			$isNew  		= ( empty( $this->id) ) ? true : false;
		else
			$isNew          = true;

		// @rule: Get the rulesets for this user.
		$acl 			= EasyBlogACLHelper::getRuleSet();

		// @rule: Process badword filters for title here.
		$blockedWord 	= EasyBlogHelper::getHelper( 'String' )->hasBlockedWords( $this->title );
		if( $blockedWord !== false )
		{
			$this->setError( JText::sprintf( 'COM_EASYBLOG_BLOG_TITLE_CONTAIN_BLOCKED_WORDS' , $blockedWord ) );
			return false;
		}

		// @rule: Check for minimum words in the content if required.
		if( $config->get( 'main_post_min' ) )
		{
			$minimum	= $config->get( 'main_post_length' );
			$total 		= JString::strlen( strip_tags( $this->intro . $this->content ) );

			if( $total < $minimum )
			{
				$this->setError( JText::sprintf( 'COM_EASYBLOG_CONTENT_LESS_THAN_MIN_LENGTH' , $minimum) );
				return false;
			}
		}

		// @rule: Check for invalid title
		if( empty($this->title) || $this->title == JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_DEFAULT_TITLE' ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR' ) );
			return false;
		}

		// @rule: For edited blogs, ensure that they have permissions to edit it.
		if( !$isNew && $this->created_by != JFactory::getUser()->id && !EasyBlogHelper::isSiteAdmin() && empty($acl->rules->moderate_entry) )
		{

			if( !class_exists( 'EasyBlogModelTeamBlogs' ) )
			{
				jimport( 'joomla.application.component.model' );
				JLoader::import( 'blog' , EBLOG_ROOT . DIRECTORY_SEPARATOR . 'models' );
			}

			// @task: Only throw error when this blog post is not a team blog post and it's not owned by the current logged in user.
			JModel::addIncludePath(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models');
			$model			= JModel::getInstance( 'TeamBlogs' , 'EasyBlogModel' );
			$contribution	= $model->getBlogContributed( $this->id );

			if( !$contribution || !$model->checkIsTeamAdmin( JFactory::getUser()->id , $contribution->team_id ) )
			{
				$this->setError( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_EDIT_BLOG' ) );
				return false;
			}
		}

		// @rule: Every blog post must be assigned to a category
		if( empty( $this->category_id ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_CATEGORY_ERROR' ) );
			return false;
		}

		// Filter / strip contents that are not allowed
		$filterTags 		= EasyBlogHelper::getHelper( 'Acl' )->getFilterTags();
		$filterAttributes	= EasyBlogHelper::getHelper( 'Acl' )->getFilterAttributes();

		// @rule: Apply filtering on contents
		jimport('joomla.filter.filterinput');
		$inputFilter 					= JFilterInput::getInstance( $filterTags , $filterAttributes , 1 , 1 , 0 );
		$inputFilter->tagBlacklist		= $filterTags;
		$inputFilter->attrBlacklist		= $filterAttributes;
		if( ( count($filterTags) > 0 && !empty($filterTags[0]) ) || ( count($filterAttributes) > 0 && !empty($filterAttributes[0]) ) )
		{
			$this->intro 					= $inputFilter->clean( $this->intro );
			$this->content 					= $inputFilter->clean( $this->content );
		}

		// @rule: Process badword filters for content here.
		$blockedWord 	= EasyBlogHelper::getHelper( 'String' )->hasBlockedWords( $this->intro . $this->content );
		if( $blockedWord !== false )
		{
			$this->setError( JText::sprintf( 'COM_EASYBLOG_BLOG_POST_CONTAIN_BLOCKED_WORDS' , $blockedWord ) );
			return false;
		}

		// @rule: Test for the empty-ness
		if( empty( $this->intro ) && empty( $this->content ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_CONTENT_ERROR' ) );
		}

		// alway set this to false no matter what! TODO: remove this column.
		$this->ispending    = '0';

		$state  	= parent::store();
		$source		= JRequest::getVar( 'blog_contribute_source' , 'easyblog' );

		// @trigger: onBeforeSave
		$this->triggerAfterSave();

		// if this is blog edit, then we should see the column isnew to determine
		// whether the post is really new or not.
		if( !$isNew )
		{
			$isNew 	= $this->isnew;
		}

		// @task: If auto featured is enabled, we need to feature the blog post automatically since the blogger is featured.
		if( $config->get('main_autofeatured', 0) && EasyBlogHelper::isFeatured( 'blogger' , $this->created_by) && !EasyBlogHelper::isFeatured( 'post' , $this->id) )
		{
			EasyBlogHelper::makeFeatured( 'post' , $this->id);
		}

		// @task: This is when the blog is either created or updated.
		if( $source == 'easyblog' && $state && $this->published == POST_ID_PUBLISHED && $log )
		{

			// @rule: Add new stream item in jomsocial
			EasyBlogHelper::addJomSocialActivityBlog( $this, $isNew );


			// @rule: Log new stream item into EasyBlog
			$activity   = new stdClass();
			$activity->actor_id		= $this->created_by;
			$activity->target_id	= '0';
			$activity->context_type	= 'post';
			$activity->context_id	= $this->id;
			$activity->verb         = ( $isNew ) ? 'add' : 'update';
			$activity->uuid         = $this->title;
			EasyBlogHelper::activityLog( $activity );
		}

		if( $source == 'easyblog' && $state && $this->published == POST_ID_PUBLISHED && $isNew && $log )
		{
			// @rule: Send email notifications out to subscribers.
			$author 			= EasyBlogHelper::getTable( 'Profile' );
			$author->load( $this->created_by );

			// @rule: Ping pingomatic
			if( $config->get( 'main_pingomatic' ) )
			{
				if( !EasyBlogHelper::getHelper( 'Pingomatic' )->ping( $this->title , EasyBlogHelper::getExternalLink('index.php?option=com_easyblog&view=entry&id=' . $this->id, true) ) )
				{
					EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_SAVE_PINGOMATIC_ERROR') , 'error');
				}
			}

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'blog.create' , $this->created_by );

			// @rule: Add userpoints for jomsocial
			if( $config->get( 'main_jomsocial_userpoint' ) )
			{
				$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
				if( JFile::exists( $path ) )
				{
					require_once( $path );
					CUserPoints::assignPoint( 'com_easyblog.blog.add' , $this->created_by );
				}
			}

			$link	= $this->getExternalBlogLink( 'index.php?option=com_easyblog&view=entry&id='. $this->id );

			// @rule: Add notifications for jomsocial 2.6
			if( $config->get( 'integrations_jomsocial_notification_blog' ) )
			{
				// Get list of users who subscribed to this blog.
				$target	= $this->getRegisteredSubscribers( 'new' , array( $this->created_by ) );
				EasyBlogHelper::getHelper( 'JomSocial' )->addNotification( JText::sprintf( 'COM_EASYBLOG_JOMSOCIAL_NOTIFICATIONS_NEW_BLOG' , $author->getName() , $link  , $this->title ) , 'easyblog_new_blog' , $target , $this->created_by , $link );
			}

			// @rule: Mighty Touch karma points
			EasyBlogHelper::getHelper( 'MightyTouch' )->setKarma( $this->created_by , 'new_blog' );

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.new.blog' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_BLOG' , $this->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.new.blog' , $this->created_by );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.new.blog' , $this->created_by );

			// Assign badge for users that report blog post.
			// Only give points if the viewer is viewing another person's blog post.
			EasyBlogHelper::getHelper( 'EasySocial' )->assignBadge( 'blog.create' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_CREATE_BLOG_POST' ) );
			
			if( $config->get( 'integrations_easydiscuss_notification_blog' ) )
			{
				// Get list of users who subscribed to this blog.
				$target	= $this->getRegisteredSubscribers( 'new' , array( $this->created_by ) );

				EasyBlogHelper::getHelper( 'EasyDiscuss' )->addNotification( $this ,
									JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_BLOG' , $author->getName() , $this->title) ,
									EBLOG_NOTIFICATIONS_TYPE_BLOG ,
									$target ,
									$this->created_by,
									$link );
			}

			$my 	= JFactory::getUser();

			// @rule: Add points for AlphaUserPoints
			if( $my->id == $this->created_by && EasyBlogHelper::isAUPEnabled() )
			{
				// get blog post URL
				$url	= EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$this->id);
				$aupid	= AlphaUserPointsHelper::getAnyUserReferreID( $this->created_by );
				AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_add_blog', $aupid , 'easyblog_add_blog_' . $this->id, JText::sprintf('COM_EASYBLOG_AUP_NEW_BLOG_CREATED', $url, $this->title) );
			}

			// @rule: Process trackbacks
			$this->processTrackbacks();

			// Update the isnew column so that if user edits this entry again, it doesn't send any notifications the second time.
			$this->isnew 	= 0;
			$this->store( false );
		}

		return $state;
	}

	public function notify( $underApproval = false )
	{
		if( empty($this->send_notification_emails) )
		{
			return;
		}
		
		// @rule: Send email notifications out to subscribers.
		$author 			= EasyBlogHelper::getTable( 'Profile' );
		$author->load( $this->created_by );

		$data[ 'blogTitle']				= $this->title;
		$data[ 'blogAuthor']			= $author->getName();
		$data[ 'blogAuthorAvatar' ]		= $author->getAvatar();
		$data[ 'blogAuthorLink' ]		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $author->id , false , true );
		$data[ 'blogAuthorEmail' ]		= $author->user->email;
		$data[ 'blogIntro' ]			= $this->intro;
		$data[ 'blogContent' ]			= $this->content;

		// Try to truncate introtext based on the configured settings because content is empty.
		if( empty( $this->content ) )
		{
			$obj 			= clone( $this );
			$obj->readmore	= EasyBlogHelper::requireReadmore( $obj );
			EasyBlogHelper::truncateContent( $obj , false , true );
			$data['blogIntro']			= $obj->text;
		}

		$data[ 'blogLink' ]				= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id='. $this->id, false, true);

		$date							= EasyBlogDateHelper::dateWithOffSet( $this->created );
		$data[ 'blogDate' ]				= EasyBlogDateHelper::toFormat( $date , '%A, %B %e, %Y' );

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		if( JFactory::getApplication()->isAdmin() && $sh404exists )
		{
			$data[ 'blogLink' ]			= JURI::root() . 'index.php?option=com_easyblog&view=entry&id=' . $this->id;
			$data[ 'blogAuthorLink' ]	= JURI::root() . 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $author->id;
		}

		$config 		= EasyBlogHelper::getConfig();
		$emailBlogTitle = JString::substr( $this->title , 0 , $config->get( 'main_mailtitle_length' ) );
		$emailTitle 	= JText::sprintf( 'COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_ADDED_WITH_TITLE' ,  $emailBlogTitle ) . ' ...';

		$notification	= EasyBlogHelper::getHelper( 'Notification' );
		$emails 		= array();

		// @rule: Fetch custom emails defined at the back end.
		if( $config->get( 'notification_blogadmin' ) )
		{
			if( $config->get( 'custom_email_as_admin' ) )
			{
				$notification->getCustomEmails( $emails );
			}
			else
			{
				$notification->getAdminEmails( $emails );
			}
		}

		// @task: If this blog post is a team posting, we shouldn't send notification to everyone.
		$model			= JModel::getInstance( 'TeamBlogs' , 'EasyBlogModel' );
		$contribution	= $model->getBlogContributed( $this->id );

		if( $contribution )
		{
			$team		= EasyBlogHelper::getTable( 'TeamBlog' );
			$team->load( $contribution->team_id );

			$contribution->access	= $team->access;
		}
		else
		{
			$contribution			= new stdClass();
			$contribution->access	= EBLOG_TEAMBLOG_ACCESS_EVERYONE;
		}

		// @task: This is when this blog post is posted into a team.
		if( $contribution && $config->get( 'notification_teamsubscriber' ) && isset( $contribution->team_id ) )
		{
			// @task: Send emails to users who is a member of the team
			$notification->getTeamUserEmails( $emails , $contribution->team_id );

			// @task: Send emails to users who have subscribed to the team
			$notification->getTeamSubscriberEmails( $emails , $this );
		}

		// @task: Only send emails to these group of users provided that, it is not a team posting or private team posting.
		if( !$contribution || $contribution->access != EBLOG_TEAMBLOG_ACCESS_MEMBER )
		{
			// @rule: Get all email addresses for the whole site.
			if( $config->get( 'notification_allmembers' ) )
			{
				$notification->getAllEmails( $emails );
			}
			else
			{
				// @rule: Send to subscribers that subscribe to the bloggers
				if( $config->get( 'notification_blogsubscriber' ) )
				{
					$notification->getBloggerSubscriberEmails( $emails , $this );
				}

				// @rule: Send to subscribers that subscribed to the category
				if( $config->get( 'notification_categorysubscriber' ) )
				{
					$notification->getCategorySubscriberEmails( $emails , $this );
				}

				// @rule: Send notification to all site's subscribers
				if($config->get('notification_sitesubscriber') )
				{
					$notification->getSiteSubscriberEmails( $emails , $this );
				}
			}
		}

		// @rule: We need to remove the email of the creator since the creator of this blog post should not receive the email.
		if( isset( $emails[ $author->user->email ] ) )
		{
			unset( $emails[ $author->user->email ] );
		}

		// @task: Add the emails into the mail queue now.
		if( !empty( $emails ) )
		{
			$notification->send( $emails , $emailTitle , 'email.blog.new' , $data );
		}

		// @task: If this blog post is under approval, we need to send email to the site admin
		if( $underApproval )
		{
			// We know that this blog post requires moderation. Send notification to the author and let them know, that it is being moderated.
			$authorEmail		= array();

			$obj 				= new stdClass();
			$obj->unsubscribe	= false;
			$obj->email 		= $author->user->email;

			$authorEmail[ $author->user->email ]	= $obj;

			$notification->send( $authorEmail , JText::_('COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_APPROVED')  , 'email.blog.approved' , $data );
		}

	}

	public function createMeta( $key , $desc )
	{
		$id		= $this->getMetaId();

		// @rule: Save meta tags for this entry.
		$meta		= EasyBlogHelper::getTable( 'Meta' );
		$meta->load( $id );

		$meta->set( 'keywords'		, $key );
		$meta->set( 'description'	, $desc );
		$meta->set( 'content_id'	, $this->id );
		$meta->set( 'type'			, META_TYPE_POST );
		$meta->store();
	}

	public function getExternalBlogLink( $url )
	{
		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		$link			= EasyBlogRouter::getRoutedURL( $url , false, true );

		if( JFactory::getApplication()->isAdmin() && $sh404exists )
		{
			$link	= rtrim( JURI::root() , '/' ) . $url;
		}

		return $link;
	}

	public function processTrackbacks()
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		if( !class_exists( 'EasyBlogModelTrackbackSent') )
		{
			JLoader::import( 'trackbacksent' , EBLOG_ROOT . DIRECTORY_SEPARATOR . 'models' );
		}

		$model 	= JModel::getInstance( 'TrackbackSent' , 'EasyBlogModel' );

		// get lists of trackback URLs based on blog ID
		$trackbacks		= $model->getSentTrackbacks( $this->id , true );

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'trackback.php' );
		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

		if( !$trackbacks )
		{
			return false;
		}

		foreach( $trackbacks as $trackback )
		{
			$author	= EasyBlogHelper::getTable( 'Profile' );
			$author->load( $this->created_by );

			$tb		= new EasyBlogTrackBack( $author->getName() , $author->getName() , 'UTF-8' );
			$text	= empty( $this->intro ) ? $this->content : $this->intro;
			$url	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $this->id , false , true );

			if( $tb->ping( $trackback->url , $url , $this->title , $text ) )
			{
				$table		= EasyBlogHelper::getTable( 'TrackbackSent' );
				$table->load( $trackback->id );
				$table->markSent();
			}
		}

		return true;
	}

	/**
	 * Method to process trackbacks
	 */
	public function storeTrackbacks( $urls )
	{
		if( empty( $urls ) )
		{
			return false;
		}

		$rows 	= explode( '\n' , $urls );
		foreach( $rows as $row )
		{
			$trackback	= EasyBlogHelper::getTable( 'TrackbackSent' , 'Table' );

			if( !$trackback->load( $row , true , $blog->id ) )
			{
				$trackback->post_id		= $blog->id;
				$trackback->url			= $row;
				$trackback->sent		= 0;
				$trackback->store();
			}
		}

		return true;
	}

	/**
	 * Get's necessary asset values from this post.
	 */
	public function getAsset()
	{
		$asset 	= EasyBlogHelper::getTable( 'BlogAsset' );
		$asset->loadByPost( $this->id );

		return $asset;
	}

	public function getQuote()
	{
		// Quotes are stored as the title.
		return $this->title;
	}

	public function getVideo()
	{
		$asset	= $this->getAsset();
		$video	= $asset->get( 'value' );

		// @TODO: Video manipulation

		return $video;
	}

	public function getPhoto()
	{
		$asset	= $this->getAsset();
		$photo	= $asset->get( 'value' );

		// @TODO: Video manipulation

		return $photo;
	}

	public function getLink()
	{
		$asset	= $this->getAsset();
		$url	= $asset->get( 'value' );

		// @TODO: Video manipulation

		return $url;
	}

	public function bindText()
	{

	}

	/**
	 * This method should be invoked after a ->store is called so that it will process the tags.
	 *
	 * @access	public
	 * @param	Array	$tags	An array of tag titles.
	 */
	public function processTags( $tags , $isNew = true )
	{
		// @rule: If this item is still dirty (uncleaned), we do not want to allow anything to pass through.
		if( !$this->id || is_null( $this->id ) )
		{
			return false;
		}

		if( !class_exists( 'EasyBlogModelPostTag') )
		{
			JLoader::import( 'posttag' , EBLOG_ROOT . DIRECTORY_SEPARATOR . 'models' );
		}

		if( !class_exists( 'EasyBlogModelTags') )
		{
			JLoader::import( 'tags' , EBLOG_ROOT . DIRECTORY_SEPARATOR . 'models' );
		}

		// @rule: Get the rulesets for this user.
		$acl 		= EasyBlogACLHelper::getRuleSet();

		// @task: Delete existing associated tags.
		$tagModel		= JModel::getInstance( 'Tags' , 'EasyBlogModel' );
		$postTagModel	= JModel::getInstance( 'PostTag' , 'EasyBlogModel' );

		$postTagModel->deletePostTag( $this->id );

		// @rule: Only process default tags when the blog post is new.
		if( $isNew )
		{
			// @rule: These are the tags that is configured to be included in the posts.
			$defaultTags	= $tagModel->getDefaultTags();

			if( !empty( $defaultTags ) )
			{
				foreach( $defaultTags as $key => $tagId )
				{
					// Associate the default tags with this blog item.
					$postTagModel->add( $tagId , $this->id , EasyBlogHelper::getDate()->toMySQL() );
				}
			}
		}

		// @rule: Process user defined tags now
		if( !empty( $tags ) )
		{
			// What if the default tag already included this?
			foreach( $tags as $title )
			{
				// @rule: Skip empty tags
				if( empty( $title ) )
				{
					continue;
				}

				$table	= EasyBlogHelper::getTable( 'Tag' );
				$exists	= $table->exists( $title );

				// @rule: Skip this if the user doesn't have create acl rules.
				if( !$exists && !$acl->rules->create_tag )
				{
					continue;
				}

				if( !$exists )
				{
					// @rule: Store the tags first.
					$table->set( 'created_by'	, $this->created_by );
					$table->set( 'title'		, JString::trim( $title ) );
					$table->set( 'created'		, EasyBlogHelper::getDate()->toMySQL() );
					$table->set( 'published'	, 1 );
					$table->set( 'status' 		, '' );
					$table->store();
				}
				else
				{
					$table->load( $title , true );

					// @rule: Test if this tag is already associated since it may be the default tag.
					if( $postTagModel->isAssociated( $this->id , $table->id ) )
					{
						continue;
					}
				}

				// @rule: Add the association of tags here.
				$postTagModel->add( $table->id , $this->id , EasyBlogHelper::getDate()->toMySQL() );
			}
		}
	}

	/**
	 * Method to store tags for a blog post.
	 *
	 * @access	private
	 * @param	TableBlog	$blog 	The blog's database row.
	 */
	public function saveTags( $tags )
	{
		// If there's no tags, just skip the whole block
		if( !$tags )
		{
			return false;
		}

		$config 	= EasyBlogHelper::getConfig();
		$acl 		= EasyBlogACLHelper::getRuleSet();

		// @rule: Needed to add points for each tag creation
		if( $config->get( 'main_jomsocial_userpoint' ) )
		{
			$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';

			if( JFile::exists( $path ) )
			{
				require_once( $path );
			}
		}

		if( !is_array( $tags ) )
		{
			$tags 	= array( $tags );
		}

		$model 		= EasyBlogHelper::getModel( 'PostTag' );

		foreach( $tags as $title )
		{
			// Skip this if the tag is invalid.
			if( empty( $title ) )
			{
				continue;
			}

			$tag	= EasyBlogHelper::getTable( 'Tag' );
			$tag->load( $title , true );

			if( !$tag->exists( $title ) && $acl->rules->create_tag )
			{
				$tag->created_by 	= JFactory::getUser()->id;
				$tag->title 		= $title;
				$tag->created 		= EasyBlogHelper::getDate()->toMySQL();
				$tag->store();
			}

			// Add the association for the tag.
			$model->add( $tag->id , $blog->id , EasyBlogHelper::getDate()->toMySQL() );
		}

		return true;
	}

	public function autopost( $userSites , $centralizedSites = array() )
	{
		$config 	= EasyBlogHelper::getConfig();
		$allowed	= array( EBLOG_OAUTH_LINKEDIN , EBLOG_OAUTH_FACEBOOK , EBLOG_OAUTH_TWITTER );

		// @rule: Process centralized options first
		// See if there are any global postings enabled.
		if( !empty( $centralizedSites ) )
		{
			foreach( $centralizedSites as $item )
			{
				if( $config->get( 'integrations_' . $item . '_centralized' ) )
				{
					EasyBlogSocialShareHelper::share( $this , constant( 'EBLOG_OAUTH_' . JString::strtoupper( $item ) ) , true );
				}
			}
		}

		if( !empty( $userSites ) )
		{
			foreach( $userSites as $site )
			{
				if( in_array( $site , $allowed ) && $config->get( 'integrations_' . $site ) )
				{
					EasyBlogSocialShareHelper::share( $this , constant( 'EBLOG_OAUTH_' . JString::strtoupper( $site ) ) );
				}
			}
		}
	}

	/**
	 * This method invokes the trigger for events before save
	 *
	 */
	public function triggerBeforeSave()
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();

		// @task: Try to mimic Joomla's com_content behavior.
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_content' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'route.php' );

		// @trigger: onBeforeEasyBlogSave trigger
		$this->introtext	= '';
		$this->text			= '';
		$dispatcher->trigger( 'onBeforeEasyBlogSave' , array( &$this , $this->isNew() ) );

		// @trigger: onBeforeContentSave trigger
		// @rule: Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext	= $this->intro;
		$this->text			= $this->content;

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$dispatcher->trigger('onContentBeforeSave', array('easyblog.blog', &$this, $this->isNew() ));
		}
		else
		{
			$dispatcher->trigger('onBeforeContentSave', array(&$this, $this->isNew() ));
		}

		// @rule: Since content plugins uses introtext and text columns, we'll just need to retrieve the values after it has been modified.
		$this->intro		= $this->introtext;
		$this->content		= $this->text;

		// @rule: Remove these properties after all process.
		unset( $this->introtext );
		unset( $this->text );
	}

	/**
	 * This method invokes the trigger for events after the blog is saved
	 *
	 */
	public function triggerAfterSave()
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();

		// @trigger: onAfterEasyBlogSave
		$this->introtext	= '';
		$this->text			= '';
		$dispatcher->trigger( 'onAfterEasyBlogSave' , array(&$this, $this->isNew() ) );

		// @trigger: onAfterContentSave
		// @rule: Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext	= $this->intro;
		$this->text			= $this->content;

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$dispatcher->trigger( 'onContentAfterSave', array( 'easyblog.blog', &$this, $this->isNew() ) );
		}
		else
		{
			$dispatcher->trigger( 'onAfterContentSave', array( &$this , $this->isNew() ) );
		}

		// @rule: Since content plugins uses introtext and text columns, we'll just need to retrieve the values after it has been modified.
		$this->intro		= $this->introtext;
		$this->content		= $this->text;

		// @rule: Remove these properties after all process.
		unset( $this->introtext );
		unset( $this->text );
	}

	public function isNew()
	{
		// @rule: Determine if this record is new or not.
		$isNew  		= ( empty( $this->id) ) ? true : false;

		// if this is blog edit, then we should see the column isnew to determine
		// whether the post is really new or not.
		if( !$isNew )
		{
			$isNew 	= $this->isnew;
		}

		return $isNew;
	}

	/**
	 * Retrieves the blog image for this post. Each post may only contain 1 blog image.
	 *
	 * @param	null
	 * @return 	EasyBlogImage
	 */
	public function getImage()
	{
		static $image	= array();

		if( !isset( $image[ $this->id ] ) )
		{
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'image.php' );
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR .  'json.php' );

			if( !$this->image )
			{
				$image[ $this->id ]		= false;

				return false;
			}

			$json 			= new Services_JSON();
			$imageObject	= $json->decode( $this->image );

			if( !$imageObject )
			{
				$image 		= false;

				return false;
			}

			// Get the configuration object.
			$cfg 			= EasyBlogHelper::getConfig();

			// Let's see where should we find for this.
			$storagePath 	= '';
			$storageURI		= '';

			if( isset( $imageObject->place ) && $imageObject->place == 'shared' )
			{
				$storagePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $cfg->get( 'main_shared_path' ) , '/\\');
				$storageURI		= rtrim( JURI::root() , '/' ) . '/' . trim( str_ireplace( '\\' , '/' , $cfg->get( 'main_shared_path' ) ) , '/\\');
			}
			else
			{
				$place 			= $imageObject->place;
				$place 			= explode( ':' , $place );
				$place[1]		= (int) $place[1];

				$path 			= $imageObject->path;

				// Set the storage path
				$storagePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $cfg->get( 'main_image_path' ) , '/\\') . DIRECTORY_SEPARATOR . $place[1];

				// @task: Set the storage URI
				$storageURI		= rtrim( JURI::root() , '/' ) . '/' . trim( $cfg->get( 'main_image_path' ) , '/\\') . '/' . $place[1];
			}

			// Ensure that the item really exist before even going to do anything on the original image.
			// If the image was manually removed from FTP or any file explorer, this shouldn't yield any errors.
			$itemPath 			= $storagePath . DIRECTORY_SEPARATOR . trim( $imageObject->path , '/\\' );

			if( !JFile::exists( $itemPath ) )
			{
				// @TODO: Perhaps we should update $this->image with an empty value since image no longer exists.
				$image[ $this->id ]		= false;

				return false;
			}


			$image[ $this->id ]			= new EasyBlogImage( $imageObject->path , $storagePath , $storageURI );
		}

		return $image[ $this->id ];
	}

	/**
	 * Get a list of tag objects that are associated with this blog post.
	 *
	 * @access	public
	 * @param	null
	 * @return	Array	An Array of TableTag objects.
	 */
	public function getTags()
	{
		// @task: Get the tags relations model.
		$model	= EasyBlogHelper::getModel( 'PostTag' );

		$result	= $model->getBlogTags( $this->id );

		return $result;
	}
}
