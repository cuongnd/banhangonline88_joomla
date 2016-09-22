<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.pagebreak
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.utilities.utility');

class FSJ_Pagebreak
{
	var $shown_page;
	
	public function parsePagebreaks($text, $page = 0, $title = '')
	{
		// need to return the text we want to display, and the current page no (or all)
		$row = new stdClass();
		$row->text = $text;
		$row->toc = '';
		$row->id = mt_rand(1,10000);
		if ($title)
			$row->title = $title;

		if (!$this->__parsePages($row, $page))
			return $text;
		
		return $row->toc . $row->text;
	}
	
	private function __parsePages(&$row, $page = 0)
	{
		$style = FSJ_Settings::Get('core_pagebreak', 'style');

		// Expression to search for.
		$regex = '#<hr(.*)class="system-pagebreak"(.*)(\/)*>#iU';

		$input = JFactory::getApplication()->input;

		$print = $input->getBool('print');
		$showall = $input->getBool('showall');
		$view = $input->getString('view');
		$full = $input->getBool('fullview');

		if (!$page)
			$page = 0;

		if ($print)
		{
			$row->text = preg_replace($regex, '<br />', $row->text);
			return true;
		}

		// Simple performance check to determine whether bot should process further.
		if (JString::strpos($row->text, 'class="system-pagebreak') === false)
		{
			return true;
		}


		// Find all instances of plugin and put in $matches.
		$matches = array();
		preg_match_all($regex, $row->text, $matches, PREG_SET_ORDER);

		if (($showall && FSJ_Settings::Get('core_pagebreak', 'showall')))
		{
			$hasToc = FSJ_Settings::Get('core_pagebreak', 'multipage_toc');

			if ($hasToc)
			{
				// Display TOC.
				$page = 1;
				$this->_createToc($row, $matches, $page);
			}
			else
			{
				$row->toc = '';
			}

			$row->text = preg_replace($regex, '<br />', $row->text);

			return true;
		}

		// Split the text around the plugin.
		$text = preg_split($regex, $row->text);

		// Count the number of pages.
		$n = count($text);

		// We have found at least one plugin, therefore at least 2 pages.
		if ($n > 1)
		{
			$hasToc = FSJ_Settings::Get('core_pagebreak', 'multipage_toc');

			// Reset the text, we already hold it in the $text array.
			$row->text = '';

			if ($style == 'pages')
			{
				// Display TOC.
				if ($hasToc)
				{
					$this->_createToc($row, $matches, $page);
				}
				else
				{
					$row->toc = '';
				}

				// Traditional mos page navigation
				$pageNav = new JPagination($n, $page, 1);


				// Page counter.
				$row->text .= '<div class="pagenavcounter">';
				$row->text .= $pageNav->getPagesCounter();
				$row->text .= '</div>';


				// Page text.
				$text[$page] = str_replace('<hr id="system-readmore" />', '', $text[$page]);
				$row->text .= $text[$page];


				// Adds navigation between pages to bottom of text.
				$row->text .= '<div class="pager">';
				if ($hasToc)
				{
					$this->_createNavigation($row, $page, $n);
				} else {
					$row->text .= $pageNav->getPagesLinks();
				}
				$row->text .= '</div>';
			}
			else
			{
				$t[] = $text[0];

				$t[] = (string) JHtml::_($style . '.start', 'article' . $row->id . '-' . $style);

				foreach ($text as $key => $subtext)
				{
					if ($key >= 1)
					{
						$match = $matches[$key - 1];
						$match = (array) JUtility::parseAttributes($match[0]);

						if (isset($match['alt']))
						{
							$title	= stripslashes($match['alt']);
						}
						elseif (isset($match['title']))
						{
							$title	= stripslashes($match['title']);
						}
						else
						{
							$title	= JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $key + 1);
						}

						$t[] = (string) JHtml::_($style . '.panel', $title, 'article' . $row->id . '-' . $style . $key);
					}

					$t[] = (string) $subtext;
				}

				$t[] = (string) JHtml::_($style . '.end');

				$row->text = implode(' ', $t);
			}
		}

