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
					<h4><?php echo JText::_( 'COM_EASYBLOG_FEEDS_FILTER_BY' ); ?>:</h4>
					<?php echo $this->state; ?>
				</div>
			</div>
		</div>

		<div class="span10">

			<div class="row-fluid">
				<div class="filter-search input-append pull-left">
					<label class="element-invisible" for="search"><?php echo JText::_( 'COM_EASYBLOG_SEARCH' ); ?> :</label>
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();"
					placeholder="<?php echo JText::_( 'COM_EASYBLOG_SEARCH' , true ); ?>" />
					<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
					<button class="btn" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
				</div>

				<div class="pull-left notice">
					<?php echo JText::_('COM_EASYBLOG_FEEDS_PROCESSING_NOTES');?>
				</div>
			</div>

			<div class="row-fluid">
				<div style="width:750px;">
					<div id="progress-bar" style="display:none;">
					    <div class="bar-holder">
					        <div class="bar-progress" id="bar-progress" style="width:0%;"></div>
					    </div>
					</div>
					<span id="feeds-msg" class="msg-in info" style="display:none;"><?php echo JText::_('COM_EASYBLOG_FEEDS_MIGRATE_NOTES'); ?></span>
				</div>
			</div>


			<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="center nowrap"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th><?php echo JText::_( 'COM_EASYBLOG_FEEDS_TITLE' ); ?></th>
					<th width="45%" style="text-align: left;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_URL' ); ?></th>
					<th width="8%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_LAST_IMPORT' ); ?></th>
					<th width="5%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_PUBLISHED' ); ?></th>
					<th width="1%" class="center nowrap"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->feeds ){ ?>
					<?php $i = 0; ?>

					<?php foreach( $this->feeds as $row ){ ?>
					<tr>
						<td><?php echo JHTML::_('grid.id', $i++, $row->id); ?></td>
						<td align="left">
							<a href="index.php?option=com_easyblog&amp;c=feeds&amp;task=edit&amp;cid=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
						</td>
						<td>
							<a href="<?php echo $row->url; ?>" target="_blank"><?php echo $row->url; ?></a>
						</td>
						<td class="center nowrap">
							<?php
							if( $row->last_import == '0000-00-00 00:00:00' )
							{
								echo JText::_( 'COM_EASYBLOG_NEVER' );
							}
							else
							{
								$date	= EasyBlogHelper::getDate();

								echo $date->toMySQL();
							}
							?>
						</td>
						<td class="center nowrap">
							<?php echo JHTML::_('jgrid.published', $row->published, $i ); ?>
						</td>
						<td class="center nowrap"><?php echo $row->id;?></td>
					</tr>
					<?php } ?>

				<?php } else { ?>
					<tr>
						<td colspan="6">
							<?php echo JText::_('COM_EASYBLOG_FEEDS_NO_FEEDS_YET');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			</table>
		</div>

	</div>
</div>
