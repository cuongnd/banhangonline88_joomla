<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');
$userProfileEditLink    = ( $system->config->get( 'toolbar_editprofile' ) ) ? EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' ) : 'javascript:void(0);';
?>
<a href="<?php echo $userProfileEditLink;?>">
	<img class="dashboard-avatar float-l mrl" src="<?php echo $system->profile->getAvatar();?>" />
</a>
<div class="dashboard-breadcrumb">
	<div class="user-brief">
		<span class="user-name">
			<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' );?>"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_HOME' );?></a>
		</span>

		<?php if( isset( $breadcrumbs ) && $breadcrumbs ){ ?>
			<?php foreach( $breadcrumbs as $title => $link ){ ?>
			<span>
				<?php if( !empty( $link ) ){ ?>
					<a href="<?php echo $link;?>">
				<?php } ?>

				<?php echo $title ;?>

				<?php if( !empty( $link ) ){ ?>
					</a>
				<?php } ?>
			</span>
			
			<?php }?>
		<?php } ?>
	</div>
</div>
