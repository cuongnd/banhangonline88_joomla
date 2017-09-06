<?php
/**
 * SocialBacklinks Plugins view
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
 * SocialBacklinks Plugins view class
 */
class SBViewsPlugins extends SBViewsBase
{
	/**
	 * Renders the list of settings of each plugin
	 * @return void
	 */
	protected function _content_settings( )
	{
		$this->assign( 'plugins', SBPlugin::get( 'content.' ) );
	}

	/**
	 * Renders the list of select categories and single articles
	 * @return void
	 */
	protected function _content_items( )
	{
		$plugin = $this->get( 'plugin' );

		$options = $plugin->getOptions( );
		$this->assign( 'alias', $plugin->getAlias( ) );
		$this->assign( 'selected_content', $options['selected_content'] );

		$tree = null;
		try
		{
			$treec = $plugin->getTreeOfCategories( );
			$tree = @JArrayHelper::toObject( $treec );
		}
		catch(Exception $e)
		{
		}
		
		$this->assign( 'tree', $tree );
		$this->assign( 'items', JModelLegacy::getInstance( 'SBModelsItems' )->plugin( $plugin )->getList( ) );

		if ( isset( $options['fields_to_show_in_query_list'] ) ) {
			$this->assignRef( 'shown_fields', $options['fields_to_show_in_query_list'] );
		}
		else {
			$default_fields = array( array(
					'title' => 'ID',
					'field' => 'id'
				) );
			$this->assignRef( 'shown_fields', $default_fields );
		}

		$selected_categories = !empty( $options['categories'] ) ? $options['categories'] : array( );
		$this->assignRef( 'selected_categories', $selected_categories );
		if ( $options['add_plugin_content_items_js_file'] )
		{
			$this->assign( 'plugin_js_file', $this->_getPluginJSFile( $this->plugin ) );
		}
		$this->assign( 'use_plugin_category_row_tmpl', $options['use_plugin_category_row_tmpl'] );
	}

	/**
	 * Renders row of single category and all of its children
	 *
	 * @param array 	$item_params 	list of params (parent, item, selected)
	 * @param string 	$layout			layout of the category row
	 * @param boolean 	$use_plugin 	should we use plugin layout or not
	 * @param int 		$level
	 * @param int 		$k
	 * @return void
	 */
	protected function _renderCategory( array $item_params, $layout, $use_plugin = false, $level = 1, &$k = 1 )
	{
		$k = 1 - $k;

		$params = array_merge( $item_params, array(
			'level' => $level,
			'is_parent' => count( get_object_vars( $item_params['item']->_children ) ),
			'k' => $k
		) );
		$this->assignRef( 'params', $params );

		echo ($use_plugin) ? $this->_renderPluginTmpl( $this->getLayout() . '_' . $layout, $this->plugin ) : $this->loadTemplate( $layout );

		if ( !empty( $item_params['item']->_children ) ) {
			foreach (( array ) $item_params['item']->_children as $child) {
				$item_params = array( 'parent' => $params['item']->id, 'item' => $child, 'selected' => ( array ) $params['selected'] );
				$this->_renderCategory( $item_params, $layout, $use_plugin, $level + 1, $k );
			}
		}
	}

	/**
	 * Renderes plugin's template
	 * @param  string Template
	 * @param  SBPluginsAbstract
	 * @param  array Data to be assigned to template
	 * @return string
	 */
	private function _renderPluginTmpl( $tmpl, SBPluginsAbstract $plugin, $data = array() )
	{
		$alias = $plugin->getAlias( );
		$path = JPATH_ROOT . "/plugins/socialbacklinks/{$alias}/{$alias}/tmpl_{$tmpl}.php";
		if ( $filename = realpath( $path ) ) {
			ob_start( );
			extract( $data, EXTR_OVERWRITE );
			include $filename;
			return ob_get_clean( );
		}
		else {
			JError::raiseError( 500, JText::_( 'SB_CONTENT_WRONG_TEMPLATE' ) );
		}
	}
	
	/**
	 * Returns the link to JS file for Content Items layout
	 * @param SBPluginsAbstract $plugin
	 * @return string
	 */
	private function _getPluginJSFile( SBPluginsAbstract $plugin )
	{
		$alias = $plugin->getAlias();
		$layout = $this->getLayout();
		return "/plugins/socialbacklinks/{$alias}/{$layout}.js";
	}
	
	/**
	 * Renders the list of settings of each network
	 * @return void
	 */
	protected function _network_settings( )
	{
		$this->assign( 'networks', SBPlugin::get( 'network.' ) );
	}

	/**
	 * Renders network setting
	 * @return void
	 */
	protected function _network_settings_form( )
	{
		$plugin = $this->plugin;

		if ( !empty( $plugin ) && ($plugin = SBPlugin::get( 'network.' . $plugin )) ) {
			$alias = $plugin->getAlias( );
			$tmp = $this->_renderPluginTmpl( 'settings', $plugin );
			$this->assignRef( 'plugin_tmpl', $tmp );
		}
	}

}
