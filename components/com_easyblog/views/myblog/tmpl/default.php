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

$css = '<link rel="stylesheet" href="'.JURI::base().'components/com_easyblog/assets/css/default.css" type="text/css" />';
$mainframe->addCustomHeadTag($css);
?>

<h1>Blogger</h1>

		<ul id="blog-toolbar">
			<li>
                <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog'); ?>"><?php echo JText::_('COM_EASYBLOG_CATEGORIES'); ?></a>
                <div id="item-blog" class="submenu-container">
                    <ul class="toolbar-submenu">
                        <li><?php echo JText::_('COM_EASYBLOG_FILTER'); ?> : </li>
                        <li><a href="#"><span><?php echo JText::_('LATEST'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('HITS'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('ALPHABETICAL'); ?></span></a></li>
                    </ul>
                </div>
            </li>
			<li class="active">
                <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger'); ?>"><?php echo JText::_('COM_EASYBLOG_BLOGGERS'); ?></a>
                <div id="item-blog" class="submenu-container">
                    <ul class="toolbar-submenu">
                        <li class="current"><?php echo JText::_('COM_EASYBLOG_FILTER'); ?> : </li>
                        <li class="current"><a href="#"><span><?php echo JText::_('LATEST'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('HITS'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('ALPHABETICAL'); ?></span></a></li>
                    </ul>
                </div>                
            </li>
			<li>
                <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=latest'); ?>"><?php echo JText::_('COM_EASYBLOG_LATEST_POST'); ?></a>
                <div id="item-blog" class="submenu-container">
                    <ul class="toolbar-submenu">
                        <li><?php echo JText::_('COM_EASYBLOG_FILTER'); ?> : </li>
                        <li class="current"><a href="#"><span><?php echo JText::_('LATEST'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('HITS'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('ALPHABETICAL'); ?></span></a></li>
                    </ul>
                </div>            
            </li>
			<li>
                <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogroll'); ?>"><?php echo JText::_('BLOGROLL'); ?></a>
                <div id="item-blog" class="submenu-container">
                    <ul class="toolbar-submenu">
                        <li><?php echo JText::_('COM_EASYBLOG_FILTER'); ?> : </li>
                        <li class="current"><a href="#"><span><?php echo JText::_('LATEST'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('HITS'); ?></span></a></li>
                        <li><a href="#"><span><?php echo JText::_('ALPHABETICAL'); ?></span></a></li>
                    </ul>
                </div>                
            </li>
			<li><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard'); ?>"><?php echo JText::_('COM_EASYBLOG_DASHBOARD'); ?></a></li>
		</ul>

<div id="blog-content-container">
	<!-- Show 5 latest entries in the category -->
	<div id="section-1" style="">

		<!-- Category Block -->
		<div class="blog-list-block">
			<div class="avatar-container">
				<a href="#" class="blogger-avatar">
		        	<img src="<?php echo JURI::base(); ?>/components/com_easyblog/assets/samples/03.jpg" alt="Shayna Mahmud" />
		    	</a>
			</div>
			<div class="info-container">
				<div class="intro">
				    <h3><a href="#">Shayna Mahmud</a></h3>
			        <p class="blogger-bio"><strong>Introduction text about this blogger.</strong>
					Lorem ipsum dolor sit amet, consectetur adipiscing elit.
					In augue tellus, accumsan ut, mollis vel, tristique nec,
					lectus. In tempus, nisl eget faucibus iaculis, ipsum orci
					commodo est, sed gravida libero est at pede. Nulla tempor.</p>
			    </div>
	
				<h4>Latest blog post:</h4>
			    <ul class="blog-entry-links">
			        <li><a href="#">Blog post by Shayna Mahmud 1</a></li>
			        <li><a href="#">Blog post by Shayna Mahmud 2</a></li>
			        <li><a href="#">Blog post by Shayna Mahmud 3</a></li>
			        <li><a href="#">Blog post by Shayna Mahmud 4</a></li>
			        <li><a href="#">Blog post by Shayna Mahmud 5</a></li>
			    </ul>
			    <p class="showmore"><a href="#">Show all entries posted by Shayna Mahmud &raquo;</a></p>
			</div>
			<div class="clr"></div>
		</div>
		

	</div>

</div>