<div class="pas search-field" style="background:#f5f5f5;">
    <div class="pas mrl">
		<input type="text" id="search-content" class="input width-half" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>';}" onfocus="if (this.value == '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>') {this.value = '';}" value="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>" />
		<input type="button" onclick="eblog.editor.search.load();return false;" value="<?php echo JText::_('COM_EASYBLOG_SEARCH'); ?>" class="buttons mls" />
	</div>
</div>
<div class="search-results-content"></div>
