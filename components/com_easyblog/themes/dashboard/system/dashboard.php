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
<div class="dashboard-head clearfix">
	<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
	<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $system->profile->id );?>" target="_blank" class="buttons"><?php echo JText::_( 'COM_EASYBLOG_VIEW_YOUR_BLOG' );?></a>
</div>
<div id="write_container">
	<?php if( $system->config->get( 'layout_dashboardstats') ){ ?>
	<div class="ui-modbox" class="widget-stats">
		<div class="ui-modhead">
			<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_PAGE_HEADING'); ?></div>
		</div>
		<div class="ui-modbody clearfix">
			<ul class="ui-statinfo reset-ul float-li">
			<li>
				<span class="stat-info"><?php echo $blogStat->blog; ?></span>
				<span>
					<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=entries' );?>"><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_POST_COUNT' , $blogStat->blog );?></a>
				</span>
			</li>
			<li>
				<span class="stat-info"><?php echo $blogStat->category; ?></span>
				<span>
					<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories' );?>"><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_COUNT' , $blogStat->category );?></a>
				</span>
			</li>
			<li>
				<span class="stat-info"><?php echo $blogStat->tag; ?></span>
				<span>
					<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=tags' );?>"><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_TAGS_COUNT' , $blogStat->tag );?></a>
				</span>
			</li>
			<?php if(! $system->config->get('comment_intensedebate') && !$system->config->get('comment_disqus') && !$system->config->get('comment_jomcomment') && !$system->config->get('comment_jcomments') && !$system->config->get('comment_rscomments')){ ?>
			<li>
				<span class="stat-info"><?php echo $blogStat->comment; ?></span>
				<span>
					<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=comments' );?>"><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_COMMENTS_COUNT' , $blogStat->comment );?></a>
				</span>
			</li>
			<?php } ?>

			<?php if( $system->config->get( 'main_bloggersubscription' ) ){ ?>
			<li>
				<span class="stat-info"><?php echo $blogStat->subscriber; ?></span>
				<span><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_SUBSCRIBERS_COUNT' , $blogStat->subscriber );?></span>
			</li>
			<?php } ?>
			
			<?php if($system->config->get('layout_teamblog')){ ?>
			<li>
				<span class="stat-info"><?php echo $blogStat->team; ?></span>
				<span><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_TEAMS_COUNT' , $blogStat->subscriber );?></span>
			</li>
			<?php } ?>
			<li>
				<span class="stat-info"><?php echo $blogStat->totalHits;?></span>
				<span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_TOTAL_HITS' );?></span>
			</ul>
		</div>
	</div>
	<?php } ?>

	<div class="ui-modbox" class="widget-stream">
		<div class="ui-modhead">
			<div class="ui-modtitle"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_YOUR_ACTIVITY_LOG' );?></div>
		</div>
		<div class="ui-modbody clearfix">
			<?php if( count( $activities ) > 0 ) { ?>
			<ul id="stream-container" class="ui-stream reset-ul">
					<?php
					foreach( $activities as $activity)
					{
						echo $activity;
					}
					?>
			</ul>
			<ul class="ui-stream reset-ul">
				<li id="stream-load" class="stream-load" <?php echo ( empty($hasNextStream) ) ? 'style="display:none;"' : ''; ?> ><a href="javascript:void(0);" onclick="eblog.stream.load('<?php echo $currentDate['startdate']; ?>')"><?php echo JText::_('COM_EASYBLOG_STREAM_LOAD_MORE'); ?></a></li>
			</ul>
			<?php } else {
				echo JText::_( 'COM_EASYBLOG_STREAM_NO_ACTIVITY' );
			} ?>
		</div>
	</div>
</div>

