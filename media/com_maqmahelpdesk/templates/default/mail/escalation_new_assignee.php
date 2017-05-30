<html>
<style type="text/css">
    .heading2 {
        background-color: #FFFFFF;
        color: #CC0000;
        font-size: 18px;
        font-family: Arial, Trebuchet MS, Verdana;
        line-height: 150%;
        text-align: left;
    }

    .bodytable {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
    }

    .footer {
        background-color: #FFFFFF;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        color: #666;
        padding-top: 50px;
    }

    td.bodytext {
        font-size: 11px;
    }

    th.bodytext {
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        background-color: #F5F5F5;
    }
</style>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td class="heading2"><?php echo JText::_('tkt_assigned_new_assignee_head');?></td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="3" cellpadding="5" class="bodytable">
    <tr>
        <td colspan="2" class="bodytext"><?php echo JText::_('dear');?>
            &nbsp;%assigned%,<br/><br/><br/><?php echo JText::_('tkt_assigned_new_assignee_intro');?><br/><br/><br/>
        </td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('ticketid');?></th>
        <td width="99%" valign="top" class="bodytext"><a href="%tkt_url%" target="_blank">#%ticket%</a></td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('chng_date');?></th>
        <td width="99%" valign="top" class="bodytext">%cur_datelong%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('chng_author');?></th>
        <td width="99%" valign="top" class="bodytext">%chng_author%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('tpl_assignedto');?></th>
        <td width="99%" valign="top" class="bodytext">%assigned%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('tpl_last');?>
            &nbsp;<?php echo JText::_('act_summary');?>&nbsp;(<?php echo JText::_('tpl_ifany');?>)
        </th>
        <td width="99%" valign="top" class="bodytext">%rpl_summary%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('tpl_last');?>
            &nbsp;<?php echo JText::_('act_msg');?>&nbsp;(<?php echo JText::_('tpl_ifany');?>)
        </th>
        <td width="99%" valign="top" class="bodytext">%rpl_msg%</td>
    </tr>
    <tr>
        <td colspan="2" class="bodytext">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" class="bodytext">&nbsp;</td>
    </tr>
    <tr>
        <th colspan="2" class="bodytext"><?php echo JText::_('tpl_other_details');?></th>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('user');?>
        </th>
        <td width="99%" align="left" valign="top" class="bodytext">%tkt_user% (<a href="mailto:%tkt_user_email%">%tkt_user_email%</a>)
        </td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('tpl_client');?></th>
        <td width="99%" align="left" valign="top" class="bodytext">%tkt_client%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('status');?></th>
        <td width="99%" align="left" valign="top" class="bodytext">%status%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('category');?></th>
        <td width="99%" align="left" valign="top" class="bodytext">%category%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('priority');?>
        </th>
        <td width="99%" align="left" valign="top" class="bodytext">%priority%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('tpl_assignedto');?></th>
        <td width="99%" align="left" valign="top" class="bodytext">%assigned%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('tpl_original');?>
            &nbsp;<?php echo JText::_('tpl_tktsubj');?></th>
        <td width="99%" align="left" valign="top" class="bodytext">%tkt_subj%</td>
    </tr>
    <tr>
        <th align="left" valign="top" nowrap class="bodytext"><?php echo JText::_('tpl_original');?>
            &nbsp;<?php echo JText::_('tpl_tktmsg');?></th>
        <td width="99%" align="left" valign="top" class="bodytext">%tkt_msg%</td>
    </tr>
    <tr>
        <td colspan="2" align="left" class="bodytext">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" class="footer">
            <hr noshade="noshade" size="1" color="#666" width="100%"/>
            <br/>
            <?php echo JText::_('email_footer');?></td>
    </tr>
</table>
</html>
