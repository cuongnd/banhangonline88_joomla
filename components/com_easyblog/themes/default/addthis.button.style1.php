<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li id="bookmark-link" class="bookmark">
	<div class="addthis_toolbox addthis_default_style ">
	<a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=<?php echo $addthis_customcode; ?>" class="addthis_button_compact"><?php echo $displayText; ?></a>
	<span class="addthis_separator">|</span>
	<a class="addthis_button_preferred_1"></a>
	<a class="addthis_button_preferred_2"></a>
	<a class="addthis_button_preferred_3"></a>
	<a class="addthis_button_preferred_4"></a>
	</div>
	<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=<?php echo $addthis_customcode; ?>"></script>
</li>