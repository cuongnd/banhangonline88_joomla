<?php
// namespace administrator\components\com_jchat\models;
/**
 * @package JCHAT::RECORDER::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Recorder medias model responsibilities
 *
 * @package JCHAT::RECORDER::administrator::components::com_jchat
 * @subpackage models
 * @since 2.9
 */
class JChatModelRecorder extends JChatModel {
	/**
	 * Legge e invia nello stream di output i contenuti del file in chunk,
	 * ovviando ai problemi di limiti legati alla normale readfile
	 * @access private
	 * @param string $nomefile
	 * @return boolean
	 */
	private function readfile_chunked($filename) {
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$buffer = '';
		$cnt = 0;
		$handle = fopen ( $filename, 'rb' );
		if ($handle === false) {
			return false;
		}
		while ( ! feof ( $handle ) ) {
			$buffer = fread ( $handle, $chunksize );
			echo $buffer;
			@ob_flush ();
			flush ();
		}
		$status = fclose ( $handle );
		return $status;
	}
	
	/**
	 * Restituisce la query string costruita per ottenere il wrapped set richiesto in base
	 * allo userstate, opzionalmente seleziona i campi richiesti
	 * 
	 * @access private
	 * @return string
	 */
	protected function buildListQuery($fields = 'a.*') {
		// WHERE
		$where = array();
		$whereString = null;
				
		//Filtro testo
		if($this->state->get('searchword')) {
			$where[] = "\n (a.peer1 LIKE " .
						$this->_db->quote('%' . $this->state->get('searchword') . '%') .
						"\n OR a.peer2 LIKE " . 
						$this->_db->quote('%' . $this->state->get('searchword'). '%') . ")";
		}
		
		//Filtro periodo
		if($this->state->get('fromPeriod')) {
			$where[] = "\n a.timerecord > " . $this->_db->quote($this->state->get('fromPeriod'));
		}
		
		if($this->state->get('toPeriod')) {
			$where[] = "\n a.timerecord <= " . $this->_db->quote($this->state->get('toPeriod'));
		}
		
		if (count($where)) {
			$whereString = "\n WHERE " . implode ("\n AND ", $where);
		}
		
		// ORDERBY
		if($this->state->get('order')) {
			$orderString = "\n ORDER BY " . $this->state->get('order') . " ";
		}
		
		//Filtro testo
		if($this->state->get('order_dir')) {
			$orderString .= $this->state->get('order_dir');
		}
		
		$query = "SELECT $fields" .
				 "\n FROM #__jchat_recordings AS a" .
				 $whereString .
				 $orderString;
		return $query;
	}

	/**
	 * Main get data method
	 * @access public
	 * @return Object[]
	 */
	public function getData() {
		// Build query
		$query = $this->buildListQuery ();
		$this->_db->setQuery ( $query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		try {
			$result = $this->_db->loadObjectList ();
			if($this->_db->getErrorNum()) {
				throw new JChatException(JText::_('COM_JCHAT_ERROR_RETRIEVING_MESSAGES') . $this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->app->enqueueMessage($e->getMessage(), $e->getErrorLevel());
			$result = array();
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->app->enqueueMessage($jchatException->getMessage(), $jchatException->getErrorLevel());
			$result = array();
		}
		return $result;
	}
	
	/**
	 * Restituisce le select list usate dalla view per l'interfaccia
	 * @access public
	 * @return array
	 */
	public function getFilters() {
		$lists = array();
		
		return $lists;
	}
	
	/**
	 * Delete entity
	 *
	 * @param array $ids
	 * @access public
	 * @return boolean
	 */
	public function deleteEntity($ids) {
		$table = $this->getTable ();
		
		// Ciclo su ogni entity da cancellare
		if (is_array ( $ids ) && count ( $ids )) {
			foreach ( $ids as $id ) {
				try {
					$table->load($id);
					
					// Delete the file system media too
					$pathToMedias = JPATH_ROOT . '/media/com_jchat/recordings/';
					if(file_exists($pathToMedias . $table->title)) {
						if(!unlink($pathToMedias . $table->title)) {
							throw new JChatException ( JText::_('COM_JCHAT_ERROR_DELETING_MEDIAFILE'), 'error' );
						}
					}
						
					if (! $table->delete ( $id )) {
						throw new JChatException ( $table->getError (), 'error' );
					}
				} catch ( JChatException $e ) {
					$this->setError ( $e );
					return false;
				} catch ( Exception $e ) {
					$JChatException = new JChatException ( $e->getMessage (), 'error' );
					$this->setError ( $JChatException );
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Download uploaded file message
	 *
	 * @access public
	 * @return boolean
	 */
	public function downloadEntity($ids = null) {
		$table = $this->getTable ();
		
		// Ciclo su ogni entity da cancellare
		if (is_array ( $ids ) && count ( $ids )) {
			foreach ( $ids as $id ) {
				try {
					$table->load($id);
					// Delete the file system media too
					$pathToMedias = JPATH_ROOT . '/media/com_jchat/recordings/';
					if(!file_exists($pathToMedias . $table->title)) {
						throw new JChatException ( JText::_('COM_JCHAT_NOEXISTS_MEDIAFILE'), 'warning' );
					}
					
					$fsize = @filesize ( $pathToMedias . $table->title );
					$cont_dis = 'attachment';
					$mimeType = 'video/webm';
					
					// required for IE, otherwise Content-disposition is ignored
					if (ini_get ( 'zlib.output_compression' )) {
						ini_set ( 'zlib.output_compression', 'Off' );
					}
					header ( "Pragma: public" );
					header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
					header ( "Expires: 0" );
					header ( "Content-Transfer-Encoding: binary" );
					header ( 'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $table->title . '";' . ' size=' . $fsize . ';' ); //RFC2183
					header ( "Content-Type: " . $mimeType ); // MIME type
					header ( "Content-Length: " . $fsize );
					if (! ini_get ( 'safe_mode' )) { // set_time_limit doesn't work in safe mode
						@set_time_limit ( 0 );
					}
					// No encoding - we aren't using compression... (RFC1945)
					//header("Content-Encoding: none");
					//header("Vary: none");
					$this->readfile_chunked($pathToMedias . $table->title);
					
					jexit();
				} catch ( JChatException $e ) {
					$this->setError ( $e );
					return false;
				} catch ( Exception $e ) {
					$JChatException = new JChatException ( $e->getMessage (), 'error' );
					$this->setError ( $JChatException );
					return false;
				}
			}
		}
		return true;
	}
 
	/**
	 * Class constructor
	 *
	 * @access public
	 * @param $config array
	 * @return Object&
	 */
	public function __construct($config = array()) {
		parent::__construct ( $config );

		$componentParams = JComponentHelper::getParams($this->option);
		$this->setState('cparams', $componentParams);
	}
} 