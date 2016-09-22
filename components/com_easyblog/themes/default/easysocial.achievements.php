<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-badges">
	<h3><?php echo JText::_( 'COM_EASYBLOG_ACHIEVEMENTS' ); ?></h3>
	<?php if( $badges ){ ?>
	<ul class="reset-ul">
		<?php foreach( $badges as $badge ){ ?>
		<li>
			<a href="<?php echo FRoute::badges( array( 'id' => $badge->id , 'layout' => 'item' ) );?>"><img src="<?php echo $badge->getAvatar();?>" width="32" /></a>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
</div>