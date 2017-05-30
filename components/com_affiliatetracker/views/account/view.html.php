<?php

/*------------------------------------------------------------------------
# com_invoices - Invoices for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2012 JoomlaFinances.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaFinances.com
# Technical Support:	Forum - http://www.JoomlaFinances.com/forum
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

jimport( 'joomla.application.component.view');

class AffiliateViewAccount extends JViewLegacy
{

	public $_path = array(
		'template' => array()
	);

	function display($tpl = null)
	{
		$params = JComponentHelper::getParams( 'com_affiliatetracker' );

		//Check if the user can request more accounts
		$user = JFactory::getUser();
		$id = JRequest::getVar('id');
		if (!$id) {
			if (AffiliateHelper::maxNumAccountsReached($user->id)) {

				$itemid = $params->get('itemid');
				if($itemid != "") $itemid = "&Itemid=" . $itemid;
				$link = JRoute::_('index.php?option=com_affiliatetracker&view=accounts' . $itemid);
				$msg = JText::_('MAX_ACCOUNTS_REACHED');
				$type = "warning";

				$app = JFactory::getApplication();
				$app->redirect($link, $msg, $type);
				return false;
			}
		}

		$mainframe = JFactory::getApplication();
		$uri	= JFactory::getURI();

		$pathway	= $mainframe->getPathway();
		$document	= JFactory::getDocument();

		$account		=  $this->get('Data');

		$this->assignRef('account',		$account);
		$this->assignRef('params',		$params);

		$document->addStyleSheet('components/com_affiliatetracker/assets/styles.css');
		$document->addScript('components/com_affiliatetracker/assets/account.js');

		parent::display($tpl);
	}


}
?>
