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
				<th width="5">
					<?php echo JText::_( 'Num' ); ?>
				</th>
				<th width="5"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $this->orderDirection, $this->order ); ?></th>
				<th width="200" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PENDING_ACTION' ); ?></th>
				<th width="10%" nowrap="nowrap"><?php echo JText::_( 'Category' ); ?></th>
				<th width="10%" nowrap="nowrap"><?php echo JText::_( 'Creator' ); ?></th>
				<th width="10%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'DATE', 'a.created', $this->orderDirection, $this->order ); ?></th>
				<th width="20" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.entry_id', $this->orderDirection, $this->order ); ?></th>

			</tr>
		</thead>
		<tbody>
		<?php
		if( $this->blogs )
		{
			$k = 0;
			$x = 0;
			$config	= JFactory::getConfig();
			for ($i=0, $n=count($this->blogs); $i < $n; $i++)
			{
				$row 		= $this->blogs[$i];
				$user		= JFactory::getUser( $row->created_by );
				$previewLink	= rtrim( JURI::root() , "/" ) . "/" . JRoute::_("index.php?option=com_easyblog&view=entry&id=" . $row->id);
				$preview 	= '<a href="' . $previewLink .'" target="_blank"><img src="'.JURI::base().'/images/preview_f2.png"/ style="width:20px; height:20px; "></a>';
				$editLink	= JRoute::_('index.php?option=com_easyblog&c=blogs&task=edit&draft_id='.$row->id.'&approval=1');

				$date		= EasyBlogHelper::getDate( $row->created );
			?>

			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset( $i ); ?>
				</td>
				<td width="7">
					<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
				</td>
				<td align="left">
					<span class="editlinktip hasTip">
						<a href="<?php echo $editLink; ?>">
						<?php echo $row->title; ?>
						</a>
					</span>
				</td>
				<td align="center">
					<a class="button" href="javascript:void(0);" onclick="admin.blog.reject('<?php echo $row->id; ?>');">
						<?php echo JText::_('COM_EASYBLOG_REJECT_BUTTON');?>
					</a>
					|
					<a class="button" href="javascript:void(0);" onclick="admin.blog.approve('<?php echo $row->id; ?>','<?php echo JText::_( 'COM_EASYBLOG_CONFIRM_APPROVE_BLOG' );?>');">
						<?php echo JText::_('COM_EASYBLOG_APPROVE_BUTTON');?>
					</a>
				</td>
				<td align="center">
					<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=category&task=edit&catid=' . $row->category_id);?>"><?php echo $this->getCategoryName( $row->category_id);?></a>
				</td>
				<td align="center">
					<span class="editlinktip hasTip">
						<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $user->name; ?></a>
					</span>
				</td>
				<td align="center">
					<?php echo EasyBlogDateHelper::toFormat($date); ?>
				</td>
				<td align="center">
					<?php echo $row->id; ?>
				</td>

			</tr>

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
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>

</div>
