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

<div id="ezblog-body" class="category-wrapper-<?php echo $category->id; ?>">
	<?php if( $this->getParam('show_category_header', '1' ) ){ ?>
	<div id="ezblog-detail" class="forCategory mtl">
		<div class="profile-head clearfix">
			<?php if($config->get('layout_categoryavatar', true)) : ?>
			<div class="profile-avatar float-l prel">
				<i class="pabs"></i>
				<span class="avatar-wrapper">
				<img src="<?php echo $category->getAvatar();?>" align="top" class="avatar" />
				</span>
			</div>
			<?php endif; ?>
			<div class="profile-info">
				<h3 class="profile-title rip mbm"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id); ?>"><?php echo JText::_( $category->title ); ?></a></h3>
				<?php if ($category->get( 'description' ) ) : ?>
				<div class="profile-info">
					<?php echo $category->get( 'description' ); ?>
				</div>
				<?php endif; ?>
				<div class="profile-connect">
					<ul class="connect-links reset-ul float-li clearfix">
					<?php if($config->get('main_categorysubscription')) { ?>
					<li>
						<a href="javascript:eblog.subscription.show( '<?php echo EBLOG_SUBSCRIPTION_CATEGORY; ?>' , '<?php echo $category->id;?>');" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?>" class="link-subscribe">
							<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?></span>
						</a>
					</li>
					<?php } ?>

					<?php if( $privacy->allowed ) : ?>
						<?php if( $config->get('main_rss') ){ ?>
						<li>
							<a href="<?php echo $category->getRSS();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>" class="link-rss">
								<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
							</a>
						</li>
						<?php } ?>
					<?php endif; ?>

						<li><span class="total-post"><?php echo $this->getNouns( 'COM_EASYBLOG_CATEGORIES_COUNT' , $category->cnt , true ); ?></span></li>
					</ul>
				</div>
				<?php if(! empty($category->nestedLink)) { ?>
				<div class="blogger-child small ptm mtm" style="border-top:1px dotted #ccc">
					<span><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_SUBCATEGORIES' ); ?></span>
					<?php echo $category->nestedLink; ?>
				</div>
				<?php } ?>
			</div><!--end: .profile-info-->

			<div class="clear"></div>
		</div><!--end: .profile-head-->
	</div>
	<?php } ?>

	<?php if ( $teamBlogCount > 0) { ?>
	<div class="eblog-message info"><?php echo $this->getNouns( 'COM_EASYBLOG_CATEGORIES_LISTINGS_TEAMBLOG_COUNT' , $teamBlogCount , true ); ?></div>
	<?php } ?>

	<div id="ezblog-posts" class="forCategory">
		<?php if($system->my->id == 0 && $category->private == 1 ){ ?>
			<div class="eblog-message warning"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_FOR_REGISTERED_USERS_ONLY');?></div>
		<?php } else if( $category->private == 2 && !$allowCat ) { ?>
			<div class="eblog-message warning"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_NOT_ALLOWED');?></div>
		<?php } else { ?>
			<?php if( $blogs ) { ?>
				<?php foreach( $blogs as $row ){ ?>
					<?php if( $system->config->get( 'main_password_protect' ) && !empty( $row->blogpassword ) ){ ?>
						<!-- Password protected theme files -->
						<?php echo $this->fetch( 'blog.item.protected.php' , array( 'row' => $row ) ); ?>
					<?php } else { ?>
						<!-- Normal post theme files -->
						<?php echo $this->fetch( 'blog.item'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php' , array( 'row' => $row ) ); ?>
					<?php } ?>
				<?php } ?>
			<?php } else { ?>
				<div class="eblog-message warning"><?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?></div>
			<?php } ?>

			<?php if( $pagination ){ ?>
				<div class="eblog-pagination"><?php echo $pagination; ?></div>
			<?php } ?>
		<?php } ?>
	</div>

</div>
