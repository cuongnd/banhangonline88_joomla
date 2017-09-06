<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

if (!defined( '_ARK_QUICKICON_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_ARK_QUICKICON_MODULE', 1 );

	require_once( JPATH_COMPONENT .DS. 'helper.php' );

	function quickiconButton( $link, $icon, $text, $id, $modalclass='' )
	{
		$app 		= JFactory::getApplication();
		$lang		= JFactory::getLanguage();
		$template	= $app->getTemplate();        

		// RENDER BTN
		// the id is for auto firing of the buttons
		?>
		<a id="arktaskbtn_<?php echo $id; ?>" href="<?php echo $link; ?>"<?php echo $modalref;?>>
			<span class="icon-<?php echo $icon; ?>"></span>
			<div><?php echo $text; ?></div>
		</a>
		<?php
	}

	echo '<div id="arkcpanel">';
	
	$base = 'index.php?option=com_arkeditor';
	$view = '&amp;view=';
	$task = '&amp;task=';
	$canDo = ARKHelper::getActions();
	$isMobile = ARKHelper::isMobile();
	$isIOS =   (ARKHelper::isMobile() || ARKHelper::isiPad());
	$user = JFactory::getUser();

	quickiconButton( $base . $view . 'list', 'puzzle', JText::_( 'COM_ARKEDITOR_QUICKICON_PLUGIN_NAME' ), 'list' );

	if( $user->authorise('core.manage', 'com_installer') )
	{
		quickiconButton( 'index.php?option=com_installer', 'cube', JText::_( 'COM_ARKEDITOR_QUICKICON_INSTALL_NAME' ), 'install' );
	}

	if( $user->authorise('core.manage', 'com_installer') && $user->authorise('core.delete', 'com_installer'))
	{
		quickiconButton( 'index.php?option=com_installer&amp;view=manage&amp;filter_group=arkeditor', 'trash', JText::_( 'COM_ARKEDITOR_QUICKICON_UNINSTALL_NAME' ), 'plugin' );
	}
	
	if(!$isIOS)
		quickiconButton( $base . $view . 'toolbars', 'menu-3', JText::_( 'COM_ARKEDITOR_QUICKICON_LAYOUT_NAME' ), 'toolbars' );

	$db = JFactory::getDBO();
	$db->setQuery('SELECT extension_id  FROM #__extensions WHERE type = "plugin" AND folder= "editors" AND element = "arkeditor"');
	$result = $db->loadresult();

	if($result)
	{
		$link = 'index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id='.$result;
		quickiconButton( $link, 'power-cord', JText::_( 'COM_ARKEDITOR_QUICKICON_ARKEDITOR_NAME' ), 'editor' );
	}

	quickiconButton( $base . $view . 'list', 'cog', JText::_( 'COM_ARKEDITOR_QUICKICON_OPTIONS_NAME' ), 'options' );
	quickiconButton( $base . $view . 'list', 'help', JText::_( 'COM_ARKEDITOR_QUICKICON_HELP_NAME' ), 'help' );
	quickiconButton( $base . $view . 'list', 'warning', JText::_( 'COM_ARKEDITOR_QUICKICON_BUG_NAME' ), 'bug' );

	echo '</div>';
}