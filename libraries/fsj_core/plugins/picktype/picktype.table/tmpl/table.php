<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php $this->OutputTable(); ?>

<script>

function pickMultiple(count)
{
	var ids = new Array();
	var titles = new Array();
	for (var i = 0 ; i < count ; i++)
	{
		var cb = $fsj('#cb'+i);
		if (cb.attr('checked'))
		{
			ids[ids.length] = cb.val();
			titles[titles.length] = jQuery('#title_' + cb.val()).text();
		}
	}
	
	window.parent.Add<?php echo $this->id; ?>Items('<?php echo $this->pluginid; ?>',ids, titles);
}

function pickItem(id)
{
	var ids = new Array();
	ids[ids.length] = id;
	
	var titles = new Array();
	titles[titles.length] = jQuery('#title_' + id).text();
	
	window.parent.Add<?php echo $this->id; ?>Items('<?php echo $this->pluginid; ?>',ids, titles);
}

</script>
