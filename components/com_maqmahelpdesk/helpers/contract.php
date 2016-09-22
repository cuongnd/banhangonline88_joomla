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

class HelpdeskContract
{
	static function Get($id, $type = 'u')
	{
		$database = JFactory::getDBO();
		$is_client = ($type=='u' ? HelpdeskUser::IsClient($id) : $id);

		$valid_contract = 0; // False by default
		switch ($type)
		{
			case 'u' :
				$check_sql = "AND u.id_user='" . $id . "'";
				break;
			case 'c' :
				$check_sql = "AND c.id_client='" . $id . "'";
				break;
		}

		// Get client active contract
		$sql = "SELECT c.id, c.id_contract, c.id_client, c.status, c.date_start, c.date_end, c.unit AS unit, c.value, c.actual_value
				FROM #__support_client_users as u,
					 #__support_contract as c
				WHERE u.id_client=c.id_client " . $check_sql . " AND c.status='A'";
		$database->setQuery($sql);
		$contracts = $database->loadObjectList();

		// Process first contract found for the client
		if (count($contracts) == 0)
		{
			// Do not have an active contract so try to activate one
			if (HelpdeskContract::MakeActive($is_client, 0))
			{
				$contract = HelpdeskContract::Get($id, $type);
				$valid_contract = is_object($contract) ? 1 : 0;
			}
			else
			{
				$valid_contract = 0;
			}
		}
		else
		{
			$contract = $contracts[0];
			$valid_contract = 1;
		}
		if (!$valid_contract)
		{
			return false;
		}
		else
		{
			return $contract;
		}
	}

	static function MakeActive($id, $time)
	{
		$database = JFactory::getDBO();

		$database->setQuery("SELECT * FROM #__support_contract WHERE id_client='" . $id . "' AND status='I'");
		$contracts = $database->loadObjectList();

		if (count($contracts) == 0) {
			return 0;
		} else {
			for ($i = 0; $i < count($contracts); $i++)
			{
				$contract = $contracts[$i];
				$today_date = mktime(0, 0, 0, HelpdeskDate::DateOffset("%m"), HelpdeskDate::DateOffset("%d"), HelpdeskDate::DateOffset("%Y"));
				$end_date = mktime(0, 0, 0, substr($contract->date_end, 5, 2), substr($contract->date_end, 8, 2), substr($contract->date_end, 0, 4));
				$start_date = mktime(0, 0, 0, substr($contract->date_start, 5, 2), substr($contract->date_start, 8, 2), substr($contract->date_start, 0, 4));

				if ($end_date >= $today_date && $start_date <= $today_date && $contract->date_end != '0000-00-00' && ($contract->unit == 'Y' || $contract->unit == 'M' || $contract->unit == 'D')) {
					$database->setQuery("UPDATE #__support_contract SET status='A' WHERE id='" . $contract->id . "'");
					$database->query();
					return true;

				} elseif ($end_date >= $today_date && $start_date <= $today_date && $contract->value > $contract->actual_value && ($contract->unit == 'T' || $contract->unit == 'H')) {
					$database->setQuery("UPDATE #__support_contract SET status='A', actual_value=(actual_value+" . ($contract->unit == 'T' ? 0 : $time) . ") WHERE id='" . $contract->id . "'");
					$database->query();
					return true;

				}
			}
		}

		return false;
	}

	static function MakeInactive($id)
	{
		$database = JFactory::getDBO();

		$database->setQuery("SELECT * FROM #__support_contract WHERE id='" . $id . "'");
		$contract = null;
		$contract = $database->loadObject();

		// Contract is turned Inactive
		if ($contract->value <= $contract->actual_value)
		{
			$overtime = ($contract->actual_value - $contract->value);

			// Update the contract
			$sql = "UPDATE #__support_contract
					SET status='I',
					    actual_value=value
					WHERE id=" . (int) $id;
			$database->setQuery($sql);
			$database->query();

			// Save the overtime
			$sql = "UPDATE #__support_client
					SET overtime=$overtime
					WHERE id=" . (int) $contract->id_contract;
			$database->setQuery($sql);
			$database->query();

			return true;
		}
		// Contract maintains Active
		else
		{
			return false;
		}
	}

	static function IsValid($id, $type = 'u')
	{
		$database = JFactory::getDBO();
		$is_client = HelpdeskUser::IsClient();

		$valid_contract = 0; // False by default
		switch ($type) {
			case 'u' :
				$check_sql = "AND u.id_user='" . $id . "'";
				break;
			case 'c' :
				$check_sql = "AND c.id_client='" . $id . "'";
				break;
		}
		// Get client active contract
		$sql = "SELECT c.id, c.id_contract, c.id_client, c.status, c.date_start, c.date_end, c.unit, c.value, c.actual_value
				FROM #__support_client_users as u,
					 #__support_contract as c
				WHERE u.id_client=c.id_client " . $check_sql . " AND c.status='A'";
		$database->setQuery($sql);
		$contracts = $database->loadObjectList();

		// Process each contract found for the client until an active is found
		if (count($contracts) == 0) {
			$valid_contract = 0; // Do not have an active contract
		} else {
			foreach ($contracts as $contract) {
				if (!$valid_contract) {
					$valid_contract = 0;
					switch ($contract->unit) {
						case 'T' : // Contract based on the number of tickets
							$valid_contract = ($contract->value <= $contract->actual_value) ? 0 : 1;
							break;
						case 'H' : // Contract based on the number of labour hours
							$valid_contract =($contract->value <= $contract->actual_value) ? 0 : 1;
							break;
						default	: // Contract based on dates
							$today_timestamp = HelpdeskDate::ParseDate(HelpdeskDate::DateOffset("%Y-%m-%d"), '%Y-%m-%d');
							$start_timestamp = HelpdeskDate::ParseDate($contract->date_start, '%Y-%m-%d');
							$end_timestamp = HelpdeskDate::ParseDate($contract->date_end, '%Y-%m-%d');
							$valid_contract = (($today_timestamp >= $start_timestamp && $end_timestamp > $today_timestamp) ? 1 : 0);
							break;
					}
				}
			}
		}

		if (!$valid_contract) {
			return false;
		} else {
			return true;
		}
	}

	static function ShowProgressbar()
	{
		$id_client = HelpdeskUser::IsClient();
		$is_support = HelpdeskUser::IsSupport();

		$contract = HelpdeskContract::Get($id_client,'c');

		if ( isset($contract->unit) && !$is_support )
		{
			if ($contract->unit != 'H' && $contract->unit != 'T')
			{
				$start = strtotime($contract->date_start);
				$current = strtotime(date("Y-m-d H:i:s")) - $start;
				$end = (strtotime($contract->date_end) - $start);
				if ($end!='')
				{
					$percentage = 100 - (($current * 100) / $end);
					$percentage = ($percentage < 0 ? 0 : $percentage);
				}
				else
				{
					$percentage = 0;
				}
			}else{
				$percentage = (($contract->actual_value * 100) / $contract->value);
			} ?>

			<div class="maqmahelpdesk">
				<div class="progress">
					<div class="bar" style="width:<?php echo number_format($percentage,0);?>%;"><?php echo JText::_("CONTRACT_PROGRESS");?>: <?php echo ($contract->unit != 'H' && $contract->unit != 'T' ? number_format($percentage,0).'%' : HelpdeskDate::ConvertDecimalsToHoursMinutes($contract->actual_value));?></div>
				</div>
			</div><?php
		}
	}
}
