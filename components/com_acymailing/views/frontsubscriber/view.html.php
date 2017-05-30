<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
include(ACYMAILING_BACK.'views'.DS.'subscriber'.DS.'view.html.php');

class FrontsubscriberViewFrontsubscriber extends SubscriberViewSubscriber
{

	var $ctrl='frontsubscriber';

	function display($tpl = null)
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet( ACYMAILING_CSS.'frontendedition.css' );

		JHTML::_('behavior.tooltip');

		global $Itemid;
		$this->assignRef('Itemid',$Itemid);

		parent::display($tpl);
	}

	function listing(){

		if(empty($_POST) && !JRequest::getInt('start') && !JRequest::getInt('limitstart')){
			JRequest::setVar('limitstart',0);
		}

		return parent::listing();
	}
}
