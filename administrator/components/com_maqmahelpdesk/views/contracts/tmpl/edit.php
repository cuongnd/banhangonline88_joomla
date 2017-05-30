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
	static function display(&$row, $lists)
	{
		$editor = JFactory::getEditor();
		$GLOBALS['titulo_contracts_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('contract_type'); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'contracts') {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.name.value == "") {
                alert("<?php echo JText::_('name_required'); ?>.");
            } else if (form.id_priority.value == "0") {
                alert("<?php echo JText::_('priority_required'); ?>.");
            } else if (form.unit.value == "") {
                alert("<?php echo JText::_('unit_required'); ?>.");
            } else if (form.val.value == "") {
                alert("<?php echo JText::_('value_required'); ?>.");
            } else {
				<?php echo $editor->save('description'); ?>
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }
        }

        $jMaQma(document).ready(function () {
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=contracts"><?php echo JText::_('contract_types'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('name')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('name')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('name'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
	                            <input type="text"
	                                   id="name"
	                                   name="name"
	                                   value="<?php echo $row->name; ?>"
	                                   maxlength="100" />
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('priority')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('priority')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('priority'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['priority']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('unit')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('unit')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('unit'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['unit']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('value')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('value')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('value'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
	                            <input type="text"
	                                   id="val"
	                                   name="val"
	                                   value="<?php echo $row->val; ?>"
	                                   maxlength="10"
	                                   size="12" />
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
								<?php echo $editor->display('description', $row->description, '100%', '300', '75', '20');?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	        <input type="hidden" name="task" value=""/>
	    </form><?php
	}
}
