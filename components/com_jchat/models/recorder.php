<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::RECORDER::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Here the entity is the file media file XHR2 uploaded
 * 
 * @package JCHAT::RECORDER::components::com_jchat
 * @subpackage models
 * @since 2.9
 */ 
class JChatModelRecorder extends JChatModel {
	/**
	 * Store the media uploaded file on a dedicated table for the backend manager
	 * 
	 * @access private
	 * @return boolean 
	 */
	private function storeDBMessage() { 
		$table = $this->getTable ();
		
		// Bind values to the ORM
		$table->title = $this->getState('blobfilename');
		$table->size = $this->getState('blobfilesize');
		$table->timerecord = $this->getState('timerecord');
		$table->peer1 = $this->getState('peer1');
		$table->peer2 = $this->getState('peer2');
		
		if (! $table->store ()) {
			return false;
		}
		
		return true;
	}

	/**
	 * Store uploaded file to the media recordings folder, 
	 * fully manage error messages and ask for database insert
	 * 
	 * @access public
	 * @return Object 
	 */
	public function storeEntity() {
		// Response JSON object
		$response = new stdClass ();
		$this->getComponentParams();
		$recordedMediaFolder = JPATH_ROOT . '/media/com_jchat/recordings';
		$alreadyExists = false;
		
		// Get uploaded model state
		$uploadedMediaFile = $this->getState('blobfile');
		$uploadedMediaFileName = $this->getState('blobfilename');
		
		try {
			$tmpFile = $uploadedMediaFile['tmp_name'];
			 
			if(!$tmpFile) {
				throw new Exception(JText::_('COM_JCHAT_NOFILE_SELECTED'));
			}
			 
			if(!is_dir($recordedMediaFolder)) {
				JFolder::create($recordedMediaFolder);
			}
			 
			if(!is_writable($recordedMediaFolder)) {
				if(!chmod($recordedMediaFolder, 0775)) {
					throw new Exception( JText::_('COM_JCHAT_MEDIADIR_WRITABLE'));
				}
			}
		
			// Check if file already esists
			if(file_exists($recordedMediaFolder . '/' . $uploadedMediaFileName)) {
				unlink($recordedMediaFolder . '/' . $uploadedMediaFileName);
				$alreadyExists = true;
			}
			
			if(!move_uploaded_file($tmpFile, $recordedMediaFolder . '/' . $uploadedMediaFileName)) {
				throw new Exception( JText::_('COM_JCHAT_MEDIAUPLOAD_ERROR'));
			}
			
			if(!$alreadyExists) {
				if(!$this->storeDBMessage($uploadedMediaFileName)) {
					throw new Exception( JText::_('COM_JCHAT_STORING_MEDIADB_ERROR'));
				}
			}
		} catch ( Exception $e ) {
			$response->result = false;
			$response->exception_message = $e->getMessage ();
			return $response;
		}
		
		// Manage exceptions from DB Model and return to JS domain
		$response->result = true;
		
		return $response;
	}

	/**
	 * Class constructor
	 * 
	 * @access public
	 * @return Object &
	 */
	public function __construct($config = null) {
		parent::__construct($config);
	}
} 
?>