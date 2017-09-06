<?php
/**
 * SocialBacklinks Back-End Controller for syncronizing
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
 * Sync controller
 */
class SBControllersSync extends SBControllersBase
{
	/**
	 * Stores status of sync
	 * @var array
	 */
	protected $_status;

	/**
	 * Performs manually sync
	 * @return void
	 */
	public function synchronize( )
	{
		@set_time_limit(0);
		@ignore_user_abort(true);
		if (JRequest::getInt('diagnose',0) == 1) {
			@error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
			@ini_set('display_errors', TRUE);
		} else {
			@error_reporting(0);
			@ini_set('display_errors', FALSE);
		}
		
		if ( $loading = $this->_request->get( 'loading', false ) ) {
			$this->_request->set( array(
				'view' => 'histories',
				'layout' => 'sync_result'
			) );
			$this->getView( 'histories' )->assign( 'loading', true );
			parent::display( );
		}
		else {
			$path = $this->_getSyncFilePath( );
			
			// Remove temp file
			if ( file_exists( $path ) ) {
				unlink( $path );
			}
			
			$this->sync( );
			
			// Remove temp file
			if ( file_exists( $path ) ) {
				unlink( $path );
			}

			// Get statistics information after last record
			$model = $this->getModel( 'histories' );
			
			if ( SBHelpersSync::getLastId( 'history', false ) >= 0 ) {
				$model = $model->reset( )->last_id( SBHelpersSync::getLastId( 'history', false ) );
			}
			
			// Get view off synchronization result
			$view = $this->getView( 'histories', 'html' );
			$view->setLayout( 'sync_result' );
			$view->setModel( $model, true );

			$contents = '';
			ob_start( );
			$view->display( );
			$contents = ob_get_clean();

			$response = array(
				'error' => false,
				'html' => $contents
			);
			echo json_encode( $response );
			JFactory::getApplication( )->close( );
		}

	}

	/**
	 * Performs sync process
	 * @return void
	 */
	public function sync( )
	{
		// Checking connection to at least one social network
		$networks = array( );
		foreach (SBPlugin::get('network.') as $network) {
			if ( $network->enabled ) {
				$networks[$network->getAlias( )] = $network;
			}
		}
		if ( !count( $networks ) ) {
			return true;
		}

		$last_sync = SBHelpersSync::getLastSyncDate( );
		if (JRequest::getInt('diagnose',0) == 1) {
			echo "Last sync date: $last_sync<br />";
		}
		SBHelpersSync::updateLastSyncDate( );
		// Performing all content plugins
		foreach (SBPlugin::get('content.') as $content) {
			$this->_performErrors( $content, $networks );
			$this->_postNewArticles( $content, $networks );
			SBHelpersSync::cleanHistory();
		}
	}

	/**
	 * Tries to resend items with errors
	 * @param  SBPluginsContentsInterface The content plugin
	 * @param  array The list of active networks
	 * @return void
	 */
	protected function _performErrors( $plugin, $networks )
	{
		$selected_content = (bool)$plugin->selected_content;
		$errors = JModelLegacy::getInstance( 'SBModelsErrors' )->plugin( $plugin )->select( '*' )->getList( );
		if ( count( $errors ) ) {
			SBHelpersSync::setLastId();
			foreach ($errors as $error) {
				$item = SBHelpersSync::formatData( $plugin, $error );

				if ( isset( $networks[$error->network] ) ) {
					try {
						$success = $networks[$error->network]->addPost( $item->title, $item->link, $item->desc, $item->image );
					}
					catch(SBPluginsException $e) {
						$success = false;
						$error = $e->getMessage( );
					}

					if ( $success ) {
						JModelLegacy::getInstance( 'SBModelsErrors' )->setData( array( 'cid' => $error->socialbacklinks_error_id ) )->delete( );

						$date = SBHelpersSync::convertDate( );

						// Add the result of the sends to history
						$params = array(
							'network' => $error->network,
							'extension' => $plugin->getAlias( ),
							'item_id' => $error->item_id,
							'title' => $error->title,
							'result' => $success,
							'created' => $date->toSql()
						);

						JModelLegacy::getInstance( 'SBModelsHistories' )->reset( )->setData( $params )->insert( );
					}

				}
			}
		}
	}

