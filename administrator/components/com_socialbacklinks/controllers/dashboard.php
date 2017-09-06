<?php
/**
 * SocialBacklinks dashboard controller
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * SocialBacklinks Dashboard controller class, which manage general settings
 */
class SBControllersDashboard extends SBControllersBase
{
	/**
	 * @see SBControllersBase::display()
	 */
	public function display( $cachable = false, $urlparams = false )
	{
		$doc = JFactory::getDocument( );
		$view = $this->getView( 'dashboard', $doc->getType( ) );

		$model = $this->getModel( 'config' );
		$view->setModel( $model, true );

		$view->display( );
		
		return $this;
	}

	/**
	 * Displays the error message
	 *
	 * @return void
	 */
	public function error( )
	{
		$errors = $this->_request->get( 'post.errors', array(), 'array' );
		
		$doc = JFactory::getDocument( );
		$view = $this->getView( 'dashboard', $doc->getType( ) );
		
		// TODO : check is it necessary
		$model = $this->getModel( 'config' );
		$view->setModel( $model, true );

		$view->setLayout( 'error' );
		$view->assignRef( 'errors', $errors );

		$view->display( );
	}

}
