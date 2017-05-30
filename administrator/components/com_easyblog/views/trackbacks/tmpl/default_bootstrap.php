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

					<h4><?php echo JText::_( 'COM_EASYBLOG_FILTER' );?>:</h4>
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
					<th width="1%">
						<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
					</th>
					<th class="title">
						<?php echo JText::_('COM_EASYBLOG_TRACKBACKS_TRACKBACK_FROM');?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?>
					</th>
					
					<th class="center" width="20%">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_BLOG_TITLE' ), 'blog_name', $this->orderDirection, $this->order ); ?>
					</th>

					<th class="center" width="10%">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_DATE' ), 'created', $this->orderDirection, $this->order ); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_( 'COM_EASYBLOG_ID' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->trackbacks ){ ?>
					
					<?php foreach( $this->trackbacks as $row ){ ?>
					<tr>
						<td class="center nowrap">
							<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
						</td>
						<td align="left">
							<div>
								<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=trackback&task=edit&id=' . $row->id );?>"><?php echo $row->title; ?></a>
							</div>
							<div class="small">
								<?php echo $row->excerpt;?>
								<div style="margin-top: 5px;"><a href="<?php echo $row->url;?>" target="_blank"><?php echo $row->url;?></a></div>
							</div>
						</td>
						<td class="center nowrap">
							<?php echo JHTML::_('grid.published', $row, $i ); ?>
						</td>
						<td class="center nowrap">
							<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=blogs&task=edit&blogid=' . $blog->id );?>"><?php echo $blog->title; ?></a>
						</td>
						<td class="center nowrap">
							<?php echo EasyBlogDateHelper::dateWithOffset( $row->created )->toMySQL();?>
						</td>
						<td class="center nowrap">
							<?php echo $row->id; ?>
						</td>
					</tr>
					<?php }?>

				<?php } else { ?>
				<tr>
					<td colspan="6" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_TRACKBACKS_NO_TRACKBACKS_YET');?>
					</td>
				</tr>
				<?php } ?>
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</tbody>


			</table>
		</div>

	</div>
</div>