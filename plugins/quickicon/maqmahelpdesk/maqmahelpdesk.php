<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id$
 * $LastChangedDate$
 *
 */

defined('_JEXEC') or die ('Restricted access');

class plgQuickiconMaQmaHelpdesk extends JPlugin
{
	public function onGetIcons($context)
	{
        return array(array(
            'link' => 'index.php?option=com_maqmahelpdesk',
            'image' => JURI::root().'media/com_maqmahelpdesk/images/logo48px.png',
            'text' => 'MaQma Helpdesk',
            'id' => 'plg_quickicon_maqmahelpdesk'
        ));
	}
}
