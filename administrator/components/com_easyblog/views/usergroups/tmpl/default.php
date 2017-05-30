<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog.ready(function($){

	$.Joomla("submitbutton", function(action){

		if(action == 'remove')
		{
		    if(confirm("<?php echo JText::_('COM_EASYBLOG_BLOGGERS_DELETE_NOTICE_CONFIRMATION'); ?>"))
		    {
	            submitform( action );
		    }
		    else
		    {
				return false;
		    }
		}
		else
		{
		    $.Joomla("submitform", [action]);
		}
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="adminform-body">
<table class="adminlist table table-striped" cellspacing="1">
<thead>
	<tr>
		<th width="5"><?php echo JText::_( 'Num' ); ?></th>
		<th style="text-align: left;"><?php echo JText::_( 'Group Name' );?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->groups )
{
	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->groups); $i < $n; $i++)
	{
		$row	= $this->groups[ $i ];
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td>
			<?php echo ($i + 1 ); ?>
		</td>
		<td>
			<a href="javascript:void(0);" onclick="parent.insertGroup('<?php echo $row->id;?>','<?php echo $this->escape(addslashes($row->name));?>');"><?php echo $row->name;?></a>
		</td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
?>
</tbody>
</table>
</div>
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="users" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
