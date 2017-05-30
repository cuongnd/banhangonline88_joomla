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

class plgAcymailingUrltracker extends JPlugin
{

	function plgAcymailingUrltracker(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'urltracker');
			$this->params = new JParameter( $plugin->params );
		}
	}

	function acymailing_replaceusertags(&$email,&$user,$send=true){

		if(!$email->sendHTML OR empty($user->subid) OR empty($email->type) OR !in_array($email->type,array('news','autonews','followup','welcome','unsub')) OR !acymailing_level(1)) return;

		$urlClass = acymailing_get('class.url');
		if($urlClass === null) return;
		$urls = array();

		if(!preg_match_all('#href[ ]*=[ ]*"(?!mailto:|\#|ymsgr:|callto:|file:|ftp:|webcal:|skype:)([^"]+)"#Ui',$email->body,$results)) return;

		foreach($results[1] as $i => $url){
			if(isset($urls[$results[0][$i]]) || strpos($url,'task=out')) continue;

			$isFile = false;
			$extension = strtolower(substr($url,strrpos($url,'.')+1));
			if(in_array($extension,array('zip','png','gif','jpeg','jpg','doc','pdf','gz','docx','xls','xlsx'))){
				$isFile = true;
			}
			if(strpos($url,'?')){
				$isFile = false;
			}

			$config = acymailing_config();
			$trackingSystem = $config->get('trackingsystem', 'acymailing');

			if(strpos($url,'utm_source') === false && !$isFile && strpos($trackingSystem, 'google') !== false){
				$args = array();
				$args[] = 'utm_source=newsletter_'.@$email->mailid;
				$args[] = 'utm_medium=email';
				$args[] = 'utm_campaign='.@$email->alias;
				$anchor = '';
				if(strpos($url,'#') !== false){
					$anchor = substr($url,strpos($url,'#'));
					$url = substr($url,0,strpos($url,'#'));
				}

				if(strpos($url,'?')){ $mytracker = $url.'&'.implode('&',$args); }
				else{ $mytracker = $url.'?'.implode('&',$args); }
				$mytracker .= $anchor;
				$urls[$results[0][$i]] = str_replace($results[1][$i],$mytracker,$results[0][$i]);
				$url = $mytracker;
			}

			if(strpos($trackingSystem, 'acymailing')!== false){
				if(strpos($url,ACYMAILING_LIVE) === false || $isFile || strpos($url,'#') !== false){
					if(preg_match('#subid|passw|modify|\{|%7B#i',$url)) continue;
					$mytracker = $urlClass->getUrl($url,$email->mailid,$user->subid);
				}else{
					$extraParam = 'acm='.$user->subid.'_'.$email->mailid;
					if(strpos($url,'#')){
						$before = substr($url,0,strpos($url,'#'));
						$after = substr($url,strpos($url,'#'));
					}else{
						$before = $url;
						$after = '';
					}
					$mytracker = $before.(strpos($before,'?') ? '&':'?').$extraParam.$after;
				}

				if(empty($mytracker)) continue;
				$urls[$results[0][$i]] = str_replace($results[1][$i],$mytracker,$results[0][$i]);
			}
		}

		$email->body = str_replace(array_keys($urls),$urls,$email->body);

	}//endfct

	function onAcyDisplayTriggers(&$triggers){
		$triggers['clickurl'] = JText::_('ON_USER_CLICK');
	}

	 function onAcyDisplayFilters(&$type,$context="massactions"){

		if($this->params->get('displayfilter_'.$context,true) == false) return;

	 	$db = JFactory::getDBO();
		$db->setQuery("SELECT `mailid`,CONCAT(`subject`,' ( ',`mailid`,' )') as 'value' FROM `#__acymailing_mail` WHERE `type` IN('news','autonews','followup') ORDER BY `subject` ASC ");
		$allemails = $db->loadObjectList();
		if(empty($allemails)) return;
		$element = new stdClass();
		$element->mailid = 0;
		$element->value = JText::_('EMAIL_NAME');
		array_unshift($allemails,$element);

		$type['clickstats'] = JText::_('CLICK_STATISTICS');

		$jsOnChange = 'if(document.getElementById(\'filter__num__clickstats_urlid\')){ document.getElementById(\'filter__num__clickstats_urlid\').value=\'all\';}';
		$jsOnChange .= 'displayCondFilter(\'changeList\', \'toChange__num__\',__num__,\'mailid=\'+document.getElementById(\'filter__num__clickstats_mailid\').value);';

		$return = '<div id="filter__num__clickstats">'.JHTML::_('select.genericlist',  $allemails, "filter[__num__][clickstats][mailid]", 'onchange="'.$jsOnChange.'" class="inputbox" size="1" style="max-width:200px"', 'mailid', 'value', null, 'filter__num__clickstats_mailid');
		$return .= '<span id="toChange__num__">' . JText::_('LINK_ID') . ' : <input type="text" name="filter[__num__][clickstats][urlid]" value="0" readonly="readonly"/></span></div>';

	 	return $return;
	 }

	 function onAcyTriggerFct_changeList(){
		$db = JFactory::getDBO();
		$mailid = JRequest::getVar('mailid');
		$num = JRequest::getInt('num');
		if($mailid == 0){
			$queryUrl = "SELECT urlid, CONCAT(name, ' ( ',urlid,' )') AS 'name' FROM #__acymailing_url WHERE SUBSTRING(`name`,1,230) != SUBSTRING(`url`,1,230) ORDER BY name ASC";
		} else{
			$queryUrl = "SELECT u.urlid, CONCAT(u.name, ' ( ',u.urlid,' )') AS 'name' FROM #__acymailing_url AS u LEFT JOIN #__acymailing_urlclick AS uc ON u.urlid=uc.urlid WHERE uc.mailid=".intval($mailid)." GROUP BY u.urlid ORDER BY u.name ASC";
		}
		$db->setQuery($queryUrl);
	 	$allurls = $db->loadObjectList();

		$element = new stdClass();
		$element->urlid = 'all';
		$element->name = JText::_('ALL_URLS');
		array_unshift($allurls,$element);

	 	return JText::_('CLICKED_LINK').' : '.JHTML::_('select.genericlist',  $allurls, "filter[".$num."][clickstats][urlid]", 'onchange="countresults('.$num.')" class="inputbox" size="1" style="width:150px;"', 'urlid', 'name', null, 'filter'.$num.'clickstats_urlid');
	 }

	 function onAcyProcessFilterCount_clickstats(&$query,$filter,$num){
		$this->onAcyProcessFilter_clickstats($query,$filter,$num);
		return JText::sprintf('SELECTED_USERS',$query->count());
	}

	function onAcyProcessFilter_clickstats(&$query,$filter,$num){
		$alias = 'url'.$num;
		$query->join[$alias] = '#__acymailing_urlclick as '.$alias.' on sub.subid = '.$alias.'.subid';
		if(intval($filter['mailid']) != 0) $query->where[] = $alias.'.mailid = '.intval($filter['mailid']);
		if($filter['urlid'] != 'all' && intval($filter['urlid']) != 0) $query->where[] = $alias.'.urlid = '.intval($filter['urlid']);
	}

}//endclass
