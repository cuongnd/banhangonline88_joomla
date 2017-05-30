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
?>
<table class="reset-table">
	<tr>
		<td>		
	  		<div style="padding:5px 11px 5px 5px">
                <textarea type="text" name="trackback" id="trackback" class="inputbox write-trackback width-full" rows="4"><?php echo (isset($this->blog->unsaveTrackbacks)) ? $this->blog->unsaveTrackbacks : ''; ?></textarea>
                <div class="small"><?php echo JText::_('COM_EASYBLOG_BLOGS_TRACKBACK_INSTRUCTIONS'); ?></div>
            </div>
		</td>
	</tr>
	<?php if ( !empty($this->trackbacks) ) { ?>
	<tr>
		<td>
			<span style="font-weight: 700;"><?php echo JText::_('COM_EASYBLOG_BLOGS_TRACKBACK_SENT'); ?> :</span>
			<div id="trackback-list-container">
				<ul id="trackback-list">
					<?php foreach($this->trackbacks as $trackback) { ?>
					<li class="trackback-list-item">
						<span class="trackback-caption"><?php echo $trackback->url;?></span>
					</li>
					<?php } ?>
				</ul>
			</div>								
		</td>
	</tr>
	<?php } ?>
</table>