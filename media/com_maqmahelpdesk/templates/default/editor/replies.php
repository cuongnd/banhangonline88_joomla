<form name="selectdownloadlink" action="link" method="get">
    <?php echo JHtml::_('form.token'); ?>
    <div style="padding:10px;">
        <div style="width:100%;text-align:right;">
            <button type="button" onclick="insertDownloadLink();"><?php echo JText::_('add');?></button>
            <button type="button"
                    onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('cancel');?></button>
        </div>
        <fieldset class="adminform">
            <legend><?php echo JText::_('select_reply');?>:</legend>
            <?php echo $replies;?>
        </fieldset>
    </div>
</form>

<script language="Javascript">
    <!--
    function insertDownloadLink() {
        if ($jMaQma("#reply").val() == 0 || $jMaQma("#reply").val() == '') {
            return false;
        }

        var id = $jMaQma("#reply").val();

        $jMaQma.ajax({
            type:"POST",
            url:"<?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&task=ajax_reply&format=ajax&id=" + $jMaQma("#reply").val(),
            success:function (html) {
                window.parent.jInsertEditorText(html, '<?php echo preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', JRequest::getVar('e_name'));?>');
                window.parent.SqueezeBox.close();
            }
        });
    }
    //-->
</script>