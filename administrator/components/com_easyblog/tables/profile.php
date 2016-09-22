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
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );

class EasyBlogTableProfile extends EasyBlogTable
{
	var $id				= null;
	var $title			= null;
	var $nickname		= null;
	var $avatar			= null;
	var $description	= null;
	var $biography		= null;
	var $url			= null;
	var $params			= null;
	var $user			= null;
	var $permalink		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_users' , 'id' , $db );
	}

	function bind( $data, $ignore = array() )
	{
		parent::bind( $data, $ignore );

		$this->url	= $this->_appendHTTP( $this->url );

		//default to username for blogger permalink if empty
		if(empty($this->permalink))
		{
			$user	= JFactory::getUser($this->id);
			$this->permalink	= ( isset($user->username) ) ? $user->username : $this->id;
			$this->permalink 	= JFilterOutput::stringURLSafe( $this->permalink );
		}
		else
		{
			$this->permalink	= JFilterOutput::stringURLSafe($this->permalink);
		}

		return true;
	}

	public function store($updateNulls = false)
	{
		$isNew  = ( empty( $this->id ) ) ? true : false;

		$state 	= parent::store($updateNulls);
		$my 	= JFactory::getUser();

		if( $my->id == $this->id )
		{
			JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.update.profile' , $this->id , JText::_( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_UPDATE_PROFILE' ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.update.profile' , $this->id );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.update.profile' , $this->id );
		}

		if( ! $isNew )
		{
			$activity   = new stdClass();
			$activity->actor_id		= $my->id;
			$activity->target_id	= ( $my->id == $this->id ) ? '0' : $this->id;
			$activity->context_type	= 'profile';
			$activity->context_id	= $this->id;
			$activity->verb         = 'update';
			EasyBlogHelper::activityLog( $activity );
		}

		return $state;
	}

	function _createDefault( $id )
	{
		$db	= $this->getDBO();

		$user	= JFactory::getUser($id);

		$obj				= new stdClass();
		$obj->id 			= $user->id;
		$obj->nickname		= $user->name;
		$obj->avatar		= 'default_blogger.png';
		$obj->description 	= '';
		$obj->url			= '';
		$obj->params		= '';

		//default to username for blogger permalink
		$obj->permalink		= JFilterOutput::stringURLSafe( $user->username );

		// we do not insert 0 id into users table.
		if( $user->id )
		{
			$db->insertObject('#__easyblog_users', $obj);
		}

		return $obj;
	}

	/**
	 * override load method.
	 * if user record not found in eblog_profile, create one record.
	 *
	 */
	function load($id = null, $reset = true)
	{
		static $users = null;

		$id = ( $id == '0' ) ? null : $id;
		if( is_null($id) )
		{
			$this->bind( JFactory::getUser(0) );
			return $this;
		}

		if (empty($id))
		{
			// When the id is null or 0
			$this->bind( JFactory::getUser() );
			return $this;
		}

		if( !isset( $users[ $id ] ) )
		{
			if((! parent::load($id)) && ($id != 0))
			{
				$obj	= $this->_createDefault($id);
				$this->bind( $obj );
			}

			$users[ $id ] = clone $this;
		}

		$this->user	= JFactory::getUser( $id );
		$this->bind( $users[ $id ] );

		return $users[ $id ];
	}

	function setUser( $my )
	{
		$this->load( $my->id );
		$this->user = $my;
	}

	function getLink()
	{
		return EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->id);
	}

	function getName(){

		if($this->id == 0)
		{
			return JText::_('COM_EASYBLOG_GUEST');
		}

		$config 		= EasyBlogHelper::getConfig();
		$displayname    = $config->get('layout_nameformat');

		if( !$this->user )
		{
			$this->user	= JFactory::getUser( $this->id );
		}

		switch($displayname)
		{
			case "name" :
				$name = $this->user->name;
				break;
			case "username" :
				$name = $this->user->username;
				break;
			case "nickname" :
			default :
				$name = (empty($this->nickname)) ? $this->user->name : $this->nickname;
				break;
		}

		return EasyBlogStringHelper::escape( $name );
	}

	function getId(){
		return $this->id;
	}

	/**
	 * Retrieves the user's avatar
	 *
	 **/
	function getAvatar()
	{
		// not sure why when reach here, the $this->id is NULL, this is not suppose to happen.
		if( is_null( $this->id ) || $this->id == "")
		{
			$this->id = '0';
		}

		return EasyBlogHelper::getHelper( 'avatar' )->getAvatarURL( $this );
	}

	function getDescription($raw = false)
	{
		$description = $raw ? $this->description : nl2br($this->description);
		return $description;
	}

	/**
	 * Retrieves the user's twitter link
	 **/
	function getTwitterLink()
	{
		return EasyBlogHelper::getHelper( 'SocialShare' )->getLink( 'twitter' , $this->id );
	}

	/**
	 * Determines whether the blogger is a featured blogger
	 **/
	function isFeatured()
	{
		return EasyBlogHelper::isFeatured( 'blogger', $this->id );
	}

	/**
	 * Retrieves the biography from the specific blogger
	 **/
	function getBiography($raw = false)
	{
		static $loaded	= array();

		if( !isset( $loaded[ $this->id ] ) )
		{
			$status		= '';
			$config		= EasyBlogHelper::getConfig();

			if( $config->get( 'integrations_jomsocial_blogger_status' ) )
			{
				$path		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

				if( JFile::exists( $path ) )
				{
					require_once( $path );
					require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'string.php' );
					$user	= CFactory::getUser( $this->id );
					$status	= $user->getStatus();
				}
			}

			if( !empty( $this->biography ) && empty( $status ) )
			{
				$status	= $raw ? $this->biography : nl2br( $this->biography );
			}

			if( empty( $status ) )
			{
				$lang	= JFactory::getLanguage();
				$lang->load( 'com_easyblog' , JPATH_ROOT );

				$status	= JText::sprintf( 'COM_EASYBLOG_BIOGRAPHY_NOT_SET' , $this->getName() );
			}

			$loaded[ $this->id ]	= $status;
		}

		return $loaded[ $this->id ];
	}

	function getWebsite()
	{
		$url 	= $this->url == 'http://' ? '' : $this->url;

		return $url;
	}

	/*
	 * Generates profile links for the author.
	 *
	 * @param	null
	 * @return	string	The link to their profile
	 */
	public function getProfileLink( $defaultItemId = '' )
	{
		static $instance	= array();
		static $phpbbDB		= null;
		static $phpbbpath	= null;
		static $isBlogger	= array();

		// since it's for avatar, we'll follow the avatar's integration
		$config	= EasyBlogHelper::getConfig();
		$source	= $config->get( 'layout_avatarIntegration' );

		if(! $config->get('main_nonblogger_profile') )
		{
			// 1st check if this user a blogger or not.
			$showLink   = false;
			if( isset($isBlogger[$this->id]) )
			{
				$showLink   = $isBlogger[$this->id];
			}
			else
			{
				$showLink	= EasyBlogHelper::isBlogger( $this->id );
				$isBlogger[$this->id]   = $showLink;
			}

			if( !$showLink )
			{
				return 'javascript: void(0);';
			}
		}

		// phpbb case
		if($source == 'phpbb' && $phpbbDB === null)
		{
			$phpbbpath	= $config->get( 'layout_phpbb_path' );
			$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . $phpbbpath . DIRECTORY_SEPARATOR . 'config.php';

			if (JFile::exists($file))
			{
				require($file);

				$host		= $dbhost;
				$user		= $dbuser;
				$password	= $dbpasswd;
				$database	= $dbname;
				$prefix		= $table_prefix;
				$driver		= $dbms;
				$debug		= 0;

				$options	= array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );
				$phpbbDB	= JDatabase::getInstance( $options );
			} else {
				$phpbbDB	= false;
			}
		}
		if ($phpbbDB === false)
		{
			// can't get phpbb's config file, fallback to default profile link
			$source		= 'default';
		}

		// Always use the core linking if user does not wants this.
		if( !$config->get( 'layout_avatar_link_name') )
		{
			$source		= 'default';
		}

		// to ensure the passed in value is only a number
		$defaultItemId  = str_replace( '&Itemid=', '', $defaultItemId);

		// to ensure the uniqueness of the key
		$key = $source . '-' . $defaultItemId;

		// this is where the magic starts
		if (!isset($instance[$this->id][$key]))
		{
			$defaultItemId  = ( !empty( $defaultItemId ) ) ? '&Itemid=' . $defaultItemId : '';
			$defaultLink	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->id . $defaultItemId );

			switch ($source)
			{
				case 'k2':

					$file1 = JPATH_ROOT . '/components/com_k2/helpers/route.php';
					$file2 = JPATH_ROOT . '/components/com_k2/helpers/utilities.php';

					jimport( 'joomla.filesystem.file' );

					if( JFile::exists( $file1 ) && JFile::exists( $file2 ) )
					{
						require_once($file1);
						require_once($file2);

						$ret	= K2HelperRoute::getUserRoute( $this->id );
					}
					else
					{
						$ret 	= $defaultLink;
					}

					break;
				case 'mightyregistration':
					$ret	= JRoute::_( 'index.php?option=com_community&view=profile&user_id=' . $this->id , false );
					break;
				case 'communitybuilder':
					$ret	= JRoute::_('index.php?option=com_comprofiler&task=userProfile&user=' . $this->id, false);
					break;

				case 'easysocial':

					$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
					$ret 			= '';

					if( $easysocial->exists() )
					{
						$easysocial->init();

						$user 		= Foundry::user( $this->id );
						$ret 		= $user->getPermalink();
					}
					break;

				case 'jomsocial':
					$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
					$ret	= ( JFile::exists($file) && require_once($file) )? CRoute::_( 'index.php?option=com_community&view=profile&userid=' . $this->id ) : '';
					break;

				case 'kunena':

					$app = JFactory::getApplication(); 
					$menu = $app->getMenu();
					$menuItem = $menu->getItems( 'link', 'index.php?option=com_kunena&view=user', true);

					if ($menuItem) {
						$ret = JRoute::_('index.php?Itemid=' . $menuItem->id . '&userid=' . $this->id, false);
					} else {
						$ret = JRoute::_('index.php?option=com_kunena&func=fbprofile&userid=' . $this->id, false);	
					}
					
					break;

				case 'phpbb':
					$juser	= JFactory::getUser( $this->id );
					$query	= 'SELECT ' . $phpbbDB->quoteName('user_id') . ' '
							. 'FROM ' . $phpbbDB->quoteName('#__users') . ' WHERE LOWER('.$phpbbDB->quoteName('username') . ') = LOWER(' . $phpbbDB->quote($juser->username) . ') ';
					$phpbbDB->setQuery($query, 0, 1);
					$phpbbuserid	= $phpbbDB->loadResult();
					$ret	= $phpbbuserid ? JURI::root() . rtrim( $phpbbpath , '/' ) . '/memberlist.php?mode=viewprofile&u=' . $phpbbuserid : '';
					break;
				case 'anahita':
					$person	= KFactory::get( 'lib.anahita.se.person.helper' )->getPerson( $this->id );

					$ret	= $person->getURL();
					break;
				case 'easydiscuss':
					$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'router.php';
					$ret	= ( JFile::exists($file) && require_once($file) ) ? DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id='.$this->id, false ) : '';
					break;
				case 'gravatar':
				case 'default':
				default:
					$ret	= '';
					break;
			}
			$instance[$this->id][$key]	= $ret ? $ret : $defaultLink;
		}

		return $instance[$this->id][$key];
	}

	public function getPermalink()
	{
		$url	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->id );

		return $url;
	}

	function getParams(){
		return $this->params;
	}

	function getUserType(){
		return $this->user->usertype;
	}

	function _appendHTTP($url)
	{
		$returnStr	= '';
		$regex = '/^(http|https|ftp):\/\/*?/i';
		if (preg_match($regex, trim($url), $matches)) {
			$returnStr	= $url;
		} else {
			$returnStr	= 'http://' . $url;
		}

		return $returnStr;
	}

	function getRSS()
	{
		$config			= EasyBlogHelper::getConfig();

		if( $config->get( 'main_feedburnerblogger' ) )
		{
			$feedburner	= EasyBlogHelper::getTable( 'Feedburner', 'Table' );
			$feedburner->load($this->id);

			if(! empty($feedburner->url))
			{
				$rssLink    = $feedburner->url;
				return $rssLink;
			}
		}

		return EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=blogger&id=' . $this->id );
	}

	function getAtom()
	{
		return EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=blogger&id=' . $this->id, true );
	}

	function isOnline()
	{
		static	$loaded	= array();

		if( !isset( $loaded[ $this->id ] ) )
		{
			$db		= EasyBlogHelper::db();
			$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__session' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'userid' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'client_id') . '<>' . $db->Quote( 1 );
			$db->setQuery( $query );

			$loaded[ $this->id ]	= $db->loadResult() > 0 ? true : false;
		}
		return $loaded[ $this->id ];
	}

	/**
	 * Retrieve a list of tags created by this user
	 **/
	public function getTags()
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_tag' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) .'=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		$tags	= array();

		foreach( $rows as $row )
		{
			$tag	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
			$tag->bind( $row );
			$tags[]	= $tag;
		}

		return $tags;
	}

	/**
	 * Retrieve a list of tags created by this user
	 **/
	public function getCommentsCount()
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_comment' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) .'=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		return $db->loadResult();
	}
}
