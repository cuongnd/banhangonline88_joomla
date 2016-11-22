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

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemModalsInstallerScript extends PlgSystemModalsInstallerScriptHelper
{
	public $name           = 'MODALS';
	public $alias          = 'modals';
	public $extension_type = 'plugin';

	public function onAfterInstall()
	{
		// Copy modal.php to system template folder
		JFile::copy(__DIR__ . '/modal.php', JPATH_SITE . '/templates/system/modal.php');
	}
}
