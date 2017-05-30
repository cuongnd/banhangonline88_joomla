<?php // no direct access

/*------------------------------------------------------------------------
# com_invoices - Invoices for Joomla
# ------------------------------------------------------------------------
# author        Germinal Camps
# copyright       Copyright (C) 2012 JoomlaFinances.com. All Rights Reserved.
# @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites:       http://www.JoomlaFinances.com
# Technical Support:  Forum - http://www.JoomlaFinances.com/forum
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

$user = JFactory::getUser();

switch($this->account->towho){

  case "user":
  
    $theusername = $this->account->name ;
    
  break;  
  
  case "admin":
  
    $theusername = JText::_('ADMINISTRATOR') ;
    
  break;
}

switch($this->account->towho){

case "user": 
?>
<?php echo JText::sprintf( 'EMAIL_PAYMENT_SALUTATION', $theusername ); ?><br /><br />

<?php echo JText::_( 'NEW_ACCOUNT_APPROVED' ); ?><br /><br />

<strong><?php echo JText::_( 'ACCOUNT_DETAILS' ); ?></strong>:<br /><br />

<?php echo JText::_( 'ACCOUNT_NAME' ); ?>: <?php echo $this->account->account_name; ?><br />
<?php echo JText::_( 'CONTACT_NAME' ); ?>: <?php echo $this->account->name; ?><br />
<?php echo JText::_( 'CONTACT_EMAIL' ); ?>: <?php echo $this->account->email; ?><br />
<?php echo JText::_( 'CONTACT_USERNAME' ); ?>: <?php echo $this->account->username; ?><br />

<?php echo JText::_( 'YOUR_LINK' ); ?>: <?php echo AffiliateHelper::get_account_link($this->account->id); ?><br />

<br />
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>
<?php break;
 
case "admin": 
?>
<?php echo JText::sprintf( 'EMAIL_PAYMENT_SALUTATION', $theusername ); ?><br /><br />

<?php echo JText::_( 'NEW_ACCOUNT_EMAIL' ); ?><br /><br />

<strong><?php echo JText::_( 'ACCOUNT_DETAILS' ); ?></strong>:<br /><br />

<?php echo JText::_( 'ACCOUNT_NAME' ); ?>: <?php echo $this->account->account_name; ?><br />
<?php echo JText::_( 'CONTACT_NAME' ); ?>: <?php echo $this->account->name; ?><br />
<?php echo JText::_( 'CONTACT_EMAIL' ); ?>: <?php echo $this->account->email; ?><br />
<?php echo JText::_( 'CONTACT_USERNAME' ); ?>: <?php echo $this->account->username; ?><br />

<br />
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>
<?php break;
}
?>