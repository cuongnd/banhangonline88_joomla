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
<?php if( $posts ){ ?>
<ul class="archive-list for-search reset-ul">
	<?php foreach( $posts as $post ){ ?>
	<li>
		<h3 class="blog-title rip mbs">
			<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $post->id );?>"><?php echo $post->title;?></a>
		</h3>

			<b><?php echo $this->formatDate( '%b %d %Y' , $post->created ); ?></b> -
			<?php echo $post->content; ?>...
			<br />
		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $post->id );?>"  class="search-permalink"><?php echo EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $post->id , false , true ); ?></a>
	</li>
	<?php } ?>
</ul>

<?php if ( $pagination->getPagesLinks() ) :?>
<div class="eblog-pagination"><?php echo $pagination->getPagesLinks();?></div>
<?php endif; ?>

<?php } ?>