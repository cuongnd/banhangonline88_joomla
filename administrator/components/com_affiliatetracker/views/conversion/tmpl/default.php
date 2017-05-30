<?php // no direct access

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author        Germinal Camps
# copyright       Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites:       http://www.JoomlaThat.com
# Technical Support:  Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

$user = JFactory::getUser();

$payment_status = AffiliatesHelper::getPaymentStatus();
/*
switch($this->payment->towho){
  case "user":
  
    $theusername = $this->payment->theusername ;
    
  break;  
  case "admin":
  
    $theusername = JText::_('ADMINISTRATOR') ;
    
  
  break;
}

switch($this->payment->payment_status){
  case 1:
  
    $themessage = JText::_( 'EMAIL_PAYMENT_SUCCESSFULL' );
  
  break;
  case 2:
  
    $themessage = JText::_( 'EMAIL_PAYMENT_PENDING' );
  
  break;
  case 0:
  
    $themessage = JText::_( 'EMAIL_PAYMENT_UNSUCCESSFULL' );
  
  break;  
  
}
*/
     
?>

<?php //echo JText::sprintf( 'EMAIL_PAYMENT_SALUTATION', $theusername ); ?><br /><br />

<?php //echo $themessage; ?><br /><br />

<strong><?php echo JText::_( 'CONVERSION_DETAILS' ); ?></strong>:<br /><br />


<?php echo JText::_( 'VALUE' ); ?>: <?php echo AffiliatesHelper::format($this->conversion->value); ?><br />
<?php echo JText::_( 'COMISSION' ); ?>: <?php echo AffiliatesHelper::format($this->conversion->comission); ?><br />
<?php echo JText::_( 'DATE_CREATED' ); ?>: <?php if($this->conversion->date_created == "0000-00-00 00:00:00") echo JText::_('NOT_SETTED'); else echo JHTML::_('date', $this->conversion->date_created, JText::_('DATE_FORMAT_LC3')); ?><br />

<br />
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>