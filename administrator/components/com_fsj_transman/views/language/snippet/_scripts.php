<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<script>

jQuery(document).ready(function () {
	fsj_add_shortcut_key(true, false, 'S', '#toolbar-apply');
	fsj_add_shortcut_key(true, true, 'S', '#toolbar-save');
	fsj_add_shortcut_key(true, false, 'D', '#toolbar-cancel');
	fsj_add_shortcut_key(true, false, 'N', '#toolbar-save-new');
	fsj_add_shortcut_key(true, true, 'N', '#toolbar-save-copy');
});

Joomla.submitbutton = function(task) {
	if (task == 'language.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
		Joomla.submitform(task, document.getElementById('item-form'));
	} else {
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	}
}
	

</script>
