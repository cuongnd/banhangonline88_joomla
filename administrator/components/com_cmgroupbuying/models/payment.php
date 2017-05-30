<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelPayment extends JModelAdmin
{
	public function getTable($type = 'Payment', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');
		// Get the form.
		$form = $this->loadForm('com_cmgroupbuying.payment', 'payment', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.payment.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		// Initialise variables;
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew = true;

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

		// Save the payment method succesfully
		// Now save its configuration
		if($data['id'] > 0)
		{
			$data = $_POST['jform'];
			$folderName = $data['folder'];
			$configuration = $_POST['payment_configuration'];
			$configuration = str_replace("\\", "", $configuration);
			$configurationFile = "../components/com_cmgroupbuying/payments/" . $folderName . "/configuration.php";
			$fp = fopen($configurationFile, "w");
			fputs($fp, $configuration, strlen($configuration));
			fclose ($fp);
		}
		return true;
	}

	public function getPaymentById($paymentId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_payments WHERE id = " . $paymentId;
		$db->setQuery($query);
		$payment = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $payment;
	}
}