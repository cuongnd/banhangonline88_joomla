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
<div class="row-fluid">
	<div class="span12">
		<div class="span2">
			<label><?php echo JText::_( 'COM_EASYBLOG_FILTER' );?> :</label>
			<?php echo $this->filter->type; ?>
		</div>

		<div class="span10">

			<div class="filter-bar">

				<div class="filter-search input-append pull-left">
					<label class="element-invisible" for="search"><?php echo JText::_( 'COM_EASYBLOG_SEARCH' ); ?> :</label>
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" 
					placeholder="<?php echo JText::_( 'COM_EASYBLOG_SEARCH' , true ); ?>" />
					<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
					<button class="btn" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
					<div class="blogger-listing-dropdown-limit">
						<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				</div>
			</div>

			<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" align="center" style="text-align: center;">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th class="title" style="text-align: left;" width="30%"><?php echo JText::_('COM_EASYBLOG_META_TITLE'); ?></th>
					<th class="title" style="text-align: center;" width="5%"><?php echo JText::_('COM_EASYBLOG_META_INDEXING'); ?></th>
					<th class="title" style="text-align: left;" width="30%"><?php echo JText::_('COM_EASYBLOG_META_KEYWORDS'); ?></th>
					<th class="title" style="text-align: left;" width="30%"><?php echo JText::_('COM_EASYBLOG_META_DESCRIPTION'); ?></th>

					<th width="5%" class="center">
						<?php echo JHTML::_('grid.sort', 'Type' , 'type', $this->orderDirection, $this->order ); ?>
					</th>

					<th width="1%" class="center nowrap">
						<?php echo JHTML::_('grid.sort', 'ID' , 'id', $this->orderDirection, $this->order ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->meta ){ ?>
					<?php $i = 0; ?>
					
					<?php foreach( $this->meta as $row ){ ?>
					<tr>
						<td class="center">
							<?php echo JHTML::_('grid.id', $i , $row->id); ?>
						</td>

						<td align="left">
							<a href="index.php?option=com_easyblog&view=meta&id=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
						</td>

						<td class="nowrap hidden-phone center">
							<a onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->indexing ? 'removeIndexing' : 'addIndexing';?>')" href="javascript:void(0);" class="btn btn-micro active">
								<i class="icon-<?php echo !$row->indexing ? 'unpublish' : 'publish';?>"></i>
							</a>
						</td>

						<td align="left">
							<?php echo $row->keywords ? $row->keywords : JText::_( 'COM_EASYBLOG_NOT_DEFINED' ); ?>
						</td>

						<td align="left">
							<?php echo $row->description ? $row->description : JText::_( 'COM_EASYBLOG_NOT_DEFINED' ); ?>
						</td>

						<td class="center">
							<?php echo $row->type; ?>
						</td>

						<td class="center">
							<?php echo $row->id;?>
						</td>

					</tr>
						<?php $i++; ?>
					<?php }?>

				<?php } else { ?>
					<tr>
						<td colspan="7" class="center">
							<?php echo JText::_('COM_EASYBLOG_NO_META_TAGS_INDEXED_YET');?>
						</td>
					</tr>
				<?php } ?>

			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			</table>

		</div>
	</div>
</div>
