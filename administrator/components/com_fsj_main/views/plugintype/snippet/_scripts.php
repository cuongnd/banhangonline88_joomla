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
	fsj_add_shortcut_key(true, false, false, 'S', '#toolbar-apply');
	fsj_add_shortcut_key(true, false, true, 'S', '#toolbar-save');
	fsj_add_shortcut_key(true, true, false, 'S', '#toolbar-save');
	fsj_add_shortcut_key(true, false, false, 'D', '#toolbar-cancel');
	fsj_add_shortcut_key(true, false, false, 'N', '#toolbar-save-new');
	fsj_add_shortcut_key(true, false, true, 'N', '#toolbar-save-copy');
	fsj_add_shortcut_key(true, true, false, 'P', '#toolbar-publishclose');

	jQuery('.tt-delay').fsj_tooltip();
});

Joomla.submitbutton = function(task) {
	if (task == 'plugintype.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
		<?php //echo $this->form->getField('settings')->save(); ?>
		Joomla.submitform(task, document.getElementById('item-form'));
	} else {
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	}
}
	

</script>
