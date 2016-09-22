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
            <label><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FILTER_BY' ); ?> :</label>
            <?php echo $this->filterList; ?>
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
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
					</th>

					<?php if( $this->filter != 'site' ){ ?>
					<th>
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_SUBSCRIPTION_COLUMN_' . strtoupper( $this->filter ) ) , 'bname', $this->orderDirection, $this->order ); ?>
					</th>
					<?php } ?>
					
					<th width="10%" class="center nowrap">
						<?php echo JText::_( 'COM_EASYBLOG_SUBSCRIBER_EMAIL' ); ?>
					</th>
					<th width="20%" class="center nowrap">
						<?php echo JText::_( 'COM_EASYBLOG_SUBSCRIBER_NAME' ); ?>
					</th>
					<th width="5%" class="center nowrap">
						<?php echo JText::_( 'COM_EASYBLOG_SUBSCRIPTION_DATE' ); ?>
					</th>
					<th width="1%" class="center nowrap"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if( $this->subscriptions ){ ?>
				<?php $i = 0; ?>
				<?php foreach( $this->subscriptions as $row ){ ?>
				<tr>
					<td class="center">
						<?php echo JHTML::_('grid.id', $i++, $row->id); ?>
					</td>
					
					<?php if( $this->filter != 'site' ){ ?>
					<td>
						<?php echo $row->bname;?><?php echo ($this->filter == 'blogger') ? ' (' . $row->busername. ')' : ''; ?>
					</td>
					<?php } ?>

					<td class="center">
						<?php echo $row->email;?>
					</td>

					<td class="center">
						<?php echo (empty($row->name)) ? $row->fullname :  $row->name;?>
					</td>

					<td class="center">
						<?php echo $row->created; ?>
					</td>

					<td class="center">
						<?php echo $row->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" align="center">
						<?php echo JText::_('COM_EASYBLOG_NO_SUBSCRIPTION_FOUND');?>
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