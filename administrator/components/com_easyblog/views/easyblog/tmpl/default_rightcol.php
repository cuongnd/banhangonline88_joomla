<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="mb-10" id="versionInfo">
</div>

<script type="text/javascript">
EasyBlog.require().script('legacy').done(function($){
	$(".si_accordion > h3:first").addClass("active");
	$(".si_accordion > h3").siblings("div").hide();
	$(".si_accordion > h3:first + div").show();

	$(".si_accordion > h3").click(function(){
	  $(this).next("div").toggle().siblings("div").hide();
	  $(this).toggleClass("active");
	  $(this).siblings("h3").removeClass("active");
	});

	// Load news content via Ajax
	ejax.load( 'easyblog' , 'getNews' );
});
</script>
<div class="si_accordion">
	<h3><div><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_STATS_TITLE'); ?></div></h3>
	<div class="user-guide user-guide-stats">
		<?php echo $this->loadTemplate( 'stats' );?>
	</div>

	<h3><div><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_WHATS_NEXT_TITLE'); ?></div></h3>
	<div id="guide" class="user-guide">
		<ul class="reset-ul unstyled">
			<li>
				<b><span><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_SET_TITLE'); ?></span></b>
				<div><?php echo JText::sprintf('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_SET_TITLE_DESC', rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=settings'); ?></div>
				<a href="<?php echo rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=settings';?>" class="button"><?php echo JText::_( 'COM_EASYBLOG_QUICKGUIDE_CHANGE_TITLE_BUTTON' );?></a>
			</li>

			<li>
				<b><span><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_SETUP_PERMISSIONS'); ?></span></b>
				<div><?php echo JText::sprintf('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_SETUP_PERMISSIONS_DESC', rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=acls'); ?></div>
				<a href="<?php echo rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=acls';?>" class="button"><?php echo JText::_( 'COM_EASYBLOG_QUICKGUIDE_SET_PERMISSION_BUTTON' );?></a>
			</li>

			<li>
				<b><span><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_ADD_MORE_CATEGORIES'); ?></span></b>
				<div><?php echo JText::sprintf('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_ADD_MORE_CATEGORIES_DESC', rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=categories'); ?></div>
				<a href="<?php echo rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=category';?>" class="button"><?php echo JText::_( 'COM_EASYBLOG_QUICKGUIDE_ADD_CATEGORY_BUTTON' );?></a>
			</li>

			<li>
				<b><span><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_UPDATE_PROFILE'); ?></span></b>
				<div><?php echo JText::sprintf('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_UPDATE_PROFILE_DESC', rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=dashboard'); ?></div>
				<a href="<?php echo rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=dashboard';?>" class="button"><?php echo JText::_( 'COM_EASYBLOG_QUICKGUIDE_DASHBOARD_BUTTON' );?></a>
			</li>
			<li>
				<b><span><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_UPDATE_API_KEY'); ?></span></b>
				<div><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_UPDATE_API_KEY_DESC'); ?></div>
				<form name="apiform" action="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=settings&task=saveApi' );?>" method="post">
				<input type="text" class="inputbox" style="width: 200px;" name="apikey" value="<?php echo EasyBlogHelper::getConfig()->get( 'apikey' ); ?>" />
				<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_SAVE' );?>" class="button btn btn-api" />
				<input type="hidden" name="option" value="com_easyblog" />
				<input type="hidden" name="c" value="settings" />
				<input type="hidden" name="task" value="saveApi" />
				</form>
				<div class="pt-5">
					<?php echo JText::_( 'COM_EASYBLOG_QUICKGUIDE_RETRIEVE_API_KEY' ); ?> <a href="http://stackideas.com/api.html" target="_blank" /><?php echo JText::_( 'COM_EASYBLOG_DOWNLOADS_AREA');?></a>
				</div>
			</li>
			<li class="start">
				<div style="margin: 8px 0 0 0;"><span><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_NEED_HELP_STARTING_UP_DESC'); ?></span></div>
			</li>

		</ul>
	</div>

	<h3><div><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_ABOUT_TITLE'); ?></div></h3>
	<div class="user-guide">
		<?php echo $this->loadTemplate( 'about' );?>
	</div>

	<h3><div><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_NEWS_TITLE'); ?></div></h3>
	<div class="user-guide">
		<?php echo $this->loadTemplate( 'news' );?>
	</div>
</div>

<div class="social-channels" style="margin-top: 20px;">

	<h3>
		<div><?php echo JText::_( 'COM_EASYBLOG_OUR_SOCIAL_CHANNELS' );?></div>
	</h3>
	<div class="social-channels-content">
		<table>
			<tr>
				<td style="width: 160px;">
					<h3>Like us on Facebook</h3>
					<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>
					<div class="fb-like" data-href="http://www.facebook.com/StackIdeas" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
				</td>
				<td>
				<h3>Follow us on Twitter</h3>
				<a href="https://twitter.com/stackideas" class="twitter-follow-button" data-show-count="false">Follow @stackideas</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	
				</td>
			</tr>
		</table>
	</div>

</div>
