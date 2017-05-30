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
$id_wk = JRequest::getVar('id_wk', 0, '', 'int');
$id_cat = JRequest::getVar('id_cat', 0, '', 'int');
$id_user = JRequest::getVar('id_user', 0, '', 'int');
$city = JRequest::getVar('city', '', '', 'string');
$action = JRequest::getVar('action', '', '', 'string');

$database = JFactory::getDBO();
$supportConfig = HelpdeskUtility::GetConfig();

switch ($action) {
	case 'save':
		$sql = "DELETE FROM #__support_workgroup_category_assign
				WHERE `id_workgroup`='" . $id_wk . "' AND `id_category`='" . $id_cat . "' AND `city`='" . $city . "' AND `id_user`='" . $id_user . "'";
		$database->setQuery($sql);
		$database->query();
		$sql = "INSERT INTO #__support_workgroup_category_assign(`id_workgroup`, `id_user`, `id_category`, `city`)
				VALUES('" . $id_wk . "', '" . $id_user . "', '" . $id_cat . "', '" . $city . "')";
		$database->setQuery($sql);
		$database->query();
		break;

	case 'delete':
		$sql = "DELETE FROM #__support_workgroup_category_assign
				WHERE `id_workgroup`='" . $id_wk . "'
				  AND `id_category`='" . $id_cat . "'
				  AND `city`='" . $city . "'";
		$database->setQuery($sql);
		$database->query();
		break;
}

if ($action == 'new' || $action == 'edit') {
	$GLOBAL['action'] = $action;
	$assign = null;

	if ($action == 'edit') {
		$database->setQuery("SELECT * FROM #__support_workgroup_category_assign WHERE `id_workgroup`='" . $id_wk . "' AND `id_category`='" . $id_cat . "'");
		$assign = $database->loadObject();
	} else {
		$assign = new stdClass;
		$assign->id_workgroup = 0;
		$assign->id_category = '';
		$assign->id_user = '';
		$assign->city = '';
	}

	// Build Location select list
	$sql = "SELECT DISTINCT(`city`) AS value, `city` AS text FROM #__support_users ORDER BY `city`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$rows = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows);
	$lists['city'] = JHTML::_('select.genericlist', $rows, 'city', 'class="inputbox" size="1"', 'value', 'text', $assign->city);

	// Build Support Staff select list
	$sql = "SELECT DISTINCT(`id`) AS value, `name` AS text FROM #__support_category WHERE id_workgroup='" . $id_wk . "' ORDER BY `name`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$rows = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows);
	$lists['id_category'] = JHTML::_('select.genericlist', $rows, 'assign_cat', 'class="inputbox" size="1"', 'value', 'text', $assign->id_category);

	// Build Support Staff select list
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission p, #__users u WHERE p.id_user=u.id AND p.id_workgroup='" . $id_wk . "' ORDER BY u.name";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$rows = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows);
	$lists['id_user'] = JHTML::_('select.genericlist', $rows, 'assign_user', 'class="inputbox" size="1"', 'value', 'text', $assign->id_user); ?>

<input type="hidden" id="assign_wk" name="assign_wk" value="<?php echo $id_wk;?>"/>
<table class="table table-striped table-bordered ontop">
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('city'); ?> </td>
		<td align="left"><?php echo $lists['city']; ?></td>
	</tr>
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('category'); ?> </td>
		<td align="left"><?php echo $lists['id_category']; ?></td>
	</tr>
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('assignto'); ?> </td>
		<td align="left"><?php echo $lists['id_user']; ?></td>
	</tr>
	<tr>
		<td colspan="2" style="background:#e5e5e5;padding:5px;text-align:right;">
			<a href="javascript:;" onclick="saveAssign();"
			   class="btn btn-success"><?php echo JText::_('saveassign');?></a>
			<a href="javascript:;" onclick="cancelAssign();" class="btn"><?php echo JText::_('cancel');?></a>
		</td>
	</tr>
</table><?php

} else {
	$sql = "SELECT wca.id_workgroup, wca.id_category, wca.city, wca.id_user, w.wkdesc, u.name AS username, c.name AS catname
			FROM #__support_workgroup_category_assign AS wca
				 INNER JOIN #__support_workgroup AS w ON w.id=wca.id_workgroup
				 INNER JOIN #__users AS u ON u.id=wca.id_user
				 INNER JOIN #__support_category AS c ON c.id=wca.id_category
			WHERE wca.id_workgroup='" . $id_wk . "'";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	print '<table id="assignstable" class="table table-striped table-bordered ontop">';
	print '<thead>';
	print '<tr>';
	print '	<th>' . JText::_('workgroup') . '</th>';
	print '	<th>' . JText::_('category') . '</th>';
	print '	<th>' . JText::_('city') . '</th>';
	print '	<th>' . JText::_('user') . '</th>';
	print '	<th width="50" nowrap="nowrap" align="center">&nbsp;</th>';
	print '</tr>';
	print '</thead>';

	print '<tbody>';

	if (count($rows) == 0) {
		print '<tr><td colspan="5">' . JText::_('register_not_found') . '</td></tr>';
	} else {

		for ($i = 0; $i < count($rows); $i++) {
			$row = $rows[$i];

			print '<tr>';
			print '	<td>' . $row->wkdesc . '</td>';
			print '	<td>' . $row->catname . '</td>';
			print '	<td>' . $row->city . '</td>';
			print '	<td>' . $row->username . '</td>';
			print '	<td align="center">';
			print '		<a href="javascript:;" onClick="javascript:showAssign(' . $row->id_workgroup . ',' . $row->id_category . ',\'' . $row->city . '\',' . $row->id_user . ');"><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/edit.png" border="0" alt="' . JText::_('edit') . '" /></a>';
			print '		<a href="javascript:;" onClick="javascript:deleteAssign(' . $row->id_workgroup . ',' . $row->id_category . ',\'' . $row->city . '\',' . $row->id_user . ');"><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/delete.png" border="0" alt="' . JText::_('delete') . '" /></a>';
			print '	</td>';
			print '</tr>';
		}
	}
	if ($id_wk) {
		print '<tr>';
		print '   <td colspan="5" style="background:#e5e5e5;padding:5px;text-align:right;">';
		print '	  <a href="javascript:;" onclick="showAssign(0,0,\'\',0);" class="btn btn-success">' . JText::_('newassign') . '</a>';
		print '   </td>';
		print '</tr>';
	}
	print '</tbody>';
	print '</table>';
	print '<input type="hidden" id="nr_assigns" name="nr_assigns" value="' . count($rows) . '" />';
}
