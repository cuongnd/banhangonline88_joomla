<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikaauctionToggleHelper {

	private $ctrl = 'toggle';
	private $extra = '';
	private $token = '';

	public function __construct() {
		$this->token = '&'.hikaauction::getFormToken().'=1';
	}

	private function getToggle($column, $table = ''){
		$params = new stdClass();
		$params->mode = 'pictures';
		$params->values = array(
			0 => 1,
			1 => 0
		);
		$params->aclass = array(
			0 => 'unpublish',
			1 => 'publish'
		);
		return $params;
	}

	public function toggle($id, $value, $table, $extra = null, $ajaxCall = false) {
		static $jsIncluded = false;

		$column = substr($id, 0, strpos($id, '-'));
		$params = $this->getToggle($column, $table);
		$newValue = $params->values[$value];

		if(!$jsIncluded && ($params->mode == 'pictures' || $params->mode == 'class')) {
			$jsIncluded = true;
			$js = 'function hikabookingToggleElem(el,id,v,t,e){'."\r\n".
				'var w=window, d=document, o=w.Oby, el=el.parentNode;'."\r\n".
				'if(!el) return; el.className="toggle_onload";'.
				'var url="'.hikaauction::completeLink($this->ctrl.'&task={TASK-}&value={VALUE-}&table={TABLE-}'.$this->token,true,true).'";'."\r\n".
				'url = url.replace("{TASK-}",id).replace("{VALUE-}",v).replace("{TABLE-}",t);'.
				'o.xRequest(url,{update:el},function(x,p){el.className="toggle_loading";});'."\r\n".
				'}';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		$desc = empty($params->description[$value]) ? '' : $params->description[$value];
		$return = '';
		if(!$ajaxCall)
			$return .= '<div class="toggle_loading">';
		$return .= '<a href="javascript:void(0);" class="'.$params->aclass[$value].'" onclick="hikabookingToggleElem(this,\''.$id.'\',\''.$newValue.'\',\''.$table.'\')" title="'.str_replace('"','&quot;',$desc).'"></a>';
		if(!$ajaxCall)
			$return .= '</div>';
		return $return;

	}

	public function display($column, $value) {
		$params = $this->getToggle($column);
		return '<span class="toggle_display '.$params->aclass[$value].'"></span>';
	}

	public function delete($lineId, $elementids, $table, $confirm = false, $text = '') {
		static $jsIncluded = false;

		if(!$jsIncluded) {
			$jsIncluded = true;
			$js = 'function hikabookingDeleteElem(el,id,v,t,r){'."\r\n".
				'var w=window, d=document, o=w.Oby, el=el.parentNode;'."\r\n".
				'if(r && !confirm("'.JText::_('TOGGLE_VALIDDELETEITEMS',true).'")) return false;'."\r\n".
				'var url="'.hikaauction::completeLink($this->ctrl.$this->extra.$this->token.'&task=delete&value={VALUE-}&table={TABLE-}', true).'";'.
				'url=url.replace("{VALUE-}",v).replace("{TABLE-}",t);'."\r\n".
				'o.xRequest(url,null,function(x,p){var e = d.getElementById(id); if(e) e.style.display="none"; else el.style.display="none";});'."\r\n".
				'}';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		if(empty($text))
			$text = '<img src="'.HIKAAUCTION_IMAGES.'icon-16/delete.png"/>';
		return '<a href="javascript:void(0);" onclick="hikabookingDeleteElem(this,\''.$lineId.'\',\''.$elementids.'\',\''.$table.'\','. ($confirm ? 'true' : 'false').')">'.$text.'</a>';
	}
}
