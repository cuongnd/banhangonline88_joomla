<script type="text/javascript">
	EasyBlog.require()
		.script(
			'http://static.zemanta.com/core/jquery.js',
			'http://static.zemanta.com/core/jquery.zemanta.js'
		)
		.stylesheet(
			'http://static.zemanta.com/core/zemanta-widget.css'
		)
		.done(function($){
		});
</script>

<div id="zemanta-sidebar">
	<div id="zemanta-control" class="zemanta"></div><div id="zemanta-message" class="zemanta">Loading Zemanta...</div><div id="zemanta-filter" class="zemanta"></div><div id="zemanta-gallery" class="zemanta"></div><div id="zemanta-articles" class="zemanta"></div><div id="zemanta-preferences" class="zemanta"></div>
</div>

<div id="zemanta-links">
	<ul id="zemanta-links-div-ul">
		<li class="zemanta-title"><?php echo JText::_( 'COM_EASYBLOG_ZEMANTA_LINK_RECOMMENDATIONS' );?></li>
	</ul>
	<p class="zem-clear">&nbsp;</p>
</div>
<script type="text/javascript">
window.ZemantaGetAPIKey = function () {
	return '<?php echo $system->config->get( 'layout_dashboard_zemanta_api' );?>';
}
</script>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_easyblog/assets/js/zemanta.platform.js"></script>
