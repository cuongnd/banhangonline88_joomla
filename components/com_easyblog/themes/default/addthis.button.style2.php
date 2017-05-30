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
	<a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=<?php echo $addthis_customcode; ?>" class="addthis_button_compact"><?php echo $displayText; ?></a>
	<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=<?php echo $addthis_customcode; ?>"></script>
</li>