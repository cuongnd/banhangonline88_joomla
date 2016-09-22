<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Parameters
$id = JRequest::getVar('id', 0, '', 'int');
$id_form = JRequest::getVar('id_form', 0, '', 'int');
$action = JRequest::getVar('action', '', '', 'string');
$caption = JRequest::getVar('caption', '', '', 'string');
$type = JRequest::getVar('type', '', '', 'string');
$value = JRequest::getVar('value', '', '', 'string');
$order = JRequest::getVar('order', '', '', 'string');
$size = JRequest::getVar('size', '', '', 'string');
$maxlength = JRequest::getVar('maxlength', '', '', 'string');
$required = JRequest::getVar('required', 0, '', 'int');
$orders = JRequest::getVar('orders', '', '', 'string');

$CONFIG = new JConfig();
$supportConfig = HelpdeskUtility::GetConfig();
$database = JFactory::getDBO();

switch ($action) {
	case 'save':
		if ($id == 0) {
			$sql = "INSERT INTO #__support_form_field(`id_form`, `caption`, `type`, `value`, `order`, `size`, `maxlength`, `required`) VALUES('" . $id_form . "', " . $database->quote($caption) . ", " . $database->quote($type) . ", " . $database->quote($value) . ", '" . $order . "', '" . $size . "', '" . $maxlength . "', '" . $required . "')";
		} else {
			$sql = "UPDATE #__support_form_field SET `caption`=" . $database->quote($caption) . ", `type`=" . $database->quote($type) . ", `value`=" . $database->quote($value) . ", `order`='" . $order . "', `size`='" . $size . "', `maxlength`='" . $maxlength . "', `required`='" . $required . "' WHERE id='" . $id . "'";
		}
		$database->setQuery($sql);
		$database->query();
		break;

	case 'delete':
		$sql = "DELETE FROM #__support_form_field WHERE id='" . $id . "'";
		$database->setQuery($sql);
		$database->query();
		break;

	case 'saveorder':
		$order_links = explode(';', $orders);
		for ($i = 0; $i < (count($order_links) - 1); $i++) {
			$link = explode('|', $order_links[$i]);
			$sql = "UPDATE #__support_form_field SET `order`='" . $link[1] . "' WHERE id='" . $link[0] . "'";
			$database->setQuery($sql);
			$database->query();
		}
		break;
}

if ($action == 'new' || $action == 'edit') {
	$row = null;

	if ($action == 'edit') {
		$database->setQuery("SELECT * FROM #__support_form_field WHERE id='" . $id . "'");
		$row = $database->loadObject();
	} else {
		$database->setQuery("SELECT MAX(`order`) FROM #__support_form_field WHERE id_form='" . $id_form . "'");
		$row = new stdClass;
		$row->order = (int)$database->loadResult() + 1;
		$row->id = 0;
		$row->id_form = $id_form;
		$row->caption = '';
		$row->type = '';
		$row->value = '';
		$row->size = '';
		$row->maxlength = '';
		$row->required = 0;
	}

	// Build the field type select list
	$ftypelist[] = JHTML::_('select.option', '', JText::_('selectlist'));
	$ftypelist[] = JHTML::_('select.option', 'text', JText::_('textbox'));
	$ftypelist[] = JHTML::_('select.option', 'select', JText::_('listbox'));
	$ftypelist[] = JHTML::_('select.option', 'radio', JText::_('radiobutton'));
	$ftypelist[] = JHTML::_('select.option', 'checkbox', JText::_('checkbox'));
	$ftypelist[] = JHTML::_('select.option', 'textarea', JText::_('textarea'));
	$ftypelist[] = JHTML::_('select.option', 'htmleditor', JText::_('htmleditor'));
	$lists['type'] = JHTML::_('select.genericlist', $ftypelist, 'field_type', 'class="inputbox" size="1"', 'value', 'text', $row->type);

	$lists['required'] = HelpdeskForm::SwitchCheckbox('radio', 'field_required', array(JText::_('MQ_NO'), JText::_('MQ_YES')), array('0', '1'), $row->required, 'switch'); ?>

<input type="hidden" id="field_id" name="field_id" value="<?php echo $row->id; ?>"/>

<div class="field w100">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('caption') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('caption'); ?>
		</span>
	<input class="large" type="text" id="field_caption" name="field_caption" value="<?php echo $row->caption; ?>"
		   maxlength="100"/>
</div>
<div class="field w100">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('type') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('type'); ?>
		</span>

	<div class="controlset-pad">
		<?php echo $lists['type']; ?>
	</div>
</div>
<div class="field w100" style="height:75px;">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('default_value') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('default_value'); ?>
		</span>
	<input class="large" type="text" id="field_value" name="field_value" value="<?php echo $row->value; ?>"
		   maxlength="100"/><br/>

	<div style="margin-left:170px;">
		<?php echo JText::_('fields_desc'); ?>
	</div>
</div>
<div class="field w50">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('size') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('size'); ?>
		</span>
	<input class="medium" type="text" id="field_size" name="field_size" value="<?php echo $row->size; ?>"
		   maxlength="100"/>
</div>
<div class="field w50">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('maxlength') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('maxlength'); ?>
		</span>
	<input class="medium" type="text" id="field_maxlength" name="field_maxlength" value="<?php echo $row->maxlength; ?>"
		   maxlength="100"/>
</div>
<div class="field w50">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('ordering') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('ordering'); ?>
		</span>
	<input class="medium" type="text" id="field_order" name="field_order" value="<?php echo $row->order; ?>"
		   maxlength="100"/>
</div>
<div class="field w50">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('required') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('required'); ?>
		</span>

	<div class="controlset-pad">
		<?php echo $lists['required']; ?>
	</div>
</div>
<div class="field w100">
	<p align="right" style="background:#e5e5e5;padding:5px;">
		<a href="javascript:;" onclick="saveField();" class="btn btn-success"><?php echo JText::_('savefield');?></a>
		<a href="javascript:;" onclick="cancelField();" class="btn"><?php echo JText::_('cancel');?></a>
	</p>
</div>

<script type="text/javascript">
	$jMaQma('#form-general').hide('fade');
	$jMaQma('#form-actions').hide('fade');
	$jMaQma('#form-fields').show('fade');
</script><?php

} elseif ($action == 'tags') {
	$sql = "SELECT id, `caption` FROM #__support_form_field WHERE id_form='" . $id_form . "' ORDER BY `order`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList(); ?>

<table class="table table-striped table-bordered ontop">
	<thead>
	<tr>
		<th class="title"><?php echo JText::_('field'); ?></th>
		<th class="title"><?php echo JText::_('caption'); ?></th>
		<th class="title"><?php echo JText::_('tag'); ?></th>
	</tr>
	</thead>
	<tbody><?php
		$k = 0;
		$j = 0;
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $row->caption;?></td>
			<td>&#60;tag:<?php echo $row->id;?> /&#62</td>
			<td>&#60;field:<?php echo $row->id;?> /&#62;</td>
		</tr><?php
		}
		if (count($rows) == 0) {
			print '<tr><td colspan="3">' . JText::_('register_not_found') . '</td></tr>';
		} ?>
	</tbody>
