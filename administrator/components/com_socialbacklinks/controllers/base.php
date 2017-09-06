<?php
/**
 * SocialBacklinks Back-End
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

jimport( 'joomla.application.component.controller' );

/**
 * SocialBacklinks Base abstract controller
 */
abstract class SBControllersBase extends JControllerLegacy
{
	/**
	 * The list of models
	 * @var	array
	 */
	protected $_models = array();

	/**
	 * Request object
	 * @var SBControllersLibsRequest
	 */
	protected $_request;

	/**
	 * Constructor
	 * @see JControllerLegacy::__construct()
	 */
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
		
		// To neutralize a notice on the dashboard
		$app = &JFactory::getApplication();
		if (!isset($app->JComponentTitle)) {
			$app->JComponentTitle = "";
		}
		
		$this->_request = new SBControllersLibsRequest();
	}

	/**
	 * @see JControllerLegacy::getName()
	 */
	public function getName()
	{
		$name = $this->name;
		if ( empty( $name ) ) {
			$r = null;
			if ( !preg_match( '/Controllers(.*)/i', get_class( $this ), $r ) ) {
				JError::raiseError( 500, "JControllerLegacy::getName() : Cannot get or parse class name." );
			}
			$name = strtolower( $r[1] );
		}

		return $name;
	}

	/**
	 * Returns the request data
	 * @return ArrayObject
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Adds to request list of values
	 * @param  array
	 * @return void
	 */
	public function setRequest( $request )
	{
		$this->_request->set( $request );
	}

	/**
	 * @see JControllerLegacy::getModel()
	 */
	public function getModel( $name = '', $prefix = 'SBModels', $config = array() )
	{
		if ( empty( $name ) ) {
			$name = $this->getName();
		}

		$name = strtolower( $name );

		if ( empty( $this->_models[$name] ) ) {
			$this->_models[$name] = parent::getModel( $name, $prefix, $config );
		}

		return $this->_models[$name];
	}

	/**
	 * @see JControllerLegacy::getView()
	 */
	public function getView( $name = '', $type = '', $prefix = 'SBViews', $config = array() )
	{
		$view = parent::getView( $name, $type, $prefix, $config );
		if ( key_exists( 'layout', $config) && ($config['layout'] != $view->getLayout()) ) {
			$view->setLayout( $config['layout'] );
		}
		return $view;
	}

	/**
	 * @see JControllerLegacy::createView()
	 */
	protected function createView( $name, $prefix = '', $type = '', $config = array() )
	{
		$result = null;
		// Clean the view name
		$viewName = preg_replace( '/[^A-Z0-9_]/i', '', $name );
		$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );
		$viewType = preg_replace( '/[^A-Z0-9_]/i', '', $type );

		// Build the view class name
		$viewClass = $classPrefix . ucfirst( $viewName );
		if ( !class_exists( $viewClass ) ) {
		    JError::raiseError( 500, JText::sprintf( 'JLIB_APPLICATION_ERROR_VIEW_CLASS_NOT_FOUND', $viewClass, $path ) );
            return $result;
		}

		$result = new $viewClass( $config );
		return $result;
	}

	/**
	 * @see JControllerLegacy::display()
	 */
	public function display( $cachable = false, $urlparams = false )
	{
		$document = JFactory::getDocument();

		$viewType = $document->getType();
		$viewName = $this->_request->get( 'view', $this->getName() );
		$viewLayout = $this->_request->get( 'layout', 'default' );

		$view = $this->getView( $viewName, $viewType, 'SBViews', array( 'base_path' => $this->basePath, 'layout' => $viewLayout ) );

		// Get/Create the model
		if ( $model = $this->getModel( $viewName ) ) {
			// Push the model into the view (as default)
			$view->setModel( $model, true );
		}
        
        $view->assignRef( 'document', $document );

		$conf = JFactory::getConfig();

        // Display the view
        if ( $cachable && ($viewType != 'feed') && ($conf->get('caching') >= 1) )
        {
            $option = $this->_request->option;
            $cache = JFactory::getCache( $option, 'view' );

            if ( is_array($urlparams) ) {
                $app = JFactory::getApplication();

                $registeredurlparams = $app->get( 'registeredurlparams' );

                if ( empty($registeredurlparams) ) {
                    $registeredurlparams = new stdClass;
                }

                foreach ( $urlparams as $key => $value ) {
                    // Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
                    $registeredurlparams->$key = $value;
                }

                $app->set( 'registeredurlparams', $registeredurlparams );
            }

            $cache->get( $view, 'display' );
        }
        else {
            $view->display();
        }

        return $this;
	}

}
