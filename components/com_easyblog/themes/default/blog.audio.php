<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<p>
	<script type="text/javascript">
	// @task: Load jwplayer.js and initialize the video player.
	EasyBlog.require()
		.script( '<?php echo rtrim( JURI::root() , '/');?>/components/com_easyblog/assets/vendors/jwplayer/jwplayer.js' )
		.done(function($){

			jwplayer( 'audio-placeholder-<?php echo $uid;?>' ).setup({
				'width': '350px',
				'height': '24px',
				'file': '<?php echo $url;?>',
				'image': '',
				'controlbar': 'bottom',
				'autostart': <?php echo $autoplay; ?>,
				'backcolor': '#333333',
				'frontcolor': '#ffffff',
				'modes': [
					{
						type: 'html5'
					},
					{
						type: 'flash',
						src: $.rootPath + '/components/com_easyblog/assets/vendors/jwplayer/player.swf'
					},
					{
						type: 'download'
					}
				]
			});
	});
	</script>
	<div class="eblog-audio-player">
		<div id="audio-placeholder-<?php echo $uid;?>"></div>
	</div>
</p>
