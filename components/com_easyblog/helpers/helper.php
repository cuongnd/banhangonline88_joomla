<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file' );
jimport('joomla.filesystem.folder' );
jimport('joomla.html.parameter' );
jimport('joomla.application.component.model');
jimport('joomla.access.access');

require_once( JPATH_ROOT . '/components/com_easyblog/constants.php' );
require_once( EBLOG_CLASSES . '/themes.php' );
require_once( EBLOG_CLASSES . '/adsense.php' );
require_once( EBLOG_CLASSES . '/image.php' );
require_once( EBLOG_HELPERS . '/router.php' );
require_once( EBLOG_CLASSES . '/date.php' );
require_once( EBLOG_HELPERS . '/acl.php' );
require_once( EBLOG_HELPERS . '/comment.php' );
require_once( EBLOG_HELPERS . '/socialshare.php' );
require_once( EBLOG_HELPERS . '/xml.php' );

// For 3rd party compatibility with plugins
require_once( JPATH_ROOT . '/components/com_content/helpers/route.php' );

if( !function_exists( 'dump' ) )
{
}

if( ! class_exists( 'EasyBlogHelper' ) )
{

class EasyBlogHelper
{
	public static $headersLoaded = null;

	/**
	 * Get's the database object.
	 *
	 * @since	3.7
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function db()
	{
		$db 	= EasyBlogHelper::getHelper( 'DB' );

		return $db;
	}

	public static function compileJS()
	{
		$compile 	= JRequest::getVar( 'compile' );
		$minify 	= JRequest::getVar( 'minify' );

		if( $compile )
		{
			require_once( EBLOG_CLASSES . '/compiler.php' );

			$minify 	= $minify ? true : false;
			$compiler 	= new EasyBlogCompiler();
			$compiler->compile( $minify );

			exit;
		}

	}

	public static function getJConfig()
	{
		$config 	= EasyBlogHelper::getHelper( 'JConfig' );
		return $config;
	}

	public static function getRegistry( $contents = '' )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'registry.php' );

		$registry 	= new EasyBlogRegistry( $contents );

		return $registry;
	}

	public static function getForm( $name = '' , $contents = '' , $manifestFile , $xpath = false )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'form.php' );

		$form 		= new EasyBlogFormHelper( $name , $contents , $manifestFile , $xpath );

		return $form;
	}

	public static function getDate( $current = '', $tzoffset = null )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'date.php' );

		$date		= new EasyBlogDate( $current, $tzoffset );

		return $date;
	}

	public static function getToken( $contents = '' )
	{
		$version 	= EasyBlogHelper::getJoomlaVersion();

		if( $version >= '1.6' )
		{
			return JFactory::getSession()->getFormToken();
		}


		return JUtility::getToken();
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @param	string	$helper	The helper class . Class name should be the same name as the file. e.g EasyBlogXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function getHelper( $helper )
	{
		static $obj	= array();

		if( !isset( $obj[ $helper ] ) )
		{
			$file	= EBLOG_HELPERS . DIRECTORY_SEPARATOR . JString::strtolower( $helper ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$class	= 'EasyBlog' . ucfirst( $helper ) . 'Helper';

				$obj[ $helper ]	= new $class();
			}
			else
			{
				$obj[ $helper ]	= false;
			}
		}

		return $obj[ $helper ];
	}

	public static function getPagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature]))
		{
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'pagination.php' );
			$pagination	= new EasyBlogPagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = &$pagination;
		}

		return $instances[$signature];
	}

	public static function addJomsocialPoint( $action , $userId = 0 )
	{
		$my	= JFactory::getUser();

		if( !empty( $userId ) )
		{
			$my	= JFactory::getUser( $userId );
		}

		$config	= EasyBlogHelper::getConfig();

		if( $my->id != 0 && $config->get('main_jomsocial_userpoint') )
		{
			$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';

			if( JFile::exists( $jsUserPoint ) )
			{
				require_once( $jsUserPoint );
				CUserPoints::assignPoint( $action , $my->id );
			}
		}
		return true;
	}

	public static function removeJomsocialActivity( $blog )
	{
		$config		= EasyBlogHelper::getConfig();

		if(! $config->get( 'integrations_jomsocial_unpublish_remove_activity' ) )
		{
			return false;
		}

		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easyblog' , JPATH_ROOT );

		if( !JFile::exists( $jsCoreFile ) )
		{
			return false;
		}

		require_once( $jsCoreFile );

		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::remove('easyblog', $blog->id );

	}

	public static function addJomsocialActivity( $blog , $command , $title , $actor , $contents )
	{
		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= EasyBlogHelper::getConfig();
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easyblog' , JPATH_ROOT );

		if( !JFile::exists( $jsCoreFile ) )
		{
			return false;
		}
		require_once( $jsCoreFile );

		$obj			= new stdClass();
		$obj->title		= $title;
		$obj->content	= $contents;
		$obj->cmd 		= $command;

		if( $config->get( 'integrations_jomsocial_activity_likes' ) )
		{
			$obj->like_id	= $blog->id;
			$obj->like_type	= 'com_easyblog';
		}

		if( $config->get( 'integrations_jomsocial_activity_comments' ) )
		{
			$obj->comment_id	= $blog->id;
			$obj->comment_type	= 'com_easyblog';
		}

		$obj->actor		= $actor;
		$obj->target	= 0;
		$obj->app		= 'easyblog';
		$obj->cid		= $blog->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	/*
	 * type - string - info | warning | error
	 */

	public static function setMessageQueue($message, $type = 'info')
	{
		$session 	= JFactory::getSession();

		$msgObj				= new stdClass();
		$msgObj->message	= $message;
		$msgObj->type		= strtolower($type);

		//save messsage into session
		$session->set('eblog.message.queue', $msgObj, 'EBLOG.MESSAGE');

	}

	public static function getMessageQueue()
	{
		$session	= JFactory::getSession();
		$msgObj		= $session->get('eblog.message.queue', null, 'EBLOG.MESSAGE');

		//clear messsage into session
		$session->set('eblog.message.queue', null, 'EBLOG.MESSAGE');

		return $msgObj;
	}

	public static function getPermalink( $title )
	{
		$permalink	= EasyBlogRouter::generatePermalink( $title );

		// Make sure no such permalink exists.
		$originalSlug	= $permalink;
		$i				= 1;
		while( EasyBlogRouter::_isBlogPermalinkExists( $permalink ) )
		{
			$permalink	= $originalSlug . '-' . $i;
			$i++;
		}

		return $permalink;
	}

	/**
	 * @deprecated since 3.5
	 *
	 */
	public static function getNotification()
	{
		return self::getHelper( 'Notification' );
	}

	public static function getMailQueue()
	{
		static $mailq = false;

		if( !$mailq )
		{
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mailqueue.php' );

			$mailq	= new EMailQueue();
		}
		return $mailq;

	}

	public static function getRegistor()
	{
		static $registor = false;

		if( !$registor )
		{
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'registration.php' );

			$registor	= new ERegistration();
		}
		return $registor;

	}

	public static function getOnlineParser()
	{
		$data		= new stdClass();

		// Get the xml file
		$site		= EBLOG_UPDATES_SERVER;
		$xml		= 'stackideas.xml';
		$contents	= '';

		$handle		= @fsockopen( $site , 80, $errno, $errstr, 30);

		if( !$handle )
			return false;

		$out	= "GET /$xml HTTP/1.1\r\n";
		$out	.= "Host: $site\r\n";
		$out	.= "Connection: Close\r\n\r\n";

		fwrite($handle, $out);

		$body		= false;

		while( !feof( $handle ) )
		{
			$return	= fgets( $handle , 1024 );

			if( $body )
			{
				$contents	.= $return;
			}

			if( $return == "\r\n" )
			{
				$body	= true;
			}
		}
		fclose($handle);

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'xml.php' );

		$parser 	= new EasyBlogXMLHelper( $contents );

		return $parser;
	}

	private static function getLocalParser()
	{
		$contents	= JFile::read( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'easyblog.xml' );

		$parser 	= new EasyBlogXMLHelper( $contents );

		return $parser;
	}

	public static function getLocalVersion( $buildOnly = false )
	{
		$parser	= EasyBlogHelper::getLocalParser();

		if( !$parser )
		{
			return false;
		}

		$data	= $parser->getVersion();

		if( $buildOnly )
		{
			$data	= explode( '.' , $data );
			return $data[2];
		}
		return $data;
	}

	/**
	 * Gets the latest version of EasyBlog from server.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public static function getLatestVersion()
	{
		$parser 	= self::getOnlineParser();

		if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
		{
			$version 	= $parser->xpath( 'easyblog' );
			$version 	= (string) $version[0]->version[0];

			if( !$version )
			{
				return false;
			}

			return $version;
		}

		$element	= $parser->document->getElementByPath( 'easyblog/version' );

		return $element->data();
	}

	/**
	 * Retrieves recent news from site.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public static function getRecentNews()
	{
		$parser	= EasyBlogHelper::getOnlineParser();

		if( !$parser )
		{
			return false;
		}

		$result 	= array();

		if( EasyBlogHelper::getJoomlaVersion() >= '3.0')
		{
			$news 	= $parser->xpath( 'easyblog/news' );
			$news 	= $news[0];

			foreach( $news->children() as $item )
			{
				$obj 			= new stdClass();
				$obj->title		= (string) $item->title;
				$obj->desc 		= (string) $item->description;
				$obj->date 		= (string) $item->pubdate;

				$result[]		= $obj;
			}

		}
		else
		{
			$items	= $parser->document->getElementByPath('easyblog/news');

			foreach($items->children() as $item)
			{
				$element	= $item->getElementByPath( 'title' );
				$obj		= new stdClass();
				$obj->title	= $element->data();
				$element	= $item->getElementByPath( 'description' );
				$obj->desc	= $element->data();
				$element	= $item->getElementByPath( 'pubdate' );
				$obj->date	= $element->data();
				$result[]		= $obj;
			}
		}

		return $result;
	}

	public static function getSubscriptionbyUser( $userId )
	{
		$db = EasyBlogHelper::db();

		$query	=	"SELECT `id`, 'subscription' as `type`, `post_id` as `cid`, `user_id`, `created` FROM " . $db->nameQuote('#__easyblog_post_subscription') . " WHERE `user_id` = " . $db->Quote($userId);
		$query	.= 	" UNION SELECT `id`, 'categorysubscription' as `type`, `category_id` as `cid`, `user_id`, `created` FROM " . $db->nameQuote('#__easyblog_category_subscription') . " WHERE `user_id` = " . $db->Quote($userId);
		$query	.= 	" UNION SELECT `id`, 'bloggersubscription' as `type`, `blogger_id` as `cid`, `user_id`, `created` FROM " . $db->nameQuote('#__easyblog_blogger_subscription') . " WHERE `user_id` = " . $db->Quote($userId);
		$query	.= 	" UNION SELECT `id`, 'teamsubscription' as `type`, `team_id` as `cid`, `user_id`, `created` FROM " . $db->nameQuote('#__easyblog_team_subscription') . " WHERE `user_id` = " . $db->Quote($userId);
		$query	.= 	" UNION SELECT `id`, 'sitesubscription' as `type`, '0' as `cid`, `user_id`, `created` FROM " . $db->nameQuote('#__easyblog_site_subscription') . " WHERE `user_id` = " . $db->Quote($userId);
		$query	.=	" ORDER BY `type` ";

		$db->setQuery($query);

		$subs = $db->loadObjectlist();

		if(empty($subs))
		{
			return false;
		}

		return $subs;
	}

	public static function getTable( $tableName )
	{
		require_once( JPATH_ADMINISTRATOR . '/components/com_easyblog/tables/table.php' );
		$table 	= EasyBlogTable::getInstance( $tableName );

		return $table;
	}

	public static function getModel( $name , $backend = false )
	{
		static $model = array();

		if( !isset( $model[ $name ] ) )
		{
			$file	= JString::strtolower( $name );
			$path 	= JPATH_ROOT;

			if( $backend )
			{
				$path 	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator';
			}

			$path	= $path . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $file . '.php';

			jimport('joomla.filesystem.path');
			if ( JFolder::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass		= 'EasyBlogModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
				require_once( $path );


			$model[ $name ] = new $modelClass();
		}

		return $model[ $name ];
	}

	public static function getConfig()
	{
		static $config	= null;

		if( is_null( $config ) )
		{
			//load default ini data first
			$ini		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'configuration.ini';
			$raw		= JFile::read($ini);

			$default 	= EasyBlogHelper::getRegistry( $raw );

			//get config stored in db
			$dbConfig	= EasyBlogHelper::getTable( 'configs' , 'Table' );
			$dbConfig->load( 'config' );


			// Load the stored config as a registry
			$stored 	= EasyBlogHelper::getRegistry( $dbConfig->params );

			EasyBlogHelper::getHelper( 'Registry' )->extend( $default , $stored );

			$config 	= $default;
		}

		return $config;
	}

	/*
	 * Method used to determine whether the user a guest or logged in user.
	 * return : boolean
	 */
	public static function isLoggedIn()
	{
		$my			= JFactory::getUser();
		$loggedIn	= (empty($my) || $my->id == 0) ? false : true;

		return $loggedIn;
	}

	public static function isSiteAdmin( $userId = null )
	{
		$my	= JFactory::getUser( $userId );

		$admin = false;
		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$admin	= $my->authorise('core.admin');
		}
		else
		{
			$admin	= $my->usertype == 'Super Administrator' || $my->usertype == 'Administrator' ? true : false;
		}
		return $admin;
	}

	public static function isTeamAdmin()
	{
		static $isAdmin	= null;

		$my	= JFactory::getUser();

		if($my->id == 0)
		{
			return false;
		}
		else
		{
			if( !isset( $isAdmin[ $my->id ] ) )
			{
				$model			= self::getModel( 'TeamBlogs' );

				$isTeamAdmin	= $model->checkIsTeamAdmin($my->id);

				if($isTeamAdmin === false)
				{
					$isTeamAdmin	=EasyBlogHelper::isSiteAdmin();
				}

				$isAdmin[$my->id]	= $isTeamAdmin;
			}

			return $isAdmin[$my->id];
		}
	}

	/*
	 * Method used to determine the current user is the owner of a blog post.
	 * return : boolean
	 */
	public static function isMineBlog($userId1, $userId2)
	{
		return ($userId1 == $userId2) && (($userId1 != 0) || ($userId2 != 0) );
	}

	/**
	 * Gets the comment responses for an article
	 * Deprecated since 2.0
	 **/
	public static function getCommentCount( $articleId )
	{
		$blog	= EasyBlogHelper::getTable( 'Blog' );
		$blog->load( $articleId );

		return EasyBlogHelper::getHelper( 'Comment' )->getCommentCount( $blog );
	}

	public static function formatTeamMembers( $data )
	{
		$memberInfo = array();
		for($i = 0; $i < count($data); $i++)
		{
			$member	=& $data[$i];

			$profile = EasyBlogHelper::getTable( 'Profile', 'Table' );
			$profile->load( $member->user_id );
			$profile->displayName = $profile->getName();
			$memberInfo[] = $profile;
		}

		return $memberInfo;
	}

	public static function removeFeaturedImage( $text )
	{
		$pattern = '#<img class="featured[^>]*>#i';
		$pattern = '#<a class="easyblog-thumb-preview featured(.*)</a>#i';
		return preg_replace( $pattern , '' , $text , 1 );
	}

	public static function removeGallery( $text )
	{
		$pattern	= '#<div class="easyblog-placeholder-gallery"(.*)</div>#is';

		return preg_replace( $pattern , '' , $text );
	}

	public static function formatDraftBlog( $data )
	{
		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();
		$config			= EasyBlogHelper::getConfig();

		if(! empty($data))
		{
			$modelPT	= self::getModel( 'PostTag' );

			for($i = 0; $i < count($data); $i++)
			{
				$row	=& $data[$i];

				$profile = EasyBlogHelper::getTable( 'Profile', 'Table' );
				$profile->load( $row->created_by );
				$row->avatar		= $profile->getAvatar();
				$row->displayName	= $profile->getName();

				$date				= EasyBlogHelper::getDate( $row->created );
				$row->totalComments	= 0;
				$row->isFeatured	= 0;

				$row->category		= (empty($row->category)) ? JText::_('COM_EASYBLOG_UNCATEGORIZED') : $row->category;
				$row->text			= '';

				$requireVerification = false;
				if($config->get('main_password_protect', true) && !empty($row->blogpassword))
				{
					$row->title	= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $row->title);
				}

				//blog summary
				$row->summary	= JString::substr(strip_tags($row->content), 0, 200);


				$row->introtext	= $row->intro;
				$row->excerpt	= $row->introtext;
				$row->content	= $row->text;

				//get number of tag count.
				$row->tagcount	= 0;

				$row->_tags		= array();
				$blogTags		= array();

				if( !empty($row->tags) )
				{
					$blogTags	= explode( ',', $row->tags);
				}

				if(! empty($blogTags))
				{
					$row->_tags	= $blogTags;
					$row->tagcount	= count( $blogTags );
				}
				else
				{
					$row->tags	= JText::_('COM_EASYBLOG_UNTAGGED');
				}

				$row->comments	= array();

			}//end foreach
		}//end if

		return $data;
	}

	public static function verifyBlogPassword($crypt, $id)
	{
		if(!empty($crypt) && !empty($id))
		{
			$jSession = JFactory::getSession();
			$password = $jSession->get('PROTECTEDBLOG_'.$id, '', 'EASYBLOG');

			if($crypt == $password)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * This would inject a rel=nofollow attribute into anchor links.
	 *
	 * @access	public
	 * @param 	string	$content 	The content subject.
	 * @return 	string 				The content which is fixed.
	 */
	public static function addNoFollow( $content )
	{
		// @rule: Try to replace any rel tag that already exist.
		$pattern	= '/rel=[^>]*"/i';

		preg_match( $pattern , $content , $matches );

		if( $matches )
		{
			foreach( $matches as $match )
			{
				$result		= str_ireplace( 'rel="' , 'rel="nofollow ' , $match );
				$content	= str_ireplace( $match , $result , $content );
			}
		}
		else
		{
			$content		= str_ireplace( '<a' , '<a rel="nofollow"' , $content );
		}

		return $content;
	}

	/**
	 * Responsible to format the blog object for the microblog posts.
	 */
	public static function formatMicroblog( &$row )
	{
		$allowed 	= array( EBLOG_MICROBLOG_PHOTO , EBLOG_MICROBLOG_VIDEO , EBLOG_MICROBLOG_QUOTE , EBLOG_MICROBLOG_LINK );

		if( !in_array( $row->source , $allowed ) )
		{
			return;
		}


		$path		= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'microblog' . DIRECTORY_SEPARATOR . strtolower( $row->source ) . '.php';

		if( !JFile::exists( $path ) )
		{
			return false;
		}

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'microblog' . DIRECTORY_SEPARATOR . strtolower( $row->source ) . '.php' );

		$className	= 'EasyBlogMicroBlog' . ucfirst( $row->source );

		if( !class_exists( $className ) )
		{
			return false;
		}

		$microblog		= new $className;
		$microblog->processOutput( $row );
	}

	/**
	 * Responsible to format the blog posts and append neccessary data.
	 *
	 * @access	public
	 * @param	Array	$data			An array of blog posts.
	 * @param	boolean	$loadComments	Determines whether or not to load the comments into the object.
	 * @param	boolean $removeFeaturedImage	Determines whether or not to remove featured image from the content.
	 * @param	boolean	$loadVideo		If true, video codes will be processed.
	 * @param	boolean	$frontpage		Determines whether this is for the front page or not.
	 * @return	Array	An array of formatted blog posts.
	 */
	public static function formatBlog( $data , $loadComments = false , $removeFeaturedImage = true, $loadVideo = true , $frontpage = false , $loadGallery = true )
	{
		$app	= JFactory::getApplication();
		$params	= $app->getParams('com_easyblog');
		$model	= EasyBlogHelper::getModel( 'Blog' );
		$config	= EasyBlogHelper::getConfig();

		// @rule: If nothing is supplied, just return the empty data.
		if( empty( $data ) )
		{
			return $data;
		}

		// @task: Get the tags relations model.
		$modelPT	= EasyBlogHelper::getModel( 'PostTag' );

		// @task : Resultset data
		$result 	= array();

		for($i = 0; $i < count($data); $i++)
		{
			$row				=& $data[$i];
			$blog 				= EasyBlogHelper::getTable( 'Blog' );
			$blog->bind( $row );

			// @task: Since the $blog object does not contain 'team_id', we need to set this here.
			if( isset( $row->team_id ) )
			{
				$blog->team_id 		= $row->team_id;
			}

			// @task: Since the $blog object does not contain 'category', we need to set this here.
			$blog->category 		= $row->category;
			$blog->featuredImage	= isset( $row->featuredImage ) ? $row->featuredImage : '';

			$profile				= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$profile->load( $blog->created_by );

			// @legacy The following variables are no longer used in 3.5
			// @since 3.5
			$blog->avatar			= $profile->getAvatar();
			$blog->avatarLink		= $profile->getProfileLink();
			$blog->displayName		= $profile->getName();

			// @Assign dynamic properties that must exist everytime formatBlog is called
			// We can't rely on ->author because CB plugins would mess things up.
			$blog->author			= $profile;
			$blog->blogger			= $profile;

			$blog->isFeatured		= EasyBlogHelper::isFeatured('post', $blog->id);
			$blog->category			= (empty($blog->category)) ? JText::_('COM_EASYBLOG_UNCATEGORIZED') : JText::_( $blog->category );

			// @task: Detect password protections.
			$requireVerification	= false;
			$tmpTitle				= $blog->title;
			if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
			{
				$blog->title				= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $blog->title);
				$requireVerification	= true;
			}

			// @rule: If user already authenticated with the correct password, we will hide the password
			if( $requireVerification && EasyBlogHelper::verifyBlogPassword( $blog->blogpassword , $blog->id ) )
			{
				$blog->title			= $tmpTitle;
				$blog->blogpassword		= '';
			}

			// @rule: Initialize all variables
			$blog->videos		= array();
			$blog->galleries	= array();
			$blog->albums 		= array();
			$blog->audios		= array();

			// @rule: Before anything get's processed we need to format all the microblog posts first.
			if( !empty( $blog->source ) )
			{
				self::formatMicroblog( $blog );
			}

			// @rule: Detect if the content requires a read more link.
			$blog->readmore 	= EasyBlogHelper::requireReadmore( $blog );

			// @rule: Remove any adsense codes from the content.
			$blog->intro		= EasyBlogGoogleAdsense::stripAdsenseCode( $blog->intro );
			$blog->content		= EasyBlogGoogleAdsense::stripAdsenseCode( $blog->content );

			// @rule: Content truncations.
			EasyBlogHelper::truncateContent( $blog , $loadVideo , $frontpage , $loadGallery );

			// @task: Legacy fix for blog posts prior to 3.5
			// Remove first image from featured post
			if( $removeFeaturedImage && $blog->isFeatured )
			{
				$blog->text	= EasyBlogHelper::removeFeaturedImage( $blog->text );
			}

			// @rule: Add nofollow tags if necessary
			if( $config->get( 'main_anchor_nofollow') )
			{
				$blog->text	= self::addNoFollow( $blog->text );
			}

			// @rule: $limitstart variable is required by content plugins.
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

			// @trigger: onEasyBlogPrepareContent
			JPluginHelper::importPlugin( 'easyblog' );
			EasyBlogHelper::triggerEvent( 'easyblog.prepareContent' , $blog , $params , $limitstart );

			$blog->introtext	= $blog->intro;

			// @trigger: onPrepareContent / onContentPrepare
			EasyBlogHelper::triggerEvent( 'prepareContent' , $blog , $params , $limitstart );

			$blog->excerpt		= $blog->introtext;
			$blog->content		= $blog->text;
			//onPrepareContent trigger end

			// @rule: Assign tags to the custom properties.
			$blog->tags			= $modelPT->getBlogTags( $blog->id );

			// @rule: Assign total comments in this blog post.
			$blog->totalComments	= EasyBlogHelper::getHelper( 'Comment' )->getCommentCount( $blog );
			$blog->comments			= array();

			if( $loadComments )
			{
				$blog->comments	= EasyBlogHelper::getHelper( 'Comment' )->getBlogComment( $blog->id, $config->get( 'layout_showcommentcount' , 3 ) , 'desc', true );
			}

			$blog->event = new stdClass();

			// @trigger: onContentAfterTitle / onAfterDisplayTitle
			$results		= EasyBlogHelper::triggerEvent( 'afterDisplayTitle' , $blog , $params , $limitstart );
			$blog->event->afterDisplayTitle	= JString::trim( implode( "\n" , $results ) );

			// @trigger: onContentAfterTitle / onAfterDisplayTitle
			$results		= EasyBlogHelper::triggerEvent( 'beforeDisplayContent' , $blog , $params , $limitstart );
			$blog->event->beforeDisplayContent	= JString::trim( implode( "\n" , $results ) );

			// @trigger: onContentAfterTitle / onAfterDisplayTitle
			$results		= EasyBlogHelper::triggerEvent( 'afterDisplayContent' , $blog , $params , $limitstart );
			$blog->event->afterDisplayContent	= JString::trim( implode( "\n" , $results ) );

			// Facebook Like integrations
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'facebook.php' );
			$facebookLike		= EasyBlogFacebookLikes::getLikeHTML( $blog );
			$blog->facebookLike	= $facebookLike;

			$result[]	= $blog;
		}

		return $result;
	}

	/**
	 * Reverse of strip_tags
	 *
	 */
	function strip_only($str, $tags, $stripContent = false)
	{
		$content = '';
		if(!is_array($tags))
		{
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));

			if(end($tags) == '')
			{
				array_pop($tags);
			}
		}

		foreach($tags as $tag)
		{
			if ($stripContent)
			{
				$content = '(.+</'.$tag.'[^>]*>|)';
			}
			$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
		}
		return $str;
	}
	/**
	 * Truncate's blog post with the respective settings.
	 *
	 * @access	public
	 */
	public static function truncateContent( &$row , $loadVideo = false , $frontpage = false , $loadGallery = true )
	{
		$config			= EasyBlogHelper::getConfig();
		$truncate		= true;
		$maxCharacter	= $config->get('layout_maxlengthasintrotext', 150);

		// @task: Maximum characters should not be lesser than 0
		$maxCharacter	= $maxCharacter <= 0 ? 150 : $maxCharacter;

		// Check if truncation is really necessary because if introtext is already present, just use it.
		if( !empty($row->intro) && !empty($row->content) )
		{
			// We do not want the script to truncate anything since we'll just be using the intro part.
			$truncate			= false;
		}

		// @task: If truncation is not necessary or the intro text is empty, let's just use the content.
		if( !$config->get( 'layout_blogasintrotext' ) || !$truncate )
		{

			//here we process the video and get the links.
			if( $loadVideo )
			{
				$row->intro		= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $row->intro );
				$row->content	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $row->content );
			}

			// @rule: Process audio files.
			$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->intro );
			$row->content		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->content );

			if( ( ( $config->get( 'main_image_gallery_frontpage' ) && $frontpage ) || !$frontpage ) && $loadGallery )
			{
				$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->process( $row->intro , $row->created_by );
				$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->process( $row->content , $row->created_by );

				// Process jomsocial albums
				$row->intro		= EasyBlogHelper::getHelper( 'Album' )->process( $row->intro , $row->created_by );
				$row->content	= EasyBlogHelper::getHelper( 'Album' )->process( $row->content , $row->created_by );
			}

			// @task: Strip out video tags
			$row->intro		= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->content );

			// @task: Strip out audio tags
			$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->content );

			// @task: Strip out gallery tags
			$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->content );

			// @task: Strip out album tags
			$row->intro		= EasyBlogHelper::getHelper( 'Album' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Album' )->strip( $row->content );

			// @rule: Once the gallery is already processed above, we will need to strip out the gallery contents since it may contain some unwanted codes
			// @2.0: <input class="easyblog-gallery"
			// @3.5: {ebgallery:'name'}
			$row->intro			= EasyBlogHelper::removeGallery( $row->intro );
			$row->content		= EasyBlogHelper::removeGallery( $row->content );

			if( $frontpage && $config->get( 'main_truncate_image_position' ) == 'hidden' )
			{
				// Need to remove images, and videos.
				$row->intro = self::strip_only( $row->intro , '<img>' );
				$row->content = self::strip_only( $row->content , '<img>' );
			}


			$row->text			= empty( $row->intro ) ? $row->content : $row->intro;

			return $row;
		}

		// @rule: If this is a normal blog post, we match them manually
		if( isset($row->source) && ( !$row->source || empty( $row->source ) ) )
		{
			// @rule: Try to match all videos from the blog post first.
			$row->videos		= EasyBlogHelper::getHelper( 'Videos' )->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->galleries	= EasyBlogHelper::getHelper( 'Gallery' )->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->audios 		= EasyBlogHelper::getHelper( 'Audio' )->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->albums		= EasyBlogHelper::getHelper( 'Album' )->getHTMLArray( $row->intro . $row->content );
		}

		// @task: Here we need to strip out all items that are embedded since they are now not required because they'll be truncated.
		// @task: Strip out video tags
		$row->intro		= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->content );

		// @task: Strip out audio tags
		$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->content );

		// @task: Strip out gallery tags
		$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->content );

		// @task: Strip out album tags
		$row->intro		= EasyBlogHelper::getHelper( 'Album' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Album' )->strip( $row->content );

		// This is the combined content of the intro and the fulltext
		$content		= $row->intro . $row->content;

		switch( $config->get( 'main_truncate_type' ) )
		{
			case 'chars':

				// @task: Remove Zemanta tags
				$content	= EasyBlogHelper::removeZemantaTags( $content );

				// Remove uneccessary html tags to avoid unclosed html tags
				$content	= strip_tags( $content );

				// Remove blank spaces since the word calculation should not include new lines or blanks.
				$content	= trim( $content );

				// @task: Let's truncate the content now.
				$row->text	= JString::substr( $content , 0 , $maxCharacter);
			break;


			case 'words':
				$tag		= false;
				$count		= 0;
				$output		= '';

				// @task: Remove Zemanta tags
				$content		= EasyBlogHelper::removeZemantaTags( $content );

				// Remove uneccessary html tags to avoid unclosed html tags
				$content		= strip_tags( $content );

				$chunks		= preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

				foreach($chunks as $piece)
				{

					if( !$tag || stripos($piece, '>') !== false )
					{
						$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
					}

					if( !$tag && trim($piece) == '' )
					{
						$count++;
					}

					if( $count > $maxCharacter && !$tag )
					{
						break;
					}

					$output .= $piece;
				}

				unset($chunks);
				$row->text	= $output;

			break;



			case 'break':
				$position	= 0;
				$matches	= array();
				$tag		= '<br';

				$matches	= array();

				do
				{
					$position	= @JString::strpos( strtolower( $content ) , $tag , $position + 1 );

					if( $position !== false )
					{
						$matches[]	= $position;
					}
				} while( $position !== false );

				$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

				if( count( $matches ) > $maxTag )
				{
					$row->text	= JString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 6 );
					$row->readmore	= true;
				}
				else
				{
					$row->text	= $content;
					$row->readmore	= false;
				}

			break;


			default:
				$position	= 0;
				$matches	= array();
				$tag		= '</p>';

				// @task: If configured to not display any media items on frontpage, we need to remove it here.
				if( $frontpage && $config->get( 'main_truncate_image_position' ) == 'hidden' )
				{
					// Need to remove images, and videos.
					$content 	= self::strip_only( $content , '<img>' );
				}

				do
				{
					$position	= @JString::strpos( strtolower( $content ) , $tag , $position + 1 );

					if( $position !== false )
					{
						$matches[]	= $position;
					}
				} while( $position !== false );

				// @TODO: Configurable
				$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

				if( count( $matches ) > $maxTag )
				{
					$row->text	= JString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 4 );

					$htmlTagPattern    		= array('/\<div/i', '/\<table/i');
					$htmlCloseTagPattern   	= array('/\<\/div\>/is', '/\<\/table\>/is');
					$htmlCloseTag   		= array('</div>', '</table>');

					for( $i = 0; $i < count($htmlTagPattern); $i++ )
					{

						$htmlItem   			= $htmlTagPattern[$i];
						$htmlItemClosePattern	= $htmlCloseTagPattern[$i];
						$htmlItemCloseTag		= $htmlCloseTag[$i];

						preg_match_all( $htmlItem , strtolower( $row->text ), $totalOpenItem );

						if( isset( $totalOpenItem[0] ) && !empty( $totalOpenItem[0] ) )
						{
							$totalOpenItem	= count( $totalOpenItem[0] );

							preg_match_all( $htmlItemClosePattern , strtolower( $row->text ) , $totalClosedItem );

							$totalClosedItem	= count( $totalClosedItem[0] );

							$totalItemToAdd	= $totalOpenItem - $totalClosedItem;

							if( $totalItemToAdd > 0 )
							{
								for( $y = 1; $y <= $totalItemToAdd; $y++ )
								{
									$row->text 	.= $htmlItemCloseTag;
								}
							}
						}
					}

					$row->readmore	= true;
				}
				else
				{
					$row->text		= $content;
					$row->readmore	= false;
				}

			break;
		}

		//var_dump($row );exit;

		if( $config->get( 'main_truncate_ellipses' ) && isset( $row->readmore) && $row->readmore )
		{
			$row->text	.= JText::_( 'COM_EASYBLOG_ELLIPSES' );
		}

		if( isset($row->source) && ( !$row->source || empty( $row->source ) ) )
		{
			// @task: Determine the position of media items that should be included in the content.
			$embedHTML			= '';
			$embedVideoHTML		= '';
			$imgHTML            = '';

			if( !empty( $row->galleries ) )
			{
				$embedHTML		.= implode( '' , $row->galleries );
			}

			if( !empty( $row->audios ) )
			{
				$embedHTML		.= implode( '' , $row->audios );
			}

			if( !empty( $row->videos ) )
			{
				$embedVideoHTML		= implode( '' , $row->videos );
			}

			if( !empty( $row->albums ) )
			{
				$embedHTML		.= implode( '' , $row->albums );
			}

			// @legacy fix: For users prior to 3.5
			if( ( $config->get( 'main_truncate_type' ) == 'chars' ||  $config->get( 'main_truncate_type' ) == 'words' ) && !$row->getImage() )
			{
				// Append image in the post if truncation is done by characters
				if( ($config->get( 'main_teaser_image' ) && !$frontpage ) || ( $frontpage && $config->get('main_truncate_image_position') != 'hidden' ) )
				{
					// Match images that has preview.
					$pattern		= '/<a class="easyblog-thumb-preview"(.*?)<\/a>/is';

					preg_match( $pattern , $row->intro . $row->content , $matches );

					// Legacy images that doesn't have previews.
					if( empty( $matches ) )
					{
						$pattern		= '#<img[^>]*>#i';

						preg_match( $pattern , $row->intro . $row->content , $matches );
					}



					if( !empty( $matches ) )
					{
						if( $config->get( 'main_teaser_image_align' ) == 'float-l' || $config->get( 'main_teaser_image_align') == 'float-r' )
						{
							$imgHTML    = '<div class="teaser-image clearfix ' . $config->get( 'main_teaser_image_align' ) . '" style="margin:8px;max-width:98%;">' . $matches[ 0 ] . '</div>';
						}
						else
						{
							$imgHTML	= '<div class="teaser-image clearfix" style="margin:8px;max-width:98%;text-align: ' . $config->get( 'main_teaser_image_align' ) . ' !important;">' . $matches[ 0 ] . '</div>';

						}
					}
				}
			}



			// images
			if( $config->get( 'main_truncate_image_position') == 'top' && !empty( $imgHTML ) )
			{
				$row->text	= $imgHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_image_position') == 'bottom' && !empty( $imgHTML ) )
			{
				$row->text	= $row->text . $imgHTML;
			}


			// videos
			if( $config->get( 'main_truncate_video_position') == 'top' && !empty( $embedVideoHTML) )
			{
				$row->text	= $embedVideoHTML . '<br />' . $row->text;
			}
			else if( $config->get( 'main_truncate_video_position') == 'bottom' && !empty( $embedVideoHTML) )
			{
				$row->text	= $row->text . '<br />' . $embedVideoHTML;
			}


			// @task: Prepend the other media items in the start of the blog posts.
			if( $config->get( 'main_truncate_media_position') == 'top' && !empty( $embedHTML ) )
			{
				$row->text	= $embedHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_media_position') == 'bottom' && !empty( $embedHTML) )
			{
				$row->text	.= $embedHTML;
			}
		}

		return $row;
	}

	/**
	 * This method searches for built in tags and strips them off. This should only be used
	 * when you are trying to output some data that doesn't contain html tags.
	 */
	public static function stripEmbedTags( $content )
	{
		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content	= str_ireplace( '&quot;' , '"' , $content );

		$pattern	= array('/\{video:.*?\}/',
							'/\{"video":.*?\}/',
							'/\[embed=.*?\].*?\[\/embed\]/'
							);

		$replace    = array('','','');


		return preg_replace( $pattern , $replace , $content );
	}

	/**
	 * Determines if a content requires a read more link.
	 *
	 * @access	public
	 * @param 	StdClas	$row
	 */
	public static function requireReadmore( &$row )
	{
		$config 		= EasyBlogHelper::getConfig();
		$maxCharacter   = $config->get('layout_maxlengthasintrotext', 150);

		// Decide whether or not to show read more link
		$readmore		= true;

		if( $config->get( 'layout_respect_readmore' ) )
		{
			// When introtext is not empty and content is empty
			if( !empty( $row->intro ) && empty($row->content) )
			{
				if( JString::strlen( strip_tags( $row->intro ) ) > $maxCharacter && $config->get( 'layout_blogasintrotext' ) )
				{
					$readmore 		= true;
				}
				else
				{
					$readmore 		= false;
				}
			}

			// Backward compatibility, this is probably from an older version
			if( empty( $row->intro ) && !empty($row->content) )
			{
				if( JString::strlen( strip_tags( $row->content ) ) > $maxCharacter )
				{
					$readmore 		= true;
				}
				else
				{
					$readmore		= false;
				}
			}

			// New way of doing things where user explicitly set the read more line.
			if( !empty($row->intro) && !empty($row->content) )
			{
				$readmore		= true;
			}
		}

		return $readmore;
	}


	public static function removeZemantaTags( $content )
	{
		$output = preg_replace( '/<p class="zemanta-img-attribution"(.*?)<\/p>/is' , '' , $content );

		return $output;
	}

	public static function triggerEvent( $event , &$row , &$params , $limitstart )
	{
		$dispatcher = JDispatcher::getInstance();
		$version	= EasyBlogHelper::getJoomlaVersion();
		$events		= array(
								'1.5' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent' 			=> 'onPrepareContent',
													'afterDisplayTitle'			=> 'onAfterDisplayTitle',
													'beforeDisplayContent'		=> 'onBeforeDisplayContent',
													'afterDisplayContent'		=> 'onAfterDisplayContent',
													'beforeSave'				=> 'onBeforeContentSave'
												),
								'1.6' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												),
								'1.7' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												),
								'2.5' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												),
								'3.0' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												),
								'3.1' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												),
								'3.2' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												),
								'3.3' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												),
								'3.4' => array(
													'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
													'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
													'easyblog.commentCount'		=> 'onGetCommentCount',
													'prepareContent'			=> 'onContentPrepare',
													'afterDisplayTitle'			=> 'onContentAfterTitle',
													'beforeDisplayContent'		=> 'onContentBeforeDisplay',
													'afterDisplayContent'		=> 'onContentAfterDisplay',
													'beforeSave'				=> 'onContentBeforeSave'
												)
							);

		// Need to make this behave like how Joomla category behaves.
		if( !isset( $row->catid ) )
		{
			$row->catid	= $row->category_id;
		}

		if ($version >= '1.6') {
			$result = $dispatcher->trigger( $events[ $version ][ $event ] , array( 'easyblog.blog' , &$row , &$params , $limitstart ) );
		}
		else
		{
			$result = $dispatcher->trigger( $events[ $version ][ $event ] , array( &$row , &$params , $limitstart ) );
		}

		// Remove unwanted fields.
		unset( $row->catid );

		return $result;
	}

	public static function getExternalLink($link, $xhtml = false)
	{
		$uri	= JURI::getInstance();
		$domain	= $uri->toString( array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(EasyBlogRouter::_( $link, $xhtml , null, true ), '/');
	}

	public static function makeFeatured($type, $contentId)
	{
		$db		= EasyBlogHelper::db();

		$date 	= EasyBlogHelper::getDate();
		$query	= 'SELECT `id` FROM `#__easyblog_featured` WHERE `content_id` = ' . $db->Quote($contentId);
		$query	.= ' AND `type` = ' . $db->Quote($type);
		$db->setQuery($query);

		$fid	= $db->loadResult();

		if(empty($fid))
		{
			$obj = new stdClass();

			$my					= JFactory::getUser();

			$obj->content_id	= $contentId;
			$obj->type			= $type;
			$obj->created		= $date->toMySQL();

			$db->insertObject( '#__easyblog_featured' , $obj );

			if( $type == 'post' )
			{
				$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
				$blog->load( $contentId );

				$config				= EasyBlogHelper::getConfig();
				if( $config->get( 'integrations_jomsocial_feature_blog_activity' ) )
				{
					$lang		= JFactory::getLanguage();
					$lang->load( 'com_easyblog' , JPATH_ROOT );
					$title	= JString::substr( $blog->title , 0 , 30 ) . '...';

					$easyBlogItemid	= '';
					$mainframe 	= JFactory::getApplication();
					if( $mainframe->isAdmin() )
					{
						$easyBlogItemid	= EasyBlogRouter::getItemId('latest');
						$easyBlogItemid = '&Itemid=' . $easyBlogItemid;
					}

					$blogLink		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id='. $blog->id . $easyBlogItemid, false, true);

					$sh404exists	= EasyBlogRouter::isSh404Enabled();

					$mainframe 		= JFactory::getApplication();
					if( $mainframe->isAdmin() && $sh404exists )
					{
						$blogLink		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog&view=entry&id='. $blog->id . $easyBlogItemid;
					}

					$blogContent	= '';

					$requireVerification = false;
					if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
					{
						$row->title	= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $blog->title);
						$requireVerification = true;
					}

					if($requireVerification && !EasyBlogHelper::verifyBlogPassword($blog->blogpassword, $blog->id))
					{
						$theme = new CodeThemes();
						$theme->set('id', $blog->id);
						$theme->set('return', base64_encode( $blogLink ) );
						$blogContent	= $theme->fetch( 'blog.protected.php' );
					}
					else
					{
						$blogContent	= $blog->intro . $blog->content;

						// Get blog's image and use it as the cover photo.
						$image 			= '';

						if( $blog->getImage() )
						{
							$imageSource	= $blog->getImage()->getSource( 'frontpage' );

							if( $imageSource )
							{
								$image	= '<a href="' . $blogLink . '"><img src="' . $imageSource . '" style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"/></a>';
							}
						}
						else
						{
							// Try to find for an image in the content.
							$pattern		= '#<img[^>]*>#i';
							preg_match( $pattern , $blogContent , $matches );

							if( $matches )
							{
								$matches[0]		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"' , $matches[0 ] );

								$image 			= '<a href="' . $blogLink . '">' . $matches[0] . '</a>';
							}
						}

						// Strip unwanted data.
						$blogContent	= EasyBlogHelper::getHelper( 'Videos' )->strip( $blogContent );
						$blogContent	= EasyBlogGoogleAdsense::stripAdsenseCode( $blogContent );
						$blogContent	= JString::substr($blogContent, 0, $config->get( 'integrations_jomsocial_blogs_length', 250 ) ) . ' ...';

						// Remove all html tags from the content as we want to chop it down.
						$blogContent	= strip_tags( $blogContent );

						if( !empty( $image ) )
						{
							$blogContent 	= $image . $blogContent . '<div style="clear: both;"></div>';
						}

						$blogContent	.= '<div style="text-align: right;"><a href="' . $blogLink . '">' . JText::_( 'COM_EASYBLOG_CONTINUE_READING' ) . '</a></div>';
					}

					$title			= JText::sprintf( 'COM_EASYBLOG_JS_ACTIVITY_BLOG_FEATURED' , $blogLink , $title);
					EasyBlogHelper::addJomsocialActivity( $blog , 'easyblog.blog.featured' , $title , $my->id , $blogContent );
				}

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $config->get( 'integrations_easysocial_stream_featurepost' ) && $easysocial->exists() )
				{
					$easysocial->createFeaturedBlogStream( $blog, $my->id );
				}

				if( $config->get( 'integrations_mighty_activity_feature_blog' ) )
				{
					EasyBlogHelper::getHelper( 'MightyTouch' )->addFeaturedActivity( $blog );
				}
				$blog->notify( false, 0, true );
			}
		}

		return true;
	}

	public static function removeFeatured($type, $contentId)
	{
		$db		= EasyBlogHelper::db();

		$query	= 'DELETE FROM `#__easyblog_featured` WHERE `content_id` = ' . $db->Quote($contentId);
		$query	.= ' AND `type` = ' . $db->Quote($type);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	public static function isFeatured($type, $contentId)
	{
		static $blogs	= array();

		if( !isset( $blogs[ $type ][ $contentId ] ) )
		{
			$db		= EasyBlogHelper::db();

			$query	= 'SELECT COUNT(1) FROM `#__easyblog_featured` WHERE `content_id` = ' . $db->Quote($contentId);
			$query	.= ' AND `type` = ' . $db->Quote($type);

			$db->setQuery($query);

			$result = $db->loadResult();
			$result = (empty($result)) ? 0 : $result;
			$blogs[ $type ][ $contentId ]	= $result;
		}
		return ( $blogs[ $type ][ $contentId ] > 0 );
	}

	public static function addJomSocialActivityBlog( $blog, $isNew, $isFeed = false )
	{
		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= EasyBlogHelper::getConfig();
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easyblog' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( $isFeed )
		{
			if( $isNew && !$config->get( 'integrations_jomsocial_rss_import_activity' ) )
			{
				return false;
			}
		}
		else
		{
			if( $isNew && !$config->get( 'integrations_jomsocial_blog_new_activity' ) )
			{
				return false;
			}
		}

		// We do not want to add activities if update blog activity is disabled.
		if( !$isNew && !$config->get( 'integrations_jomsocial_blog_update_activity') )
		{
			return false;
		}

		if( JFile::exists( $jsCoreFile ) )
		{
			require_once( $jsCoreFile );

			$blogCommand	= ($isNew) ? 'easyblog.blog.add' : 'easyblog.blog.update';

			$blogTitle		= htmlspecialchars( $blog->title );
			$maxTitleLength	= $config->get( 'jomsocial_blog_title_length' , 80 );

			if( JString::strlen( $blogTitle) > $maxTitleLength )
			{
				$blogTitle		= JString::substr( $blog->title , 0 , $maxTitleLength ) . '...';
			}

			$category		= EasyBlogHelper::getTable( 'Category' , 'Table' );
			$category->load( $blog->category_id );

			$easyBlogItemid	= '';
			$mainframe		= JFactory::getApplication();
			$blogLink		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id='. $blog->id . $easyBlogItemid, false, true);
			$categoryLink	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id . $easyBlogItemid, false, true);

			// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
			$sh404exists	= EasyBlogRouter::isSh404Enabled();
			if( $mainframe->isAdmin() && $sh404exists )
			{
				$easyBlogItemid	= EasyBlogRouter::getItemId('latest');
				$easyBlogItemid = '&Itemid=' . $easyBlogItemid;

				$blogLink		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog&view=entry&id='. $blog->id . $easyBlogItemid;
				$categoryLink	= rtrim( JURI::root() , '/' ) .'/index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id . $easyBlogItemid;
			}

			$blogContent	= '';
			if($config->get('integrations_jomsocial_submit_content'))
			{

				$requireVerification = false;
				if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
				{
					$row->title	= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $blog->title);
					$requireVerification = true;
				}

				$blogContent	= '';
				if($requireVerification && !EasyBlogHelper::verifyBlogPassword($blog->blogpassword, $blog->id))
				{
					$theme = new CodeThemes();
					$theme->set('id', $blog->id);
					$theme->set('return', base64_encode( $blogLink ) );
					$blogContent	= $theme->fetch( 'blog.protected.php' );
				}
				else
				{
					$blogContent	= $blog->intro . $blog->content;

					// Get blog's image and use it as the cover photo.
					$image 			= '';

					if( $blog->getImage() )
					{
						$imageSource	= $blog->getImage()->getSource( 'frontpage' );

						if( $imageSource )
						{
							$image	= '<a href="' . $blogLink . '"><img src="' . $imageSource . '" style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"/></a>';
						}
					}
					else
					{
						// Try to find for an image in the content.
						$pattern		= '#<img[^>]*>#i';
						preg_match( $pattern , $blogContent , $matches );

						if( $matches )
						{
							$matches[0]		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"' , $matches[0 ] );

							$image 			= '<a href="' . $blogLink . '">' . $matches[0] . '</a>';
						}
					}

					// Strip unwanted data.
					$blogContent	= EasyBlogHelper::getHelper( 'Videos' )->strip( $blogContent );
					$blogContent	= EasyBlogGoogleAdsense::stripAdsenseCode( $blogContent );
					$blogContent	= JString::substr($blogContent, 0, $config->get( 'integrations_jomsocial_blogs_length', 250 ) ) . ' ...';

					// Remove all html tags from the content as we want to chop it down.
					$blogContent	= strip_tags( $blogContent );

					if( !empty( $image ) )
					{
						$blogContent 	= $image . $blogContent . '<div style="clear: both;"></div>';
					}

					$blogContent	.= '<div style="text-align: right;"><a href="' . $blogLink . '">' . JText::_( 'COM_EASYBLOG_CONTINUE_READING' ) . '</a></div>';
				}
			}

			$title		= ($isNew) ? JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_ADDED_NON_CATEGORY' , $blogLink , $blogTitle) : JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_UPDATED_NON_CATEGORY' , $blogLink , $blogTitle);
			if($config->get('integrations_jomsocial_show_category'))
			{
				$title	= ($isNew) ? JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_ADDED' , $blogLink , $blogTitle , $categoryLink , JText::_( $category->title ) ) : JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_UPDATED' , $blogLink , $blogTitle , $categoryLink , JText::_( $category->title ) );;
			}


			$obj			= new stdClass();
			$obj->access	= $blog->private;
			$obj->title		= $title;
			$obj->content	= $blogContent;
			$obj->cmd		= $blogCommand;

			if( $config->get( 'integrations_jomsocial_activity_likes' ) )
			{
				$obj->like_id	= $blog->id;
				$obj->like_type = 'com_easyblog';

				if( !$isNew )
				{
					$obj->comment_type	= 'com_easyblog.update';
				}
			}

			if( $config->get( 'integrations_jomsocial_activity_comments' ) )
			{
				$obj->comment_id	= $blog->id;
				$obj->comment_type	= 'com_easyblog';

				if( !$isNew )
				{
					$obj->comment_type	= 'com_easyblog.update';
				}

			}

			$obj->actor		= $blog->created_by;
			$obj->target	= 0;
			$obj->app		= 'easyblog';
			$obj->cid		= $blog->id;

			// add JomSocial activities
			CFactory::load ( 'libraries', 'activities' );
			CActivityStream::add($obj);
		}
	}

	public static function addJomSocialActivityComment( $comment, $blogTitle )
	{
		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= EasyBlogHelper::getConfig();

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integrations_jomsocial_comment_new_activity' ) )
		{
			return false;
		}

		if( JFile::exists( $jsCoreFile ) )
		{
			require_once( $jsCoreFile );

			$config		= EasyBlogHelper::getConfig();
			$command	= 'easyblog.comment.add';

			$maxTitleLength		= $config->get( 'jomsocial_blog_title_length' , 80 );

			if( JString::strlen( $blogTitle) > $maxTitleLength )
			{
				$blogTitle		= JString::substr( $blogTitle , 0 , $maxTitleLength ) . '...';
			}
			$blogLink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id='. $comment->post_id, false, true) . '#comment-' . $comment->id;

			$content	= '';
			if($config->get('integrations_jomsocial_submit_content'))
			{
				$content	= $comment->comment;
				$content	= EasyBlogCommentHelper::parseBBCode( $content );
				$content	= nl2br( $content );
				$content	= strip_tags( $content );
				$content	= JString::substr( $content, 0 , $config->get( 'integrations_jomsocial_comments_length' ) );
			}

			$obj			= new stdClass();

			if( !$comment->created_by )
			{
				$obj->title 	= JText::sprintf( 'COM_EASYBLOG_JS_ACTIVITY_GUEST_COMMENT_ADDED' , $blogLink , $blogTitle );
			}
			else
			{
				$obj->title		= JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_COMMENT_ADDED' , $blogLink , $blogTitle );
			}

			$obj->content	= ($config->get('integrations_jomsocial_submit_content')) ? $content : '';
			$obj->cmd		= $command;
			$obj->actor 	= $comment->created_by;

			$obj->target	= 0;
			$obj->app		= 'easyblog';
			$obj->cid		= $comment->id;

			if( $config->get( 'integrations_jomsocial_activity_likes' ) )
			{
				$obj->like_id	= $comment->id;
				$obj->like_type	= 'com_easyblog.comments';
			}

			if( $config->get( 'integrations_jomsocial_activity_comments' ) )
			{
				$obj->comment_id	= $comment->id;
				$obj->comment_type	= 'com_easyblog.comments';
			}
			// add JomSocial activities
			CFactory::load ( 'libraries', 'activities' );
			CActivityStream::add($obj);
		}
	}

	public static function uploadAvatar( $profile, $isFromBackend = false )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();

		$acl		= EasyBlogACLHelper::getRuleSet();

		if(! $isFromBackend)
		{
			if(empty($acl->rules->upload_avatar))
			{
				$url	= 'index.php?option=com_easyblog&view=dashboard&layout=profile';
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
				$mainframe->redirect(EasyBlogRouter::_($url, false));
			}
		}
		$avatar_config_path	= $config->get('main_avatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path		= JPATH_ROOT.DIRECTORY_SEPARATOR.$avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;
		$file				= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$mainframe->redirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false) );
				}
				else
				{
					//from backend
					$mainframe->redirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=users', false), JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
				}
				return;
			}
			else
			{
				// folder created. now copy index.html into this folder.
				if(! JFile::exists( $upload_path . DIRECTORY_SEPARATOR . 'index.html' ) )
				{
					$targetFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'index.html';
					$destFile	= $upload_path . DIRECTORY_SEPARATOR .'index.html';

					if( JFile::exists( $targetFile ) )
						JFile::copy( $targetFile, $destFile );
				}
			}
		}

		//makesafe on the file
		$file['name']	= $my->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path.DIRECTORY_SEPARATOR.$file['name'];
			$target_file			= JPath::clean($target_file_path . DIRECTORY_SEPARATOR. JFile::makeSafe($file['name']));
			$isNew					= false;

			//include_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_easyblog'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php');
			require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );

			if (! EasyImageHelper::canUpload( $file, $err ))
			{
				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( JText::_( $err ) , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=users', false), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( $file['error'] , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=users', false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar	= $profile->avatar;
			$tempAvatar	= '';

			if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
			{
				$session	= JFactory::getSession();
				$sessionId	= $session->getToken();

				$fileExt	= JFile::getExt(JPath::clean($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DIRECTORY_SEPARATOR . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=users', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					EasyBlogHelper::setMessageQueue( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=users', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth	= EBLOG_AVATAR_LARGE_WIDTH;
			$configImageHeight	= EBLOG_AVATAR_LARGE_HEIGHT;

			$image = new EasySimpleImage();
			$image->load($file['tmp_name']);
			$image->resizeToFill($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
			{
				//if(JFile::exists( JPATH_ROOT.DIRECTORY_SEPARATOR.$oldAvatar ))
				if(JFile::exists( $tempAvatar ))
				{
					//JFile::delete( JPATH_ROOT.DIRECTORY_SEPARATOR.$oldAvatar );
					JFile::delete( $tempAvatar );
				}
			}

			if($isNew && !$isFromBackend)
			{
				if( $my->id != 0 && $config->get('main_jomsocial_userpoint') )
				{
					$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
					if( JFile::exists( $jsUserPoint ) )
					{
						require_once( $jsUserPoint );
						CUserPoints::assignPoint( 'com_easyblog.avatar.upload' , $my->id );
					}
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return 'default_blogger.png';
		}

	}


	public static function uploadCategoryAvatar( $category, $isFromBackend = false )
	{
		return EasyBlogHelper::uploadMediaAvatar( 'category', $category, $isFromBackend);
	}

	public static function uploadTeamAvatar( $team, $isFromBackend = false )
	{
		return EasyBlogHelper::uploadMediaAvatar( 'team', $team, $isFromBackend);
	}

	public static function uploadMediaAvatar( $mediaType, $mediaTable, $isFromBackend = false )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();

		$acl		= EasyBlogACLHelper::getRuleSet();


		// required params
		$layout_type			= ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type				= ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type	= ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';



		if(! $isFromBackend && $mediaType == 'category')
		{
			if(empty($acl->rules->upload_cavatar))
			{
				$url  = 'index.php?option=com_easyblog&view=dashboard&layout='.$layout_type;
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
				$mainframe->redirect(EasyBlogRouter::_($url, false));
			}
		}

		$avatar_config_path	= ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path		= JPATH_ROOT.DIRECTORY_SEPARATOR.$avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;
		$file				= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$mainframe->redirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false) );
				}
				else
				{
					//from backend
					$mainframe->redirect( EasyBlogRouter::_('index.php?option=com_easyblog&view='.$layout_type, false), JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
				}
				return;
			}
			else
			{
				// folder created. now copy index.html into this folder.
				if(! JFile::exists( $upload_path . DIRECTORY_SEPARATOR . 'index.html' ) )
				{
					$targetFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'index.html';
					$destFile	= $upload_path . DIRECTORY_SEPARATOR .'index.html';

					if( JFile::exists( $targetFile ) )
						JFile::copy( $targetFile, $destFile );
				}
			}
		}

		//makesafe on the file
		$file['name']	= $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path.DIRECTORY_SEPARATOR.$file['name'];
			$target_file			= JPath::clean($target_file_path . DIRECTORY_SEPARATOR. JFile::makeSafe($file['name']));
			$isNew					= false;

			//include_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_easyblog'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php');
			require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );

			if (! EasyImageHelper::canUpload( $file, $err ))
			{
				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( JText::_( $err ) , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view='.$view_type, false), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( $file['error'] , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view='.$view_type, false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar	= (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar	= '';
			if( $oldAvatar != $default_avatar_type)
			{
				$session	= JFactory::getSession();
				$sessionId	= $session->getToken();

				$fileExt	= JFile::getExt(JPath::clean($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DIRECTORY_SEPARATOR . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					EasyBlogHelper::setMessageQueue( JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					EasyBlogHelper::setMessageQueue( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth	= EBLOG_AVATAR_LARGE_WIDTH;
			$configImageHeight	= EBLOG_AVATAR_LARGE_HEIGHT;

			$image = new EasySimpleImage();
			$image->load($file['tmp_name']);
			$image->resizeToFill($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != $default_avatar_type)
			{
				if(JFile::exists( $tempAvatar ))
				{
					JFile::delete( $tempAvatar );
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return $default_avatar_type;
		}

	}

	public static function unPublishPost()
	{
		$db		= EasyBlogHelper::db();
		$date	= EasyBlogHelper::getDate();

		$query	= 'UPDATE `#__easyblog_post`';
		$query	.= ' SET `published` = ' . $db->Quote('0');
		$query	.= ' WHERE `publish_down` > `publish_up`';
		$query	.= ' AND `publish_down` <= ' . $db->Quote( EasyBlogHelper::getDate()->toMySQL() );
		$query	.= ' AND `publish_down` != ' . $db->Quote('0000-00-00 00:00:00');
		$query	.= ' AND `published` != ' . $db->Quote('0');
		$query	.= ' AND `published` != ' . $db->Quote('3');
		$query	.= ' AND `ispending` = ' . $db->Quote('0');


		$db->setQuery($query);
		$db->query();
	}


	public static function processScheduledPost( $max = 5 )
	{
		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );

		$db		= EasyBlogHelper::db();
		$date	= EasyBlogHelper::getDate();
		$config	= EasyBlogHelper::getConfig();

		$query	= 'SELECT * FROM `#__easyblog_post`';
		$query	.= ' WHERE `publish_up` <= ' . $db->Quote( EasyBlogHelper::getDate()->toMySQL() );
		$query	.= ' AND `published` = ' . $db->Quote('2');
		$query	.= ' AND `ispending` = ' . $db->Quote('0');
		$query	.= ' ORDER BY `id`';

		if($max)
		{
			$query  .= ' LIMIT ' . $max;
		}

		$db->setQuery($query);
		$blogs  	= $db->loadObjectList();

		if (!$blogs) {
			return;
		}

		foreach($blogs as $item)
		{
			$blog = EasyBlogHelper::getTable( 'Blog', 'Table' );
			$blog->bind( $item );

			$curDate			= EasyBlogHelper::getDate();
			$query 	= 'UPDATE ' . $db->nameQuote( '#__easyblog_post' )
					. ' SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ',' . $db->nameQuote( 'isnew' ) . '=' . $db->Quote( 0 )
					. ', ' . $db->nameQuote( 'modified' ) . '=' . $db->Quote( $curDate->toMySQL() )
					. ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $blog->id );

			$db->setQuery($query);
			$db->query();

			$autopost = json_decode($blog->autoposting);

			// @rule: Process centralized options first
			// See if there are any global postings enabled.
			$blog->autopost(array(), $autopost);

			// send out notification.
			$blog->notify();
		}
	}

	/**
	 * Allows caller to detect specific css files from site's template
	 * and load it into the headers if necessary.
	 *
	 * @param	string $fileName
	 */
	public static function addTemplateCss( $fileName )
	{
		$document		= JFactory::getDocument();
		$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/css/' . $fileName );

		$mainframe		= JFactory::getApplication();
		$templatePath 	= JPATH_ROOT . '/templates/' . $mainframe->getTemplate() . '/html/com_easyblog/assets/css/' . $fileName;

		if( JFile::exists($templatePath) )
		{
			$document->addStyleSheet( rtrim(JURI::root(), '/') . '/templates/' . $mainframe->getTemplate() . '/html/com_easyblog/assets/css/' . $fileName );

			return true;
		}

		return false;
	}

	/**
	 * Renders a module position in the template
	 */
	public static function renderModule( $position , $attributes = array() , $content = null )
	{
		$doc		= JFactory::getDocument();
		$renderer	= $doc->loadRenderer( 'module' );
		$buffer		= '';
		$modules	= JModuleHelper::getModules( $position );

		foreach( $modules as $module )
		{
			$theme	= new CodeThemes();
			$theme->set( 'position'	, $position );
			$theme->set( 'output' 	, $renderer->render( $module , $attributes , $content ) );
			$buffer .= $theme->fetch( 'modules.item.php' );
		}

		return $buffer;
	}

	/*
	 * Loads necessary dependency for the module stylings.
	 *
	 * @param	null
	 * @return	null
	 */
	public static function loadModuleCss()
	{
		static $loaded	= false;

		if( !$loaded )
		{
			$document	= JFactory::getDocument();

			EasyBlogHelper::addTemplateCss( 'module.css' );

			$loaded		= true;
		}
		return $loaded;
	}

	/**
	 * Method to load the theme CSS file based on the config
	 *
	 * @param	String		Filename to be loaded
	 * @param	Boolean		Is this a dashboard?
	 */
	public static function loadThemeCss( $file, $is_dashboard = false )
	{
		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();

		$template	= $mainframe->getTemplate();

		$bloggertheme			= EasyBlogHelper::getBloggerTheme();
		$enableBloggerTheme		= $config->get('layout_enablebloggertheme', true);
		$availableBloggerTheme	= $config->get('layout_availablebloggertheme');

		if( !is_array( $availableBloggerTheme ) )
		{
			$availableBloggerTheme	= explode( '|' , $config->get('layout_availablebloggertheme') );
		}

		if($enableBloggerTheme && !empty($bloggertheme) && $bloggertheme != 'global' && in_array($bloggertheme, $availableBloggerTheme))
		{
			$theme = $bloggertheme;
			JRequest::setVar('theme', $bloggertheme);
		}
		else
		{
			$theme = $config->get( 'layout_theme' );
		}

		// @rule: Use Google fonts if necessary.
		$headingFont	= $config->get( 'layout_googlefont' );
		if(  $headingFont != 'site' )
		{
			// Replace the word Bold with :Bold
			$headingFont	= JString::str_ireplace( ' bold' , ':bold' , $headingFont );

			// Replace spaces with +
			$headingFont	= JString::str_ireplace( ' ' , '+' , $headingFont );

			$url			= 'https://fonts.googleapis.com/css?family=' . $headingFont;
			$document->addStyleSheet( $url );
		}

		$googlePlus		= $config->get( 'main_googleone', 0 );
		$socialFrontEnd	= $config->get( 'main_googleone_frontpage', 0 );

		if( $socialFrontEnd && $googlePlus)
		{
			$googlPlusUrl	= 'https://apis.google.com/js/plusone.js';
			$document->addScript( $googlPlusUrl );
		}

		$path		= null;
		$direction	= $document->direction;

		 /**
		 * for theme development purpose
		 */
		$usethis	= JRequest::getWord('theme');

		if ( !empty( $usethis ) ) {
			JRequest::setVar('theme', $usethis);
			$theme	= $usethis;
		}


		// new in 1.1
		// load the file based on the theme's config.ini
		$themeConfig = EasyBlogHelper::getThemeInfo( $theme );

		if( !$is_dashboard )
		{
			/**
			 * Load blog theme file
			 *
			 * Priority level
			 *
			 * 1. /templates/<joomla_template>/html/com_easyblog/
			 * 2. /components/com_easyblog/themes/<selected_theme>/
			 * 3. /components/com_easyblog/themes/<parent_for_selected_theme>/
			 * 4. /components/com_easyblog/themes/default/
			 */
			$siteOverride	= false;

			$overrideFile 	= JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/css/' . $file;

			if ( JFile::exists( $overrideFile ) )
			{
				$path			= rtrim( JURI::root(), '/' ) . '/templates/' . $template . '/html/com_easyblog/css/' . $file;
				$siteOverride	= true;
			}
			elseif( JFile::exists( EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file ) )
			{
				if ( $themeConfig->get('parent_css') )
				{
					$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/' . $themeConfig->get('parent') . '/css/' . $file;
					$document->addStylesheet( $path );
				}
				$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/' . $theme . '/css/' . $file;
			}
			else
			{
				$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/default/css/' . $file;
			}

			// Always load the system css if template override in use as we want to allow them to
			// reuse the styling as much as possible.
			if( !$siteOverride )
			{
				$document->addStylesheet( rtrim( JURI::root() , '/' ) . '/components/com_easyblog/themes/default/css/styles.css' );
			}
			$document->addStylesheet( $path );
		}


		/**
		 * Load dashboard theme file
		 *
		 * Priority level
		 *
		 * 1. /templates/<joomla_template>/html/com_easyblog/dashboard/
		 * 2. /components/com_easyblog/themes/<selected_theme>/dashboard/
		 * 3. /components/com_easyblog/themes/dashboard/
		 */
		if ( $is_dashboard )
		{
			$dashboardTheme	= JString::trim( JString::strtolower( $config->get( 'layout_dashboard_theme' ) ) );
			$overridePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file;
			$themePath		= JFile::exists( EBLOG_THEMES . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . $dashboardTheme . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file );

			// Always load the base css file.
			$document->addStyleSheet( rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/dashboard/system/css/styles.css' );

			if ( JFile::exists( $overridePath ) )
			{
				$path = rtrim( JURI::root(), '/' ) . '/templates/' . $template . '/html/com_easyblog/dashboard/css/' . $file;
			}
			elseif( $themePath )
			{
				$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/dashboard/' . $dashboardTheme . '/css/' . $file;
			}
			else
			{
				$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/dashboard/system/' . $file;
			}
			$document->addStylesheet( $path );
		}

		/**
		 * Load RTL file for default theme
		 */
		if ( $direction == 'rtl' )
		{
			// we need default RTL be loaded always
			$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/default/css/rtl.css';
			$document->addStylesheet( $path );


			// checking the template override folder
			if ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'rtl.css' ) )
			{
				$path = rtrim( JURI::root(), '/' ) . '/templates/' . $template . '/html/com_easyblog/css/rtl.css';
				$document->addStylesheet( $path );
			}


			// check if we need to load the parent CSS based on theme's config.ini
			// since 1.1
			if ( $themeConfig->get('parent_css') )
			{
				// now check if the parent theme have th RTL file
				if ( JFile::exists( EBLOG_THEMES . DIRECTORY_SEPARATOR . $themeConfig->get('parent') . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'rtl.css' ) )
				{
					$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/' . $themeConfig->get('parent') . '/css/rtl.css';
					$document->addStylesheet( $path );
				}
			}


			// checking the theme folder
			if( JFile::exists( EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'rtl.css' ) )
			{
				$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/' . $theme . '/css/rtl.css';
				$document->addStylesheet( $path );
			}
		}


		/**
		 * Load RTL file for dashboard theme
		 */
		if ( $direction == 'rtl' && $is_dashboard )
		{
			if ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'rtl.css' ) )
			{
				$path = rtrim( JURI::root(), '/' ) . '/templates/' . $template . '/html/com_easyblog/dashboard/css/rtl.css';
			}
			elseif( JFile::exists( EBLOG_THEMES . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'rtl.css' ) )
			{
				$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/dashboard/' . $template . '/css/rtl.css';
			}
			else
			{
				$path	= rtrim( JURI::root(), '/' ) . '/components/com_easyblog/themes/dashboard/system/css/rtl.css';
			}
			$document->addStylesheet( $path );
		}
	}

	public static function getUserId( $username )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT `id` FROM `#__easyblog_users` WHERE `permalink`=' . $db->Quote( $username );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		if(empty($result))
		{
			$query	= 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->Quote( $username );
			$db->setQuery( $query );
			$result = $db->loadResult();
		}

		return $result;
	}

	public static function getBlogNavigation( $blogId, $creationDate, $typeId = '0', $type = 'sitewide')
	{
		$db		= EasyBlogHelper::db();
		$my		= JFactory::getUser();
		$config	= EasyBlogHelper::getConfig();

		$keys	= array('prev','next');
		$nav	= array();
		$nav['prev']	= null;
		$nav['next']	= null;

		$isBloggerMode	= EasyBlogRouter::isBloggerMode();


		$menus	= JFactory::getApplication()->getMenu();
		$menu	= $menus->getActive();
		$queryInclude 	= '';

		if( is_object( $menu ) )
		{
			$params 	= EasyBlogHelper::getRegistry();
			$params->load( $menu->params );
			$cats	= EasyBlogHelper::getCategoryInclusion( $params->get( 'inclusion' ) );

			if( $cats )
			{
				if( !is_array( $cats ) )
				{
					$cats	= array( $cats );
				}

				$queryInclude	= ' AND a.`category_id` IN (';

				foreach( $cats as $allowedCat )
				{
					$queryInclude .= $db->Quote( $allowedCat );

					if( next( $cats ) !== false )
					{
						$queryInclude .= ',';
					}
				}
				$queryInclude 	.= ')';
			}
		}

		// get all private categories id
		$excludeCats	= EasyBlogHelper::getPrivateCategories();
		$queryExclude	= '';
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (';

			for( $i = 0; $i < count( $excludeCats ); $i++ )
			{
				$queryExclude	.= $db->Quote( $excludeCats[ $i ] );

				if( next( $excludeCats ) !== false )
				{
					$queryExclude .= ',';
				}
			}
			$queryExclude	.= ')';
		}

		foreach($keys as $key)
		{

			$query	= 'SELECT a.`id`, a.`title`';
			$query	.= ' FROM `#__easyblog_post` AS `a`';

			if($type == 'team' && ! empty($typeId))
			{
				$query	.= ' INNER JOIN `#__easyblog_team_post` AS `b`';
				$query	.= ' 	ON a.`id` = b.`post_id`';
			}

			$query	.= ' WHERE a.`published` = ' . $db->Quote('1');
			$query	.= ' AND a.`ispending` = ' . $db->Quote('0');


			//blog privacy setting
			// @integrations: jomsocial privacy
			$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() && !EasyBlogHelper::isSiteAdmin() )
			{
				$esPrivacyQuery  = $easysocial->buildPrivacyQuery( 'a' );
				$query 	.= $esPrivacyQuery;

			}
			else if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin() )
			{
				require_once( $file );

				$my			= JFactory::getUser();
				$jsFriends	= CFactory::getModel( 'Friends' );
				$friends	= $jsFriends->getFriendIds( $my->id );

				// Insert query here.
				$query	.= ' AND (';
				$query	.= ' (a.`private`= 0 ) OR';
				$query	.= ' ( (a.`private` = 20) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

				if( empty( $friends ) )
				{
					$query	.= ' ( (a.`private` = 30) AND ( 1 = 2 ) ) OR';
				}
				else
				{
					$query	.= ' ( (a.`private` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
				}

				$query	.= ' ( (a.`private` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
				$query	.= ' )';
			}
			else
			{
				//blog privacy setting
				if($my->id == 0)
					$query .= ' AND a.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}

			//include categories
			if( !empty( $queryInclude ) )
			{
				$query	.= $queryInclude;
			}

			//exclude private categories
			$query		.= $queryExclude;


			if($isBloggerMode !== false)
				$query	.= ' AND a.`created_by` = ' . $db->Quote($isBloggerMode);


			if($type == 'team' && ! empty($typeId))
			{
				$query	.= ' 	AND b.`team_id` = ' . $db->Quote($typeId);
			}
			else
			{
				$query	.= ' 	AND a.`issitewide` = ' . $db->Quote('1');
			}

			//language filtering
			if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
			{
				// @rule: When language filter is enabled, we need to detect the appropriate contents
				$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();

				if( $filterLanguage )
				{
					$query	.= ' AND (';
					$query	.= ' a.`language`=' . $db->Quote( JFactory::getLanguage()->getTag() );
					$query	.= ' OR a.`language`=' . $db->Quote( '' );
					$query	.= ' OR a.`language`=' . $db->Quote( '*' );
					$query	.= ' )';
				}
			}

			if($key == 'prev')
			{
				$query	.= ' AND a.`created` < ' . $db->Quote($creationDate);
				$query	.= ' ORDER BY a.`created` DESC';
			}
			else if($key == 'next')
			{
				$query	.= ' AND a.`created` > ' . $db->Quote($creationDate);
				$query	.= ' ORDER BY a.`created` ASC';
			}

			$query		.= ' LIMIT 1';

			$db->setQuery($query);
			$result		= $db->loadObjectList();

			$nav[$key]	= $result;
		}

		return $nav;
	}


	public static function setMeta( $id, $type, $defaultViewDesc = '')
	{
		$config = EasyBlogHelper::getConfig();

		$db = EasyBlogHelper::db();

		$query = 'SELECT id, keywords, description, indexing FROM ' . $db->nameQuote('#__easyblog_meta') . ' WHERE content_id = ' . $db->Quote($id) . ' and type = ' . $db->Quote($type);
		$db->setQuery($query);

		$result = $db->loadObject();

		if( !$result )
		{
			$result 	= new stdClass();
		}

		// If the category was created without any meta, we need to automatically fill in the description
		if( $type == META_TYPE_CATEGORY && !$result )
		{
			$category 	= EasyBlogHelper::getTable( 'Category' );
			$category->load( $id );

			JFactory::getDocument()->setMetadata( 'description' , strip_tags( $category->description ) );
		}

		//auto fill meta keywords if the option is enable and the user did not set his own meta keywords.
		if($type==META_TYPE_POST && ( ($config->get('main_meta_autofillkeywords') && empty($result->keywords) )|| ($config->get( 'main_meta_autofilldescription') )) )
		{
			$keywords = array();

			//set category into the meta keyword.
			$post	= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$post->load( $id );

			$post->intro	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $post->intro, true );
			$post->content	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $post->content, true );

			$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
			$category->load( $post->category_id );
			$keywords[] = $category->title;

			if( $config->get( 'main_meta_autofillkeywords') && empty($result->keywords) )
			{
				//set tags into the meta keyword.
				$modelPT	= self::getModel( 'PostTag' );
				$blogTags	= $modelPT->getBlogTags($id);

				if(!empty($blogTags))
				{
					foreach($blogTags as $tag)
					{
						$keywords[] = $tag->title;
					}
				}

				$result->keywords = implode(',', $keywords);
			}

			if( $config->get( 'main_meta_autofilldescription' ) && empty($result->description) )
			{
				$content	= !empty( $post->intro ) ? strip_tags( $post->intro ) : strip_tags( $post->content );
				$content	= str_ireplace( "\r\n" , "" , $content );
				$content	= str_ireplace( "&nbsp;" , " " , $content );
				$content	= trim($content);

				// @rule: Set description into meta headers
				$result->description	= JString::substr( $content , 0 , $config->get( 'main_meta_autofilldescription_length' ) );
				$result->description	= EasyBlogStringHelper::escape( $result->description );
			}

			// Remove JFBConnect codes.
			if( isset( $result->description ) )
			{
				$pattern 	= '/\{JFBCLike(.*)\}/i';
				$result->description	= preg_replace( $pattern , '' , $result->description );
			}
		}

		// check if the descriptin or keysword still empty or not. if yes, try to get from joomla menu.
		if( empty( $result->description ) && empty( $result->keywords ) )
		{
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getActive();

			if( is_object( $item ) )
			{
				$params 		= EasyBlogHelper::getRegistry( $item->params );

				$description	= $params->get( 'menu-meta_description' , '' );
				$keywords		= $params->get( 'menu-meta_keywords' , '' );

				if( ! ( empty($description) && empty($keywords) ) )
				{
					$result					= new stdClass();
					$result->description	= EasyBlogStringHelper::escape( $description );
					$result->keywords		= $keywords;
				}
			}
		}


		if ( !empty( $result ) ) {
			$document = JFactory::getDocument();

			if ( !empty( $result->keywords ) )
			{
				$document->setMetadata('keywords', $result->keywords);
			}

			if ( !empty( $result->description ) )
			{
				$document->setMetadata('description', $result->description);
			}
			else
			{
				if( !empty( $defaultViewDesc) )
				{
					//get joomla default description.
					$joomlaDesc 	= EasyBlogHelper::getJConfig()->get( 'MetaDesc' );

					$metaDesc	= $defaultViewDesc . ' - ' . $joomlaDesc;
					$metaDesc   = EasyBlogStringHelper::escape( $metaDesc ) ;
					$document->setMetadata('description', $metaDesc);
				}
			}

			// @task: Set the noindex if necessary.
			if( isset( $result->indexing ) && !$result->indexing )
			{
				$document->setMetadata( 'robots' , 'noindex,follow' );
			}
		}
	}

	public static function addLikes($contentId, $type, $userId = '0')
	{
		if($userId == '0')
		{
			$user	= JFactory::getUser();
			$userId	= $user->id;
		}

		$date	= EasyBlogHelper::getDate();
		$likes	= EasyBlogHelper::getTable( 'Likes', 'Table' );

		$params   = array();
		$params['type']			= $type;
		$params['content_id']	= $contentId;
		$params['created_by']	= $userId;
		$params['type']			= $type;
		$params['created']		= $date->toMySQL();

		$likes->bind($params);
		$likes->store();
		return $likes->id;
	}

	public static function removeLikes($likesId)
	{
		$likes	= EasyBlogHelper::getTable( 'Likes', 'Table' );
		$likes->load($likesId);

		$likes->delete();
		return true;
	}

	public static function getLikesAuthors($contentId, $type, $userId)
	{
		$db		= EasyBlogHelper::db();
		$config	= EasyBlogHelper::getConfig();

		$displayFormat  = $config->get('layout_nameformat');
		$displayName    = '';

		switch($displayFormat){
			case "name" :
				$displayName = 'a.name';
				break;
			case "username" :
				$displayName = 'a.username';
				break;
			case "nickname" :
			default :
				$displayName = 'b.nickname';
				break;
		}

		$query	= 'select a.id as `user_id`, c.id, ' . $displayName . ' as `displayname`';
		$query	.= ' FROM `#__users` as a';
		$query	.= '  inner join `#__easyblog_users` as b';
		$query	.= '    on a.id = b.id';
		$query	.= '  inner join `#__easyblog_likes` as c';
		$query	.= '    on a.id = c.created_by';
		$query	.= ' where c.content_id = ' . $db->Quote($contentId);
		$query	.= ' and c.`type` = '. $db->Quote($type);
		$query	.= ' order by c.id desc';

		$db->setQuery($query);
		$list   = $db->loadObjectList();

		if(count($list) <= 0)
		{
			return '';
		}

		// else continue here
		$onwerInside = false;

		$names	= array();
		for($i = 0; $i < count($list); $i++)
		{

			if($list[$i]->user_id == $userId)
			{
				$onwerInside	= true;
				array_unshift($names, JText::_('COM_EASYBLOG_YOU') );
			}
			else
			{
				$blogger	= EasyBlogHelper::getTable( 'Profile', 'Table');
				$blogger->load( $list[ $i ]->user_id  );
				$names[]	= '<a href="' . $blogger->getProfileLink() . '">' . $list[$i]->displayname . '</a>';
			}
		}

		$max	= 3;
		$total	= count($names);
		$break	= 0;

		if($total == 1)
		{
			$break	= $total;
		}
		else
		{
			if($max >= $total)
			{
				$break	= $total - 1;
			}
			else if($max < $total)
			{
				$break	= $max;
			}
		}

		$main	= array_slice($names, 0, $break);
		$remain	= array_slice($names, $break);

		$stringFront	= implode(", ", $main);
		$returnString	= '';

		if(count($remain) > 1)
		{
			$returnString	= JText::sprintf('COM_EASYBLOG_AND_OTHERS_LIKE_THIS', $stringFront, count($remain));
		}
		else if(count($remain) == 1)
		{
			$returnString	= JText::sprintf('COM_EASYBLOG_AND_LIKE_THIS', $stringFront, $remain[0]);
		}
		else
		{
			if( EasyBlogHelper::isLoggedIn() && $onwerInside )
			{
				$returnString	= JText::sprintf('COM_EASYBLOG_LIKE_THIS_SINGULAR', $stringFront);
			}
			else
			{
				$returnString	= JText::sprintf('COM_EASYBLOG_LIKE_THIS_PLURAL', $stringFront);
			}
		}

		return $returnString;
	}

	public static function dateTimePicker( $id, $caption, $date = '', $reset = false )
	{
		return '
			<div id="datetime_' . $id . '" class="datetime_container">
				<span class="datetime_caption">' . $caption . '</span>
				<a class="ui-button" href="javascript:void(0)" id="datetime_edit_' . $id . '" onclick="eblog.editor.datetimepicker.element(\''.$id.'\', \''.$reset.'\')">'.JText::_('COM_EASYBLOG_EDIT_BUTTON').'</a>
				<input type="hidden" name="' . $id . '" id="' . $id . '" value="'.$date.'" />
			</div>';
	}

	public static function formatBlogCommentsLite( $comments )
	{
		for($i = 0; $i < count($comments); $i++)
		{
			$row		=& $comments[$i];
			$creator	= EasyBlogHelper::getTable( 'Profile', 'Table' );

			if($row->created_by != 0)
			{
				$creator->load($row->created_by);
				$user	= JFactory::getUser($row->created_by);
				$creator->setUser($user);
			}

			//get integrated avatar
			$row->poster	= $creator;
			$row->comment	= EasyBlogCommentHelper::parseBBCode($row->comment);
		}

		return $comments;
	}

	/**
	 * Process trackback when saving blog post
	 * - check if there un-ping URLs
	 * - mark as sent if necessary
	 *
	 * @param	Integer		Blog ID
	 * @param	Array		Trackbacks from user input
	 * @param	Object		User object
	 */
	public static function processTrackbacks($blogId, $trackbacks, $user)
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'trackback.php' );
		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

		$trackbackTbl	= EasyBlogHelper::getTable( 'TrackbackSent' , 'Table' );

		$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$author->setUser( $user );


		for ( $x = 0; $x < count($trackbacks); $x++ )
		{
			// check if the URL has been added to our record
			$exists	= $trackbackTbl->load( $trackbacks[$x] , true , $blogId );

			// if not exists, we need to store them
			if( !$exists )
			{
				$trackbackTbl->post_id	= $blog->id;
				$trackbackTbl->url		= $url;
				$trackbackTbl->sent		= 0;
				$trackbackTbl->store();
			}
		}

		// now load trackback model
		$trackbackModel = self::getModel('TrackbackSent');

		// get lists of trackback URLs based on blog ID
		$tbacks = $trackbackModel->getSentTrackbacks( $blogId, true );

		// loop each URL, ping if necessary
		for( $x = 0; $x < count($tbacks); $x++ )
		{
			$tb		= new EasyBlogTrackBack( $author->getName() , $author->getName() , 'UTF-8');
			$text	= empty( $blog->intro ) ? $blog->content : $blog->intro;
			if( $tb->ping( $tbacks->url , EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true ) , $blog->title , $text ) )
			{
				//@task: Since the trackback was successful, store the trackback into the table.

				$trackbackTbl->load($tbacks->id);

				$new_trackbacks				= array();
				$new_trackbacks['url']		= $tback->url;
				$new_trackbacks['post_id']	= $tback->post_id;
				$new_trackbacks['sent']		= 1;

				$trackbackTbl->bind($new_trackbacks);

				$trackbackTbl->store();
			}
		}
	}

	/**
	 * Given a page title, this method would try to find any existing menu items that are tied to the current page view.
	 * If a page title is tied, it will then use the page title defined in the menu.
	 */
	public static function getPageTitle( $default = '' )
	{
		$config			= EasyBlogHelper::getConfig();
		$itemid			= JRequest::getVar('Itemid', '');
		$originalTitle	= $default;

		// @task: If we can't detect the item id, just return the default page title that was passed in.
		if( empty( $itemid ) )
		{
			return $default;
		}

		// Prepare Joomla's site title if necessary.
		$jConfig 	= EasyBlogHelper::getJConfig();
		$addTitle 	= $jConfig->get( 'sitename_pagetitles' );

		// Only add Joomla's site title if it was configured to.
		if( $addTitle )
		{
			$siteTitle 	= $jConfig->get( 'sitename' );

			if( $addTitle == 1 )
			{
				$default 	= $siteTitle . ' - ' . $default;
			}

			if( $addTitle == 2 )
			{
				$default	= $default . ' - ' . $siteTitle;
			}

		}

		// @task: Let's find the menu item.
		$menu		= JFactory::getApplication()->getMenu();
		$item		= $menu->getItem($itemid);

		// @task: If configured to not append the blog title on the page, do not set any page title.
		if( !$config->get( 'main_pagetitle_autoappend' ) && $default == $config->get( 'main_title' ) )
		{
			$default 	= '';
		}

		// @task: If menu item cannot be found anywhere, just use the default
		if( !$item )
		{
			// @task: If default item is not empty just return the page title.
			return $default;
		}

		// @task: Let's get the page title from the menu.
		$params 	= EasyBlogHelper::getRegistry();
		$params->load( $item->params );
		$title 		= $params->get( 'page_title' , '' );

		// @task: If a title is found, just use the configured title.
		if( !empty( $title ) )
		{
			return $title;
		}

		return $default;
	}

	public static function loadHeaders()
	{
		$document = JFactory::getDocument();
		if ($document->getType()!=='html') return;

		if( !self::$headersLoaded )
		{
			$config 		= EasyBlogHelper::getConfig();
			$enableLightbox  = $config->get( 'main_media_lightbox_preview' ) ? 'true' : 'false';
			$lightboxTitle 	= $config->get( 'main_media_show_lightbox_caption' ) ? 'true' : 'false';
			$enforceLightboxSize = $config->get( 'main_media_lightbox_enforce_size' ) ? 'true' : 'false';
			$lightboxWidth = $config->get( 'main_media_lightbox_max_width' );
			$lightboxHeight = $config->get( 'main_media_lightbox_max_height' );
			$lightboxStripExtension = $config->get( 'main_media_lightbox_caption_strip_extension' ) ? 'true' : 'false';

			$lightboxWidth 			= ( empty( $lightboxWidth ) ) ? '640' : $lightboxWidth;
			$lightboxHeight 		= ( empty( $lightboxHeight ) ) ? '480' : $lightboxHeight;

			$url = EasyBlogHelper::getBaseUrl();

			$document	= JFactory::getDocument();

			// @task: Legacy ejax global variables.
			$ajaxData	=
"/*<![CDATA[*/
	var eblog_site 	= '" . $url . "';
	var spinnerPath = '" . EBLOG_SPINNER . "';
	var lang_direction	= '" . $document->direction . "';
	var eblog_lightbox_title = " . $lightboxTitle . ";
	var eblog_enable_lightbox = " . $enableLightbox . ";
	var eblog_lightbox_enforce_size = " . $enforceLightboxSize . ";
	var eblog_lightbox_width = " . $lightboxWidth . ";
	var eblog_lightbox_height = " . $lightboxHeight . ";
	var eblog_lightbox_strip_extension = " . $lightboxStripExtension . ";
/*]]>*/";

			$document->addScriptDeclaration( $ajaxData );

			EasyBlogHelper::addTemplateCss( 'common.css' );

			// Load EasyBlogConfiguration class
			require_once(EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'configuration.php');

			// Get configuration instance
			$configuration = EasyBlogConfiguration::getInstance();

			// Attach configuration to headers
			$configuration->attach();

			self::$headersLoaded = true;
		}

		return self::$headersLoaded;
	}

	/**
	 * Get Tweetmeme and Google Buzz social button to show
	 *
	 * @params	Object		The blog item
	 *
	 * @return	String		HTML code to be shown
	 */
	public static function showSocialButton( $blog , $frontpage = false )
	{
		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'socialbutton.php' );

		$config		= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();

		// get all required settings

		$isBottom   = false;

		// RTL enabled?
		$rtl			= $config->get('social_rtl', 1);
		$extraStyling	= '';

		// get prefered social button position
		$pos		= $config->get('main_socialbutton_position' , 'left' );
		$isBottom	= ($pos == 'bottom' || $pos == 'top') ? true : false;

		$extraStyling	.= ($pos == 'top') ? ' top' : '';
		$extraStyling	.= ($pos == 'bottom') ? ' bottom' : '';

		// in here, if the possition is bottom, we treat it as left for the css styling later.
		$pos		= ($pos == 'bottom' || $pos == 'top') ? 'left' : $pos;

		// if RTL setting is enabled
		if ( $rtl )
		{
			// only process this if the direction is RTK
			if ( $document->direction == 'rtl' )
			{
				// if user set position to left, we change it to right.
				// and the other way around too
				if ( $pos == 'left' )
				{
					$pos = 'right';
				}
				else
				{
					$pos = 'left';
				}
			}
		}

		$teamId = ( isset($blog->team) ) ? $blog->team : '';

		// initializing social buttons class object for later use.
		$button = EasyBlogHelper::getHelper('SocialButton');
		$button->setBlog($blog);
		$button->setFrontend($frontpage);
		$button->setPosition($pos);
		$button->setTeamId($teamId);
		$button->setBottom($isBottom);

		$html 				= '';
		$loadsocialbutton	= true;

		if($config->get('main_password_protect') && !empty($blog->blogpassword))
		{
			if(!EasyBlogHelper::verifyBlogPassword($blog->blogpassword, $blog->id))
			{
				$loadsocialbutton = false;
			}
		}

		if($loadsocialbutton)
		{
			$extraStyling	.= ( $isBottom ) ? ' width-full' : '';

			// sorting social buttons based on configuration.
			$socialButtons	= explode( ',', EBLOG_SOCIAL_BUTTONS );
			$socialButtonOrders = array();

			foreach($socialButtons as $key)
			{
				$config_key = 'integrations_order_' . $key;
				$socialButtonOrders[$key]   = $config->get( $config_key , '0');
			}

			asort($socialButtonOrders);
			foreach( $socialButtonOrders as $key => $val )
			{
				$html	.= $button->$key();
			}

			// only generate output if both or either one is enabled
			if ( !empty($html) )
			{
				$html = '<div id="socialbutton" class="align' . $pos . $extraStyling . '">' . $html . '</div>';
			}

		}

		echo $html;
	}

	// this function used to show the login form
	public static function showLogin($return='')
	{
		$my = JFactory::getUser();

		if($my->id == 0)
		{

			if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) {
				$comUserOption	= 'com_users';
				$tasklogin		= 'user.login';
				$tasklogout		= 'user.logout';
				$viewRegister	= 'registration';
				$inputPassword	= 'password';
			} else {
				$comUserOption	= 'com_user';
				$tasklogin		= 'login';
				$tasklogout		= 'logout';
				$viewRegister	= 'register';
				$inputPassword	= 'passwd';
			}

			if(empty($return))
			{
				$currentUri = JRequest::getURI();
				$uri		= base64_encode($currentUri);
			}
			else
			{
				$uri		= $return;
			}


			$tpl	= new CodeThemes();
			$tpl->set( 'return' , $uri );

			$tpl->set( 'comUserOption' , $comUserOption );
			$tpl->set( 'tasklogin' , $tasklogin );
			$tpl->set( 'tasklogout' , $tasklogout );
			$tpl->set( 'viewRegister' , $viewRegister );
			$tpl->set( 'inputPassword' , $inputPassword );

			echo $tpl->fetch( 'guest.login.php' );
		}
	}


	public static function getThemeObject( $name )
	{
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );

		$path	= EBLOG_THEMES;
		$file	= 'config.xml';

		if( !JFile::exists( $path . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $file ) )
		{
			return false;
		}

		$manifest	= JFile::read( $path . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $file );

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'xml.php' );
		$parser 	= new EasyBlogXMLHelper( $manifest );

		$obj			= new stdClass();
		$obj->element 	= $name;
		$obj->name		= $name;
		$obj->path		= $path . DIRECTORY_SEPARATOR . $name;
		$obj->writable	= is_writable( $path . DIRECTORY_SEPARATOR . $name );
		$obj->created	= JText::_( 'Unknown' );
		$obj->updated	= JText::_( 'Unknown' );
		$obj->author	= JText::_( 'Unknown' );
		$obj->version	= JText::_( 'Unknown' );
		$obj->desc		= JText::_( 'Unknown' );

		if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
		{
			$childrens		= $parser->children();

			foreach( $childrens as $key => $value )
			{
				if( $key == 'description' )
				{
					$key	= 'desc';
				}

				$obj->$key 	= (string) $value;
			}

			$obj->path			= $path . DIRECTORY_SEPARATOR . $name;
		}
		else
		{
			$created 			= $parser->document->getElementByPath( 'created' );

			if( $created)
			{
				$obj->created	= $created->data();
			}

			$updated			= $parser->document->getElementByPath( 'updated' );

			if( $updated )
			{
				$obj->updated	= $updated->data();
			}

			$author 			= $parser->document->getElementByPath( 'author' );

			if( $author )
			{
				$obj->author	= $author->data();
			}

			$version 			= $parser->document->getElementByPath( 'version' );

			if( $author )
			{
				$obj->version	= $version->data();
			}

			$description 		= $parser->document->getElementByPath( 'description' );

			if( $description )
			{
				$obj->desc		= $description->data();
			}

			$obj->path			= $path . DIRECTORY_SEPARATOR . $name;
		}


		return $obj;
	}

	public static function getThemeInfo( $name )
	{
		jimport( 'joomla.filesystem.file' );

		$mainframe	= JFactory::getApplication();

		$file =	'';

		// We need to specify if the template override folder also have config.ini file
		if ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'config.ini' ) )
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'config.ini';
		}

		// then check the current theme folder
		elseif ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog'. DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini' ) )
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini';
		}

		if( !empty( $file ) )
		{
			$raw		= JFile::read( $file );

			$registry 	= EasyBlogHelper::getRegistry( $raw );

			return $registry;
		}

		return EasyBlogHelper::getRegistry();
	}


	/**
	 * Generates a html code for category selection.
	 *
	 * @access	public
	 * @param	int		$parentId	if this option spcified, it will list the parent and all its childs categories.
	 * @param	int		$userId		if this option specified, it only return categories created by this userId
	 * @param	string	$outType	The output type. Currently supported links and drop down selection
	 * @param	string	$eleName	The element name of this populated categeries provided the outType os dropdown selection.
	 * @param	string	$default	The default selected value. If given, it used at dropdown selection (auto select)
	 * @param	boolean	$isWrite	Determine whether the categories list used in write new page or not.
	 * @param	boolean	$isPublishedOnly	If this option is true, only published categories will fetched.
	 * @param	array 	$exclusion	A list of excluded categories that it should not be including
	 */

	public static function populateCategories( $parentId , $userId , $outType , $eleName , $default , $isWrite = false , $isPublishedOnly = false , $isFrontendWrite = false , $exclusion = array() )
	{
		$catModel	 = EasyBlogHelper::getModel( 'Categories' );

		$parentCat	= null;

		if(! empty($userId))
		{
			$parentCat	= $catModel->getParentCategories($userId, 'blogger', $isPublishedOnly, $isFrontendWrite , $exclusion );
		}
		else if(! empty($parentId))
		{
			$parentCat	= $catModel->getParentCategories($parentId, 'category', $isPublishedOnly, $isFrontendWrite , $exclusion );
		}
		else
		{
			$parentCat	= $catModel->getParentCategories('', 'all', $isPublishedOnly, $isFrontendWrite , $exclusion );
		}

		$ignorePrivate	= false;

		switch($outType)
		{
			case 'link' :
				$ignorePrivate	= false;
				break;
			case 'popup':
			case 'select':
			default:
				$ignorePrivate	= true;
				break;
		}

		// Now let's do a loop to find it's child categories.
		if(! empty($parentCat))
		{
			for($i = 0; $i < count($parentCat); $i++)
			{
				$parent =& $parentCat[$i];

				//reset
				$parent->childs = null;

				EasyBlogHelper::buildNestedCategories( $parent->id, $parent, $ignorePrivate, $isPublishedOnly, $isFrontendWrite , $exclusion );
			}
		}

		if( $isWrite )
		{
			$defaultCatId	= EasyBlogHelper::getDefaultCategoryId();
			$default		= ( empty( $default ) ) ? $defaultCatId : $default;
		}

		$formEle		= '';

		if( $outType == 'select' && $isWrite )
		{
			$selected	= !$default ? ' selected="selected"' : '';
			$formEle	.= '<option value="0"' . $selected . '>' . JText::_( 'COM_EASYBLOG_SELECT_A_CATEGORY' ) . '</option>';
		}

		if( $parentCat )
		{
			foreach($parentCat as $category)
			{
				if($outType == 'popup')
				{
					$formEle	.= '<div class="category-list-item" id="'.$category->id.'"><a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $category->id. '\')">' .$category->title . '</a>';
					$formEle	.= '<input type="hidden" id="category-list-item-'.$category->id.'" value="'.$category->title.'" />';
					$formEle	.= '</div>';
				}
				else
				{
					$selected	= ($category->id == $default) ? ' selected="selected"' : '';
					$formEle	.= '<option value="'.$category->id.'" ' . $selected. '>' . JText::_( $category->title ) . '</option>';
				}

				EasyBlogHelper::accessNestedCategories($category, $formEle, '0', $default, $outType);
			}
		}

		$html	= '';
		$html	.= '<select name="' . $eleName . '" id="' . $eleName .'" class="inputbox">';
		if(! $isWrite)
			$html	.=	'<option value="0">' . JText::_('COM_EASYBLOG_SELECT_PARENT_CATEGORY') . '</option>';
		$html	.=	$formEle;
		$html	.= '</select>';

		return $html;
	}


	public static function buildNestedCategories($parentId, $parent, $ignorePrivate = false, $isPublishedOnly = false, $isWrite = false , $exclusion = array() )
	{
		$my		= JFactory::getUser();

		$catModel			= EasyBlogHelper::getModel( 'Categories' );
		$childs				= $catModel->getChildCategories($parentId, $isPublishedOnly, $isWrite , $exclusion );
		$accessibleCatsIds	= EasyBlogHelper::getAccessibleCategories( $parentId );

		if(! empty($childs))
		{
			for($j = 0; $j < count($childs); $j++)
			{
				$child	= $childs[$j];
				$child->childs = null;

				if(! $ignorePrivate)
				{
					if( count( $accessibleCatsIds ) > 0)
					{
						$access = false;
						foreach( $accessibleCatsIds as $canAccess)
						{
							if( $canAccess->id == $child->id)
							{
								$access = true;
							}
						}

						if( !$access )
							continue;

					}
					else
					{
						continue;
					}
				}

				if(! EasyBlogHelper::buildNestedCategories($child->id, $child, $ignorePrivate, $isPublishedOnly))
				{
					$parent->childs[]   = $child;
				}
			}// for $j
		}
		else
		{
			return false;
		}

	}


	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '')
	{
		if(isset($arr->childs) && is_array($arr->childs))
		{
			$sup	= '<sup>|_</sup>';
			$space	= '';
			$ld		= (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if($type == 'select' || $type == 'popup')
			{
				$deep++;
				for($d=0; $d < $deep; $d++)
				{
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			for($j	= 0; $j < count($arr->childs); $j++)
			{
				$child	= $arr->childs[$j];

				if($type == 'select')
				{
					$selected	= ($child->id == $default) ? ' selected="selected"' : '';

					$html	.= '<option value="'.$child->id.'" ' . $selected . '>' . $space . $sup . JText::_($child->title)  . '</option>';
				}
				else if($type == 'popup')
				{
					$html	.= '<div class="category-list-item" id="'.$child->id.'">' . $space . $sup . '<a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $child->id. '\')">' . JText::_($child->title) . '</a>';
					$html	.= '<input type="hidden" id="category-list-item-'.$child->id.'" value="'. JText::_($child->title) .'" />';
					$html	.= '</div>';
				}
				else
				{
					$str	= '<a href="' . EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$child->id) . '">' .  JText::_($child->title) . '</a>';
					$html	.= (empty($html)) ? $str : $ld . $str;
				}

				EasyBlogHelper::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter);
			}
		}
		else
		{
			return false;
		}
	}



	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs))
		{

			for($j	= 0; $j < count($arr->childs); $j++)
			{
				$child		= $arr->childs[$j];
				$newArr[]	= $child->id;
				EasyBlogHelper::accessNestedCategoriesId($child, $newArr);
			}
		}
		else
		{
			return false;
		}
	}


	/**
	 * function to retrieve the linkage backward from a child id.
	 * return the full linkage from child up to parent
	 */

	public static function populateCategoryLinkage($childId)
	{
		$arr		= array();
		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]		= $obj;

		if((!empty($category->parent_id)))
		{
			EasyBlogHelper::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr		= array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;



		$arr[]		= $obj;

		if((!empty($category->parent_id)))
		{
			EasyBlogHelper::accessCategoryLinkage($category->parent_id, $arr);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Get post title by ID
	 */
	public static function getPostTitle($id)
	{
		$db = EasyBlogHelper::db();

		$query = 'SELECT ' . $db->nameQuote('title') . ' FROM ' . $db->nameQuote('#__easyblog_post') . ' WHERE id = ' . $db->Quote($id);
		$db->setQuery($query);
		return $db->loadResult();
	}


	/**
	 * Check AlphaUserPoints Integration
	 */
	public static function isAUPEnabled()
	{
		jimport('joomla.filesystem.file');

		$config = EasyBlogHelper::getConfig();
		$aup	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_alphauserpoints' . DIRECTORY_SEPARATOR . 'helper.php';

		// make sure the config is enabled
		if ( $config->get('main_alpha_userpoint') ) {

			if ( JFile::exists( $aup ) )
			{
				require_once( $aup );
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public static function storeSession($data, $key, $ns = 'COM_EASYBLOG')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'COM_EASYBLOG')
	{
		$data	= null;
		$mySess	= JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$data = $mySess->get($key, '', $ns);
			$mySess->clear($key, $ns);
			return $data;
		}
		else
		{
			return $data;
		}
	}

	public static function clearSession($key, $ns = 'COM_EASYBLOG')
	{
		$mySess = JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$mySess->clear($key, $ns);
		}
		return true;
	}

	public static function isTeamBlogJoined($userId, $teamId)
	{
		$teamIds	= EasyBlogHelper::getViewableTeamIds($userId);
		return in_array($teamId, $teamIds);
	}

	// this function used to show the access denied page
	public static function showAccessDenied($type='', $access = '0')
	{
		$message = JText::_('COM_EASYBLOG_SORRY_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW');

		if($type == 'teamblog')
		{
			if($access == '1')
			{
				$message = JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY');
			}
		}


		$tpl	= new CodeThemes();
		$tpl->set( 'message' , $message );
		echo $tpl->fetch( 'access.denied.php' );
	}

	public static function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];


		return $jVersion;
	}

	public static function isJoomla30()
	{
		return EasyBlogHelper::getJoomlaVersion() >= '3.0';
	}

	public static function isJoomla25()
	{
		return EasyBlogHelper::getJoomlaVersion() >= '1.6' && EasyBlogHelper::getJoomlaVersion() <= '2.5';
	}

	public static function isJoomla15()
	{
		return EasyBlogHelper::getJoomlaVersion() == '1.5';
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */

	public static function getSAUsersIds()
	{
		$db = EasyBlogHelper::db();

		$query	= 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup = array();
		foreach($result as $group)
		{
			if(JAccess::checkGroup($group->id, 'core.admin'))
			{
				$saGroup[]  = $group;
			}
		}


		//now we got all the SA groups. Time to get the users
		$saUsers = array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr = JAccess::getUsersByGroup($sag->id);
				if(count($userArr) > 0)
				{
					foreach($userArr as $user)
					{
						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	public static function getDefaultSAIds()
	{
		$saUserId = '62';

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsers	= EasyBlogHelper::getSAUsersIds();
			$saUserId	= $saUsers[0];
		}

		return $saUserId;
	}

	/*
	 * Method for broswer detection
	 */
	private static function getBrowserUserAgent()
	{
		$browser = new stdClass;

		// set to lower case to avoid errors, check to see if http_user_agent is set
		$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

		// run through the main browser possibilities, assign them to the main $browser variable
		if (stristr($navigator_user_agent, "opera"))
		{
				$browser->userAgent = 'opera';
				$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "msie 8"))
		{
				$browser->userAgent = 'msie8';
				$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie 7"))
		{
				$browser->userAgent = 'msie7';
				$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie 4"))
		{
				$browser->userAgent = 'msie4';
				$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie"))
		{
				$browser->userAgent = 'msie';
				$browser->dom = true;
		}
		elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari")))
		{
				$browser->userAgent = 'safari';
				$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "gecko"))
		{
				$browser->userAgent = 'mozilla';
				$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "mozilla/4"))
		{
				$browser->userAgent = 'ns4';
				$browser->dom = false;
		}
		else
		{
				$browser->dom = false;
				$browser->userAgent = 'Unknown';
		}

		return $browser;
	}

	public static function getPDFlinkProperties()
	{
		$browser = EasyBlogHelper::getBrowserUserAgent();

		switch($browser->userAgent)
		{
			case 'msie8':
			case 'msie7':
				$properties = ' target="_BLANK" ';
				break;
			default:
				$properties = ' onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" ';
		}

		return $properties;
	}

	public static function getFirstImage( $content )
	{
		//try to search for the 1st img in the blog
		$img		= '';
		$pattern	= '#<img[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( $matches )
		{
			$img	= $matches[0];
		}


		//image found. now we process further to get the absolute image path.
		if(! empty($img) )
		{
			//get the img src

			$pattern = '/src\s*=\s*"(.+?)"/i';
			preg_match($pattern, $img, $matches);
			if($matches)
			{
				$imgPath	= $matches[1];
				$imgSrc		= EasyImageHelper::rel2abs($imgPath, JURI::root());

				return $imgSrc;
			}
		}

		return false;
	}

	public static function getFBInitScript()
	{
		$config		= EasyBlogHelper::getConfig();
		$fbScript	= '';

		if($config->get('main_facebook_like'))
		{
			$fbScript = '<div id="fb-root"></div>';
			$fbScript .= '<script type="text/javascript">';
			$fbScript .= '	window.fbAsyncInit = function() {';
			$fbScript .= '		FB.init({appId: \'' . $config->get('main_facebook_like_admin') . '\', status: true, cookie: true,';
			$fbScript .= '			xfbml: true});';
			$fbScript .= '	};';
			$fbScript .= '	(function() {';
			$fbScript .= '		var e = document.createElement(\'script\'); e.async = true;';
			$fbScript .= '		e.src = document.location.protocol +';
			$fbScript .= '			\'//connect.facebook.net/en_US/all.js\';';
			$fbScript .= '		document.getElementById(\'fb-root\').appendChild(e);';
			$fbScript .= '	}());';
			$fbScript .= '</script>';
		}

		return $fbScript;
	}

	public static function getBloggerTheme()
	{
		$id		= EasyBlogRouter::isBloggerMode();

		if( empty( $id ) )
		{
			return false;
		}

		$profile = EasyBlogHelper::getTable('Profile', 'Table');
		$profile->load( $id );

		$userparams	= EasyBlogHelper::getRegistry( $profile->params );

		return $userparams->get('theme', false);
	}

	public static function getFeaturedImage( $content )
	{
		$pattern = '#<img class="featured[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return $matches[0];
		}

		// If featured image is not supplied, try to use the first image as the featured post.
		$pattern				= '#<img[^>]*>#i';

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return $matches[0];
		}

		// If all else fail, try to use the default image
		return false;
	}

	public static function getJoomlaUserGroups( $cid = '' )
	{
		$db = EasyBlogHelper::db();

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
			$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
			$query .= ' FROM #__usergroups AS a';
			$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		}
		else
		{
			$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' a ';
		}

		// condition
		$where  = array();

		// we need to filter out the ROOT and USER dummy records.
		if(EasyBlogHelper::getJoomlaVersion() < '1.6')
		{
			$where[] = '(a.`id` > 17 AND a.`id` < 26)';
		}

		if( !empty( $cid ) )
		{
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}
		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		$query .= $where;

		// grouping and ordering
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$query	.= ' GROUP BY a.id';
			$query	.= ' ORDER BY a.lft ASC';
		}
		else
		{
			$query 	.= ' ORDER BY a.id';
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getUserGids( $userId = '' )
	{
		$user	= '';

		if( empty($userId) )
		{
			$user	= JFactory::getUser();
		}
		else
		{
			$user	= JFactory::getUser($userId);
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$groupIds = JAccess::getGroupsByUser($user->id, false);

			$grpId		= array();

			foreach($groupIds as $key => $val)
			{
				$grpId[] = $val;
			}

			if( empty($grpId) )
			{
				//this case shouldn't happen but it happened. sigh.
				$grpId[] = '1';
			}

			return $grpId;
		}
		else
		{
			if( $user->gid == '' )
			{
				//this case shouldn't happen but it happened. sigh.
				return array('0');
			}


			return array( $user->gid );
		}
	}

	public static function getAccessibleCategories( $parentId = 0 )
	{
		$db			= EasyBlogHelper::db();
		$my			= JFactory::getUser();

		$gids		= '';
		$catQuery	= 	'select distinct a.`id`, a.`private`';
		$catQuery	.=  ' from `#__easyblog_category` as a';
		$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0');

		if( $my->id != 0 )
		{
			$catQuery	.=  ' OR a.`private` = ' . $db->Quote('1');
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$gid	= array();
			if( $my->id == 0 )
			{
				$gid	= JAccess::getGroupsByUser(0, false);
			}
			else
			{
				$gid	= JAccess::getGroupsByUser($my->id, false);
			}

			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			$catQuery	.= ' OR a.`id` IN (';
			$catQuery	.= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
			$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
			$catQuery	.= ')';
		}
		else
		{
			$gid    = array();

			if( $my->id == 0 )
			{
				$gid[] = '0';
			}
			else
			{
				$gid	= EasyBlogHelper::getUserGids();
			}

			$gid	= EasyBlogHelper::getUserGids();

			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}


				$catQuery	.= ' OR a.`id` IN (';
				$catQuery	.= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
				$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
				$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
				$catQuery	.= ')';
			}

		}

		if( $parentId )
		{
			$catQuery   .= ' AND a.parent_id = ' . $db->Quote($parentId);
		}

		$db->setQuery($catQuery);
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getPrivateCategories()
	{
		$db				= EasyBlogHelper::db();
		$my				= JFactory::getUser();
		$excludeCats	= array();

		$catQuery = '';
		// get all private categories id
		if($my->id == 0)
		{
			$catQuery	= 	'select distinct a.`id`, a.`private`';
			$catQuery	.=  ' from `#__easyblog_category` as a';
			$catQuery	.=	' 	left join `#__easyblog_category_acl` as b on a.`id` = b.`category_id` and b.`acl_id` = ' . $db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery	.=  ' where a.`private` != ' . $db->Quote('0');

			$gid	= array();
			$gids	= '';

			if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
			{
				$gid	= JAccess::getGroupsByUser(0, false);
			}
			else
			{
				$gid	= EasyBlogHelper::getUserGids();
			}

			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
				$catQuery	.= ' and b.`category_id` NOT IN (';
				$catQuery	.= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
				$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
				$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
			}

		}
		else
		{
			$gid	= EasyBlogHelper::getUserGids();
			$gids   = '';
			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			$catQuery = 'select id from `#__easyblog_category` as a';
			$catQuery .= ' where not exists (';
			$catQuery .= '		select b.category_id from `#__easyblog_category_acl` as b';
			$catQuery .= '			where b.category_id = a.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery .= '			and b.type = ' . $db->Quote('group');
			$catQuery .= '			and b.content_id IN (' . $gids . ')';
			$catQuery .= '      )';
			$catQuery .= ' and a.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		}

		if( !empty( $catQuery ) )
		{

			$db->setQuery($catQuery);
			$result = $db->loadObjectList();

			for($i=0; $i < count($result); $i++)
			{
				$item			=& $result[$i];
				$item->childs	= null;

				EasyBlogHelper::buildNestedCategories($item->id, $item);

				$catIds		= array();
				$catIds[]	= $item->id;
				EasyBlogHelper::accessNestedCategoriesId($item, $catIds);

				$excludeCats	= array_merge($excludeCats, $catIds);
			}
		}

		return $excludeCats;
	}

	public static function getViewableTeamIds($userId = '')
	{

		$db	= EasyBlogHelper::db();
		$my	= '';

		if( empty($userId) )
		{
			$my = JFactory::getUser();
		}
		else
		{
			$my = JFactory::getUser($userId);
		}

		$teamBlogIds = '';


		if( $my->id == 0)
		{
			//get team id with access == 3
			$query	= 'select `id` FROM `#__easyblog_team` where `access` = ' . $db->Quote( '3' );
			$query	.= ' and `published` = ' . $db->Quote( '1' );
			$db->setQuery($query);

			$result	= $db->loadResultArray();
			return $result;
		}
		else
		{

			$gid	= EasyBlogHelper::getUserGids( $userId );
			$gids	= '';
			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			// get the teamid from this user.
			$query	= 'select distinct `id` from `#__easyblog_team` as t left join `#__easyblog_team_users` as tu on t.id = tu.team_id';
			$query	.= ' left join `#__easyblog_team_groups` as tg on t.id = tg.team_id';
			$query	.= ' where t.`published` = ' . $db->Quote( '1' );
			$query	.= ' and (tu.`user_id` = ' . $db->Quote( $my->id );
			$query	.= ' OR t.`access` IN (2, 3)';
			$query	.= ' OR tg.group_id IN (' . $gids . ')';
			$query	.= ')';

			$db->setQuery($query);

			$result = $db->loadResultArray();
			return $result;
		}

	}

	public static function getDefaultCategoryId()
	{
		$db = EasyBlogHelper::db();

		$gid	= EasyBlogHelper::getUserGids();
		$gids	= '';
		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$query	= 'SELECT a.id';
		$query	.= ' FROM `#__easyblog_category` AS a';
		$query	.= ' WHERE a.`published` = ' . $db->Quote( '1' );
		$query	.= ' AND a.`default` = ' . $db->Quote( '1' );
		$query	.= ' and a.id not in (';
		$query	.= ' 	select id from `#__easyblog_category` as c';
		$query	.= ' 	where not exists (';
		$query	.= '			select b.category_id from `#__easyblog_category_acl` as b';
		$query	.= '				where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
		$query	.= '				and b.type = ' . $db->Quote('group');
		$query	.= '				and b.content_id IN (' . $gids . ')';
		$query	.= '		)';
		$query	.= '	and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		$query	.= '	)';
		$query	.= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';
		$query	.= ' ORDER BY a.`lft` LIMIT 1';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty( $result ) ) ? '0' : $result ;
	}

	public static function isBlogger( $userId )
	{
		if( empty( $userId ) )
			return false;

		$acl = EasyBlogACLHelper::getRuleSet( $userId );
		if( $acl->rules->add_entry )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public static function getUniqueFileName($originalFilename, $path)
	{
		$ext			= JFile::getExt($originalFilename);
		$ext			= $ext ? '.'.$ext : '';
		$uniqueFilename	= JFile::stripExt($originalFilename);

		$i = 1;

		while( JFile::exists($path.DIRECTORY_SEPARATOR.$uniqueFilename.$ext) )
		{
			// $uniqueFilename	= JFile::stripExt($originalFilename) . '-' . $i;
			$uniqueFilename	= JFile::stripExt($originalFilename) . '_' . $i . '_' . EasyBlogHelper::getDate()->toFormat( "%Y%m%d-%H%M%S" );
			$i++;
		}

		//remove the space into '-'
		$uniqueFilename = str_ireplace(' ', '-', $uniqueFilename);

		return $uniqueFilename.$ext;
	}

	public static function getCategoryMenuBloggerId()
	{
		$itemId			= JRequest::getInt('Itemid', 0);
		$menu			= JFactory::getApplication()->getMenu();
		$menuparams		= $menu->getParams( $itemId );
		$catBloggerId	= $menuparams->get('cat_bloggerid', '');

		return $catBloggerId;
	}

	// Add canonical URL to satify Googlebot. Incase they think it's duplicated content.
	public static function addCanonicalURL( $extraFishes = array() )
	{
		if (empty( $extraFishes ))
		{
			return;
		}

		$juri = JURI::getInstance();

		foreach( $extraFishes as $fish )
		{
			$juri->delVar( $fish );
		}

		$preferredURL	= $juri->toString();

		jimport('joomla.filter.filterinput');
		$inputFilter	= JFilterInput::getInstance();
		$preferredURL	= $inputFilter->clean($preferredURL, 'string');

		$document	= JFactory::getDocument();
		$document->addHeadLink( $preferredURL, 'canonical', 'rel');
	}



	/*
	 * below are the accepted keys for the object or array
	 * [actor_id]
	 * [target_id] - optional. default 0
	 * [context_type]
	 * [context_id]
	 * [verb]
	 * [source_id]
	 */
	public static function activityLog( $data )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'stream.php' );

		$logger = new EasyBlogStream();
		return $logger->add($data);
	}

	public static function activityGet( $userId, $limit = 0, $startlimit = 0, $withLimit = true )
	{
		require_once( EBLOG_CLASSES . '/stream.php' );

		$logger		= new EasyBlogStream();
		$data[0]	= $logger->get($userId , $limit, $startlimit);
		$data[1]	= $logger->getDateData();

		if( $withLimit )
		{
			return $data;
		}
		else
		{
			return $data[0];
		}
	}

	public static function activityHasNextItems( $userId, $limit = 0, $startdate )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'stream.php' );

		$logger	= new EasyBlogStream();

		$data	= array();
		$data	= $logger->_getDateRange($userId, $limit, $startdate);

		return $data;

	}

	public static function addScriptDeclaration( $code='' )
	{
		return '<script type="text/javascript">EasyBlog.ready(function($){' . $code . '});</script>';
	}

	public static function addScriptDeclarationBookmarklet( $code='' )
	{
		return '<script type="text/javascript">EasyBlog.require().library("bookmarklet").done(function($){'. $code . '});</script>';
	}

	public static function getUnsubscribeLink($subdata, $external=false)
	{
		$easyblogItemId	= EasyBlogRouter::getItemId( 'latest' );
		$unsubdata		= base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->user_id."\r\ntoken=".md5($subdata->id.$subdata->created));

		return EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&controller=subscription&task=unsubscribe&data='.$unsubdata.'&Itemid=' . $easyblogItemId, false, $external);
	}

	public static function getEditProfileLink()
	{
		$default 	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile');
		$config 	= EasyBlogHelper::getConfig();

		if( $config->get( 'integrations_easysocial_editprofile' ) )
		{
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

			if( $easysocial->exists() )
			{
				$default 	= FRoute::profile( array( 'layout' => 'edit' ) );
			}
		}

		return $default;
	}

	public static function getRegistrationLink()
	{
		$config 	= EasyBlogHelper::getConfig();
		$default	= JRoute::_( 'index.php?option=com_user&view=register' );

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?com_easysocial&view=registration' );
		}

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':
				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::registration();
				}
				else
				{
					$link 	= $default;
				}

				break;

			case 'cb':
				$link 	= JRoute::_( 'index.php?option=com_comprofiler&task=registers' );
				break;
			break;

			case 'joomla':

				$link 	= $default;

			break;

			case 'jomsocial':
				$link	= JRoute::_( 'index.php?option=com_community&view=register' );
			break;
		}

		return $link;
	}

	public static function getLoginLink( $returnURL = '' )
	{
		$config 	= EasyBlogHelper::getConfig();

		if( !empty( $returnURL ) )
		{
			$returnURL	= '&return=' . $returnURL;
		}

		$default 	= EasyBlogRouter::_( 'index.php?option=com_user&view=login' . $returnURL );


		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$default 	= EasyBlogRouter::_('index.php?option=com_easysocial&view=login' . $returnURL );
		}

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::login();
				}
				else
				{
					$link 	= $default;
				}

			break;

			case 'cb':
				$link 	= JRoute::_( 'index.php?option=com_comprofiler&task=login' . $returnURL);
				break;
			break;

			case 'joomla':
			case 'jomsocial':
				$link 	= $default;
			break;
		}

		return $link;
	}

	public static function getResetPasswordLink()
	{
		$config		= EasyBlogHelper::getConfig();
		$default	= JRoute::_( 'index.php?option=com_user&view=reset' );

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?com_easysocial&view=reset' );
		}


		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::profile( array( 'layout' => 'forgetPassword' ) );
				}
				else
				{
					$link 	= $default;
				}

			break;

			case 'cb':
				$link 		= JRoute::_( 'index.php?option=com_comprofiler&task=lostpassword' );
			break;

			case 'joomla':
			case 'jomsocial':

				$link 	= $default;
			break;
		}

		return $link;
	}

	public static function getRemindUsernameLink()
	{
		$config 	= EasyBlogHelper::getConfig();

		$default	= JRoute::_( 'index.php?option=com_user&view=remind' );

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?com_easysocial&view=remind' );
		}

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'easysocial':

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::profile( array( 'layout' => 'forgetPassword' ) );
				}
				else
				{
					$link 	= $default;
				}

			break;

			default:

				$link 	= $default;

			break;
		}

		return $link;
	}

	public static function log( $var = '', $force = 0 )
	{
		$debugroot = EBLOG_HELPERS . '/debug';

		$firephp = false;
		$chromephp = false;

		if( JFile::exists( $debugroot . '/fb.php' ) && JFile::exists( $debugroot . '/FirePHP.class.php' ) )
		{
			include_once( $debugroot . '/fb.php' );
			fb( $var );
		}

		if( JFile::exists( $debugroot . '/chromephp.php' ) )
		{
			include_once( $debugroot . '/chromephp.php' );
			ChromePhp::log( $var );
		}
	}

	/**
	 * Legacy method for installations prior to 3.5
	 *
	 * @deprecated 3.5
	 * @since 3.5
	 */
	public function processVideos( $content , $created_by )
	{
		return EasyBlogHelper::getHelper( 'Videos' )->processVideos( $content );
	}

	public static function getCategoryInclusion( $categories )
	{
		$inclusion  = $categories;

		if( !empty( $categories ) && $categories == 'all' )
		{
			$inclusion  = '';
		}
		else
		{
			if( is_array($categories) )
			{
				if( in_array('all', $categories) )
				{
					$inclusion  = '';
				}
			}
			else
			{
				if( !empty( $categories ) )
				{
					$inclusion  = explode( ',', $categories );
				}
			}
		}

		return $inclusion;
	}


	public static function uniqueLinkSegments( $urls = '' )
	{
		if( $urls )
		{
			$container  = explode('/', $urls);
			$container	= array_unique($container);
			$urls = implode('/', $container);
		}
		return $urls;
	}

	public static function getBaseUrl()
	{
		static $url;

		if (isset($url)) return $url;

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$uri		= JFactory::getURI();
			$language	= $uri->getVar( 'lang' , 'none' );

			// Remove any ' or " from the language because language should only have -
			$language 	= JRequest::getCmd('lang', '');
			$app		= JFactory::getApplication();
			$config		= EasyBlogHelper::getJConfig();
			$router		= $app->getRouter();
			$url		= rtrim( JURI::base() , '/' );

			$url 		= $url . '/index.php?option=com_easyblog&lang=' . $language;

			if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
			{
				$rewrite	= $config->get('sef_rewrite');

				$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
				$path		=  $rewrite ? $base : JString::substr( $base , 10 );
				$path		= JString::trim( $path , '/' );
				$parts		= explode( '/' , $path );

				if( $parts )
				{
					// First segment will always be the language filter.
					$language	= reset( $parts );
				}
				else
				{
					$language	= 'none';
				}

				if( $rewrite )
				{
					$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_easyblog';
					$language	= 'none';
				}
				else
				{
					$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_easyblog';
				}
			}
		}
		else
		{

			$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog';
		}

		$menu = JFactory::getApplication()->getmenu();

		if( !empty($menu) )
		{
			$item = $menu->getActive();
			if( isset( $item->id) )
			{
				$url    .= '&Itemid=' . $item->id;
			}
		}

		// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix.
		// Need to sort them out here.
		$currentURL		= isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '';

		if( !empty( $currentURL ) )
		{
			// When the url contains www and the current accessed url does not contain www, fix it.
			if( stristr($currentURL , 'www' ) === false && stristr( $url , 'www') !== false )
			{
				$url	= str_ireplace( 'www.' , '' , $url );
			}

			// When the url does not contain www and the current accessed url contains www.
			if( stristr( $currentURL , 'www' ) !== false && stristr( $url , 'www') === false )
			{
				$url	= str_ireplace( '://' , '://www.' , $url );
			}
		}

		return $url;
	}

}

class EBC extends EasyBlogHelper {}

}


class EB extends EBC {}
{
}
