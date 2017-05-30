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

class HelpdeskTemplate
{
	static function GetFile($file)
	{
		$workgroupSettings = HelpdeskDepartment::GetSettings();
		$tmplfile = JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . (isset($workgroupSettings->theme) ? $workgroupSettings->theme . '/' : '') . $file . '.php';

		if(!is_file($tmplfile)) {
			$tmplfile = JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/' . $file . '.php';
		}

		return $tmplfile;
	}

	static function Parse($var_set, $tmplfile, $message = '', $tpltype = 'mail')
	{
		$id_workgroup = JRequest::getInt('id_workgroup', 0);

		// Initialize Template Engine
		if ($tmplfile) {
			$message = HelpdeskTemplate::Get($message, $id_workgroup, $tpltype . '/' . $tmplfile);
		}

		foreach ($var_set as $var => $var_value) {
			$message = str_replace($var, $var_value, $message); // Replaces the each variable
		}

		return $message;
	}

	static function Get($msg, $id_workgroup, $tmplfile)
	{
		$workgroupSettings = HelpdeskDepartment::GetSettings();

		// Anonymous access
		if (!isset($workgroupSettings->theme))
		{
			$workgroupSettings = new stdClass();
			$workgroupSettings->theme = 'default';
		}

		// Get the template from the file
		$file = JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/' . $tmplfile . '.php';
		if(!is_file($file)) {
			$file = JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/' . $tmplfile . '.php';
		}
		if (is_file($file)) {
			ob_start();
			include($file);
			$tmpl_code = ob_get_contents();
			ob_end_clean();
		} else {
			$tmpl_code = '<small>' . JText::_('notemplatefile') . $file . '</small>';
		}

		return $tmpl_code;
	}
}
