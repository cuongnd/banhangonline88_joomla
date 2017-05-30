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
	static function display(&$row)
	{
		$editor = JFactory::getEditor(); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'replies') {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.subject.value == "") {
                alert("<?php echo JText::_('subject_required'); ?>");
            } else {
				<?php echo $editor->save('answer'); ?>
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
	            <a href="index.php?option=com_maqmahelpdesk&task=replies"><?php echo JText::_('predefined_replies'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('subject')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('subject')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('subject'); ?>
			                    </span>
	                        </div>
	                        <div class="span10">
	                            <input type="text"
	                                   id="subject"
	                                   name="subject"
	                                   class="span10"
	                                   value="<?php echo $row->subject; ?>"
	                                   maxlength="100" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('answer')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('answer')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('answer'); ?>
			                    </span>
	                        </div>
	                        <div class="span10">
								<?php echo $editor->display('answer', $row->answer, '100%', '200', '75', '20');?>
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
