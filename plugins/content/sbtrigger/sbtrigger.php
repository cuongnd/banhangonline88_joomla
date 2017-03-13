<?php
/**
 * SocialBacklinks Synchronizer System Plugin
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.utilities.date' );

/**
 * Joomla SocialBacklinks Trigger plugin
 */
class plgContentSBTrigger extends JPlugin
{
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		JLoader::register( 'SBLoader', JPATH_ADMINISTRATOR . '/components/com_socialbacklinks/loader.php' );
		SBLoader::instantiate( );
	}

	/**
	 * Do something onAfterContentSave
	 * @return void
	 */
	public function onContentAfterSave( $context, $article, $isNew )
	{	
		// Check requirements for correct component work
		$helper = new SBHelpersRequirements( );
		if ( !$helper->check( ) ) {
			return true;
		}

		// Trigger an asynchronous sync
		SBHelpersSync::asynchronousCall();
		//JFactory::getApplication()->enqueueMessage('A SocialBacklinks sync has been triggered...');
	}

}
