<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
</form>
<form name="<?php echo $this->id; ?>ParamsForm" id="<?php echo $this->id; ?>ParamsForm">

<style>
fieldset.adminform
{
	margin: 0px;
}

fieldset
{
	border: none;
}

</style>
<div id="editcell">

<fieldset class="adminform">
	<ul class="adminformlist">
		<?php $fields = $this->form->getFieldSet(); ?>
		<?php foreach ($fields as &$field): ?>							
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</li>			
		<?php endforeach; ?>
	</ul>
</fieldset>
<div>
			<input class="btn_back" type="submit" id="<?php echo $this->id; ?>submit" value="<?php echo JText::_($this->addbtntext); ?>" />
			<input class="btn_back" type="submit" id="<?php echo $this->id; ?>cancel" value="<?php echo JText::_('JCANCEL'); ?>" />
</div>
	
<script>
jQuery(document).ready(function () {
	jQuery('#<?php echo $this->id; ?>ParamsForm').submit( function(ev) {
		ev.preventDefault();
	});
	jQuery('#<?php echo $this->id; ?>submit').click( function(ev) {
		ev.preventDefault();
		AddItem();
	});
	jQuery('#<?php echo $this->id; ?>cancel').click( function(ev) {
		ev.preventDefault();
		window.parent.TINY.box.hide();
	});
});

function AddItem()
{
	// need to somehow serialize the form relparamsform into an array, and pass it to a function
	var inputs = jQuery('#<?php echo $this->id; ?>ParamsForm :input');

    // not sure if you wanted this, but I thought I'd add it.
    // get an associative array of just the values.
    var values = {};
    jQuery(inputs).each(function() {
		var name = this.name;
		name = name.replace('params[','');
		name = name.replace(']','');
		if (name.length > 0)
			values[name] = jQuery(this).val();
    });
	window.parent.Add<?php echo $this->id; ?>ParamItem('<?php echo $this->pluginid; ?>',values);
}
</script>

</div>