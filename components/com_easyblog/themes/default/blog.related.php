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
<div id="section-related" class="blog-related blog-section tab_container">
	<div>
		<ul class="entry-related-post reset-ul">
		<?php foreach( $blogRelatedPost as $post ){ ?>
		<?php
			$blogger	= EasyBlogHelper::getTable( 'Profile', 'Table');
			$blogger->load( $post->created_by );
		?>
			<li id="entry_<?php echo $post->id; ?>">
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$post->id); ?>"><?php echo $post->title; ?></a> 
				 - <a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $post->category_id );?>" class="blog-category small"><?php echo $this->escape( JText::_( $post->category ) ); ?></a>
				<span class="blog-date float-r small"><?php echo $this->formatDate( $system->config->get('layout_shortdateformat'), $post->created ); ?></span>
			</li>
		<?php } ?>
	    </ul>
    </div>
</div>
