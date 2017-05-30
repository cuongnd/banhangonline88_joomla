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

class JButtonPopsched extends JButton
{
	var $_name = 'Popsched';


	function fetchButton( $type='Popsched', $url = '', $width=640, $height=480 )
	{
		$text = JText::_('SCHEDULE',true);

		if(!ACYMAILING_J30) {
			JHTML::_('behavior.modal','a.modal');
			$html  = "<a id=\"a_schedule\" class=\"modal\" href=\"$url\" rel=\"{handler: 'iframe', size: {x: $width, y: $height}}\">\n";
			$html .= "<span class=\"icon-32-schedule\" title=\"$text\">\n</span>\n".$text."\n</a>\n";
			return $html;
		}

		$html = '<button class="btn btn-small modal" data-toggle="modal" data-target="#modal-'.$type.'"><i class="icon-14-schedule"></i> '.$text.'</button>';
		$params['title']  = $text;
		$params['url']    = $url;
		$params['height'] = $height;
		$params['width']  = $width;
		$html .= JHtml::_('bootstrap.renderModal', 'modal-'.$type, $params);
		$html .= '<script>'."\r\n" . 'jQuery(document).ready(function(){jQuery("#modal-'.$type.'").appendTo(jQuery(document.body));});'."\r\n".'</script>';
		return $html;
	}

	function fetchId( $type='Popsched', $html = '', $id = 'popshed' )
	{
		return 'toolbar-'.$id;
	}
}

class JToolbarButtonPopsched extends JButtonPopsched {}
