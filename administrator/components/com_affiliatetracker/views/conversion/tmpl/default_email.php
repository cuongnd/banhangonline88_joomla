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

$payment_status = AffiliateHelper::getPaymentStatus();

switch($this->conversion->towho){
  case "user":
  
    $theusername = $this->conversion->owner_name ;
    
  break;  
  case "admin":
  
    $theusername = JText::_('ADMINISTRATOR') ;
    
  
  break;
}
/*
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

switch($this->conversion->towho){
  case "user": 
?>
<?php echo JText::sprintf( 'EMAIL_PAYMENT_SALUTATION', $theusername ); ?><br /><br />

<?php echo JText::_( 'NEW_CONVERSION_APPROVED_EMAIL' ); ?><br /><br />

<strong><?php echo JText::_( 'CONVERSION_DETAILS' ); ?></strong>:<br /><br />


<?php echo JText::_( 'VALUE' ); ?>: <?php echo AffiliateHelper::format($this->conversion->value); ?><br />
<?php echo JText::_( 'COMISSION' ); ?>: <?php echo AffiliateHelper::format($this->conversion->comission); ?><br />
<?php echo JText::_( 'DATE_CREATED' ); ?>: <?php if($this->conversion->date_created == "0000-00-00 00:00:00") echo JText::_('NOT_SETTED'); else echo JHTML::_('date', $this->conversion->date_created, JText::_('DATE_FORMAT_LC3')); ?><br />

<br />
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>
<?php break;
case "admin": 
?>
<?php echo JText::sprintf( 'EMAIL_PAYMENT_SALUTATION', $theusername ); ?><br /><br />

<?php 
if($this->conversion->approved) echo JText::_( 'NEW_CONVERSION_APPROVED_EMAIL' );
else  echo JText::_( 'NEW_CONVERSION_PENDING_EMAIL' ); ?><br /><br />

<strong><?php echo JText::_( 'CONVERSION_DETAILS' ); ?></strong>:<br /><br />

<?php echo JText::_( 'VALUE' ); ?>: <?php echo AffiliateHelper::format($this->conversion->value); ?><br />
<?php echo JText::_( 'COMISSION' ); ?>: <?php echo AffiliateHelper::format($this->conversion->comission); ?><br />
<?php echo JText::_( 'DATE_CREATED' ); ?>: <?php if($this->conversion->date_created == "0000-00-00 00:00:00") echo JText::_('NOT_SETTED'); else echo JHTML::_('date', $this->conversion->date_created, JText::_('DATE_FORMAT_LC3')); ?><br />
<?php echo JText::_( 'NAME' ); ?>: <?php echo $this->conversion->name; ?><br />
<?php echo JText::_( 'ITEM' ); ?>: <?php echo $this->conversion->extended_name; ?><br />

<br />
<strong><?php echo JText::_( 'ACCOUNT_DETAILS' ); ?></strong>:<br /><br />

<?php echo JText::_( 'ACCOUNT_NAME' ); ?>: <?php echo $this->conversion->account_name; ?><br />
<?php echo JText::_( 'CONTACT_NAME' ); ?>: <?php echo $this->conversion->owner_name; ?><br />
<?php echo JText::_( 'CONTACT_EMAIL' ); ?>: <?php echo $this->conversion->email; ?><br />
<?php echo JText::_( 'CONTACT_USERNAME' ); ?>: <?php echo $this->conversion->username; ?><br />

<br />

<strong><?php echo JText::_( 'ACTOR_DETAILS' ); ?></strong>:<br /><br />

<?php echo JText::_( 'ACTOR_NAME' ); ?>: <?php echo $this->conversion->actor_name; ?><br />
<?php echo JText::_( 'ACTOR_EMAIL' ); ?>: <?php echo $this->conversion->actor_email; ?><br />
<?php echo JText::_( 'ACTOR_USERNAME' ); ?>: <?php echo $this->conversion->actor_username; ?><br />

<br />
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>
<?php break;
}
?>