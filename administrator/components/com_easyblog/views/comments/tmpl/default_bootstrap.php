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
			<h4><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_FILTER_BY' );?>:</h4>
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
					<th width="1%" class="center"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
					<th><?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT');?></th>
					
					<th class="center" width="20%">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_BLOG_TITLE' ), 'blog_name', $this->orderDirection, $this->order ); ?>
					</th>

					<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_PUBLISHED' ); ?></th>
					<th class="center" width="10%">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_DATE' ), 'created', $this->orderDirection, $this->order ); ?>
					</th>
					<th class="center" width="10%">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_( 'COM_EASYBLOG_ID' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->comments ){ ?>

					<?php $i = 0; ?>
					<?php foreach( $this->comments as $row ){ ?>
						<?php $x = $i; ?>
						<tr>
							<td class="center nowrap">
								<?php echo JHTML::_('grid.id', $i++, $row->id); ?>
							</td>
							<td>
								<span class="editlinktip hasTip">
									<?php $title 	= trim($row->title); ?>
									<a href="index.php?option=com_easyblog&amp;c=comment&amp;task=edit&amp;commentid=<?php echo $row->id;?>">
									<?php if (!empty($title)) { ?>
										<?php echo $row->title; ?></a>
									<?php }else{ ?>
										<?php echo JText::_( 'COM_EASYBLOG_COMMENTS_NO_TITLE'); ?>
									<?php } ?>
									</a>
								</span>
								<div class="small">
									<?php echo $row->comment;?>
								</div>
							</td>
							<td class="center">
								<a href="<?php echo JURI::root() . 'index.php?option=com_easyblog&amp;view=entry&amp;id=' . $row->post_id; ?>" target="_blank"><?php echo $row->blog_name; ?></a>
							</td>
							<td class="center">
								<?php if( $row->isModerate ) { ?>
									<?php if( EasyBlogHelper::getJoomlaVersion() <= '1.5' ){ ?>
										<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','publish')"><img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/pending.png';?>" width="16" height="16" border="0" /></a>
									<?php } else { ?>
										<?php echo JHTML::_( 'grid.boolean' , $x , $row->published , 'publish' , 'publish' ); ?>
									<?php } ?>
								<?php } else { ?>
									<?php echo JHTML::_('jgrid.published', $row->published , $x ); ?>
								<?php } ?>
							</td>

							<td class="center">
								<?php echo EasyBlogHelper::getDate( $row->created )->toMySQL( true );?>
							</td>
							<td class="center">
								<?php if ( $row->created_by == 0 ) : ?>
									<?php echo $row->name; ?>
								<?php else : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $row->name; ?></a>
								<?php endif; ?>
							</td>
							<td class="center"><?php echo $row->id; ?></td>
						</tr>
					<?php } ?>

				<?php } else { ?>
				<tr>
					<td colspan="7" align="center">
						<?php echo JText::_('COM_EASYBLOG_COMMENTS_NO_COMMENT_YET');?>
					</td>
				</tr>
				<?php } ?>

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
</div>
