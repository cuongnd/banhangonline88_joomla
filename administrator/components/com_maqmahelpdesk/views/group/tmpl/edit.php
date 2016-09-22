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
		$GLOBALS['title_group_form'] = ($_row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('usergroup'); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'group') {
                Joomla.submitform(pressbutton);
                return;
            }

            USERSObj = document.adminForm.id_user;
            var j = 0;
            h = 0;
            for (var j = 0; j < USERSObj.length; j++) {
                if (USERSObj[j].selected) {
                    h = h + 1;
                }
            }

            PrepareUsers()

            if (form.gname.value == "") {
                alert("<?php echo JText::_('group_required'); ?>");
            } else {
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }
        }

        function FillUsers() {
            GRPS1 = document.adminForm.users.value;
            GRPS = GRPS1.split(/\s*,\s*/);

            for (i = 0; i < document.adminForm.id_user.length; i++) {
                for (z = 0; z < GRPS.length; z++) {
                    if (document.adminForm.id_user[i].value == GRPS[z]) {
                        document.adminForm.id_user[i].selected = true;
                    }
                }
            }
        }

        function PrepareUsers() {
            USERSObj = document.adminForm.id_user;
            document.adminForm.users.value = '';
            USERSVal = '';

            var j = 0;
            for (var j = 0; j < USERSObj.length; j++) {
                if (USERSObj[j].selected) {
                    USERSVal = USERSVal + USERSObj[j].value + ",";
                }
            }

            document.adminForm.users.value = USERSVal.substring(0, USERSVal.length - 1);
        }

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script>

	    <form action="index.php" method="POST" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=client"><?php echo JText::_('clients_manager'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=group"><?php echo JText::_('groups'); ?></a>
	            <span><?php echo JText::_('manage'); ?></span>
	        </div>
	        <div class="contentarea pad5">
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
	                                   id="gname"
	                                   name="gname"
                                       class="span10"
	                                   value="<?php echo $_row->gname; ?>"
	                                   maxlength="50" />
	                        </div>
	                    </div>
	                </div>
	            </div>
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
	                            <textarea name="description"
                                          class="span10"
	                                      rows="5"
	                                      cols="45"><?php echo $_row->description;?></textarea>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('unregistered_users')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('unregistered_users')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('unregistered_users'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['unregister']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('default')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('default')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('default'); ?>
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
	                             data-original-title="<?php echo htmlspecialchars(JText::_('clients')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('clients')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('clients'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['clients']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="users" value="<?php echo $lists['group_users']; ?>"/>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="id" value="<?php echo $_row->id; ?>"/>
	        <input type="hidden" name="task" value=""/>
	    </form><?php
		if ($_row->id > 0) {
			echo "<script type='text/javascript'>FillUsers();</script>";
		}
	}
}