		return true;
	}

	protected function _createTOC(&$row, &$matches, &$page)
	{
		$heading = isset($row->title) ? $row->title : JText::_('FSJ_PAGEBREAK_NO_TITLE');
		$input = JFactory::getApplication()->input;
		$limitstart = $input->getUInt('start', 0);
		$showall = $input->getInt('showall', 0);

		// TOC header.
		$row->toc = '<div class="pull-right article-index">';

		if (FSJ_Settings::Get('core_pagebreak', 'article_index'))
		{
			$headingtext = JText::_('FSJ_PAGEBREAK_ARTICLE_INDEX');

			if (FSJ_Settings::Get('core_pagebreak', 'article_index_text'))
			{
				$headingtext = htmlspecialchars(FSJ_Settings::Get('core_pagebreak', 'article_index_text'));
			}

			$row->toc .= '<h3>' . $headingtext . '</h3>';
		}

		// TOC first Page link.
		$class = ($limitstart === 0 && $showall === 0) ? 'toclink active' : 'toclink';
		$row->toc .= '<ul class="nav nav-tabs nav-stacked">
		<li class="' . $class . '">

			<a href="' . JRoute::_($this->baselink) . '" class="' . $class . '">'
			. $heading .
			'</a>

		</li>
		';

		$i = 2;

		foreach ($matches as $bot)
		{
			$link = JRoute::_($this->baselink . '&limitstart=' . ($i - 1));

			if (@$bot[0])
			{
				$attrs2 = JUtility::parseAttributes($bot[0]);

				if (@$attrs2['alt'])
				{
					$title	= stripslashes($attrs2['alt']);
				}
				elseif (@$attrs2['title'])
				{
					$title	= stripslashes($attrs2['title']);
				}
				else
				{
					$title	= JText::sprintf('FSJ_PAGEBREAK_PAGE_NUM', $i);
				}
			}
			else
			{
				$title	= JText::sprintf('FSJ_PAGEBREAK_PAGE_NUM', $i);
			}

			$class = ($limitstart == $i - 1) ? 'toclink active' : 'toclink';
			$row->toc .= '
				<li class="' . $class . '">

					<a href="' . $link . '" class="' . $class . '">'
				. $title .
				'</a>
				</li>
				';
			$i++;
		}

		if (FSJ_Settings::Get('core_pagebreak', 'showall'))
		{
			$link = JRoute::_($this->baselink . '&showall=1');
			$class = ($showall == 1) ? 'toclink active' : 'toclink';
			$row->toc .= '
			<li class="' . $class . '">
					<a href="' . $link . '" class="' . $class . '">'
				. JText::_('FSJ_PAGEBREAK_ALL_PAGES') .
				'</a>

			</li>
			';
		}

		$row->toc .= '</ul></div>';
	}

	protected function _createNavigation(&$row, $page, $n)
	{
		
		$pnSpace = '';

		if (JText::_('JGLOBAL_LT') || JText::_('JGLOBAL_LT'))
			$pnSpace = ' ';

		if ($page < $n - 1)
		{
			$page_next = $page + 1;
			$link_next = JRoute::_($this->baselink . '&limitstart=' . ($page_next));
			$next = '<a href="' . $link_next . '">' . JText::_('JNEXT') . $pnSpace . JText::_('JGLOBAL_GT') . JText::_('JGLOBAL_GT') . '</a>';
		} else {
			$next = JText::_('JNEXT');
		}

		if ($page > 0)
		{
			$page_prev = $page - 1 == 0 ? '' : $page - 1;
			$link_prev = JRoute::_($this->baselink . '&limitstart=' . ($page_prev));
			$prev = '<a href="' . $link_prev . '">' . JText::_('JGLOBAL_LT') . JText::_('JGLOBAL_LT') . $pnSpace . JText::_('JPREV') . '</a>';
		} else {
			$prev = JText::_('JPREV');
		}
		
		$row->text .= '<ul><li>' . $prev . ' </li><li>' . $next . '</li></ul>';
	}
}
