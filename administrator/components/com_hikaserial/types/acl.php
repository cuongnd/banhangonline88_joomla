<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikaserialAclType {

	protected $groups = null;

	protected function load() {
		$this->groups = array();
		$db = JFactory::getDBO();
		if(version_compare(JVERSION,'1.6.0','<')) {

			$db->setQuery('SELECT a.* FROM `#__core_acl_aro_groups` AS a WHERE a.value = \'USERS\'');
			$userRoot = $db->loadObject();

			$db->setQuery('SELECT a.* FROM `#__core_acl_aro_groups` AS a WHERE a.lft > ' . (int)$userRoot->lft . ' AND a.lft < ' . (int)$userRoot->rgt . ' ORDER BY a.lft ASC');
			$groups = $db->loadObjectList('id');
			foreach($groups as &$group){
				if(isset($groups[$group->parent_id])){
					$group->level = intval(@$groups[$group->parent_id]->level) + 1;
				} else {
					$group->level = 0;
				}
				$group->text = JText::_( $group->name );
			}
			unset($group);
			foreach($groups as &$group) {
				$this->groups[] = $group;
			}
		} else {
			$db->setQuery('SELECT a.*, a.title as text, a.id as value FROM `#__usergroups` AS a ORDER BY a.lft ASC');
			$groups = $db->loadObjectList('id');
			foreach($groups as &$group){
				if(isset($groups[$group->parent_id])){
					$group->level = intval(@$groups[$group->parent_id]->level) + 1;
				} else {
					$group->level = 0;
				}
			}
			unset($group);
			foreach($groups as &$group) {
				$this->groups[] = $group;
			}
		}
	}

	public function getList() {
		if(empty($this->groups)) {
			$this->load();
		}
		return $this->groups;
	}

	public function display($map, $values, $allBtn = false, $min = false) {
		hikaserial::loadJslib('otree');
		if(empty($this->groups)) {
			$this->load();
		}
		$map = str_replace('"','',$map);
		$id = str_replace(array('[',']',' '),array('_','','_'),$map);
		$cpt = count($this->groups)-1;

		$ret = '<div id="'.$id.'_otree" class="oTree"></div><input type="hidden" value="'.$values.'" name="'.$map.'" id="'.$id.'"/>
<script type="text/javascript">
var data_'.$id.' = ' . $this->getData($values, $allBtn, $min) . ';
'.$id.' = new window.oTree("'.$id.'",{rootImg:"'.HIKASHOP_IMAGES.'otree/", showLoading:false, useSelection:false, checkbox:true},null,data_'.$id.',true);
'.$id.'.callbackCheck = function(treeObj, id, value) {
	var node = treeObj.get(id), d = document, e = d.getElementById("'.$id.'");
	if(node.state == 5) {
		if(value === true) {
			treeObj.chks("*",false);
			e.value = "all";
		} else if(value === false) {
			treeObj.chks(false,false,true);
			e.value = "none";
		}
	} else {
		var v = treeObj.getChk();
		node = treeObj.get(0);
		if(v === false || v.length == 0) {
			e.value = "none";
			treeObj.chk(1,0,false,false);
		} else if( v.length > '.$cpt.') {
			e.value = "all";
			treeObj.chk(1,1,false,false);
		} else {
			e.value = v.join(",");
			treeObj.config.tricheckbox = true;
			treeObj.chk(1,2,false,false);
			treeObj.config.tricheckbox = false;
		}
	}
};
</script>';
		return $ret;
	}

	private function getData($values, $allBtn = false, $min = false) {
		$cpt = count($this->groups)-1;
		$sep = '';
		$ret = '[';
		$rootDepth = 0;
		$arrValues = explode(',', $values);

		if($allBtn) {
			$ret .= '{"status":5,"name":"'.JText::_('HIKA_ALL').'","icon":"folder","value":""';
			if($values == 'all')
				$ret .= ',"checked":true';
			$ret .= '}';
			$sep = ',';
		}

		foreach($this->groups as $k => $group) {
			$next = null;
			if($k < $cpt)
				$next = $this->groups[$k+1];

			$status = 4;
			if(!empty($next) && $next->level > $group->level)
				$status = 2;

			if($min == true && $k == 0)
				$status = 3;

			$ret .= $sep.'{"status":'.$status.',"name":"'.str_replace('"','&quot;',$group->text).'"';
			$ret .= ',"value":'.$group->id;

			if($values == 'all' || in_array($group->id, $arrValues)) {
				$ret .= ',"checked":true';
			}

			$sep = '';
			if(!empty($next)) {
				if($next->level > $group->level) {
					$ret .= ',"data":[';
				} else if($next->level < $group->level) {
					$ret .= '}'.str_repeat(']}', $group->level - $next->level);
					$sep = ',';
				} else {
					$ret .= '}';
					$sep = ',';
				}
			} else {
				$ret .= '}'.str_repeat(']}', $group->level - $rootDepth);
			}
		}
		$ret .= ']';
		return $ret;
	}
}
