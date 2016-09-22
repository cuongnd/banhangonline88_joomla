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

class MaQmaHelpdeskTableClient extends JTable
{
	var $id = null;
	var $date_created = null;
	var $clientname = null;
	var $address = null;
	var $zipcode = null;
	var $city = null;
	var $state = null;
	var $country = null;
	var $phone = null;
	var $fax = null;
	var $mobile = null;
	var $email = null;
	var $contactname = null;
	var $website = null;
	var $description = null;
	var $client_mail_notify = null;
	var $travel_time = 0;
	var $rate = 0.00;
	var $manager = 0;
	var $block = 0;
	var $logo = null;
	var $clientid = null;
	var $taxnumber = null;
	var $approval = 0;
	var $slug = null;
	var $autoassign = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_client', 'id', $_db);
	}
}