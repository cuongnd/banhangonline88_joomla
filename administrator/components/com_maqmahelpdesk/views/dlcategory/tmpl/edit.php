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

class MaQmaHtmlEdit
{
	static function display(&$_row, $lists)
	{
		$editor = JFactory::getEditor(); ?>

		<script language="javascript" type="text/javascript">
			Joomla.submitbutton = function (pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'dlcategory') {
					Joomla.submitform(pressbutton);
					return;
				}

				if (form.cname.value == "") {
					alert("<?php echo JText::_('name_required'); ?>");
				} else {
					<?php echo $editor->save('description'); ?>
					Joomla.submitform(pressbutton, document.getElementById('adminForm'));
				}
			}

			$jMaQma(document).ready(function () {
				$jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
			});
		</script>

		<form action="index.php" method="POST" id="adminForm" name="adminForm" enctype="multipart/form-data"
		      class="label-inline">
		<?php echo JHtml::_('form.token'); ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=product"><?php echo JText::_('downloads'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=dlcategory"><?php echo JText::_('categories'); ?></a>
			<span><?php echo JText::_('edit'); ?></span>
		</div>
		<div class="contentarea pad5">
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('parent')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('parent')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('parent'); ?>
				                    </span>
						</div>
						<div class="span10">
							<?php echo $lists['parent']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('name')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('name')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('name'); ?>
				                    </span>
						</div>
						<div class="span10">
							<input type="text"
							       class="span10"
							       id="cname"
							       name="cname"
							       value="<?php echo $_row->cname; ?>"
							       maxlength="100" />
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('slug')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('slug_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('slug'); ?>
				                    </span>
						</div>
						<div class="span10">
							<input type="text"
							       class="span10"
							       id="slug"
							       name="slug"
							       value="<?php echo $_row->slug; ?>"
							       maxlength="100"/>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('description')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('description'); ?>
				                    </span>
						</div>
						<div class="span10">
							<?php echo $editor->display('description', $_row->description, '100%', '300', '75', '20'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('image')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('image')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('image'); ?>
				                    </span>
						</div>
						<div class="span10">
							<input class="inputbox"
							       type="file"
							       name="image"
							       value=""
							       size="50" />
							<?php if ($_row->image != ''): ?>
								<br/>
								<img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/logos/<?php echo $_row->image;?>"
								     alt=""
								     width="48"
								     height="48" /><br />
								<?php echo JText::_("DELETE_IMAGE") . $lists['delete_image'];?>
							<?php endif;?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('published')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('published')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('published'); ?>
				                    </span>
						</div>
						<div class="span10">
							<?php echo $lists['published']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="id" value="<?php echo $_row->id; ?>"/>
		<input type="hidden" name="task" value=""/>
		</form><?php
	}
}