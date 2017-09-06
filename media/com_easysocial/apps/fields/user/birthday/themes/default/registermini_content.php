<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-field-datetime>
	<div>
		<?php echo $field->_('title'); ?>
	</div>

	<div class="o-grid o-grid--gutters">
		<div class="o-grid__cell">
			<?php echo $dateHTML[0]; ?>
		</div>

		<div class="o-grid__cell">
			<?php echo $dateHTML[1]; ?>
		</div>

		<div class="o-grid__cell">
			<?php echo $dateHTML[2]; ?>
		</div>
	</div>
	
	<input type="hidden" id="<?php echo $inputName; ?>-date" name="<?php echo $inputName; ?>[date]" value="<?php echo $date; ?>" data-field-datetime-value />
	
	<div class="es-fields-error-note" data-field-error></div>
</div>
