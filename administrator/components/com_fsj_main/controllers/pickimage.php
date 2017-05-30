<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.fields.pickimage');

class fsj_mainControllerPickImage extends JControllerLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	function display($cachable = false, $urlparams = Array())
	{
		$pi = new FSJ_PickImage();
		$pi->PickImage();
	}
}
