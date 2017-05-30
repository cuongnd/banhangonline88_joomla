<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelMailTemplate extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getMailTemplateByName($templateName)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_mail_templates WHERE name = '$templateName'";
		$db->setQuery($query);
		$template = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $template;
	}
}