</table><?php

} else {
	$sql = "SELECT * FROM #__support_form_field WHERE id_form='" . $id_form . "' ORDER BY `order`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList(); ?>

<table class="table table-striped table-bordered ontop">
	<thead>
	<tr>
		<th class="valgmdl"><?php echo JText::_('caption'); ?></th>
		<th class="valgmdl"><?php echo JText::_('type'); ?></th>
		<th class="algcnt valgmdl" width="70"><?php echo JText::_('required'); ?></th>
		<th class="algcnt valgmdl" width="100">Order <a href="javascript:saveFieldOrder();"><img src="../components/com_maqmahelpdesk/images/toolbar/save.png" border="0" width="16" height="16" alt="<?php echo JText::_('saveorder'); ?>" align="absmiddle"/></a></th>
		<th class="algcnt valgmdl" width="80">&nbsp;</th>
	</tr>
	</thead>
	<tbody><?php
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = &$rows[$i];
			$alt = $row->required ? JText::_('required') : JText::_('optional');
			$img = $row->required ? 'ok' : 'remove'; ?>

			<input type="hidden" id="form<?php echo $i;?>" name="form<?php echo $i;?>" value="<?php echo $row->id;?>"/>
			<tr>
				<td class="valgmdl"><?php echo $row->caption;?></td>
				<td class="valgmdl"><?php echo $row->type;?></td>
				<td class="algcnt valgmdl"><span class="btn btn-<?php echo ($img=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span></td>
				<td class="algcnt valgmdl"><input type="text" id="order<?php echo $i;?>" name="order<?php echo $i;?>" size="5" value="<?php echo $row->order;?>" style="width:50px;text-align:center"/></td>
				<td class="algcnt valgmdl">
					<div class="btn-group">
						<a class="btn" onclick="javascript:showField(<?php echo $row->id;?>);"><i class="ico-pencil"></i></a>
						<a class="btn btn-danger" onclick="javascript:deleteField(<?php echo $row->id;?>);"><i class="ico-trash ico-white"></i></a>
					</div>
				</td>
			</tr><?php
		}
		if (count($rows) == 0) {
			print '<tr><td colspan="5">' . JText::_('register_not_found') . '</td></tr>';
		} ?>
	<tr>
		<td colspan="5" style="text-align:right;background:#e5e5e5;padding:5px;">
			<a href="javascript:;" onclick="showField(0);" class="btn btn-success"><?php echo JText::_('add');?></a>
		</td>
	</tr>
	</tbody>
</table>

<input type="hidden" id="nr_forms" name="nr_forms" value="<?php echo count($rows);?>"/><?php
}
