<?php
/**	
 * SocialBacklinks Plugins view Content Items layout
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
JHtml::_( 'behavior.framework', true );
//jimport( 'joomla.html.pane' );
jimport( 'joomla.html.html.tabs' );

$doc = JFactory::getDocument( );
$doc->addStylesheet( JURI::root( true ) . '/media/com_socialbacklinks/css/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.css' ) );
$doc->addStyleDeclaration( "
	.overlay-text
		{
		background-color: #fff;
		position: absolute;
		display: none;
		width: 500px;
		height: 50px;
		text-align: center;
		top: 50%;
		left: 50%;
		margin: -25px 0 0 -250px;
		opacity: 1;
		filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
		z-index: 9999;
		font-size: 18px;
		font-weight: bold;
		color: #1c1c1c;
		}
	.save-wrapper
		{
		height: 25px;
		}
" );
$doc->addScript( JURI::root( true ) . '/media/com_socialbacklinks/js/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.js' ) );
if ( isset( $this->plugin_js_file ) )
{
	$doc->addScript( JURI::root( true ) . $this->plugin_js_file );
}
$doc->addScriptDeclaration( "
document.addEvent('domready', function() {
	var select = new SB.Selectbox({
		component: '{$this->alias}',
		messages : {
			'no_unique_items' : '" . JText::_( 'SB_NO_UNIQUE_ITEMS', true ) . "'
		}
	});
});

document.addEvent('domready', function()
{
	var content = new SB.Content(
	{
		'wrapper': '.block-wrapper',
		'single_block': '.block-wrapper',
		'plugin': '{$this->alias}',
		'delete_msg': '" . JText::_( 'SB_DELETE_ARTICLE', true ) . "',
		'ajax_error_msg': '" . JText::_( 'SB_OTHER_ERROR', true ) . "',
		'ajax_success_msg': '" . JText::_( 'SB_SAVED', true ) . "',
		'empty_table_msg': '" . JText::_( 'SB_NO_ITEMS', true ) . "',
		
		'status_msg': {
			0: '" . JText::_( 'SB_ALL_ARTICLES_SELECTED', true ) . "',
			1: '" . JText::_( 'SB_SOME_ARTICLES_SELECTED', true )
		. "'
		}
	});
})
" );
?>
<form action="index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object=id" method="post" 
		name="adminForm" class="popup block-wrapper">
	
	<div class="header-wrapper">
		<div class="header-text"><?php echo JText::_( 'SB_SELECT_CONTENT_TO_SYNC' ) ?></div>
		<div class="options-wrapper">
			<div class="save-button button">
				<span><?php echo JText::_( 'SB_SAVE' ) ?></span>
			</div>
			<div class="select-items">
				<label for="selected_content0" class="radiobtn selected_content">
					<input type="radio" value="0" id="selected_content0" name="selected_content" 
						<?php echo ( $this->selected_content == 0 ) ? 'checked="checked"' : '' ?>
					/>
					<?php echo JText::_( 'SB_ALL_CONTENT' ); ?>
				</label>
				<label for="selected_content1" class="radiobtn selected_content">
					<input type="radio" value="1" id="selected_content1" name="selected_content" 
						<?php echo ( $this->selected_content == 1 ) ? 'checked="checked"' : '' ?>
					/>
					<?php echo JText::_( 'SB_SELECTED_CONTENT' ); ?>
				</label>
			</div>
		</div>
	</div>
<?php
if ( $this->selected_content ) {
	$content_style = ' style="display: block; opacity: 1;"';
	$overlay_style = ' style="display: none; opacity: 0;"';
}
else {
	$content_style = ' style="display: none; opacity: 0;"';
	$overlay_style = ' style="display: block; opacity: 1;"';
}
?>

<div id="content-body"<?php echo $content_style ?>>
	<?php
	$options = array(
		'onActive' => 'function(title, description){
			description.setStyle("display", "block");
			title.addClass("open").removeClass("closed");
		}',
		'onBackground' => 'function(title, description){
			description.setStyle("display", "none");
			title.addClass("closed").removeClass("open");
		}',
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
	);

	//$pane = JPane::getInstance( 'Tabs' );
	//echo $pane->startPane( 'content' );
	echo JHtml::_('tabs.start', 'Tabs', $options);
	
	//echo $pane->startPanel( JText::_( 'SB_CATEGORIES' ), 'categories' );
	echo JHtml::_('tabs.panel', JText::_( 'SB_CATEGORIES' ), 'categories');
	?>
	<div id="categories-body">
		<?php if ( isset( $this->plugin_categories_template ) ) echo $this->plugin_categories_template;
		else echo $this->loadTemplate( 'categories' ) ?>
	</div>
	<?php
	//echo $pane->endPanel( );
	//echo $pane->startPanel( JText::_( 'SB_SINGLE_ARTICLES' ), 'articles' );
	echo JHtml::_('tabs.panel', JText::_('SB_SINGLE_ARTICLES'), 'articles');
	
	// Initialize variables
	// TODO : Test it for necessaries
	$app = JFactory::getApplication( );
	$template = $app->getTemplate( );
	$doc->addStyleSheet( "templates/{$template}/css/general.css" );
	?>
	<div id="articles-body">
		<?php echo $this->loadTemplate( 'articles' ) ?>
	</div>
	<?php
	//echo $pane->endPanel( );
	//echo $pane->endPanel( );
	echo JHtml::_('tabs.end');
	?>
</div>

<div class="overlay-text"<?php echo $overlay_style ?>>
	<?php echo JText::_( 'SB_ALL_CONTENT_SYNCED' ) ?>
</div>

<div class="ajax-overlay"></div>
<div class="ajax-loader"></div>
</form>
