<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
/*
class FSJ_PickTable
{
	var $xml = null;
	function FSJ_PickTable($xmlfile)
	{
		if (!file_exists($xmlfile))
			return;
		$this->xml = simplexml_load_file($xmlfile);
	}
	
	function LoadData()
	{
		if (!$this->xml)
			return;
			
		$qry = (string)$this->xml->sql;
		$where = array();
		
		if ($this->xml->where)
		{
			foreach ($this->xml->where as $w)
			{
				$where[] = (string)$w;
			}
		}
		
		if ($this->xml->use_auth)
		{
			// sort out which articles the user can view here, based on published, access, author
			// sort published out here	
			$published = (string)$this->xml->use_auth->attributes()->published;
			$access = (string)$this->xml->use_auth->attributes()->access;
			$author = (string)$this->xml->use_auth->attributes()->author;
			$where[] = "{$published} = 1";
		}
		
		if (count($where) > 0)
		{
			$qry .= " WHERE " . implode(" AND ",$where);	
		}
		
		$this->order = JRequest::getVar('order','');
		$this->orderdir = JRequest::getVar('orderdir','ASC');
		
		if ($this->order == "" && $this->xml->ordering)
			$this->order = (string)$this->xml->ordering;
			
		if ($this->order)
		{
			$qry .= " ORDER	BY {$this->order} {$this->orderdir} ";
		}
		
		$db = JFactory::getDBO();
		$db->setQuery($qry);
		
		//echo "Qry : $qry<br>";
		$db->query();
		$this->num_rows = $db->getNumRows();
		
		$mainframe = JFactory::getApplication();
		$this->limit = $mainframe->getUserStateFromRequest('global.list.limitpick', 'limit', 10, 'int');
		$this->limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		
		$this->pagination = new JPagination($this->num_rows, $this->limitstart, $this->limit );
		$db->setQuery($qry, $this->limitstart, $this->limit);
		
		$this->data = $db->loadObjectList();
		//echo $qry."<br>";
		//print_p($this->data);
	}
	
	function Process()
	{
		if (!$this->xml)
			return;
	}
	
	function Display()
	{
		if (!$this->xml)
			return;
		
		// output header
?>
	<table width="100%" class="fsj_table">
		<thead>
			<tr>
				<th width="5">#</th>
				<th width="20">
   					<input type="checkbox" id="toggle" value="" onclick="checkAll(<?php echo count( $this->data ); ?>);" />
				</th>
<?php foreach ($this->xml->displayfields->field as $field): ?>
				<th><?php echo JHTML::_('grid.sort',  $field->attributes()->id, $field->attributes()->sort, $this->orderdir, $this->order ); ?></th>
<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
<?php

    $k = 0;
    for ($i=0, $n=count( $this->data ); $i < $n; $i++)
    {
        $row = $this->data[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->id );
        
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $row->id; ?>
            </td>
			<td>
				<?php echo $checked; ?>
			</td>
<?php foreach ($this->xml->displayfields->field as $field): ?>
			<td>
				<?php 
				if ($field->attributes()->link)
				{
					echo "<a href='#' class='pick_link' id='pick_{$row->id}'>";
				}
				$field_name = (string)$field->attributes()->name; 
				if ((string)$field->attributes()->type == "yesno")
				{
					echo FSJ_Helper::GetYesNoText($row->$field_name);
				} else {
					echo $row->$field_name; 
				}
				if ($field->attributes()->link)
				{
					echo "</a>";
				}
				?>
			</td>
<?php endforeach; ?>
		</tr>
        <?php
        $k = 1 - $k;
    }
    ?>		
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>
	<input type="submit" id="relsubmit" value="<?php echo JText::_($this->buttontext); ?>" />
	<input type="submit" id="relcancel" value="<?php echo JText::_('JCANCEL'); ?>" />

<input type="hidden" name="order" id="order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $this->orderdir; ?>" />
<input type="hidden" name="boxchecked" id='boxchecked' value="0" />
	
<script>
jQuery(document).ready(function () {
	jQuery('#relsubmit').click(function (ev) {
		ev.preventDefault();
		if (jQuery('#boxchecked').val() < 1)
		{
			alert("<?php echo JText::_('FSJ_ATTACH_PLEASE_SELECT'); ?>");
		} else {
			pickMultiple(<?php echo count( $this->data ); ?>);
		}
	});
	jQuery('#relcancel').click(function (ev) {
		ev.preventDefault();
		window.parent.TINY.box.hide();
	});
});

function tableOrdering(field, order)
{
	jQuery('#order').val(field);
	jQuery('#orderdir').val(order);
	jQuery('#pickRelForm').submit();
}	
function checkAll(count)
{
	var checked = false;
	if (jQuery('#toggle').attr('checked'))
		checked = true;
	for (var i = 0 ; i < count ; i++)
	{
		jQuery('#cb'+i).attr('checked',checked);
	}	
	
	if (checked)
	{
		jQuery('#boxchecked').val(count);			
	} else {
		jQuery('#boxchecked').val(0);			
	}
}			

function isChecked(checked)
{
	if (checked)
	{
		jQuery('#boxchecked').val(parseInt(jQuery('#boxchecked').val()) + 1);			
	} else {
		jQuery('#boxchecked').val(parseInt(jQuery('#boxchecked').val()) - 1);	
	}				
}

jQuery(document).ready(function () {
	jQuery('.pick_link').click(function (ev) {
		ev.preventDefault();
		var id = jQuery(this).attr('id').split('_')[1];
		pickItem(id);
	});
});
</script>
<?php
	}	
}*/