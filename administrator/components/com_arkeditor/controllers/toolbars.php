<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKControllerToolbars extends ARKController
{
	protected $canDo = false;

	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->canDo = ARKHelper::getActions();

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'edit', 		'display' );
		$this->registerTask( 'add', 		'display' );
		$this->registerTask( 'trash', 		'remove' );	// drop-down menu
		$this->registerTask( 'remove', 		'remove' );
	}

	function display($cachable = false, $urlparams = false )
	{
		switch($this->getTask())
		{
			case 'add'     :
			case 'edit'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'form' );
				JRequest::setVar( 'view', 'toolbar' );
			}	break;
			case 'preview'	:
			{
				JRequest::setVar( 'view', 'toolbar' );
				JRequest::setVar( 'layout', 'popup' );
			} 
		}

		parent::display(false, $urlparams);
	}

	/**
	* Compiles information to add or edit a toolbar
	* @param string The current GET/POST option
	* @param integer The unique id of the record to edit
	*/
	function copy()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( JText::_( 'JINVALID_TOKEN' ) );

		if( !$this->canDo->get('core.create') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_arkeditor&view=toolbars', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_COPY' ), 'error' );
			return false;
		}

		// Initialize some variables
		$db 	= JFactory::getDBO();
		$app	= JFactory::getApplication();
		$cid	= $app->input->get( 'cid', array(), 'array' );
		$n		= count( $cid );

		if ($n == 0) {
			return ARKHelper::error( JText::_( 'JERROR_NO_ITEMS_SELECTED' ) );
		}

		$row =& ARKHelper::getTable('toolbar');
		$toolbarpugins	= array();
		
		$i = 1;	
		
		$ncid = array();

		foreach ($cid as $id)
		{
			// load the row from the db table
			$row->load( (int) $id );
			$row->title 		= 'Copy of ' . $row->title;
			$row->id 			= 0;
			$row->iscore 		= 0;
			$row->published 	= 1;
			$sql 				= $db->getQuery( true );
			$sql->select( 'COUNT(1)' )
				->from( '#__ark_editor_toolbars' )
				->where( 'title = '.$db->quote($row->title));

			//get offset for name of copy
			$offset		= $db->setQuery( $sql )->loadResult();
			$original = $row->name;
			$row->name 	= $row->name . ($offset +1);			
			
			if (!$row->check()) {
				return ARKHelper::error( $row->getError() );
			}
			if (!$row->store()) {
				return ARKHelper::error( $row->getError() );
			}

			$row->checkin();
			
		}
		
		$this->event_args = array('original'=> $original ,'copy' => $row->name);

		$msg = JText::sprintf( 'COM_ARKEDITOR_TOOLBAR_COPY', $n );
		$this->setRedirect( 'index.php?option=com_arkeditor&view=toolbars', $msg );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( JText::_( 'JINVALID_TOKEN' ) );

		if( !$this->canDo->get('core.edit') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_arkeditor&view=toolbars', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}
		
		$app = JFactory::getApplication();	

		$db 	= JFactory::getDBO();
		$row 	= ARKHelper::getTable('toolbar');
		$task 	= $this->getTask();
		$form	= $app->input->get( 'jform', array(), 'array' );
		$form['rows'] = $app->input->get( 'rows', array(), 'array' );
		$components = $app->input->get( 'components', array(), 'array' );
		$params = $app->input->get( 'params', array(), 'array' );
		$params['components'] = $components;
		$form['params'] = $params;

		$id = $form['id'];
		
		$oldname = '';
		$isNew = false;

		if(!$id)
		{
			$isNew = true;
			$name = $form['name'];
			$form['name'] = str_replace(array(' ','-'),array('','_'),$name);			
		}
		else
		{
			$row->load((int)$id);
			$oldname = $row->name;
		}
		
			if (!$row->bind($form)) {
			ARKHelper::error( $row->getError() );
		}
		
		$row->published = 1; //Always published for now. 
		
		if (!$row->check()) {
			ARKHelper::error( $row->getError() );
		}
		if (!$row->store()) {
			ARKHelper::error( $row->getError() );
		}
		$row->checkin();

		//code to add plugins from layout
		$rows  = JRequest::getVar( 'rows', array(), 'post');
		$rows = str_replace( ',/,,/,', ',/,', $rows );
        $rows = str_replace( ',;,;,', ',;,', $rows );
        
		$newRows = array();
		if($rows)
		{
			$rows = explode(';',$rows);
			foreach($rows as $record)
			{
				if(strpos($record,'/') !== false)
				{
					$items = preg_split('/(\/)/',$record,-1,PREG_SPLIT_DELIM_CAPTURE);
             
					foreach($items as $item)
					{
                        if($item != '/')
						{
							//$newRows[] = '/';
							$newItems = array_filter(explode(',',trim($item,',')));
							if($newItems)
								$newRows[] = $newItems;
						}		
						else
                        {
							$newRows[] = $item;
                        }
					}
				}
				else
					$newRows[] = explode(',',trim($record,','));
			}
		}
			
        if($newRows[count($newRows)-1] == '/')
        {
            array_pop($newRows);
        }

        //arguments for onSave Event
		$this->event_args = array('name'=>$row->name,'oldname'=>$oldname,'title'=>$row->title,'rows'=>$newRows,'isNew'=>$isNew);

		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'COM_ARKEDITOR_TOOLBAR_APPLY', $row->title );
				$this->setRedirect( 'index.php?option=com_arkeditor&task=toolbars.edit&cid[]='. $row->id, $msg );
				break;

			case 'save':
			default:
				$msg = JText::sprintf( 'COM_ARKEDITOR_TOOLBAR_SAVE', $row->title );
				$this->setRedirect( 'index.php?option=com_arkeditor&view=toolbars', $msg );
				break;
		}
	}	

	function cancel()
	{
	  // Check for request forgeries
		JRequest::checkToken() or die( JText::_( 'JINVALID_TOKEN' ) );

		$app 	= JFactory::getApplication();
		$row 	= ARKHelper::getTable('toolbar');
		$form 	= $app->input->get( 'jform', array(), 'array' );
		$row->bind($form);
		$row->checkin();
       	$this->setRedirect( 'index.php?option=com_arkeditor&view=toolbars');
	}
		
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( JText::_( 'JINVALID_TOKEN' ) );

		if( !$this->canDo->get('core.delete') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_arkeditor&view=toolbars', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}

		$db		= JFactory::getDBO();
		$app	= JFactory::getApplication();
		$cid  	= $app->input->get( 'cid', array(0), 'array' );
		JArrayHelper::toInteger($cid, array(0));

		if (count( $cid ) < 1) {
			ARKHelper::error( JText::_( 'JWARNING_DELETE_MUST_SELECT' ) );
		}

		if (empty( $cid )) {
			return ARKHelper::error( JText::_( 'JGLOBAL_NO_ITEM_SELECTED' ) );
		}

		$cids = implode( ',', $cid );
		
		$editor = JPluginHelper::getPlugin('editors','arkeditor');
		$params =  new JRegistry($editor->params);
		$defaults = array(strtolower($params->get('toolbar','back')),strtolower($params->get('toolbar_ft','front')) );
		$sql  = $db->getQuery( true );
		$sql->select( 'count(1)' )
			->from( '#__ark_editor_toolbars' )
			->where( 'id IN ('.$cids.')' )
			->where( 'LOWER(name)  IN (' . $db->quote( implode($db->quote(','),$defaults) ).')' );
		$total = $db->setQuery( $sql )->loadResult();
		if($msg = $db->getErrorMsg())
		{
			return ARKHelper::error( $msg);
		}
				
		if($total > 0){
			$this->setRedirect( 'index.php?option=com_arkeditor&view=toolbars');
			return ARKHelper::error( JText::_( 'COM_ARKEDITOR_TOOLBAR_NO_DEL_DEFAULT' ) );
		}

				
		$sql  = $db->getQuery( true );
		$sql->select( 'count(1)' )
			->from( '#__ark_editor_toolbars' )
			->where( 'id IN ('.$cids.')' )
			->where( 'iscore = 1' );
		$total = $db->setQuery( $sql )->loadResult();
		if($msg = $db->getErrorMsg())
		{
			return ARKHelper::error( $msg);
		}

		if($total > 0){
			$this->setRedirect( 'index.php?option=com_arkeditor&view=toolbars');
			return ARKHelper::error( JText::_( 'COM_ARKEDITOR_TOOLBAR_NO_DEL_CORE' ) );
		}

		$sql  = $db->getQuery( true );
		$sql->select( 'name' )
			->from( '#__ark_editor_toolbars' )
			->where( 'id IN ('.$cids.')' );
		$rows = $db->setQuery( $sql )->loadColumn();

		if (!$db->query()) {
			return ARKHelper::error( $db->getErrorMsg() );
		}

		$this->event_args = array('names' => $rows);	


		//delete toolbars
		$sql  = $db->getQuery( true );
		$sql->delete( '#__ark_editor_toolbars' )
			->where( 'id IN ('.$cids.')' );
		$db->setQuery( $sql );
		if (!$db->query()) {
			ARKHelper::error( $db->getErrorMsg() );
		}

		$msg = JText::sprintf( 'COM_ARKEDITOR_TOOLBAR_DELETE', implode(',',$rows) );
		$this->setRedirect( 'index.php?option=com_arkeditor&view=toolbars',$msg );
	}

	
}