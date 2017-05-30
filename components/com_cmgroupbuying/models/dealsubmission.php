<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelDealSubmission extends JModelAdmin
{
	public function getTable($type = 'Deal', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		// Get the form.
		$form = $this->loadForm('com_cmgroupbuying.dealsubmission', 'dealsubmission', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		return $item;
	}

	protected function loadFormData()
	{
		$layout = JFactory::getApplication()->input->get('layout', '', 'word');

		if($layout != "edit")
		{
			$data = $this->getItem();
		}
		else
		{
			// Check the session for previously entered form data.
			$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.dealsubmission.data', array());

			if (empty($data))
			{
				$data = $this->getItem();
			}
		}

		return $data;
	}

	public function save($data)
	{
		$user = JFactory::getUser();

		// We do not check for unpublished partner here. Checking is done in controller and view already.
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerByUserId($user->id);

		$data['partner_id'] = $partner['id'];
		$data['approved'] = 0;
		$data['featured'] = 0;
		$data['published'] = 0;
		$data['tipped_date'] = '';
		$data['tipped'] = 0;
		$data['voided']  = 0;

		// Initialise variables;
		$table = $this->getTable();
		$key  = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew  = true;

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

			// Bind the data.
			if(!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Check the data.
			if(!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if(!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Clean the cache.
			$this->cleanCache();
		}
		catch(Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if(isset($table->$pkName))
		{
			$this->setState($this->getName().'.id', $table->$pkName);
		}

		$this->setState($this->getName().'.new', $isNew);

		// Save the deal succesfully
		$dealId = $table->id;
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Now save its location
		$locationIdList = $data['location_id'];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->delete($db->quoteName('#__cmgroupbuying_deal_location'))
			->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$db->execute();

		foreach($locationIdList as $locationId)
		{
			$query->clear()
				->insert($db->quoteName('#__cmgroupbuying_deal_location'))
				->columns(
					array(
						$db->quoteName('deal_id'),
						$db->quoteName('location_id')
					)
				)
				->values($db->quote($dealId) . ', ' . $db->quote($locationId));

			$db->setQuery($query);
			$db->execute();
		}

		// Now save its options
		for($i = 1; $i <= 10; $i++)
		{
			if($data['option_name_' . $i] != '' && $data['option_original_price_' . $i] != '' && $data['option_price_' . $i] != '')
			{
				$name = htmlspecialchars($data['option_name_' . $i], ENT_QUOTES);
				$originalPrice = $data['option_original_price_' . $i];
				$price = $data['option_price_' . $i];

				$query->clear()
					->select($db->quoteName('id'))
					->from($db->quoteName('#__cmgroupbuying_deal_option'))
					->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId))
					->where($db->quoteName('option_id') . ' = ' . $db->quote($i));

				$db->setQuery($query);
				$optionId = $db->loadResult();

				if(empty($optionId))
				{
					$query->clear()
						->insert($db->quoteName('#__cmgroupbuying_deal_option'))
						->columns(
							array(
								$db->quoteName('deal_id'),
								$db->quoteName('option_id'),
								$db->quoteName('name'),
								$db->quoteName('original_price'),
								$db->quoteName('price'),
							)
						)
						->values(
							$db->quote($dealId) . ', ' .
							$db->quote($i) . ', ' .
							$db->quote($name) . ', ' .
							$db->quote($originalPrice) . ', ' .
							$db->quote($price)
						);

					$db->setQuery($query);
					$db->execute();

					if($db->getErrorNum())
					{
						JError::raiseWarning(500, $db->getErrorMsg());
						return false;
					}
				}
				else
				{
					$query->clear()
						->update($db->quoteName('#__cmgroupbuying_deal_option'))
						->set($db->quoteName('name')			. ' = ' . $db->quote($name))
						->set($db->quoteName('original_price')	. ' = ' . $db->quote($originalPrice))
						->set($db->quoteName('price')			. ' = ' . $db->quote($price))
						->where($db->quoteName('id')			. ' = ' . $db->quote($optionId));

					$db->setQuery($query);
					$db->execute();

					if($db->getErrorNum())
					{
						JError::raiseWarning(500, $db->getErrorMsg());
						return false;
					}
				}
			}
			else
			{
				$query->clear()
					->delete($db->quoteName('#__cmgroupbuying_deal_option'))
					->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId))
					->where($db->quoteName('option_id') . ' = ' . $db->quote($i));
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}

	protected function cmGenerateNewTitle($alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while($table->load(array('alias' => $alias)))
		{
			$title = JString::increment($title);
			$alias = JString::increment($alias, 'dash');
		}

		return array($title, $alias);
	}
}