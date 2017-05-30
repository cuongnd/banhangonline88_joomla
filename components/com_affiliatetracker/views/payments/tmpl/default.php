<?php // no direct access

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

$itemid = $this->params->get('itemid');
if($itemid != "") $itemid = "&Itemid=" . $itemid;

?>

<div class="page-header">
  <h1><?php echo JText::_('AFFILIATE_TITLE'); ?></h1>
</div>
<?php echo AffiliateHelper::nav_tabs(); ?>

<?php 
      $intro = new stdClass();
     
      $intro->text = $this->params->get('textpayments');
      
      $dispatcher = JDispatcher::getInstance();
      $plug_params = new JRegistry('');
      
      JPluginHelper::importPlugin('content');
      $results = $dispatcher->trigger('onContentPrepare', array ('com_affiliatetracker.accounts', &$intro, &$plug_params, 0));
      
      echo $intro->text; 
      ?>
<div class="navbar">
<div class="navbar-inner">
<form action="<?php echo JRoute::_("index.php?option=com_affiliatetracker&view=payments".$itemid); ?>" method="get" name="adminForm"  class="navbar-form pull-left form-horizontal">


  <?php echo JHTML::calendar($this->lists['date_in'], "date_in", "date_in", "%Y-%m-%d", array("class" => "input-small", "placeholder" => JText::_( 'FROM' ))); ?>
 
  <?php echo JHTML::calendar($this->lists['date_out'], "date_out", "date_out", "%Y-%m-%d", array("class" => "input-small", "placeholder" => JText::_( 'TO' ))); ?>
 
    
    <button type="submit" class="btn btn-inverse" data-original-title="<?php echo JText::_('SEARCH'); ?>"><?php echo JText::_('FILTER_RESULTS'); ?></button>

</div></div>

<div class="row-fluid at_module_wrapper" >
  <?php 		
$modules = JModuleHelper::getModules("at_payments");
$document	=JFactory::getDocument();
$renderer	= $document->loadRenderer('module');
$attribs 	= array();
$attribs['style'] = 'xhtml';
foreach ( @$modules as $mod ) 
{
	echo $renderer->render($mod, $attribs);
}
?>
</div>

<?php echo AffiliateHelper::time_options(); ?>
  
  <div class="at_totals">
<div class="row-fluid ">
  <div class="span12 text-center">
    <div class="big_number"><?php echo AffiliateHelper::format($this->payments); ?></div>
    <span class="label label-success"><?php echo JText::_('AMOUNT_RECEIVED'); ?></span><br />
    <span class="muted"><?php echo JText::_('LAST_'.$this->timespan.'_DAYS'); ?></span> </div>
</div>
</div>
<br  />
<table class="table table-striped logs_table">
  <thead>
    <tr>
      
      <th class=""> <?php echo JHTML::_( 'grid.sort', 'DATETIME', 'pa.payment_datetime', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JText::_('PAYMENT_METHOD'); ?> </th>
      <th class="hidden-phone"> <?php echo JText::_('PAYMENT_STATUS'); ?> </th>
      <th class=" width55 text-right"> <?php echo JHTML::_( 'grid.sort', 'AMOUNT', 'pa.payment_amount', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
    </tr>
  </thead>
  <?php
	$k = 0;
	$total_payments = 0 ;
	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link_account 		= JRoute::_( 'index.php?option=com_affiliatetracker&view=logs&account_id='. $row->id .$itemid);
		
    $total_payments += $row->payment_amount ;
		
		?>
  <tr class="<?php echo "row$k"; ?>">
    <td class=""><?php echo JHTML::_('date', $row->payment_datetime, JText::_('DATE_FORMAT_CUSTOM')); ?></td>
    <td class="hidden-phone"><?php echo JText::_($row->payment_type); ?></td>
    <td class="hidden-phone"><?php echo AffiliateHelper::payment_status( $row ); ?></td>
    <td align="right" class="text-right"><?php echo AffiliateHelper::format($row->payment_amount); ?></td>
  </tr>
  <?php
		$k = 1 - $k;
	}
	?>
  <tfoot>
    <tr class="totals">
   
      <td  class=""></td>
      <td  class="hidden-phone"></td>
      <td  class="hidden-phone"></td>
      <td align="right" class="text-right"><?php echo AffiliateHelper::format($total_payments); ?></td>
    </tr>
    
  </tfoot>
</table>
<br />
<?php 		
$modules = JModuleHelper::getModules("at_bottom");
$document	=JFactory::getDocument();
$renderer	= $document->loadRenderer('module');
$attribs 	= array();
$attribs['style'] = 'xhtml';
foreach ( @$modules as $mod ) 
{
	echo $renderer->render($mod, $attribs);
}
?>
<input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="view" value="payments" />
  <input type="hidden" name="Itemid" value="<?php echo $this->params->get('itemid'); ?>" />
  <input type="hidden" name="filter_order" value="<?php echo JRequest::getVar('filter_order'); ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo JRequest::getVar('filter_order_Dir'); ?>" />
  <div class="pagination" align="center"> <?php echo $this->pagination->getListFooter(); ?> </div>
</form>
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>
