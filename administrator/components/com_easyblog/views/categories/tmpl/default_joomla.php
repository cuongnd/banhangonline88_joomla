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

$ordering 		= ($this->order == 'lft');
$originalOrders	= array();
?>
<div class="adminform-head">
	<table class="adminform">
		<tr>
			<td width="50%">
				<label><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_SEARCH' ); ?> :</label>
				<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
			</td>
			<td width="50%" style="text-align: right;">
				<label><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_FILTER_BY' ); ?> :</label>
				<?php echo $this->state; ?>
			</td>
		</tr>
	</table>
</div>
<div class="adminform-body">
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1%"><?php echo JText::_( 'Num' ); ?></th>
		<?php if(empty($this->browse)) : ?>
		<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->categories ); ?>);" /></th>
		<?php endif; ?>
		<th class="title" style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_CATEGORIES_CATEGORY_TITLE' ) , 'title', $this->orderDirection, $this->order ); ?></th>
		<th width="5%"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_DEFAULT' ); ?></th>
		<?php if(empty($this->browse)) : ?>
		<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_PRIVACY' ); ?></th>
		<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_PUBLISHED' ); ?></th>
		<th width="8%">
			<?php echo JHTML::_('grid.sort',   'Order', 'lft', 'desc', $this->order ); ?>
			<?php echo JHTML::_('grid.order',  $this->categories ); ?>
		</th>
		<?php endif; ?>
		<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_ENTRIES' ); ?></th>
		<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_CHILD_COUNT' ); ?></th>
		<th class="title" width="15%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_CATEGORIES_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
		<?php if( !$this->browse ){ ?>
		<th class="title" width="1%"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></th>
		<?php } ?>
		<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->categories )
{

	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->categories); $i < $n; $i++)
	{
		$row = $this->categories[$i];

		$link 			= 'index.php?option=com_easyblog&amp;c=category&amp;task=edit&amp;catid='. $row->id;
		$previewLink	= JURI::root() . 'index.php?option=com_easyblog&amp;view=categories&layout=listings&id=' . $row->id;
		$published 	= JHTML::_('grid.published', $row, $i );
		$user		= JFactory::getUser( $row->created_by );

		$orderkey	= array_search($row->id, $this->ordering[$row->parent_id]);
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
		<?php if(empty($this->browse)) : ?>
		<td><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
		<?php endif; ?>
		<td align="left">
			<?php echo str_repeat( '|&mdash;' , $row->depth ); ?>
			<span class="editlinktip hasTip">
			<?php if( $this->browse ){ ?>
				<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');"><?php echo $row->title;?></a>
			<?php } else { ?>
				<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			<?php } ?>
			</span>
		</td>
		<td align="center">
			<?php if( $row->default ){ ?>
				<img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easyblog/assets/images/default.png" />
			<?php } else { ?>
				<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=category&task=makeDefault&cid=' . $row->id );?>"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easyblog/assets/images/nodefault.png" /></a>
			<?php } ?>
		</td>


		<?php if(empty($this->browse)) : ?>
		<td align="center">
			<?php echo ( $row->private ) ? JText::_('COM_EASYBLOG_CATEGORIES_PRIVATE') : JText::_('COM_EASYBLOG_CATEGORIES_PUBLIC') ?>
		</td>

		<td align="center">
			<?php echo $published; ?>
		</td>
		<td class="order">
			<?php if ($this->saveOrder) : ?>
				<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$row->parent_id][$orderkey - 1]), 'orderup', 'Move Up', $ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$row->parent_id][$orderkey + 1]), 'orderdown', 'Move Down', $ordering); ?></span>
			<?php endif; ?>

			<?php $disabled = 'disabled="disabled"'; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			<?php $originalOrders[] = $orderkey + 1; ?>
		</td>
		<?php endif; ?>
		<td align="center">
			<?php if( $this->browse ){ ?>
				<?php echo $row->count; ?>
			<?php } else { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=blogs&filter_category=' . $row->id);?>"><?php echo $row->count;?></a>
			<?php } ?>
		</td>
		<td align="center">
			<?php echo $row->child_count; ?>
		</td>
		<td align="center">
			<?php if( $this->browse ){ ?>
				<?php echo $user->name; ?>
			<?php } else { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $user->name; ?></a>
			<?php } ?>
		</td>
		<?php if( !$this->browse ){ ?>
		<td align="center"><a href="<?php echo $previewLink; ?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a></td>
		<?php } ?>
		<td align="center"><?php echo $row->id;?></td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<td colspan="12" align="center">
			<?php echo JText::_('COM_EASYBLOG_NO_CATEGORY_CREATED_YET');?>
		</td>
	</tr>
<?php
}
?>
</tbody>

<tfoot>
	<tr>
		<td colspan="12">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>