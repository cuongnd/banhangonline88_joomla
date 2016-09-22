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

class MaQmaToolBarHelper extends JToolbarHelper
{
	static public function Preview($url = '', $updateEditors = false)
	{
		$bar = JToolBar::getInstance('toolbar');
		$html = '<a href="' . $url . '" target="_blank"><span class="icon-32-print_f2"></span>' . JText::_('print') . '</a>';
		$bar->appendButton('Custom', $html, 'print_f2');
	}

	static public function Popup($url = '', $class = '', $label, $updateEditors = false)
	{
		$bar = JToolBar::getInstance('toolbar');
		$html = '<a href="' . $url . '" target="_blank"><span class="icon-32-' . $class . '"></span>' . $label . '</a>';
		$bar->appendButton('Custom', $html, $class);
	}

	static public function newLink($url = '', $class = '', $label, $updateEditors = false)
	{
		$bar = JToolBar::getInstance('toolbar');
		$html = '<a href="' . $url . '"><span class="icon-32-' . $class . '"></span>' . $label . '</a>';
		$bar->appendButton('Custom', $html, $class);
	}

	static public function exportPdf($url = '')
	{
		$bar = JToolBar::getInstance('toolbar');
		$html = '<a href="' . $url . '" target="_blank"><span class="icon-32-pdf_link"></span>' . JText::_('pdf_export') . '</a>';
		$bar->appendButton('Custom', $html, 'pdf_link');
	}

	static public function exportCsv($url = '')
	{
		$bar = JToolBar::getInstance('toolbar');
		$html = '<a href="' . $url . '" target="_blank"><span class="icon-32-save_f2"></span>CSV</a>';
		$bar->appendButton('Custom', $html, 'save_f2');
	}
}
