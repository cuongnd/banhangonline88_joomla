<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$image_path=$this->params->file_path;
$image_path=JUri::root().$this->imageHelper->getPath($image_path);
$tag_image_id='image-item-'.$this->params->file_id;
$type = (!empty($this->params->product_type) && $this->params->product_type == 'variant') ? 'variant' : 'product';
?>

<a href="#delete" class="deleteImg" onclick="return window.productMgr.delImage(this, '<?php echo $type; ?>');"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" border="0"></a>
<a href="javascript:void(0)" class="editImg" onclick="window.productMgr.editImageOnline('<?php echo $tag_image_id ?>','<?php echo $image_path ?>');"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/edit.png" border="0"></a>
<div class="hikamarket_image">
<?php
	$size_x = (int)$this->config->get('product_edition_image_x', 100);
	if($size_x <= 20) $size_x = 100;
	$size_y = (int)$this->config->get('product_edition_image_y', 100);
	if($size_y <= 20) $size_y = 100;
	if(empty($this->params->file_id))
		$this->params->file_id = 0;
	$image = $this->imageHelper->getThumbnail(@$this->params->file_path, array($size_x, $size_y), array('default' => true));
	if(!empty($image) && $image->success) {
		$content = '<input type="hidden" size_x="'.$size_x.'" size_y="'.$size_y.'"  name="'.$tag_image_id.'" value="'.$this->params->file_id.'" class="image-item" src="'.$image_path.'" id="'.$tag_image_id.'"><img id="thumbnail_image-'.$this->params->file_id.'" src="'.$image->url.'" alt="'.$image->filename.'" />';
	} else {
		$content = '<input type="hidden" size_x="'.$size_x.'" size_y="'.$size_y.'"  name="'.$tag_image_id.'" value="'.$this->params->file_id.'" class="image-item" src="'.$image_path.'" id="'.$tag_image_id.'"><img id="thumbnail-'.$this->params->file_id.'" src="" alt="'.@$this->params->file_name.'" />';
	}
	echo $this->popup->display(
		$content,
		'MARKET_IMAGE',
		hikamarket::completeLink('product&task=image&cid='.@$this->params->file_id.'&pid='.@$this->params->product_id,true),
		'',
		750, 460, 'onclick="return window.productMgr.editImage(this, '.$this->params->file_id.', \''.$type.'\');"', '', 'link'
	);
?>
</div><input type="hidden" name="data[<?php echo $type; ?>][product_images][]" value="<?php echo @$this->params->file_id;?>"/>
