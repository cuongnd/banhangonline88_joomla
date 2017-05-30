<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HTML_autoClose
{
	static function ShowAutoClose($message)
	{
		?>
		<div class="detailmsg">
			<h1><?php echo $message; ?></h1>
		</div><?php
	}
}

