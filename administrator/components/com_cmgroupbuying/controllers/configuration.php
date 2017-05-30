<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class CMGroupBuyingControllerConfiguration extends JControllerLegacy
{
	public function apply()
	{
		$jinput = JFactory::getApplication()->input;
		$data = $jinput->post->get('jform', array(), 'array');
		$this->save($data);
		$this->setRedirect('index.php?option=com_cmgroupbuying&view=configuration');
	}

	public function savenclose()
	{
		$jinput = JFactory::getApplication()->input;
		$data = $jinput->post->get('jform', array(), 'array');
		$this->save($data);
		$this->setRedirect('index.php?option=com_cmgroupbuying');
	}

	public function save($data)
	{
		$app = JFactory::getApplication();
		$model = JModelLegacy::getInstance('Configuration','CMGroupBuyingModel');

		$data['sh404sef_deal_alias'] = JApplication::stringURLSafe($data['sh404sef_deal_alias']);
		$data['sh404sef_category_alias'] = JApplication::stringURLSafe($data['sh404sef_category_alias']);
		$data['sh404sef_partner_alias'] = JApplication::stringURLSafe($data['sh404sef_partner_alias']);
		

		if($model->save($data))
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_SAVED'), 'message');
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_SAVE_FAILED'), 'error');
		}
	}
}