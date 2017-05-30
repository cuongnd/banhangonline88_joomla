<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="config-document">
	<div id="page-main" class="tab">
	    <div>
			<?php echo $this->loadTemplate('main');?>
		</div>
	</div>
	<div id="page-advanceTheme" class="tab">
		<div>
			<?php echo $this->loadTemplate('advance_theme');?>
		</div>
	</div>
</div>
<div class="clr"></div>
