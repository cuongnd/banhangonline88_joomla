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
class hikaauctionPopupHelper {

	function __construct() {
	}

	function display($text, $title, $url, $id, $params = array()) {
		$html = '';
		$popupMode = 'mootools';
		if(HIKAAUCTION_J30) {
			$config = hikaauction::config();
			$popupMode = $config->get('popup_mode', 'bootstrap');
			if(empty($popupMode) || $popupMode == 'inherit')
				$popupMode = 'bootstrap';
		}

		if(!isset($params['attr'])) $params['attr'] = '';
		if(!isset($params['icon'])) $params['icon'] = '';
		if(!isset($params['type'])) $params['type'] = 'button';
		if(!isset($params['dynamicUrl'])) $params['dynamicUrl'] = false;

		switch($popupMode) {
			case 'bootstrap':
				return $this->displayBootstrap($text, $title, $url, $id, $params);
			case 'mootools':
			default:
				return $this->displayMootools($text, $title, $url, $id, $params);
		}
		return $html;
	}

	function displayBootstrap($text, $title, $url, $id, $params = array()) {
		if(!isset($params['attr'])) $params['attr'] = '';
		if(!isset($params['icon'])) $params['icon'] = '';
		if(!isset($params['type'])) $params['type'] = 'button';
		if(!isset($params['dynamicUrl'])) $params['dynamicUrl'] = false;

		$isOnclick = (strpos($params['attr'], 'onclick="') !== false);

		if($text !== null) {
			$attr = $params['attr'];
			if($params['type'] == 'button')
				$attr = $this->getAttr($params['attr'],'btn btn-small');

			$onclick = '';
			if(!$isOnclick) {
				$fct_url = '\''.$url.'\'';
				if(!empty($id) && $params['type'] == 'button' && $params['dynamicUrl'])
					$fct_url = $url;
				if(empty($id) && $params['type'] != 'button')
					$fct_url = 'this.href';
				if((!empty($id) && $params['type'] != 'button') || (empty($id) && $params['type'] == 'button'))
					$fct_url = 'null';

				$onclick = ' onclick="window.hikaauction.openBox(this,'.$fct_url.',true); return false;"';
			}

			$href = '';
			if($params['type'] != 'button')
				$href = ' href="'. (empty($id) ? $url : '#') . '"';

			$el_id = '';
			if(!empty($id))
				$el_id = ' id="'.$id.'"';

			$html = (($params['type'] == 'button')?'<button ':'<a ') . $href . $attr . $el_id . $onclick . '>';

			if(!empty($params['icon']))
				$html .= '<i class="icon-16-'.$params['icon'].'"></i> ';

			$html .= $text . (($params['type'] == 'button')?'</button>':'</a>');
		} else {
			$html = '<a style="display:none;" href="#" id="'.$id.'" onclick="window.hikaauction.openBox(this,null,true); return false;"></a>';
		}

		$bootstrapParams = array(
			'title' => JText::_($title),
			'url' => $url,
			'height' => $params['height'],
			'width' => $params['width']
		);
		if($params['dynamicUrl']) {
			$bootstrapParams['url'] = '\'+'.$url.'+\'';
		}
		if(!empty($id)) {
			$footer = '';
			if(!empty($params['footer']) && $params['footer'] === true) {
				$footer = '<div class="modal-footer">'.
						'<button class="btn" type="button" data-dismiss="modal">'.JText::_('HKP_CANCEL').'</button>'.
						'<button class="btn btn-primary" type="submit" onclick="window.hikaauction.submitPopup(\''.$id.'\');">'.JText::_('HKP_VALIDATE').'</button>'.
					'</div>';

				$footer = str_replace(array("'","\r","\n"), array("\\'",'',''), $footer);
			}

			$renderModal = JHtml::_('bootstrap.renderModal', 'modal-'.$id, $bootstrapParams, $footer);
			$html .= str_replace(
				array(
					'id="modal-'.$id.'"',
					'<iframe'
				),
				array(
					'id="modal-'.$id.'" style="width:'.($params['width']+20).'px;height:'.($params['height']+90).'px;margin-left:-'.(($params['width']+20)/2).'px"',
					'<iframe id="modal-'.$id.'-iframe"'
				),
				$renderModal
			);
			$html .= '<script>'."\n".'jQuery(document).ready(function(){jQuery("#modal-'.$id.'").appendTo(jQuery(document.getElementById("hikaauction_main_content") || document.body));});'."\n".'</script>'."\n";
		}

		return $html;
	}

