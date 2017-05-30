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
	static function display(&$row, $lists, $status_list)
	{
		$GLOBALS['titulo_status_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('status'); ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
		<?php echo JHtml::_('form.token'); ?>
	    <div class="breadcrumbs">
	        <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	        <a href="index.php?option=com_maqmahelpdesk&task=status"><?php echo JText::_('status'); ?></a>
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
	                         data-original-title="<?php echo htmlspecialchars(JText::_('status_group')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('status_group')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('status_group'); ?>
				                    </span>
	                    </div>
	                    <div class="span8">
							<?php echo $lists['group']; ?>
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
							<?php echo $lists['default']; ?>
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
							<?php echo $lists['show']; ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="field w100t">
	            <div class="divider"><a href="javascript:;" class="lbl"><?php echo JText::_('advanced_options');?></a>
	            </div>
	        </div>
	        <div id="advanced_options">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('user_access_status')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('user_access_status')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('user_access_status'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['user_access']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('status_workflow')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('status_workflow')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('status_workflow'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['status_workflow']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('allow_old_status_back')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('allow_old_status_back')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('allow_old_status_back'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['allow_old_status_back']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('status_side_title')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('status_side_title')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('status_side_title'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['status_side']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('default_for_manager')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('default_for_manager_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('default_for_manager'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['default_manager']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('auto_status_agents')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('auto_status_agents_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('auto_status_agents'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['auto_status_agents']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('auto_status_users')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('auto_status_users_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('auto_status_users'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['auto_status_users']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('status_color')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('status_color_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('status_color'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
	                            <input style="width:175px;"
	                                   type="text"
	                                   id="color"
	                                   name="color"
	                                   value="<?php echo $row->color; ?>"
	                                   maxlength="7" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	    <input type="hidden" name="task" value=""/>
	    </form>

	    <script type='text/javascript'>
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'status') {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.description.value == "") {
                alert("<?php echo JText::_('description_required'); ?>");
            } else if ($jMaQma("#auto_status_agents").is(":checked") && $jMaQma("#auto_status_users").is(":checked")) {
                alert('<?php echo JText::_('auto_status_error');?>');
            } else {
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }
        }

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
            $jMaQma("#color").miniColors({letterCase:'uppercase'});
        });
	    </script><?php
	}
}
