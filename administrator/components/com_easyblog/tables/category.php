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
require_once( JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_easyblog'.DIRECTORY_SEPARATOR.'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'privacy.php' );

class EasyBlogTableCategory extends EasyBlogTable
{
	var $id 						= null;
	var $created_by		= null;
	var $title					= null;

	var $alias					= null;
	var $avatar					= null;

	var $parent_id				= null;

	var $private				= null;
	var $created				= null;
	var $status			= null;
	var $published		= null;

	var $ordering		= null;

	var $description	= null;

	var $level			= null;
	var $lft			= null;
	var $rgt			= null;
	var $default		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_category' , 'id' , $db );
	}

	function load( $key = null, $permalink = false )
	{
		if( !$permalink )
		{
			return parent::load( $key );
		}

		$db		= $this->getDBO();

		$query	= 'SELECT id FROM ' . $this->_tbl . ' '
				. 'WHERE alias=' . $db->Quote( $key );
		$db->setQuery( $query );

		$id		= $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if( !$id )
		{
			$query	= 'SELECT id FROM ' . $this->_tbl . ' '
					. 'WHERE alias=' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );
			$db->setQuery( $query );

			$id		= $db->loadResult();
		}
		return parent::load( $id );
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	function delete($pk = null)
	{
		$db		= $this->getDBO();
		$config = EasyBlogHelper::getConfig();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		if( $count > 0 )
		{
			return false;
		}

		$my 	= JFactory::getUser();

		if( $this->created_by != 0 && $this->created_by == $my->id )
		{
	    	JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
	    	$config 	= EasyBlogHelper::getConfig();

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.delete.category' , $my->id , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_CATEGORY' , $this->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.delete.category' , $my->id );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.delete.category' , $my->id );

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'category.remove' , $this->created_by );

			if( $config->get( 'main_jomsocial_userpoint' ) )
			{
				$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
				if( JFile::exists( $path ) )
				{
					require_once( $path );
					CUserPoints::assignPoint( 'com_easyblog.category.remove' , $this->created_by );
				}
			}

			// @since 1.2
			// AlphaUserPoints
			if( EasyBlogHelper::isAUPEnabled() )
			{
				AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_category', AlphaUserPointsHelper::getAnyUserReferreID( $this->created_by ) , '', JText::sprintf('COM_EASYBLOG_AUP_CATEGORY_DELETED', $this->title) );
			}
		}

		/* TODO */
		//remove avatar if previously already uploaded.
		$avatar = $this->avatar;

		if( $avatar != 'cdefault.png' && !empty($avatar))
		{

			$avatar_config_path = $config->get('main_categoryavatarpath');
			$avatar_config_path = rtrim($avatar_config_path, '/');
			$avatar_config_path = JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

			$upload_path		= JPATH_ROOT.DIRECTORY_SEPARATOR.$avatar_config_path;

			$target_file_path		= $upload_path;
			$target_file 			= JPath::clean($target_file_path . DIRECTORY_SEPARATOR. $avatar);

			if(JFile::exists( $target_file ))
			{
				JFile::delete( $target_file );
			}
		}

		//activity logging.
		$activity   = new stdClass();
		$activity->actor_id		= $my->id;
		$activity->target_id	= '0';
		$activity->context_type	= 'category';
		$activity->context_id	= $this->id;
		$activity->verb         = 'delete';
		$activity->uuid         = $this->title;

		$return = parent::delete();

		if( $return )
			EasyBlogHelper::activityLog( $activity );

		return $return;
	}

	function aliasExists()
	{
		$db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );

		if( $this->id != 0 )
		{
			$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	function bind( $data, $ignore = array() )
	{
		parent::bind( $data, $ignore );

		if( empty( $this->created ) )
		{
			$this->created	= EasyBlogHelper::getDate()->toMySQL();
		}

		jimport( 'joomla.filesystem.filter.filteroutput');

		$i	= 1;
		while( $this->aliasExists() || empty($this->alias) )
		{
			$this->alias	= empty($this->alias) ? $this->title : $this->alias . '-' . $i;
			$i++;
		}

		//$this->alias 	= JFilterOutput::stringURLSafe( $this->alias );
		$this->alias 	= EasyBlogRouter::generatePermalink( $this->alias );
	}

	function getRSS()
	{
		return EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=categories&id=' . $this->id, false, 'category' );
	}

	function getAtom()
	{
		return EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=categories&id=' . $this->id , true, 'category' );
	}

	function getAvatar()
	{
		$avatar_link    = '';

		if($this->avatar == 'cdefault.png' || $this->avatar == 'default_category.png' || $this->avatar == 'components/com_easyblog/assets/images/default_category.png' || $this->avatar == 'components/com_easyblog/assets/images/cdefault.png' || empty($this->avatar))
		{
			$avatar_link   = 'components/com_easyblog/assets/images/default_category.png';
		}
		else
		{
			require_once( EBLOG_HELPERS . '/image.php' );
			$avatar_link   = EasyImageHelper::getAvatarRelativePath('category') . '/' . $this->avatar;
		}

		return rtrim(JURI::root(), '/') . '/' . $avatar_link;
	}

	function getPostCount()
	{
		$db = EasyBlogHelper::db();

		$query  = 'SELECT count(1) FROM `#__easyblog_post` WHERE `category_id` = ' . $db->Quote($this->id);
		$db->setQuery($query);

		return $db->loadResult();
	}

	function getChildCount()
	{
		$db = EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		$db->setQuery($query);

		return $db->loadResult();
	}

	/*
	 * Retrieves a list of active bloggers that contributed in this category.
	 *
	 * @param	null
	 * @return	Array	An array of TableProfile objects.
	 */
	public function getActiveBloggers()
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT DISTINCT(`created_by`) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$rows		= $db->loadObjectList();

		if( !$rows )
		{
			return false;
		}

		$bloggers	= array();
		foreach( $rows as $row )
		{
			$profile	= EasyBlogHelper::getTable( 'Profile' , 'Table' );
			$profile->load( $row->created_by );

			$bloggers[]	= $profile;
		}

		return $bloggers;
	}

	public function store($updateNulls = false)
	{
		if( !empty( $this->created ))
		{
			$offset     	= EasyBlogDateHelper::getOffSet();
			$newDate		= EasyBlogHelper::getDate( $this->created, $offset );
			$this->created  = $newDate->toMySQL();
		}
		else
		{
			$newDate		= EasyBlogHelper::getDate();
			$this->created  = $newDate->toMySQL();
		}

	    $my 		= JFactory::getUser();

	    // Add point integrations for new categories
	    if( $this->id == 0 && $my->id > 0 )
	    {
	    	JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
	    	$config 	= EasyBlogHelper::getConfig();

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.new.category' , $my->id , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_CATEGORY' , $this->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.new.category' , $my->id );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.new.category' , $my->id );

			// @since 1.2
			// AlphaUserPoints
			if( EasyBlogHelper::isAUPEnabled() )
			{
				AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_add_category', '', 'easyblog_add_category_' . $this->id, JText::sprintf('COM_EASYBLOG_AUP_NEW_CATEGORY_CREATED', $this->title) );
			}

			if( $config->get('main_jomsocial_userpoint') )
			{
				$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
				if( JFile::exists( $path ) )
				{
					require_once( $path );
					CUserPoints::assignPoint( 'com_easyblog.category.add' , $my->id );
				}
			}

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'category.create' , $my->id );
	    }

		// Figure out the proper nested set model
		if( $this->id == 0 && $this->lft == 0 )
		{
			// No parent id, we use the current lft,rgt
			if( $this->parent_id )
			{
				$left           = $this->getLeft( $this->parent_id );
				$this->lft      = $left;
				$this->rgt      = $this->lft + 1;

				// Update parent's right
				$this->updateRight( $left );
				$this->updateLeft( $left );
			}
			else
			{
				$this->lft      = $this->getLeft() + 1;
				$this->rgt      = $this->lft + 1;
			}
		}

		$isNew  	= ( empty( $this->id ) ) ? true : false;

		$return = parent::store();

		//activity logging.
		$activity   = new stdClass();
		$activity->actor_id		= $my->id;
		$activity->target_id	= '0';
		$activity->context_type	= 'category';
		$activity->context_id	= $this->id;
		$activity->verb         = ( $isNew ) ? 'add' : 'update';
		$activity->uuid         = $this->title;

		EasyBlogHelper::activityLog( $activity );

	    return $return;
	}

	public function saveACL( $post )
	{

		$catRuleItems	= EasyBlogHelper::getTable( 'CategoryAclItem' , 'Table' );
		$categoryRules  = $catRuleItems->getAllRuleItems();

		foreach( $categoryRules as $rule)
		{
			$key    = 'category_acl_'.$rule->action;
			if( isset( $post[ $key ] ) )
			{
				if( count( $post[ $key ] ) > 0)
				{
					foreach( $post[ $key ] as $joomla)
					{
						//now we reinsert again.
						$catRule	= EasyBlogHelper::getTable( 'CategoryAcl' , 'Table' );
						$catRule->category_id	= $this->id;
						$catRule->acl_id 		= $rule->id;
						$catRule->type 			= 'group';
						$catRule->content_id 	= $joomla;
						$catRule->status 		= '1';
						$catRule->store();
					} //end foreach

				} //end if
			}//end if
		}
	}

	public function deleteACL( $aclId = '' )
	{
		$db = EasyBlogHelper::db();

		$query  = 'delete from `#__easyblog_category_acl`';
		$query	.= ' where `category_id` = ' . $db->Quote( $this->id );
		if( !empty($aclId) )
			$query	.= ' and `acl_id` = ' . $db->Quote( $aclId );

		$db->setQuery( $query );
		$db->query();

		return true;
	}

	public function getAssignedACL()
	{
		$db = EasyBlogHelper::db();

		$acl    = array();

		$query  = 'SELECT a.`category_id`, a.`content_id`, a.`status`, b.`id` as `acl_id`';
		$query  .= ' FROM `#__easyblog_category_acl` as a';
		$query  .= ' LEFT JOIN `#__easyblog_category_acl_item` as b';
		$query  .= ' ON a.`acl_id` = b.`id`';
		$query  .= ' WHERE a.`category_id` = ' . $db->Quote( $this->id );
		$query  .= ' AND a.`type` = ' . $db->Quote( 'group' );

		//echo $query;

		$db->setQuery( $query );
		$result = $db->loadObjectList();


		$joomlaGroups    = EasyBlogHelper::getJoomlaUserGroups();

		if( EasyBlogHelper::getJoomlaVersion() < '1.6' )
		{
			$guest  = new stdClass();
			$guest->id		= '0';
			$guest->name	= 'Public';
			$guest->level	= '0';
			array_unshift($joomlaGroups, $guest);
		}

		$acl             = $this->_mapRules($result, $joomlaGroups);

		return $acl;

	}

	public function _mapRules( $catRules, $joomlaGroups)
	{
		$db 	= EasyBlogHelper::db();
		$acl    = array();

		$query  = 'select * from `#__easyblog_category_acl_item` order by id';
		$db->setQuery( $query );

		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $item )
		{
			$aclId 		= $item->id;
			$default    = $item->default;

			foreach( $joomlaGroups as $joomla )
			{
				$groupId    	= $joomla->id;
				$catRulesCnt    = count($catRules);

				if ( empty($acl[$aclId][$groupId]) )
				{
					$acl[$aclId][$groupId] = new stdClass();
				}

				//now match each of the catRules
				if( $catRulesCnt > 0)
				{
					$cnt    = 0;
					foreach( $catRules as $rule)
					{
						if($rule->acl_id == $aclId && $rule->content_id == $groupId)
						{
							$acl[$aclId][$groupId]->status  	= $rule->status;
							$acl[$aclId][$groupId]->acl_id  	= $aclId;
							$acl[$aclId][$groupId]->groupname	= $joomla->name;
							$acl[$aclId][$groupId]->groupid		= $groupId;
							break;
						}
						else
						{
							$cnt++;
						}
					}

					if( $cnt == $catRulesCnt)
					{
						//this means the rules not exist in this joomla group.
						$acl[$aclId][$groupId]->status  	= '0';
						$acl[$aclId][$groupId]->acl_id  	= $aclId;
						$acl[$aclId][$groupId]->groupname	= $joomla->name;
						$acl[$aclId][$groupId]->groupid		= $groupId;
					}
				}
				else
				{
					$acl[$aclId][$groupId]->status  	= $default;
					$acl[$aclId][$groupId]->acl_id  	= $aclId;
					$acl[$aclId][$groupId]->groupname	= $joomla->name;
					$acl[$aclId][$groupId]->groupid		= $groupId;
				}
			}
		}

		return $acl;
	}

	public function checkPrivacy()
	{
		$obj			= new EasyBlogPrivacyError();
		$obj->allowed	= true;

		$my 			= JFactory::getUser();

		if( $this->private == '1' && $my->id == 0)
		{
			$obj->allowed	= false;
			$obj->error		= EasyBlogPrivacyHelper::getErrorHTML();
		}
		else
		{
			if( $this->private == '2')
			{
				$cats    = EasyBlogHelper::getPrivateCategories();

				if( in_array($this->id, $cats) )
				{
					$obj->allowed	= false;
					$obj->error		= JText::_( 'COM_EASYBLOG_PRIVACY_JOMSOCIAL_NOT_AUTHORIZED_ERROR' );
				}

			}
		}

		return $obj;
	}

	// category ordering with lft and rgt
	public function updateLeft( $left, $limit = 0 )
	{
		$db     = EasyBlogHelper::db();
		$query  = 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'lft' ) . '=' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'lft' ) . ' + 2 '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'lft' ) . '>=' . $db->Quote( $left );

		if( !empty( $limit ) )
			$query  .= ' and `lft`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function updateRight( $right, $limit = 0 )
	{
		$db     = EasyBlogHelper::db();
		$query  = 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'rgt' ) . '=' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'rgt' ) . ' + 2 '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'rgt' ) . '>=' . $db->Quote( $right );

		if( !empty( $limit ) )
			$query  .= ' and `rgt`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function getLeft( $parent = 0 )
	{
		$db     = EasyBlogHelper::db();

		if( $parent != 0 )
		{
			$query  = 'SELECT `rgt`' . ' '
					. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '=' . $db->Quote( $parent );
		}
		else
		{
			$query  = 'SELECT MAX(' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'rgt' ) . ') '
					. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl );
		}
		$db->setQuery( $query );

		$left   = (int) $db->loadResult();

		return $left;
	}

	function move( $direction, $where = '' )
	{
		$db = EasyBlogHelper::db();

		if( $direction == -1) //moving up
		{
			// getting prev parent
			$query  = 'select `id`, `lft`, `rgt` from `#__easyblog_category` where `lft` < ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft desc limit 1';

			//echo $query;exit;
			$db->setQuery($query);
			$preParent  = $db->loadObject();

			// calculating new lft
			$newLft = $this->lft - $preParent->lft;
			$preLft = ( ($this->rgt - $newLft) + 1) - $preParent->lft;

			//get prevParent's id and all its child ids
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($preParent->lft) . ' and rgt <= ' . $db->Quote($preParent->rgt);
			$db->setQuery($query);

			// echo '<br>' . $query;
			$preItemChilds = $db->loadResultArray();
			$preChildIds   = implode(',', $preItemChilds);
			$preChildCnt   = count($preItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();

			$childIds   = implode(',', $itemChilds);
			$ChildCnt   = count($itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.
			//update current parent block
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft - ' . $db->Quote($newLft);
			if( $ChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($newLft);
			}
			$query  .= ' where `id` in (' . $childIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($preLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($preLft);
			$query  .= ' where `id` in (' . $preChildIds . ')';

			//echo '<br>' . $query;
			//exit;
			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);
			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($preParent->id);
			$db->setQuery($query);
			$db->query();

			return true;
		}
		else //moving down
		{
			// getting next parent
			$query  = 'select `id`, `lft`, `rgt` from `#__easyblog_category` where `lft` > ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft asc limit 1';

			$db->setQuery($query);
			$nextParent  = $db->loadObject();


			$nextLft 	= $nextParent->lft - $this->lft;
			$newLft 	= ( ($nextParent->rgt - $nextLft) + 1) - $this->lft;


			//get nextParent's id and all its child ids
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($nextParent->lft) . ' and rgt <= ' . $db->Quote($nextParent->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$nextItemChilds = $db->loadResultArray();
			$nextChildIds   = implode(',', $nextItemChilds);
			$nextChildCnt   = count($nextItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();
			$childIds   = implode(',', $itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.

			//update next parent block
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `lft` = `lft` - ' . $db->Quote($nextLft);
			if( $nextChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($nextLft);
			}
			$query  .= ' where `id` in (' . $nextChildIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			//update current parent
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($newLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($newLft);
			$query  .= ' where `id` in (' . $childIds. ')';

			//echo '<br>' . $query;
			//exit;

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($nextParent->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			return true;
		}
	}

	public function rebuildOrdering($parentId = null, $leftId = 0 )
	{
		$db = EasyBlogHelper::db();

		$query  = 'select `id` from `#__easyblog_category`';
		$query  .= ' where parent_id = ' . $db->Quote( $parentId );
		$query  .= ' order by lft';

		$db->setQuery( $query );
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildOrdering($node->id, $rightId );

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false) return false;
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$updateQuery    = 'update `#__easyblog_category` set';
		$updateQuery    .= ' `lft` = ' . $db->Quote( $leftId );
		$updateQuery    .= ', `rgt` = ' . $db->Quote( $rightId );
		$updateQuery    .= ' where `id` = ' . $db->Quote($parentId);

		$db->setQuery($updateQuery);

		// If there is an update failure, return false to break out of the recursion.
		if (! $db->query())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	public function updateOrdering()
	{
		$db = EasyBlogHelper::db();

		$query  = 'select `id` from `#__easyblog_category`';
		$query  .= ' order by lft';

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if( count( $rows ) > 0 )
		{
			$orderNum = '1';

			foreach( $rows as $row )
			{
				$query  = 'update `#__easyblog_category` set';
				$query  .= ' `ordering` = ' . $db->Quote( $orderNum );
				$query  .= ' where `id` = ' . $db->Quote( $row->id );

				$db->setQuery( $query );
				$db->query();

				$orderNum++;
			}
		}

		return true;
	}

	public function getMetaId()
	{
		$db = $this->_db;

		$query  = 'SELECT a.`id` FROM `#__easyblog_meta` AS a';
		$query  .= ' WHERE a.`content_id` = ' . $db->Quote($this->id);
		$query  .= ' AND a.`type` = ' . $db->Quote( META_TYPE_CATEGORY );

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function createMeta()
	{
		$id		= $this->getMetaId();

		// @rule: Save meta tags for this entry.
		$meta		= EasyBlogHelper::getTable( 'Meta' );
		$meta->load( $id );

		$meta->set( 'keywords'		, '' );

		if( !$meta->description )
		{
			$meta->description 	= strip_tags( $this->description );
		}

		$meta->set( 'content_id'	, $this->id );
		$meta->set( 'type'			, META_TYPE_CATEGORY );
		$meta->store();
	}
}
