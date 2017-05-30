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
	<div class="span2">
		<div id="sidebar">
			<div class="sidebar-nav">
				<h4 class="page-header"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FILTER' ); ?>:</h4>

				<?php echo $this->state; ?>
			</div>
		</div>

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
				<?php if(empty($this->browse)){ ?>
				<th class="center" width="1%"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
				<?php } ?>
				
				<th class="nowrap"><?php echo JHTML::_('grid.sort', 'Title' , 'title', $this->orderDirection, $this->order ); ?></th>

				<?php if( !$this->browse ){ ?>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_DEFAULT' ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></th>
				<?php } ?>

				<th class="center nowrap" width="5%"><?php echo JText::_( 'COM_EASYBLOG_TAGS_ENTRIES' ); ?></th>

				<th class="center" width="10%"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_AUTHOR', 'created_by', $this->orderDirection, $this->order ); ?></th>
				
				<?php if( !$this->browse ){ ?>
				<th class="title" width="5%"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></th>
				<?php } ?>

				<th class="center" width="1%">
					<?php echo JText::_( 'COM_EASYBLOG_ID' ); ?>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php if( $this->tags ){ ?>
				<?php $i = 0; ?>
				<?php foreach( $this->tags as $row ){ ?>
					<tr>
						<?php if( !$this->browse ){ ?>
						<td class="center">
							<?php echo JHTML::_('grid.id', $i, $row->id); ?>
						</td>
						<?php } ?>

						<td align="left">
							<?php if( $this->browse ){ ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');">
							<?php } else {?>
								<a href="<?php echo JRoute::_('index.php?option=com_easyblog&amp;c=tag&amp;task=edit&amp;tagid='. $row->id); ?>">
							<?php } ?>
								<?php echo $row->title; ?></a>
						</td>

						<?php if( !$this->browse ){ ?>
						<td class="center">
							<?php if( $row->default ){ ?>
								<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=tag&task=unsetDefault&cid=' . $row->id );?>"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easyblog/assets/images/default.png" /></a>
							<?php } else { ?>
								<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=tag&task=setDefault&cid=' . $row->id );?>"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easyblog/assets/images/nodefault.png" /></a>
							<?php } ?>
						</td>

						<td class="center">
							<?php echo JHTML::_('jgrid.published', $row->published , $i ); ?>
						</td>
						<?php } ?>

						<td class="center">
							<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=blogs&tagid=' . $row->id);?>"><?php echo $row->count;?></a>
						</td>
						
						<td class="center">
							<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo JFactory::getUser($row->created_by)->name; ?></a>
						</td>

						<?php if( !$this->browse ){ ?>
						<td class="center">
							<a href="<?php echo JURI::root() . 'index.php?option=com_easyblog&amp;view=tags&layout=tag&id=' . $row->id; ?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a>
						</td>
						<?php } ?>

						<td class="center">
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php $i++; ?>
				<?php } ?>

			<?php } else { ?>
			<tr>
				<td colspan="9" align="center">
					<?php echo JText::_('COM_EASYBLOG_TAGS_NO_TAG_CREATED');?>
				</td>
			</tr>
			<?php } ?>

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

			<?php $k = 1 - $k; } ?>
		<?php
		}
		else
		{
		?>

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
</div>
