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
$id_wk = JRequest::getVar('id_wk', 0, '', 'int');
$action = JRequest::getVar('action', '', '', 'string');
$name = JRequest::getVar('name', '', '', 'string');
$description = JRequest::getVar('description', '', '', 'string');
$image = JRequest::getVar('image', '', '', 'string');
$published = JRequest::getVar('published', '', '', 'string');
$public = JRequest::getVar('public', '', '', 'string');
$ordering = JRequest::getVar('ordering', '', '', 'string');
$link = JRequest::getVar('link', '', '', 'string');
$section = JRequest::getVar('section', '', '', 'string');
$orders = JRequest::getVar('linkstable', array(0), '', 'array');

$CONFIG = new JConfig();
$supportConfig = HelpdeskUtility::GetConfig();
$database = JFactory::getDBO();

switch ($action) {
	case 'save':
		if ($id == 0) {
			$sql = "INSERT INTO #__support_links(`id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`, `public`) VALUES('" . $id_wk . "', " . $database->quote($name) . ", " . $database->quote($description) . ", " . $database->quote($image) . ", " . $database->quote($link) . ", " . $database->quote($section) . ", '" . $published . "', '" . $ordering . "', '" . $public . "')";
		} else {
			$sql = "UPDATE #__support_links
					SET `name`=" . $database->quote($name) . ", `description`=" . $database->quote($description) . ", `image`=" . $database->quote($image) . ", `link`=" . $database->quote($link) . ", `published`=" . (int)$published . ", `ordering`=" . (int)$ordering . ", `public`=" . (int)$public . "
					WHERE id=" . (int)$id;
		}
		$database->setQuery($sql);
		$database->query();
		break;

	case 'delete':
		$sql = "DELETE FROM #__support_links WHERE id='" . $id . "'";
		$database->setQuery($sql);
		$database->query();
		break;

	case 'saveorder':
		for ($i = 1; $i < count($orders); $i++) {
			$sql = "UPDATE #__support_links
					SET ordering='" . $i . "'
					WHERE id='" . $orders[$i] . "' AND `section`='" . $section . "'";
			$database->setQuery($sql);
			$database->query();
		}
		break;
}

if ($action == 'new' || $action == 'edit') {
	$GLOBAL['action'] = $action;
	$link = null;

	if ($action == 'edit') {
		$database->setQuery("SELECT * FROM #__support_links WHERE id='" . $id . "' AND id_workgroup='" . $id_wk . "'");
		$link = $database->loadObject();
	} else {
		$database->setQuery("SELECT MAX(ordering) FROM #__support_links WHERE id_workgroup='" . $id_wk . "'");
		$link = new stdClass;
		$link->ordering = ($database->loadResult() + 1);
		$link->id = 0;
		$link->name = '';
		$link->description = '';
		$link->url = '';
		$link->link = '';
		$link->image = '';
		$link->section = $section;
		$link->published = 1;
		$link->public = 1;
	}

	$lists['public'] = HelpdeskForm::SwitchCheckbox('radio', 'public', array(JText::_('MQ_NO'), JText::_('MQ_YES')), array('0', '1'), $link->public, 'switch');
	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'link_published', array(JText::_('MQ_NO'), JText::_('MQ_YES')), array('0', '1'), $link->published, 'switch'); ?>

<input type="hidden" id="link_id" name="link_id" value="<?php echo $link->id; ?>"/>
<input type="hidden" id="link_section" name="link_section" value="<?php echo $link->section; ?>"/>
<input type="hidden" id="link_ordering" name="link_ordering" value="<?php echo $link->ordering; ?>"/>

<table id="linkstable" class="table table-striped table-bordered ontop">
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('name'); ?> </td>
		<td align="left"><input type="text" size="100" value="<?php echo $link->name; ?>" class="inputbox"
								id="link_name" name="link_name"/></td>
	</tr>
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('tmpl_msg23'); ?> </td>
		<td align="left"><input type="text" size="100" value="<?php echo $link->description; ?>" class="inputbox"
								id="link_description" name="link_description"/></td>
	</tr>
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('link'); ?> </td>
		<td align="left"><input type="text" size="100" value="<?php echo $link->link; ?>" class="inputbox" id="link_url"
								name="link_url"/></td>
	</tr>
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('image'); ?> </td>
		<td align="left"><input type="text" size="100" value="<?php echo $link->image; ?>" class="inputbox"
								id="link_image" name="link_image"/></td>
	</tr><?php
	if ($section != 'A') {
		?>
		<tr>
			<td nowrap valign="top" class="key"><?php echo JText::_('public'); ?> </td>
			<td align="left"><?php echo$lists['public']; ?></td>
		</tr><?php
	} else {
		?>
		<input type="hidden" name="public" value="0"><?php
	} ?>
	<tr>
		<td nowrap valign="top" class="key"><?php echo JText::_('published'); ?> </td>
		<td align="left"><?php echo$lists['published']; ?></td>
	</tr>
	<tr>
		<td colspan="2" style="background:#e5e5e5;padding:5px;text-align:right;">
			<a href="javascript:;" onclick="saveLink();" class="btn btn-success"><?php echo JText::_('savelink');?></a>
			<a href="javascript:;" onclick="cancelLink();" class="btn"><?php echo JText::_('cancel');?></a>
		</td>
	</tr>
