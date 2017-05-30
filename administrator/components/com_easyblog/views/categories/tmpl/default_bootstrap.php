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
<div class="row-fluid">

	<div class="span12">

		<div class="span2">
			<h4><?php echo JText::_( 'COM_EASYBLOG_FILTER' ); ?>: </h4>
			<?php echo $this->state; ?>
		</div>

		<div class="span10">
			<div class="filter-bar">
				<div class="filter-search input-append pull-left">
					<label class="element-invisible" for="search"><?php echo JText::_( 'COM_EASYBLOG_SEARCH' ); ?> :</label>
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();"
					placeholder="<?php echo JText::_( 'COM_EASYBLOG_SEARCH' , true ); ?>" />
					<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
					<button class="btn" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
				</div>
				<div class="blogger-listing-dropdown-limit">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			</div>

			<table class="table table-striped">
			<thead>
				<tr>
					<?php if(empty($this->browse)) : ?>
					<th width="5"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<?php endif; ?>

					<th><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_CATEGORIES_CATEGORY_TITLE' ) , 'title', $this->orderDirection, $this->order ); ?></th>

					<th width="5%" class="center nowrap">
						<?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_DEFAULT' ); ?>
					</th>

					<?php if(empty($this->browse)) : ?>
					<th width="5%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_PRIVACY' ); ?></th>
					<th width="5%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_PUBLISHED' ); ?></th>
					<th width="8%">
						<?php echo JHTML::_('grid.sort',   'Order', 'lft', 'desc', $this->order ); ?>
						<?php echo JHTML::_('grid.order',  $this->categories ); ?>
					</th>
					<?php endif; ?>
					<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_ENTRIES' ); ?></th>
					<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_CHILD_COUNT' ); ?></th>
					<th class="center nowrap" width="15%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_CATEGORIES_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
					<?php if( !$this->browse ){ ?>
					<th class="center nowrap" width="1%"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></th>
					<?php } ?>
					<th width="1%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->categories ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $this->categories as $row ){ ?>
					<tr>
						<?php if(empty($this->browse)) : ?>
						<td><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
						<?php endif; ?>

						<td align="left">
							<?php echo str_repeat( '|&mdash;' , $row->depth ); ?>
							<span class="editlinktip hasTip">
							<?php if( $this->browse ){ ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');"><?php echo $row->title;?></a>
							<?php } else { ?>
								<a href="index.php?option=com_easyblog&amp;c=category&amp;task=edit&amp;catid=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
							<?php } ?>
							</span>
						</td>
						<td class="center nowrap">

							<a href="<?php echo !$row->default ? JRoute::_( 'index.php?option=com_easyblog&c=category&task=makeDefault&cid=' . $row->id ) : 'javascript:void(0)' ;?>" class="btn btn-micro jgrid">
							<?php if( !$row->default ){ ?>
								<i class="icon-star-empty"></i>
							<?php } else { ?>
								<i class="icon-star"></i>
							<?php } ?>
							</a>
						</td>


						<?php if(empty($this->browse)) : ?>
						<td class="center nowrap">
							<?php echo ( $row->private ) ? JText::_('COM_EASYBLOG_CATEGORIES_PRIVATE') : JText::_('COM_EASYBLOG_CATEGORIES_PUBLIC') ?>
						</td>

						<td class="center nowrap">
							<?php echo JHtml::_('jgrid.published', $row->published , $i  ); ?>
						</td>

						<td class="order center nowrap">
							<?php $orderkey 	= array_search($row->id, $this->ordering[$row->parent_id]); ?>
								<div class="pull-left">
							<?php if ($this->saveOrder) : ?>
								<span class="pull-left"><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$row->parent_id][$orderkey - 1]), 'orderup', 'Move Up', $this->ordering); ?></span>
								<span class="pull-right"><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$row->parent_id][$orderkey + 1]), 'orderdown', 'Move Down', $this->ordering); ?></span>
							<?php endif; ?>
								</div>
								<div class="pull-right">
							<?php $disabled = 'disabled="disabled"'; ?>
							<input type="text" name="order[]" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="input-xsmall" style="width: 30px;text-align:center;"/>
							<?php $originalOrders[] = $orderkey + 1; ?>
								</div>

						</td>
						<?php endif; ?>

						<td class="center nowrap">
							<?php if( $this->browse ){ ?>
								<?php echo $row->count; ?>
							<?php } else { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=blogs&filter_category=' . $row->id);?>"><?php echo $row->count;?></a>
							<?php } ?>
						</td>

						<td class="center nowrap">
							<?php echo $row->child_count; ?>
						</td>

						<td class="center nowrap">
							<?php if( $this->browse ){ ?>
								<?php echo JFactory::getUser( $row->created_by )->name; ?>
							<?php } else { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo JFactory::getUser( $row->created_by )->name; ?></a>
							<?php } ?>
						</td>
						<?php if( !$this->browse ){ ?>
						<td align="center">
							<a href="<?php echo JURI::root();?>index.php?option=com_easyblog&amp;view=categories&layout=listings&id=<?php echo $row->id;?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a></td>
						<?php } ?>
						<td align="center"><?php echo $row->id;?></td>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="12" align="center">
						<?php echo JText::_('COM_EASYBLOG_NO_CATEGORY_CREATED_YET');?>
					</td>
				</tr>
				<?php } ?>
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

	</div>
</div>
