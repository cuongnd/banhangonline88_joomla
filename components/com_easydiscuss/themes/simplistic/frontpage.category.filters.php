<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$catUnreadCount 	= $category->getUnreadCount( false );

// Set TRUE to exclude featured post in the unresolved count
$catUnresolvedCount = $category->getUnresolvedCount( false );

$catUnansweredCount = $category->getUnansweredCount( false);
?>
<!-- Category Filters -->
<div class="discuss-filter mt-20 mr-10">
	<ul class="nav nav-tabs">

		<li class="filterItem<?php echo !$category->activeFilter || $category->activeFilter == 'allposts' || $category->activeFilter == 'all' ? ' active' : '';?>">
			<a class="btn-small allPostsFilter" data-foundry-toggle="tab" href="#">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
		</li>

		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $catUnreadCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unread' ? ' active' : '';?>">
			<a class="newPostsFilter" data-foundry-toggle="tab" href="#">
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
				<span class="label label-important label-notification"><?php echo $catUnreadCount;?></span>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unresolved') && $system->config->get('main_qna') && $catUnresolvedCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unresolved' ? ' active' : '';?>">
			<a class="unResolvedFilter" data-foundry-toggle="tab" href="#">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
				<span class="label label-important label-notification"><?php echo $catUnresolvedCount;?></span>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unanswered') && $catUnansweredCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unanswered' ? ' active' : '';?>">
			<a class="unAnsweredFilter" data-foundry-toggle="tab" href="#">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				<?php if( $category->getUnansweredCount() ){ ?>
				<span class="label label-important label-notification"><?php echo $catUnansweredCount;?></span>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
	</ul>
	<ul class="nav nav-tabs nav-tabs-alt">
		<!-- @php -->
		<li class="filterItem<?php echo $category->activeSort == 'latest' || $category->activeSort == '' ? ' active' : '';?> secondary-nav">
			<a class="btn-small sortLatest" data-foundry-toggle="tab" href="#"><?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?></a>
		</li>

		<!-- @php -->
		<li class="filterItem<?php echo $category->activeSort == 'popular' ? ' active' : '';?> secondary-nav">
			<a class="sortPopular" data-foundry-toggle="tab" href="#" <?php echo ($category->activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> ><?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?></a>
		</li>

	</ul>
</div>
