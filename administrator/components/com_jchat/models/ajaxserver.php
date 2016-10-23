<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::AJAXSERVER::components::com_jchat 
 * @subpackage models
 * @author Joomla! Extensions Store
 * @copyright (C)2014 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

/**
 * Ajax Server model responsibilities
 *
 * @package JCHAT::AJAXSERVER::components::com_jchat  
 * @subpackage models
 * @since 2.9
 */
interface IAjaxserverModel {
	public function loadAjaxEntity($id, $param, $DIModels) ;
}

/** 
 * Classe che gestisce il recupero dei dati per il POST HTTP
 * @package JCHAT::AJAXSERVER::components::com_jchat  
 * @subpackage models
 * @since 2.9
 */
class JChatModelAjaxserver extends JChatModel implements IAjaxserverModel {
	/**
	 * Get license informations about this user subscription license email code
	 * Use the RESTFul interface API on the remote License resource
	 *
	 * @static
	 * @access private
	 * @param Object[] $additionalModels Array for additional injected models type hinted by interface
	 * @return Object
	 */
	private function getLicenseStatus($additionalModels = null) {
		// Get email license code
		$code = JComponentHelper::getParams('com_jchat')->get('registration_email', null);
	
		// Instantiate HTTP client
		$HTTPClient = new JChatHttp();
	
		/*
		 * Status domain code
		 * Remote API Call
		 */
		$headers = array('Accept'=>'application/json', 'User-agent' => 'JChatsocial Enteprise updater');
		if($code) {
			try {
				$prodCode = 'jchatent';
				$cdFuncUsed = 'str_' . 'ro' . 't' . '13';
				$HTTPResponse = $HTTPClient->get($cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet') . "/option,com_easycommerce/action,licenseCode/email,$code/productcode,$prodCode", $headers);
			} catch (Exception $e) {
				$HTTPResponse = new stdClass();
				$HTTPResponse->body = '{"success":false,"reason":"connection_error","details":"' . $e->getMessage() . '"}';
			}
		} else {
			$HTTPResponse = new stdClass();
			$HTTPResponse->body = '{"success":false,"reason":"nocode_inserted"}';
		}
			
		// Deserializing della response
		try {
			$objectHTTPResponse = json_decode($HTTPResponse->body);
			if(!is_object($objectHTTPResponse)) {
				throw new Exception('decoding_error');
			}
		} catch (Exception $e) {
			$HTTPResponse = new stdClass();
			$HTTPResponse->body = '{"success":false,"reason":"' . $e->getMessage() . '"}';
			$objectHTTPResponse = json_decode($HTTPResponse->body);
		}
	
		return $objectHTTPResponse;
	}
	
	/**
	 * Perform the asyncronous update of the component
	 * 1- Dowload the remote update package file
	 * 2- Use the Joomla installer to install it
	 * 3- Return status to the js app
	 *
	 * @static
	 * @access private
	 * @param Object[] $additionalModels Array for additional injected models type hinted by interface
	 * @return Object
	 */
	private function downloadComponentUpdate($additionalModels = null) {
		// Response JSON object
		$response = new stdClass ();
		$cdFuncUsed = 'str_' . 'ro' . 't' . '13';
		$ep = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet' . '/XZY1305SQUOnifs3243564864kfunjx35tdrnty1386g.ugzy');
		$file_path = JFactory::getConfig()->get('tmp_path', '/tmp') . '/KML1305FDHBavsf3243564864xshawk35gqeagl1386t.zip';
	
		try {
			// Ensure CURL support
			if (! function_exists ( 'curl_init' )) {
				throw new JChatException ( JText::_ ( 'COM_JCHAT_CURL_NOT_SUPPORTED' ), 'error' );
			}
	
			// Firstly test if the server is up and HTTP 200 OK
			$ch = curl_init($ep);
			curl_setopt( $ch, CURLOPT_NOBODY, true );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
			curl_setopt( $ch, CURLOPT_MAXREDIRS, 3 );
			curl_exec( $ch );
	
			$headerInfo = curl_getinfo( $ch );
			curl_close( $ch );
			if($headerInfo['http_code'] != 200 || !$headerInfo['download_content_length']) {
				throw new JChatException ( JText::_ ( 'COM_JCHAT_ERROR_DOWNLOADING_REMOTE_FILE' ), 'error' );
			}
	
			// 1- Download the remote update package file and put in local file
			$fp = fopen ($file_path, 'w+');
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $ep );
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			curl_exec( $ch );
			curl_close( $ch );
			fclose( $fp );
	
			if (!filesize($file_path)) {
				throw new JChatException ( JText::_ ( 'COM_JCHAT_ERROR_WRITING_LOCAL_FILE' ), 'error' );
			}
	
			// All went well
			$response->result = true;
		} catch ( JChatException $e ) {
			$response->result = false;
			$response->exception_message = $e->getMessage ();
			$response->errorlevel = $e->getErrorLevel();
			return $response;
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( JText::sprintf ( 'COM_JCHAT_ERROR_UPDATING_COMPONENT', $e->getMessage () ), 'error' );
			$response->result = false;
			$response->exception_message = $jchatException->getMessage ();
			$response->errorlevel = $jchatException->getErrorLevel();
			return $response;
		}
	
		return $response;
	}
	
