<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div>
<form action="<?php echo hikamarket::completeLink('serials&task=import&pack_id='.$this->pack->pack_id); ?>" method="post" name="hikamarket_serial_import_form" id="hikamarket_serial_import_form">
	<h3><?php echo JText::sprintf('HIKASERIAL_IMPORT_SERIALS_FOR_PACK', $this->pack->pack_name); ?></h3>

	<textarea style="width:100%" rows="20" name="data[serials]" placeholder="<?php echo JText::_('ENTER_SERIALS_HERE', true); ?>"></textarea>

	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="import" />
	<input type="hidden" name="pack_id" value="<?php echo (int)$this->pack->pack_id; ?>" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
