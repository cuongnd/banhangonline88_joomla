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
        <td class="heading2"><?php echo JText::_('contract_expired_head');?></td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="5" class="bodytable">
    <tr>
        <td width="936" class="bodytext"><p><?php echo JText::_('dear');?>&nbsp;%manager%,</p>

            <p><?php echo JText::_('contract_expired_intro');?></p>
            <br/></td>
    </tr>
    <tr>
        <td align="left" class="bodytext">&nbsp;</td>
    </tr>
    <tr>
        <td class="footer">
            <hr noshade="noshade" size="1" color="#666" width="100%"/>
            <br/>
            <?php echo JText::_('email_footer');?></td>
    </tr>
</table>
</html>
