<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
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

jimport('joomla.filesystem.folder');

class PlgSystemArkMedia extends JPlugin
{

	public $app;
	
	private $allowed_views = array ('images','imagesList');


	public function onAfterRoute()
	{
			       
        //if not media manager bail out
		if ($this->app->input->get('option','') != 'com_media' )
		{
			return;
		}

		//Fix for third party templates failing to style the Media Manager
        if($this->app->isSite())
		    JFactory::getDocument()->addStyleDeclaration( '@font-face {font-family: \'IcoMoon\';src: url(\'media/jui/fonts/IcoMoon.eot\');src: url(\'media/jui/fonts/IcoMoon.eot?#iefix\') format(\'embedded-opentype\'), url(\'media/jui/fonts/IcoMoon.woff\') format(\'woff\'), url(\'media/jui/fonts/IcoMoon.ttf\') format(\'truetype\'), url(\'media/jui/fonts/IcoMoon.svg#IcoMoon\') format(\'svg\');font-weight: normal; font-style: normal;} [class^="icon-"]:before,[class*=" icon-"]:before { font-family: \'IcoMoon\'; font-style: normal; speak: none; } .icon-folder-2:before {content: "\2e";} ul.manager .height-50 .icon-folder-2 { height: 35px; width: 35px; line-height: 35px; font-size: 30px;}' );

		$task = $this->app->input->get('task','');

		if($task == 'file.upload')
		{
			$url = $this->app->input->server->get('HTTP_REFERER','','string');
			
			if($url) 
			{
				$uri = JFactory::getURI($url);		
				if($uri->getVar('arkimage',false))
				{
					$return = JFactory::getSession()->get('com_media.return_url');
					if($return)
					{
						$return .= '&arkimage=1';
						JFactory::getSession()->set('com_media.return_url',$return);
					}

					$config = JComponentHelper::getParams('com_media');
					$plugin = JPluginHelper::getPlugin('editors', 'arkeditor');
					$params = JComponentHelper::getParams('com_arkeditor');
					$editor_params = new JRegistry($plugin->params);
					$params->merge($editor_params);
					$path = $params->get('imagePath','images');
					$config->set('image_path', $path);
				}
				elseif($uri->getVar('arkmedia',false))
				{
					$config = JComponentHelper::getParams('com_media');
					$plugin = JPluginHelper::getPlugin('editors', 'arkeditor');
					$params = JComponentHelper::getParams('com_arkeditor');
					$editor_params = new JRegistry($plugin->params);
					$params->merge($editor_params);
					$path = $params->get('filePath','files');
					$config->set('image_path', $path);
				}	
			}		
			return;
		}
		
		$viewName = $this->app->input->get('view','');

		
		if(!$viewName || ($viewName && !in_array($viewName,$this->allowed_views) ))    
		{
			return;
		}
			
        $url = $this->app->input->server->get('HTTP_REFERER','','string');
		
        if($url)
        {	
			$uri = JFactory::getURI($url);	

           if(!JFactory::getSession()->get('com_media.return_url') && $uri->getVar('arkimage',false)
            && !($this->app->input->get('fieldid',false) === false) && !$this->app->input->get('redirect',false))
           {
              $redirectUrl = 'index.php?option=com_media&view=images&tmpl=component&arkimage=1&redirect=1&fieldid=' . $this->app->input->getCmd('fieldid', '') . '&e_name=' . $this->app->input->getCmd('e_name') . '&asset=' . $this->app->input->getCmd('asset') . '&author=' . $this->app->input->getCmd('author'); 
              $this->app->redirect($redirectUrl);
                
              return;
           }
		   
		    if(!JFactory::getSession()->get('com_media.return_url') && $uri->getVar('arkmedia',false)
            && !($this->app->input->get('fieldid',false) === false) && !$this->app->input->get('redirect',false))
           {
              $redirectUrl = 'index.php?option=com_media&view=images&tmpl=component&arkmedia=1&redirect=1&fieldid=' . $this->app->input->getCmd('fieldid', '') . '&e_name=' . $this->app->input->getCmd('e_name') . '&asset=' . $this->app->input->getCmd('asset') . '&author=' . $this->app->input->getCmd('author'); 
              $this->app->redirect($redirectUrl);
                
              return;
           }
   
		 } 
             
        if(in_array($viewName, array('images','media','mediaList')))
           $this->app->setUserState('com_media.arkimage',false);

              	
		if ($this->app->input->get('arkimage',false) ||  $this->app->getUserState('com_media.arkimage',false))
		{
			$config = JComponentHelper::getParams('com_media');
			$plugin = JPluginHelper::getPlugin('editors', 'arkeditor');
			$params = JComponentHelper::getParams('com_arkeditor');
			$editor_params = new JRegistry($plugin->params);
			$params->merge($editor_params);
 			$path = $params->get('imagePath','images');
			$config->set('image_path', $path);

            //test to see if root image path has been created
            $fullPath = JPATH_SITE.'/'.$path;
			
		    if(!JFolder::exists($fullPath))
		    {
                if(!JFolder::create($fullPath))
				    throw new Exception('Could not create root path "'.'$path'.'" folder');
		    }

            $this->app->setUserState('com_media.arkimage',true);

			return; 
		}
		
		if(in_array($viewName, array('images','media','mediaList')))
           $this->app->setUserState('com_media.arkmedia',false);

		
		if (!$this->app->input->get('arkmedia',false) 
			&& !$uri->getVar('arkmedia',false)
			&& !$this->app->getUserState('com_media.arkmedia',false))
		{
			return;
		}
		
		$config = JComponentHelper::getParams('com_media');
		$plugin = JPluginHelper::getPlugin('editors', 'arkeditor');
		$params = JComponentHelper::getParams('com_arkeditor');
		$editor_params = new JRegistry($plugin->params);
		$params->merge($editor_params);
		$path = $params->get('filePath','files');
		$config->set('image_path', $path);

        //test to see if root files path has been created
        $fullPath = JPATH_SITE.'/'.$path;
		
	
		if(!JFolder::exists($fullPath))
		{
			if(!JFolder::create($fullPath))
				throw new Exception('Could not create root files path "'.$path.'" folder');
		}
		
		$this->app->setUserState('com_media.arkmedia',true);
		
		if($task)
		{
			require JPATH_PLUGINS.'/system/arkmedia/helpers/media.php';
			return;
		}		
		        
        $user = JFactory::getUser();
                
        //if user is guest lets bail
		if($user->get('guest'))
		{
			return;
		}
		
				
		$component_path = JPATH_SITE.'/administrator/components/com_media';
		
	
		
		require JPATH_PLUGINS.'/system/arkmedia/media.php';
		
       	$controller = JControllerLegacy::getInstance('Media',array('base_path'=>$component_path));
		
	
		if($viewName == 'images')
			$view = $controller->getView('images', 'html','',array('base_path'=>$component_path,'template_path'=>JPATH_PLUGINS.'/system/arkmedia/html/images'));
		else
			$view = $controller->getView('imagesList', 'html','',array('base_path'=>$component_path,'template_path'=>JPATH_PLUGINS.'/system/arkmedia/html/imageslist'));
			
		$this->app->input->set('layout','default'); //make sure layout is set too default
		
	}
}