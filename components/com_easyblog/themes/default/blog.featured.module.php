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
<?php if( $featured && $system->config->get( 'layout_featured' ) ) { ?>
<script type="text/javascript">
EasyBlog.require()
.script( 'featured' )
.done(function( $ ){

	$( '.featured-slider' ).implement( EasyBlog.Controller.Featured.Scroller ,
	{
		autorotate : {
			enabled :  <?php echo $system->config->get( 'layout_featured_autorotate' ) == '0' ? 'false' : 'true';?>,
			interval: <?php echo $system->config->get( 'layout_featured_autorotate_interval' );?>
		}
	},
	function(){

	});

});
</script>
<div id="ezblog-featured" class="featured-slider prel">
	<div class="slider-holder prel">
		<ul class="featured-entries reset-ul float-li pabs">
		<?php foreach ( $featured as $row ){ ?>
			<li class="featured-item">
				<?php if( $row->getImage() ){ ?>
					<!-- Blog Image -->
					<b class="featured-tag pabs"></b>
					<div class="featured-image prel">
						<div>
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>" title="<?php echo $this->escape( $row->title );?>"><img src="<?php echo $row->getImage()->getSource( 'featured' );?>" /></a>
						</div>
					</div>
				<?php } else if( $row->featuredImage ) { ?>
	 				<!-- Support for legacy images -->
					<b class="featured-tag pabs"></b>
					<div class="featured-image prel">
						<div>
							<?php echo $row->featuredImage;?>
						</div>
					</div>
				<?php } ?>

				<div class="featured-meta">
					<h3 class="featured-title rip"><a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $row->id );?>"><?php echo $row->title;?></a></h3>
						<div class="featured-content">
						   <?php $content = "";

						   if( !empty($row->intro) )
						   {
						   		$content = $row->intro;
						   }
						   else
						   {
						   		$content = $row->content;
						   }
						   echo JString::substr( strip_tags( $content ) , 0 , $system->config->get( 'layout_featured_intro_limit' ) ); ?>
						</div>

						<div class="featured-readmore mtm mbm">
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>"  class="featured-readmore"><span><?php echo JText::_('COM_EASYBLOG_READ_MORE'); ?></span></a>
						</div>

						<!--SHOWCASE AUTHOR-->
						<div class="featured-author">
							<?php if ( $system->config->get('layout_avatar') ) : ?>
							<a href="<?php echo $row->author->getProfileLink(); ?>" class="float-l avatar mrm"><img src="<?php echo $row->author->getAvatar(); ?>" width="35" height="35" class="avatar" /></a>
							<?php endif; ?>
							<div class="showcase-author">
								<?php echo JText::_('COM_EASYBLOG_BY'); ?> <a href="<?php echo $row->author->getProfileLink(); ?>"><?php echo $row->author->getName(); ?></a><br />
								<div class="small"><?php echo $this->formatDate( $system->config->get('layout_dateformat') , $row->created ); ?></div>
							</div>
						</div>

						<?php if( $system->config->get( 'main_ratings_frontpage' ) ) { $locked = $system->config->get( 'main_ratings_frontpage_locked' ); ?>
						<div class="blog-rating mtm">
							<?php echo EasyBlogHelper::getHelper( 'ratings' )->getHTML( $row->id , EBLOG_RATINGS_TYPE_ENTRY , JText::_( 'COM_EASYBLOG_RATINGS_RATE_BLOG_ENTRY') , 'featured-blog-' . $row->id . '-ratings' , isset( $locked ) ? $locked : false ); ?>
						</div>
						<?php } ?>
				</div>
				<div class="clear"></div>
			</li>
		<?php } ?>
		</ul>
	</div>

	<div class="featured-navi prel">
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=featured'); ?>" title="<?php echo JText::_('COM_EASYBLOG_FEATURED_VIEWMORE');?>" class="featured-more ir pabs"><?php echo JText::_('COM_EASYBLOG_FEATURED_VIEWMORE');?></a>
		<div class="featured-a">
			<?php for( $i = 1; $i <= count( $featured ); $i++ ){ ?>
				<a class="slider-navi-<?php echo $i;?> item<?php echo $i == 1 ? ' active' : '';?>" href="javascript:void(0);" data-slider="<?php echo $i;?>"><span><?php echo $i;?></span></a>
			<?php } ?>
		</div>
	</div>
</div>
<?php } ?>