	function displayMootools($text, $title, $url, $id, $params) {
		if(!isset($params['attr'])) $params['attr'] = '';
		if(!isset($params['icon'])) $params['icon'] = '';
		if(!isset($params['type'])) $params['type'] = 'button';
		if(!isset($params['dynamicUrl'])) $params['dynamicUrl'] = false;

		$isOnclick = (strpos($params['attr'], 'onclick="') !== false);

		$html = '';
		JHtml::_('behavior.modal');
		if($text === null)
			return $html;

		$onClick = '';
		if($params['dynamicUrl']) {
			if(!$isOnclick)
				$onClick = ' onclick="this.href=' . str_replace('"', '\"', $url) . '; return window.hikaauction.openBox(this,this.href);"';
			$isOnclick = true;
			$url = '#';
		}

		$a = $params['attr'];
		if(!empty($id) && !$isOnclick && empty($params['footer']))
			$onClick = ' onclick="return window.hikaauction.openBox(this,null,false);"';

		if(!empty($params['footer']) && $params['footer'] === true) {
			static $createBoxInit = false;
			if(!$createBoxInit) {
				$doc = JFactory::getDocument();
				$js = '
if(!window.localPage) window.localPage = {};
window.localPage.createBox = function(el,href,options) {
	if(typeof options == "string")
		options = JSON.decode(options, false);
	var content = \'<div><div class="sbox-header">\'+options.title+\'</div>'.
	'<iframe id="modal-squeezebox-iframe" width="\'+options.size.x+\'" height="\'+options.size.y+\'" frameborder="0" src="\'+href+\'"></iframe>'.
	'<div class="sbox-footer">'.
	'<button onclick="window.hikaauction.closeBox();" class="btn" type="button">'.JText::_('HKP_CANCEL').'</button>'.
	'<button class="btn btn-primary" type="submit" onclick="window.hikaauction.submitPopup(\\\'squeezebox\\\');">'.JText::_('HKP_VALIDATE').'</button>'.
	'</div></div>\';
	options.size.x += 10;
	options.size.y += 65;
	var size = {x: options.size.x, y: options.size.y};
	options.handler = "string";
	options.content = content;
	SqueezeBox.initialize(options);
	SqueezeBox.setContent("string",content);
	SqueezeBox.resize(size, true);
	return false;
};
';
				$doc->addScriptDeclaration($js);
				$createBoxInit = true;
			}
			if($params['dynamicUrl']) {
				$onClick=' onclick="this.href=' . str_replace('"', '\"', $url) . '; return window.localPage.createBox(this,this.href,this.rel);"';
			} else {
				$onClick=' onclick="return window.localPage.createBox(this,this.href,this.rel);"';
			}
			$title = str_replace("'", "\\'", JText::_($title));
			$html = '<a '.$a.$onClick.' id="'.$id.'" href="'.$url.'" rel="{title:\''.$title.'\',size:{x:'.$params['width'].',y:'.$params['height'].'}}">';
		} else {
			$html = '<a '.$a.$onClick.' id="'.$id.'" href="'.$url.'" rel="{handler: \'iframe\', size: {x: '.$params['width'].', y: '.$params['height'].'}}">';
		}
		if($params['type'] == 'button')
			$html .= '<button class="btn" onclick="return false">';
		$html .= $text;
		if($params['type'] == 'button')
			$html .= '</button>';
		$html .= '</a>';

		return $html;
	}

	function image($content, $url, $id = null, $attr = '') {
		$html = '';
		$popupMode = 'mootools';

		switch($popupMode) {
			case 'shadowbox':
			case 'shadowbox-embbeded':
				return $this->imageShadowbox($content, $url, $id, $attr);
			case 'mootools':
			default:
				return $this->imageMootools($content, $url, $id, $attr);
		}
		return $html;
	}

	function imageMootools($content, $url, $id = null, $attr = '') {
		JHtml::_('behavior.modal');
		$html = '';
		if($content === null)
			return $html;

		$isOnclick = (strpos($attr, 'onclick="') !== false);
		$onClick = '';
		if(!$isOnclick)
			$onClick = ' onclick="SqueezeBox.fromElement(this,{parse:\'rel\'});return false;"';

		if(!empty($id))
			$id = ' id="'.$id.'"';
		else
			$id = '';

		$html = '<a '.$attr.$onClick.$id.' href="'.$url.'" rel="{handler:\'image\'}" target="_blank">'.$content.'</a>';
		return $html;
	}

	function imageShadowbox($content, $url, $id = null, $attr = '') {
		$html = '';
		if($content === null)
			return $html;

		static $init = false;
		if($init === false) {
			$config = hikashop_config();
			$shadowboxMode = $config->get('image_popup_mode', 'mootools');
			if($shadowboxMode != 'shadowbox-embbeded') {
				$doc = JFactory::getDocument();
				$doc->addStyleSheet('//www.hikashop.com/cdn/shadowbox/shadowbox.css');
				$doc->addScript('//www.hikashop.com/cdn/shadowbox/shadowbox.js');
				$doc->addScriptDeclaration("\r\n".'Shadowbox.init();'."\r\n");
			}
			$init = true;
		}

		$isRel = (strpos($attr, 'rel="') !== false);
		$rel = '';
		if(!$isRel)
			$rel = ' rel="shadowbox"';

		if(!empty($id))
			$id = ' id="'.$id.'"';
		else
			$id = '';

		$html = '<a '.$attr.$rel.$id.' href="'.$url.'">'.$content.'</a>';
		return $html;
	}

	function getAttr($attr, $class) {
		if(empty($attr)) {
			return 'class="'.$class.'"';
		}
		$attr = ' '.$attr;
		if(strpos($attr, ' class="') !== false) {
			$attr = str_replace(' class="', ' class="'.$class.' ', $attr);
		} elseif(strpos($attr, ' class=\'') !== false) {
			$attr = str_replace(' class=\'', ' class=\''.$class.' ', $attr);
		} else {
			$attr .= ' class="'.$class.'"';
		}
		return trim($attr);
	}
}
