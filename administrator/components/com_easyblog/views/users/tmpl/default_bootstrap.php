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
		<?php if( !$this->browse ){ ?>
		<div class="span2">
			<h4><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_FILTER_BY' ); ?>: </h4>
			<?php echo $this->state; ?>
		</div>
		<?php } ?>

		<div class="<?php echo $this->browse ? 'span12' : 'span10';?>">
			
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

				<?php if( $this->users && !$this->browse ) : ?>
				<div class="pull-right">
					<div class="notice error"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_DELETE_NOTICE');?></div>
				</div>
				<?php endif; ?>
			</div>

			<div class="clearfix"></div>

			<table class="table table-striped">
			<thead>
				<tr>
					<?php if(empty($this->browse)) : ?>
					<th width="5"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
					<?php endif; ?>

					<?php if( !$this->browse ){ ?>
					<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_FEATURED' ); ?></th>
					<?php } ?>

					<th style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_NAME' ), 'a.name', $this->orderDirection, $this->order ); ?></th>
					
					<th style="text-align: center;" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_USERNAME' ) , 'a.username', $this->orderDirection, $this->order ); ?></th>
					<th style="text-align: center;" width="15%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_USER_GROUP' ) , 'a.usertype', $this->orderDirection, $this->order ); ?></th>
					<th style="text-align: left;" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_EMAIL' ) , 'a.email', $this->orderDirection, $this->order ); ?></th>
					<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_BLOG_ENTRIES' ); ?></th>
					<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
					<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if( $this->users ){ ?>
				<?php $i = 0; ?>

				<?php foreach( $this->users as $row ){ ?>
				<tr>

					<?php if( !$this->browse ){ ?>
					<td class="center">
						<?php echo JHTML::_('grid.id', $i++, $row->id); ?>
					</td>
					<?php } ?>

					<?php if( !$this->browse ){ ?>
					<td class="nowrap hidden-phone center">
						<a class="btn btn-micro jgrid" onclick="return listItemTask('cb<?php echo $i - 1;?>','<?php echo $row->featured ? 'unfeature' : 'feature';?>')">
						<?php if( $row->featured ){ ?>
							<i class="icon-star"></i>
						<?php } else { ?>
							<i class="icon-star-empty"></i>
						<?php } ?>
						</a>
					</td>
					<?php } ?>

					<td>
					<?php
					if( $this->browse )
					{
						if( $this->browseUID )
						{
					?>
						<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo $this->escape( addslashes( $row->name ) );?>', '<?php echo $this->browseUID;?>');"><?php echo $row->name;?></a>
					<?php
						}
						else
						{
					?>
						<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo $this->escape( addslashes( $row->name ) );?>');"><?php echo $row->name;?></a>
					<?php
						}
					}
					else
					{
					?>
						<a href="index.php?option=com_easyblog&c=user&id=<?php echo $row->id;?>&task=edit"><?php echo $row->name;?></a>
					<?php
					}
					?>
					</td>


					<td class="center">
						<?php echo $row->username; ?>
					</td>

					<td class="center">
						<?php echo (EasyBlogHelper::getJoomlaVersion() >= '1.6') ? $row->usergroups : $row->usertype;?>
					</td>

					<td class="center">
						<?php echo $row->email; ?>
					</td>

					<td class="center">
						<?php echo $this->getPostCount( $row->id );?>
					</td>

					<td class="center">
						<?php echo $row->id; ?>
					</td>

					<td class="center">
						<a href="<?php echo EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $row->id, true, true);?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a>
					</td>
				</tr>
				<?php } ?>

			<?php } else { ?>
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


