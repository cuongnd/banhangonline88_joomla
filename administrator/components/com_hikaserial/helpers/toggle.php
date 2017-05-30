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
class hikaserialToggleHelper {

	private $ctrl = 'toggle';
	private $extra = '';
	private $token = '';

	public function __construct() {
		$this->token = '&'.hikaserial::getFormToken().'=1';
	}

	private function getToggle($column, $table = ''){
		$params = new stdClass();
		$params->mode = 'pictures';
		$params->values = array(
			0 => 1,
			1 => 0
		);
		if(!HIKASHOP_J16) {
			$params->pictures = array(
				0 => 'images/publish_x.png',
				1 => 'images/tick.png'
			);
		} elseif(!HIKASHOP_J30) {
			$params->mode = 'pictures';
			$params->aclass = array(
				0 => 'grid_false',
				1 => 'grid_true'
			);
		} else {
			$params->mode = 'class';
			$params->aclass = array(
				0 => 'icon-unpublish',
				1 => 'icon-publish'
			);
		}
		return $params;
	}

	public function toggle($id, $value, $table, $extra = null) {
		static $jsIncluded = false;

		$column = substr($id, 0, strpos($id, '-'));
		$params = $this->getToggle($column, $table);
		$newValue = $params->values[$value];

		if(!$jsIncluded && ($params->mode == 'pictures' || $params->mode == 'class')) {
			$jsIncluded = true;
			$js = 'function joomToggleElem(id,v,t,e){'."\r\n".
				'var w=window, d=document, o=w.Oby, el=d.getElementById(id);'."\r\n".
				'if(!el) return; el.className="onload";'.
				'var url="index.php?option='.HIKASERIAL_COMPONENT.'&tmpl=component&ctrl='.$this->ctrl.$this->token.'&task="+id+"&value="+v+"&table="+t;'."\r\n".
				'if(e){ url+="&extra[color]="+e; } '."\r\n".
				'o.xRequest(url,{update:el},function(x,p){el.className="loading";});'."\r\n".
				'}';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		if($params->mode == 'pictures') {
			$desc = empty($params->description[$value]) ? '' : $params->description[$value];
			if(empty($params->pictures)) {
				$text = ' ';
				$class='class="'.$params->aclass[$value].'"';
			} else {
				$text = '<img src="'.$params->pictures[$value].'"/>';
				$class = '';
			}
			return '<a href="javascript:void(0);" '.$class.' onclick="joomToggleElem(\''.$id.'\',\''.$newValue.'\',\''.$table.'\')" title="'.str_replace('"','&quot;',$desc).'">'.$text.'</a>';
		}

		if($params->mode == 'class') {
			$desc = empty($params->description[$value]) ? '' : $params->description[$value];
			$extrastyle = '';
			if(!empty($extra['color'])) {
				$extrastyle='background-color:'.$extra['color'].';';
			} else {
				$extra['color'] = '';
			}
			$return = '<a href="javascript:void(0);" onclick="joomToggleElem(\''.$id.'\',\''.$newValue.'\',\''.$table.'\',\''.urlencode($extra['color']).'\');" title="'.str_replace('"','&quot;',$desc).'"><div class="'. $params->aclass[$value] .'" style="'.$extrastyle.'">';
			if(!empty($extra['tooltip']))
				$return .= JHTML::_('tooltip', $extra['tooltip'], '','','&nbsp;&nbsp;&nbsp;&nbsp;');
			$return .= '</div></a>';
			return $return;
		}

		return '';
	}

	public function display($column, $value) {
		$params = $this->getToggle($column);
		if(empty($params->pictures)) {
			return '<a class="'.$params->aclass[$value].'" href="#" style="cursor:default;"> </a>';
		}
		return '<img src="'.$params->pictures[$value].'"/>';
	}

	public function delete($lineId, $elementids, $table, $confirm = false, $text = '') {
		static $jsIncluded = false;

		if(!$jsIncluded) {
			$jsIncluded = true;
			$js = 'function joomDeleteElem(id,v,t,r){'."\r\n".
					'var w=window, d=document, o=w.Oby, el=d.getElementById(id);'."\r\n".
					'if(r && !confirm("'.JText::_('HIKA_VALIDDELETEITEMS',true).'")) return false;'."\r\n".
					'var url="index.php?option='.HIKASERIAL_COMPONENT.'&tmpl=component&ctrl='.$this->ctrl.$this->extra.$this->token.'&task=delete&value="+v+"&table="+t;'."\r\n".
					'o.xRequest(url,null,function(x,p){el.style.display="none";});'."\r\n".
					'}';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		if(empty($text))
			$text = '<img src="'.HIKASERIAL_IMAGES.'icon-16/delete.png"/>';
		return '<a href="javascript:void(0);" onclick="joomDeleteElem(\''.$lineId.'\',\''.$elementids.'\',\''.$table.'\','. ($confirm ? 'true' : 'false').')">'.$text.'</a>';
	}
}
