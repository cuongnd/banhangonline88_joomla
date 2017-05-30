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

jimport('joomla.application.component.controller');

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_ROOT . DIRECTORY_SEPARATOR . 'controller.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'acl.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'socialshare.php' );

@mb_regex_encoding('UTF-8');
@mb_internal_encoding('UTF-8');

class EasyBlogControllerXMLRPC extends EasyBlogParentController
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct()
	{
		// Include the tables in path
		JLoader::import( 'xmlrpc', JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'phpxmlrpc' );
		JLoader::import( 'xmlrpcs', JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'phpxmlrpc' );

		parent::__construct();
	}

	public function display($cachable = false, $urlparams = array())
	{
		$methods = $this->_getServices();

		// Create a new xmlrpc server here
		$server = new xmlrpc_server($methods, false);

		// Allow casting to be defined by that actual values passed
		$server->functions_parameters_type = 'phpvals';

		// Set the encoding to UTF-8
		$server->xml_header('UTF-8');

		// Set the debug level
		$server->setDebug(0);

		// start the service
		$server->service();

		exit;
	}


	function _getServices()
	{
		global $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		return array
		(
			// blogger API
			'blogger.getUsersBlogs' => array(
				'function' => 'EasyBlogXMLRPCServices::getUserBlogs',
				'docstring' => JText::_('Returns a list of weblogs to which an author has posting privileges.'),
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),
			'blogger.getUserInfo' => array(
				'function' => 'EasyBlogXMLRPCServices::getUserInfo',
				'docstring' => JText::_('Returns information about an author in the system.'),
				'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),
			'blogger.deletePost' => array(
				'function' => 'EasyBlogXMLRPCServices::deletePost',
				'docstring' => 'Deletes a post.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean))
			),
			'blogger.getTemplate' => array(
				'function' => 'EasyBlogXMLRPCServices::deletePost',
				'docstring' => 'Deletes a post.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean))
			),

			// metaWeblog API
			'metaWeblog.getUsersBlogs' => array(
				'function' => 'EasyBlogXMLRPCServices::getUserBlogs',
				'docstring' => JText::_('Returns a list of weblogs to which an author has posting privileges.'),
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),
			'metaWeblog.getUserInfo' => array(
				'function' => 'EasyBlogXMLRPCServices::getUserInfo',
				'docstring' => JText::_('Returns information about an author in the system.'),
				'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),
			'metaWeblog.deletePost' => array(
				'function' => 'EasyBlogXMLRPCServices::deletePost',
				'docstring' => 'Deletes a post.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean))
			),
			'metaWeblog.newPost' => array(
				'function' => 'EasyBlogXMLRPCServices::newPost',
				'docstring' => 'Creates a new post, and optionally publishes it.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct, $xmlrpcBoolean))
			),
			'metaWeblog.editPost' => array(
				'function' => 'EasyBlogXMLRPCServices::editPost',
				'docstring' => 'Updates the information about an existing post.',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct, $xmlrpcBoolean))
			),
			'metaWeblog.getPost' => array(
				'function' => 'EasyBlogXMLRPCServices::getPost',
				'docstring' => 'Returns information about a specific post.',
				'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),
			'metaWeblog.getCategories' => array(
				'function' => 'EasyBlogXMLRPCServices::getCategories',
				'docstring' => 'Returns the list of categories',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),
			'metaWeblog.getRecentPosts' => array(
				'function' => 'EasyBlogXMLRPCServices::getRecentPosts',
				'docstring' => 'Returns a list of the most recent posts in the system.',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcInt))
			),
			'metaWeblog.newMediaObject' => array(
				'function' => 'EasyBlogXMLRPCServices::newMediaObject',
				'docstring' => 'Uploads media to the blog.',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct))
			),


			// WordPress API
			'wp.getUsersBlogs'	=> array(
				'function' => 'EasyBlogXMLRPCServices::getUserBlogs',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.getAuthors'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_getAuthors',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.getCategories' => array(
				'function' => 'EasyBlogXMLRPCServices::getCategories',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.getTags' => array(
				'function' => 'EasyBlogXMLRPCServices::wp_getTags',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.newCategory'=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_newCategory',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct ))
			),

			'wp.deleteCategory'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_deleteCategory',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.suggestCategories'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_suggestCategories',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.uploadFile'	=> array(
				'function' => 'EasyBlogXMLRPCServices::newMediaObject',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.getPostStatusList'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_getPostStatusList',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.getOptions'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_getOptions',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcArray ))
			),

			'wp.setOptions'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_setOptions',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct ))
			),

			'wp.getCommentCount'=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_getCommentCount',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.getComment'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_getComment',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.getComments' => array(
				'function' => 'EasyBlogXMLRPCServices::wp_getComments',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct ))
			),

			'wp.deleteComment' => array(
				'function' => 'EasyBlogXMLRPCServices::wp_deleteComment',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),
			'wp.editComment' => array(
				'function' => 'EasyBlogXMLRPCServices::wp_editComment',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct ))
			),
			'wp.newComment'	=> array(
				'function' => 'EasyBlogXMLRPCServices::wp_newComment',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct ))
			),

			'wp.getCommentStatusList' => array(
				'function' => 'EasyBlogXMLRPCServices::wp_getCommentStatusList',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'wp.newPage' => array(
				'function' => 'EasyBlogXMLRPCServices::newPost',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			// MovableType API
			'mt.setPostCategories' 	=> array(
				'function' => 'EasyBlogXMLRPCServices::mt_setPostCategories',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcArray ))
			),

			'mt.getPostCategories' 	=> array(
				'function' => 'EasyBlogXMLRPCServices::mt_getPostCategories',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'mt.getCategoryList' => array(
				'function' => 'EasyBlogXMLRPCServices::getCategories',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'mt.publishPost' => array(
				'function' => 'EasyBlogXMLRPCServices::mt_publishPost',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			)


		);
	}

}

