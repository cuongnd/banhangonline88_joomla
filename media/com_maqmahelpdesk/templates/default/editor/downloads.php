<script language="Javascript">
    <!--
    function insertDownloadLink() {
        var itemid = $jMaQma("#download_menu").val();
        var wkid = $jMaQma("#download_wk").val();
	    var html = 'index.php?option=com_maqmahelpdesk&Itemid=' + itemid + '&id_workgroup=' + wkid + '&task=downloads_product&id=' + $jMaQma("#id_product").val();
	    html = '<a href="' + html + '">Download ' + $jMaQma("#id_product option:selected").text() + '</a>';

        if ($jMaQma("#download_menu").val() == 0 || $jMaQma("#download_menu").val() == '' || $jMaQma("#download_menu").val() == null) {
            return false;
        }
        if ($jMaQma("#download_wk").val() == 0 || $jMaQma("#download_wk").val() == '' || $jMaQma("#download_wk").val() == null) {
            return false;
        }
        if ($jMaQma("#id_product").val() == 0 || $jMaQma("#id_product").val() == '' || $jMaQma("#id_product").val() == null) {
            return false;
        }

        window.parent.jInsertEditorText(html, '<?php echo preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', JRequest::getVar('e_name'));?>');
        window.parent.SqueezeBox.close();
    }
    //-->
</script>

<form name="selectdownloadlink" action="link" method="get">
    <?php echo JHtml::_('form.token'); ?>
    <div style="padding:10px;">
        <div style="width:100%;text-align:right;">
            <button type="button" onclick="insertDownloadLink();"><?php echo JText::_('add');?></button>
            <button type="button"
                    onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('cancel');?></button>
        </div>
        <fieldset class="adminform">
            <legend><?php echo JText::_('select_download');?></legend>
            <table class="admintable" cellspacing="1">
                <tr>
                    <td width="10%" valign="top" class="key">Menu:</td>
                    <td><?php echo $menus;?></td>
                    <td width="10%" valign="top" class="key"><?php echo JText::_("workgroup");?>:</td>
                    <td><?php echo $wks;?></td>
                    <td width="10%" valign="top" class="key"><?php echo JText::_("download");?>:</td>
                    <td><?php echo $products;?></td>
                </tr>
            </table>
        </fieldset>
    </div>
</form>