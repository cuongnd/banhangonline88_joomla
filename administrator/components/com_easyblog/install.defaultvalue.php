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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

if(!defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}

function getEblogId()
{
	$db		= DBHelper::db();

	if( getJoomlaVersion() >= '1.6' ) {
		$query 	= 'SELECT ' . $db->nameQuote( 'extension_id' ) . ' '
			. 'FROM ' . $db->nameQuote( '#__extensions' ) . ' '
			. 'WHERE `element`=' . $db->Quote( 'com_easyblog' ) . ' '
			. 'AND `type`=' . $db->Quote( 'component' ) . ' ';
	} else {
		$query 	= 'SELECT ' . $db->nameQuote( 'id' ) . ' '
			. 'FROM ' . $db->nameQuote( '#__components' ) . ' '
			. 'WHERE `option`=' . $db->Quote( 'com_easyblog' ) . ' '
			. 'AND `parent`=' . $db->Quote( '0');
	}

	$db->setQuery( $query );

	return $db->loadResult();
}

function menuExist()
{
	$db		= DBHelper::db();

	if( getJoomlaVersion() >= '1.6' ) {
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' .  $db->Quote( 'index.php?option=com_easyblog%') . ' '
			. 'AND `client_id`=' . $db->Quote( '0' ) . ' '
			. 'AND `type`=' . $db->Quote( 'component' ) . ' '
			. 'AND `menutype` !=' . $db->Quote( 'main' );
	} else {
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' .  $db->Quote( 'index.php?option=com_easyblog%');
	}

	$db->setQuery( $query );

	$requiresUpdate	= ( $db->loadResult() >= 1 ) ? true : false;

	return $requiresUpdate;
}

/**
 * Method to update menu's component id.
 *
 * @return boolean true on success false on failure.
 */
function updateMenuItems()
{
	// Get new component id.
	$db 	= DBHelper::db();

	$cid = getEblogId();

	if( !$cid )
		return false;

	$joomlaVersion = getJoomlaVersion();

	if( $joomlaVersion >= '1.6' ) {
		$query 	= 'UPDATE ' . $db->nameQuote( '#__menu' ) . ' '
			. 'SET component_id=' . $db->Quote( $cid ) . ' '
			. 'WHERE `link` LIKE ' . $db->Quote('index.php?option=com_easyblog%') . ' '
			. 'AND `type`=' . $db->Quote( 'component' ) . ' '
			. 'AND `menutype` != ' . $db->Quote( 'main' ) . ' '
			. 'AND `client_id`=' . $db->Quote( '0' );
	} else {
		// Update the existing menu items.
		$query 	= 'UPDATE ' . $db->nameQuote( '#__menu' ) . ' '
			. 'SET `componentid`=' . $db->Quote( $cid ) . ' '
			. 'WHERE `link` LIKE ' . $db->Quote('index.php?option=com_easyblog%');
	}

	$db->setQuery( $query );
	$db->query();

	if( $joomlaVersion < '1.6' ) {
		//now this is to update the old viewname 'easyblog' to new viewname 'latest'
		$query 	= 'UPDATE ' . $db->nameQuote( '#__menu' )
				. ' SET `link` = ' . $db->Quote( 'index.php?option=com_easyblog&view=latest' )
				. ' WHERE `link` = ' . $db->Quote('index.php?option=com_easyblog&view=easyblog')
				. ' AND `componentid` = ' . $db->Quote( $cid );

		$db->setQuery( $query );
		$db->query();
	}

	return true;
}

/**
 * Method to add menu's item.
 *
 * @return boolean true on success false on failure.
 */
function createMenuItems()
{
	// Get new component id.
	$db 	= DBHelper::db();

	$cid = getEblogId();

	if( !$cid )
	{
		return false;
	}

	//get the default 'HOME' menu item the menytype.
	$query	= 'SELECT `menutype` FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'home' ) . '=' . $db->Quote( '1' );
	$db->setQuery( $query );

	$menuType   = $db->loadResult();

	if( empty( $menuType ) )
	{
		return false;
	}

	$status = true;
	if( getJoomlaVersion() >= '1.6' ) {
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'helpers' . DS . 'helper.php' );

		$table 	= JTable::getInstance( 'Menu' , 'JTable' );

		$table->menutype		= $menuType;
		$table->title 			= 'EasyBlog';
		$table->alias 			= 'easyblog';
		$table->path 			= 'easyblog';
		$table->link 			= 'index.php?option=com_easyblog&view=latest';
		$table->type 			= 'component';
		$table->published 		= '1';
		$table->parent_id 		= '1';
		$table->component_id	= $cid;
		$table->client_id 		= '0';
		$table->language 		= '*';

		$table->setLocation('1', 'last-child');

		if(!$table->store()){
			$status = false;
		}
	} else {

		$query 	= 'SELECT ' . $db->nameQuote( 'ordering' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__menu' ) . ' '
				. 'ORDER BY ' . $db->nameQuote( 'ordering' ) . ' DESC LIMIT 1';
		$db->setQuery( $query );
		$order 	= $db->loadResult() + 1;

		// Update the existing menu items.
		$query 	= 'INSERT INTO ' . $db->nameQuote( '#__menu' )
			. '('
				. $db->nameQuote( 'menutype' ) . ', '
				. $db->nameQuote( 'name' ) . ', '
				. $db->nameQuote( 'alias' ) . ', '
				. $db->nameQuote( 'link' ) . ', '
				. $db->nameQuote( 'type' ) . ', '
				. $db->nameQuote( 'published' ) . ', '
				. $db->nameQuote( 'parent' ) . ', '
				. $db->nameQuote( 'componentid' ) . ', '
				. $db->nameQuote( 'sublevel' ) . ', '
				. $db->nameQuote( 'ordering' ) . ' '
			. ') '
			. 'VALUES('
				. $db->quote( $menuType ) . ', '
				. $db->quote( 'EasyBlog' ) . ', '
				. $db->quote( 'easyblog' ) . ', '
				. $db->quote( 'index.php?option=com_easyblog&view=latest' ) . ', '
				. $db->quote( 'component' ) . ', '
				. $db->quote( '1' ) . ', '
				. $db->quote( '0' ) . ', '
				. $db->quote( $cid ) . ', '
				. $db->quote( '0' ) . ', '
				. $db->quote( $order ) . ' '
			. ') ';

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			$status = false;
		}
	}

	return $status;
}

