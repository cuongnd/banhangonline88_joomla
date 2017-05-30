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
<div class="adminform-head">
    <table class="adminform">
    	<tr>
    		<td width="50%">
    		  	<label><?php echo JText::_( 'COM_EASYBLOG_SEARCH' ); ?> :</label>
    			<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
    		</td>
    		<td width="50%" style="text-align: right;">
                <label><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FILTER_BY' ); ?> :</label>
                <?php echo $this->state; ?>
            </td>
    	</tr>
    </table>
</div>

<div class="adminform-body">
<div style="margin:10px;">
	<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=teamrequest'); ?>" class="button mt5 ml5"><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_VIEW_REQUEST'); ?></a>
</div>
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="5" style="text-align:center;">
			<?php echo JText::_( 'Num' ); ?>
		</th>
		<?php if( !$this->browse ){ ?>
		<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->teams ); ?>);" /></th>
		<?php }?>
		<th style="text-align: left;"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_TEAMBLOGS_TEAM_NAME', 'a.title', $this->orderDirection, $this->order ); ?></th>
		<?php if( !$this->browse ){ ?>
		<th width="50px" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></th>
		<?php } ?>
		<th width="150px" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_ACCESS' ); ?></th>
		<th width="50px" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS' ); ?></th>
		<th width="50px" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW' ); ?></th>
		<th width="50px" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->teams )
{
	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->teams); $i < $n; $i++)
	{
		$row = $this->teams[$i];

		$editLink 		= 'index.php?option=com_easyblog&amp;c=teamblogs&amp;task=edit&amp;id=' . $row->id;
		$previewLink	= rtrim( JURI::root() , "/" ) . "/" . JRoute::_("index.php?option=com_easyblog&view=teamblog&id=" . $row->id);
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td style="text-align:center;"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
		<?php if( !$this->browse ){ ?>
		<td width="7"><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
		<?php } ?>
		<td>
			<?php if( $this->browse ){ ?>
				<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');">
			<?php } else {?>
				<a href="<?php echo $editLink;?>">
			<?php } ?><?php echo $row->title;?></a>
		</td>
		<?php if( !$this->browse ){ ?>
		<td align="center"><?php echo JHTML::_('grid.published', $row, $i ); ?></td>
		<?php } ?>
		<td align="center"><?php echo $this->getAccessHTML( $row->access ); ?></td>
		<td align="center"><?php echo $this->getMembersCount( $row->id );?></td>
		<td align="center"><a href="<?php echo $previewLink;?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a></td>
		<td width="7" align="center"><?php echo $row->id;?></td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<td colspan="8" align="center">
			<?php echo JText::_('COM_EASYBLOG_NO_TEAM_BLOGS_CREATED_YET');?>
		</td>
	</tr>
<?php
}
?>
</tbody>

<tfoot>
	<tr>
		<td colspan="11">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>