class EasyBlogXMLRPCServices
{
	public static function mt_publishPost($postid, $username, $password)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$db = EasyBlogHelper::db();

		$query  = 'UPDATE `#__easyblog_post` SET `published` = ' . $db->Quote('1');
		$query  .= ' WHERE `id` = ' . $db->Quote($postid);
		$db->setQuery($query);
		$db->query();

		return new xmlrpcresp(new xmlrpcval(true, $xmlrpcBoolean));
	}


	public static function wp_getPostStatusList($blogid, $username, $password)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$structArray = new xmlrpcval(
			array(
				'private'		=> new xmlrpcval(JText::_('Private')),
				'publish'		=> new xmlrpcval(JText::_('Published'))
			), $xmlrpcStruct);

		return new xmlrpcresp($structArray);

	}


	public static function wp_suggestCategories($blogid, $username, $password, $category, $max)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$db		= EasyBlogHelper::db();

		$query	= 'SELECT * FROM `#__easyblog_category`';
		$query	.= ' where `published` = ' .  $db->Quote( '1' );
		$query  .= ' and `title` LIKE ' . $db->Quote('%'.$category.'%');
		$query  .= ' order by `title`';
        $query  .= (empty($max)) ? '' : ' LIMIT ' . $max;

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		if (!$categories) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, 'No categories available, or an error has occured.' );
		}

		$structArray = array();
		foreach ($categories as $category)
		{
			$structArray[] = new xmlrpcval(array(
				"category_id"	=> new xmlrpcval($category->id),
				"category_name"	=> new xmlrpcval($category->title)
			), 'struct');
		}

		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}


	public static function wp_deleteCategory($blogid, $username, $password, $categoryid)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

        $my = JFactory::getUser($username);
        $db = EasyBlogHelper::db();

        // TODO : check ACL if the user allow to create new category.
		$acl		= EasyBlogACLHelper::getRuleSet($my->id);
		$config     = EasyBlogHelper::getConfig();

		if( empty($acl->rules->create_category) )
		{
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('NO PERMISSION TO DELETE CATEGORY'));
		}

		if(empty($categoryid))
		{
		    return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('EMPTY CATEGORY'));
		}

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );
	    $catTbl	= EasyBlogHelper::getTable( 'Category', 'Table' );
	    $catTbl->load($categoryid);

	    if(! $catTbl->delete())
	    {
	        return new xmlrpcresp(0, $xmlrpcerruser+2, JText::_('FAILED TO DELETE CATEGORY'));
	    }

		return new xmlrpcresp(new xmlrpcval(true, $xmlrpcBoolean));
	}


	public static function mt_getPostCategories($postid, $username, $password)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$db = EasyBlogHelper::db();

		$query  = 'SELECT b.`title`, a.`category_id` FROM `#__easyblog_post` as a';
		$query	.= ' INNER JOIN `#__easyblog_category` as b ON a.`category_id` = b.`id`';
		$query  .= ' WHERE a.`id` = ' . $db->Quote($postid);

		$db->setQuery($query);
		$result = $db->loadObject();

		//Set categories
		$category = array();

		if(! empty($result))
		{
			$category[] = new xmlrpcval(array(
				'categoryName'	=> new xmlrpcval($result->title, $xmlrpcString),
				'categoryId'    => new xmlrpcval($result->category_id, $xmlrpcString),
				'isPrimary'     => new xmlrpcval(true, $xmlrpcBoolean)
			), $xmlrpcStruct);
		}

		return new xmlrpcresp(new xmlrpcval( $category , $xmlrpcArray));
	}

	public static function mt_setPostCategories($postid, $username, $password, $categories)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$catId  = $categories[0]['categoryId'];

		$db = EasyBlogHelper::db();
		$query  = 'UPDATE `#__easyblog_post` SET `category_id` = ' . $db->Quote( $catId );
		$query  .= ' WHERE `id` = ' . $db->Quote( $postid );

		$db->setQuery($query);
		$db->query();

		// Nothing to process. Just return true for now.
		return new xmlrpcresp(new xmlrpcval(true, $xmlrpcBoolean));
	}

	public static function wp_newCategory($blogid, $username, $password, $category)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

        $my = JFactory::getUser($username);
        $db = EasyBlogHelper::db();

        // TODO : check ACL if the user allow to create new category.
		$acl		= EasyBlogACLHelper::getRuleSet($my->id);
		$config     = EasyBlogHelper::getConfig();

		if( empty($acl->rules->create_category) )
		{
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('NO PERMISSION TO CREATE CATEGORY'));
		}

	    //check whether the category already created.
	    $catName    = $category["name"];

	    $query  = 'SELECT `id` FROM `#__easyblog_category`';
		$query  .= ' WHERE `title` = ' . $db->Quote($catName);

		$db->setQuery($query);
		$cid    = $db->loadResult();

		if($cid != '')
		{
			return new xmlrpcresp(new xmlrpcval($cid, $xmlrpcString));
		}

        $newCategory   = array();
	    $newCategory['title'] 		= $category["name"];
	    $newCategory['alias'] 		= (empty($category["slug"])) ? '' : $category["slug"];
	    $newCategory['created_by'] 	= $my->id;
	    $newCategory['parent_id'] 	= (isset($category["parent_id"])) ? $category["parent_id"] : '0';
	    $newCategory['private'] 	= '0';
	    $newCategory['published'] 	= '1';

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );
	    $catTbl	= EasyBlogHelper::getTable( 'Category', 'Table' );
        $catTbl->bind($newCategory);
        $catTbl->store(); //save the cat 1st so that the id get updated

		return new xmlrpcresp(new xmlrpcval($catTbl->id, $xmlrpcString));
	}



	public static function wp_getTags($appkey, $username, $password )
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

        EasyBlogXMLRPCHelper::loginUser($username, $password);

        $my = JFactory::getUser($username);
		$db = EasyBlogHelper::db();

		$query	= 'SELECT * FROM `#__easyblog_tag`';
		$query	.= ' where `published` = ' .  $db->Quote( '1' );
		$query  .= ' order by `title`';

		$db->setQuery($query);
		$items = $db->loadObjectList();

		$structArray = array();
		if(count($items) > 0)
		{
			foreach ($items as $item)
			{
				$structArray[] = new xmlrpcval(array(
					'tag_id'	=> new xmlrpcval($item->id),
					'name'      => new xmlrpcval($item->title),
					'count'     => new xmlrpcval('0'),
					'slug'      => new xmlrpcval($item->alias),
					'html_url'	=> new xmlrpcval('')
				), $xmlrpcStruct);
			}
		}

		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}


	public static function wp_getAuthors($appkey, $username, $password)
	{
	    /*
		 * for now we just return the current user.
		 */

        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

        EasyBlogXMLRPCHelper::loginUser($username, $password);

        $my = JFactory::getUser($username);

		$structArray = array();
		$structArray[] = new xmlrpcval(array(
		    'user_id'		=> new xmlrpcval($my->id, $xmlrpcBoolean),
			'user_login'	=> new xmlrpcval($my->username, $xmlrpcString),
			'display_name'	=> new xmlrpcval($my->name, $xmlrpcString)
			), 'struct');

       return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}

	public static  function wp_setOptions($blogid, $username, $password, $options)
	{
	    // currently this method not supported.
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

	    //return new xmlrpcresp(new xmlrpcval( $options , $xmlrpcArray));
	    return new xmlrpcresp(0, $xmlrpcerruser+1, 'Currently this feature not supported.');
	}


	public static function wp_getOptions($blogid, $username, $password, $options)
	{
	    // currently this method not supported.

        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

	    return new xmlrpcresp(0, $xmlrpcerruser+1, 'Currently this feature not supported.');
	}

	/* Wp api: currently we do not support comment features */
	public static function wp_getCommentStatusList($blogid, $username, $password)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

	    return new xmlrpcresp(0, $xmlrpcerruser+1, 'Currently this feature not supported.');
	}


	public static function wp_newComment($blogid, $username, $password, $postid, $data)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

	    return new xmlrpcresp(0, $xmlrpcerruser+1, 'Currently this feature not supported.');
	}

	public static function wp_editComment($blogid, $username, $password, $commentid, $data)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

	    return new xmlrpcresp(0, $xmlrpcerruser+1, 'Currently this feature not supported.');
	}

	public static function wp_deleteComment($blogid, $username, $password, $commentid)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

	    return new xmlrpcresp(0, $xmlrpcerruser+1, 'Currently this feature not supported.');
	}


	public static function wp_getComments($blogid, $username, $password, $data)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

	    return new xmlrpcresp(0, $xmlrpcerruser+1, 'Currently this feature not supported.');
	}


	public static function wp_getComment($blogid, $username, $password, $commentid)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_( 'Currently this feature not suported.' ) );
	}

	public static function wp_getCommentCount($blogid, $username, $password, $postid)
	{
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$structArray = new xmlrpcval(
			array(
				"approved" 				=> new xmlrpcval('0'),
				"awaiting_moderation" 	=> new xmlrpcval('0'),
				"spam" 					=> new xmlrpcval('0'),
				"total_comments" 		=> new xmlrpcval('0')
			), $xmlrpcStruct);

		return new xmlrpcresp($structArray);
	}

	/* metaweblog api */
	public static function getUserBlogs($appkey, $username, $password){
        $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$my 	= JFactory::getUser($username);
		$uri	= JURI::getInstance();

		$admin	= EasyBlogXMLRPCHelper::isSiteAdmin($my);
		$domain	= $uri->toString( array('scheme', 'host', 'port'));
		$xmlrpcLink	= $domain . '/index.php?option=com_easyblog&controller=xmlrpc';

		$structArray = array();
		$structArray[] = new xmlrpcval(array(
		    'isAdmin'	=> new xmlrpcval($admin, $xmlrpcBoolean),
			'url'		=> new xmlrpcval(JURI::root(), $xmlrpcString),
			'blogid'	=> new xmlrpcval($my->id, $xmlrpcString),
			'blogName'	=> new xmlrpcval($my->name . '\'s blog entries', $xmlrpcString),
			'xmlrpc'	=> new xmlrpcval($xmlrpcLink, $xmlrpcString)
			), 'struct');

		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}

	public static function getUserInfo($appkey, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcStruct;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );
		$my = JFactory::getUser($username);

	    $profile = EasyBlogHelper::getTable( 'Profile', 'Table' );
	    $profile->load($my->id);
	    $profile->setUser($my);

	    $userURL    = (empty( $profile->url ) || $profile->url == 'http://') ? '' : $profile->url;

		$struct = new xmlrpcval(
		array(
			'nickname'	=> new xmlrpcval($profile->nickname),
			'userid'	=> new xmlrpcval($my->id),
			'url'		=> new xmlrpcval($userURL),
			'email'		=> new xmlrpcval($my->email),
			'lastname'	=> new xmlrpcval($my->name),
			'firstname'	=> new xmlrpcval($my->name)
		), $xmlrpcStruct);

		return new xmlrpcresp($struct);
	}


	public static function newPost($blogid, $username, $password, $content, $publish)
	{
		return EasyBlogXMLRPCServices::editPost(0, $username, $password, $content, $publish);
	}

	public static function editPost($postid, $username, $password, $content, $publish)
	{
	    $mainframe  = JFactory::getApplication();
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		jimport( 'joomla.application.component.model' );

		$my		= JFactory::getUser($username);
		$acl	= EasyBlogACLHelper::getRuleSet($my->id);

		if( empty( $my->id ) )
		{
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('NO PERMISSION TO CREATE BLOG'));
		}

		if(empty($acl->rules->add_entry))
		{
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('NO PERMISSION TO CREATE BLOG'));
		}

		$isNew  = true;

		// create a new blog jtable object
		$isDraft    = false;
		$blog 		= '';

		if( empty($acl->rules->publish_entry) )
		{
			// Try to load this draft to see if it exists
			$blog		= EasyBlogHelper::getTable( 'Draft' );
			$isDraft    = true;
		}
		else
		{
			$blog 	= EasyBlogHelper::getTable( 'Blog', 'Table' );
		}

		if(isset($postid) && ! empty($postid))
		{
		    $isNew  = false; //we are doing editing
		    $blog->load($postid);
		}

		//prepare initial blog settings.
		$config 		= EasyBlogHelper::getConfig();
		$isPrivate    	= $config->get('main_blogprivacy', '0');
		$allowComment   = $config->get('main_comment', 1);
		$allowSubscribe	= $config->get('main_subscription', 1);
		$showFrontpage  = $config->get('main_newblogonfrontpage', 0);
		$sendEmails		= $config->get('main_sendemailnotifications', 1);

		//check if user have permission to enable privacy.
		$aclBlogPrivacy = $acl->rules->enable_privacy;
		$isPrivate 		= empty($aclBlogPrivacy)? '0' : $isPrivate;

		$showFrontpage 	= (empty($acl->rules->contribute_frontpage)) ? '0' : $showFrontpage;


		/**
		 * Map the data input into blog's recognised data format
		 */
		$post   = array();

		$post['permalink'] = $blog->permalink;
		if(isset($content["wp_slug"]))
		{
			$post['permalink'] = $content["wp_slug"];
		}

		//check if comment is allow on this blog
		if(isset($content["mt_allow_comments"]))
		{

			if(! is_numeric($content["mt_allow_comments"])) {
				switch($content["mt_allow_comments"]) {
					case "closed":
						$post['allowcomment'] = 0;
						break;
					case "open":
						$post['allowcomment'] = 1;
						break;
					default:
						$post['allowcomment'] = $allowComment;
						break;
				}
			}
			else
			{
				switch((int) $content["mt_allow_comments"]) {
					case 0:
					case 2:
						$post['allowcomment'] = 0;
						break;
					case 1:
						$post['allowcomment'] = 1;
						break;
					default:
						$post['allowcomment'] = $allowComment;
						break;
				}
			}
		}//end if allowcomment

		$post['title']		= $content['title'];
		$post['intro']      = '';
		$post['content']    = '';

		if (isset($content['mt_text_more']) && $content['mt_text_more']) {
			$post['intro']		= $content['description'];
			$post['content']	= $content['mt_text_more'];
		}
		else if(isset($content['more_text']) && $content['more_text']){
			$post['intro']		= $content['description'];
			$post['content']	= $content['more_text'];
		}
		else
		{
		    $post['content']	= $content['description'];
		}


		// if introtext still empty and excerpt is provide, then we use it.
		if(empty($post['intro']) && isset($content['mt_excerpt']))
		{
		    $post['intro']  = $content['mt_excerpt'];
		}

		//set category
		if(isset($content['categories']))
		{
		    $categoryTitle  = '';

			if (is_array($content['categories']))
			{
			    //always get the 1st option. currently not supported multi categories
				$categoryTitle	= @$content['categories'][0];
			}
			else
			{
			    $categoryTitle  = $content['categories'];
			}

			if(empty($categoryTitle))
			{
			    if( $isNew ) $post['category_id'] = 1; // by default the 1 is the uncategorised.
			}
			else
			{
			    $db     = EasyBlogHelper::db();

				$query = 'SELECT `id` FROM `#__easyblog_category`';
				$query .= ' WHERE `title` = ' . $db->Quote($categoryTitle);

				$db->setQuery($query);
				$result = $db->loadResult();

				if(! empty($result))
				{
				    $post['category_id'] = $result;
				}
				else
				{
				    $post['category_id'] = 1;
				}
			}
		}
		else
		{
		    if( $isNew ) $post['category_id'] = 1;
		}

		$post['published']  = $publish;
		$post['private']	= $isPrivate;
		if( isset( $content["post_status"] ) )
		{
			switch( $content["post_status"] )
			{
				case 'publish':
					$post['published']  = 1;
					break;
				case 'private':
					$post['published']  = 1;
					$post['private']    = 1;
					break;
				case 'draft':
					$post['published']  = 0;
					break;
				case 'schedule':
					$post['published']  = 2;
					break;
				case 'pending':
				default:
					$post['published']  = 0;
					break;
			}
		}

		// echo '<pre>';
		// var_dump($post['published']);
		// var_dump($post['content']);
		// echo '</pre>';
		// exit;

		// Do some timestamp voodoo
		$tzoffset       = EasyBlogDateHelper::getOffSet();
		$overwriteDate  = false;
		if ( !empty( $content['date_created_gmt'] ) )
		{
		    $date   = EasyBlogHelper::getDate($content['date_created_gmt']);
			$blog->created = $date->toFormat();
		}
		else if ( !empty( $content['dateCreated']) )
		{
		    $date	= EasyBlogHelper::getDate($content['dateCreated']);
		    //$date   = EasyBlogDateHelper::dateWithOffSet( $content['dateCreated'] );
		    $today	= EasyBlogHelper::getDate();

		    // somehow blogsy time always return the current time 5 sec faster.
			if ( $date->toUnix() > ( $today->toUnix() + 5 ) )
			{
				$post['published'] = 2;

				$overwriteDate['created'] 	 = $today->toFormat();
				$overwriteDate['publish_up'] = $date->toFormat();
			}
			else
			{
				$blog->created 				 = $date->toFormat();
				$overwriteDate['created'] 	 = $date->toFormat();
			}
			// echo $date->toUnix();
			// echo '##';
			// echo $date->toFormat();
			// echo '##';
			// echo $today->toFormat();
			// echo '##';
			// echo $today->toUnix();
			// echo '##';
			// echo $today->toUnix() + 5;
			// exit;
		}
		else
		{
		    if(!$isNew)
		    {
				$date	= EasyBlogDateHelper::dateWithOffSet($blog->created);
		        $blog->created  = $date->toFormat();

		        $date	= EasyBlogDateHelper::dateWithOffSet($blog->publish_up);
		        $blog->publish_up  = $date->toFormat();
			}
		}

		// we bind this attribute incase if easyblog was a old version.
		$post['issitewide'] = '1';

		//bind the inputs
		$blog->bind($post, true);

		$blog->intro		= $post['intro'];
		$blog->content		= $post['content'];
		$blog->created_by   = $my->id;
		$blog->ispending 	= 0;//(empty($acl->rules->publish_entry)) ? 1 : 0;
		$blog->published    = $post['published'];

		if( $overwriteDate !== false )
		{
			$blog->created 		= $overwriteDate['created'];
			if( isset( $overwriteDate['publish_up'] ) )
			{
				$blog->publish_up 	= $overwriteDate['publish_up'];
			}
		}

		$blog->subscription = $allowSubscribe;
		$blog->frontpage    = $showFrontpage;
		$blog->send_notification_emails    = $sendEmails;
		$blog->permalink    = (empty($post['permalink'])) ? EasyBlogHelper::getPermalink( $blog->title ) : $post['permalink'];

		// add in fancy box style.
		$postcontent    = $blog->intro . $blog->content;

		// cater for wlw
		// $pattern		= '#<a.*?\><img[^>]*><\/a>#i';
		$pattern = '#<a[^>]*\><img[^>]*><\/a>#i';
		preg_match_all( $pattern , $postcontent , $matches );

		if( $matches && count( $matches[0] ) > 0 )
		{
			foreach( $matches[0] as $match )
			{
				$input  		= $match;
				$largeImgPath   = '';

				//getting large image path
				$pattern	= '#<a[^>]*>#i';
				preg_match( $pattern , $input , $anchors );

				if( $anchors )
				{
					preg_match( '/href\s*=\s*[\""\']?([^\""\'\s>]*)/i' , $anchors[0] , $adata );

					if( $adata )
					{
						$largeImgPath   = $adata[1];
					}
				}

				$input  	= $match;
				$pattern	= '#<img[^>]*>#i';
				preg_match( $pattern , $input , $images );

				if( $images )
				{
					preg_match( '/src\s*=\s*[\""\']?([^\""\'\s>]*)/i' , $images[0] , $data );

					if( $data )
					{
						$largeImgPath   = (empty( $largeImgPath ) ) ? $data[1] : $largeImgPath;
						$largeImgPath   = urldecode( $largeImgPath );
						$largeImgPath   = str_replace( ' ', '-', $largeImgPath );

						$encodedurl   = urldecode( $data[1] );
						$encodedurl     = str_replace( ' ', '-', $encodedurl );
						$images[0]      = str_replace( $data[1] , $encodedurl , $images[0]);


						$blog->intro 	= str_replace( $input , '<a class="easyblog-thumb-preview" href="' . $largeImgPath . '">' . $images[0] . '</a>' , $blog->intro );
						$blog->content 	= str_replace( $input , '<a class="easyblog-thumb-preview" href="' . $largeImgPath . '">' . $images[0] . '</a>' , $blog->content );
					}
				}
			}
		}
		else
		{
			$pattern	= '#<img[^>]*>#i';
			preg_match_all( $pattern , $postcontent , $matches );

			if( $matches && count( $matches[0] ) > 0 )
			{
				foreach( $matches[0] as $match )
				{
					$input  = $match;
					preg_match( '/src\s*=\s*[\""\']?([^\""\'\s>]*)/i' , $input , $data );

					if( $data )
					{

						$oriImage  = $data[1];
						$data[1]   = urldecode( $data[1] );
						$data[1]   = str_replace( ' ', '-', $data[1] );

						$encodedurl   	= urldecode( $oriImage );
						$encodedurl     = str_replace( ' ', '-', $encodedurl );
						$imageurl     	= str_replace( $oriImage , $encodedurl , $input);

						$blog->intro 	= str_replace( $input , '<a class="easyblog-thumb-preview" href="' . $data[1] . '">' . $imageurl . '</a>' , $blog->intro );
						$blog->content 	= str_replace( $input , '<a class="easyblog-thumb-preview" href="' . $data[1] . '">' . $imageurl . '</a>' , $blog->content );
					}

				}
			}
		}

		if( $isDraft )
		{
			$blog->pending_approval    = true;

			// we need to process trackbacks and tags here.

			//adding trackback.
			if(!empty($acl->rules->add_trackback))
			{
				$trackback  =  (isset($content['mt_tb_ping_urls'])) ? $content['mt_tb_ping_urls'] : '';

				if( !empty($trackback) && count( $trackback ) > 0 )
				{
					$trackback  = implode( "\n", $trackback);

					$blog->trackbacks   = $trackback;
				}
			}

			// add new tag
			$tags			= (isset($content['mt_keywords'])) ? $content['mt_keywords'] : '';
			$blog->tags     = $tags;
		}

		if (!$blog->store())
		{
			$msg = $blog->getError();
			$msg    = ( empty( $msg ) ) ? 'Post store failed' : $msg;

			return new xmlrpcresp(0, $xmlrpcerruser+1, $msg );
		}

		if( $isDraft && !empty( $blog->id ) )
		{
			// if this post is under moderation, we will stop here.
			return new xmlrpcresp(new xmlrpcval($blog->id, $xmlrpcString));
		}

		/**
		 * JomSocial userpoint.
		 */
		if($isNew && $blog->published == '1' && $my->id != 0 )
		{
			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'blog.create' , $my->id );

			if( $config->get('main_jomsocial_userpoint') )
			{
				$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
				if( JFile::exists( $jsUserPoint ) )
				{
					require_once( $jsUserPoint );
					CUserPoints::assignPoint( 'com_easyblog.blog.add' , $my->id );
				}
			}

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.new.blog' , $my->id , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_BLOG' , $blog->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.new.blog' , $my->id );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.new.blog' , $my->id );

			// Assign badge for users that report blog post.
			// Only give points if the viewer is viewing another person's blog post.
			EasyBlogHelper::getHelper( 'EasySocial' )->assignBadge( 'blog.create' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_CREATE_BLOG_POST' ) );

			// @rule: Mighty Touch karma points
			EasyBlogHelper::getHelper( 'MightyTouch' )->setKarma( $my->id , 'new_blog' );
		}

		//add jomsocial activities
		if(($blog->published == '1') && ($config->get('main_jomsocial_activity')) )
		{
            EasyBlogXMLRPCHelper::addJomsocialActivities($blog, $isNew);
		}

		// AlphaUserPoints
		// since 1.2
		if ( EasyBlogHelper::isAUPEnabled() )
		{
			// get blog post URL
			$url = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$blog->id);
			AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_add_blog', '', 'easyblog_add_blog_' . $blog->id, JText::sprintf('AUP NEW BLOG CREATED', $url, $blog->title) );
		}

		//adding trackback.
		if(!empty($acl->rules->add_trackback))
		{
			$trackback  =  (isset($content['mt_tb_ping_urls'])) ? $content['mt_tb_ping_urls'] : '';
			EasyBlogXMLRPCHelper::addTrackback($trackback, $blog, $my);
		}


		// add new tag
		$date			= EasyBlogHelper::getDate();

		$tags			= (isset($content['mt_keywords'])) ? $content['mt_keywords'] : '';
		$postTagModel 	= EasyBlogHelper::getModel( 'PostTag' );

		if($blog->id != '0')
		{
			//Delete existing associated tags.
			$postTagModel->deletePostTag( $blog->id );
		}

		if( !empty( $tags ) )
		{

			$arrTags    = explode(',', $tags);
			$tagModel 	= EasyBlogHelper::getModel( 'Tags' );

			foreach( $arrTags as $tag )
			{
				if(!empty($tag))
				{
					$table	= EasyBlogHelper::getTable( 'Tag' , 'Table' );

					//@task: Only add tags if it doesn't exist.
					if( !$table->exists( $tag ) )
					{
						if($acl->rules->create_tag)
						{
							$tagInfo['created_by']	= $my->id;
							$tagInfo['title'] 		= JString::trim($tag);
							$tagInfo['created']		= $date->toMySQL();

							$table->bind($tagInfo);

							$table->published	= 1;
							$table->status		= '';

							$table->store();
						}
					}
					else
					{
						$table->load( $tag , true );
					}

					//@task: Store in the post tag
					$postTagModel->add( $table->id , $blog->id , $date->toMySQL() );
				}
			}
		}




		if( $blog->published )
		{
			$allowed	= array( EBLOG_OAUTH_LINKEDIN , EBLOG_OAUTH_FACEBOOK , EBLOG_OAUTH_TWITTER );
			$blog->autopost( $allowed , $allowed );
		}

		return new xmlrpcresp(new xmlrpcval($blog->id, $xmlrpcString));
	}

	public static function getPost($postid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$my     = JFactory::getUser($username);
		$acl	= EasyBlogACLHelper::getRuleSet($my->id);

		if(empty($acl->rules->add_entry))
		{
			return new xmlrpcresp(0, $xmlrpcerruser+2, JText::_('ACCESS DENIED'));
		}

		jimport( 'joomla.application.component.model' );
		// JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );
		// JModel::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' );

		// create a new blog jtable object
		$blog = EasyBlogHelper::getTable( 'Blog', 'Table' );
		$blog->load( $postid );

		if( empty($blog->title) )
		{
		    return new xmlrpcresp(0, $xmlrpcerruser + 1, JText::_('Blog post not found.'));
		}

		$catId  = (empty($blog->category_id)) ? '1' : $blog->category_id;
		$category = EasyBlogHelper::getTable( 'Category', 'Table' );
		$category->load($catId);

		//publishing
		$publish    = 'publish';
		switch( $blog->published )
		{
			case 1:
				$publish = 'publish';
				break;
			case 3:
				$publish = 'draft';
				break;
			case 2:
			default:
				$publish = 'pending';
		}

		//tags /* to do */
		$modelPT 		= EasyBlogHelper::getModel( 'PostTag' );
		$blogTags		= $modelPT->getBlogTags($blog->id);
  		$arrBlogTags	= array();

        $tagnames       = '';

		if(! empty($blogTags))
		{
			foreach($blogTags as $bt)
			{
				$arrBlogTags[] = $bt->title;
			}

			$tagnames	= implode(',', $arrBlogTags);
	    }

		$tagnames   	= array(new xmlrpcval($tagnames, $xmlrpcString));


		$tzoffset       = EasyBlogDateHelper::getOffSet();
		$dateCreated	= EasyBlogHelper::getDate($blog->created, $tzoffset);
		$postURL		= 'index.php?option=com_easyblog&view=entry&id=' . $blog->id;
		$articleLink 	= EasyBlogRouter::getRoutedURL($postURL, false, true);

		$struct = new xmlrpcval(
		array(
			'link'				=> new xmlrpcval($articleLink),
			'permaLink'			=> new xmlrpcval($articleLink),
			'userid'			=> new xmlrpcval($my->id),
			'title'				=> new xmlrpcval($blog->title),
			'description'		=> new xmlrpcval($blog->intro),
			'more_text'			=> new xmlrpcval($blog->content),
			'mt_text_more'		=> new xmlrpcval($blog->content),
			'dateCreated'		=> new xmlrpcval($dateCreated->toISO8601(), 'dateTime.iso8601'),
			'categories' 		=> new xmlrpcval(array(new xmlrpcval($category->title)), $xmlrpcArray),
			'mt_excerpt' 		=> new xmlrpcval($blog->intro),
			'mt_text_more' 		=> new xmlrpcval($blog->content),
			'mt_allow_comments' => new xmlrpcval($blog->allowcomment),
			'mt_allow_pings' 	=> new xmlrpcval('0'),
			'mt_keywords' 		=> new xmlrpcval($tagnames, $xmlrpcArray),
			'post_status' 		=> new xmlrpcval($publish),
			'postid'			=> new xmlrpcval($blog->id),
			'date_created_gmt'  => new xmlrpcval($dateCreated->toISO8601()),
			'wp_slug' 			=> new xmlrpcval($blog->permalink)
		), $xmlrpcStruct);

		return new xmlrpcresp($struct);
	}

	public static function deletePost($appkey, $postid, $username, $password, $publish)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$my 		= JFactory::getUser($username);
		$config     = EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet($my->id);

		if(empty($acl->rules->delete_entry))
		{
			return new xmlrpcresp(0, $xmlrpcerruser+2, JText::_('YOU DO NOT HAVE DELETE RIGHT'));
		}

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );
		// create a new blog jtable object
		$blog = EasyBlogHelper::getTable( 'Blog', 'Table' );
		//$blog->load( $postid );

		if(! $blog->load( $postid ))
			return new xmlrpcresp(0, $xmlrpcerruser+1, 'Sorry, blog entry not found.' );


		if( !$blog->delete() )
		{
			return new xmlrpcresp(0, $xmlrpcerruser+1, 'Post delete failed' );
		}

		return new xmlrpcresp(new xmlrpcval(true, $xmlrpcBoolean));
	}

	public static function getRecentPosts($blogid, $username, $password, $numposts)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$my = JFactory::getUser($username);
		$db = EasyBlogHelper::db();

		$query = 'SELECT a.*';
		$query .= '	FROM `#__easyblog_post` AS a';
		$query .= '	WHERE a.`created_by` = '. $db->Quote($my->id);
		//$query .= '	AND (a.`published` = 1 OR a.`published`= 0 OR a.`published`=2 OR a.`published`=3)';
		$query .= '	ORDER BY a.`created` DESC';

		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (!$items) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, 'No posts available, or an error has occured.' );
		}

		$structArray = array();
		foreach ($items as $item)
		{
			//$dateCreated	= EasyBlogHelper::getDate($item->created);
			$dateCreated	= iso8601_encode(strtotime($item->created), 0);
			$postURL		= EasyBlogRouter::getEntryRoute($item->id);

			//$articleLink 	= JRoute::_($postURL, true);
			$articleLink 	= $postURL;

			$structArray[] = new xmlrpcval(array(
				//'dateCreated'	=> new xmlrpcval($dateCreated->toISO8601(), 'dateTime.iso8601'),
				'dateCreated'	=> new xmlrpcval($dateCreated, 'dateTime.iso8601'),
				'title'			=> new xmlrpcval($item->title),
				'description'	=> new xmlrpcval($item->intro . $item->content),
				'userid'		=> new xmlrpcval($item->created_by),
				'postid'		=> new xmlrpcval($item->id),
				'link'			=> new xmlrpcval($articleLink),
				'permaLink'		=> new xmlrpcval($articleLink)
			), $xmlrpcStruct);
		}

		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}

	public static function getCategories($blogid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$db		= EasyBlogHelper::db();
		$my   	= JFactory::getUser($username);

		$query	= 'SELECT * FROM `#__easyblog_category`';
		$query	.= ' where `published` = ' .  $db->Quote( '1' );
		$query  .= ' order by `title`';

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		if (!$categories) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, 'No categories available, or an error has occured.' );
		}

		$structArray = array();

		foreach ($categories as $category)
		{
			$structArray[] = new xmlrpcval(array(
				'title'					=> new xmlrpcval($category->title),
				'description'			=> new xmlrpcval($category->title),
				'categoryId'			=> new xmlrpcval($category->id),
				'parentId'				=> new xmlrpcval('0'),
				'categoryDescription'	=> new xmlrpcval($category->title),
				'categoryName'			=> new xmlrpcval($category->title),
				'htmlUrl'				=> new xmlrpcval(''),
				'rssUrl'				=> new xmlrpcval('')
			), 'struct');
		}


		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}

	public static function newMediaObject($blogid, $username, $password, $file)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		EasyBlogXMLRPCHelper::loginUser($username, $password);

		$user	= JUser::getInstance($username);
		$acl	= EasyBlogACLHelper::getRuleSet($user->id);

		if(empty($acl->rules->upload_image))
		{
			return new xmlrpcresp(0, $xmlrpcerruser+2, JText::_('YOU DO NOT HAVE IMAGE UPLOAD RIGHT'));
		}

		$config 			= EasyBlogHelper::getConfig();
		$main_image_path	= $config->get('main_image_path');
        $main_image_path 	= rtrim($main_image_path, '/');

		$rel_upload_path    = $main_image_path . '/' . $user->id;
		$userUploadPath    	= JPATH_ROOT . DIRECTORY_SEPARATOR . str_ireplace('/', DIRECTORY_SEPARATOR, $main_image_path . DIRECTORY_SEPARATOR . $user->id);
		$folder     		= JPath::clean($userUploadPath);

		$dir  	 = $userUploadPath . DIRECTORY_SEPARATOR;
		$tmp_dir = JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

  		if(! JFolder::exists($dir))
  		{
  		    JFolder::create($dir);
  		}

		if(strpos($file['name'], '/') !== FALSE)
			$file['name']= substr($file['name'], strrpos($file['name'],'/')+1 );
		elseif(strpos($file['name'], '\\' !== FALSE))
			$file['name']= substr($file['name'], strrpos($file['name'],'\\')+1 );

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');

		$file['name']	= JFile::makesafe($file['name']);
		$timestamp = date('His');
		$file['name']	= substr($file['name'], 0, -4) . $timestamp . '.' . JFile::getExt($file['name']);
		// $file['name']	= substr($file['name'], 0, -4) . '.' . JFile::getExt($file['name']);

		// write to temp folder
		$file['tmp_name']	= $tmp_dir . $file['name'];
		@JFile::write( $file['tmp_name'], $file['bits']);


		$file['size'] = 0;
        $error  = '';
        $allowed	= EasyImageHelper::canUploadFile( $file );
		if ( $allowed !== true ) {
			@JFile::delete( $file['tmp_name'] );
			return new xmlrpcresp(0, $xmlrpcerruser+1, 'The file is not valid' );
		}

		// @JFile::write( $dir . $file['name'], $file['bits']);

		// @task: Ensure that images goes through the same resizing format when uploading via media manager.
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );
		$media 				= new EasyBlogMediaManager();
		$result 			= $media->upload( $dir , $userUploadPath , $file , '/', 'user' );


		@JFile::delete( $file['tmp_name'] );

		$file['name']	= EasyBlogXMLRPCHelper::cleanImageName( $file['name'] );

		$fileUrl    = rtrim(JURI::root(), '/') . '/' . $rel_upload_path . '/' . $file['name'];
		return new xmlrpcresp(new xmlrpcval(array(
					'url'			=> new xmlrpcval($fileUrl)
				), 'struct'));
	}

}

