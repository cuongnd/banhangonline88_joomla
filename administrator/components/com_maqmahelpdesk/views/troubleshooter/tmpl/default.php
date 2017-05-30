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

function showTroubleshooter(&$rows)
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig(); ?>

<script type='text/javascript'>
	function SubmitForm() {
		document.adminForm.submit()
	}
</script>

<script language="JavaScript" src="<?php echo JURI::root() . "media/com_maqmahelpdesk/js/dtree.js"; ?>"></script>

<form action="index.php" method="POST" id="adminForm" name="adminForm">
	<?php echo JHtml::_('form.token'); ?>
	<div id="contentbox"><?php
		$troubleHeader = null;
		$database->setQuery("SELECT * FROM #__support_troubleshooter ORDER BY id");
		$troubles = $database->loadObjectList();

		if (count($troubles) > 0) {
			?>
			<div class="dtree">
				<p><a href="javascript: d.openAll();"><?php echo JText::_('open'); ?></a> | <a
					href="javascript: d.closeAll();"><?php echo JText::_('close'); ?></a></p>

				<script type="text/javascript">
					<!--
					d = new dTree('d');<?php
						for ($i = 0; $i < count($troubles); $i++) {
							$rowLvl = &$troubles[$i];
							$link = "index.php?option=com_maqmahelpdesk&task=troubleshooter_edit&id=" . $rowLvl->id . "&parent=" . $rowLvl->parent;
							print "d.add(" . $rowLvl->id . "," . ($rowLvl->parent == 0 ? -1 : $rowLvl->parent) . ",'" . addslashes($rowLvl->title) . "','" . $link . "','','','" . JURI::root() . "/media/com_maqmahelpdesk/images/dtree/folder.gif');\n";
						} ?>

					document.write(d);
					//-->
				</script>
			</div><?php
		} else {
			?>
			<b><a
				href="index.php?option=com_maqmahelpdesk&task=troubleshooter_new"><?php echo JText::_('add'); ?></a><br><?php echo JText::_('nothing_trouble'); ?>
			</b><?php
		} ?>
	</div>
	<div id="infobox">
		<span id="infoarrow"></span>
		<dl class="first">
			<dd class="title"><?php echo JText::_('INFO_TROUBLESHOOTER_TITLE');?></dd>
			<dd class="last">
				<?php echo JText::_('INFO_TROUBLESHOOTER_DESC');?>
                <div class="btn-group">
                    <a href="#" target="_blank" class="btn btn-small"><i class="ico-book"></i> <?php echo JText::_('more_information');?></a>
                    &nbsp;
                    <a id="mqmCloseHelp" href="javascript:;" class="btn btn-small btn-inverse"><i class="ico-off ico-white"></i> <?php echo JText::_('close');?></a>
                </div>
			</dd>
		</dl>
	</div>

	<input type="hidden" name="option" value="com_maqmahelpdesk"/>
	<input type="hidden" name="task" value="troubleshooter"/>
</form>

	<script type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'show_help') {
                $jMaQma("#infobox").show();
                return;
            }

            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
        }
	</script><?php
}

function troubleForm($row, $parent, $id)
{
	$editor = JFactory::getEditor();
	$GLOBALS['title_edit_trouble'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('troubleshooter'); ?>

<script language="javascript">
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'troubleshooter') {
            Joomla.submitform(pressbutton);
			return;
		}

		if (form.title.value == "") {
			alert("<?php echo JText::_('title_required'); ?>");
		} else {
			<?php echo $editor->save('description'); ?>
			Joomla.submitform(pressbutton, document.getElementById('adminForm'));
		}
	}
</script>

<form action="index.php" method="POST" id="adminForm" name="adminForm" class="label-inline">
	<?php echo JHtml::_('form.token'); ?>
	<?php if ($row->id): ?>
	<div style="text-align:right;background:#e5e5e5;padding:5px;margin-bottom:10px;">
		<div class="btn-group">
			<a href="index.php?option=com_maqmahelpdesk&task=troubleshooter_new&parent=<?php echo $id; ?>"
			   class="btn btn-success primary"><?php echo JText::_('add');?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=troubleshooter_delete&id=<?php echo $id; ?>"
			   class="btn"><?php echo JText::_('delete');?></a>
		</div>
	</div>
	<?php endif; ?>
	<div class="field w100">
		<span class="label" rel="tooltip"
              data-original-title="<?php echo htmlspecialchars(JText::_('title')); ?>">
			<?php echo JText::_('title'); ?>
		</span>
		<input class="large" type="text" id="title" name="title" value="<?php echo $row->title; ?>" maxlength="100"/>
	</div>
	<div class="field w100" style="height:550px;">
		<span class="label" rel="tooltip"
              data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>">
			<?php echo JText::_('description'); ?>
		</span>

		<div
			style="margin-left:170px;"><?php echo $editor->display('description', $row->description, '100%', '500', '75', '20');?></div>
	</div>

	<input type="hidden" name="option" value="com_maqmahelpdesk"/>
	<input type="hidden" name="id" value="<?php echo JRequest::getVar('id', 0, '', 'int'); ?>"/>
	<input type="hidden" name="parent" value="<?php echo JRequest::getVar('parent', 0, '', 'int'); ?>"/>
	<input type="hidden" name="task" value="<?php echo JRequest::getVar('task', 0, '', 'int'); ?>"/>
</form><?php
}
