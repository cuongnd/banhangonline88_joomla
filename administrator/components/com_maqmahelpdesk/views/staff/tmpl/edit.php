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
		$session = JFactory::getSession(); ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline pad5">
		<?php echo JHtml::_('form.token'); ?>
        <div class="contentarea pad5">
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="span2 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('user')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('staff_users_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('user'); ?>
		                    </span>
                        </div>
                        <div class="span10">
                            <input type="text" id="ac_me" name="ac_me" value="<?php echo $row->name; ?>" maxlength="100" class="span10" />
                            <input type="hidden" id="id_user" name="id_user" value="<?php echo $row->id_user; ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
	            <div class="span6">
	                <div class="row-fluid">
	                    <div class="span4 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('staff_wk_tooltip')); ?>">
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
	                         data-original-title="<?php echo htmlspecialchars(JText::_('can_delete')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('can_delete_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('can_delete'); ?>
		                    </span>
	                    </div>
	                    <div class="span8">
							<?php echo $lists['can_delete']; ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="row-fluid">
	            <div class="span6">
	                <div class="row-fluid">
	                    <div class="span4 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('support_staff_type')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('staff_type_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('support_staff_type'); ?>
		                    </span>
	                    </div>
	                    <div class="span8">
							<?php echo $lists['sup_usertype']; ?>
	                    </div>
	                </div>
	            </div>
	            <div class="span6">
	                <div class="row-fluid">
	                    <div class="span4 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('manage_bugtracker')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('manage_bugtracker_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('manage_bugtracker'); ?>
		                    </span>
	                    </div>
	                    <div class="span8">
							<?php echo $lists['bugtracker']; ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="row-fluid">
	            <div class="span6">
	                <div class="row-fluid">
	                    <div class="span4 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('support_level')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('support_level_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('support_level'); ?>
		                    </span>
	                    </div>
	                    <div class="span8">
							<?php echo $lists['level']; ?>
	                    </div>
	                </div>
	            </div>
	            <div class="span6">
	                <div class="row-fluid">
	                    <div class="span4 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('assign_category_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('category'); ?>
		                    </span>
	                    </div>
	                    <div class="span8" id="categoryField">
							<?php echo $lists['category']; ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="row-fluid">
	            <div class="span12">
	                <div class="row-fluid">
	                    <div class="span2 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('assign_notification')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('assign_notification_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('assign_notification'); ?>
		                    </span>
	                    </div>
	                    <div class="span10">
							<?php echo $lists['assign_report']; ?>
	                        <br/><?php echo $lists['assign_report_users']; ?>
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
        function showhidediv() {
            if ($jMaQma('#assign_report1').is(':checked')) {
                $jMaQma("#div_support_users").show();
            } else {
                $jMaQma("#div_support_users").hide();
            }
        }

        $jMaQma(document).ready(function () {
            $jMaQma("#ac_me").autocompletemqm("index.php?option=com_maqmahelpdesk&task=ajax_getuser&format=raw&session=<?php echo $session->getId();?>", {
                selectFirst:false,
                scroll:true,
                scrollHeight:300,
                formatItem:function (data, i, n, value) {
                    return '<img src="' + data[5] + '" width="32" height="32" align="left">' + data[1] + ' (' + data[4] + ')' + (data[3] != '' ? '<br><i>' + data[3] + '</i>' : '');
                },
                selectExecute:function (data) {
                    $jMaQma("#id_user").val(data[0]);
                    $jMaQma("#ac_me").val(data[1]);
                }
            });
            $jMaQma("#assign_report1").click(function () {
                showhidediv();
            });
            $jMaQma("#assign_report0").click(function () {
                showhidediv();
            });

            showhidediv();
        });

        function GetCategories() {
            $jMaQma.ajax({
                url:"index.php?option=com_maqmahelpdesk&task=staff_categories&id_user=" + $jMaQma("#id_user").val() + "&id_workgroup=" + $jMaQma("#id_workgroup").val() + "&format=raw",
                success:function (data) {
                    $jMaQma("#categoryField").html(data);
                }
            });
        }

        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'staff') {
                Joomla.submitform(pressbutton);
                return;
            }
            if ($jMaQma("#id_workgroup").val() == '0') {
                alert('<?php echo JText::_('select_workgroup');?>');
                return false;
            }
            if ($jMaQma("#id_user").val() == '' || $jMaQma("#id_user").val() == '0') {
                alert('<?php echo JText::_('select_user');?>');
                return false;
            }
            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
        }

        $jMaQma(document).ready(function () {
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script><?php
	}
}
