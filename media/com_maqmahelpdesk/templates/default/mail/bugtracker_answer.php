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
    }

    td.bodytext {
        font-size: 11px;
    }

    th.bodytext {
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        background-color: #E4E4E4
    }
</style>
<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#333333">
    <tr>
        <td class="heading2"><?php echo JText::_('bugtracker_mail_subject_answer');?></td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="5" class="bodytable">
    <tr>
        <th align="left" nowrap class="bodytext"><?php echo JText::_('title');?>:</th>
        <td width="851" class="bodytext"><a href="[url]" target="_blank">[title]</a></td>
    </tr>
    <tr>
        <th align="left" nowrap class="bodytext"><?php echo JText::_('description');?>:</th>
        <td class="bodytext">[description]</td>
    </tr>
    <tr>
        <th align="left" nowrap class="bodytext"><?php echo JText::_('answer');?>:</th>
        <td class="bodytext">[answer]</td>
    </tr>
    <tr>
        <td colspan="2" class="footer">
            <br/><br/>
            <hr noshade="noshade" size="1" color="#666666"/>
            <p><?php echo JText::_('email_footer');?></p></td>
    </tr>
</table>
</html>
