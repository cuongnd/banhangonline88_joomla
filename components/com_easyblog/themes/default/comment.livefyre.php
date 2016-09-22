<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="section-comments">
	<!-- START: Livefyre Embed -->
	<div id="livefyre-comments"></div>
	<script type="text/javascript" src="http://zor.livefyre.com/wjs/v3.0/javascripts/livefyre.js"></script>
	<script type="text/javascript">
	(function () {
		var articleId = fyre.conv.load.makeArticleId(null);
		fyre.conv.load({}, [{
			el: 'livefyre-comments',
			network: "livefyre.com",
			siteId: "<?php echo $siteId;?>",
			articleId: "<?php echo $blog->id;?>",
			signed: false,
			collectionMeta: {
				articleId: "<?php echo $blog->id;?>",
				url: fyre.conv.load.makeCollectionUrl(),
			}
		}], function() {});
	}());
	</script>
	<!-- END: Livefyre Embed -->
</div>