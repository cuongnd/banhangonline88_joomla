<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
class PlgAjaxInlineModeStateListener extends JPlugin
{

	public function onAjaxInlineModeStateListener()
	{
        //Set the state for the editor ;-)
		JFactory::getApplication()->setUserState('com_arkeditor.autoDisableInline', JFactory::getApplication()->input->get('state') );
	}
}
