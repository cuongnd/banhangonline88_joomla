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
<li class="blog-post micro-twitter<?php echo isset( $customClass ) ? ' item-' . $customClass : '';?>" itemscope itemtype="http://schema.org/Blog">

	<!-- @template: Date -->
	<?php if( $this->getParam( 'show_created_date' ) ){ ?>
		<!-- Creation date -->
		<div class="blog-created">
			<?php //echo JText::_( 'COM_EASYBLOG_ON' ); ?>
			<!-- @php -->
			<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $row->{$this->getParam( 'creation_source')} ); ?>">
				<div class="blog-text-date">
					<?php echo $this->formatDate( '%d %B %Y' , $row->{$this->getParam( 'creation_source')} );?>
				</div>
			</time>
		</div>
	<?php } ?>

	<div class="blog-tweet" itemprop="name">
		<h3 class="blog-title rip">
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $entry->id ); ?>" itemprop="url">
				<?php echo EasyBlogHelper::getHelper( 'String' )->linkTweets( EasyBlogHelper::getHelper( 'String' )->url2link(  $this->escape( $entry->text ) ) ); ?>
			</a>
		</h3>
	</div>

	<?php if( $this->getparam( 'show_tags' , true ) && !empty($entry->tags) ){ ?>
	<div class="mts">
		<?php echo $this->fetch( 'tags.item.php' , array( 'tags' => $entry->tags ) ); ?>
	</div>
	<?php } ?>
</li>
