<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	12 March 2012
 * @file name	:	helpers/select.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
defined('_JEXEC') or die('Restricted access');

class ProjectHelper {
	function getProjectDetails($project_id){
		$db = JFactory::getDbo();
		$query = "SELECT p.* FROM #__jblance_project p".
				 " WHERE p.id=".$db->quote($project_id);
		$db->setQuery($query);
		$project = $db->loadObject();
		return $project;
	}
	
	function averageBidAmt($project_id){
		$db = JFactory::getDbo();
		$query = "SELECT AVG(amount) FROM #__jblance_bid WHERE project_id=".$db->quote($project_id);
		$db->setQuery($query);
		$avg = $db->loadResult();
		if($avg)
			return $avg;
		else
			return 0;
	}
	
	function getBidAmt($project_id){
		$db = JFactory::getDbo();
		$query = "SELECT b.amount bidamount FROM #__jblance_project p ".
				 "INNER JOIN #__jblance_bid b ON p.id=b.project_id ".
				 "WHERE p.id=".$db->quote($project_id)." AND b.status='COM_JBLANCE_ACCEPTED'";
		$db->setQuery($query);
		$bidamt = $db->loadResult();
		return $bidamt;
	}
	
	/**
	 * This function calculates the project commission fee for the given user and bid amount from amt & perc, whichever is higher
	 * In case of Hourly projects, project commission shall be calculate only from percent but not from fixed fee (since v1.2.8).
	 * 
	 * @param integer $user_id
	 * @param float $bid_amount
	 * @param string $user_type
	 * 
	 * @return float $project_fee calculated project commission
	 */
	function calculateProjectFee($user_id, $bid_amount, $user_type, $project_type = 'COM_JBLANCE_FIXED'){
		
		$lastPlan = JblanceHelper::whichPlan($user_id);		//get the current active plan of the publisher/buyer
		
		if($user_type == 'freelancer'){
			$projFeeAmt = $lastPlan->flFeeAmtPerProject;
			$projFeePer = $lastPlan->flFeePercentPerProject;
		}
		elseif($user_type == 'buyer'){
			$projFeeAmt = $lastPlan->buyFeeAmtPerProject;
			$projFeePer = $lastPlan->buyFeePercentPerProject;
		}
		
		//calculate the project fee from freelancer, from amt & perc, whichever is higher
		$fee_per = round((($projFeePer /100) * $bid_amount), 2);
		if($project_type == 'COM_JBLANCE_FIXED'){
			if($fee_per >= $projFeeAmt)
				$project_fee = $fee_per;
			else
				$project_fee = $projFeeAmt;
		}
		elseif($project_type == 'COM_JBLANCE_HOURLY')
			$project_fee = $fee_per;
		
		return $project_fee;
	}
	
	/**
	 * Check if the user has bid for the project.
	 * @param int $project_id
	 * @param int $user_id
	 * @return boolean
	 */
	function hasBid($project_id, $user_id){
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__jblance_bid b WHERE b.user_id=$user_id AND b.project_id=$project_id";
		$db->setQuery($query);
		$bid = $db->loadObject();
		if(count($bid))
			return 1;
		else
			return 0;
	}
	
 	/**
 	 * This method returns the bid info from the project id and user has bid
 	 * @param int $pid
 	 * @param int $userid
 	 * @return object $bidInfo bid info
 	 */
 	function getBidInfo($pid, $userid){
 		$db = JFactory::getDbo();
 		$query = "SELECT id AS bidid, amount AS bidamount, status, attachment, p_status, p_percent FROM #__jblance_bid ".
 				 "WHERE project_id = ".$db->quote($pid)." AND user_id =".$db->quote($userid);
 		$db->setQuery($query);
 		$bidInfo = $db->loadObject();
 		return $bidInfo;
 	}
}