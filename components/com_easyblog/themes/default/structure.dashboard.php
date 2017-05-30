<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="eblog-wrapper" class="eblog-<?php echo $themeName;?> eblog-<?php echo $headingFont . $suffix . $bootstrap;?>">

	<?php echo $jsToolbar; ?>

	<?php echo $toolbar; ?>

	<?php echo $messages; ?>

	<?php echo $contents; ?>
</div>