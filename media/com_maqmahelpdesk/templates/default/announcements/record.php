<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $announce->introtext; ?> <?php echo ($announce->urgent ? '<span class="lbl lbl-important">' . JText::_("URGENT") . '</span>' : '');?></h2>

    <div class="w100 h30">
        <div class="w50 tal fllf"><em><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($announce->date));?></em></div>
        <div class="w50 tar flrg">
	        <a
            href="javascript:void window.open('<?php echo $link;?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');"
            class="btn"><?php echo JText::_('announce_print');?></a>
        </div>
    </div>

    <div class="tal"><?php echo $announce->bodytext;?></div>

</div>