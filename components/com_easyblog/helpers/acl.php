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

class EasyBlogACLHelper
{
	public static function getRuleSet( $cid='' )
	{
		static $rulesData = null;

		$my		= empty($cid) ? JFactory::getUser() : JFactory::getUser($cid);

		if( !isset( $rulesData[ $my->id ] ) )
		{
			$db		= EasyBlogHelper::db();
			$config = EasyBlogHelper::getConfig();

			$rulesets			= new stdClass();
			$rulesets->rules	= new stdClass();

			// @Task: Retrieve rules
			$rules				= EasyBlogACLHelper::getRules('id');

			if( !empty($my->id) )
			{
				$rulesets->id		= $my->id;
				$rulesets->name		= $my->name;
				$rulesets->group	= isset( $my->usertype ) ? $my->usertype : '';

				// @Task: Load default values.
				foreach($rules as $rule)
				{
					$rulesets->rules->{$rule->action} = (INT) $rule->default;
				}

				// @Task: Retreive assigned rulesets for this particular user.
				// Assigned rulesets always have higher precedence
				$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_group' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $my->id ) . ' '
						. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'assigned' );
				$db->setQuery( $query );
				$result = $db->loadObjectList();

				if(count($result) > 0)
				{
					$result = array();
					$result[]   = $db->loadObjectList();

					$rulesets	= EasyBlogACLHelper::mapRules( $result , $rules , $rulesets );
				}
				else
				{
					$result = array();
					if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
					{
						// get user's joomla usergroups ids.
						$groupIds   = '';
						$query		= 'SELECT `group_id` FROM `#__user_usergroup_map` WHERE `user_id` = ' . $db->Quote($my->id);
						$db->setQuery($query);

						$groupIds   = $db->loadResultArray();
						$groups		= array();

						// get the last index.
						for($i = 0; $i < count($groupIds); $i++)
						{
							$grpId   =& $groupIds[$i];
							$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_group' ) . ' '
									. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote($grpId) . ' '
									. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );

							$db->setQuery( $query );
							$groups[] = $db->loadObjectList();
						}

						// Allow explicit overrides in the groups
						// If user A is in group A (allow) and group B (not allowed) , user A should be allowed
						$result		= array();

						foreach( $groups as $group )
						{
							foreach( $group as $rule )
							{
								if( !isset( $result[0][ $rule->acl_id ] ) )
								{
									$result[0][ $rule->acl_id ]	= new stdClass();
								}


								if( isset( $result[0][ $rule->acl_id]->acl_id ) && $result[0][ $rule->acl_id ]->status != '1' || !isset( $result[0][ $rule->acl_id]->acl_id ) )
								{
									$result[0][ $rule->acl_id ]->acl_id	= $rule->acl_id;
									$result[0][ $rule->acl_id ]->status	= $rule->status;
								}
							}
						}
					}
					else
					{
						$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_group' ) . ' '
								. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $my->gid ) . ' '
								. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );

						$db->setQuery( $query );
						$result[] = $db->loadObjectList();
					}

