<html>
<body leftmargin="0" marginheight="0" marginwidth="0" offset="0"
      style="margin: 0; padding: 0; font-family: arial, helvetica, sans-serif; " topmargin="0">
<div id="styles"
     style="margin-left:auto; margin-right:auto; width: 750px; font-size: 13px; font-family: arial, helvetica, sans-serif;">
    <div id="notification"
         style="padding: 10px 0; color: #999; font-size: 11px; text-align: center; font-family: arial, helvetica, sans-serif;">
        <?php echo sprintf(JText::_('mail_auto_msg'), JURI::root());?>
    </div>
    <div id="email_border1"
         style="border: 3px solid #ddd;  background: white; font-family: arial, helvetica, sans-serif; ">
        <div id="email_border3" style="border: 3px solid #eee;">
            <div id="padding" style="padding: 0 20px 0 20px;">
                <div id="div" style="padding: 10px 0 0 0;">
                    [intro]
                </div>
                <div id="div" style="padding: 0 0 20px 0;">
                    <table style="width:100%; font-family: arial, helvetica, sans-serif; font-size: 13px;">
                        <tr>
                            <td style="padding:3px;background:#eee;vertical-align:top;width:30%;"><?php echo JText::_('message');?></td>
                            <td style="padding:3px;background:#fff;vertical-align:top;">[message]</td>
                        </tr>
                        <tr>
                            <td style="padding:3px;background:#eee;vertical-align:top;width:30%;"><?php echo JText::_('workgroup');?></td>
                            <td style="padding:3px;background:#fff;vertical-align:top;">[department]</td>
                        </tr>
                        <tr>
                            <td style="padding:3px;background:#eee;vertical-align:top;width:30%;"><?php echo JText::_('status');?></td>
                            <td style="padding:3px;background:#fff;vertical-align:top;">[status]</td>
                        </tr>
                        <tr>
                            <td style="padding:3px;background:#eee;vertical-align:top;width:30%;"><?php echo JText::_('category');?></td>
                            <td style="padding:3px;background:#fff;vertical-align:top;">[category]</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="help"
                 style="padding: 10px 20px; background: #feffc7; border-top: 1px solid #ccc; text-align: center; font-size: 12px;">
                <?php echo JText::_('email_footer');?>
                <div id="div" style="padding: 3px 0 0 0; text-align: center;">
						<span>
							<a href="[helpdesk]" target="_blank"><?php echo JText::_('online_support');?></a>
						</span>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>