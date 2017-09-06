<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

class ARKTableLanguage extends JTable
{
	/**
	 * Primary Key
	 *
	 *  @var int
	 */
	var $id = null;

	/**
	 * @var varchar
	 */
	var $tag = null;

	/**
	 * @var varchar
	 */
	var $filename = null;


	function __construct(& $db) {
		parent::__construct('#__ark_editor_languages', 'id', $db);
	}

}