</table><?php

} else {
	$sql = "SELECT * FROM #__support_links WHERE section=" . $database->quote($section) . " ORDER BY ordering";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	print '<table id="linkstable" class="table table-striped table-bordered ontop">';
	print '<thead>';
	print '<tr>';
	print '	<th width="20">&nbsp;</th>';
	print '	<th class="title">' . JText::_('name') . '</th>';
	print '	<th class="title">' . JText::_('tmpl_msg23') . '</th>';
	print '	<th class="title">' . JText::_('link') . '</th>';
	if ($section != 'A') {
		print '	<th class="center" width="50" nowrap="nowrap" align="center">' . JText::_('public') . '</th>';
	}
	print '	<th class="center" width="50" nowrap="nowrap" align="center">' . JText::_('published') . '</th>';
	print '	<th class="center" width="50" nowrap="nowrap" align="center">&nbsp;</th>';
	print '</tr>';
	print '</thead>';

	print '<tbody>';

	if (count($rows) == 0) {
		print '<tr><td colspan=7>' . JText::_('register_not_found') . '</td></tr>';
	} else {
		$k = 0;

		for ($i = 0; $i < count($rows); $i++) {
			$row = $rows[$i];

			$alt = $row->published ? JText::_('published') : JText::_('unpublish');
			$img = $row->published ? 'ok' : 'no';

			$alt1 = $row->public ? 'Public' : 'Not Public';
			$img1 = $row->public ? 'ok' : 'no';

			print '<input type="hidden" id="link' . $i . '" name="link' . $i . '" value="' . $row->id . '" />';
			print '<tr id="contentTableLinks-row-' . $row->id . '" class="row' . $k . '">';
			print '	<td width="20" class="dragHandle"></td>';
			print '	<td>' . $row->name . '</td>';
			print '	<td>' . $row->description . '</td>';
			print '	<td>' . $row->link . '</td>';
			if ($section != 'A') {
				print '	<td align="center"><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . $img1 . '.png" border="0" alt="' . $alt1 . '" /></td>';
			}
			print '	<td align="center"><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . $img . '.png" border="0" alt="' . $alt . '" /></td>';
			print '	<td align="center">';
			print '		<a href="javascript:;" onClick="javascript:showLink(' . $row->id . ');"><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/edit.png" border="0" alt="' . JText::_('edit') . '" /></a>';
			print '		<a href="javascript:;" onClick="javascript:deleteLink(' . $row->id . ');"><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/delete.png" border="0" alt="' . JText::_('delete') . '" /></a>';
			print '	</td>';
			print '</tr>';
			$k = 1 - $k;
		}
	}
	print '<tr>';
	print '   <td colspan="7" style="background:#e5e5e5;padding:5px;text-align:right;">';
	print '	  <a href="javascript:;" onclick="showLink(0);" class="btn btn-success">' . JText::_('newlink') . '</a>';
	print '   </td>';
	print '</tr>';
	print '</tbody>';
	print '</table>';

	print '<input type="hidden" id="nr_links" name="nr_links" value="' . count($rows) . '" />'; ?>

<script type="text/javascript">
	$jMaQma(document).ready(function () {
		$jMaQma('#linkstable').tableDnD({
			onDrop:function (table, row) {
				$jMaQma("div#loading").show();
				$jMaQma("div#<?php echo ($section == 'F' ? 'links' : 'linkscpanel');?>").load("index.php?option=com_maqmahelpdesk&task=<?php echo ($section == 'F' ? 'workgroup' : 'config');?>_ajax&page=links&tmpl=component&format=raw&" + $jMaQma('#linkstable').tableDnDSerialize(),
					{
						action:'saveorder',
						section:'<?php echo $section;?>'
					},
					function () {
						$jMaQma("div#loading").hide();
					});
			},
			dragHandle:"dragHandle"
		});

		$jMaQma("#linkstable tr").hover(function () {
			$jMaQma(this.cells[0]).addClass('showDragHandle');
		}, function () {
			$jMaQma(this.cells[0]).removeClass('showDragHandle');
		});
	});
</script><?php
}
