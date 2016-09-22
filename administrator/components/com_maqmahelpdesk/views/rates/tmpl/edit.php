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
		$GLOBALS['titulo_rates_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('tmpl_msg13'); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'rates') {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.description.value == "") {
                alert("<?php echo JText::_('description_required'); ?>");
            } else {
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }
        }

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="javascript:;"><?php echo JText::_('actitivity_options'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=rates"><?php echo JText::_('activity_rates'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('description')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('description'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
	                            <input type="text"
	                                   id="description"
	                                   name="description"
	                                   value="<?php echo $row->description; ?>"
	                                   maxlength="100" />
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('multiplier')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('multiplier')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('multiplier'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
	                            <input type="text"
	                                   id="multiplier"
	                                   name="multiplier"
	                                   value="<?php echo $row->multiplier; ?>"
	                                   maxlength="100" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('is_default')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('is_default')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('is_default'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['isdefault']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('published')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('published')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('published'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['published']; ?>
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
