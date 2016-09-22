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
?>
<script type="text/javascript">
EasyBlog.require().library('backgroundposition').script('legacy').done(function($){

	eblog.dashboard.lists.init( 'comments' );

	// Expand the search
	$('#dashboard-comments #comment-search').bind('focus', function(){

		$(this).animate({
			width: '250',
			backgroundPositionX: 260,
			backgroundPositionY: 'center'
		});
	});

	$('#dashboard-comments #comment-search').bind( 'blur' , function(){
		$(this).animate({
			width: '150',
			backgroundPositionX: 160,
			backgroundPositionY: 'center'
		});
	});

});
</script>

<div id="dashboard-comments" class="stackSelectGroup">
	<div class="dashboard-head clearfix">
		<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
		<form name="commentsearch" action="<?php echo htmlspecialchars(JRequest::getURI()); ?>" method="get" class="head-option">
			<span><input type="text" name="post-search" class="input text width-150 search-head" value="<?php echo $this->escape( $search );?>" id="comment-search" /></span>
		</form>
	</div>

	<div class="ui-optbox clearfix fsm">
		<ul class="ui-entries-filter reset-ul float-li float-r">
			<li<?php echo $filter == 'all' ? ' class="active"' : '';?>><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=comments&filter=all');?>"><?php echo JText::_('COM_EASYBLOG_FILTER_ALL'); ?></a></li>
			<li<?php echo $filter == 'published' ? ' class="active"' : '';?>><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=comments&filter=published');?>"><?php echo JText::_('COM_EASYBLOG_FILTER_PUBLISHED'); ?></a></li>
			<li<?php echo $filter == 'unpublished' ? ' class="active"' : '';?>><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=comments&filter=unpublished');?>"><?php echo JText::_('COM_EASYBLOG_FILTER_UNPUBLISHED'); ?></a></li>
			<li<?php echo $filter == 'moderate' ? ' class="active"' : '';?>><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=comments&filter=moderate');?>"><?php echo JText::_('COM_EASYBLOG_FILTER_PENDING'); ?></a></li>
		</ul>
		<div class="entries-select float-l">
			<?php if( !empty($this->acl->rules->manage_comment) || !empty($this->acl->rules->delete_comment ) ){ ?>
				<ul class="ui-list-select-actions reset-ul float-li clearfix">
					<li class="float-l">
	                    <input type="checkbox" class="stackSelectAll float-l" name="toggle" id="toggle"/>
	                    <label for="toggle" class="float-l mls mts"><?php echo JText::_( 'COM_EASYBLOG_SELECT_ALL')?></label>
	                </li>
					<li id="select-actions" class="float-l">
						<select id="comment-action">
							<option value="default"><?php echo JText::_('COM_EASYBLOG_WITH_SELECTED');?></option>
							<?php if( !empty($this->acl->rules->manage_comment) ) : ?>
								<option value="publishComment"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
								<option value="unpublishComment"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
							<?php endif; ?>
							<?php if( !empty($this->acl->rules->delete_comment) ) : ?>
								<option value="removeComment"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
							<?php endif; ?>
						</select>
						<input type="button" class="ui-button" value="<?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>" onclick="eblog.dashboard.action( 'comment' , 'index.php?option=com_easyblog&view=dashboard&layout=comments' );" />
					</li>
				</ul>
			<?php } ?>
		</div>
	</div>
	<?php $this->set( 'showCheckbox' , true );?>
	<?php echo $this->fetch( 'dashboard.comments.item.php' ); ?>

	<div class="clearfix"></div>
</div>
