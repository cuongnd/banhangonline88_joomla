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
		$editor = JFactory::getEditor(); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'glossary') {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.term.value == "") {
                alert("<?php echo JText::_('term_required'); ?>");
            } else if (form.id_category.value == 0 || form.id_category.value == '') {
                alert("<?php echo JText::_('bug_category_required'); ?>");
            } else {
				<?php echo $editor->getContent('description'); ?>
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
	            <a href="index.php?option=com_maqmahelpdesk&task=glossary"><?php echo JText::_('glossary'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('term')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('form_name_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('term'); ?>
				                    </span>
	                        </div>
	                        <div class="span10">
	                            <input type="text"
	                                   id="term"
	                                   name="term"
                                       class="span10"
	                                   value="<?php echo $row->term; ?>"
	                                   maxlength="100" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('category')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('category'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['id_category']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('anonymous_access')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('anonymous_access')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('anonymous_access'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['anonymous']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
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
								<?php echo $editor->display('description', $row->description, '100%', '500', '75', '20');?>
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
