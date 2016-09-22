<div class="pf_row" filename="<?php echo $file->filename; ?>">
			
	<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $file->id; ?>" category="<?php echo $file->category; ?>">
	<a href='#' onclick='window.parent.add_file_wrap("<?php echo $file->id; ?>", "<?php echo $file->category; ?>", true);' class='filename'><?php echo $file->filename; ?></a>
				
</div>