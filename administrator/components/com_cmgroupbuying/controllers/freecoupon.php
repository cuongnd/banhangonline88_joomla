<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class CMGroupBuyingControllerFreeCoupon extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function save($data = null, $urlVar = null)
	{
		if(parent::save())
		{
			return true;
		}

		return false;
	}
}