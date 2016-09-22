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
<div id="ezblog-body">
	<div id="ezblog-label" class="latest-post clearfix">
		<span><?php echo $archiveTitle; ?></span>
		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=archive' );?>" class="float-r"><?php echo JText::_( 'COM_EASYBLOG_SWITCH_TO_LIST_VIEW' ); ?></a>
	</div>
	<div class="archive-list mtm">
		<?php if($data){ ?>
		<ul class="reset-ul">
			<?php foreach( $data as $row ){ ?>
				<?php echo $this->fetch( 'blog.item'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php' , array( 'row' => $row ) ); ?>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="eblog-message info mtm"><?php echo $emptyPostMsg;?></div>
		<?php } ?>
	</div>

	<?php if( $pagination ){ ?>
	<div class="pagination clearfix">
		<?php echo $pagination; ?>
	</div>
	<?php } ?>
</div>
