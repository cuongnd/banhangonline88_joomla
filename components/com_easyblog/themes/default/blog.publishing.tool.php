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
<?php if( $printEnabled ) : ?>
<li class="print">
	<a rel="nofollow" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
	   title="<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT'); ?>"
	   href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&tmpl=component&print=1&id=' . $blogId); ?>">
		<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT'); ?>
	</a>
</li>
<?php endif; ?>

<?php if( (EasyBlogHelper::getJoomlaVersion() <= '1.5') && $pdfEnabled ){ ?>
<?php
$format	= $system->config->get( 'main_phocapdf_enable' ) ? 'phocapdf' : 'pdf';
?>
<li class="pdf">
	<a rel="nofollow"
	   <?php echo $pdfLinkProperties; ?>
	   title="<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PDF'); ?>"
	   href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&format=' . $format . '&id=' . $blogId); ?>">
		<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PDF'); ?>
	</a>
</li>
<?php } else if( EasyBlogHelper::getJoomlaVersion() >= '1.6' && $system->config->get( 'main_phocapdf_enable' ) ) { ?>
<li class="pdf">
	<a rel="nofollow"
	   <?php echo $pdfLinkProperties; ?>
	   title="<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PDF'); ?>"
	   href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&format=pdf&id=' . $blogId); ?>">
		<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PDF'); ?>
	</a>
</li>

<?php } ?>
