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

class HelpdeskDigistore
{
	static function License()
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$is_client = HelpdeskUser::IsClient();

		// Check if there are valid licenses
		$sql = "SELECT COUNT(*)
				FROM `#__digistore_licenses` AS l 
					 INNER JOIN `#__digistore_products` AS p ON p.`id`=l.`productid`
				WHERE (l.`userid`=" . (int) $user->id . " OR l.`userid` IN (SELECT c.`id_user` FROM `#__support_client_users` AS c WHERE c.`id_client`=" . (int) $is_client . "))
				  AND (l.`expires`>=NOW() OR l.`expires` IS NULL)";
		$database->setQuery($sql);
		$check = $database->loadResult();

		return $check;
	}

	static function Domain()
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$is_client = HelpdeskUser::IsClient();

		// Domains integration is set NO so returns true
		if (!$supportConfig->digistore_domains)
		{
			return true;
		}

		// Check if there are valid licenses with domain filled
		$sql = "SELECT COUNT(*)
				FROM `#__digistore_licenses` AS l 
					 INNER JOIN `#__digistore_products` AS p ON p.`id`=l.`productid`
				WHERE (l.`userid`=" . (int) $user->id . " OR l.`userid` IN (SELECT c.`id_user` FROM `#__support_client_users` AS c WHERE c.`id_client`=" . (int) $is_client . "))
				  AND (l.`expires`>=NOW() OR l.`expires` IS NULL) 
				  AND l.`domain`!=''";
		$database->setQuery($sql);
		$check = $database->loadResult();

		return $check;
	}

	static function Validation()
	{
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$workgroupSettings = HelpdeskDepartment::GetSettings();
		$is_support = HelpdeskUser::IsSupport();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$Itemid = JRequest::getInt('Itemid', 0);
		$failed = false;

		if (!$is_support && $supportConfig->integrate_digistore && $workgroupSettings->digistore)
		{
			if (!$user->id)
			{
				$url = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=workgroup_view");
				$mainframe->redirect($url);
			}
			if (!self::Domain())
			{
				$failed = true;
				$message = JText::_('digistore_no_domains');
			}
			if (!self::License())
			{
				$failed = true;
				$message = JText::_('digistore_no_licenses');
			}
			if ($failed)
			{
				?>
				<div id="mqmMessage">
					<img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/alert.png"
						 align="absmiddle"/> <?php echo $message;?>
				</div><?php
			}
		}

		return $failed;
	}
}
