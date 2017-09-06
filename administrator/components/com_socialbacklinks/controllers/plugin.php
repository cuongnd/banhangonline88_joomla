<?php
/**
 * SocialBacklinks Plugin controller
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
 * SocialBacklinks Plugin controller class, which manage different plugins
 */
class SBControllersPlugin extends SBControllersBase
{
	/**
	 * Saves new settings data of plugin
	 * @return void
	 */
	public function save( )
	{
		if ( !($plugin = $this->_request->get( 'plugin' )) || !($plugin = SBPlugin::get( $plugin )) ) {
			return;
		}
		$plugin_type = $plugin->getType( );

		if ( $this->_request->name ) {
			$data = array( $this->_request->name => $this->_request->value );
		}
		elseif ( ($cid = $this->_request->get( 'post.cid', array( ), 'array' )) && count( $cid ) ) {
			$data = $cid;
		}
		else {
			$data = $this->_request->get( 'post.value', array( ), 'array' );
			// Checks whether the categories or items list should be empty
			if ( isset( $data['categories'] ) && ( $data['categories'][0] == -1 ) )
			{
				$data['categories'] = array();
			}
			if ( isset( $data['items'] ) && ( $data['items'][0] == -1 ) )
			{
				$data['items'] = array();
			}
		}

		// Store new configuration values
		$params = array(
			'section' => $plugin_type,
			'name' => $plugin->getAlias( ),
			'value' => $data
		);
		$success = $this->getModel( 'Plugins' )->setData( $params )->ignore( 'task' )->update( );

		// Return response
		if ( $success ) {
			$response = array( 'error' => false );
		}
		else {
			$response = array(
				'error' => true,
				'msg' => JText::_( "SB_OTHER_ERROR" )
			);
		}
		echo json_encode( $response );
		JFactory::getApplication( )->close( );
	}

	/**
	 * Renders all settings of selected type
	 */
	public function renderSettings( )
	{
		$plugin_type = $this->_request->get( 'get.plugintype', 'content' );
		$this->_request->set( array(
			'view' => 'plugins',
			'layout' => $plugin_type . '_settings'
		) );
		parent::display( );
	}

	/**
	 * Shows form of select items
	 * @return void
	 */
	public function selectItems( )
	{
		$doc = JFactory::getDocument( );
		$view = $this->getView( 'plugins', $doc->getType( ) );
		
		$model = $this->getModel( 'items' );
		$view->setModel( $model, true );

		$content_plugin = $this->_request->get( 'get.content', 'content' );
		$plugin = SBPlugin::get( 'content.' . $content_plugin );

		$view->setLayout( 'content_items' );
		$view->assign( 'plugin', $plugin );
		$view->display( );
	}

	/**
	 * Returns the list of items in specific category
	 * @return void
	 */
	public function getItems( )
	{
		$plugin = $this->_request->plugin;
		$plugin = SBPlugin::get( 'content.' . $plugin );

		$category_id = $this->_request->get( 'post.catid', 0, 'int' );
		$level = $this->_request->get( 'post.level', 0, 'int' );
		$filter = $this->_request->get( 'post.filter', null );

		$items = JModelLegacy::getInstance( 'SBModelsItems' )->plugin( $plugin )->category( $category_id )->level( $level )->filter( $filter )->getListByCategory( );
		echo json_encode( $items );
		jexit( );
	}

	/**
	 * Returns items by id
	 * @return void
	 */
	public function getItemsById( )
	{
		$plugin = $this->_request->plugin;
		$plugin = SBPlugin::get( 'content.' . $plugin );
		$ids = $this->_request->get( 'post.ids', array( ), 'array' );
		$items = JModelLegacy::getInstance( 'SBModelsItems' )->plugin( $plugin )->ids($ids)->getList( );
		// Formats item creation date
		if ( !empty($items) ) {
			foreach ((array) $items as $index => $item ) {
				$items[$index]->created = JHtml::_( 'date', $item->created, JText::_('DATE_FORMAT_LC4') ); 
			}
		}
		echo json_encode( $items );
		jexit( );
	}

