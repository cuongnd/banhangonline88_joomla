<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $announce->introtext; ?></h2>

    <table width="100%">
        <tr>
            <td><?php echo JText::_('date');?>:</td>
            <td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($announce->date));?></td>
        </tr>
        <tr>
            <td><?php echo JText::_('subject');?>:</td>
            <td><?php echo $announce->introtext;?></td>
        </tr>
        <tr>
            <td valign="top"><?php echo JText::_('body');?>:</td>
            <td><?php echo $announce->bodytext;?></td>
        </tr>
        <tr>
            <td><?php echo JText::_('urgent');?>:</td>
            <td><?php echo ($announce->urgent ? '<span class="lbl lbl-important">' . JText::_("JYES") . '</span>' : '<span class="lbl lbl-success">' . JText::_("JNO") . '</span>');?></td>
        </tr>
    </table>

</div>