<?php
/**
 * SocialBacklinks Config controller
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
 * SocialBacklinks Config controller class, which manage different configuration
 */
class SBControllersConfig extends SBControllersBase
{
	/**
	 * Renders the block of settings
	 */
	public function renderSettings( )
	{
		$this->_request->set( array( 'layout' => 'block' ) );

		parent::display( );
	}

	/**
	 * Saves new configuration data
	 * @return void
	 */
	public function save( )
	{
		$cid = JRequest::getVar( 'cid', array( ), 'POST', 'array' );
		$section = JRequest::getCmd( 'section', 'basic' );

		// Check input values
		$data = array( );
		foreach ($cid as $name => $value) {
			$data[$name] = array(
				'id' => 0,
				'section' => $section,
				'name' => $name,
				'value' => $value
			);
		}
		if ( !$this->_validate( $data ) ) {
			$response = array(
				'error' => true,
				'msg' => $this->getError( )
			);
			echo json_encode( $response );
			JFactory::getApplication( )->close( );
			return true;
		}

		// Check plugin value
		if ( isset( $cid['sbsynchronizer'] ) ) {
			SBHelpersRequirements::changePluginStatus( $cid['sbsynchronizer'], 'sbsynchronizer' );
			unset( $cid['sbsynchronizer'] );
		}
		
		// Store new configuration values
		$success = true;
		$params = array(
			'section' => $section,
			'name' => '',
			'value' => ''
		);

		foreach ($cid as $name => $value) {
			$params['name'] = $name;
			$params['value'] = $value;
			$success = $this->getModel( 'Config' )->setData( $params )->update( );
			if ( !$success ) {
				break;
			}
		}

		// Return response
		if ( $success ) {
			// Build status message
			$cid = JRequest::getVar( 'cid', array( ), 'POST', 'array' );
			$response = array(
				'error' => false,
				'msg' => SBHelpersConfig::buildStatusMsg( $cid )
			);
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
	 * Edits configuration parameter
	 * @return void
	 */
	public function edit( )
	{
		$data = array(
			'key' => JRequest::getString( 'key', 0 ),
			'section' => JRequest::getCmd( 'section' ),
			'name' => JRequest::getCmd( 'name' ),
			'value' => JRequest::getString( 'value' )
		);

		$response = array( );

		if ( !$this->_validate( array( $data ) ) ) {
			$response = array(
				'error' => true,
				'msg' => $this->getError( )
			);
			echo json_encode( $response );
			JFactory::getApplication( )->close( );
			return true;
		}

		if ( $data['id'] ) {
			$params = array(
				'id' => $data['id'],
				'value' => $data['value']
			);
		}
		else {
			$params = array(
				'section' => $data['section'],
				'name' => $data['name'],
				'value' => $data['value']
			);
		}
		$success = $this->getModel( 'Config' )->setData( $params )->update( );

		if ( $success ) {
			$response = array( 'error' => false, );
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
	 * Checks input data for correct value
	 * @param  array $data Data to  be checked
	 * @return boolean
	 */
	protected function _validate( array $data )
	{
		foreach ($data as $name => $item) {
			// Check necessity of validation
			if ( (($item['name'] == 'send_errors_email') && isset( $data['errors_recipient_type'] ) && ($data['errors_recipient_type']['value'] != 1)) || (in_array( $item['name'], array(
				'sync_periodicity',
				'errors_recipient_type',
				'send_errors_email'
			) ) && isset( $data['sbsynchronizer'] ) && !$data['sbsynchronizer']['value']) || (($item['name'] == 'clean_history_periodicity') && isset( $data['clean_history'] ) && !$data['clean_history']['value']) ) {
				continue;
			}

			if ( empty( $item['key'] ) && (empty( $item['name'] ) || empty( $item['section'] )) ) {
				$this->setError( JText::_( "SB_NO_IDENTIFY" ) );
				return false;
			}

			if ( in_array( $item['name'], array(
				'sync_periodicity',
				'send_errors_email',
				'clean_history_periodicity'
			) ) && empty( $item['value'] ) ) {
				$this->setError( JText::sprintf( "SB_NO_VALUE_ERROR", $item['name'] ) );
				return false;
			}

			if ( (($item['name'] == 'sync_periodicity') || ($item['name'] == 'clean_history_periodicity')) && (!is_numeric( $item['value'] ) || ($item['value'] <= 0)) ) {
				$this->setError( JText::sprintf( "SB_NO_INT_VALUE_ERROR", $item['name'] ) );
				return false;
			}

			if ( $item['name'] == 'send_errors_email' ) {
				jimport( 'joomla.mail.helper' );
				if ( !JMailHelper::isEmailAddress( $item['value'] ) ) {
					$this->setError( JText::sprintf( "SB_NO_EMAIL_ERROR", $item['name'] ) );
					return false;
				}
			}
		}

		return true;
	}

}
