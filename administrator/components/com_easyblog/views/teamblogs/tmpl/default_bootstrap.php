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
			<div class="sidebar">
				<div class="sidebar-nav">

					<ul class="nav nav-list" id="submenu">
						<li class="active">
							<a href="index.php?option=com_easyblog&amp;view=teamblogs"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS' );?></a>
						</li>
						<li>
							<a href="index.php?option=com_easyblog&amp;view=teamrequest"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_VIEW_REQUEST' );?></a>
						</li>
					</ul>

					<hr />
					<h4><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FILTER_BY' ); ?>:</h4>
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


			<table class="table table-strped">
			<thead>
				<tr>
					<?php if( !$this->browse ){ ?>
					<th width="5">
						<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
					</th>
					<?php }?>


					<?php if( !$this->browse ){ ?>
					<th width="1%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></th>
					<?php } ?>
					
					<th style="text-align: left;">
						<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_TEAMBLOGS_TEAM_NAME', 'a.title', $this->orderDirection, $this->order ); ?>
					</th>

					<th width="5%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_ACCESS' ); ?></th>
					<th width="1%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS' ); ?></th>
					<th width="1%" class="center nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->teams ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $this->teams as $row ){ ?>
					<tr>
						<?php if( !$this->browse ){ ?>
						<td width="1%" class="center nowrap">
							<?php echo JHTML::_('grid.id', $i , $row->id); ?>
						</td>
						<?php } ?>

						<?php if( !$this->browse ){ ?>
						<td class="center nowrap">
							<?php echo JHTML::_( 'jgrid.published' , $row->published , $i ); ?>
						</td>
						<?php } ?>

						<td>
							<?php if( $this->browse ){ ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');">
							<?php } else {?>
								<a href="index.php?option=com_easyblog&amp;c=teamblogs&amp;task=edit&amp;id=<?php echo $row->id;?>">
							<?php } ?><?php echo $row->title;?></a>
						</td>

						<td class="center nowrap"><?php echo $this->getAccessHTML( $row->access ); ?></td>
						<td class="center nowrap"><?php echo $this->getMembersCount( $row->id );?></td>
						<td class="center nowrap"><?php echo $row->id;?></td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6">
							<?php echo JText::_('COM_EASYBLOG_NO_TEAM_BLOGS_CREATED_YET');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="10">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			</table>
		</div>

	</div>
</div>
