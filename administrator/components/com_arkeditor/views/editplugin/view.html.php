<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKViewEditPlugin extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $user;
	protected $form;
	protected $item;
	protected $state;
	protected $params;

	function display( $tpl = null )
	{
		$this->canDo		= ARKHelper::getActions();
		$this->app			= JFactory::getApplication();
		$this->user			= JFactory::getUser();
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->params		= $this->prepareForm($this->item);

		if(!$this->canDo->get('core.edit'))
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=list', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_EDIT' ), 'error' );
			return false;
		}//end if

		//language
		$lang = ARKHelper::getLanguage();
		$tag = $lang->getTag();
		JFactory::$language = $lang; //override Joomla default language class 
		
		$name = $this->item->name;
        
        
		$plugin = 'plg_arkeditor'.$name;

		$pluginOverideFile = JPATH_COMPONENT.'/language/overrides/'.$tag.'.'.$plugin.'.ini';
		$pluginLangFile = JPATH_COMPONENT.'/language/'.$tag.'/'.$tag.'.'.$plugin. '.ini';
		
		if(JFile::exists($pluginOverideFile)) //check in language overrides to see if user has installed an override language file
			$lang->loadFile($pluginOverideFile, $plugin);
		else if(JFile::exists($pluginLangFile))	//load core language file if it exists
			$lang->load($plugin, JPATH_COMPONENT);
		else
		{	
			//load english default languge
			if(JFile::exists( JPATH_COMPONENT.'/language/en-GB/en-GB.plg_arkeditor'. $name.'.ini')) //This should exist!
			{
				$lang->load($plugin, JPATH_COMPONENT,'en-GB');
			}
		}
		$this->item->description = JText::_($this->item->description);


		$this->form->bind($this->item);

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			ARKHelper::error( implode("\n", $errors));
			return false;
		}

		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$this->app->input->set('hidemainmenu', true);

		$bar 	= JToolBar::getInstance('toolbar');
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));

		JToolBarHelper::title( JText::_( 'COM_ARKEDITOR_SUBMENU_PLUGIN_NAME' ) .':' . chr( 32 ) . JText::_($this->item->name), 'puzzle' );

		if( $this->canDo->get('core.create') && !$checkedOut )
		{
			JToolBarHelper::apply( 'list.apply' );
			JToolBarHelper::save( 'list.save' );
		}//end if

    	JToolBarHelper::cancel( 'list.cancel', 'JTOOLBAR_CLOSE' );
    	//JToolBarHelper::help( $this->app->input->get( 'view' ), true );

		ARKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function

	function prepareForm(&$item)
	{
        if($item->iscore)
           @$data = file_get_contents( JPATH_COMPONENT.DS.'editor'.DS.'plugins'.DS.$item->name.'.xml' );
        else
           @$data = file_get_contents( JPATH_PLUGINS.DS.'arkeditor'.DS.$item->name.DS.$item->name.'.xml' );

		if($data )
		{
			$data = preg_replace( array('/\<params group="options">/i','/\<params>/i','/\<params(.*)\<\/params\>/is'), array('<params name="advanced">','<params name="basic">','<config><fields name="params"><fieldset$1</fieldset></fields></config>'), $data );
			$data = str_replace( array( '<install', '</install', '<params', '</params', '<param', '</param' ), array( '<form', '</form', '<fieldset','</fieldset', '<field', '</field' ), $data );

			// Re-style fields to J3.0
			// Can't just str_replace because fields might already have a class
			$xml 	= ARKHelper::getXML( $data, false );
			$nodes 	= $xml->xpath( '//field[@type="radio" or @type="resizeradio"]' );
			
			foreach( $nodes as $node )
			{
				$radio = 'btn-group';
				$class = ( (string)$node->attributes()->class ) ? (string)$node->attributes()->class . chr( 32 ) . $radio : $radio;
			
				if( $node->attributes()->class )
				{
					$node->attributes()->class = $class;
				}
				else
				{
					$node->addAttribute( 'class', $class );
				}
			}
			
			$data = $xml->asXML();
		} else
		{
			$data = '<install><form>dummy data</form></install>';
		}//end if

		ARKForm::addFieldPath(JPATH_COMPONENT . DS . 'models' . DS . 'fields');
		$form = ARKForm::getInstance( 'com_arkeditor.plugin', $data,array(),true,'//config'); 

		//load plugins language file
		$lang		= JFactory::getLanguage();
		$lang->load('com_plugins', JPATH_ADMINISTRATOR, null, false, false);

		JPluginHelper::importPlugin('content');

		$dispatcher	= JDispatcher::getInstance();

		// Trigger the form preparation event.
		$jpara	= new JRegistry( $item->params );
		$data = $jpara->toArray();
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		$form->bind($data);

		return $form;


	}
}