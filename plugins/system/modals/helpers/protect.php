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

class PlgSystemModalsHelperProtect
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		$this->params->protected_tags = array(
			$this->params->tag_character_start . $this->params->tag,
			$this->params->tag_character_start . $this->params->tag_content
		);
	}

	public function protect(&$string)
	{
		RLProtect::protectFields($string);
		RLProtect::protectSourcerer($string);
	}

	public function protectTags(&$string)
	{
		RLProtect::protectTags($string, $this->params->protected_tags);
	}

	public function unprotectTags(&$string)
	{
		RLProtect::unprotectTags($string, $this->params->protected_tags);
	}
}