class EasyBlogXMLRPCHelper
{
	public static function addJomsocialActivities($blog, $isNew)
	{
		EasyBlogHelper::addJomSocialActivityBlog($blog, $isNew);
	}

	public static function addTrackback($trackback, $blogObj, $my)
	{
		// JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );
		// JModel::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' );
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'trackback.php' );

		$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$author->setUser( $my );

		if ( !empty( $trackback ) && is_string( $trackback ) ) {

			$trackbacks	= explode( ' ' , $trackback);

			for ( $x = 0; $x < count($trackbacks); $x++ )
			{
				$tbl = EasyBlogHelper::getTable( 'TrackbackSent' , 'Table' );

				// check if the URL has been added to our record
				$exists	= $tbl->load( $trackbacks[$x] , true , $blogObj->id );

				// if not exists, we need to store them
				if( !$exists )
				{
					$tbl = EasyBlogHelper::getTable( 'TrackbackSent' , 'Table' );

					$tbl->post_id	= $blogObj->id;
					$tbl->url		= $trackbacks[$x];
					$tbl->sent		= 0;
					$tbl->store();
				}
			}
		}


		// only process this part when publish blog
		if ( $blogObj->published == '1' ) {

			// now load trackback model
			jimport( 'joomla.application.component.model' );
			$trackbackModel 	= EasyBlogHelper::getModel( 'TrackbackSent' );

			// get lists of trackback URLs based on blog ID
			$tbacks = $trackbackModel->getSentTrackbacks( $blogObj->id, true );

			if(count($tbacks) > 0)
			{
				// loop each URL, ping if necessary
				foreach( $tbacks as $tback )
				{
					$tb		= new EasyBlogTrackBack( $author->getName() , $author->getName() , 'UTF-8');
					$text	= empty( $blogObj->intro ) ? $blogObj->content : $blogObj->intro;
					if( @$tb->ping( $tback->url , EasyBlogRouter::getEntryRoute($blogObj->id) , $blogObj->title , $text ) )
					{
						$tbl = EasyBlogHelper::getTable( 'TrackbackSent' , 'Table' );
						$tbl->load($tback->id);

						$new_trackbacks = array();
						$new_trackbacks['url']		= $tback->url;
						$new_trackbacks['post_id']	= $tback->post_id;
						$new_trackbacks['sent']		= 1;

						$tbl->bind($new_trackbacks);
						$tbl->store();
					}
				}//enf foreach
			}//end if
		}

	}

	public static function cleanImageName( $argFileName )
	{

		// Ensure that the file name is safe.
		$argFileName		= JFile::makeSafe( $argFileName );

		// Ensure that the file name does not contain UTF-8 data.
		$argFileName		= trim( $argFileName );
		$fileName			= $argFileName;

		//
		if(strpos( $fileName , '.' ) === false )
		{
			$fileName	= EasyBlogHelper::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . '.' . $fileName;
		}
		else if( strpos( $fileName , '.' ) == 0 )
		{
			$fileName	= EasyBlogHelper::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . $fileName;
		}

		// We do not want to allow spaces in the name.
		$fileName 		= str_ireplace(' ', '-', $fileName);

		return $fileName;
	}

	public static function loginUser($username, $password)
	{
		global $xmlrpcerruser;

		if(!EasyBlogXMLRPCHelper::authenticateUser($username, $password))
				return new xmlrpcresp(0, 403, "Login Failed");
	}

	public static function authenticateUser($username, $password)
	{
		// Get the global JAuthentication object
		jimport( 'joomla.user.authentication');
		$auth = JAuthentication::getInstance();
		$credentials = array( 'username' => $username, 'password' => $password );
		$options = array();

		$app 		= JFactory::getApplication();
		$response	= $app->login( $credentials );

		if( $response === true )
		{
			$my   	= JFactory::getUser($username);
			if( $my->id == 0 )
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	public static function isSiteAdmin($user)
	{
	    return EasyBlogHelper::isSiteAdmin( $user->id );
	}
}