function blogCategoryExist()
{
	$db		= DBHelper::db();

	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__easyblog_category' );
	$db->setQuery( $query );

	$exist	= ( $db->loadResult() > 0 ) ? true : false;

	return $exist;
}

function _getSuperAdminId()
{
	$db = DBHelper::db();

	if( getJoomlaVersion() >= '1.6' ) {
		$saUsers	= getSAUsersIds();

		$result = '42';
		if(count($saUsers) > 0)
		{
			$result = $saUsers['0'];
		}
	} else {
		$query  = 'SELECT `id` FROM `#__users`';
		$query  .= ' WHERE (LOWER( usertype ) = ' . $db->Quote('super administrator');
		$query  .= ' OR `gid` = ' . $db->Quote('25') . ')';
		$query  .= ' ORDER BY `id` ASC';
		$query  .= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		$result = (empty($result)) ? '62' : $result;
	}

	return $result;

}

function createBlogCategory()
{
	$db 	= DBHelper::db();

	$suAdmin    = _getSuperAdminId();
	$query 	= "INSERT INTO `#__easyblog_category` (`id`, `created_by`, `title`, `alias`, `created`, `status`, `published`, `ordering`, `lft`, `rgt`, `default`) VALUES ('1', " . $db->Quote($suAdmin) .", 'Uncategorized', 'uncategorized', now(), 0, 1, 0, 1, 2, 1)";

	$db->setQuery( $query );

	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}
	return true;
}

function updateACLRules()
{
	$db		= DBHelper::db();

	$query	= "INSERT INTO `#__easyblog_acl` (`id`, `action`, `description`, `published`, `ordering`) VALUES
				(1, 'add_entry', 'If allowed, user is allowed to post a new blog post.', 1, 0),
				(2, 'publish_entry', 'If allowed, user is allowed to publish their blog post on the site. ', 1, 0),
				(3, 'allow_feedburner', 'If allowed, user can set their own Feedburner URL which will be linked to their own blog.', 1, 0),
				(4, 'upload_avatar', 'If allowed, user can upload their avatar.', 1, 0),
				(5, 'manage_comment', 'If allowed, user can manage any comments that are posted throughout the site.', 1, 0),
				(6, 'update_twitter', 'If allowed, user can post updates to their own Twitter account.', 1, 0),
				(7, 'update_tweetmeme', 'If allowed, user can update Tweetmeme settings. **Deprecated**', 0, 0),
				(8, 'delete_entry', 'If allowed, user can delete their own blog post.', 1, 0),
				(9, 'add_trackback', 'If allowed, user can specify trackback urls when creating a new blog post.', 1, 0),
				(10, 'contribute_frontpage', 'If allowed, user can publish their blog post on the front page of EasyBlog.', 1, 0),
				(11, 'create_category', 'If allowed, user can create a new category on the site.', 1, 0),
				(12, 'create_tag', 'If allowed, user can create new tags on the site.', 1, 0),
				(13, 'add_adsense', 'If allowed, user can set their own Google Adsense codes.', 1, 0),
				(14, 'allow_shortcode', 'If allowed, user can use short code URLs. **Deprecated**', 0, 0),
				(15, 'allow_rss', 'If allowed, user can use use custom RSS. **Deprecated**', 0, 0),
				(16, 'custom_template', 'If allowed, user can use a custom template. **Deprecated**', 0, 0),
				(17, 'enable_privacy', 'If allowed, user can specify a custom privacy option for their blog posts.', 1, 0),
				(18, 'allow_comment', 'If allowed, user can post a comment on the site.', 1, 0),
				(19, 'allow_subscription', 'If allowed, user can subscribe to the blog on the site.', 1, 0),
				(20, 'manage_pending', 'If allowed, user can manage pending blog posts.', 1, 0),
				(21, 'upload_image', 'If allowed, user can upload new images via the media manager.', 1, 0),
				(23, 'upload_cavatar', 'If allowed, user can upload a new avatar for the category.', 1, 0),
				(24, 'update_linkedin', 'If allowed, user can post updates to their own LinkedIn account.', 1, 0),
				(25, 'change_setting_comment', 'If allowed, user can toggle the ability to comment on their blog post.', 1, 0),
				(26, 'change_setting_subscription', 'If allowed, user can toggle the ability to allow subscriptions for their blog post.', 1, 0),
				(27, 'update_facebook', 'If allowed, user can post updates to their own Facebook account.', 1, 0),
				(28, 'delete_category', 'If allowed, user can delete their category.', 1, 0),
				(29, 'moderate_entry', 'If allowed, user can moderate all blog entries from the site.', 1, 0),
				(30, 'edit_comment', 'If allowed, user can edit comments from their dashboard area.', 1, 0),
				(31, 'delete_comment', 'If allowed, user can delete comments on their blogs.', 1, 0),
				(32, 'feature_entry', 'If allowed, user can feature blog posts on the site.', 1, 0),
				(33, 'media_places_album', 'If allowed, user can utilize the Albums in media manager.', 1, 0),
				(34, 'media_places_flickr', 'If allowed, user can utilize Flickr in media manager.', 1, 0),
				(35, 'media_places_shared', 'If allowed, user can access the shared folder in media manager.', 1, 0),
				(36, 'allow_seo', 'If allowed, user can set custom SEO options e.g. meta description and meta keywords in their pages.', 1, 0),
				(37, 'access_toolbar', 'If allowed, user can view the toolbar on the site.', 1, 0)";

	$db->setQuery( $query );
	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}

	return true;
}


