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
EasyBlog.require().script('legacy').done(function($){
	ejax.load( 'archive' , 'loadCalendar', 'component', '<?php echo $itemId; ?>', 'small', 'blog', '' );
});
</script>
<div id="ezblog-body">
	<div id="ezblog-label" class="latest-post clearfix">
		<span><?php echo JText::_( 'COM_EASYBLOG_ARCHIVE_PAGE_TITLE' ); ?></span>
		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=archive' );?>" class="float-r"><?php echo JText::_( 'COM_EASYBLOG_SWITCH_TO_LIST_VIEW' ); ?></a>
	</div>

	<div id="easyblogcalendar-component-wrapper" class="com_easyblogcalendar mtl">
		<div style="text-align:center;"><?php echo JText::_('COM_EASYBLOG_ARCHIVE_CALENDAR_LOADING'); ?></div>
		<div style="text-align:center;"><img src="<?php echo rtrim(JURI::root(), '/').'/components/com_easyblog/assets/images/loader.gif' ?>" /></div>
	</div>
	<div class="clearfix"></div>
</div>