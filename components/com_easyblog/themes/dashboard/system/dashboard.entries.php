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

	eblog.dashboard.lists.init( 'entries' );

	// Expand the search
	$('#dashboard-entries #post-search').bind('focus', function(){

		$(this).animate({
			width: '200',
			backgroundPositionX: 210,
			backgroundPositionY: 'center'
		});
	});

	$('#dashboard-entries #post-search').bind( 'blur' , function(){
		$(this).animate({
			width: '150',
			backgroundPositionX: 160,
			backgroundPositionY: 'center'
		});
	});

});
</script>
<div id="dashboard-entries" class="prel stackSelectGroup">
	<div class="dashboard-head clearfix">
		<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
		<form name="search-entries" method="get" class="head-option clearfix">
			<input type="text" name="post-search" class="input text width-150 search-head float-r" id="post-search" value="<?php echo $this->escape( $search );?>" />
			<a class="buttons sibling-l<?php echo $postType == 'posts' ? ' pressed' : '';?>" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries&postType=posts');?>"><?php echo JText::_( 'COM_EASYBLOG_STANDARD_POSTS'); ?> <b><?php echo $postCount;?></b></a><?php if( $system->config->get( 'main_microblog' ) ){ ?><a class="buttons sibling-r<?php echo $postType == 'microblog' ? ' pressed' : '';?>" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries&postType=microblog');?>"><?php echo JText::_( 'COM_EASYBLOG_SHORT_UPDATES');?> <b><?php echo $microPostCount;?></b></a><?php } ?>
		</form>
	</div>
	<div class="ui-optbox clearfix fsm">
		<ul class="ui-entries-filter reset-ul float-li float-r">
			<li class="<?php echo $filter == 'all' ? 'active' : ''; ?>"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=all' . $urlType );?>"><?php echo JText::_('COM_EASYBLOG_FILTER_ALL'); ?></a></li>
			<li class="<?php echo $filter == 'published' ? 'active' : ''; ?>"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=published' . $urlType );?>"><?php echo JText::_('COM_EASYBLOG_FILTER_PUBLISHED'); ?></a></li>
			<li class="<?php echo $filter == 'unpublished' ? 'active' : ''; ?>"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=unpublished' . $urlType );?>"><?php echo JText::_('COM_EASYBLOG_FILTER_UNPUBLISHED'); ?></a></li>
			<li class="<?php echo $filter == 'scheduled' ? 'active' : ''; ?>"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=scheduled' . $urlType );?>"><?php echo JText::_('COM_EASYBLOG_FILTER_SCHEDULED'); ?></a></li>
		</ul>
		<div class="entries-select float-l">
			<?php if(!empty($this->acl->rules->publish_entry) || !empty($this->acl->rules->delete_entry) ) { ?>
			<ul class="ui-list-select-actions reset-ul float-li clearfix">
				<li>
					<input type="checkbox" class="stackSelectAll float-l" name="toggle" id="toggle"/>
					<label for="toggle" class="float-l mls mts"><?php echo JText::_( 'COM_EASYBLOG_SELECT_ALL' );?></label>
				</li>
				<li id="select-actions">
					<select name="entries-action" id="entries-action">
						<option value="default"><?php echo JText::_('COM_EASYBLOG_WITH_SELECTED');?></option>
						<option value="copy"><?php echo JText::_( 'COM_EASYBLOG_COPY_SELECTED' );?></option>
						<?php if( $this->acl->rules->publish_entry ){ ?>
							<option value="publishBlog"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
							<option value="unpublishBlog"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
						<?php } ?>
						<?php if( $this->acl->rules->delete_entry ){ ?>
							<option value="deleteBlog"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
						<?php } ?>
					</select>
					<input type="button" class="ui-button" value="<?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>" onclick="eblog.dashboard.action( 'entries', 'index.php?option=com_easyblog&view=dashboard&layout=entries' );"/>
				</li>
			</ul>
			<?php } ?>
		</div>
	</div>
	<form name="entries-form" id="entries-form">
		<ul class="item_list reset-ul">

		<?php if ( $entries ) : ?>

			<?php $i = 0; ?>
			<?php foreach( $entries as $entry ):
			$blogAuthor = EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blogAuthor->load( $entry->created_by );

			$team		= '';
			$teamBlog	= null;

			if( isset($entry->team_id) )
			{
				if( !empty( $entry->team_id ) )
				{
					$team		= '&team=' . $entry->team_id;
					$teamBlog   = EasyBlogHelper::getTable( 'TeamBlog', 'Table');
					$teamBlog->load( $entry->team_id );
				}
			}

			?>
			<li id="eb-entry-<?php echo $entry->id; ?>">
				<div class="listing">
					<span class="ui-list-select pabs"><input type="checkbox" class="stackSelect" value="<?php echo $entry->id; ?>" name="cid[]" id="cb<?php echo $i;?>" /></span>
					<div class="ui-avatar float-l">
						<?php if( !empty($team) ) : ?>
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $teamBlog->id); ?>" title="<?php echo $teamBlog->title; ?>"  class="isTeamBlog pabs">
								<img src="<?php echo $teamBlog->getAvatar(); ?>" alt="<?php echo $teamBlog->title; ?>" class="avatar" />
							</a>
						<?php endif; ?>
						<a href="<?php echo $blogAuthor->getProfileLink(); ?>" title="<?php echo $blogAuthor->getName(); ?>"  class="isBlogger pabs">
							<img src="<?php echo $blogAuthor->getAvatar(); ?>" alt="<?php echo $blogAuthor->getName(); ?>" class="avatar" />
						</a>
					</div>

					<div class="ui-content">
						<?php if( !empty( $entry->source ) ){ ?>
						<b class="item-type item-type-<?php echo strtolower( $entry->source );?> float-r" title="<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper( $entry->source ) ); ?>"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper( $entry->source ) ); ?></b>
						<?php } ?>

						<div class="item_title">
							<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id );?>"><?php echo $entry->title;?></a>
							<?php if( $entry->isFeatured ){ ?><sup class="featured-item"><?php echo JText::_( 'COM_EASYBLOG_FEATURED_FEATURED' );?></sup><?php } ?>
						</div>
						<div class="ui-item-content">
							<ul class="ui-entry-meta clearfix reset-ul float-li">
								<?php if ( $entry->published == 2 || $entry->published == 3 ) { ?>
								<li id="publishing-<?php echo $entry->id?>" class="ico-publish">
									<?php if ( $entry->published == 2 ) : ?>
										<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_POST_IS_SCHEDULED'); ?></span>
									<?php elseif($entry->published == 3) : ?>
										<span><?php echo JText::_('COM_EASYBLOG_DRAFT');?></span>
									<?php endif; ?>
								</li>
								<?php } ?>
								<li class="ico-comments">
									<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id . '#comment' );?>"><?php echo $this->getNouns( 'COM_EASYBLOG_COMMENTS_COUNT' , $entry->totalComments , true ); ?></a>
								</li>
								<li class="ico-tags">
									<?php if (count($entry->tags)>0): ?>
										<?php echo $this->getNouns( 'COM_EASYBLOG_TAGS_COUNT' , count( $entry->tags ) , true ); ?>
										<?php echo EasyBlogTooltipHelper::getTagsHTML( $entry->tags, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'parent')) ); ?>
									<?php else: ?>
										<?php echo $this->getNouns( 'COM_EASYBLOG_TAGS_COUNT' , count( $entry->tags ) , true ); ?>
									<?php endif; ?>
								</li>
								<li class="ico-hits">
									<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id );?>"><?php echo $entry->hits;?> <?php echo JText::_( 'COM_EASYBLOG_HITS_TOTAL_TITLE');?></a>
								</li>
								<li class="ico-category"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $entry->category_id); ?>" class="fsm"><?php echo $entry->category; ?></a></li>
							</ul>
							<span class="ui-inmsg mlm"></span>
						</div>

						<ul id="eblog-comment-toolbar<?php echo $entry->id; ?>" class="ui-button-option ui-button-group clearfix reset-ul float-li mtm">
							<?php
								foreach( $consumers as $consumer )
								{
									$acl	= 'update_' . $consumer->type;

									if( isset( $this->acl->rules->$acl ) && $this->acl->rules->$acl && $system->config->get( 'integrations_' . $consumer->type . '_centralized_and_own' ) )
									{
										$shared	= $consumer->isShared( $entry->id );
										$state	= $shared ? '' : '_disabled';
							?>
							<li class="has-tooltip">
								<a href="javascript:void(0)" onclick="eblog.socialshare.share('<?php echo $entry->id;?>' , '<?php echo $consumer->type;?>' );" class="buttons social-share">
									<img id="oauth_img_<?php echo $consumer->type;?>_<?php echo $entry->id;?>" src="<?php echo JURI::root();?>/components/com_easyblog/assets/icons/socialshare/<?php echo $consumer->type . $state;?>.png" />
								</a>
								<div class="tip-item stackTip tipsA">
									<div>
									<script type="text/x-json">({ my: 'right top', at: 'left bottom', of: { traverseUsing: 'prev' } })</script>
									<i></i>
											<?php if( $shared ){ ?>
												<span class="socialshare_<?php echo $consumer->type;?>"><?php echo JText::sprintf( 'COM_EASYBLOG_OAUTH_BLOG_ENTRY_SHARED_ON' , ucfirst( $consumer->type ) , $this->formatDate( $system->config->get('layout_dateformat'), $consumer->getSharedDate( $entry->id ) ) );?></span>
											<?php } else { ?>
												<span class="socialshare_<?php echo $consumer->type;?>"><?php echo JText::sprintf( 'COM_EASYBLOG_OAUTH_BLOG_ENTRY_NOT_SHARED' , ucfirst( $consumer->type ) );?></span>
											<?php } ?>
									</div>
								</div>
							</li>
							<?php
									}
								}
							?>
							<?php if ( $entry->published == 0 || $entry->published == 1 ) : ?>
							<li id="publishing-<?php echo $entry->id?>" class="ico-publish">
								<?php if ( $entry->published == 0 ) : ?>
								<a class="buttons published" href="javascript:void(0);" onclick="eblog.blog.togglePublish( '<?php echo $entry->id;?>' );"><?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?></a>
								<?php elseif($entry->published == 1) : ?>
								<a class="buttons unpublished" href="javascript:void(0);" onclick="eblog.blog.togglePublish( '<?php echo $entry->id;?>' );"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?></a>
								<?php endif; ?>
							</li>
							<?php endif; ?>
							<?php if( $this->acl->rules->add_entry ) : ?>
							<li><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write&blogid='.$entry->id);?><?php echo ($system->config->get( 'layout_dashboardanchor' ) ) ? '#write-entry' : '';?>" class="buttons"><?php echo JText::_('COM_EASYBLOG_EDIT'); ?></a> </li>
							<?php endif; ?>

							<?php if( $this->acl->rules->delete_entry ) : ?>
							<li><a href="javascript:eblog.blog.confirmDelete('<?php echo $entry->id;?>', '<?php echo 'index.php?option=com_easyblog&view=dashboard&layout=entries'; ?>');" class="buttons"><?php echo JText::_('COM_EASYBLOG_DELETE'); ?></a></li>
							<?php endif; ?>
							<li><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$entry->id);?>" class="buttons"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW' );?></a></li>
							<span class="ui-clock float-r"><?php echo $this->formatDate( JText::_( 'DATE_FORMAT_LC1' ) ,  $entry->created); ?></span>
						</ul>
					</div>

				</div>
			</li>
			<?php $i++; ?>
			<?php endforeach; ?>

		<?php else: ?>
			<li class="no_item">
				<div><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_EMPTY'); ?></div>
			</li>
		<?php endif; ?>
		</ul>

		<?php if ( !empty($pagination) ) : ?>
			<div class="eblog-pagination"><?php echo $pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
	</form>
</div>
