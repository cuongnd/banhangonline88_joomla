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

class MaQmaHtmlCFEdit
{
	static function display(&$row, $lists)
	{
		$GLOBALS['titulo_contracts_fields_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('cfield'); ?>

		<script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'contract_fields_save') {
                Joomla.submitform(pressbutton);
                return;
            }

            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
        }
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=customfield"><?php echo JText::_('cfields'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=contracts"><?php echo JText::_('contracts'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=contracts_fields"><?php echo JText::_('users_cfield_menu'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('field')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('field')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('field'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['fields']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('required')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('required')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('required'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['required']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	        <input type="hidden" name="ordering" value="<?php echo $lists['ordering']; ?>"/>
	        <input type="hidden" name="task" value=""/>
	    </form><?php
	}
}
