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

$supportConfig = HelpdeskUtility::GetConfig();

$database->setQuery("SELECT k.id, k.kbtitle, k.date_created, u.name AS uname"
		. "\nFROM #__support_kb k, #__users u"
		. "\nWHERE k.id_user=u.id"
		. "\nORDER BY `date_created` DESC"
		. "\nLIMIT 5"
);
$rows = $database->loadObjectList(); ?>

<table class="table table-striped table-bordered noleftborder" cellspacing="0">
	<thead>
	<tr>
		<th colspan="2"><?php echo JText::_('latest_kb'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($rows as $row) { ?>
		<tr>
			<td><a
				href="index.php?option=com_maqmahelpdesk&task=kb_edit&cid[0]=<?php echo $row->id;?>"><?php echo $row->kbtitle;?></a>
			</td>
			<td><?php echo $row->uname;?></td>
		</tr><?php
	}
	if (count($rows) == 0) { ?>
		<tr>
			<td colspan="2"><?php echo JText::_('KB_NO_ITENS'); ?></td>
		</tr><?php
	} ?>
	</tbody>
</table>

<br/>

<?php
$database->setQuery("SELECT (sum(r.rate) / count(r.id)) AS rate, count(r.id) AS trates, k.id, k.kbtitle, k.date_created"
		. "\nFROM #__support_kb k, #__support_rate r"
		. "\nWHERE r.id_table=k.id AND r.source='K'"
		. "\nGROUP BY k.id, k.kbtitle, k.date_created"
		. "\nORDER BY rate DESC"
		. "\nLIMIT 5"
);
$rows = $database->loadObjectList();
?>
<table class="table table-striped table-bordered noleftborder" cellspacing="0" style="margin-right:5px;">
	<thead>
	<tr>
		<th colspan="2"><?php echo JText::_('top10_articles'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($rows as $row) { ?>
	<tr>
		<td><a
			href="index.php?option=com_maqmahelpdesk&task=kb_edit&cid[0]=<?php echo $row->id;?>"><?php echo $row->kbtitle;?></a>
		</td>
		<td><img src="../media/com_maqmahelpdesk/images/rating/<?php echo ceil($row->rate);?>star.png"
				 alt="<?php echo str_replace('%1', ceil($row->trates), JText::_('narticles_votes')); ?>"></td>
	</tr><?php
	}
	if (count($rows) == 0) { ?>
		<tr>
			<td colspan="2"><?php echo JText::_('no_articles_rated'); ?></td>
		</tr><?php
	} ?>
	</tbody>
</table>
