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
        <td class="heading2"><?php echo JText::_('tkt_new_comment_head');?></td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="5" class="bodytable">
    <tr>
        <td colspan="2" class="bodytext">
            <p><?php echo JText::_('tkt_new_comment_intro');?>: </p>
            <br/></td>
    </tr>
    <tr>
        <th align="left" nowrap class="bodytext"><?php echo JText::_('title');?>:</th>
        <td width="851" class="bodytext"><a href="%url" target="_blank">%kb_title</a></td>
    </tr>
    <tr>
        <th align="left" nowrap class="bodytext"><?php echo JText::_('url');?>:</th>
        <td class="bodytext"><a href="%url" target="_blank">%url</a></td>
    </tr>
    <tr>
        <td colspan="2" class="bodytext"></td>
    </tr>
    <tr>
        <th width="85" align="left" nowrap class="bodytext"><?php echo JText::_('comment');?></th>
        <td class="bodytext">%kb_comment</td>
    </tr>
    <tr>
        <td colspan="2" class="footer">
            <br/><br/>
            <hr noshade="noshade" size="1" color="#666666"/>
            <p><?php echo JText::_('email_footer');?></p></td>
    </tr>
</table>
</html>
