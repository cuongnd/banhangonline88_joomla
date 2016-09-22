<div class="maqmahelpdesk container-fluid">

    <form id="viewform" name="viewform" action="recebe.php" method="post" onsubmit="return false;">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" id="option" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" id="task" name="task" value="ticket_saveview"/>
        <input type="hidden" id="id_workgroup" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" id="id" name="id" value="<?php echo $row->id;?>"/>
        <input type="hidden" id="tmpl" name="tmpl" value="component"/>
        <input type="hidden" id="format" name="format" value="raw"/>

        <table>
            <tr>
                <td><?php echo JText::_('name');?>:</td>
                <td><input type="text" id="name" name="name" value="<?php echo $row->name;?>" style="width:200px;"/>
                </td>
            </tr>
            <tr>
                <td><?php echo JText::_('ordering');?>:</td>
                <td>
                    <select id="ordering" name="ordering" style="width:150px;">
                        <option value="t.duedate"><?php echo JText::_('duedate');?></option>
                        <option value="t.status"><?php echo JText::_('tpl_status');?></option>
                        <option value="t.subject"><?php echo JText::_('subject');?></option>
                        <option value="t.date"><?php echo JText::_('DATE_CREATED');?></option>
                        <option value="t.last_update"><?php echo JText::_('DATE_UPDATED');?></option>
                    </select>
                    <select id="orderby" name="orderby" style="width:70px;">
                        <option value="ASC"><?php echo JText::_('ORDER_ASC');?></option>
                        <option value="DESC"><?php echo JText::_('ORDER_DESC');?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo JText::_('default');?>:</td>
                <td><select id="default" name="default" style="width:220px;">
                    <option value="0"><?php echo JText::_('MQ_NO');?></option>
                    <option value="1"><?php echo JText::_('MQ_YES');?></option>
                </select>
                </td>
            </tr>
        </table>

        <table cellspacing="0" cellpadding="5">
            <tr class="dynfields">
                <td class="operatorp">
                    <select name="operator[]" style="width:70px;">
                        <option value="AND"><?php echo JText::_('view_and');?></option>
                        <option value="OR"><?php echo JText::_('view_or');?></option>
                    </select>
                </td>
                <td class="fieldp">
                    <select name="field[]" style="width:125px;" onchange="SetValueOptions(this);">
                        <option value=""></option>
                        <option value="t.id_status"><?php echo JText::_('tpl_status');?></option>
                        <option value="s.status_group"><?php echo JText::_('status_group');?></option>
                        <option value="t.assign_to"><?php echo JText::_('select_assign');?></option>
                        <option value="t.id_workgroup"><?php echo JText::_('workgroup');?></option>
                        <option value="t.id_category"><?php echo JText::_('category');?></option>
                        <option value="t.approved"><?php echo JText::_('approved');?></option>
                    </select>
                </td>
                <td class="arithmeticp">
                    <select name="arithmetic[]" style="width:125px;">
                        <option value=""></option>
                        <option value="="><?php echo JText::_('equal_to');?></option>
                        <option value="!="><?php echo JText::_('not_equal');?></option>
                        <option value="LIKE"><?php echo JText::_('contais');?></option>
                    </select>
                </td>
                <td class="valuep">

                </td>
                <td>
                    <a href="javascript:;" onclick="RemoveParameter(this);"><img
                        src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/circle_remove.png"
                        border="0" alt=""/></a>
                </td>
            </tr>
        </table>
        <table id="dynafields" cellspacing="0" cellpadding="5"></table>
        <p><a href="javascript:;" onclick="AddParameter()"
              class="btn btn-success"><?php echo JText::_('add_parameter');?></a></p>
    </form>

    <script type="text/javascript"><?php
    if ($row->id) {
        for ($i = 0; $i < count($operators); $i++) {
            switch ($fields[$i]) {
                case 't.id_status';
                    $ftype = 'StatusOptions';
                    break;
                case 't.id_priority';
                    $ftype = 'PriorityOptions';
                    break;
                case 't.assign_to';
                    $ftype = 'AssignOptions';
                    break;
                case 't.id_workgroup';
                    $ftype = 'WorkgroupOptions';
                    break;
                case 't.id_category';
                    $ftype = 'CategoryOptions';
                    break;
                case 's.status_group';
                    $ftype = 'StatusGroupOptions';
                    break;
            }

            if (!$i) {
                ?>
            $tableparameter = $jMaQma(".dynfields:first");
            $tableparameter.find(".operatorp select").val('<?php echo $operators[$i]; ?>');
            $tableparameter.find(".fieldp select").val('<?php echo $fields[$i]; ?>');
            $tableparameter.find(".arithmeticp select").val('<?php echo $arithmetics[$i]; ?>');
            $tableparameter.find(".valuep").html(<?php echo $ftype; ?>);
            $tableparameter.find(".valuep select").val('<?php echo $values[$i]; ?>');<?php
            } else {
                ?>
            $tableparameter = $jMaQma(".dynfields:first").clone().appendTo("#dynafields");
            $tableparameter.find(".operatorp select").val('<?php echo $operators[$i]; ?>');
            $tableparameter.find(".fieldp select").val('<?php echo $fields[$i]; ?>');
            $tableparameter.find(".arithmeticp select").val('<?php echo $arithmetics[$i]; ?>');
            $tableparameter.find(".valuep").html(<?php echo $ftype; ?>);
            $tableparameter.find(".valuep select").val('<?php echo $values[$i]; ?>');<?php
            }
        }
    } ?>

    if ($jMaQma("#OpenViews .modal-body #id").val() > 0) {
        $jMaQma("#OpenViews .modal-body #ordering").val('<?php echo $row->ordering;?>');
        $jMaQma("#OpenViews .modal-body #orderby").val('<?php echo $row->orderby;?>');
        $jMaQma("#OpenViews .modal-body #default").val(<?php echo $row->default;?>);
        $jMaQma("#OpenViews .modal-body #viewtype").val('<?php echo $row->viewtype;?>');
    }
    </script>

</div>