<?php
/**    
 * SocialBacklinks Plugins view Network Connect Status layout
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

JHtml::_( 'behavior.framework', true );

$doc = JFactory::getDocument( );
$doc->addStylesheet( JURI::root( true ) . '/media/com_socialbacklinks/css/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.css' ) );
$doc->addStyleDeclaration( '
	.contentpane 
		{
		height: 90%;
		}
	.contentpane div 
		{
		width: 100%; 
		padding: 70px 0 0; 
		text-align: center; 
		font-size: 14px
		}
	.error-block
		{
		display: block;
		}
' );
$doc->addScript( JURI::root( true ) . '/media/com_socialbacklinks/js/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.js' ) );
$doc->addScriptDeclaration( "
document.addEvent('domready', function()
{
	var network = new SB.Network(
	{
		type: '{$this->type}',
		task: '{$this->task}',
		error: {$this->error}
	})
})
	" );
if ( $this->error )
{
	$class = 'class="error-block"';
}
else {
	$class = '';
}
?>
<div <?php echo $class ?>>
	<?php echo $this->escape( $this->msg ); ?>
</div>
