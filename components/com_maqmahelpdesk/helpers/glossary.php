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

class HelpdeskGlossary
{
	static function Popup($content)
	{
		$database = JFactory::getDBO();
		//$supportConfig = HelpdeskUtility::GetConfig();

		$find_exact = 1;
		//$show_once_only = 0;
		//$mosinfopop_entrytext = $content;
		$times = -1;

		// Get all terms from Glossary
		$query = "SELECT id, term, description, ucase(term) as uterm FROM #__support_glossary WHERE published='1'";
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		if (count($rows)) {
			foreach ($rows as $row) {
				$keyword = htmlentities(trim($row->term));
				if ($keyword) {
					$escaped_keyword = str_replace('/', '\\/', $keyword);
					if ($find_exact)
						$regex = '/(\b)' . $escaped_keyword . '(\b)/i';
					else
						$regex = '/' . $escaped_keyword . '/i';
					$key = JString::strtoupper($keyword);
					$definition = preg_replace("/(\015\012)|(\015)|(\012)/", '&nbsp;<br />', $row->description);
					$glossary[$row->id] = array('id' => $row->id, 'term' => $keyword, 'desc' => $definition, 'regex' => $regex, 'found' => false);
				}
			}
		}

		if (isset($glossary)) {
			if (count($glossary)) {
				$htmlregex = '#(<a .*?</a\ *>|<script .*?</script\ *>|</?.*?>|\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>)#i';
				$bits = preg_split($htmlregex, $content);

				preg_match_all($htmlregex, $content, $matches);
				$newcontent = "";
				$found = array();
				foreach ($bits as $i => $bit) {
					foreach ($glossary as $elm) {
						if (!($times == '1' && isset($found[$elm['id']]))) {
							$newpart = "{sc_glossary_replace " . $elm['id'] . "}";
							if (preg_match($elm['regex'], $bit)) {
								$found[$elm['id']] = $elm['term'];
							}
							$bit = preg_replace($elm['regex'], $newpart, $bit, $times);
						}
					}
					$newcontent .= $bit;
					if (isset($matches[0][$i])) $newcontent .= $matches[0][$i];
				}
				$content = $newcontent;

				$regex = '/{(sc_glossary_replace)\s*(.*?)}/i';
				$matches = array();
				preg_match_all($regex, $content, $matches, PREG_SET_ORDER);

				foreach ($matches as $elm) {
					$id = $elm[2];
					if (isset($glossary[$id])) {
						$output = '<span class="editlinktip hasTip" title="<b>' . htmlspecialchars($glossary[$id]['term'] . '</b>::<div style=\'width:200px;\'>' . $glossary[$id]['desc']) . '</div>"><u>' . $glossary[$id]['term'] . '</u></span>';
					} else {
						$output = "";
					}
					$content = preg_replace($regex, $output, $content, 1);
				}
			}
		}

		return $content;
	}
}
