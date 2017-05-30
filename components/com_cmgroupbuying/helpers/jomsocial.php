<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperJomsocial
{
	public static function checkInstalled()
	{
		$jsAPI = JPATH_ROOT . '/components/com_community/libraries/core.php';

		if(file_exists($jsAPI))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getUserPoints($userId)
	{
		$points = 0;

		if(CMGroupBuyingHelperJomsocial::checkInstalled())
		{
			$jspath = JPATH_ROOT . '/components/com_community';
			include_once($jspath. '/libraries/core.php');
			// Get CUser object
			$user = CFactory::getUser($userId);
			$points = $user->getKarmaPoint();
		}

		return $points;
	}

	public static function rewardReferral($referrerId)
	{
		if(CMGroupBuyingHelperJomsocial::checkInstalled() && $referrerId > 0)
		{
			include_once(JPATH_ROOT . '/components/com_community/libraries/userpoints.php');
			CUserPoints::assignPoint('com_cmgroupbuying.referral.add', $referrerId);
		}
	}

	public static function newPoints($userId, $points)
	{
		if(CMGroupBuyingHelperJomsocial::checkInstalled())
		{
			// The following code was taken from components/com_community/libraries/userpoints.php, assignPoint function
			// because JomSocial uses fixed point in point rule
			require_once(JPATH_ROOT . '/components/com_community/libraries/core.php' );
			require_once(JPATH_ROOT . '/components/com_community/libraries/karma.php' );
			//get the rule points
			//must use the JFactory::getUser to get the aid
			$juser = & JFactory::getUser($userId);

			if( $juser->id != 0 )
			{
				if(!method_exists($juser,'authorisedLevels')) {
					$aid    = $juser->aid;
					// if the aid is null, means this is not the current logged-in user. 
					// so we need to manually get this aid for this user.
					if(is_null($aid))
					{
						$aid = 0; //defautl to 0
						// Get an ACL object
						$acl =& JFactory::getACL();
						$grp = $acl->getAroGroup($juser->id);
						$group = 'USERS';

						if($acl->is_group_child_of( $grp->name, $group))
						{
							$aid = 1;
							// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
							if ($acl->is_group_child_of($grp->name, 'Registered') ||
								$acl->is_group_child_of($grp->name, 'Public Backend'))    {
								$aid = 2;
							}
						}
					}
				} else {
					//joomla 1.6
					$aid = $juser->authorisedLevels();
				}

				//CMGroupBuying developer: comment this line because we use custom point, we don't use rule
				//$points    = CUserPoints::_getActionPoint($action, $aid);

				$user = CFactory::getUser($userId);
				$points += $user->getKarmaPoint();
				$user->_points = $points;
				$user->save();
			}
		}
	}

	public static function postActivity($deal, $activityTitle)
	{
		if(CMGroupBuyingHelperJomsocial::checkInstalled())
		{
			require_once(JPATH_ROOT . '/components/com_community/libraries/core.php' );
			$title = $activityTitle;
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);
			$link = '<a href="' . $link . '">' . $deal['name'] . '</a>';
			$title = str_replace('{deal_name}', $link, $title);
			$title = str_replace('{buyer_name}', JFactory::getUser()->name, $title);
			$act = new stdClass();
			$act->cmd = 'wall.write';
			$act->actor = JFactory::getUser()->id;
			$act->target = 0; // no target
			$act->title = JText::_($title);
			$act->content = '';
			$act->app = 'wall';
			$act->cid = 0;
			CFactory::load('libraries', 'activities');
			CActivityStream::add($act);
		}
	}

	public static function newBonusPoints($userId)
	{
		if(CMGroupBuyingHelperJomsocial::checkInstalled() && $userId > 0)
		{
			include_once(JPATH_ROOT . '/components/com_community/libraries/userpoints.php');
			CUserPoints::assignPoint('com_cmgroupbuying.bonus.add', $userId);
		}
	}
}