					$rulesets	= EasyBlogACLHelper::mapRules( $result , $rules , $rulesets );
				}
			}
			else
			{
				if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
				{
					// Cannot rely on JComponentHelper::getParams( 'com_users' )->get( 'guest_usergroup' ) because Joomla 3.2.0 onwards always returns 13.
					$user 	= JFactory::getUser();
					$tmp	= $user->getAuthorisedGroups();
					$gid 	= $tmp[ 0 ];

					$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_group' ) . ' '
							. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $gid ) . ' '
							. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );

					$db->setQuery( $query );
					$groups[] = $db->loadObjectList();

					// Allow explicit overrides in the groups
					// If user A is in group A (allow) and group B (not allowed) , user A should be allowed
					$result		= array();

					foreach( $groups as $group )
					{
						foreach( $group as $rule )
						{
							if( !isset( $result[0][ $rule->acl_id ] ) )
							{
								$result[0][ $rule->acl_id ]	= new stdClass();
							}

							if( isset( $result[0][ $rule->acl_id]->acl_id ) && $result[0][ $rule->acl_id ]->status != '1' || !isset( $result[0][ $rule->acl_id]->acl_id ) )
							{
								$result[0][ $rule->acl_id ]->acl_id	= $rule->acl_id;
								$result[0][ $rule->acl_id ]->status	= $rule->status;
							}
						}
					}

					$rulesets	= EasyBlogACLHelper::mapRules( $result , $rules , $rulesets );
				}
				else
				{
					$rulesets->id 		= '0';
					$rulesets->name 	= 'guest';
					$rulesets->group	= 'none';

					foreach($rules as $rule)
					{
						$rulesets->rules->{$rule->action} = 0;
					}
				}
			}

			$rulesData[ $my->id ]	= $rulesets;
		}

		return $rulesData[ $my->id ];
	}

	/**
	 * Retrieves the filter for html tags
	 */
	public static function getFilterTags()
	{
		$my		= JFactory::getUser();

		// @rule: Check for assigned first.
		$filter = self::getFilterRule( $my->id , 'assigned' );

		// @rule: If assigned doesn't exist, then we revert back to the group.
		if( !$filter )
		{
			$gids 	= EasyBlogHelper::getUserGids( $my->id );

			foreach( $gids as $gid )
			{
				$result = self::getFilterRule( $gid , 'group' );

				if( $result !== false )
				{
					$filter 	= $result;
				}
			}
		}

		if( $filter )
		{
			$tags 	= $filter->get( 'disallow_tags' );
			$tags 	= strtolower( $tags );
			$tags 	= explode( ',' , $tags );

			return $tags;
		}

		return array();
	}


	/**
	 * Retrieves the filter for html tags
	 */
	public static function getFilterAttributes()
	{
		$my		= JFactory::getUser();

		// @rule: Check for assigned first.
		$filter = self::getFilterRule( $my->id , 'assigned' );

		// @rule: If assigned doesn't exist, then we revert back to the group.
		if( !$filter )
		{
			$gids 	= EasyBlogHelper::getUserGids( $my->id );

			foreach( $gids as $gid )
			{
				$result = self::getFilterRule( $gid , 'group' );

				if( $result !== false )
				{
					$filter 	= $result;
				}
			}
		}

		if( $filter )
		{
			$attributes 	= $filter->get( 'disallow_attributes' );
			$attributes 	= strtolower( $attributes );
			$attributes 	= explode( ',' , $attributes );

			return $attributes;
		}

		return array();
	}

	/**
	 * Retrieve the rule
	 *
	 */
	private static function getFilterRule( $contentId , $type )
	{
		$db 	= EasyBlogHelper::db();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_filters' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $contentId ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
		$db->setQuery( $query );
		
		$result	= $db->loadObject();

		if( !$result )
		{
			return false;
		}
		$filter 	= EasyBlogHelper::getTable( 'AclFilter' );
		$filter->bind( $result );

		return $filter;
	}


	private static function mapRules( $result , $rules , $rulesets )
	{
		foreach( $result as $items )
		{
			foreach( $items as $rule )
			{
				if( isset( $rules[ $rule->acl_id ] ) )
				{
					$action	= $rules[ $rule->acl_id ]->action;

					if(isset($rulesets->rules->{$action}))
					{
						// 'No' explicitly win
						if($rulesets->rules->{$action} == '0')
							continue;
						else
							$rulesets->rules->{$action}	= $rule->status;
					}
					else
					{
						$rulesets->rules->{$action}	= $rule->status;
					}
				}
			}
		}

		return $rulesets;
	}

	private static function getRules($key='')
	{
		$db = EasyBlogHelper::db();
		$sql = 'SELECT * FROM '.$db->nameQuote('#__easyblog_acl').' WHERE `published`=1 ORDER BY `id` ASC';
		$db->setQuery($sql);

		return $db->loadObjectList($key);
	}
}
