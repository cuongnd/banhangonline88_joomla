<li class="tab-button" id="write-slug-button" align="center"><a href="javascript:void(0);" onclick="eblog.editor.tab.show('write-slug');"><?php echo JText::_('COM_EASYBLOG_PERMALINK'); ?></a></li>

<!-- permalink container -->
<div id="write-slug" class="container" style="display: none;">
<div class="section-container">
	<div class="clearfix">
		<label for="slug" class="label label-title"><?php echo JText::_('COM_EASYBLOG_PERMALINK'); ?></label>
		<input type="text" name="permalink" id="permalink" value="<?php echo $blog->permalink;?>" class="input text write-title" />
		</div>
	<div>
		<small><?php echo JText::_('COM_EASYBLOG_PERMALINK_DESC'); ?></small>
	</div>
</div>
</div>
<!-- permalink container -->
	    	