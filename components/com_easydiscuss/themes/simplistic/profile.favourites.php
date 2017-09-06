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
?>
<h3><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FAVOURITES'); ?></h3>
<hr />
<?php if( $posts ){ ?>
<ul class="unstyled discuss-list clearfix">
	<?php foreach( $posts as $post ) { ?>
	<li>
		<?php echo $this->loadTemplate( 'profile.post.item.php' , array( 'post' => $post , 'favourites' => true ) ); ?>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div class="empty">
	<?php echo JText::_( 'COM_EASYDISCUSS_NO_POST_YET' );?>
</div>
<?php } ?>
