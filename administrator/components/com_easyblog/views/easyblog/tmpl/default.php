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
<script type="text/javascript">
EasyBlog.require().script('legacy').done(function($)
{
	// Load version info via ajax instead to speed up the back end.
	ejax.load( 'easyblog' , 'getVersion' );

	
	<?php if( EasyBlogHelper::getJoomlaVersion() >= '3.0' ){ ?>
		
		$('#newpost30').prependTo('#toolbar-options').show();
		$('#purgeCache30').appendTo('#toolbar-options').show();

	<?php } else if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
		var li			= $( '<li>' ),
			cacheLi		= $( '<li>' );

		$( '[data-eb-purge-cache]' ).appendTo( cacheLi ).show();

		$( cacheLi ).appendTo( '#toolbar.toolbar-list ul' );

		$( '#newpost' ).appendTo( li ).show();

		$( li ).appendTo( '#toolbar.toolbar-list ul' );

		$( '#toolbar.toolbar-list ul' ).children().each(function(){
			$(this).find( 'a' ).prepend( '<i>' );
		});
	<?php } else { ?>

		$( '[data-eb-purge-cache]' ).appendTo( '.icon-48-home' ).show();
		$('#newpost').appendTo('.icon-48-home').show();

	<?php }?>

	// move version notice to header
	$('.icon-48-home').css({ position: 'relative' , display: 'block'});

	$( '[data-eb-purge-cache-button]' ).on( 'click' , function()
	{
		ejax.load( 'easyblog' , 'purgeCache' );
	});

});
</script>
<table id="easyblog_panel">
	<tr>
		<td valign="top" width="65%" style="padding:10px">
			<ul id="easyblog-items">
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=settings') , 'settings.png' , JText::_('COM_EASYBLOG_HOME_SETTINGS') , JText::_('COM_EASYBLOG_HOME_SETTINGS_DESC') , false , 'setting' ); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=autoposting') , 'autoposting.png' , JText::_('COM_EASYBLOG_HOME_AUTOPOSTING') , JText::_('COM_EASYBLOG_HOME_AUTOPOSTING_DESC') , false , 'autoposting' ); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=blogs') , 'blogs.png' , JText::_('COM_EASYBLOG_HOME_BLOG_ENTRIES') , JText::_('COM_EASYBLOG_HOME_BLOG_ENTRIES_DESC') , false , 'blog' ); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=acls') , 'acls.png' , JText::_('COM_EASYBLOG_HOME_ACL') , JText::_('COM_EASYBLOG_HOME_ACL_DESC') , false , 'acl' ); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=users') , 'users.png' , JText::_('COM_EASYBLOG_HOME_BLOGGERS'), JText::_('COM_EASYBLOG_HOME_BLOGGERS_DESC') , false , 'user'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=categories') , 'categories.png' , JText::_('COM_EASYBLOG_HOME_CATEGORIES') , JText::_('COM_EASYBLOG_HOME_CATEGORIES_DESC') , false , 'category' ); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=comments') , 'comments.png' , JText::_('COM_EASYBLOG_HOME_COMMENTS'), JText::_('COM_EASYBLOG_HOME_COMMENTS_DESC') , false , 'comment'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=feeds') , 'rss.png' , JText::_('COM_EASYBLOG_HOME_FEEDS') , JText::_('COM_EASYBLOG_HOME_FEEDS_DESC') , false , 'feeds'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=spools') , 'spools.png' , JText::_('COM_EASYBLOG_HOME_MAIL_POOL') , JText::_('COM_EASYBLOG_HOME_MAIL_POOL_DESC') , false , 'mail'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=metas') , 'meta.png' , JText::_('COM_EASYBLOG_HOME_META_TAGS') , JText::_('COM_EASYBLOG_HOME_META_TAGS_DESC') , false , 'meta' ); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=migrators') , 'migrators.png' , JText::_('COM_EASYBLOG_HOME_MIGRATORS') , JText::_('COM_EASYBLOG_HOME_MIGRATORS_DESC') , false , 'migrator'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=pending') , 'pending.png' , JText::_('COM_EASYBLOG_HOME_PENDING_POSTS') , JText::_('COM_EASYBLOG_HOME_PENDING_POSTS_DESC') , false , 'pending'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=reports') , 'reports.png' , JText::_('COM_EASYBLOG_HOME_REPORTS') , JText::_('COM_EASYBLOG_HOME_REPORTS_DESC') , false , 'report'); ?>		
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=subscriptions') , 'subscriptions.png' , JText::_('COM_EASYBLOG_HOME_SUBSCRIPTIONS') , JText::_('COM_EASYBLOG_HOME_SUBSCRIPTIONS_DESC') , false , 'subscription'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=tags') , 'tags.png' , JText::_('COM_EASYBLOG_HOME_TAGS') , JText::_('COM_EASYBLOG_HOME_TAGS_DESC') , false , 'tag'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=teamblogs') , 'teamblogs.png' , JText::_('COM_EASYBLOG_HOME_TEAM_BLOGS') , JText::_('COM_EASYBLOG_HOME_TEAM_BLOGS_DESC') , false , 'teamblog'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=themes') , 'themes.png' , JText::_('COM_EASYBLOG_HOME_THEMES') , JText::_('COM_EASYBLOG_HOME_THEMES_DESC') , false , 'theme'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=trackbacks') , 'trackback.png' , JText::_('COM_EASYBLOG_HOME_TRACKBACKS'), JText::_('COM_EASYBLOG_HOME_TRACKBACKS_DESC') , false , 'trackback'); ?>
			<?php $this->addButton( JRoute::_('index.php?option=com_easyblog&view=updater') , 'updater.png' , JText::_('COM_EASYBLOG_HOME_UPDATER') , JText::_('COM_EASYBLOG_HOME_UPDATER_DESC') , false , 'updater'); ?>
			<?php $this->addButton( 'http://stackideas.com/docs/easyblog.html' , 'help.png' , JText::_('COM_EASYBLOG_HELP') , JText::_('COM_EASYBLOG_HELP_DESC') , true ); ?>
			</ul>
			<div class="clr"></div>
		</td>
		<td valign="top" style="padding:10px 10px 10px 0">
			<?php echo $this->loadTemplate('rightcol'); ?>
		</td>
	</tr>
</table>
<div style="text-align: right;margin: 10px 5px 0 0;">
	<?php echo JText::_('COM_EASYBLOG_ADMIN_FOOTER');?> <a href="http://stackideas.com" target="_blank">StackIdeas</a>
</div>
<div id="purgeCache" data-eb-purge-cache style="display: none;">
	<a href="javascript:void(0);" class="button-blue home-addpost" data-eb-purge-cache-button><?php echo JText::_( 'COM_EASYBLOG_PURGE_CACHE_BUTTON' ); ?></a>
</div>

<div id="newpost" style="display: none;">
	<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=blog' );?>" class="button-green home-addpost"><?php echo JText::_( 'COM_EASYBLOG_ADD_NEW_POST_BUTTON' ); ?></a>
</div>

<a id="newpost30" href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=blog' );?>" class="btn btn-success btn-small" style="display: none; margin: 0 5px 0 0; width: auto;">
	<i class="icon-pencil-2"></i>
	<?php echo JText::_( 'COM_EASYBLOG_ADD_NEW_POST_BUTTON' ); ?>
</a>

<a id="purgeCache30" href="javascript:void(0);" class="btn btn-small" data-eb-purge-cache-button style="display: none; margin: 0 0 0 5px">
	<i class="icon-refresh"></i>
	<?php echo JText::_( 'COM_EASYBLOG_PURGE_CACHE_BUTTON' ); ?>
</a>

