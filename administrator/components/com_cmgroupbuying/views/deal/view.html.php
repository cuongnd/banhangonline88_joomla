<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewDeal extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$app = JFactory::getApplication();
		$context = 'com_cmgroupbuying.edit.deal';
		$data = $app->getUserState($context . '.data');
		$values = (array) $app->getUserState($context . '.id');

		if(empty($this->item->id) && !isset($data['coupon_elements']) && $data['coupon_elements'] == '')
		{
			$configuration = JModelLegacy::getInstance('Configuration',
					'CMGroupBuyingModel')->getConfiguration('coupon_background, coupon_elements');
			$this->form->setValue('coupon_path', null, $configuration['coupon_background']);
			$this->form->setValue('coupon_elements', null, $configuration['coupon_elements']);
		}

		// Get the deal's options when editting deal
		$optionsOfDeal = array();

		if ($this->item->id != 0)
		{
			$optionsOfDeal = JModelLegacy::getInstance('dealoption','CMGroupBuyingModel')->getOptions($this->item->id);
		}
		else
		{
			for($i=1; $i<=10; $i++)
			{
				$option = array(
					"deal_id" => 0,
					"option_id" => $i,
					"name" => $data["option_name_" . $i],
					"original_price" => $data["option_original_price_" . $i],
					"price" => $data["option_price_" . $i],
					"advance_price" => $data["option_advance_price_" . $i],
				);
				$optionsOfDeal[$i] = $option;
			}
		}

		for($i = 1; $i<= 10; $i++)
		{
			if(!isset($optionsOfDeal[$i]['name']))
			{
				$optionsOfDeal[$i]['name'] = '';
			}

			if(!isset($optionsOfDeal[$i]['original_price']))
			{
				$optionsOfDeal[$i]['original_price'] = '';
			}

			if(!isset($optionsOfDeal[$i]['price']))
			{
				$optionsOfDeal[$i]['price'] = '';
			}

			if(!isset($optionsOfDeal[$i]['advance_price']))
			{
				$optionsOfDeal[$i]['advance_price'] = '';
			}
		}

		$this->assignRef('optionsOfDeal', $optionsOfDeal);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		if ($this->item->id == 0)
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_DEAL_CREATE_DEAL'), 'deal.png');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_DEAL_EDIT_DEAL'), 'deal.png');
		}

		JToolBarHelper::apply('deal.apply');
		JToolBarHelper::save('deal.save');
		JToolBarHelper::save2new('deal.save2new');
		JToolBarHelper::save2copy('deal.save2copy');
		JToolBarHelper::cancel('deal.cancel');
	}
}
