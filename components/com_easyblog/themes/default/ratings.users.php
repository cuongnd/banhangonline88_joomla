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
<div class="rating-people">
    <ul class="ratings-people reset-ul list-full">
    <?php if( $voters ){ ?>
    	<?php foreach( $voters as $vote ){ ?>
    		<?php if( $vote->created_by != 0 ){ ?>
	        <li>
	            <div class="pas">
	            	<?php 
					$user	= EasyBlogHelper::getTable( 'Profile' );
					$user->load( $vote->created_by );
					?>
	                <img class="avatar float-l mrm" src="<?php echo $user->getAvatar();?>" width="32" />
	                <div>
						<a href="<?php echo $user->getProfileLink();?>"><?php echo $user->getName();?></a>
						<span><?php echo JText::_( 'COM_EASYBLOG_VOTED_ON_ENTRY' );?> <?php echo $this->formatDate( JText::_( 'DATE_FORMAT_LC1' ) , $vote->created );?></span>
					</div>
	            </div>
	        </li>
	        <?php } else {
	        	$guests	= $vote->times;
			} 
			?>
    	<?php } ?>
    <?php } ?>

	<?php if( $guests !== false ){ ?>
		<li>
			<div>
				<span><?php echo $this->getNouns( 'COM_EASYBLOG_GUEST_VOTES_ENTRY' , $guests , true ); ?>
			</div>
		</li>
	<?php } ?>
    </ul>
</div>