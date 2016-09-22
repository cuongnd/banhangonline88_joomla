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
$type = JRequest::getVar('type', '', '', 'string');
$value = JRequest::getVar('value', '', '', 'string');
$published = JRequest::getVar('published', 0, '', 'int');
$orders = JRequest::getVar('orders', '', '', 'string');

$CONFIG = new JConfig();
$supportConfig = HelpdeskUtility::GetConfig();
$database = JFactory::getDBO();

switch ($action) {
	case 'save':
		if ($id == 0) {
			$sql = "INSERT INTO #__support_form_action(`id_form`, `type`, `value`, `published`) VALUES('" . $id_form . "', '" . $type . "', '" . $value . "', '" . $published . "')";
		} else {
			$sql = "UPDATE #__support_form_action SET `type`=" . $database->quote($type) . ", `value`=" . $database->quote($value) . ", `published`='" . $published . "' WHERE id='" . $id . "'";
		}
		$database->setQuery($sql);
		$database->query();
		break;

	case 'delete':
		$sql = "DELETE FROM #__support_form_action WHERE id='" . $id . "'";
		$database->setQuery($sql);
		$database->query();
		break;
}

if ($action == 'new' || $action == 'edit') {
	$row = null;
	if ($action == 'edit') {
		$database->setQuery("SELECT * FROM #__support_form_action WHERE id='" . $id . "'");
		$row = $database->loadObject();
	} else {
		$row = new stdClass;
		$row->id = 0;
		$row->id_form = $id_form;
		$row->type = '';
		$row->value = '';
		$row->published = 0;
	}

	//$lists = array();

	// Build the field type select list
	$ftypelist[] = JHTML::_('select.option', '', JText::_('selectlist'));
	$ftypelist[] = JHTML::_('select.option', 'email', JText::_('send_specified_email'));
	$ftypelist[] = JHTML::_('select.option', 'user', JText::_('send_email_user'));
	$ftypelist[] = JHTML::_('select.option', 'show', JText::_('show_results_submit'));
	$ftypelist[] = JHTML::_('select.option', 'db', JText::_('save_database'));
	$ftypelist[] = JHTML::_('select.option', 'include', JText::_('include_file'));
	$lists['type'] = JHTML::_('select.genericlist', $ftypelist, 'action_type', 'class="inputbox" size="1"', 'value', 'text', $row->type);

	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'action_published', array(JText::_('MQ_NO'), JText::_('MQ_YES')), array('0', '1'), $row->published, 'switch'); ?>

<input type="hidden" id="action_id" name="action_id" value="<?php echo $row->id; ?>"/>

<div class="field w100">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('type') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('type'); ?>
		</span>

	<div class="controlset-pad">
		<?php echo $lists['type']; ?>
	</div>
</div>
<div class="field w100" style="height:160px;">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('value') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('name'); ?>
		</span>
	<input class="large" type="text" id="action_value" name="action_value" value="<?php echo $row->value; ?>"
		   maxlength="100"/><br/>

	<div style="margin-left:170px;">
		<?php echo JText::_('action_help'); ?>
	</div>
</div>
<div class="field w100">
		<span class="label editlinktip hasTip"
			  title="<?php echo htmlspecialchars(JText::_('published') . '::' . JText::_('todo')); ?>">
			<?php echo JText::_('published'); ?>
		</span>

	<div class="controlset-pad">
		<?php echo $lists['published']; ?>
	</div>
</div>
<div class="field w100">
	<p align="right" style="background:#e5e5e5;padding:5px;">
		<a href="javascript:;" onclick="saveAction();" class="btn btn-success"><?php echo JText::_('saveaction');?></a>
		<a href="javascript:;" onclick="cancelAction();" class="btn"><?php echo JText::_('cancel');?></a>
	</p>
</div>

<script type="text/javascript">
	$jMaQma('#form-general').hide('fade');
	$jMaQma('#form-actions').show('fade');
	$jMaQma('#form-fields').hide('fade');
</script><?php
} else {
	$sql = "SELECT * FROM #__support_form_action WHERE id_form='" . $id_form . "' ORDER BY `type`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList(); ?>

<table class="table table-striped table-bordered ontop">
	<thead>
	<tr>
		<th class="valgmdl"><?php echo JText::_('type'); ?></th>
		<th class="algcnt valgmdl" width="70"><?php echo JText::_('published'); ?></th>
		<th class="algcnt valgmdl" width="80">&nbsp;</th>
	</tr>
	</thead>
	<tbody><?php
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = &$rows[$i];
			if ($row->type == 'include') {
				$file_include = file_exists($row->value);
				$img_file = $img = '<img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . ($file_include ? 'ok' : 'no') . '.png" border="0" alt="' . ($file_include ? 'File exists' : 'File does not exist') . '" align="absmiddle" />';
			}

			$type_desc = '';
			switch ($row->type) {
				case 'email':
					$type_desc = JText::_('send_specified_email') . ': <b>' . $row->value . '</b>';
					break;
				case 'user':
					$type_desc = JText::_('send_email_user') . ': <b>' . $row->value . '</b>';
					break;
				case 'show':
					$type_desc = JText::_('show_results_submit');
					break;
				case 'db':
					$type_desc = JText::_('save_database');
					break;
				case 'include':
					$type_desc = JText::_('include_file') . ': <b>' . $row->value . '</b> ' . $img_file;
					break;
			}

			$img = $row->published ? 'ok' : 'remove'; ?>

			<input type="hidden" id="action<?php echo $i;?>" name="action<?php echo $i;?>" value="<?php echo $row->id;?>"/>
			<tr>
				<td class="valgmdl"><?php echo $type_desc;?></td>
				<td class="algcnt valgmdl"><span class="btn btn-<?php echo ($img=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span></td>
				<td class="algcnt valgmdl">
					<div class="btn-group">
						<a class="btn" onclick="javascript:showAction(<?php echo $row->id;?>);"><i class="ico-pencil"></i></a>
						<a class="btn btn-danger" onclick="javascript:deleteAction(<?php echo $row->id;?>);"><i class="ico-trash ico-white"></i></a>
					</div>
				</td>
			</tr><?php
		}
		if (count($rows) == 0) {
			print '<tr><td colspan="3">' . JText::_('register_not_found') . '</td></tr>';
		} ?>
	<tr>
		<td colspan="3" style="text-align:right;background:#e5e5e5;padding:5px;">
			<a href="javascript:;" onclick="showAction(0);" class="btn btn-success"><?php echo JText::_('add');?></a>
		</td>
	</tr>
	</tbody>
</table>

<input type="hidden" id="nr_actions" name="nr_actions" value="<?php echo count($rows);?>"/><?php
}
