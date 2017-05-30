<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikamarketQuantityType {

	public function display($map, $value) {
		$attribs = '';
		$label = '';
		$id = str_replace(array('][','[',']'),array('__','_',''), $map);
		if(HIKASHOP_RESPONSIVE) {
			$ret = '<div class="input-append">'.
				'<input type="text" name="'.$map.'" id="'.$id.'" value="'.$value.'" '.$attribs.'/>'.
				'<button class="btn" onclick="document.getElementById(\''.$id.'\').value=\''.JText::_('UNLIMITED').'\';return false;"><i class="icon-remove"></i></button>'.
				'</div>';
		} else {
			$ret = '<input type="text" name="'.$map.'" id="'.$id.'" value="'.$value.'" '.$attribs.'/>' .
				'<a class="marketInfinityButton" href="#" onclick="document.getElementById(\''.$id.'\').value=\''.JText::_('UNLIMITED').'\';return false;"><span>X</span></a>';
		}
		return $ret;
	}
}
