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
		$GLOBALS['titulo_wkfields_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('wk_custom_field'); ?>

        <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'wkfields') {
                Joomla.submitform(pressbutton);
                return;
            }

            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
        }

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
        </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=workgroup"><?php echo JText::_('workgroups'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=wkfields"><?php echo JText::_('cfield_assign_menu'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('workgroup')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('workgroup'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['workgroup']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('category_wkfields_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('category'); ?>
				                    </span>
	                        </div>
	                        <div id="categoryField" class="span8">
								<?php echo $lists['category']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('section')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('section_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('section'); ?>
				                    </span>
	                        </div>
	                        <div id="sectionField" class="span8">
								<?php echo $lists['section']; ?>
	                            <br />
	                            <i><?php echo JText::_('section_new');?></i>
	                            <input type="text"
	                                   id="section_new"
	                                   name="section_new"
	                                   value=""
	                                   style="width:100px;"
	                                   maxlength="50" />
	                        </div>
	                    </div>
	                </div>
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
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('permissions')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('permissions')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('permissions'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['support_only']; ?>
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
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('new_only')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('new_only')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('new_only'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['new_only']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	        <input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>"/>
	        <input type="hidden" name="task" value=""/>
	    </form>

	    <script type='text/javascript'>
        function GetSections() {
            $jMaQma.ajax({
                url:"index.php?option=com_maqmahelpdesk&task=wkfields_sections&id_workgroup=" + $jMaQma("#id_workgroup").val() + "&format=raw",
                success:function (data) {
                    $jMaQma("#sectionField select").html(data);
                }
            });
        }

        function GetCategories() {
            $jMaQma.ajax({
                url:"index.php?option=com_maqmahelpdesk&task=wkfields_categories&id_workgroup=" + $jMaQma("#id_workgroup").val() + "&format=raw",
                success:function (data) {
                    $jMaQma("#categoryField").html(data);
                }
            });
        }
	    </script><?php
	}
}
