<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('tickets_analysis'); ?></h2>

    <form id="adminForm" name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" id="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="task" value="ticket_analysis"/>
        <input type="hidden" name="execute" value="1"/>
        <input type="hidden" id="page" name="page" value="0"/>

        <div>
            <div style="float:left;width:68%;">
                <p><span style="font-weight:bold;font-size:16px;"><?php echo JText::_('filters');?></span></p>
                <table>
                    <tr style="height:30px;">
                        <td><label for="filter_workgroup"><?php echo JText::_('workgroup');?>:</label></td>
                        <td><?php echo $lists['workgroup'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_client"><?php echo JText::_('client_name');?>:</label></td>
                        <td><?php echo $lists['client'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_status"><?php echo JText::_('status');?>:</label></td>
                        <td><?php echo $lists['status'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_status_group"><?php echo JText::_('status_group');?>:</label></td>
                        <td><?php echo $lists['status_group'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_assign"><?php echo JText::_('tpl_assignedto');?>:</label></td>
                        <td><?php echo $lists['assign'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_priority"><?php echo JText::_('priority');?>:</label></td>
                        <td><?php echo $lists['priority'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_category"><?php echo JText::_('category');?>:</label></td>
                        <td><?php echo $lists['category'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_year"><?php echo JText::_('year');?>:</label></td>
                        <td><?php echo $lists['year'];?></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><label for="filter_month"><?php echo JText::_('month');?>:</label></td>
                        <td><?php echo $lists['month'];?></td>
                    </tr>
                </table>
            </div>
            <div style="float:left;width:28%;">
                <p><span style="font-weight:bold;font-size:16px;"><?php echo JText::_('columns');?></span></p>

                <table>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_ticketid" name="col_ticketid"
                                   value="1" <?php echo ($col_ticketid ? 'checked' : '')?>/></td>
                        <td><label for="col_ticketid"><?php echo JText::_('ticketid');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_workgroup" name="col_workgroup"
                                   value="1" <?php echo ($col_workgroup ? 'checked' : '')?>/></td>
                        <td><label for="col_workgroup"><?php echo JText::_('workgroup');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_subject" name="col_subject"
                                   value="1" <?php echo ($col_subject ? 'checked' : '')?>/></td>
                        <td><label for="col_subject"><?php echo JText::_('subject');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_category" name="col_category"
                                   value="1" <?php echo ($col_category ? 'checked' : '')?>/></td>
                        <td><label for="col_category"><?php echo JText::_('category');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_client" name="col_client"
                                   value="1" <?php echo ($col_client ? 'checked' : '')?>/></td>
                        <td><label for="col_client"><?php echo JText::_('client_name');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_user" name="col_user"
                                   value="1" <?php echo ($col_user ? 'checked' : '')?>/></td>
                        <td><label for="col_user"><?php echo JText::_('user');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_duedate" name="col_duedate"
                                   value="1" <?php echo ($col_duedate ? 'checked' : '')?>/></td>
                        <td><label for="col_duedate"><?php echo JText::_('duedate');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_status" name="col_status"
                                   value="1" <?php echo ($col_status ? 'checked' : '')?>/></td>
                        <td><label for="col_status"><?php echo JText::_('status');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_assign" name="col_assign"
                                   value="1" <?php echo ($col_assign ? 'checked' : '')?>/></td>
                        <td><label for="col_assign"><?php echo JText::_('tpl_assignedto');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_date_created" name="col_date_created"
                                   value="1" <?php echo ($col_date_created ? 'checked' : '')?>/></td>
                        <td><label for="col_date_created"><?php echo JText::_('date_created');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_message" name="col_message"
                                   value="1" <?php echo ($col_message ? 'checked' : '')?>/></td>
                        <td><label for="col_message"><?php echo JText::_('message');?></label></td>
                    </tr>
                    <tr style="height:30px;">
                        <td><input type="checkbox" id="col_last_message" name="col_last_message"
                                   value="1" <?php echo ($col_last_message ? 'checked' : '')?>/></td>
                        <td><label for="col_last_message"><?php echo JText::_('last_message');?></label></td>
                    </tr>
                </table>
            </div>
            <div class="clear"></div>
        </div>
        <div style="width:100%;margin-bottom:10px;">
            <?php if ($execute): ?>
            <div style="width:48%;float:left;text-align:left;">
                <a href="<?php echo JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&task=ajax_analysis&filter_workgroup=' . $filter_workgroup . '&filter_client=' . $filter_client . '&filter_status=' . $filter_status . '&filter_assign=' . $filter_assign . '&filter_priority=' . $filter_priority . '&filter_category=' . $filter_category . '&filter_year=' . $filter_year . '&filter_month=' . $filter_month . '&col_ticketid=' . $col_ticketid . '&col_workgroup=' . $col_workgroup . '&col_subject=' . $col_subject . '&col_category=' . $col_category . '&col_client=' . $col_client . '&col_user=' . $col_user . '&col_duedate=' . $col_duedate . '&col_status=' . $col_status . '&col_assign=' . $col_assign . '&col_date_created=' . $col_date_created . '&col_message=' . $col_message . '&col_last_message=' . $col_last_message . '&sessionid=' . session_id() . '&id_user=' . $user->id);?>"
                   title="<?php echo JText::_('export');?>"><img
                    src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
                    alt="<?php echo JText::_('export');?>" align="absmiddle"
                    border="0"/> <?php echo JText::_('export');?></a>
            </div>
            <?php endif;?>
            <div style="width:48%;float:right;text-align:right;">
                <button type="submit" class="btn"
                        title="<?php echo JText::_('search');?>"><?php echo JText::_('search');?></button>
            </div>
            <div style="clear:both;"></div>
        </div>
    </form>

</div>