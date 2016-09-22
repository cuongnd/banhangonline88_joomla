<div class="maqmahelpdesk container-fluid">

    <table width="100%" class="table table-striped table-bordered" cellspacing="0">
        <thead>
        <tr>
            <th><?php echo JText::_('name');?></th>
            <th width="50"><?php echo JText::_('default');?></th>
        </tr>
        </thead>
        <tbody><?php
        $i = 0;
        foreach ($rows as $row):?>
        <tr class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>">
            <td>
                <a href="javascript:;" onclick="ViewEdit(<?php echo $row->id;?>);"><?php echo $row->name;?></a>
                &nbsp;
                <a href="javascript:;" onclick="ViewDelete(<?php echo $row->id;?>);"><img
                    src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/delete.png"/></a>
            </td>
            <td width="50"
                align="center"><?php echo ($row->default ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/ok.png" />' : '');?></td>
        </tr><?php
            $i++;
        endforeach;?>
        </tbody>
    </table>

</div>