	/**
	 * Sends new article from last check to social networks
	 * @param  SBPluginsContentsInterface $plugin
	 * @param  array The list of active networks
	 * @return void
	 */
	protected function _postNewArticles( $plugin, $networks )
	{
		$last_sync = SBHelpersSync::getLastSyncDate( );
		$rows = null;
		try
		{
			$rows = JModelLegacy::getInstance( 'SBModelsItems' )->reset( )->plugin( $plugin )->getNewList( $last_sync );
		}
		catch(Exception $e)
		{
		}
		
		if ( empty( $rows ) ) {
			return true;
		}
		SBHelpersSync::setLastId();
		//FIXME Discuss why errors are not taken into account here?

		// Save the total count of rows multiplies on count networks
		$is_progressbar_enabled = $this->_request->get( 'progress', false );
		if ( count( $rows ) && count( $networks ) && $is_progressbar_enabled ) {
			$filepath = $this->_getSyncFilePath( );
			$this->_status = array(
				'total' => count( $rows ) * count( $networks ),
				'count' => 0
			);

			file_put_contents( $filepath, json_encode( array( 'sync_status' => 0.05 ) ) );
		}

		// Send articles to social networks
		foreach ($rows as $row) {
			$item = SBHelpersSync::formatData( $plugin, $row );
			
			foreach ($networks as $alias => $network) {
				// Send article to social network
				try {
					$success = $network->addPost( $item->title, $item->link, $item->desc, $item->image );
				}
				catch(SBPluginsException $e) {
					$success = false;
					$error = $e->getMessage( );
				}

				$date = SBHelpersSync::convertDate( );
				// Add the result of the sends in history
				$params = array(
					'network' => $alias,
					'extension' => $plugin->getAlias( ),
					'item_id' => $row->id,
					'title' => $row->title,
					'result' => $success,
					'created' => $date->toSql( )
				);

				JModelLegacy::getInstance( 'SBModelsHistories' )->reset( )->setData( $params )->insert( );

				// Add in file information about synchronization process
				if ( $is_progressbar_enabled ) {
					$this->_status['count'] += 1;
					file_put_contents( $filepath, json_encode( array( 'sync_status' => round( $this->_status['count'] / $this->_status['total'], 2 ) ) ) );
				}

				// Save the non send article for another attempt
				if ( !$success ) {
					SBHelpersSync::setLastId( 'error' );
					$params = array(
						'network' => $alias,
						'extension' => $plugin->getAlias( ),
						'item_id' => $row->id,
						'created' => $date->toSql( ),
						'message' => $error
					);
					JModelLegacy::getInstance( 'SBModelsErrors' )->reset( )->setData( $params )->update( );
				}
			}
		}
	}

	/**
	 * Returns the address of file to save synchronization status
	 * @return string
	 */
	protected function _getSyncFilePath( )
	{
		$session_id = JFactory::getSession( )->getId( );
		$file_path = JPATH_ROOT . '/tmp/socialbacklinks_' . md5( $session_id ) . '.txt';
		return $file_path;
	}

	/**
	 * Checks status of synchronization
	 * @return void
	 */
	public function checkStatus( )
	{
		$file_path = $this->_getSyncFilePath( );

		if ( file_exists( $file_path ) ) {
			$data = json_decode( file_get_contents( $file_path ) );
			$sync_status = $data->sync_status;
		}
		else {
			$sync_status = 0;
		}

		$data = array( 'sync_status' => $sync_status );
		echo json_encode( $data );
		JFactory::getApplication( )->close( );
	}

}