function updateGroupACLRules()
{
	$db 	= DBHelper::db();

	$userGroup  = array();

	if( getJoomlaVersion() >= '1.6' )
	{
		//get all user group for 1.6
		$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
		$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
		$query .= ' FROM #__usergroups AS a';
		$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query .= ' GROUP BY a.id';
		$query .= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$userGroups = $db->loadAssocList();

		$defaultAcl = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16 ,17, 18, 19, 21, 23, 24, 25, 26, 27, 28, 33, 34, 35, 36 , 37 );

		if(!empty($userGroups))
		{
			foreach($userGroups as $value)
			{
				switch($value['id'])
				{
					case '1':
						//default guest group in joomla 1.6
						$userGroup[$value['id']] = array(19, 37);
						break;
					case '7':
						//default administrator group in joomla 1.6
					case '8':
						//default super user group in joomla 1.6
						$userGroup[$value['id']]  = 'all';
						break;
					default:
						//every other group
						$userGroup[$value['id']]  = $defaultAcl;
				}
			}
		}
	}
	else
	{
		$defaultAcl = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16 ,17, 18, 19, 21, 23, 24, 25, 26, 27, 28, 33, 34, 35, 36 , 37 );

		//18 registered
		$userGroup[18]  = $defaultAcl;

		//19 author
		$userGroup[19]  = $defaultAcl;

		//20 editor
		$userGroup[20]  = $defaultAcl;

		//21 publisher
		$userGroup[21]  = $defaultAcl;

		//23 manager
		$userGroup[23]  = $defaultAcl;

		//24 administrator
		$userGroup[24]  = 'all';

		//25 super administrator
		$userGroup[25]  = 'all';
	}

	//getting all acl rules.
	$query  = 'SELECT `id` FROM `#__easyblog_acl` ORDER BY `id` ASC';
	$db->setQuery($query);
	$aclTemp  	= $db->loadResultArray();

	$aclRules   			= array();
	$aclRulesAllEnabled   	= array();
	//do not use array_fill_keys for lower php compatibility. use old-fashion way. sigh.
	foreach($aclTemp as $item)
	{
		$aclRules[$item] 			= 0;
		$aclRulesAllEnabled[$item]	= 1;
	}

	$mainQuery  = array();
	foreach($userGroup as $uKey => $uGroup)
	{
		// Insert default filter for all groups.
		$defaultFilterTags			= 'script,applet,iframe';
		$defaultFilterAttributes	= 'onclick,onblur,onchange,onfocus,onreset,onselect,onsubmit,onabort,onkeydown,onkeypress,onkeyup,onmouseover,onmouseout,ondblclick,onmousemove,onmousedown,onmouseup,onerror,onload,onunload';

		$filterQuery	= 'SELECT COUNT(1) FROM `#__easyblog_acl_filters` WHERE `content_id`=' . $db->Quote( $uKey );
		$filterQuery	.= 'AND `type`=' . $db->Quote( 'group' );

		$db->setQuery( $filterQuery );
		$filterExist 	= $db->loadResult();

		// @rule: Insert default filter if it doesn't exist.
		if( !$filterExist )
		{
			// We will allow admin or super admin to post anything
			if( $uGroup == 'all' )
			{
				$defaultFilterTags			= '';
				$defaultFilterAttributes	= '';
			}

			$filterQuery  = 'INSERT INTO `#__easyblog_acl_filters` (`content_id`, `disallow_tags`, `disallow_attributes`, `type`) VALUES ('
							. $db->Quote( $uKey ) . ',' . $db->Quote( $defaultFilterTags ) . ',' . $db->Quote( $defaultFilterAttributes ) . ',' . $db->Quote( 'group' )
							. ')';
			$db->setQuery( $filterQuery );
			$db->Query();
		}

		// @rule: Insert default acl if it doesn't exists
		$query  = 'SELECT COUNT(1) FROM `#__easyblog_acl_group` WHERE `content_id` = ' . $db->Quote($uKey);
		$query  .= ' AND `type` = ' . $db->Quote('group');

		$db->setQuery($query);
		$result = $db->loadResult();

		if(empty($result))
		{
			$udAcls  = array();

			if( is_array($uGroup))
			{
				$udAcls	= $aclRules;

				foreach($uGroup as $uAcl)
				{
					$udAcls[$uAcl] = 1;
				}
			}
			else if($uGroup == 'all')
			{
				$udAcls = $aclRulesAllEnabled;
			}

			foreach($udAcls as $key	=> $value)
			{
				$str    		= '(' . $db->Quote($uKey) . ', ' . $db->Quote($key) . ', ' . $db->Quote($value) . ', ' . $db->Quote('group') .')';
				$mainQuery[]    = $str;
			}
		}
		else
		{
			// @rule: Insert default acl if it partially exisits

			// This part should be executed for Easyblog upgrade
			// while new ACL rules are introduced.

			// now we need to loop the groups and in each group (already in the loop)
			// we loop the acl rules and check which is missing

			// perhaps array_diff would gives a better performance

			// Legends:
			// $uKey = group id
			// $uGroup  = string "all" | empty array | array of acl ids to be set true
			// $aclTemp = array of all acl ids

			foreach ($aclTemp as $aclId)
			{
				$query	= 'SELECT COUNT(*) FROM `#__easyblog_acl_group`'
						. ' WHERE content_id = ' . $db->Quote( $uKey )
						. ' AND acl_id = ' . $db->Quote( $aclId );
				$db->setQuery( $query );
				$result = $db->loadResult();

				if( !$result )
				{
					if( $uGroup == 'all' || (is_array($uGroup) && in_array($aclId, $uGroup)) )
					{
						$value = 1;
					}
					else
					{
						$value = 0;
					}

					$mainQuery[] = '(' . $db->Quote($uKey) . ', ' . $db->Quote($aclId) . ', ' . $db->Quote($value) . ', ' . $db->Quote('group') .')';
				}
			}
		}

	}//end foreach usergroup

	if(! empty($mainQuery))
	{
		$query  = 'INSERT INTO `#__easyblog_acl_group` (`content_id`, `acl_id`, `status`, `type`) VALUES ';
		$query  .= implode(',', $mainQuery);

		$db->setQuery($query);
		$db->query();

		if($db->getErrorNum())
		{
			return false;
		}
	}

	return true;
}