	/**
	 * Perform the asyncronous update of the component
	 * 1- Dowload the remote update package file
	 * 2- Use the Joomla installer to install it
	 * 3- Return status to the js app
	 *
	 * @static
	 * @access private
	 * @param Object[] $additionalModels Array for additional injected models type hinted by interface
	 * @return Object
	 */
	private function installComponentUpdate($additionalModels = null) {
		// Response JSON object
		$response = new stdClass ();
		$file_path = JFactory::getConfig()->get('tmp_path', '/tmp') . '/KML1305FDHBavsf3243564864xshawk35gqeagl1386t.zip';
	
		try {
			// Unpack the downloaded package file.
			$package = JInstallerHelper::unpack($file_path, true);
			if(!$package) {
				throw new JChatException ( JText::_ ( 'COM_JCHAT_ERROR_EXTRACTING_UPDATES' ), 'error' );
			}
	
			// 2- Use the Joomla installer to install it
			// New plugin installer
			$updateInstaller = new JInstaller ();
			if (! $updateInstaller->install ( $package['extractdir'] )) {
				throw new JChatException ( JText::_ ( 'COM_JCHAT_ERROR_INSTALLING_UPDATES' ), 'error' );
			}
	
			// Delete dirty files and folder
			unlink($file_path);
			$it = new RecursiveDirectoryIterator($package['extractdir'], RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($it,
					RecursiveIteratorIterator::CHILD_FIRST);
			foreach($files as $file) {
				if ($file->isDir()){
					rmdir($file->getRealPath());
				} else {
					unlink($file->getRealPath());
				}
			}
			// Delete the now empty folder
			rmdir($package['extractdir']);
	
			// All went well
			$response->result = true;
		} catch ( JChatException $e ) {
			$response->result = false;
			$response->exception_message = $e->getMessage ();
			$response->errorlevel = $e->getErrorLevel();
			return $response;
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( JText::sprintf ( 'COM_JCHAT_ERROR_UPDATING_COMPONENT', $e->getMessage () ), 'error' );
			$response->result = false;
			$response->exception_message = $jchatException->getMessage ();
			$response->errorlevel = $jchatException->getErrorLevel();
			return $response;
		}
	
		return $response;
	}
	
	/**
	 * Mimic an entities list, as ajax calls arrive are redirected to loadEntity public responsibility to get handled
	 * by specific subtask. Responses are returned to controller and encoded from view over HTTP to JS client
	 * 
	 * @access public 
	 * @param string $id Rappresenta l'op da eseguire tra le private properties
	 * @param mixed $param Parametri da passare al private handler
	 * @param Object[]& $DIModels
	 * @return Object& $utenteSelezionato
	 */
	public function loadAjaxEntity($id, $param , $DIModels) {
		//Delega la private functions delegata dalla richiesta sulla entity
		$response = $this->$id($param, $DIModels);

		return $response;
	}
}