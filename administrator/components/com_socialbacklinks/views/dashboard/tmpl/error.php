<?php
/**    
 * SocialBacklinks Dashboard view Default layout
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

JHTML::_( 'behavior.modal' );

$doc = JFactory::getDocument( );

$doc->addStylesheet( JURI::root( true ) . '/media/com_socialbacklinks/css/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.css' ) );
?>
<form class="com-main-wrapper" action="index.php" method="get" name="adminForm">
<table width="100%">
<?php echo $this->getHeader( false ); ?>
<tbody>
	<tr>
		<td valign="top" colspan="3">
			<ul class="error-list">
			<?php foreach ( $this->errors AS $error ) : ?>
				<li><?php echo $error ?></li>
			<?php endforeach; ?>
			</ul>
		</td>
	</tr>
</tbody>
<?php echo $this->getFooter( ); ?>
</table>
<input type="hidden" name="option" value="com_socialbacklinks" />
<input type="hidden" name="view" value="dashboard" />
<input type="hidden" name="task" value="" />
</form> 
