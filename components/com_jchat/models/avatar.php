<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Avatar manager model public responsibilities
 *
 * @package JCHAT::AVATAR::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
interface IAvatarModel {
	/**
	 * Create and return the current avatar thumbnail filename
	 *
	 * @access public
	 * @param string $extension2Append
	 * @return array Il path completo e soltanto il filename al file thumb avatar creato o da creare
	 */
	public function getAvatarThumbnailFileName($extension2Append = 'png');
}



/**
 * Avatar manager
 * @package JCHAT::AVATAR::components::com_jchat
 * @subpackage models
 * @since 1.0
 */ 
class JChatModelAvatar extends JChatmodel implements IAvatarModel{
	/**
	 * Site URL
	 * @access private
	 * @var string
	 */
	private $siteurl;
	
	/**
	 * User identifier here based on session id
	 * @access private
	 * @var int
	 */
	private $sessionId;
	
	/**
	 * User identifier here based on user id
	 * @access private
	 * @var int
	 */
	private $userId;
	
	/**
	 * @access private
	 * @var string
	 */
	private $userName;
	
	/**
	 * @access private
	 * @var string
	 */
	private $avatarFolder;
	
	/**
	 * @access private
	 * @static
	 * @var string
	 */
	private static $avatarFormat = 'png';
	
	/**
	 * @access private
	 * @static
	 * @var string
	 */
	private static $avatarSubPath = '/images/avatars/';
	  
	
	/**
	 * @access private
	 * @param string $originalFilename
	 * @param string $thumbFilename
	 * @return boolean 
	 */
	private function createThumbnail($originalFilename, $thumbFilename) { 
		$thumb = JChatThumbFactory::create($originalFilename);
		// Selezione modo di resizing
		switch ($this->componentParams->get('cropmode', 0)){
			case '0':
				$thumb->resize(50, 50);
				break;
				
			case '1':
				$thumb->adaptiveResize(50, 50);
				break;
		}
		
		$thumb->save($thumbFilename, 'png');
	}
	
	/**
	 * Create and return the current avatar thumbnail filename
	 * 
	 * @access public
	 * @param string $extension2Append
	 * @return array Il path completo e soltanto il filename al file thumb avatar creato o da creare
	 */
	public function getAvatarThumbnailFileName($extension2Append = 'png') {
		// Calculate in base all'md5 di id utente e username
		$calculatedHash = $this->userId ? 'uidavatar_' . $this->userId : 'gsidavatar_' . $this->sessionId;
		$finalName = $calculatedHash . '.' . $extension2Append;
		$completePathName = $this->avatarFolder . '/' . $finalName;
		$liveUrl = $this->siteurl . '/images/avatars';
			
		return array($completePathName, $finalName, $liveUrl);
	}
	
	/**
	 * @access public 
	 * @return void 
	 */
	public function storeEntity() {
		// Recupera il file in upload
		$tmpFile = $this->requestArray[$this->requestFilesName]['newavatar']['tmp_name'];
		$tmpFileName = $this->requestArray[$this->requestFilesName]['newavatar']['name'];
		
		// Nessun file inviato
		if(!$tmpFile || !$tmpFileName) {
			$msg = JText::_('COM_JCHAT_NOFILE_SELECTED');
			$this->setError($msg);
			return;
		}
		 
		// Controlla se il file è inferiore alla dimensione massima, altrimenti interrompe con errore
		$tmpFileSize = $this->requestArray[$this->requestFilesName]['newavatar']['size'];
		$allowedFileSize = $this->componentParams->get('maxfilesize', 2) * 1024 * 1024; // MB->Bytes
		if($tmpFileSize > $allowedFileSize) {
			$msg = JText::_('COM_JCHAT_SIZE_ERROR') . 'Max ' . $this->componentParams->get('maxfilesize', 2) . 'MB.';
			$this->setError($msg);
			return;
		}
		
		// Controlla se il file ha una estensione ammessa, altrimenti interrompe con errore
		$allowedExtensions = explode(',', $this->componentParams->get('avatar_allowed_extensions', 'jpg,jpeg,png,gif')); 
		$tmpFileExtension = @array_pop(explode('.', $tmpFileName));
		if(!in_array($tmpFileExtension, $allowedExtensions)) {
			$msg = JText::_('COM_JCHAT_EXT_ERROR') . $this->componentParams->get('avatar_allowed_extensions', 'jpg,jpeg,png,gif');
			$this->setError($msg);
			return;
		}
				
		// Controlla se la cartella target è scrivibile, altrimenti prova a settare i chmod, altrimenti interrompe con errore
		if(!is_writable($this->avatarFolder)) {
			// prova a amodificare i permessi
			try {
				if(!chmod($this->avatarFolder, 0775)) {
					throw new Exception( JText::_('COM_JCHAT_DIR_WRITABLE'));
				}
			} catch(Exception $e) {
				$msg = $e->getMessage();
				$this->setError($msg);
				return;
			}
		}
		 
		// Effettua la move uploaded file
		if(!move_uploaded_file($tmpFile, $this->avatarFolder . '/' . $tmpFileName)) {
			$msg = JText::_('COM_JCHAT_UPLOAD_ERROR');
			$this->setError($msg);
			return;
		}
		
		// Richiede la creazione di un hash filename per il thumbnail con format force a png
		$thumbnailFileName = $this->getAvatarThumbnailFileName(self::$avatarFormat);
		
		// Se esiste il file ripuliamo adesso; il file name è sempre univoco per utente anche il formato è forced a png
		if(file_exists($thumbnailFileName[0])) {
			unlink($thumbnailFileName[0]);
		}
		
		// Genera un thumbnail per l'immagine caricata
		$this->createThumbnail($this->avatarFolder . '/' . $tmpFileName, $thumbnailFileName[0]);
		
		// Elimina il file originale caricato 
		unlink ($this->avatarFolder . '/' . $tmpFileName);
		 
		// Richiama la showForms con user message 
		$msg = JText::_('COM_JCHAT_SUCCESS_AVATAR');
		$this->setState('result', $msg);
	}

	/**
	 * @access public
	 * @return void 
	 */
	public function deleteEntity($ids = null) {
	// Richiede la creazione di un hash filename per il thumbnail con format force a png
		$thumbnailFileName = $this->getAvatarThumbnailFileName(self::$avatarFormat);
		
		// Se esiste il file ripuliamo adesso; il file name è sempre univoco per utente anche il formato è forced a png
		if(file_exists($thumbnailFileName[0])) {
			if(unlink($thumbnailFileName[0])){ 
				$msg = JText::_('COM_JCHAT_SUCCESS_DELETE_AVATAR'); 
			} else {
				$msg = JText::_('COM_JCHAT_ERROR_DELETING_AVATAR');
				$this->setError($msg);
				return;
			}
		} else {
			$msg = JText::_('COM_JCHAT_AVATAR_NOTFOUND');
			$this->setError($msg);
			return;
		}
		
		$this->setState('result', $msg);
	}
	
	/**
	 * Class constructor
	 * @access public 
	 * @return Object &
	 */
	public function __construct($config = null) {
		$this->siteurl = JURI::base() . '/components/com_jchat/';
		$this->avatarFolder = JPATH_COMPONENT . '/images/avatars';
		
		// Current user object
		$userObject = JFactory::getUser();
		// Current user session table
		$userSessionTable = JChatHelpersUsers::getSessionTable();
		
		$this->sessionId = $userSessionTable->session_id;
		$this->userId = $userObject->id;
		$this->userName = $userObject->username;
	 
		// CONFIG LOAD
		$this->getComponentParams(); 
		
		parent::__construct($config);
	}
} 
?>