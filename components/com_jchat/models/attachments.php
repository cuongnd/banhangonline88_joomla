<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::ATTACHMENTS::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.filesystem.file');

/**
 * Here the entity is the file attachment message on stream
 * 
 * @package JCHAT::ATTACHMENTS::components::com_jchat
 * @subpackage models
 * @since 1.0
 */ 
class JChatModelAttachments extends JChatModel {
	/**
	 * @access private
	 * @var int
	 */
	private $cacheFolder;
	
	/**
	 * User object
	 *
	 * @access private
	 * @var int
	 */
	private $myUser;
	
	/**
	 * Generate file name hash
	 * 
	 * @access private
	 * @param string $filename
	 * @param string $userid
	 * @return string 
	 */
	private function generaHash($filename, $userid) { 
		$filenameStripped = JFile::stripExt($filename); 
		$fileExtension = JFile::getExt($filename);
		 
		$hash = md5($filenameStripped . $userid);
		return $hash . '.' . $fileExtension;
	}
	
	/**
	 * Store file attachment message on database as a special record
	 * 
	 * @access private
	 * @param string $filename
	 * @return boolean 
	 */
	private function storeDBMessage($filename) { 
		if ($this->getState('to', null) && !empty($filename)) {
			// Get user reference
			$to = $this->getState('to', null);
			$tologged = $this->getState('tologged', null);

			// Valid target user session id?
			if($to == -1 && $tologged) {
				$sessionSql =  "SELECT" .
							   "\n " . $this->_db->quoteName('session_id') .
							   "\n FROM #__session" .
							   "\n WHERE" .
							   "\n " . $this->_db->quoteName('userid') . " = " . (int)$tologged .
							   "\n ORDER BY " . $this->_db->quoteName('time') . " DESC" .
							   "\n LIMIT 1";
				$this->_db->setQuery($sessionSql);
				$sessionIDReceiver = $this->_db->loadResult();
				$to = $sessionIDReceiver ? $sessionIDReceiver : -1;
			}
			
			// Get users actual names
			$actualNames = JChatHelpersUsers::getActualNames ( $this->getState('from'), $this->getState('to'), $this->componentParams );
			
			$unixTimeStamp = time();
			$sql = "INSERT INTO #__jchat (" .
					$this->_db->quoteName('from') . ',' .
					$this->_db->quoteName('to') . ',' .
					$this->_db->quoteName('fromuser') . ',' .
					$this->_db->quoteName('touser') . ',' .
					$this->_db->quoteName('message') . ',' .
					$this->_db->quoteName('sent') . ',' .
					$this->_db->quoteName('read') . ',' .
					$this->_db->quoteName('type') . ',' .
					$this->_db->quoteName('status') . ',' .
					$this->_db->quoteName('actualfrom') . ',' .
					$this->_db->quoteName('actualto') . ') VALUES( ' . 
					$this->_db->quote($this->getState('from')). ", ".
					$this->_db->quote($to). ",".
					$this->_db->quote($this->myUser->id). ", ".
					$this->_db->quote($tologged). ",".
					$this->_db->quote($filename) . ",".
					$this->_db->quote($unixTimeStamp) . ",".
					"0" . "," .
					$this->_db->quote('file') . "," .
					"0" . "," .
					$this->_db->quote($actualNames['fromActualName']) . ", ".
					$this->_db->quote($actualNames['toActualName']) . 
					")";
		    $this->_db->setQuery($sql);
			if(!$this->_db->execute()){
				return false;
			} 
			
			if (empty($_SESSION['jchat_user_'.$this->getState('to')])) {
				$_SESSION['jchat_user_'.$this->getState('to')] = array();
			}
			
			$lastInsertId = $this->_db->insertid();
			$insertTime = JHtml::_('date', $unixTimeStamp, JText::_('DATE_FORMAT_LC2'));
			$_SESSION['jchat_user_'.$this->getState('to')][$lastInsertId] = array("id" => $this->_db->insertid(),
																				  "from" => $this->getState('to'),
																				  "message" => $filename,
																				  "type" => 'file',
																				  "status" => 0,
																				  "time" => $insertTime,
																				  "self" => 1,
																				  "old" => 1) ;
		}
		return true;
	}

