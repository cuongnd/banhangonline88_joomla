<html>
<head>
    <style type="text/css">
        table {
            font-size: 11px;
            font-family: "Arial";
            padding: 0;
            margin: 0;
        }

        table tr.header {
            background: #666;
            color: #fff;
        }

        table tr.odd {
            background: #fff;
        }

        table tr.even {
            background: #efefef;
        }

        table td {
            vertical-align: top;
        }

        span {
            color: #a0a0a0;
        }
    </style>
</head>
<body leftmargin="0" marginheight="0" marginwidth="0" offset="0"
      style="margin: 0, padding: 0, font-family: arial, helvetica, sans-serif; " topmargin="0">
<div id="styles"
     style="margin-left:auto; margin-right:auto; width: 540px; font-size: 13px; font-family: arial, helvetica, sans-serif;">
    <div id="email_border1"
         style="border: 3px solid #ddd;  background: white; font-family: arial, helvetica, sans-serif; ">
        <div id="email_border3" style="border: 3px solid #eee;">
            <div id="padding" style="padding: 20px 20px 30px 20px;">
                <div id="big" style="font-weight: bold; font-size: 26px; text-align: center;">
                    <?php echo JText::_('activities_subject');?>
                </div>
                <div id="big"
                     style="font-weight: normal; font-size: 16px; padding: 5px 0; color: #888; text-align: center;">
                    <?php echo date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));?>
                </div>
                <div id="div" style="font-size: 13px; margin: 20px 0; color: #444; ">
                    <table width="100%" cellpadding="5" cellspacing="0">
                        <thead>
                        <tr class="header">
                            <th>&nbsp;</th>
                            <th><?php echo JText::_('times_hours');?></th>
                            <th><?php echo JText::_('select_user');?></th>
                            <th><?php echo JText::_('ticket_number');?></th>
                            <th><?php echo JText::_('activity_details');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php echo $feed_summary; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>