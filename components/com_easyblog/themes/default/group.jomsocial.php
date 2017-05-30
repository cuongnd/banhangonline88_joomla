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
<?php if( !$ajax ){ ?>
<script type="text/javascript">
EasyBlog.ready(function($){
	window.readmoreBlogs = function()
	{
		var limit	= $( '#easyblog-limit' ).val();
		$.ajax({
			url: '<?php echo JURI::root();?>index.php?easyblog_external=1&no_html=1&tmpl=component&id=<?php echo $id;?>&showmoreBlogs=' + limit,
			success: function( data ){
				var obj = $.parseJSON( data );

				if( !obj.hasMore )
				{
					$( '#activity-more' ).hide();
				}

				if( obj.html != '' )
				{
					$( 'ul.blog-items' ).append( obj.html );
				}

				// Update the limitstart
				$( '#easyblog-limit' ).val( obj.limitstart );
			}
		});
	}
});
</script>
<div class="app-box" id="community-group-blogs">
	<?php if( !$tabs ){ ?>
	<div class="app-box-header">
		<div class="app-box-header">
			<h2 class="app-box-title"><?php echo JText::_( 'COM_EASYBLOG_JOMSOCIAL_GROUP_BLOGS_HEADING' );?></h2>
			<div class="app-box-menus">
				<div class="app-box-menu toggle"><a onclick="joms.apps.toggle('#community-group-blogs');" href="javascript: void(0)" class="app-box-menu-icon"><span class="app-box-menu-title"></span></a></div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="app-box-content">
		<div id="ezblog-body">
			<?php if( $showRss ){ ?>
			<div style="text-align:right;">
				<a href="<?php echo EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=latest' );?>&amp;group=<?php echo $id;?>" class="subscribe-rss" target="_blank"><?php echo JText::_( 'GROUP_EASYBLOG_SUBSCRIBE_TO_RSS' ); ?></a>
			</div>
			<?php } ?>
			<ul class="blog-items reset-ul">


<?php } ?>
			<?php if( count($blogs) > 0 ) : ?>
				<?php foreach( $blogs as $blog ){ ?>
				<?php $user	= CFactory::getUser( $blog->created_by ); ?>
				<li class="js-blog-item">
					<?php if( $params->get( 'avatar' , 1 ) == '1' ){ ?>
					<div class="js-blog-date">
						<?php list( $day , $month ) = explode( ' ' , $blog->formattedDate ); ?>
						<div class="blog-date-d"><?php echo $day; ?></div>
						<div class="blog-date-m"><?php echo $month; ?></div>
					</div>
					<?php } else if( $params->get( 'avatar' , 1 ) == '2') { ?>
					<a href="<?php echo $blog->blogger->getProfileLink();?>" class="js-blog-avatar">
						<img src="<?php echo $user->getAvatar(); ?>" alt="<?php echo $user->getDisplayName();?>" class="avatar" width="50" height="50" />
					</a>
					<?php } ?>
					<div class="js-blog-body">
						<div class="js-blog-head">
							<div class="js-blog-meta small">
							<?php if( $params->get( 'author' ) ){ ?>
								<?php echo JText::_( 'COM_EASYBLOG_POSTED_BY' );?>
								<a href="<?php echo $blog->blogger->getProfileLink();?>"><?php echo $user->getDisplayName();?></a>
							<?php } ?>
							<?php if( $params->get( 'categories' ) ){ ?>
								<?php echo JText::sprintf( 'COM_EASYBLOG_IN' , EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$blog->category_id) , $blog->category->title ); ?>
							<?php } ?>

							<?php if( $params->get( 'avatar' , 1 ) != '1') : ?>
							<?php echo JText::_( 'COM_EASYBLOG_ON' );?>
							<?php echo $blog->systemDate; ?>
							<?php endif; ?>

							</div>
							<h3 class="js-blog-title">
								<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id );?>"><?php echo $blog->title;?></a>
							</h3>
						</div>

						<!-- Blog Image -->
							<?php if($blog->getImage()){ ?>
								<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$blog->id); ?>" title="<?php echo $this->escape($blog->title);?>" class="blog-image float-l mrm mbm">
									<img src="<?php echo $blog->getImage()->getSource('frontpage');?>" alt="<?php echo $this->escape($blog->title);?>" />
								</a>
							<?php } ?>

						<?php if( $params->get( 'contents' ) ){ ?>
						<div class="js-blog-content">
						<?php if( !empty( $blog->intro ) ) { ?>
							<?php if( $params->get( 'truncate') != 0 ){ ?>
								<?php echo JString::substr( strip_tags( $blog->intro ) , 0 , $params->get( 'truncate' ) ); ?>
							<?php } else { ?>
								<?php echo $blog->intro; ?>
							<?php } ?>

						<?php } else { ?>
							<?php if( $params->get( 'truncate') != 0 ){ ?>
								<?php echo JString::substr( strip_tags( $blog->content ) , 0 , $params->get( 'truncate' ) ); ?>
							<?php } else { ?>
								<?php echo $blog->content; ?>
							<?php } ?>
						<?php } ?>
						</div>
						<?php } ?>

						<?php if( $params->get( 'ratings' ) ){ ?>
						<div class="js-blog-rate">
							<div class="blog-rating">
								<?php echo EasyBlogHelper::getHelper( 'ratings' )->getHTML( $blog->id , EBLOG_RATINGS_TYPE_ENTRY , JText::_( 'COM_EASYBLOG_RATINGS_RATE_BLOG_ENTRY') , 'blog-' . $blog->id . '-ratings' ); ?>
							</div>
						</div>
						<?php } ?>

						<div class="js-blog-more">
							<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id );?>"><?php echo JText::_( 'COM_EASYBLOG_CONTINUE_READING' ); ?></a>
						</div>
					</div>
				</li>
				<?php } ?>
			<?php endif; ?>
<?php if( !$ajax ){ ?>
			</ul>
			<?php if( $total == 0 ){ ?>
				<div class="cNotice cNotice-App"><?php echo JText::_( 'GROUP_EASYBLOG_NO_BLOGS_YET' );?></div>
			<?php } ?>
			<?php if( $total > $limit ){ ?>
			<div id="activity-more" class="joms-newsfeed-more">
				<a onclick="readmoreBlogs();" href="javascript:void(0);" class="more-activity-text"><?php echo JText::_( 'COM_EASYBLOG_MORE_BLOGS' );?></a>
				<div class="loading"></div>
			</div>
			<?php } ?>
			<input type="hidden" name="easyblog-limit" id="easyblog-limit" value="<?php echo $limit; ?>" />
		</div>
	</div>
</div>
<?php } ?>
