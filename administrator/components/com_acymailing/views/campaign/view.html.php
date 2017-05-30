<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php


class CampaignViewCampaign extends acymailingView
{
	function display($tpl = null)
	{
		$function = $this->getLayout();
		if(method_exists($this,$function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){
		$app = JFactory::getApplication();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();
		$config = acymailing_config();


		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $paramBase.".filter_order", 'filter_order',	'a.listid','cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $paramBase.".filter_order_Dir", 'filter_order_Dir',	'desc',	'word' );
		$pageInfo->search = $app->getUserStateFromRequest( $paramBase.".search", 'search', '', 'string' );
		$pageInfo->search = JString::strtolower( $pageInfo->search );
		$selectedCreator = $app->getUserStateFromRequest( $paramBase."filter_creator",'filter_creator',0,'int');

		$pageInfo->limit->value = $app->getUserStateFromRequest( $paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$pageInfo->limit->start = $app->getUserStateFromRequest( $paramBase.'.limitstart', 'limitstart', 0, 'int' );

		$database	= JFactory::getDBO();

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search).'%\'';
			$filters[] = "a.name LIKE $searchVal OR a.description LIKE $searchVal OR a.listid LIKE $searchVal";
		}
		$filters[] = 'a.type = \'campaign\'';
		if(!empty($selectedCreator)) $filters[] = 'a.userid = '.$selectedCreator;

		$query = 'SELECT a.*, d.name as creatorname, d.username, d.email';
		$query .= ' FROM '.acymailing_table('list').' as a';
		$query .= ' LEFT JOIN '.acymailing_table('users',false).' as d on a.userid = d.id';
		$query .= ' WHERE ('.implode(') AND (',$filters).') ';
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$database->setQuery($query,$pageInfo->limit->start,$pageInfo->limit->value);
		$rows = $database->loadObjectList();

		$queryCount = 'SELECT COUNT(a.listid) FROM '.acymailing_table('list').' as a';
		if(count($filters) > 1) $queryCount .= ' LEFT JOIN '.acymailing_table('users',false).' as d on a.userid = d.id';
		$queryCount .= ' WHERE ('.implode(') AND (',$filters).') ';

		$database->setQuery($queryCount);
		$pageInfo->elements->total = $database->loadResult();

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search,$rows);
		}

		$pageInfo->elements->page = count($rows);

		$followupClass = acymailing_get('class.listmail');
		if(!empty($rows)){
			foreach($rows as $id => $onerow){
				$rows[$id]->followup = $followupClass->getFollowup($onerow->listid);
			}
		}


		jimport('joomla.html.pagination');
		$pagination = new JPagination( $pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value );

		acymailing_setTitle(JText::_('CAMPAIGN'),'campaign','campaign');

		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		if(acymailing_isAllowed($config->get('acl_campaign_delete','all'))) JToolBarHelper::deleteList(JText::_('ACY_VALIDDELETEITEMS',true));
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy.png', JText::_('ACY_COPY') );
		JToolBarHelper::divider();
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Pophelp','campaign');
		if(acymailing_isAllowed($config->get('acl_cpanel_manage','all'))) $bar->appendButton( 'Link', 'acymailing', JText::_('ACY_CPANEL'), acymailing_completeLink('dashboard') );

		$toggleClass  = acymailing_get('helper.toggle');
		$this->assignRef('rows',$rows);
		$this->assignRef('pageInfo',$pageInfo);
		$this->assignRef('pagination',$pagination);
		$this->assignRef('toggleClass',$toggleClass);
		$delay = acymailing_get('type.delaydisp');
		$this->assignRef('delay',$delay);

		$toggleClass->toggleText();
	}

	function form(){
		$listid = acymailing_getCID('listid');

		$listClass = acymailing_get('class.list');
		if(!empty($listid)){
			$list = $listClass->get($listid);
			$followupClass = acymailing_get('class.listmail');
			$followup = $followupClass->getFollowup($listid);
		}else{
			$list = new stdClass();
			$list->published = 1;
			$list->visible = 0;
			$list->description = '';
			$user = JFactory::getUser();
			$list->creatorname = $user->name;
			$list->listid = 0;
			$list->startrule = 0;
			$followup = array();
		}

		$editor = acymailing_get('helper.editor');
		$editor->name = 'editor_description';
		$editor->content = $list->description;
		$editor->setDescription();

		$listCampaign = acymailing_get('class.listcampaign');
		$lists = $listCampaign->getLists($listid);

		if(!ACYMAILING_J16){
			$script = 'function submitbutton(pressbutton){
						if (pressbutton == \'cancel\') {
							submitform( pressbutton );
							return;
						}';
		}else{
			$script = 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton == \'cancel\') {
							Joomla.submitform(pressbutton,document.adminForm);
							return;
						}';
		}
		$script .= 'if(window.document.getElementById("name").value.length < 1){alert(\''.JText::_('ENTER_TITLE',true).'\'); return false;}';
		$script .= $editor->jsCode();
		if(!ACYMAILING_J16){
			$script .= 'submitform( pressbutton );} ';
		}else{$script .= 'Joomla.submitform(pressbutton,document.adminForm);}; '; }

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration( $script );

		acymailing_setTitle(JText::_('CAMPAIGN'),'campaign','campaign&task=edit&listid='.$listid);

		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::divider();
		$bar->appendButton( 'Pophelp','campaign');

		$startoptions = array();
		$startoptions[] = JHTML::_('select.option', "0",JText::_('START_ON_SUBSCRIBE'));
		$days = array(JText::_('MONDAY'), JText::_('TUESDAY'), JText::_('WEDNESDAY'), JText::_('THURSDAY'), JText::_('FRIDAY'), JText::_('SATURDAY'), JText::_('SUNDAY'));
		foreach($days as $i => $oneDay){
			$startoptions[] = JHTML::_('select.option', $i+1,JText::sprintf('START_ON_DAY',$oneDay));
		}

		$toggleClass = acymailing_get('helper.toggle');
		$this->assignRef('startoptions',$startoptions);
		$this->assignRef('toggleClass',$toggleClass);
		$this->assignRef('followup',$followup);
		$this->assignRef('lists',$lists);
		$this->assignRef('list',$list);
		$this->assignRef('editor',$editor);

	}
}
