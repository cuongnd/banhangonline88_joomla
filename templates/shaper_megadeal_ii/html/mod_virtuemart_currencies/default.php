<?php 
// no direct access    
defined('_JEXEC') or die('Restricted access');

// Load the method jquery script.
JHtml::_('jquery.framework');

if(!function_exists('getSPVmCurrency'))
{

	function getSPVmCurrency($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.currency_code_3', 'a.currency_symbol'));
		$query->from($db->quoteName('#__virtuemart_currencies', 'a'));
		$query->where($db->quoteName('a.virtuemart_currency_id')." = ".$db->quote($id));
		$db->setQuery($query);

		return $db->loadObject();
	}	
}

foreach ($currencies as $key => $currency)
{
	$cur = getSPVmCurrency($currency->virtuemart_currency_id);
	$currency->short_name = $cur->currency_code_3 . ' ' . $cur->currency_symbol;
}
?>

<!-- Currency Selector Module -->
<div class="vm_currency_module">
<form id="sp_currency_form" action="<?php echo JURI::getInstance()->toString(); ?>" method="post">
    <div class="sp_currency">
        <?php echo JHTML::_('select.genericlist', $currencies, 'virtuemart_currency_id', 'class="inputbox" onChange="this.form.submit()"', 'virtuemart_currency_id', 'short_name', $virtuemart_currency_id) ; ?>
    </div>
    <input class="button" style="display: none;" type="text" name="" value="<?php echo JText::_('MOD_VIRTUEMART_CURRENCIES_CHANGE_CURRENCIES') ?>" />
</form>
</div>
