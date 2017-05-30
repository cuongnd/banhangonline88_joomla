<html>
<style type="text/css">
    .heading1 {
        background-color: #E67713;
        color: #FEE3AB;
        font-weight: bold;
        font-family: Lucida Sans, Century Gothic, Arial, Helvetica, sans-serif;
        font-size: 24px;
    }

    .heading2 {
        background-color: #000764;
        color: #FFFFFF;
        font-size: 18px;
        font-family: Arial, Trebuchet MS, Verdana;
    }

    .bodytable {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
    }

    .footer {
        background-color: #E0E0E0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
    }

    .bodytext {
        font-size: 10px;
    }
</style>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td colspan="2" class="heading1"><?php echo JText::_('tickets_addon_title');?></td>
    </tr>
    <tr>
        <td colspan="2" class="heading2"><?php echo JText::_('tickets_total');?>: <b><?php echo $tickets; ?></b></td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="5" bgcolor="#666" class="bodytable">
    <tr>
        <th bgcolor="#ccc"><?php echo JText::_('date_created');?></th>
        <th bgcolor="#ccc"><?php echo JText::_('duedate');?></th>
        <th bgcolor="#ccc"><?php echo JText::_('ticketid');?></th>
        <th bgcolor="#ccc"><?php echo JText::_('subject');?></th>
        <th bgcolor="#ccc"><?php echo JText::_('workgroup');?></th>
    </tr>
    <?php echo $feed_summary; ?>
</table>
</html>