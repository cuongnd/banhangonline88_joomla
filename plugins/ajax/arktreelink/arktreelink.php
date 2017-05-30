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
 error_reporting(E_ERROR & ~E_NOTICE);
  
require_once 'helper.php';
require 'htmlentiesconverter.php';
require 'nodes.php'; 

class PlgAjaxArkTreeLink extends JPlugin
{

	public $app;
	
	public function onAjaxArkTreeLink()
	{
		
		$action  = $this->app->input->get('action','initialize');
		
		switch($action)
		{
			case 'links':
				$this->links();
			break;
			default:
				$this->initialize();
		}
			
	}

    private function initialize()
	{
		
		//now lets echo responese
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n";

		echo "<nodes>\n";

		$config = JFactory::getConfig();
		$config->set('live_site','');

		// Inialize array
		
		$contentIcon = JHtml::image('editors/arkeditor/content.png', '', null, true, true);	
		$menuIcon = JHtml::image('editors/arkeditor/menu.png', '', null, true, true);
		
		$extensions = array('content'=>array($contentIcon,$contentIcon) ,'menu'=>array($menuIcon,$menuIcon));

		ARkTreeLinkHelper::ListExtensions($extensions);

		foreach($extensions as $extension=>$icon)
		{
			$load = JURI::root().'index.php?option=com_ajax&amp;plugin=arktreelink&amp;format=json&amp;action=links&amp;extension='.$extension; 
			echo '<node text="' . ucfirst($extension).'" openicon="'.$icon[0].'" icon="'.$icon[1].'" load="'. $load . '"  selectable="false" url ="">' . "\n";
			echo "</node>\n";
		}

		echo "</nodes>";
		
		exit;
	}
	
	private function links()
	{
		
		//Get Node list for tree control
		$extension = $this->app->input->get('extension','content');

		if($extension == 'content')
		{
			$extFile =  JPATH_PLUGINS.'/ajax/arktreelink/contentnodes.php';
		}
		elseif($extension == 'menu')
		{
			$extFile =  JPATH_PLUGINS.'/ajax/arktreelink/menunodes.php';
		}
		else
		{
			$root = JPATH_PLUGINS.'/editors/arkeditor/ckeditor/plugins';
			$extFile = $root.'/'.$extension.'/links/'.$extension.'/nodes.php';
		}

		jimport('joomla.filesystem.file');
		
		if(!JFile::exists($extFile))
			throw new Exception('JLink extension '.$extension.' file could not be found!');
			
		require $extFile;	
			
		$classname = $extension.'LinkNodes';


		$linkNodeList = new $classname();
		$nodeList = $linkNodeList->getItems(); 

		//now lets echo responese
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n";
		echo "<nodes>\n";

		foreach ($nodeList as $node) 
		{
			$load = $linkNodeList->getLoadLink($node);
			echo '<node text="' . ArkHtmlEntitiesConverter::xmlEntities(htmlentities($node->name , ENT_QUOTES, "UTF-8",false)) .'"'.  
			($node->expandible ? ' openicon="_open" icon="_closed" load="'. $load . '"' : ($node->doc_icon  ? ' icon="_doc"'  :' icon="_closed"' )). 
			'  selectable="' . ($node->selectable?'true':'false') .'" url ="'.  $linkNodeList->getUrl($node) .'">' . "\n";
			echo "</node>\n";
		}

		echo "</nodes>";
		exit;	
	}
	
	
}
