<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
class FSJ_TM_Trans_Helper
{
	static function GetBaseFile($filename, $client_id, $tag, $component)
	{
		$basetag = FSJ_TM_Helper::GetBaseLanguage();
		$path_base = FSJ_TM_Helper::getPath($client_id, $basetag, $component);
		$basefile = new FSJ_TM_File();
		$basefile->base_tag = $basetag;
		$path = $path_base . DS . $basetag . "." . $filename;
		if (!file_exists($path)) // not found published file, try an unpublished one
		{
			$path = $path_bas . DS . "_unpub.".$basetag . "." . $filename;
		}
		if (!file_exists($path)) // no base file found, try loading current file instead 
		{
			$filename = str_replace($basetag, $tag, $filename);
			$path = $path_base . DS . $tag . "." . $filename;
			$basefile->has_base = false;
		}
		if (!file_exists($path)) // current file not found, try for unpub current file
		{
			$filename = str_replace($basetag, $tag, $filename);
			$path = $path_base . DS . "_unpub.".$tag . "." . $$filename;
			$basefile->has_base = false;
		}
		if (!file_exists($path))
		{
			return false;	
		}
		$basefile->LoadBase($path);
		return $basefile;
	}
	static function AddLangFile(&$strings, $filename, $tag, $client_id, $component)
	{
		if ($client_id == 1)
		{
			$path_base = JPATH_ADMINISTRATOR;
		} else {
			$path = JPATH_SITE;
		}
		$path = FSJ_TM_Helper::getPath($client_id, $tag, $component) . $tag . "." . $filename;
		if (!file_exists($path))
		{
			$strings->published = false;
			$path = FSJ_TM_Helper::getPath($client_id, $tag, $component) . "_unpub." . $tag . "." . $filename;
		}
		if (!file_exists($path))
		{
			$strings->is_new = true;
			return $path;	
		}
		$strings->LoadFile($path);	
		return $path;	
	}
}
class FSJ_TM_File {
	var $has_comments = false;
	var $has_base = true;
	var $is_new = false;
	var $base_tag = "";
	var $published = true;
	var $header_orig = array();
	var $header_trans = array();
	var $lines = array();
	var $key_index = array();
	function LoadBase($filename)
	{
		//echo "Loading Base File : $filename<br>";
		$file = file_get_contents($filename);
		$lines = explode("\n", $file);
		$last_added = null;
		$this->lines = array();
		$offset = -1;
		$header = true;
		$branding = FSJ_TM_Helper::getIgnoreLines();
		foreach ($lines as $line)
		{
			$line = trim($line);
			if (in_array(substr($line,2), $branding))
				continue;
			if ($header)
			{
				if ($line != "" && FSJ_TM_Helper::isComment($line))
				{
					$this->AddHeader($line);
					continue;
				} else {
					$header = false;
				}
			}	
			if ($line == "")
				continue;
			if (FSJ_TM_Helper::isComment($line))
			{
				$this->has_comments = true;
				// Adding a comment, if the last line was a comment batch them together
				if (!$last_added || !$last_added->is_comment)
				{
					$last_added = new FSJ_TM_Line();
					$last_added->is_comment = true;
					$this->lines[++$offset] = $last_added;
				}
				if ($last_added)
					$last_added->AddComment($line);
			} else {
				// add a new line
				if (!$last_added || !array_key_exists($last_added->key, $this->lines))
				{
					$last_added = new FSJ_TM_Line();
					$last_added->setOrig($line);	
					if ($last_added->key != "" && !array_key_exists($last_added->key, $this->key_index))
					{
						$this->lines[++$offset] = $last_added;
						$this->key_index[$last_added->key] = $offset;
					}
				}
			}
		}
	}
	function LoadFile($filename)
	{
		//echo "Loading Translated File : $filename<br>";
		$file = file_get_contents($filename);
		$lines = explode("\n", $file);
		$header = true;
		$branding = FSJ_TM_Helper::getIgnoreLines();
		foreach ($lines as $line)
		{
			$line = trim($line);
			if (in_array(substr($line,2), $branding))
				continue;
			if ($header)
			{
				if ($line != "" && FSJ_TM_Helper::isComment($line))
				{
					$this->AddHeader($line, false);
					continue;
				} else {
					$header = false;
				}
			}	
			if ($line == "")
				continue;
			if (FSJ_TM_Helper::isComment($line))
				continue;
			$parsed = FSJ_TM_Helper::ParseLine($line);
			if ($parsed)
			{
				if (array_key_exists($parsed->key, $this->key_index))
				{
					$offset = $this->key_index[$parsed->key];
					$line_obj = $this->lines[$offset];
					$line_obj->trans = $parsed->value;
				} else {
					$new_line = new FSJ_TM_Line();
					$new_line->is_new = 1;
					$new_line->setTrans($line);	
					$this->lines[] = $new_line;
				}
			}
		}
	}
	function AddHeader($text, $base = true)
	{
		if (substr($text, 0, 1) == "[" && substr($text, -1) == "]")
		{
			$text = trim($text, "[]");
			$text = trim($text);	
		} else {
			$text = substr($text, 1);
			$text = trim($text);
		}
		if ($base)
		{
			$this->header_orig[] = $text;
		} else {
			$this->header_trans[] = $text;
		}		
	}
	function getBaseHeader()
	{
		return implode("<br />", $this->header_orig);
	}	
	function getTransHeader()
	{
		return implode("\n", $this->header_trans);
	}
	function getTransHeaderCnt()
	{
		return count($this->header_trans);
	}
	function LoadSaved($input)
	{
		if (!is_array($input) && !is_object($input))
			return;
		foreach ($input as $key => $value)
		{
			if (array_key_exists($key, $this->key_index))
			{
				$offset = $this->key_index[$key];
				$line_obj = $this->lines[$offset];
				$line_obj->trans = $value;
			} else {
				// add new string to the file	
				$new_line = new FSJ_TM_Line();
				$new_line->is_new = 1;
				$new_line->key = $key;
				$new_line->trans = $value;	
				$this->lines[] = $new_line;
			}
		}	
	}
	function toFile()
	{
		$output = array();	
		foreach ($this->header_trans as $header)
			$output[] = "; " . $header;
		if (count($this->header_trans) > 0)
			$output[] = "";
		$save_branding = 1;
		/**/
		$save_branding = FSJ_Settings::get('tm_options','branding');
		/**/
		if ($save_branding)
		{
			$branding = FSJ_TM_Helper::getBranding();
			foreach ($branding as $line)
				$output[] = "; " . $line;
			$output[] = "";
		}
		$new_header = false;
		foreach ($this->lines as $line)
		{
			if ($line->is_comment)
			{
				$output[] = "";
				foreach ($line->comment as $comment)
					$output[] = "; " . $comment;	
			} else if ($line->trans !== NULL) {
				if ($line->is_new && !$new_header)
				{
					$output[] = "";
					$header = FSJ_TM_Helper::getAdditionalHeader();
					foreach ($header as $text)
						$output[] = "; " . $text;
					$output[] = "";
					$new_header = true;
				}
				$output[] = $line->toIni();//
			}
		}
		return implode("\n", $output) . "\n";
	}
}
class FSJ_TM_Line {
	var $is_comment = false;
	var $is_new = false;
	var $comment = array();
	var $key;
	var $base;
	var $trans;	
	function AddComment($text)
	{
		$this->comment[] = FSJ_TM_Helper::ParseComment($text);
	}
	function setOrig($text)
	{
		$parsed = FSJ_TM_Helper::ParseLine($text);
		if ($parsed)
		{
			$this->key = $parsed->key;
			$this->base = $parsed->value;
		}
	}	
	function setTrans($text)
	{
		$parsed = FSJ_TM_Helper::ParseLine($text);
		if ($parsed)
		{
			$this->key = $parsed->key;
			$this->trans = $parsed->value;
		}
	}
	function getInputValue()
	{
		$value = $this->trans;
		if (!$value)
			$value = $this->base;
		return htmlspecialchars($value);
	}
	function isLong()
	{
		if (strlen($this->base) > 80 || strlen($this->trans) > 80)
			return true;
		return false;
	}
	function isDone()
	{
		return ! ($this->trans === NULL);
	}
	function toIni()
	{
		if (FSJ_Settings::get('tm_options', 'quotes'))
		{
			$trans = str_replace("\"","\"_QQ_\"", $this->trans);
		} else {
			$trans = str_replace("\"","&quot;", $this->trans);
		}
		return $this->key . "=\"" . $trans . "\"";	
	}
}
