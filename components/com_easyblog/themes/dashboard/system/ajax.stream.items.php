<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if( count( $activities ) > 0 ) { ?>
	<div style="margin: 4px 0; border-top: 1px dotted #EAEAEA;"></div>
	<?php
	foreach( $activities as $activity)
	{
		echo $activity;
	}
	?>
<?php } ?>