	/**
	 * Opens edit form with exist data to edit them
	 * @return void
	 */
	public function editSettings( )
	{
		$plugin_type = $this->_request->get( 'get.plugintype', 'network' );

		if ( !$plugin = $this->_request->$plugin_type ) {
			JError::raiseError( 404, JText::_( 'SB_SETTINGS_TYPE_NO_FOUND_ERROR' ) );
			return false;
		}

		$doc = JFactory::getDocument( );
		$view = $this->getView( 'plugins', $doc->getType( ) );
		$model = $this->getModel( 'config' );
		$view->setModel( $model, true );
		
		$view->assign( 'plugin', $this->_request->get( 'get.network', $this->_request->get( 'get.content', null ) ) );
		$view->setLayout( $plugin_type . '_settings_form' );
		$view->display( );
	}

	/**
	 * Connects to some social network
	 * @return void
	 */
	public function networkConnect( )
	{
		$params = $this->_request->getHash( 'get' );
		$doc = JFactory::getDocument( );
		$view = $this->getView( 'plugins', $doc->getType( ) );
		$model = $this->getModel( 'config' );
		$view->setModel( $model, true );
		
		$plugin = $this->_request->get( 'get.network', null );
		if ( !is_null( $plugin ) && ($plugin = SBPlugin::get( "network.$plugin" )) ) {
			$alias = $plugin->getAlias( );
			$callback = $this->_request->get( 'get.callback', false, 'bool' );
			$view->assign( 'type', $alias );
			try {
				if ( $plugin->connect( $params, $callback ) ) {
					$view->assign( 'error', 0 );
					$view->assign( 'msg', JText::sprintf( 'SB_NETWORK_CONNECT_OK', JText::_( 'SB_' . strtoupper( $alias ) ) ) );
				}
			}
			catch(SBPluginsException $e) {
				$view->assign( 'error', 1 );
				$view->assign( 'msg', $e->getMessage( ) );
			}
		}

		$view->assign( 'task', 'connect' );
		$view->setLayout( 'network_connect_status' );
		$view->display( );
	}

	/**
	 * Disconnects from some social network
	 * @return void
	 */
	public function networkDisconnect( )
	{
		$doc = JFactory::getDocument( );
		$view = $this->getView( 'plugins', $doc->getType( ) );
		$model = $this->getModel( 'config' );
		$view->setModel( $model, true );

		$plugin = $this->_request->get( 'get.network', null );
		if ( !is_null( $plugin ) && ($plugin = SBPlugin::get( "network.$plugin" )) ) {
			$alias = $plugin->getAlias( );
			$callback = $this->_request->get( 'get.callback', false, 'bool' );
			$view->assign( 'type', $alias );
			try {
				if ( $plugin->disconnect( $callback ) ) {
					$view->assign( 'error', 0 );
					$view->assign( 'msg', JText::sprintf( 'SB_NETWORK_DISCONNECT_OK', JText::_( 'SB_' . strtoupper( $alias ) ) ) );
				}
			}
			catch(SBPluginsException $e) {
				$view->assign( 'error', 1 );
				$view->assign( 'msg', $e->getMessage( ) );
			}
		}

		$view->assign( 'task', 'disconnect' );
		$view->setLayout( 'network_connect_status' );
		$view->display( );
	}

	/**
	 * Displays information about social network
	 *
	 * @return void
	 */
	public function networkInfo( )
	{
		$content = $this->_request->get( 'get.network' );

		$doc = JFactory::getDocument( );
		$view = $this->getView( 'plugins', $doc->getType( ) );
		$model = $this->getModel( 'config' );
		$view->setModel( $model, true );

		$view->assign( 'content', $content );
		$view->setLayout( 'network_info' );
		$view->display( );
	}

}
