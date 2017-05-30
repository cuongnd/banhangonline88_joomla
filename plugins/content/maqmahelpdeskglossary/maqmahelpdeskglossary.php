<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/glossary.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/utility.php');

JHTML::_('behavior.tooltip');

$option = JRequest::getVar('option', '', 'REQUEST', 'string');

class plgContentMaQmaHelpdeskGlossary extends JPlugin
{
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$row->text = HelpdeskGlossary::Popup($row->text);
		return $row;
	}

    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
    {
        $row->text = HelpdeskGlossary::Popup($row->text);
        return;
    }
}