function truncateACLTable()
{
	$db 	= DBHelper::db();

	$query 	= "DELETE FROM " . $db->nameQuote('#__easyblog_acl');
	$db->setQuery( $query );
	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}

	return true;
}

function configExist()
{
	$db		= DBHelper::db();

	$query	= 'SHOW CREATE TABLE ' . $db->nameQuote('#__easyblog_configs');
	$db->setQuery( $query );

	if ($db->query()===false)
	{
		return false;
	}

	$query	= 'SELECT COUNT(*) FROM '
			. $db->nameQuote( '#__easyblog_configs' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( 'config' );
	$db->setQuery( $query );

	return ( $db->loadResult() > 0 ) ? true : false;
}

function createConfig()
{
	$db = DBHelper::db();
	$config = JPATH_ROOT . '/administrator/components/com_easyblog/configuration.ini';
	$contents = JFile::read($config);

	$obj = new stdClass();
	$obj->name = 'config';
	$obj->params = $contents;

	return $db->insertObject('#__easyblog_configs' , $obj);
}

function installDefaultPlugin( $sourcePath )
{
	$db 			= DBHelper::db();
	$pluginFolder	= $sourcePath . DS . 'default_plugin';
	$plugins		= new stdClass();

	$joomlaVersion 	= getJoomlaVersion();

	//set plugin details
	$plugins->deleteuser            = new stdClass();
	$plugins->deleteuser->zip  		= $pluginFolder . DS . 'plg_easyblogusers.zip';

	if($joomlaVersion >= '1.6'){
		$plugins->deleteuser->path 	= JPATH_ROOT . DS . 'plugins' . DS . 'user' . DS . 'easyblogusers';
	} else {
		$plugins->deleteuser->path 	= JPATH_ROOT . DS . 'plugins' . DS . 'user';
	}

	$plugins->deleteuser->name 		= 'User - EasyBlog Users';
	$plugins->deleteuser->element 	= 'easyblogusers';
	$plugins->deleteuser->folder 	= 'user';
	$plugins->deleteuser->params 	= '';
	$plugins->deleteuser->lang 		= '';

	foreach($plugins as $plugin)
	{
		if(!JFolder::exists($plugin->path))
		{
			JFolder::create($plugin->path);
		}

		if( extractArchive($plugin->zip, $plugin->path) )
		{
			if( $joomlaVersion >= '1.6' ) {
				//delete old plugin entry before install
				$sql = 'DELETE FROM '
								. $db->nameQuote('#__extensions') . ' '
					 . 'WHERE ' . $db->nameQuote('element') . '=' . $db->quote($plugin->element) . ' AND '
								. $db->nameQuote('folder') . '=' . $db->quote($plugin->folder) . ' AND '
								. $db->nameQuote('type') . '=' . $db->quote('plugin') . ' ';
				$db->setQuery($sql);
				$db->Query();

				//insert plugin again
				$sql 	= 'INSERT INTO ' . $db->nameQuote( '#__extensions' )
						. '('
							. $db->nameQuote( 'name' ) . ', '
							. $db->nameQuote( 'type' ) . ', '
							. $db->nameQuote( 'element' ) . ', '
							. $db->nameQuote( 'folder' ) . ', '
							. $db->nameQuote( 'client_id' ) . ', '
							. $db->nameQuote( 'enabled' ) . ', '
							. $db->nameQuote( 'access' ) . ', '
							. $db->nameQuote( 'protected' ) . ', '
							. $db->nameQuote( 'params' ) . ', '
							. $db->nameQuote( 'ordering' ) . ' '
						. ') '
						. 'VALUES('
							. $db->quote( $plugin->name ) . ', '
							. $db->quote( 'plugin' ) . ', '
							. $db->quote( $plugin->element ) . ', '
							. $db->quote( $plugin->folder ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '1' ) . ', '
							. $db->quote( '1' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( $plugin->params ) . ', '
							. $db->quote( '0' ) . ' '
						. ') ';
			} else {
				//delete old plugin entry before install
				$sql = 'DELETE FROM '
								. $db->nameQuote('#__plugins') . ' '
					 . 'WHERE ' . $db->nameQuote('element') . '=' . $db->quote($plugin->element) . ' AND '
								. $db->nameQuote('folder') . '=' . $db->quote($plugin->folder);
				$db->setQuery($sql);
				$db->Query();

				//insert plugin again
				$sql 	= 'INSERT INTO ' . $db->nameQuote( '#__plugins' )
						. '('
							. $db->nameQuote( 'name' ) . ', '
							. $db->nameQuote( 'element' ) . ', '
							. $db->nameQuote( 'folder' ) . ', '
							. $db->nameQuote( 'access' ) . ', '
							. $db->nameQuote( 'ordering' ) . ', '
							. $db->nameQuote( 'published' ) . ', '
							. $db->nameQuote( 'iscore' ) . ', '
							. $db->nameQuote( 'client_id' ) . ', '
							. $db->nameQuote( 'params' ) . ' '
						. ') '
						. 'VALUES('
							. $db->quote( $plugin->name ) . ', '
							. $db->quote( $plugin->element ) . ', '
							. $db->quote( $plugin->folder ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '1' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( $plugin->params ) . ' '
						. ') ';
			}

			$db->setQuery($sql);
			$db->Query();

			if($db->getErrorNum()){
				JError::raiseError( 500, $db->stderr());
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
}

function backupThemes()
{
	$backupDate	= JFactory::getDate()->toFormat('%Y%m%d%H%M%S');

	$src	= JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'themes';
	$dest	= JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'themes_bak'.DS.$backupDate;

	if(!JFolder::exists($src))
	{
		return true;
	}

	if(JFolder::copy($src, $dest))
	{
		return JFolder::delete($src);
	}

	return false;
}

function installThemes($sourcePath)
{
	$src	= $sourcePath . DS . 'site' . DS . 'themesnew';
	$dest	= JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'themes';

	if(JFolder::exists($dest))
	{
		JFolder::delete($dest);
	}

	$themeInstalled = JFolder::copy($src, $dest);

	if(JFolder::exists(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'themesnew'))
	{
		JFolder::delete(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'themesnew');
	}

	return $themeInstalled;
}

/**
 * Method to extract archive
 *
 * @returns	boolean	True on success false if fail.
 **/
function extractArchive( $source , $destination )
{
	// Cleanup path
	$destination	= JPath::clean( $destination );
	$source			= JPath::clean( $source );

	return JArchive::extract( $source , $destination );
}

function updateEasyBlogDBColumns()
{
	$db		= DBHelper::db();

	if(! _isColumnExists( '#__easyblog_post' , 'isnew' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `isnew` tinyint unsigned NULL DEFAULT 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'copyrights' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `copyrights` TEXT NOT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'copyrights' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD `copyrights` TEXT NOT NULL';
		$db->setQuery($query);
		$db->query();
	}


	if(! _isColumnExists( '#__easyblog_post' , 'robots' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `robots` TEXT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'robots' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD `robots` TEXT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_trackback_sent' , 'sent' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_trackback_sent` ADD COLUMN `sent` TINYINT(1) DEFAULT 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_team' , 'alias' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_team` ADD `alias` VARCHAR( 255 ) NULL AFTER `title` ';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'ispending' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD COLUMN `ispending` TINYINT(1) DEFAULT 0 NULL AFTER `isnew` ';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_migrate_content' , 'component' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_migrate_content` ADD COLUMN `component` varchar(255) NOT NULL DEFAULT ' . $db->Quote('com_content');
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_users' , 'permalink' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_users` ADD COLUMN `permalink` varchar(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category' , 'avatar' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `avatar` varchar(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post_subscription' , 'fullname' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post_subscription` ADD COLUMN `fullname` varchar(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_blogger_subscription' , 'fullname' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_blogger_subscription` ADD COLUMN `fullname` varchar(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category_subscription' , 'fullname' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category_subscription` ADD COLUMN `fullname` varchar(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category' , 'parent_id' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `parent_id` int(11) NULL default 0';
		$db->setQuery($query);
		$db->query();
	}

	if( ! _isColumnExists( '#__easyblog_category' , 'description' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `description` TEXT NULL AFTER `title`';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category' , 'private' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `private` int(11) NULL default 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category' , 'level' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `level` int(11) unsigned default 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category' , 'lft' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `lft` int(11) unsigned default 0';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__easyblog_category` ADD INDEX `easyblog_cat_lft` (`lft`)';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category' , 'rgt' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `rgt` int(11) unsigned default 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_category' , 'default' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_category` ADD COLUMN `default` int(11) unsigned default 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'issitewide' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD COLUMN `issitewide` TINYINT(1) DEFAULT 1 NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'latitude' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD COLUMN `latitude` VARCHAR(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'longitude' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD COLUMN `longitude` VARCHAR(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'address' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD COLUMN `address` TEXT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'system' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `system` tinyint unsigned NULL DEFAULT 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'source' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `source` VARCHAR(255) NOT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'image' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `image` TEXT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'address' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD COLUMN `address` TEXT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'latitude' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD COLUMN `latitude` VARCHAR(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'longitude' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD COLUMN `longitude` VARCHAR(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'external_source' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD COLUMN (`external_source` TEXT NULL, `external_group_id` INT( 11 ) NULL)';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'source' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD COLUMN `source` VARCHAR(255) NOT NULL';

		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_team' , 'access' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_team` ADD COLUMN `access` TINYINT(1) DEFAULT 1 NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_team' , 'avatar' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_team` ADD COLUMN `avatar` varchar(255) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_feedburner' , 'id' ) )
	{
		//now remove the PK and recreate PK.
		$query = 'ALTER TABLE `#__easyblog_feedburner` DROP PRIMARY KEY';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__easyblog_feedburner` ADD COLUMN `id` bigint(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_comment' , 'sent' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_comment` ADD `sent` TINYINT( 1 ) DEFAULT 1 NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_team_users' , 'isadmin' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_team_users` ADD `isadmin` tinyint(1) DEFAULT 0 NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'blogpassword' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `blogpassword` VARCHAR( 100 ) NOT NULL DEFAULT ""';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_ratings' , 'sessionid' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_ratings` ADD `sessionid` VARCHAR( 200 ) NOT NULL DEFAULT ""';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_tag' , 'default' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_tag` ADD `default` TINYINT( 1 ) NOT NULL DEFAULT 0 AFTER `published`';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_oauth' , 'system' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_oauth` ADD `system` tinyint unsigned NULL DEFAULT 0';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_migrate_content' , 'filename' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_migrate_content` ADD `filename` VARCHAR( 255 ) NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_feeds' , 'item_get_fulltext' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_feeds` ADD `item_get_fulltext` tinyint(3) default 0 NOT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_xml_wpdata' , 'comments' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_xml_wpdata` ADD `comments` LONGTEXT NULL AFTER `data`';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_post' , 'language' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `language` CHAR( 7 ) NOT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'language' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD `language` CHAR( 7 ) NOT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_meta' , 'indexing' ) )
	{
		$query	= 'ALTER TABLE `#__easyblog_meta` ADD `indexing` INT( 3 ) NOT NULL DEFAULT 1';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__easyblog_drafts' , 'autopost_centralized' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_drafts` ADD `autopost_centralized` TEXT NOT NULL AFTER `autopost`';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isIndexKeyExists('#__easyblog_tag', 'easyblog_tag_title') )
	{
		$query = 'ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_title` (`title`)';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_published` (`published`)';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_alias` (`alias`)';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_query1` (`published`, `id`, `title`)';
		$db->setQuery($query);
		$db->query();
	}

	if( !_isColumnExists( '#__easyblog_drafts' , 'image' ) )
	{
		$query 	= 'ALTER TABLE `#__easyblog_drafts` ADD `image` TEXT NOT NULL';
		$db->setQuery( $query );
		$db->query();
	}

	// @since 3.5.12233
	// Reporting ip address.
	if( !_isColumnExists( '#__easyblog_reports' , 'ip' ) )
	{
		$query 	= 'ALTER TABLE `#__easyblog_reports` ADD `ip` TEXT NOT NULL';
		$db->setQuery( $query );
		$db->query();
	}

	if(! _isIndexKeyExists('#__easyblog_post_tag', 'easyblog_post_tagpost_id') )
	{

		$query 	= 'ALTER TABLE `#__easyblog_post_tag` ADD INDEX `easyblog_post_tagpost_id` ( `tag_id`, `post_id` )';
		$db->setQuery( $query );
		$db->query();

		$query 	= 'ALTER TABLE `#__easyblog_acl_group` ADD INDEX `acl_grp_acl_type` ( `acl_id`, `type` )';
		$db->setQuery( $query );
		$db->query();

	}

	if(! _isIndexKeyExists('#__easyblog_post', 'easyblog_post_blogger_list') )
	{
		$query 	= 'ALTER TABLE `#__easyblog_post` ADD INDEX `easyblog_post_blogger_list` ( `published`, `id`, `created_by` )';
		$db->setQuery( $query );
		$db->query();


		$query 	= 'ALTER TABLE `#__easyblog_post` ADD INDEX `easyblog_post_search` ( `private`, `published`, `issitewide`, `created` )';
		$db->setQuery( $query );
		$db->query();
	}

	if (!_isColumnExists('#__easyblog_post', 'autoposting')) {
		$query = 'ALTER TABLE `#__easyblog_post` ADD `autoposting` TEXT NOT NULL;';

		$db->setQuery($query);
		$db->query();		
	}

	if(! _isColumnExists( '#__easyblog_post' , 'send_notification_emails' ) )
	{
		$query = 'ALTER TABLE `#__easyblog_post` ADD `send_notification_emails` TINYINT( 1 ) NOT NULL DEFAULT 1';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__easyblog_drafts` ADD `send_notification_emails` TINYINT( 1 ) NOT NULL DEFAULT 1';
		$db->setQuery($query);
		$db->query();
	}

	return true;
}


function _isColumnExists($tbName, $colName)
{
	static $cache 	= array();

	if(! isset( $cache[$tbName] ) )
	{
		$db		= DBHelper::db();

		$query	= 'SHOW FIELDS FROM ' . $db->nameQuote( $tbName );
		$db->setQuery( $query );

		$fields	= $db->loadObjectList();

		foreach( $fields as $field )
		{
			$cache[$tbName][ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
		}
	}

	$result = $cache[ $tbName ];

	if(array_key_exists($colName, $result))
	{
		return true;
	}

	return false;
}

function _isIndexKeyExists($tbName, $indexName)
{
	$db		= DBHelper::db();

	$query	= 'SHOW INDEX FROM ' . $db->nameQuote( $tbName );
	$db->setQuery( $query );
	$indexes	= $db->loadObjectList();

	$result = array();
	foreach( $indexes as $index )
	{
		$result[ $index->Key_name ]	= preg_replace( '/[(0-9)]/' , '' , $index->Column_name );
	}

	if(array_key_exists($indexName, $result))
	{
		return true;
	}

	return false;
}

function getJoomlaVersion()
{
	$jVerArr   = explode('.', JVERSION);
	$jVersion  = $jVerArr[0] . '.' . $jVerArr[1];

	return $jVersion;
}

function getSAUsersIds()
{
	$db = DBHelper::db();

	$query  = 'SELECT a.`id`, a.`title`';
	$query	.= ' FROM `#__usergroups` AS a';
	$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
	$query	.= ' GROUP BY a.id';
	$query	.= ' ORDER BY a.lft ASC';

	$db->setQuery($query);
	$result = $db->loadObjectList();

	$saGroup    = array();
	foreach($result as $group)
	{
		if(JAccess::checkGroup($group->id, 'core.admin'))
		{
			$saGroup[]  = $group;
		}
	}


	//now we got all the SA groups. Time to get the users
	$saUsers    = array();
	if(count($saGroup) > 0)
	{
		foreach($saGroup as $sag)
		{
			  $userArr	= JAccess::getUsersByGroup($sag->id);
			  if(count($userArr) > 0)
			  {
				  foreach($userArr as $user)
				  {
					  $saUsers[]    = $user;
				  }
			  }
		}
	}

	return $saUsers;
}

function postExist()
{
	$db		= DBHelper::db();

	$query	= 'SELECT COUNT(1) FROM '
			. $db->nameQuote( '#__easyblog_post' );
	$db->setQuery( $query );

	$result = $db->loadResult();
	$exist	= ( !empty($result) ) ? true : false;

	return $exist;
}

function createSamplePost()
{
	$db 	= DBHelper::db();

	$suAdmin    = _getSuperAdminId();

	$post = new stdClass();
	$post->title		= 'Congratulations! You have successfully installed EasyBlog!';
	$post->permalink	= 'congratulations-you-have-successfully-installed-easyblog';

	$post->content		= '<h2>With EasyBlog, you can be assured of quality blogging with the following features:</h2>'
						. '<ol>'
						. '<li> <strong><span style="text-decoration: underline;">Blog now, post later</span></strong><br />You can compose a blog now, suffer temporal writer\'s block, save and write again, later.</li>'
						. '<li> <strong><span style="text-decoration: underline;">Social media sharing</span></strong><br />Automatically post into your <em><strong>Twitter</strong></em>, <em><strong>Facebook</strong></em> and <em><strong>LinkedIn</strong></em> whenever you create new blog entries.</li>'
						. '<li> <strong><span style="text-decoration: underline;">Browse media</span></strong><br />Embedding images and videos is fast and easy.</li>'
						. '<li> <strong><span style="text-decoration: underline;">More third party integrations</span></strong><br />Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.</li>'
						. '<li> <strong><span style="text-decoration: underline;">Blog rating</span></strong><br />Users can show intensity of their favorite blog post by rating them with stars.</li>'
						. '</ol>'
						. '<p>And many more powerful features that you can use to make your blog work beautifully and professionally!</p>'
						. '<p> </p>'
						. '<p>We welcome any inquiries and feedback from you. Feel free to send us an email to <a href="mailto:support@stackideas.com" target="_blank">support@stackideas.com</a> for immediate attention. Or, you can also refer to our Documentations and FAQs from our website at <a href="http://stackideas.com" target="_blank">http://stackideas.com</a></p>';

	$post->intro		= '<h2>Thank you for making the right decision to start blogging in your Joomla! website.</h2>'
						. '<p><img src="http://stackideas.com/images/eblog/install_success35.png" border="0" style="align:center;" /></p>'
						. '<p> </p>';

	$query 		= 'INSERT IGNORE INTO `#__easyblog_post` ( `id`, `created_by`, `created`, `modified`, `title`, `permalink`, `content`, `intro`, `excerpt`, `category_id`, `published`, `publish_up`, `publish_down`, `ordering`, `vote`, `hits`, `private`, `allowcomment`, `subscription`, `frontpage`, `isnew`, `ispending`, `issitewide`, `blogpassword` ) '
				. 'VALUES ( "1", ' . $db->Quote($suAdmin) . ', now(), now(), ' . $db->Quote($post->title) . ', ' . $db->Quote($post->permalink) . ', ' . $db->Quote($post->content) . ', ' . $db->Quote($post->intro) . ', ' . $db->Quote($post->intro) . ', "1", "1", now(), "0000-00-00 00:00:00", "0", "0", "0", "0", "0", "1", "1", "1", "0", "1", "" )';
	$db->setQuery( $query );
	$db->query();

	//create tag for sample post
	$query 		= 'INSERT IGNORE INTO `#__easyblog_tag` ( `id`, `created_by`, `title`, `alias`, `created`, `status`, `published`, `ordering`) '
				. 'VALUES ( "1", ' . $db->Quote($suAdmin) .', "Thank You", "thank-you", now(), "0", "1", "0" ), '
				. '( "2", ' . $db->Quote($suAdmin) .', "Congratulations", "congratulations", now(), "0", "1", "0" ) ';
	$db->setQuery( $query );
	$db->query();

	//create posts tags records
	$query 		= 'INSERT INTO `#__easyblog_post_tag` ( `tag_id`, `post_id`, `created`) '
				. 'VALUES ( "1", "1", now() ), '
				. '( "2", "1", now() ) ';
	$db->setQuery( $query );
	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}
	return true;
}

function removeAdminMenu()
{
	$db = DBHelper::db();

	$query  = '	DELETE FROM `#__menu` WHERE link LIKE \'%com_easyblog%\' AND client_id = \'1\'';

	$db->setQuery($query);
	$db->query();
}

function fixMenuIds()
{
	$joomlaVersion = getJoomlaVersion();

	if( $joomlaVersion >= '1.6')
	{
		$db 				= DBHelper::db();
		$executeStepTwo    	= true;
		$element = 'COM_EASYBLOG';

		$query  = 'SELECT a.id';
		$query  .= ' FROM `#__menu` as a';
		$query  .= ' WHERE a.`parent_id` = 1';
		$query  .= ' AND a.`client_id` = 1';
		$query  .= ' AND a.`component_id` = 0';
		$query  .= ' AND a.`title` = ' . $db->quote( strtolower( $element ) );

		$db->setQuery($query);
		$invalidRow = $db->loadResult();

		if( $invalidRow )
		{
			//found invalid menu with component_id = 0.
			$query  = 'SELECT `extension_id`';
			$query  .= ' FROM `#__extensions`';
			$query  .= ' WHERE `element` = ' . $db->Quote($element);
			$query  .= ' AND `type` = ' . $db->Quote('component');
			$db->setQuery($query);

			$extension_id = $db->loadResult();

			//now we are ready to fix all the invalid admin menu
			$query  = 'UPDATE `#__menu` SET `component_id` = ' . $db->Quote($extension_id);
			$query  .= ' WHERE parent_id = 1';
			$query  .= ' AND client_id = 1';
			$query  .= ' AND component_id = 0';
			$query  .= ' AND `title` LIKE ' . $db->Quote( $element . '%');

			$db->setQuery($query);
			$db->query();

			$executeStepTwo = false;
		}

		if( $executeStepTwo )
		{
			$cid	= getEblogId();

			$query  = 'select `component_id` from `#__menu`';
			$query	.= ' where menutype = ' . $db->Quote('main');
			$query	.= ' and client_id = 1';
			$query	.= ' and title like ' . $db->Quote( $element . '%');
			$query  .= ' LIMIT 1';

			$db->setQuery($query);
			$result = $db->loadResult();

			if( !empty( $result ) )
			{
				if( $cid != $result )
				{
					// the compoent id is not match. update it.
					$query  = 'UPDATE `#__menu` SET `component_id` = ' . $db->Quote($cid);
					$query  .= ' WHERE menutype = ' . $db->Quote('main');;
					$query  .= ' AND client_id = 1';
					$query  .= ' AND component_id = ' . $db->Quote($result);
					$query  .= ' AND `title` LIKE ' . $db->Quote( $element . '%');

					$db->setQuery($query);
					$db->query();

				}
			}//end if

		}//end if steptwo

	}

}

function twitterTableExist()
{
	$db = DBHelper::db();

	$query  = 'SHOW TABLES LIKE ' . $db->quote('#__easyblog_twitter');

	$db->setQuery($query);
	$result = $db->loadResult();

	return $result? true : false;
}


function twitterTableMigrate()
{
	$db = DBHelper::db();

	$query  = 	'INSERT INTO ' . $db->nameQuote('#__easyblog_oauth') . '(`user_id`, `type`, `auto`, `request_token`, `access_token`, `message`, `created`, `params`) '
			.		'SELECT `user_id`, "twitter" as type, `auto`, '
			.			'replace(replace(`oauth_request_token`, "oauth_token_secret", "secret"), "oauth_token", "token") as request_token, '
			.			'replace(replace(`oauth_access_token` , "oauth_token_secret", "secret"), "oauth_token", "token") as access_token, '
			.			'`message`, NOW() as created, CONCAT("user_id=", `user_id`, "\r\n", "screen_name=", `username` ) as params '
			.		'FROM ' . $db->nameQuote('#__easyblog_twitter');

	$db->setQuery($query);
	$result = $db->query();

	return $result;
}

function twitterTableRemove()
{
	$db = DBHelper::db();

	$query  = 'DELETE FROM ' . $db->nameQuote('#__easyblog_twitter_posts');
	$db->setQuery($query);
	$db->query();

	$query  = 'DELETE FROM ' . $db->nameQuote('#__easyblog_twitter');
	$db->setQuery($query);
	$db->query();

	return $result;
}


function migrateJomSocialStreamNameSpace()
{
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');

	$jsCoreFile	= JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'community.php';

	if(JFile::exists( $jsCoreFile ))
	{
		$db = DBHelper::db();

		$query  = 'UPDATE `#__community_activities` SET `app` = ' . $db->Quote( 'easyblog' ) . ' WHERE `app` = ' . $db->Quote( 'com_easyblog' );

		$db->setQuery($query);
		$db->query();
	}
}

function copyMediaFiles($sourcePath)
{
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');

	$mediaSource	= $sourcePath.DS.'media'.DS.'com_easyblog';
	$mediaDestina	= JPATH_ROOT.DS.'media'.DS.'com_easyblog';

	if (! JFolder::copy($mediaSource, $mediaDestina, '', true) )
	{
		return false;
	}

	// Copy media/foundry
	// Overwrite only if version is newer
	$mediaFoundrySource		= $sourcePath . DS . 'media' . DS . 'foundry';
	$mediaFoundryDestina	= JPATH_ROOT . DS . 'media' . DS . 'foundry';
	$overwrite				= false;
	$incomingVersion 		= '';
	$installedVersion 		= '';


	if(! JFolder::exists( $mediaFoundryDestina ) )
	{
		// foundry folder not found. just copy foundry folde without need to check.
		if (! JFolder::copy($mediaFoundrySource, $mediaFoundryDestina, '', true) )
		{
			return false;
		}

		return true;
	}

	// if foundry already exists, lets check the foundry version.

	$folder	= JFolder::folders($mediaFoundrySource);

	// check if the foundry from the installer has the version or not.
	if(	!($incomingVersion = (string) JFile::read( $mediaFoundrySource . DS . $folder[0] . DS . 'version' )) )
	{
		// can't read the version number.
		return false;
	}

	if( !JFile::exists($mediaFoundryDestina . DS . $folder[0] . DS . 'version')
		|| !($installedVersion = (string) JFile::read( $mediaFoundryDestina . DS . $folder[0] . DS . 'version' )) )
	{
		// foundry version not exists or need upgrade
		$overwrite = true;
	}

	$incomingVersion	= preg_replace('/[^a-zA-Z0-9\.]/i', '', $incomingVersion);
	$installedVersion	= preg_replace('/[^a-zA-Z0-9\.]/i', '', $installedVersion);

	if( $overwrite || version_compare($incomingVersion, $installedVersion) >= 0 )
	{

		$iVerArr				= explode('.', $incomingVersion);
		$newVersionFolderName	= $iVerArr[0] . '.' . $iVerArr[1];

		if( !JFolder::copy($mediaFoundrySource . DS . $newVersionFolderName, $mediaFoundryDestina . DS . $newVersionFolderName, '', true) )
		{
			return false;
		}
	}


	return true;
}

class DBHelper
{
	public static $helper		= null;

	public static function db()
	{

		if( is_null( self::$helper ) )
		{
			$version    = DBHelper::getJoomlaVersion();
			$className	= 'EasyBlogDBJoomla15';

			if( $version >= '2.5' )
			{
				$className 	= 'EasyBlogDBJoomla30';
			}

			self::$helper   = new $className();
		}

		return self::$helper;

	}

	public static function getJoomlaVersion()
	{
		$jVerArr   = explode('.', JVERSION);
		$jVersion  = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

}


class EasyBlogDbJoomla15
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}


class EasyBlogDbJoomla30
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function nameQuote( $str )
	{
		return $this->db->quoteName( $str );
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}
