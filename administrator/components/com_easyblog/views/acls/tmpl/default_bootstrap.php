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
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="span12">

		<div id="j-sidebar-container" class="span2">
			<div class="filter-select hidden-phone">
				<h4><?php echo JText::_( 'COM_EASYBLOG_FILTER');?>:</h4>

				<?php echo $this->filter->type; ?>
			</div>
		</div>

		<div id="j-main-container" class="span10">

			<div class="filter-bar">
				<div class="filter-search input-append pull-left">
					<label class="element-invisible" for="search"><?php echo JText::_( 'COM_EASYBLOG_ACL_SEARCH' ); ?> :</label>
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->filter->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" 
					placeholder="<?php echo JText::_( 'COM_EASYBLOG_BLOGS_SEARCH' , true ); ?>" />
					<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
					<button class="btn" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
				</div>

				<div class="pull-right">
					<pre><?php echo JText::_( 'COM_EASYBLOG_ACL_ASSIGNED_TIPS');?></pre>
				</div>
			</div>

			<div class="clearfix"></div>
			<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll( this );" />
					</th>
					<th>
						<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_GROUP_NAME', 'a.`name`', $this->sort->orderDirection, $this->sort->order ); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_ID', 'a.`id`', $this->sort->orderDirection, $this->sort->order ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->rulesets ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $this->rulesets as $ruleset ){ ?>
					<tr>
						<td class="center"><?php echo JHTML::_('grid.id', $i++, $ruleset->id); ?></td>
						<td>
							<?php echo str_repeat('<span class="gi">|&mdash;</span>', $ruleset->level) ?>
							<a href="index.php?option=com_easyblog&amp;c=acl&amp;task=edit&amp;cid=<?php echo $ruleset->id;?>&amp;type=<?php echo $this->type;?>"><?php echo $ruleset->name; ?></a>
						</td>
						<td class="center">
							<?php echo $ruleset->id;?>
						</td>
					</tr>
					<?php } ?>

				<?php } else { ?>
				<tr>
					<td colspan="3">
						<?php echo JText::_( 'No ACL defined here yet.'); ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			</table>
		</div>
	</div>
</div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="view" value="acls" />
<input type="hidden" name="c" value="acl" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->sort->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="boxchecked" value="0" />
</form>