<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class CMGroupBuyingControllerPayments extends JControllerAdmin
{
	protected $text_prefix = 'COM_CMGROUPBUYING_PAYMENT';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Payment', $prefix = 'CMGroupBuyingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}