<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

echo FSJ_Page::Popup_Begin('FSJ_PICK_IMAGE');
?>

<?php 
$curpath = "images/" . str_replace("\\","/",$this->base_path) . str_replace("\\","/",$this->path);
$curpath = str_replace("/","<span style='padding-left:2px;padding-right:2px;'>/</span>", $curpath); 
?>

<div class="fsj_imgpick_cdir" style="font-size:115%;"><?php echo JText::_('FSJ_PICK_IMAGE_CUR_DIR'); ?> <span><?php echo $curpath; ?></span></div>

<ul class="thumbnails fsj_imgpick_thumbs">

<?php foreach ($this->files as $file): ?>
	
	<li class="span2">
		<div class="thumbnail">
		<?php if ($file == "") : ?>
			<a class="img_pick_link" href="#" id="ipl_">
				<div style="min-height:64px;min-width:64px;">
					<img src="<?php echo JURI::root() . 'libraries/fsj_core/assets/images/misc/pick_image/no_image-64.png'; ?>" width="64" height="64">
				</div>
				<div><?php echo JText::_('FSJ_PICK_IMAGE_NONE'); ?></div>
			</a>
		<?php elseif ($file == ".."): ?>

		<?php			
			$parent = substr($this->path,0,strlen($this->path)-1);
			if (strpos(" ".$parent,"/") > 0)
			{
				$parent = substr($parent,0,strrpos($parent,"/"));	
			} else {
				$parent = "";	
			}
		?>

			<a href="<?php echo JRoute::_($this->baselink."&type={$this->type}&offset=0&path={$parent}"); ?>" id="ipl_<?php echo $file; ?>">
				<div style="min-height:64px;min-width:64px;">
					<img src="<?php echo JURI::root() . 'libraries/fsj_core/assets/images/misc/pick_image/folder_up-64.png'; ?>" width="64" height="64">
				</div>
				<div><?php echo JText::_('FSJ_PICK_IMAGE_PARENT_DIR'); ?></div>
			</a>

		<?php elseif (is_dir($this->fullpath . DS . $file)): ?>
			<?php  
				if ($this->path)
				{
					$newpath = $this->path . DS . $file;
				} else {
					$newpath = $file;
				}
			?>
			<a href="<?php echo JRoute::_($this->baselink."&type={$this->type}&offset=0&path=".$newpath); ?>" id="ipl_<?php echo $file; ?>">
				<div class="fsjTip" title="<?php echo $file; ?>">
					<div style="min-height:64px;min-width:64px;">
						<img src="<?php echo JURI::root() . 'libraries/fsj_core/assets/images/misc/pick_image/folder-64.png'; ?>" width="64" height="64">
					</div>
					<div><?php echo $file; ?></div>
				</div>
			</a>

		<?php else:
			$dest_height = $this->dest_height;
			$dest_width = $this->dest_width;
			if (file_exists($this->fullpath . DS . $file))
			{
				$size = getimagesize($this->fullpath . DS . $file);
				$width = $size[0];
				$height = $size[1];
			} else {
				$width = 64;
				$height = 64;	
			}

			if ($width > $height)
			{
				$dest_height = floor($dest_height / ($width/$height));
			} else {
				$dest_width = floor($dest_width * ($width/$height));
			}
		
			$padtop = 0;
			if ($dest_height > $height && $dest_width > $width)
			{
				$padtop = floor(($dest_height - $height)/2);
				$dest_height = $height;	
				$dest_width = $width;
			} else {
				$padtop = floor((64 - $dest_height)/2);
			}
			
			$imageurl = JURI::root() . 'images/' . str_replace(DS,"/",$this->base_path) . $this->path . "/" . $file;
			$imageid = $file;
			if ($this->path)
				$imageid = $this->path . "/" . $imageid;
			if ($this->base_path)
				$imageid = $this->base_path . "/" . $imageid;
			
			$imageid = str_replace("\\","/", $imageid);
			$imageid = str_replace("//","/", $imageid);
			$imageid = trim($imageid, "/");
		?>

			<a href="#" class="img_pick_link" id="ipl_<?php echo $imageid; ?>">
				<div class="fsjTip" title="<?php echo $file; ?>">
					<div style="min-height:64px;min-width:64px;">
						<img style='padding-top:<?php echo $padtop; ?>px;' src="<?php echo $imageurl; ?>" width="<?php echo $dest_width; ?>" height="<?php echo $dest_height; ?>">
					</div>
					<div><?php echo $file; ?></div>
				</div>
			</a>
		
		<?php endif; ?>
		</div>
	</li>

<?php endforeach; ?>

</ul>

	<?php if (count($this->files) < 2): ?>

		<div class="fsj_imgpick_none">
			No image files found in this folder.<br><br>
			You can view the stock images by using the "Stock Images" tab at the top of the page<br>
		</div>
		
<?php endif; ?>

	<div class="pagination">
		<ul>
			<?php if ($this->currentpage > 1): ?>
				<li><a href="<?php echo JRoute::_($this->link."&offset=" . ($this->currentpage-2)*$this->perpage); ?>">&laquo;</a></li>
			<?php else: ?>
				<li class="disabled"><a href="#">&laquo;</a></li>
			<?php endif; ?>

			<?php for ($i = 1; $i <= $this->totalpages; $i++): ?>
				<?php if ($i == $this->currentpage): ?>
					<li class="active"><span><?php echo $i ?></span></li>
				<?php else : ?>
					<li>
						<a href="<?php echo JRoute::_($this->link."&offset=" . ($i-1)*$this->perpage); ?>" title="<?php echo $i ?>">
							<?php echo $i ?>
						</a>
					</li>
				<?php endif; ?>
			<?php endfor; ?>
			
			<?php if ($this->currentpage < $this->totalpages): ?>
				<li><a href="<?php echo JRoute::_($this->link."&offset=" . ($this->totalpages-1)*$this->perpage); ?>">&raquo;</a></li>
			<?php else: ?>
				<li class="disabled"><a href="#">&raquo;</a></li>
			<?php endif; ?>
		</ul>
	</div>	


<script>
jQuery(document).ready( function () {
	jQuery('#specific').click (function (ev) {
		var url = '<?php echo JRoute::_($this->baselink . '&type=specific&offset=0', false); ?>';
		window.location.href = url;
	});
	jQuery('#stock').click (function (ev) {
		var url = '<?php echo JRoute::_($this->baselink . '&type=stock&offset=0', false); ?>';
		window.location.href = url;
	});
	jQuery('#site').click (function (ev) {
		var url = '<?php echo JRoute::_($this->baselink . '&type=site&offset=0', false); ?>';
		window.location.href = url;
	});
	jQuery('.img_pick_link').click( function (ev) {
		ev.preventDefault();
		var image = jQuery(this).attr('id').substr(4);
		window.parent.ImgPickChoose(image);
	});
});
</script>
<?php echo FSJ_Page::Popup_End(); ?>