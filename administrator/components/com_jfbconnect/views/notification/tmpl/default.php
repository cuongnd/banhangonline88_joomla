<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="sourcecoast">
    <form action="index.php" method="get" name="adminForm" id="adminForm">
        <table class="jfbcAdminTableFilters">
            <tr>
                <td class="jfbcAdminTableFiltersSearch">
                    <input type="text" name="search" id="search" value="<?php echo $this->lists['search'] ?>" class="filter-search btn-group pull-left" title="<?php echo JText::_('COM_JFBCONNECT_FILTER'); ?>" placeholder="<?php echo JText::_('COM_JFBCONNECT_FILTER'); ?>" />
                    <div class="btn-group pull-left">
                        <button class="btn tip" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_GO');?>" id="jfbcSubmitButton">
                            <?php
                            if(defined('SC30')):
                            echo '<i class="icon-search"></i>';
                            endif; //SC30
                            if(defined('SC16')):
                            echo JText::_('COM_JFBCONNECT_BUTTON_GO');
                            endif; //SC16
                            ?>
                        </button>
                        <?php
                        $resetJavascript = "document.getElementById('search').value='';";
                        $resetJavascript .= "this.form.submit();";
                        ?>
                        <button class="btn tip" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_RESET');?>" id="jfbcResetButton" onclick="<?php echo $resetJavascript; ?>">
                            <?php
                            if(defined('SC30')):
                            echo '<i class="icon-remove"></i>';
                            endif; //SC30
                            if(defined('SC16')):
                            echo JText::_('COM_JFBCONNECT_BUTTON_RESET');
                            endif; //SC16
                            ?>
                        </button>
                    </div>
                </td>
            </tr>
        </table>
        <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_FBID_LABEL'), 'fb_request_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_FB_USER_FROM_LABEL'), 'fb_user_from', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th class="title"><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_JOOMLA_USER_FROM_LABEL'), 'joomla_user_from', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_FB_USER_TO_LABEL'), 'fb_user_to', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th class="title"><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_JOOMLA_USER_TO_LABEL'), 'joomla_user_to', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th class="title"><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_REQUEST_ID_LABEL'), 'jfbc_request_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_STATUS_LABEL'), 'status', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('JGLOBAL_CREATED'), 'created', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('JGLOBAL_MODIFIED'), 'modified', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_NOTIFICATION_ID_LABEL'), 'id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($this->rows)
                foreach ($this->rows as $key => $row):
                    $toUser = JFactory::getUser($row->joomla_user_to);
                    $fromUser = JFactory::getUser($row->joomla_user_from);

                    $toLink = JRoute::_("index.php?option=com_users&view=user&task=user.edit&id=" . $row->joomla_user_to);
                    $fromLink = JRoute::_("index.php?option=com_users&view=user&task=user.edit&id=" . $row->joomla_user_from);
                    $toHref = '';
                    $fromHref = '';
                    if ($row->joomla_user_to)
                        $toHref = '<a target="_blank" href="' . $toLink . '">' . $toUser->name . '</a>';
                    if ($row->joomla_user_from)
                        $fromHref = '<a target="_blank" href="' . $fromLink . '">' . $fromUser->name . '</a>';
                    ?>

                <tr class="row<?php echo ($key % 2); ?>">
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $row->fb_request_id;?></td>
                    <?php if ($row->fb_user_from != -1) { ?>
                    <td><a target="_blank" href="http://www.facebook.com/profile.php?id=<?php print $row->fb_user_from; ?>"><img
                            src="https://graph.facebook.com/<?php echo $row->fb_user_from; ?>/picture?type=small"
                            width="50"/></a></td>
                    <?php } else { ?>
                    <td><?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_APP');?></td>
                    <?php } ?>
                    <td><?php echo $fromHref;?></td>
                    <td><a target="_blank"
                           href="http://www.facebook.com/profile.php?id=<?php print $row->fb_user_to; ?>"><img
                            src="https://graph.facebook.com/<?php echo $row->fb_user_to; ?>/picture?type=small" width="50"/></a>
                    </td>
                    <td><?php echo $toHref;?></td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=request&task=edit&cid=' . $row->jfbc_request_id); ?>"><?php echo $row->jfbc_request_id;?></a>
                    </td>
                    <td><?php if ($row->status == 0) echo JText::_('COM_JFBCONNECT_REQUEST_SENT');
                    else if ($row->status == 1) echo JText::_('COM_JFBCONNECT_REQUEST_READ');
                    else if ($row->status == 2) echo JText::_('COM_JFBCONNECT_REQUEST_EXPIRED');
                        ?>
                    </td>
                    <td><?php echo $row->created;?></td>
                    <td><?php echo $row->modified;?></td>
                    <td><?php echo $row->id; ?></td>
                </tr>
                    <?php endforeach; ?>
            <tr>
                <td colspan="12">
                    <div style="text-align:center">
                        <div style="margin: 0 5px;"><strong><?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_SENT_LABEL');?>:</strong> <?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_SENT_DESC');?></div>
                        <div style="margin: 0 5px;"><strong><?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_READ_LABEL');?>:</strong> <?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_READ_DESC');?></div>
                        <div style="margin: 0 5px;"><strong><?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_EXPIRED_LABEL');?>:</strong> <?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_EXPIRED_DESC');?></div>
                        <div><br/><strong><?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_NOTE_LABEL');?>:</strong> <?php echo JText::_('COM_JFBCONNECT_NOTIFICATION_NOTE_DESC');?></div>
                    </div>
                </td>
            </tr>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="12"><?php echo $this->page->getListFooter(); ?></td>
            </tr>
            </tfoot>
        </table>

        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="notification"/>
        <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>"/>
        <input type="hidden" name="requestid" value="<?php echo JRequest::getVar('requestid');?>"/>
        <input type="hidden" name="fbuserto" value="<?php echo JRequest::getVar('fbuserto');?>"/>
        <input type="hidden" name="fbuserfrom" value="<?php echo JRequest::getVar('fbuserfrom');?>"/>
        <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
        <input type="hidden" name="boxchecked" value="0"/>
    </form>
</div>
