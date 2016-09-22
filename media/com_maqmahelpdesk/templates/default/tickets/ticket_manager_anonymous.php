<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_tickets_manager');?></h2>

    <h4><?php echo JText::_('anonymous_track');?></h4>

    <p><?php echo JText::_('anonymous_track_desc');?></p>

    <form id="adminForm" name="adminForm" method="GET" action="<?php echo JRoute::_("index.php");?>">
        <?php echo JHtml::_('form.token'); ?>
        <fieldset>
            <table>
                <tr>
                    <td width="100"><?php echo JText::_('ticketid');?>:</td>
                    <td><input type="text" id="filter_ticketid" name="filter_ticketid" class="inputbox" size="10"
                               value="" tabindex=1/></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('email');?>:</td>
                    <td><input type="text" id="filter_email" name="filter_email" size="40" value="" tabindex=2/></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" class="btn" title="<?php echo JText::_('apply_filters');?>"
                                tabindex=3><?php echo JText::_('search');?></button>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="task" value="ticket_view"/>
    </form>

</div>