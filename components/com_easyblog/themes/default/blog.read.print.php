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
<?php
$blogLink = '<a href="'.$blogger->getProfileLink().'">'.$blogger->getName().'</a>';
?>

<div id="blog-title">
	<h1><?php echo $blog->title; ?></h1>
</div>

<?php
$date		= EasyBlogDateHelper::dateWithOffSet($blog->created);
$postdate	= EasyBlogDateHelper::toFormat($date, $config->get('layout_shortdateformat', '%b %d'));
?>

<div class="title-wrapper no-avatar">
	<div class="meta1">
		<div class="inner">
			<span class="post-date"><?php echo $postdate; ?></span>

			<span class="post-category">
				<?php echo JText::sprintf('COM_EASYBLOG_POSTED_BY_AUTHOR', $blogger->getProfileLink(), $blogger->getName()); ?>
				<?php echo JText::sprintf('COM_EASYBLOG_IN', EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$blog->category_id), $this->escape($blog->getCategoryName())); ?>
			</span>
		</div>
	</div>
</div>
<?php echo $blog->event->afterDisplayTitle; ?>
<?php echo $blog->event->beforeDisplayContent; ?>
<div class="post-content clearfix">
	<?php echo $blog->content; ?>
</div>
<?php echo $blog->event->afterDisplayContent; ?>

<div class="post-copyright clearfix">
	<?php echo $blog->copyrights; ?>
</div>

<?php if( count($tags) > 0 )
{
	?><span class="post-tags1"><?php echo JText::_('COM_EASYBLOG_TAGS'); ?>: </span><?php

	$spans	= array();
	foreach ($tags as $tag)
	{
		ob_start();
		?><a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id ); ?>"><span itemprop="keywords"><?php echo JText::_( $tag->title ); ?></span></a><?php
		$spans[] = ob_get_clean();
	}

	echo implode(', ', $spans);
}
?>
<script type="text/javascript">
window.print();
</script>

<ul class="blog-icons">
	<li class="print">
		<a rel="nofollow" onclick="window.print();" title="<?php echo JText::_('PRINT'); ?>" href="javascript: void(0)">
			<?php echo JText::_('PRINT'); ?>
		</a>
	</li>
</ul>
