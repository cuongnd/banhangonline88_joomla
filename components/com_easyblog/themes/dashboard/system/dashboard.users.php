<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStylesheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/css/common.css' );
$document->addStylesheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/dashboard/system/css/styles.css' );

EasyBlogHelper::loadHeaders();
?>
<style type="text/css">
body{width:100%!important;}
body,
body .dialog-reset{margin:0!important;padding:0!important;border:0!important;width:auto!important;}
</style>
<script type="text/javascript">

function changeState(state)
{
	document.getElementById('filter_state').value = state.value;
	submitForm();
}

function changeFilter(order, direction)
{
	document.getElementById('filter_order').value = order;
	document.getElementById('filter_order_Dir').value = direction;
	submitForm();
}

function resetForm()
{
	document.getElementById('search').value = '';
	submitForm();
}

function submitForm()
{
	document.bloggerlist.submit();
}

</script>
<div id="eblog-wrapper">
<div id="ezblog-dashboard">
	<form name="bloggerlist" id="bloggerlist" method="GET" action="<?php echo JRequest::getURI(); ?>" style="width:100%;">
	<div class="form-head pbl mbl clearfix" style="border-bottom:1px solid #ddd">
		<div class="float-l">
			<input type="text" name="search" id="search" value="<?php echo $search; ?>" class="input text" />
			<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_SEARCH_BUTTON' );?>" class="buttons" />
			<input type="button" name="Reset" value="<?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' );?>" onclick="resetForm();" class="buttons" />
		</div>
		<div class="float-r">
			<select class="input select" onchange="changeState(this);">
				<option value="P" <?php echo $filter_state == 'P' ? ' selected="selected"' :'';?>><?php echo JText::_('COM_EASYBLOG_PUBLISHED');?></option>
				<option value="U" <?php echo $filter_state == 'U' ? ' selected="selected"' :'';?>><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED');?></option>
			</select>
		</div>
	</div>

	<div class="mbl" style="margin-right:1px">
	<table id="blogger-container" width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table adminlist">
		<thead>
		<tr>
			<th width="1%"><a href="javascript:void(0);" onclick="changeFilter('id', '<?php echo $orderDir; ?>');"><?php echo JText::_('ID'); ?></a></th>
			<th><a href="javascript:void(0);" onclick="changeFilter('name', '<?php echo $orderDir; ?>');"><?php echo JText::_('Name'); ?></a></th>
			<th width="25%"><a href="javascript:void(0);" onclick="changeFilter('username', '<?php echo $orderDir; ?>');"><?php echo JText::_('Username'); ?></a></th>
			<th width="25%"><a href="javascript:void(0);" onclick="changeFilter('email', '<?php echo $orderDir; ?>');"><?php echo JText::_('Email'); ?></a></th>
			<th width="10%">&nbsp;</th>
		</tr>
		</thead>
	<?php
	if(!empty($users))
	{
	?>
		<tbody>
		<?php
		$count = 0;
		foreach($users as $blogger)
		{
		?>
		<tr class="row<?php echo $count % 2; ?>">
			<td><?php echo empty($blogger->id)? '' : $blogger->id; ?></td>
			<td>
				<a href="javascript:void(0);" onclick="parent.changeAuthor('<?php echo $blogger->id; ?>', '<?php echo $blogger->getName(); ?>', '<?php echo $blogger->getAvatar();?>');">
					<?php echo $blogger->getName();?>
				</a>
			</td>
			<td><?php echo empty($blogger->user->username)? '' : $blogger->user->username; ?></td>
			<td><?php echo empty($blogger->user->email)? '' : $blogger->user->email; ?></td>
			<td align="center"><img src="<?php echo $blogger->getAvatar();?>" width="32" height="32" />
		</tr>
		<?php
		$count++;
		}
		?>
		</tbody>
	<?php
	}
	else
	{
	?>
		<tbody>
		<tr>
			<td colspan="5"><?php echo JText::_('COM_EASYBLOG_NO_BLOGGERS_AVAILABLE'); ?></td>
		</tr>
		</tbody>
	<?php
	}
	?>
	</table>
	</div>
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="controller" value="dashboard" />
	<input type="hidden" name="task" value="listBloggers" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="browse" value="1" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="asc" />
	<input type="hidden" name="filter_state" id="filter_state" value="<?php echo $filter_state; ?>" />
	</form>
	<?php if ( !empty($pagination) ) : ?>
			<div class="pagination clearfix"><?php echo $pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
</div>
</div>
<script type="text/javascript">
EasyBlog.ready(function($) {
    $('#eblog-wrapper').parents('div').addClass('dialog-reset');
});
</script>
