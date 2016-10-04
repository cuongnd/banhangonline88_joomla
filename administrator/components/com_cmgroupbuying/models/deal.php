<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelDeal extends JModelAdmin
{
	public function getTable($type = 'Deal', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		// Get the form.
		$form = $this->loadForm('com_cmgroupbuying.deal', 'deal', array('control' => 'jform', 'load_data' => $loadData));

		if(empty($form))
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
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.deal.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		$jinput = JFactory::getApplication()->input;
		$autoPost = $jinput->post->get('autopost', 0, 'integer');
		$jform = $jinput->post->get('jform', array(), 'array');

		// Initialise variables
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew = true;

		// Alter the title for save as copy
		if($jinput->get('task', '', 'word') == 'save2copy')
		{
			list($title, $alias) = $this->cmGenerateNewTitle($data['alias'], $data['name']);
			$data['name'] = $title;
			$data['alias'] = $alias;
			$data['published'] = 0;
			$data['tipped'] = 0;
			$data['voided'] = 0;
			$data['tipped_date'] = '';
			$data['featured'] = 0;
		}

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
		catch (Exception $e)
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
		$data2 = $jform;

		// Now save its location
		$locationIdList = $data2['location_id'];

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

			if($db->getErrorNum())
			{
				JError::raiseWarning(500, $db->getErrorMsg());
				return false;
			}
		}

		// Now save its options
		for($i = 1; $i <= 10; $i++)
		{
			if($data2['option_name_' . $i] != '' && $data2['option_original_price_' . $i] != '' && $data2['option_price_' . $i] != '')
			{
				$name = htmlspecialchars($data2['option_name_' . $i], ENT_QUOTES);
				$originalPrice = $data2['option_original_price_' . $i];
				$price = $data2['option_price_' . $i];
				$advancePrice = $data2['option_advance_price_' . $i];

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
								$db->quoteName('advance_price')
							)
						)
						->values(
							$db->quote($dealId) . ', ' .
							$db->quote($i) . ', ' .
							$db->quote($name) . ', ' .
							$db->quote($originalPrice) . ', ' .
							$db->quote($price) . ', ' .
							$db->quote($advancePrice)
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
						->set($db->quoteName('advance_price')	. ' = ' . $db->quote($advancePrice))
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
					->where($db->quoteName('deal_id')	. ' = ' . $db->quote($dealId))
					->where($db->quoteName('option_id')	. ' = ' . $db->quote($i));

				$db->setQuery($query);
				$db->execute();
			}
		}

		// Remove "Featured" status of the previous featured deal
		// if this deal is set to be a featured deal
		if($data2['featured'] == 1)
		{
			$this->featured($dealId, 1);
		}

		// Autopost to social networks
		if($autoPost)
		{
			$data['deal_id'] = $dealId;
			jimport('joomla.plugin.helper');
			JPluginHelper::importPlugin('cmgroupbuying');
			JFactory::getApplication()->triggerEvent('onCMGroupBuyingAfterSave', array($data));
		}

		return true;
	}

	public function getDealById($dealId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$deal = $db->loadAssoc();

		if($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		return $deal;
	}

	public function getVoidedStatus($dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('voided'))
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$status = $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		return $status;
	}

	public function setVoidedStatus($dealId, $status)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__cmgroupbuying_deals'))
			->set($db->quoteName('voided')	. ' = ' . $db->quote($status))
			->where($db->quoteName('id')	. ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		return true;
	}

	public function getTippedStatus($dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('tipped'))
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$status = $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		return $status;
	}

	public function setTippedStatus($dealId, $status)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__cmgroupbuying_deals'));
		$query->set($db->quoteName('tipped') . ' = ' . $db->quote($status));

		if($status == 1)
		{
			$query->set($db->quoteName('tipped_date') . ' = ' . $db->quote(CMGroupBuyingHelperDateTime::getCurrentDateTime()));
		}

		$query->where($db->quoteName('id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		return true;
	}

	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if(empty($pks))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable('Deal', 'CMGroupBuyingTable');

		try
		{
			$db = $this->getDbo();

			if($value == 1)
			{
				$db->setQuery(
					'UPDATE #__cmgroupbuying_deals AS a' .
					' SET a.featured = 0'
				);

				if(!$db->execute())
				{
					throw new Exception($db->getErrorMsg());
				}
			}

			$db->setQuery(
				'UPDATE #__cmgroupbuying_deals AS a' .
				' SET a.featured = '.(int) $value.
				' WHERE a.id IN ('.implode(',', $pks).')'
			);

			if(!$db->execute())
			{
				throw new Exception($db->getErrorMsg());
			}

		}
		catch(Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$table->reorder();

		$this->cleanCache();

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

	public function delete(&$pks)
	{
		// Initialise variables.
		// $dispatcher = JDispatcher::getInstance();
		$pks = (array) $pks;
		$table = $this->getTable();

		// Iterate the items to delete each one.
		foreach($pks as $i => $pk)
		{
			if($table->load($pk))
			{
				if($this->canDelete($table))
				{
					// $context = $this->option . '.' . $this->name;

					// Trigger the onContentBeforeDelete event.
					/* $result = $dispatcher->trigger($this->event_before_delete, array($context, $table));
					if(in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					} */

					if(!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
					else // Delete deal successfully
					{
						JModelAdmin::getInstance('DealOption', 'CMGroupBuyingModel')->delete($pks);
						JModelAdmin::getInstance('DealLocation', 'CMGroupBuyingModel')->delete($pks);
					}

					// Trigger the onContentAfterDelete event.
					// $dispatcher->trigger($this->event_after_delete, array($context, $table));

				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();

					if($error)
					{
						JError::raiseWarning(500, $error);
						return false;
					}
					else
					{
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
						return false;
					}
				}

			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	public function getApprovedStatus($dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('approved'))
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$status = $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		return $status;
	}

	public function setApprovedStatus($dealId, $status)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__cmgroupbuying_deals'))
			->set($db->quoteName('approved')	. ' = ' . $db->quote($status))
			->set($db->quoteName('published')	. ' = ' . $db->quote($status))
			->where($db->quoteName('id')		. ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return false;
		}

		return true;
	}
}