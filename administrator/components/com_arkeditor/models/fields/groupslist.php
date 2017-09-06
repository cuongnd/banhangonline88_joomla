<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldGroupsList extends JFormFieldList 
{
	protected $type = 'GroupsList';

	protected function getOptions()
	{
		$options 	= array();
		$items 		= array();
		$groups 	= array();
		$model 		= JModelLegacy::getInstance( 'editplugin', 'ARKModel' );
		$list 		= $model->getUserGroupList();

		// Build the field options.
		if(!empty($list))
		{
			foreach( $list as $group )
			{
				$options[] = JHtml::_( 'select.option', $group->value, $group->text );
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}	