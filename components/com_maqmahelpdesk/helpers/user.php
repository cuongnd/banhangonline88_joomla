<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskUser
{
	static function IsSupport()
	{
		$database = JFactory::getDbo();
		$user = JFactory::getUser();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$task = JRequest::getCmd('task', '', '', 'string');

		$database->setQuery("SELECT COUNT(*) FROM #__support_permission p, #__support_workgroup w WHERE p.id_user='" . $user->id . "' AND w.id = p.id_workgroup AND w.id=" . $id_workgroup);
		$is_support = $database->loadResult();

		if ($task == '' || !$id_workgroup) {
			$database->setQuery("SELECT COUNT(*) FROM #__support_permission p, #__support_workgroup w WHERE p.id_user='" . $user->id . "' AND w.id = p.id_workgroup");
			$is_support = ($database->loadResult() ? 1 : 0);
		}

		return $is_support;
	}

	static function IsClient($id_user=0)
	{
		$database = JFactory::getDbo();
		$user = JFactory::getUser();

		$sql = "SELECT u.id_client
				FROM #__support_client_users u, #__support_client c
				WHERE u.id_user='" . ($id_user ? $id_user : $user->id) . "' AND c.id=u.id_client";
		$database->setQuery($sql);
		$is_client = $database->loadResult() ? $database->loadResult() : '';

		return $is_client;
	}

	static function IsManager()
	{

	}

	static function IsOnline($id_user)
	{
		$database = JFactory::getDBO();

		$sql = "SELECT userid
				FROM #__session 
				WHERE userid = " . $id_user;
		$database->setQuery($sql);
		$check = $database->loadResult();

		return $check;
	}

	static function GetAvatar($id_user)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();
		$avatar = JURI::root() . 'media/com_maqmahelpdesk/images/avatars/anonymous.png';

		// JomSocial
		if ($supportConfig->use_jomsocial_avatars)
		{
			include_once JPATH_ROOT . '/components/com_community/libraries/core.php';
			$jsuser = CFactory::getUser($id_user);
			$avatar = $jsuser->getAvatar();
		}
		// Community Builder
		elseif ($supportConfig->use_cb_avatars)
		{
			$sql = "SELECT `avatar`
					FROM `#__comprofiler`
					WHERE `avatarapproved`=1
					  AND `user_id`=" . (int) $id_user;
			$database->setQuery($sql);
			$avatar = $database->loadResult();
			if ($avatar != '')
			{
				$folder = is_dir(JPATH_SITE . '/images/comprofiler/') ? 'comprofiler' : 'com_profiler';
				$avatar = JURI::root() . '/images/' . $folder . '/' . $avatar;
			}
		}
		// jomWall
		elseif ($supportConfig->use_jomwall_avatars)
		{
			$params = JComponentHelper::getParams('com_awdwall');
			$template = $params->get('temp', 'blue');
			$sql = "SELECT facebook_id
					FROM #__jconnector_ids
					WHERE user_id = "  . (int) $id_user;
			$database->setQuery($sql);
			$facebook_id = $database->loadResult();
			if ($facebook_id)
			{
				$avatar = 'https://graph.facebook.com/' . $facebook_id . '/picture?type=large';
			}
			else
			{
				$sql = 'SELECT avatar
						FROM #__awd_wall_users
						WHERE user_id = ' . (int) $id_user;
				$database->setQuery($sql);
				$img = $database->loadResult();

				if ($img == null)
				{
					$avatar = JURI::root() . "components/com_awdwall/images/" . $template . "/" . $template . "51.png";
				}
				else
				{
					$avatar = JURI::root() . "images/wallavatar/" . $id_user . "/thumb/tn51" . $img;
				}
			}
		}
		// eShop Suite
		elseif ($supportConfig->use_eshop_suite_avatars)
		{
			$sql = 'SELECT params
					FROM #__eshop_suite_clients
					WHERE j_user_id = ' . (int) $id_user;
			$database->setQuery($sql);
			$params = $database->loadObject();
			if(!empty($params->params))
			{
				$jsonparams = $params->params;
			}
			else
			{
				$jsonparams = '';
			}
			$jsonparams = json_decode($jsonparams);
			if(!empty($jsonparams->avatar))
			{
				$img = $jsonparams->avatar;
			}
			else
			{
				$img = '';
			}
			if ($img == null)
			{
				$avatar = JURI::base() . '/media/com_eshop_suite/images/avatars/man5.png';
			}
			else
			{
				$avatar = $img;
			}
		}
		// MaQma
		else
		{
			$sql = "SELECT `avatar`
					FROM `#__support_users`
					WHERE `id_user`=" . (int) $id_user;
			$database->setQuery($sql);
			$row = $database->loadResult();
			if ($row != '')
			{
				$avatar = $row;
			}
		}

		return $avatar;
	}

	static function GetName($id)
	{
		if (!$id) return;
		$database = JFactory::getDBO();
		$database->setQuery("SELECT `id`, `name` FROM `#__users` WHERE `id`=" . (int) $id);
		$row = $database->loadObject();
		return $row->name;
	}

	static function GetID($email)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT id FROM #__users WHERE email=" . $database->quote($email));
		$row = $database->loadObject();
		return $row->id;
	}

	static function GetIP()
	{
		if (isset($_SERVER))
		{
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			{
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}
			elseif (isset($_SERVER["HTTP_CLIENT_IP"]))
			{
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			}
			else
			{
				$ip = $_SERVER["REMOTE_ADDR"];
			}
		}
		else
		{
			if (getenv('HTTP_X_FORWARDED_FOR'))
			{
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			}
			elseif (getenv('HTTP_CLIENT_IP'))
			{
				$ip = getenv('HTTP_CLIENT_IP');
			}
			else
			{
				$ip = getenv('REMOTE_ADDR');
			}
		}
		return $ip;
	}

	static function GetEmail($id, $output = 1)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT name, email FROM #__users WHERE id=" . (int) $id);
		$row = $database->loadObject();
		if (count($row) > 0)
		{
			if ($output == 1)
			{
				return $row->email;
			}
			elseif ($output == 2)
			{
				return $row->name;
			}
		}
	}

	static function GetMobile( $id )
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT `mobile` FROM `#__support_users` WHERE `id_user`=" . (int) $id);
		return $database->loadResult();
	}
}
