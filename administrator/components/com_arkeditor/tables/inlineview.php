<?php
/*------------------------------------------------------------------------
# Copyright (C) 2015-2016 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

class ARKTableInlineView extends JTable
{
	function __construct(& $db) {
		parent::__construct('#__ark_editor_inline_views', array('element','context'), $db);
	}
	
    function check()
    {
        
        if(is_array($this->views))
        {
            $this->views = json_encode($this->views);
        }

        if(is_array($this->types))
        {
            $this->types = json_encode($this->types);
        }

        return  true;
   }


  /**
	* Overloaded bind function
	*
	* @access public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	function bind($array, $ignore = '')
	{
		if (isset( $array['params'] ) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

}
?>