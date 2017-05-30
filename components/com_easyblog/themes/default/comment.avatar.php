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
<?php if( $system->config->get( 'layout_avatar' ) ){ ?>
	<a href="<?php echo $comment->poster->id > 0 ? $comment->poster->getProfileLink() : 'javascript:void(0)' ; ?>" title="<?php echo $this->escape( $comment->poster->getName() );?>" class="comment-avatar avatar float-l">
		<img src="<?php echo $comment->poster->getAvatar(); ?>" alt="<?php echo $this->escape( $comment->poster->getName() ); ?>" class="avatar" />
	</a>
<?php } ?>