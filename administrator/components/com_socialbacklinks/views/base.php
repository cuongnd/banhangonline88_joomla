<?php
/**    
 * SocialBacklinks Base abstract view
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

jimport( 'joomla.application.component.view' );

/**
 * SocialBacklinks Base abstract view
 */
abstract class SBViewsBase extends JViewLegacy
{
	/**
	 * @see JView::getName()
	 */
	public function getName()
	{
		$name = null;
		if (isset($this->name)) {
			$name = $this->name;
		}

		if (empty( $name ))
		{
			$r = null;
			if (!preg_match('/Views((view)*(.*(view)?.*))$/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'));
			}
			if (strpos($r[3], "view"))
			{
				JError::raiseWarning('SOME_ERROR_CODE', JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME_SUBSTRING'));
			}
			$name = strtolower( $r[3] );
		}

		return $name;
	}
	
	/**
	 * @see JView::display()
	 */
	public function display( $tpl = null )
	{
		$model = $this->getModel();
		if ( $model ) {
			$this->assign( 'option', $model->get( 'option' ) );
		}
		
		$func = '_' . $this->getLayout( );
		if ( method_exists( $this, $func ) ) {
			$this->$func( );
		}
		parent::display( $tpl );
	}
	
	/**
	 * Returns the footer of component
	 * @return string
	 */
	public function getFooter( )
	{
		$modules_view = new SBViewsModules( );
		$modules_view->setLayout( 'footer' );
		
		$model = $this->getModel();
		$modules_view->setModel( $model, true );
		
		ob_start( );
		$modules_view->display( );
		$html = ob_get_contents( );
		ob_end_clean( );
		
		return $html;
	}
	
	/**
	 * Returns the header of component
	 * 
	 * @param 	boolean $with_sync 	show sync button or not 
	 * @return 	string
	 */
	public function getHeader( $with_sync = true )
	{
		$modules_view = new SBViewsModules( );
		$modules_view->assign( 'with_sync', $with_sync );
		$modules_view->setLayout( 'header' );
		
		$model = $this->getModel();
		$modules_view->setModel( $model, true );
		
		ob_start( );
		$modules_view->display( );
		$html = ob_get_contents( );
		ob_end_clean( );
		
		return $html;
	}
	
}
