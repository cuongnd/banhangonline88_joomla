<?php
/**    
 * SocialBacklinks Plugins view Network Info layout
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( );

$doc = JFactory::getDocument();
$doc->addStyleDeclaration("
	html, body
		{
		height: auto !important;
		}
	.contentpane
		{
		margin: 0;
		padding: 0;
		}
");

$src = JURI::root( true ) . '/media/com_socialbacklinks/images/' . $this->content . '-example.png';
?>
<img src="<?php echo $src ?>" title="<?php echo $this->content ?>-example" alt="<?php echo $this->content ?>-example" />
