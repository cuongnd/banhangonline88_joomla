<?php
/**
 * @package         Modals
 * @version         8.0.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_LIBRARIES . '/regularlabs/helpers/string.php';

class PlgSystemModalsHelperFile
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		$this->params->mediafiles  = RLText::createArray(strtolower($this->params->mediafiles));
		$this->params->iframefiles = RLText::createArray(strtolower($this->params->iframefiles));
	}

	public function isExternal($url)
	{
		if (strpos($url, '://') === false)
		{
			return 0;
		}

		// hostname: give preference to SERVER_NAME, because this includes subdomains
		$hostname = ($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];

		return !(strpos(preg_replace('#^.*?://#', '', $url), $hostname) === 0);
	}

	public function isMedia($url, $filetypes = array(), $ignore = 0)
	{
		$filetype = $this->getFiletype($url);
		if (!$filetype)
		{
			return 0;
		}
		if (empty($filetypes))
		{
			$filetypes = $this->params->mediafiles;
			$ignore    = 0;
		}

		$pass = in_array($filetype, $filetypes);

		return $ignore ? !$pass : $pass;
	}

	public function isIframe($url, &$data)
	{
		if (!empty($data['inline']))
		{
			return false;
		}

		if ($this->isMedia($url, $this->params->iframefiles))
		{
			return true;
		}

		if ($this->isMedia($url))
		{
			unset($data['iframe']);

			return false;
		}

		if (empty($data['iframe']))
		{
			return $this->params->iframe;
		}

		return ($data['iframe'] !== 0 && $data['iframe'] !== 'false');
	}

	public function retinaImageExists($url)
	{
		$retina_file = preg_replace('#\.([a-z0-9]+)$#i', $this->params->retinasuffix, $url);

		return is_file(JPATH_SITE . '/' . $retina_file);
	}

	public function getFiletype($url)
	{
		$info = pathinfo($url);
		if (!isset($info['extension']))
		{
			return '';
		}

		$ext = explode('?', $info['extension']);

		return strtolower($ext['0']);
	}

	public function getFileName($url)
	{
		return basename($url);
	}

	public function getFileTitle($url)
	{
		$info = pathinfo($url);

		return isset($info['filename']) ? $info['filename'] : '';
	}

	public function getFilePath($url)
	{
		return dirname($url) . '/';
	}

	public function getTitle($url, $case)
	{
		$file_name = basename($url);

		$data = $this->helpers->get('image')->getImageDataFromDataFile(dirname($url));
		$this->helpers->get('image')->setImageDataAtribute('title', $data, $file_name, 0, true);

		if (isset($data['title']))
		{
			return $data['title'];
		}

		$title = explode('.', $file_name);
		$title = $title['0'];
		$title = preg_replace('#[_-]([0-9]+|[a-z])$#i', '', $title);
		$title = str_replace(array('-', '_'), ' ', $title);

		switch ($case)
		{
			case 'lowercase':
				$title = RLString::strtolower($title);
				break;
			case 'uppercase':
				$title = RLString::strtoupper($title);
				break;
			case 'uppercasefirst':
				$title = RLString::strtoupper(RLString::substr($title, 0, 1))
					. RLString::strtolower(RLString::substr($title, 1));
				break;
			case 'titlecase':
				$title = function_exists('mb_convert_case')
					? mb_convert_case(RLString::strtolower($title), MB_CASE_TITLE)
					: ucwords(strtolower($title));
				break;
			case 'titlecase_smart':
				$title           = function_exists('mb_convert_case')
					? mb_convert_case(RLString::strtolower($title), MB_CASE_TITLE)
					: ucwords(strtolower($title));
				$lowercase_words = explode(',', ' ' . str_replace(',', ' , ', RLString::strtolower($this->params->lowercase_words)) . ' ');
				$title           = str_ireplace($lowercase_words, $lowercase_words, $title);
				break;
		}

		return $title;
	}

	public function trimFolder($folder)
	{
		return trim(str_replace(array('\\', '//'), '/', $folder), '/');
	}
}
