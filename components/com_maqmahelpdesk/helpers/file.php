<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskFile
{
	var $aFiles;
	var $fVerbose;
	var $fCasesensitiv = true;
	var $fLowercase = false;

	/** <b>Constructor</b><br>
	@param fVerbose = false display trace information
	 */
	function HelpdeskFile($fVerbose = false)
	{
		$this->fVerbose = $fVerbose;
		$this->Init();
	}

	/** Initialize Class
	 */
	function Init()
	{
		unset($this->aFiles);
		$this->aFiles = array();
	}

	/** Output messages to screen with echo
	@param sText
	 */
	function Log($sText)
	{
		if ($this->fVerbose) {
			echo $sText;
			flush();
		}
	}

	/** reads directories and filenames
	@param $sPath string path eg. '../xx/yy/' (please notice the last '/')
	@param $sInclude string regular expression for filtering directories and filenames
	@param $fRecursive bool read subdirectories as well
	@param $fFiles bool include files
	@param $fDirectory bool include directories
	@param $sRoot string prepend root directory string (good for converting filesystem paths to urls)
	@param $sExclude string regular expression for excluding files and directories
	@param $bWritable bool check if it's writable or not
	 */
	function Read($sPath, $sInclude = '', $fRecursive = false, $fFiles = true, $fDirectories = true, $sRoot = '', $sExclude = '', $bWritable = true)
	{
		$this->Log("Path: $sPath<br>\n");
		$this->Log("Include: $sInclude<br>\n");
		$this->Log("Recursive: $fRecursive<br>\n");
		$this->Log("Files: $fFiles<br>\n");
		$this->Log("Directories: $fDirectories<br>\n");
		$this->Log("Root: $sRoot<br>\n");
		$this->Log("Exclude: $sExclude<br>\n");
		$this->Log("Writable: $bWritable<br>\n");

		$aFiles = array();
		$oHandle = opendir($sPath);
		while (($sFilename = readdir($oHandle)) !== false) {
			if ($sFilename == '.' || $sFilename == '..')
				continue;
			$aFiles[] = $sFilename;
		}
		closedir($oHandle);

		foreach ($aFiles as $sFilename) {
			$sFullname = $sRoot . $sFilename;
			$fInsert = true;
			$fIsDirectory = is_dir($sPath . $sFilename);

			$fExclude = false;
			if (!empty($sExclude)) {
				if ($this->GetCasesensitiv())
					$fExclude = preg_match($sExclude, $sFullname);
				else
					$fExclude = preg_match($sExclude . '/i', $sFullname);
				if ($fExclude) {
					$this->Log("Excluded: $sFullname<br>\n");
					$fInsert = false;
				}
			}

			$fInclude = true;
			if (!empty($sInclude) && !$fIsDirectory) {
				if ($this->GetCasesensitiv())
					$fInclude = preg_match($sInclude, $sFullname);
				else
					$fInclude = preg_match($sInclude . '/i', $sFullname);
				if (!$fInclude) {
					$this->Log("Not Included: $sFullname<br>\n");
					$fInsert = false;
				}
			}

			if (!$fFiles && !$fIsDirectory)
				$fInsert = false;
			if (!$fDirectories && $fIsDirectory)
				$fInsert = false;


			if ($fInsert) {
				$this->Log("Ok: $sFullname<br>\n");

				$i = strrpos($sFilename, '.') + 1;
				if (substr($sFilename, $i - 1, 1) == '.') {
					$sFile = substr($sFilename, 0, $i - 1);
					$sExtension = substr($sFilename, $i);
				}
				else {
					$sFile = $sFilename;
					$sExtension = '';
				}

				if ($this->GetLowercase()) {
					$sFile = JString::strtolower($sFile);
					$sExtension = JString::strtolower($sExtension);
				}

				if ($bWritable) {
					$fIsWritable = is_writable($sPath . $sFile);
				}

				$aFile = array
				(
					'Path' => $sRoot,
					'File' => $sFile,
					'Extension' => $sExtension,
					'Filename' => $sFilename,
					'Fullname' => $sRoot . $sFilename,
					'IsDirectory' => $fIsDirectory,
					'IsWritable' => $fIsWritable
				);

				// Insert current file into aFiles array
				$this->aFiles[] = $aFile;
			}

			// Recursion?
			if ($fRecursive && $fIsDirectory && !$fExclude) {
				$this->Log("Rekursion: $sPath$sFilename/<br>\n");
				$this->Read($sPath . $sFilename . '/', $sInclude, $fRecursive, $fFiles, $fDirectories, $sRoot . $sFilename . '/', $sExclude, $bWritable);
			}
		}
	}

	/** Returns number of files/directories found
	return int
	 */
	function Count()
	{
		return (count($this->aFiles));
	}

	/** Sorts array of found items
	param $sKey Key to sort by: File, Extension, Filename, Fullname
	param $fAscending = true
	 */
	function Sort($sKey, $fAscending)
	{
		foreach ($this->aFiles as $aFile)
			$aSort[] = $aFile[$sKey];

		if ($fAscending)
			array_multisort($aSort, $this->aFiles);
		else
			array_multisort($aSort, SORT_DESC, $this->aFiles);
	}

	/** outputs everything found (good for debugging)
	 */
	function Output()
	{
		echo 'Number of Items found: ' . $this->Count() . "<br>\n";
		echo "<hr />\n";
		foreach ($this->aFiles as $aFile)
			$this->OutputFile($aFile);
	}

	/** outputs everything of a file entry (good for debugging)
	@param aFile File entry
	 */
	function OutputFile($aFile)
	{
		printf("Path: %s<br>\n", $this->GetPath($aFile));
		printf("File: %s<br>\n", $this->GetFile($aFile));
		printf("Extension: %s<br>\n", $this->GetExtension($aFile));
		printf("IsDirectory: %s<br>\n", $this->GetIsDirectory($aFile) ? 'true' : 'false');
		printf("IsFile: %s<br>\n", $this->GetIsFile($aFile) ? 'true' : 'false');
		printf("Filename: %s<br>\n", $this->Filename($aFile));
		printf("Directoryname: %s<br>\n", $this->Directoryname($aFile));
		printf("Fullname: %s<br>\n", $this->Fullname($aFile));
		printf("Is Writable: %s<br>\n", $this->IsWritable($aFile));
		echo "<hr />\n";
	}

	/** returns the path of a file (or directory)
	@param aFile File entry
	@return string
	 */
	function GetPath($aFile)
	{
		return ($aFile['Path']);
	}

	/** returns the filename without the extension of a file (or directory)
	@param aFile File entry
	@return string
	 */
	function GetFile($aFile)
	{
		return ($aFile['File']);
	}

	/** returns the extension of a file (or directory)
	@param aFile File entry
	@return string
	 */
	function GetExtension($aFile)
	{
		return ($aFile['Extension']);
	}

	/** returns true, if entry is a directory
	@param aFile File entry
	@return bool
	 */
	function GetIsDirectory($aFile)
	{
		return ($aFile['IsDirectory']);
	}

	/** returns true, if entry is a file
	@param aFile File entry
	@return bool
	 */
	function GetIsFile($aFile)
	{
		return (!$this->GetIsDirectory($aFile));
	}

	/** Returns Filename or Directory name (including ending '/')
	@param aFile File entry
	@return string
	 */
	function Filename($aFile)
	{
		if ($this->GetIsDirectory($aFile))
			return ($aFile['Filename'] . '/');
		else
			return ($aFile['Filename']);
	}

	/** Directoryname returns the same as Filename, but without a ending '/' for Directories.
	@param aFile File entry
	@return string
	 */
	function Directoryname($aFile)
	{
		return ($aFile['Filename']);
	}

	/** Returns Fullname (path and filename)
	@param aFile File entry
	@return string
	 */
	function Fullname($aFile)
	{
		if ($this->GetIsDirectory($aFile))
			return ($aFile['Fullname'] . '/');
		else
			return ($aFile['Fullname']);
	}

	/** Returns IsWritable (path and filename)
	@param aFile File entry
	@return string
	 */
	function IsWritable($aFile)
	{
		return ($aFile['IsWritable']);
	}

	/** Returns an array of fullnames (that is path and filename)
	return array of strings
	 */
	function Fullnames()
	{
		reset($this->aFiles);
		foreach ($this->aFiles as $sKey => $aFile)
			$aFiles[$this->Fullname($aFile)] = $this->FullName($aFile);

		return ($aFiles);
	}

	/** Returns an array of filenames (that is filename with extension, but without path)
	return array of strings
	 */
	function Filenames()
	{
		reset($this->aFiles);
		foreach ($this->aFiles as $sKey => $aFile)
			$aFiles[$this->Filename($aFile)] = $this->Filename($aFile);

		return ($aFiles);
	}

	/** Are filters casesensitiv?
	 */
	function SetCasesensitiv($fValue)
	{
		$this->fCasesensitiv = $fValue;
	}

	function GetCasesensitiv()
	{
		return ($this->fCasesensitiv);
	}

	/** Convert found file/directorynames into lowercase?
	 */
	function SetLowercase($fValue)
	{
		$this->fLowercase = $fValue;
	}

	function GetLowercase()
	{
		return ($this->fLowercase);
	}

	static function FormatFileSize($size)
	{
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2) . $units[$i];
	}

	static function deleteFile($id, $extid, $filetype)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();

		// Security measure against SQL Injection
		$id = mysql_escape_string($id);

		// Get the user name
		$database->setQuery("SELECT `name` FROM #__users WHERE id='" . $user->id . "'");
		$username = $database->loadResult();

		// Get the attachment info
		$sql = "SELECT * FROM #__support_file WHERE id_file='" . $id . "' AND id='" . $extid . "' AND source='" . $filetype . "'";
		$file_info = null;
		$database->setQuery($sql);
		$file_info = $database->loadObject();

		$file = $supportConfig->docspath . '/' . $file_info->filename;

		if ($is_support || ($user->id == $file_info->id_user)) {
			if ($filetype == 'T') {
				$ticketLogMsg = str_replace('%1', $username, JText::_('attached_deleted'));
				$ticketLogMsg = str_replace('%2', $file_info->filename, $ticketLogMsg);
				HelpdeskTicket::Log($extid, $ticketLogMsg, JText::_('attached_deleted_hidden'), '');

				$database->setQuery("DELETE FROM #__support_file WHERE id_file='" . $id . "'");
				$database->query();
				(file_exists($file)) ? unlink($file) : HelpdeskUtility::AddGlobalMessage(JText::_('download_notfound'), 'w');
				HelpdeskUtility::AddGlobalMessage(JText::_('download_filedeled'), 'i');
			}
		} else {
			HelpdeskUtility::AddGlobalMessage(JText::_('access_denied_title'), 'w');
		}
	}

	static function Download($id, $extid, $filetype)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();
		$is_client = HelpdeskUser::IsClient();

		//Security measure against SQL Injection
		$id = mysql_escape_string($id);

		//Get the filename either from Support or Clients table
		$fileObj = null;
		switch ($filetype) {
			case 'D':
				$sql = "SELECT filename, filename_original
						FROM #__support_dl_version
						WHERE id='" . $id . "'";
				$attach_subdir = '/';
				break;
			case 'C':
				$sql = "SELECT filename
						FROM #__support_client_docs
						WHERE id='" . $id . "'";
				$attach_subdir = 'docs/';
				break;
			case 'T':
				$sql = "SELECT filename
						FROM #__support_file
						WHERE id_file='" . $id . "' AND id='" . $extid . "' AND source='T'";
				$attach_subdir = '/';
				break;
			case 'K':
				$sql = "SELECT filename
						FROM #__support_file
						WHERE id_file='" . $id . "' AND id='" . $extid . "' AND source='K'";
				$attach_subdir = '/';
				break;
			case 'B':
				$sql = "SELECT filename
						FROM #__support_file
						WHERE id_file='" . $id . "' AND id='" . $extid . "' AND source='B'";
				$attach_subdir = 'bugtracker/';
				break;
		}

		$database->setQuery($sql);
		$fileObj = $database->loadObject();
		$file = $supportConfig->docspath . $attach_subdir . $fileObj->filename;

		if (file_exists($file))
		{
			if (ini_get('zlib.output_compression'))
			{
				ini_set('zlib.output_compression', 'Off');
			}

			header('HTTP/1.1 200 OK');
			header('Status: 200 OK');
			header("Cache-Control: private");
			header('Pragma: public');
			header("Expires: 0");

			if (function_exists('mime_content_type') && mime_content_type($file))
			{
				header('Content-Type: ' . mime_content_type($file));
			} else {
				if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
					header('Content-Type: application/force-download');
				} else {
					header('Content-Type: application/octet-stream');
				}
			}

			header("Content-Transfer-Encoding: binary\n");
			header('Cache-Control: cache, must-revalidate');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Disposition: attachment; filename="' . ($filetype == 'D' ? $fileObj->filename_original : $fileObj->filename) . '"');
			header('Content-Length: ' . filesize($file));

			ob_clean();
			ob_end_clean();
			flush();

			if (!ini_get('safe_mode'))
			{
				@set_time_limit(0);
			}

			@readfile($file);
			exit;
		} else {
			HelpdeskUtility::AddGlobalMessage(JText::_('download_notfound'), 'e');
			echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
		}
	}

	static function Upload($id, $source, $file, $file_path, $desc = '', $update = 0, $public = 1, $field = '', $id_reply = 0)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$is_support = HelpdeskUser::IsSupport();

		$temp_name = $_FILES[$file]['tmp_name'];
		$file_name = $_FILES[$file]['name'];
		$file_type = substr(strrchr($file_name, '.'), 1);
		$file_size = $_FILES[$file]['size'];
		$result = $_FILES[$file]['error'];

		if ($id && $source == 'T')
		{
			// Get Ticket Details
			$database->setQuery("SELECT ticketmask FROM #__support_ticket WHERE id='" . $id . "'");
			$ticket = $database->loadObject();
		}

		// Check if user uploads are allowed
		if (!$update && $source != 'D' && $source != 'CA' && $source != 'CLI' && $source != 'C' && $source != 'K' && $source != 'W')
		{
			if (!$is_support && !$supportConfig->public_attach)
			{
				HelpdeskUtility::AddGlobalMessage(JText::_('upload_notallowed'), 'e', JText::_('upload_notallowed'));
				return false;
				exit();
			}
		}
		// Check if the folder exists, if not creates it
		if (!is_dir($file_path))
		{
			mkdir($file_path);
		}
		//File Name Check
		if ($file_name == "")
		{
			//HelpdeskUtility::AddGlobalMessage( JText::_('upload_wrong'), 'e', JText::_('upload_wrong').': ' .$file_name );
			return false;
			exit();
		}
		//File Size Check
		else if ($file_size > $supportConfig->maxAllowed)
		{
			HelpdeskUtility::AddGlobalMessage(JText::_('upload_filesize'), 'e', JText::_('upload_filesize') . ': ' . $file_size);
			return false;
			exit();
		}
		//File Type Check
		else
		{
			$ext_error = false;
			$allowed = trim(str_replace(' ', '', $supportConfig->extensions));
			$file_ext = explode(',', $allowed);
			for ($i = 0, $n = count($file_ext); $i < $n; $i++)
			{
				if (JString::strtolower($file_type) == JString::strtolower($file_ext[$i]))
				{
					$ext_error = true;
				}
			}
			if ($ext_error == false)
			{
				HelpdeskUtility::AddGlobalMessage(JText::_('upload_filetype') . $file_type, 'e', JText::_('upload_filetype') . $file_type);
				return false;
				exit();
			}
		}

		isset($ticket) && $source == 'T' ? $tkt_mask = $ticket->ticketmask . '_' : $tkt_mask = 'file_';

		$copy = '';
		$n = 0;
		while (file_exists($file_path . $tkt_mask . $copy . $file_name)) {
			$n++;
			$copy = $n . '_';
		}

		$file_name = $source != 'U' ? $tkt_mask . $copy . $file_name : $file_name;

		$move_file = move_uploaded_file($temp_name, $file_path . $file_name);

		if ($move_file && $ext_error)
		{
			@chmod($file_path . $file_name, 0644);
			if ($source == 'T' || $source == 'K' || $source == 'B' )
			{
				$sql = "INSERT INTO #__support_file(id, id_user, date, source, filename, description, public, id_reply)
						VALUES('" . $id . "', '" . $user->id . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "'," . $database->quote($source) . ", " . $database->quote($file_name) . ", " . $database->quote($desc) . ", " . $public . ", " . $id_reply . ")";
				$database->setQuery($sql);
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';

			}
			elseif ($source == 'C')
			{
				$available = JRequest::getInt('available', 0);
				$database->setQuery("INSERT INTO #__support_client_docs(id_client, date_created, description, filename, available) VALUES('" . $id . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d") . "', " . $database->quote($desc) . ", " . $database->quote($file_name) . ", $available)");
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';

			}
			elseif ($source == 'W')
			{
				$database->setQuery("UPDATE #__support_workgroup SET logo=" . $database->quote($file_name) . " WHERE id='" . $id . "'");
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';

			}
			elseif ($source == 'D')
			{
				$database->setQuery("UPDATE #__support_dl SET " . $field . "=" . $database->quote($file_name) . " WHERE id='" . $id . "'");
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';

			}
			elseif ($source == 'CA')
			{
				$database->setQuery("UPDATE #__support_dl_category SET " . $field . "=" . $database->quote($file_name) . " WHERE id='" . $id . "'");
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';

			}
			elseif ($source == 'V')
			{
				$database->setQuery("UPDATE #__support_dl_version SET filename=" . $database->quote($file_name) . ", filename_original=" . $database->quote($_FILES[$file]['name']) . " WHERE id='" . $id . "'");
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';

			}
			elseif ($source == 'CLI')
			{
				$database->setQuery("UPDATE #__support_client SET logo=" . $database->quote($file_name) . " WHERE id='" . $id . "'");
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';

			}
			elseif ($source == 'U')
			{
				$database->setQuery("UPDATE #__support_users SET avatar=" . $database->quote(JURI::root() . 'media/com_maqmahelpdesk/images/avatars/' . $file_name) . " WHERE id_user='" . $user->id . "'");
				!$database->query() ? HelpdeskUtility::AddGlobalMessage($database->getErrorMsg(), 'e', $database->stderr(1)) : '';
			}
		}
		else
		{
			HelpdeskUtility::AddGlobalMessage(JText::_('upload_wrong'), 'e', JText::_('upload_wrong'));
			return false;
			exit();
		}
		unset($temp_name);
		return ($source == 'T' ? $move_file : true);
	}

	static function GetAttachments($id, $source)
	{
		$database = JFactory::getDBO();

		$sql = "SELECT u.`name`, f.`id_file`, f.`id`
			FROM `#__support_file` AS f
				 INNER JOIN `#__users` AS u ON u.`id`=f.`id_user`
			WHERE f.`source`='" . $source . "' AND f.`id`=" . $id;
		$database->setQuery($sql);
		$attachments = $database->loadObjectList();

		return $attachments;
	}

	static function downloadFileFromServer($url, $path, $file_name)
	{
		$buffer = '';
		if($file_name == NULL){ $file_name = basename($url);}
		$url_stuff = parse_url($url);
		$port = isset($url_stuff['port']) ? $url_stuff['port'] : 80;

		$fp = fsockopen($url_stuff['host'], $port);
		if(!$fp){ return false;}

		$query  = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
		$query .= 'Host: ' . $url_stuff['host'];
		$query .= "\n\n";

		fwrite($fp, $query);

		while ($tmp = fread($fp, 8192))   {
			$buffer .= $tmp;
		}

		preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);
		$file_binary = substr($buffer, - $parts[1]);
		if($file_name == NULL){
			$temp = explode(".",$url);
			$file_name = $temp[count($temp)-1];
		}
		$file_open = fopen($path . "/" . $file_name,'w');
		if(!$file_open){ return false;}
		fwrite($file_open,$file_binary);
		fclose($file_open);
		return true;
	}
}
