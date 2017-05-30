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
$mainframe	= JFactory::getApplication();
?>
<div id="ezblog-body">
	<div id="ezblog-section"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_PAGE_HEADING'); ?></div>
	<div id="ezblog-category">

	<?php if( count($data) > 0 ) { ?>

		<ul class="list-categories reset-ul">

		<?php foreach($data as $category) { ?>
			<li style="margin-left: <?php echo $category->depth * 25;?>px" class="<?php echo ( $category->depth ) ? 'child' : 'parent'; ?> category-<?php echo $category->id;?>">
				<div class="clearfull">

					<?php if($system->config->get('layout_categoryavatar', true)) { ?>
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id); ?>" class="avatar float-l">
						<img src="<?php echo $category->getAvatar();?>" align="top" width="35" class="avatar" />
					</a>
					<?php } ?>


					<div class="category-story">
						<h3 class="category-name reset-h">
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id); ?>"><?php echo JText::_( $category->title ); ?></a>
						</h3>

						<?php if ( $category->description ) { ?>
						<div class="category-description">
							<?php echo $category->description; ?>
						</div>
						<?php } ?>

						<ul class="category-status reset-ul float-li in-block mt-5">
							<?php if($system->config->get('main_categorysubscription')) { ?>
							<?php if( ($category->private && $system->my->id != 0 ) || ($system->my->id == 0 && $system->config->get( 'main_allowguestsubscribe' )) || $system->my->id != 0) : ?>
								<li>
									<a href="javascript:eblog.subscription.show( '<?php echo EBLOG_SUBSCRIPTION_CATEGORY; ?>' , '<?php echo $category->id;?>');" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?>" class="link-subscribe">
										<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?></span>
									</a>
								</li>
							<?php endif; ?>
							<?php } ?>
							<?php if( $system->config->get('main_rss') ){ ?>
								<li>
									<a href="<?php echo $category->getRSS() ;?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>" class="link-rss">
										<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
									</a>
								</li>
							<?php } ?>

							<li class="total-post">
								<span><?php echo $this->getNouns( 'COM_EASYBLOG_CATEGORIES_POST_COUNT' , $category->cnt , true ); ?></span>
							</li>
						</ul>
						<div class="total-discuss"></div>
					</div>
				</div>
			</li>

		<?php } //end foreach ?>

		</ul>

	<?php } ?>

	<?php if(count($data) <= 0) { ?>
	<div><?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND'); ?></div>
	<?php } ?>

	</div>

</div><!--end: #ezblog-body-->