	/**
	 * Read file by chunks to send to output buffer
	 * 
	 * @access private
	 * @param string $nomefile
	 * @return boolean
	 */
	private function readFileChunked($filePath) {
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$buffer = '';
		$cnt = 0;
		$handle = fopen ( $filePath, 'rb' );
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
	 * Detect mime type for streamed file
	 * 
	 * @access private
	 * @param $filename
	 * @return string Il mime type trovato a fronte del lookup nella tabella
	 */
	private function detectMimeType($filename) {
		global $mosConfig_absolute_path;
		include_once JPATH_COMPONENT_ADMINISTRATOR . '/framework/helpers/mime.mapping.php';
		
		$filename = strtolower ( $filename );
		$exts = preg_split ( "#[/\\.]#i", $filename );
		$n = count ( $exts ) - 1;
		$fileExtension = $exts [$n];
		
		foreach ( $mime_extension_map as $extension => $mime ) {
			if ($extension === $fileExtension)
				return $mime;
		}
		return 'application/octet-stream';
	}

	/**
	 * Store uploaded file to cache folder, 
	 * fully manage error messages and ask for database insert
	 * 
	 * @access public
	 * @return void 
	 */
	public function storeEntity() {
		$tmpFile = $this->requestArray[$this->requestFilesName]['newfile']['tmp_name'];
		$tmpFileName = $this->requestArray[$this->requestFilesName]['newfile']['name'];
		
		if(!$tmpFile || !$tmpFileName) {
			$msg = JText::_('COM_JCHAT_NOFILE_SELECTED');
			$this->setError($msg);
			return;
		}
		
		$tmpFileSize = $this->requestArray[$this->requestFilesName]['newfile']['size'];
		$allowedFileSize = $this->componentParams->get('maxfilesize', 2) * 1024 * 1024; // MB->Bytes
		if($tmpFileSize > $allowedFileSize) {
			$msg = JText::_('COM_JCHAT_SIZE_ERROR') .' Max ' . $this->componentParams->get('maxfilesize', 2) . 'MB.';
			$this->setError($msg);
			return;
		}
		
		$disallowedExtensions = explode(',', $this->componentParams->get('disallowed_extensions', 'exe,bat,pif')); 
		$tmpFileExtension = @array_pop(explode('.', $tmpFileName));
		if(in_array($tmpFileExtension, $disallowedExtensions)) {
			$msg = JText::_('COM_JCHAT_EXT_ERROR') . $this->componentParams->get('disallowed_extensions', 'exe,bat,pif');
			$this->setError($msg);
			return;
		}
				
		if(!is_dir($this->cacheFolder)) {
			JFolder::create($this->cacheFolder);
		}
		
		if(!is_writable($this->cacheFolder)) {
			try {
				if(!chmod($this->cacheFolder, 0775)) {
					throw new Exception( JText::_('COM_JCHAT_DIR_WRITABLE'));
				}
			} catch(Exception $e) {
				$msg = $e->getMessage();
				$this->setError($msg);
				return;
			}
		}
		 
		if(!move_uploaded_file($tmpFile, $this->cacheFolder . $tmpFileName)) {
			$msg =  JText::_('COM_JCHAT_UPLOAD_ERROR');
			$this->setError($msg);
			return;
		}
	 
		$hashedFileName = $this->generaHash($tmpFileName, $this->getState('from'));
		
		if(file_exists($this->cacheFolder . $hashedFileName)) {
			unlink($this->cacheFolder . $hashedFileName);
		}
		 
		if(!rename($this->cacheFolder . $tmpFileName, $this->cacheFolder . $hashedFileName)) {
			$msg = JText::_('COM_JCHAT_RENAME_ERROR');
			$this->setError($msg);
			return;
		}
		
		$filter = JFilterInput::getInstance();
		if(!$this->storeDBMessage($filter->clean($tmpFileName))) {
			$msg =  JText::_('COM_JCHAT_SENDMSGFILE_ERROR');
			$this->setError($msg);
			return;
		}
		
		$msg = JText::_('COM_JCHAT_SUCCESS_FILEUPLOAD');
		$this->setState('result', $msg);
	}

	/**
	 * Download uploaded file message
	 * 
	 * @access public
	 * @return void
	 */
	public function loadEntity($ids = null) { 
		$idMessage = $this->getState('idMessage');
		$idUserConversation = $this->getState('from');
		 
		try {
			$query = "SELECT #__jchat.from, #__jchat.message FROM #__jchat WHERE id = " . (int)$idMessage;
			$this->_db->setQuery($query);
			$resultInfo = $this->_db->loadObject();
			if(!$resultInfo) {
				$conversationArray = $_SESSION['jchat_user_' . $idUserConversation];
				foreach ($conversationArray as $message) {
					if($message['id'] == $idMessage) {
						$resultInfo = new stdClass();
						$resultInfo->from = $message['from'];
						$resultInfo->message = $message['message'];
						break;
					} 
				}
				if(!$resultInfo) {
					throw new Exception('COM_JCHAT_ERROR_NOTFOUND_FILE');
				}
			}
			$fileName = $this->generaHash($resultInfo->message, $resultInfo->from);
			$filePath = $this->cacheFolder . $fileName;
			
			if(!file_exists($filePath)) {
				throw new Exception('COM_JCHAT_ERROR_DELETED_FILE');
			}
		} catch (Exception $e) {
			// JS inject
			echo '<script>alert("' . JText::_($e->getMessage()) . '");window.history.go(-1);</script>';
			exit();
		} 
		
		$fsize = @filesize ( $filePath );
		$mod_date = date ( 'r', filemtime ( $filePath ) ); 
		$cont_dis = 'attachment';
		$mimeType = $this->detectMimeType ( $fileName );
		
		// required for IE, otherwise Content-disposition is ignored
		if (ini_get ( 'zlib.output_compression' )) {
			ini_set ( 'zlib.output_compression', 'Off' );
		}
		header ( "Pragma: public" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Expires: 0" );
		header ( "Content-Transfer-Encoding: binary" );
		header ( 'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $resultInfo->message . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $fsize . ';' ); //RFC2183
		header ( "Content-Type: " . $mimeType ); // MIME type
		header ( "Content-Length: " . $fsize );
		if (! ini_get ( 'safe_mode' )) { // set_time_limit doesn't work in safe mode
			@set_time_limit ( 0 );
		}
		// No encoding - we aren't using compression... (RFC1945)
		//header("Content-Encoding: none");
		//header("Vary: none");
		$downloadStatus = $this->readFileChunked ( $filePath );
		
		// Al raggiungimento dell'effettivo download si aggiorna lo status update
		if($downloadStatus) {
			$query = "UPDATE #__jchat SET status=1 WHERE id = " . (int)$idMessage;
			$this->_db->setQuery($query); 
			$this->_db->execute();
		} 
		exit();
	}

	/**
	 * Class constructor
	 * 
	 * @access public
	 * @return Object &
	 */
	public function __construct($config = null) {
		$this->getComponentParams();
		
		$this->cacheFolder = JPATH_SITE . '/components/com_jchat/cache/';
		$this->myUser = JFactory::getUser();
		
		parent::__construct($config);
	}
} 
?>