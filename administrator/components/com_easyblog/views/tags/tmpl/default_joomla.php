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
			<td width="200" style="text-align: right;">
				<label><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FILTER_BY' ); ?> :</label>
				<?php echo $this->state; ?>
			</td>
		</tr>
	</table>
</div>
<div class="adminform-body">
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1%">
			<?php echo JText::_( 'Num' ); ?>
		</th>
		<?php if(empty($this->browse)){ ?>
		<th width="1%"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->tags ); ?>);" /></th>
		<?php } ?>
		<th class="title" style="text-align: left;" width="30%"><?php echo JHTML::_('grid.sort', 'Title' , 'title', $this->orderDirection, $this->order ); ?></th>
		<?php if( !$this->browse ){ ?>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_DEFAULT' ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></th>
		<?php } ?>
		<th width="3%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_TAGS_ENTRIES' ); ?></th>
		<th class="title" width="3%"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_AUTHOR', 'created_by', $this->orderDirection, $this->order ); ?></th>
		<?php if( !$this->browse ){ ?>
		<th class="title" width="1%"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></th>
		<?php } ?>
		<th class="title" width="1%"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->tags )
{
	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->tags); $i < $n; $i++)
	{
		$row 	= $this->tags[$i];
		$user	= JFactory::getUser( $row->created_by );
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td style="text-align:center;">
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<?php if( !$this->browse ){ ?>
		<td width="7" style="text-align:center;">
			<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
		</td>
		<?php } ?>

		<td align="left">
			<span class="editlinktip hasTip">
				<?php if( $this->browse ){ ?>
					<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');">
				<?php } else {?>
					<a href="<?php echo JRoute::_('index.php?option=com_easyblog&amp;c=tag&amp;task=edit&amp;tagid='. $row->id); ?>">
				<?php } ?>
				<?php echo $row->title; ?></a>
			</span>
		</td>
		<?php if( !$this->browse ){ ?>
		<td align="center">
			<?php if( $row->default ){ ?>
				<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=tag&task=unsetDefault&cid=' . $row->id );?>"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easyblog/assets/images/default.png" /></a>
			<?php } else { ?>
				<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=tag&task=setDefault&cid=' . $row->id );?>"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easyblog/assets/images/nodefault.png" /></a>
			<?php } ?>
		</td>
		<td align="center">
			<?php echo JHTML::_('grid.published', $row, $i ); ?>
		</td>
		<?php } ?>
		<td align="center">
			<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=blogs&tagid=' . $row->id);?>"><?php echo $row->count;?></a>
		</td>
		<td align="center" width="3%">
			<span class="editlinktip hasTip">
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $user->name; ?></a>
			</span>
		</td>
		<?php if( !$this->browse ){ ?>
		<td align="center">
			<a href="<?php echo JURI::root() . 'index.php?option=com_easyblog&amp;view=tags&layout=tag&id=' . $row->id; ?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a>
		</td>
		<?php } ?>
		<td width="1%" style="text-align:center;">
			<?php echo $row->id;?>
		</td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<td colspan="9" align="center">
			<?php echo JText::_('COM_EASYBLOG_TAGS_NO_TAG_CREATED');?>
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