<?php
// 
// key 
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
/**/
class TM_Auto_Yandex
{
	var $key = '';
	function getLangCode($jcode)
	{
		$codes = array(
			'af-ZA' => 'af',		// Afrikaans
			'sq-AL' => 'sq',		// Albanian
			'ar-AA' => 'ar',		// Arabic
			'be-BY' => 'be',		// Belarusian
			'bg-BG' => 'bg',		// Bulgarian
			'ca-ES' => 'ca',		// Catalan
			'zh-CN' => 'zh',		// Chinese Simplified
			'zh-TW' => 'zh',		// Chinese Traditional
			'hr-HR' => 'hr',		// Croatian
			'cs-CZ' => 'cs',		// Czech
			'da-DK' => 'da',		// Danish
			'nl-NL' => 'nl',		// Dutch
			'nl-BE' => 'nl',		// Flemish
			'en-AU' => 'en',		// English
			'en-GB' => 'en',		// English
			'en-US' => 'en',		// English
			'et-EE' => 'et',		// Estonian
			'fi-FI' => 'fi',		// Finnish
			'fr-FR' => 'fr',		// French
			'fr-CA' => 'fr',		// French Canadian		
			'gl-ES' => 'gl',		// Galician
			'de-DE' => 'de',		// German
			'el-GR' => 'el',		// Greek
			'he-IL' => 'iw',		// Hebrew
			'hu-HU' => 'hu',		// Hungarian
			'id-ID' => 'id',		// Indonesian
			'it-IT' => 'it',		// Italian
			'jp-JP' => 'ja',		// Japanese
			'ko-KR' => 'ko',		// Korean
			'lv-LV' => 'lv',		// Latvian
			'lt-LT' => 'lt',		// Lithuanian
			'mk-MK' => 'mk',		// Macedonian
			'ms-MY' => 'ms',		// Malay
			'nb-NO' => 'no',		// Norwegian
			'fa-IR' => 'fa',		// Persian
			'pl-PL' => 'pl',		// Polish
			'pt-BR' => 'pt',		// Portuguese
			'pt-PT' => 'pt',		// Portuguese
			'ro-RO' => 'ro',		// Romanian
			'ru-RU' => 'ru',		// Russian
			'sr-RS' => 'sr',		// Serbian
			'sk-SK' => 'sk',		// Slovak
			'es-ES' => 'es',		// Spanish
			'sw-KE' => 'sw',		// Swahili
			'sv-SE' => 'sv',		// Swedish
			'ta-IN' => 'ta',		// Tamil
			'th-TH' => 'th',		// Thai
			'tr-TR' => 'tr',		// Turkish
			'uk-UA' => 'uk',		// Ukrainian
			'vi-VN' => 'vi');		// Vietnamese
		if (array_key_exists($jcode, $codes))
			return $codes[$jcode];
		return null;
	}
	function Translate($source, $dest, $phrases, &$results)
	{
		return $this->Translate_Batch($source, $dest, $phrases, $results);
	}
	function file_get_contents_utf8($url)
	{
		$curl = curl_init();
		// Setup headers - I used the same headers from Firefox version 2.0.0.6
		// below was split up because php.net said the line was too long. :/
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: "; // browsers keep this blank.
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		// execute the curl command
		if(! $html = curl_exec($curl)) 
		{ 
			trigger_error(curl_error($curl)); 
		} 
		$charset = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
		curl_close($curl); // close the connection
		return $html; // and finally, return $html
	}
	function fixPhrase($in)
	{
		$letters=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		for ($i = 0 ; $i < count($letters) ; $i++)
		{
			$letter = $letters[$i];
			$in = str_ireplace("%" . $letter, "XXXXXX" . $i . "XXXXXX", $in);
		}
		return $in;
	}
	function Translate_Batch($source, $dest, $phrases, &$results)
	{	
		if (!$this->getLangCode($dest))
			return "No matching target language code found for $dest";
		$tag = "xxyyxxyyxx";
		$this->key = FSJ_Settings::get('tm_options', 'yandexkey');
		if ($this->key == "") return "Error: You must enter your Yandex API Key in the Options page for the component.";
		$url = "https://translate.yandex.net/api/v1.5/tr.json/translate?key=" . $this->key . "&";
		$result = array();
		$to_translate = array();
		foreach ($phrases as $key => $phrase)
		{
			$phrase = $this->fixPhrase($phrase);
			$to_translate[] = "$key\n$phrase\n";
		}
		$text = implode($to_translate);
		$params = array();
		$params['lang'] = $this->getLangCode($source) . "-" . $this->getLangCode($dest);
		$params['text'] = $text;
		$parts = array();
		foreach($params as $param => $value)
		{
			$parts[] = "$param=".urlencode($value);
		}
		$furl = $url . implode("&", $parts);
		// file get contents was returning ISO-8859-2 charset, which was losing lots of info 
		//echo $furl . "<br>";
		//$furl = "https://translate.google.com/translate_a/single?client=t&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&ie=UTF-8&oe=UTF-8&rom=0&ssel=0&tsel=4&kc=0&sl=en&tl=fr&hl=en&q=hello";
		//echo $furl . "<br>";
		$page = $this->file_get_contents_utf8($furl);
		//echo htmlentities($page);
		//exit;
		$result = json_decode($page);
		if ($result->code != 200)
		{
			// return error
			return "Error: " . $result->message;
		}
		$current_key = -1;
		$output = "";
		//print_p($result);
		//foreach ($res_array as $os => $segment)
		if (!empty($result->text[0]))
		{
			$result->text[0] = explode("\n", $result->text[0]);
			foreach ($result->text[0] as $segment)
			{
				//echo "Seg: !$segment!<br>";
				if (!isset($segment)) continue;
				$translated = trim($segment);
				if (preg_match("/^(\d{1,4})/", $segment, $matches))
				{
					$current_key = $matches[1];	
				} else {
					if (substr($translated, strlen($translated) - 2, 2) == "\\n")
					$translated = substr($translated, 0, strlen($translated) - 2);
					$translated = str_replace(" .", ".", $translated);
					$translated = str_replace(" ,", ",", $translated);
					$translated = str_replace("\\\"", "\"", $translated);
					$translated = str_replace("\\n", "\n", $translated);
					// TESTING : Was getting odd errors with \uXXXX characters being included in the result
					$translated = preg_replace_callback(
						'/\\\\u([0-9a-zA-Z]{4})/',
						function ($matches) {
							return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
						},
						$translated
						);
					// TESTING : Was getting double escaped stuff in result, such as \ encoded as \\
					$translated = stripcslashes($translated);
					if (isset($results[$current_key]))
					{
						$results[$current_key] .= $translated;
					} else {
						$results[$current_key] = $translated;
					}
				}
			}
		}
		//print_p($results);
		// removed uppercasing of first letter as this breaks utf-8
		foreach ($phrases as $key => $text)
		{
			$first = substr($text, 0, 1);
			if ($first == strtoupper($first))
			{
				if (array_key_exists($key, $results))
				{
					$result = $results[$key];
					if (preg_match("/[a-z]+$/i", $result))
					{
						$result = strtoupper(substr($result, 0, 1)) . substr($result, 1);
						$results[$key] = $result;
					}
				}
			}
		}
		foreach ($results as $key => &$text)
		{
			if (!array_key_exists($key, $phrases)) continue;
			$orig = $phrases[$key];
			$letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
			for ($i = 0 ; $i < count($letters) ; $i++)
			{
				$letter = $letters[$i];
				$text = str_ireplace("XXXXXX" . $i . "XXXXXX", "%" . $letter, $text);
			}
			$text = str_ireplace("\u003c", "<", $text);
			$text = str_ireplace("\u003e", ">", $text);
			// need to de-space punctuation
			$punctuation = array('!', '(', ')', '"', '£', '$', '%', '^', '&', '*', '[', ']', '{', '}', ':', ';', '@', '#', '|', '<', '>', '=', "'", "/", '?');
			foreach ($punctuation as $p)
			{
				if (stripos($text, " ".$p) !== false && stripos($orig, " ".$p) === false)
				$text = str_ireplace(" ".$p, $p, $text);
				if (stripos($text, $p." ") !== false && stripos($orig, $p." ") === false)
				$text = str_ireplace($p." ", $p, $text);
			}
			$text = trim($text);
		}
		return true;
	}	
	function parseResult(&$text)
	{
		$result = array();
		$result_text = "";
		$count = 0;
		while (strlen($text) > 0 && $count < 1000)
		{
			$count++;
			$char = substr($text, 0, 1);
			$text = substr($text, 1);
			//echo "Found $char\n";
			if ($char == "[")
			{
				$result[] = $this->parseResult($text);
				$result_text = "";
			} else if ($char == "\"")
			{
				$type = 1;
				$next_pos = -1;
				while (1)
				{
					$next_pos = strpos($text, "\"", $next_pos+1);
					$prev = substr($text, $next_pos-1, 1);
					if ($prev != "\\")
						break;
					if ($next_pos < 1)
						break;
				}
				$entry = substr($text, 0, $next_pos);
				$text = substr($text, $next_pos+1);
				$result[] = $entry;
				$result_text = "";
			} else if ($char == ",")
			{
				if (strlen($result_text) > 0)
					$result[] = $result_text;
				$result_text = "";
				continue;
			} else if ($char == "]")
			{
				break;
			} else {
				$result_text .= $char;	
			}
		}	
		if (strlen($result_text) > 0)
			$result[] = $result_text;
		return $result;	
	}
}
/**/
