<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php $image = $this->imageHelper->getThumbnail(@$this->params->file_path, array(100, 100), array('default' => true)); ?>
<div>
	<a href="#delete" class="deleteImg" onclick="return window.hkUploaderList['hikamarket_category_image'].delImage(this);"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" border="0"></a>
	<div class="hikamarket_image">
		<img src="<?php echo $image->url; ?>" alt="<?php echo $image->filename; ?>"/>
	</div><input type="hidden" name="data[category][category_image]" value="<?php echo @$this->params->file_id;?>"/>
</div>
