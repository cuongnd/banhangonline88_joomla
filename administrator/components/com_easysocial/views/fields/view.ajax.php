<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include the main views class
FD::import( 'admin:/views/views' );

class EasySocialViewFields extends EasySocialAdminView
{
	/**
	 * Retrieve a list of fields.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getFields( $fields = array() )
	{
		$ajax 	= FD::getInstance( 'Ajax' );

		$ajax->resolve( $fields );
	}

	/**
	 * Renders a sample data for a custom fields
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function renderSample( $field )
	{
		$ajax 	= FD::ajax();

		if( $field === false )
		{
			return $ajax->reject( $this->getMessages() );
		}

		$app = $field->getApp();

		$theme	= FD::themes();

		$theme->set( 'appid', $field->app_id );
		$theme->set( 'output', $field->output );
		$theme->set( 'app', $app );

		$html = $theme->output( 'admin/profiles/form.fields.editor.item' );

		return $ajax->resolve( $html );
	}

	/**
	 * Render's field params
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function renderConfiguration( $manifest, $params, $html )
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $manifest, $params->toObject(), $html );
	}

	public function update()
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select( '#__social_fields', 'a' )
			->column( 'a.id' )
			->column( 'a.app_id' )
			->column( 'b.element' )
			->leftjoin( '#__social_apps', 'b' )
			->on( 'a.app_id', 'b.id' );

		$db->setQuery( $sql );

		$result = $db->loadObjectList();

		$elements = array();

		foreach( $result as $row )
		{
			$table = FD::table( 'field' );
			$table->load( $row->id );

			$table->unique_key = strtoupper( $row->element ) . '-' . $row->id;
			$table->store();
		}

		FD::ajax()->resolve();
	}

}
