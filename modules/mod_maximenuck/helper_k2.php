<?php
/**
 * @copyright	Copyright (C) 2013 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class modMaximenuckk2Helper {

	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 */
    static function getItems(&$params) {
        jimport('joomla.application.module.helper');
		$input = new JInput();
        
		$usek2suffix = $params->get('usek2suffix', '0');
		$k2imagesuffix = $params->get('k2imagesuffix', '_mini');
		$usek2images = $params->get('usek2images', '0');
		$k2categoryroot = $params->get('k2categoryroot', '0');
		$k2categorydepth = $params->get('k2categorydepth', '0');
		// $start = $params->get('startLevel', '1');
        // $end = $params->get('endLevel', '10');
        // $dependantitems = $params->get('dependantitems', '0');
		$k2showall = $params->get('k2showall', '1');
		$active_path = array();

        $db = JFactory::getDBO();
		$query = "SELECT *,"
				." id,"
				." 1 as level,"
				." parent,"
				." ordering"
				." FROM #__k2_categories"
				." WHERE published = 1"
				." ORDER BY parent DESC, ordering ASC";
		
        $db->setQuery($query);

        if ($db->query()) {
            $rows = $db->loadObjectList('id');
        } else {
            echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the k2 categories in Maximenu CK</p>';
            return false;
        }

        // reverse rows to put level class
        $temprows = array_reverse($rows);
        foreach ($temprows as $row) {
            if ($row->parent != 0) {
                $row->level = $rows[$row->parent]->level + 1;
            }
        }

		$active_category_id = $input->get('id', '0', 'int');

        $level = 0;
        $items = array();
        $i = 0;
		$k2categoryrootitem = new stdClass();
		$k2categoryrootitem->level = 0;
		$k2categoryrootitem->enfants = '';

        foreach ($rows as $k => &$item) {
		
			if ($item->id == $k2categoryroot) 
				$k2categoryrootitem = $item;

            // saves childs into parents items
            if ($item->level > 1) {
                $rows[$item->parent]->haschild = 'yes';
				if (! isset($rows[$item->parent]->enfants)) $rows[$item->parent]->enfants = '';
                if (isset($item->haschild)) {
                    $rows[$item->parent]->enfants.=$item->id . '|' . $item->enfants;
                } else {
                    $rows[$item->parent]->enfants.=$item->id . '|';
                }

                // add parent-child classes
                if (isset($active_category_id) && $active_category_id == $item->id) {
					$active_path[] = $item->id;
                    $j = $item->level;

                    $tempitemID = $item->parent;

                    while ($j != 1) {
                        $rows[$tempitemID]->classe .= " active";
						$active_path[] = $tempitemID ;
                        $tempitemID = $rows[$tempitemID]->parent;

                        $j--;
                    }
                }
                // if (isset($item->haschild) AND $params->get('layout', 'default') != '_:flatlist') {
                    // $item->classe .= " parent";
                // }
            }
            // create childs after respective parent
            if ($item->level == 1) { //gestion des droits des parents niveau 0
                $items[$i] = $item;
                if (isset($active_category_id) && $active_category_id == $item->id) {
                    //$item->classe .= " current active";
					$active_path[] = $item->id;
                }
				$item->path = array();
				$item->path[] = $item->id;
                if (isset($item->haschild)) {
                    // if ($params->get('layout', 'default') != '_:flatlist') 
						// $item->classe .= " parent";
                    $childs = explode("|", $item->enfants);
                    foreach ($childs as $c) {
                        if ($c) {
                            $i++;
							$item->path[] = $rows[$c]->id;
							$rows[$c]->path = $item->path;                           
                            $items[$i] = $rows[$c];
                        }
                    }
                }
            } else {
                $i--;
            }
            $i++;
        }

		$k2categoryrootitem->enfants = explode("|", $k2categoryrootitem->enfants);
		$tmpitems = array();
		$j = 0;
		$lastitem = 0;

		foreach ($items as $i => &$item) {
			// check if the item is in the active tree
			if (!$k2showall AND !in_array($item->parent, $active_path) AND ($item->level - $k2categoryrootitem->level > 1)) {
				unset($items[$i]);
				continue;
			}

			// check if the item is in the path for the selected root category
				if ( $k2categoryroot != 0
					AND (
						($item->id == $k2categoryroot)
						OR (!in_array($item->id, $k2categoryrootitem->enfants))
						) 
					) {
					unset($items[$i]);
					continue;
				}

			// check the depth
			if ($k2categorydepth AND (($item->level - $k2categoryrootitem->level) > $k2categorydepth) OR ($item->level <= $k2categoryrootitem->level) ) {
				unset($items[$i]);
				continue;
			}
			
			$tmpitems[$j] = $item;
			$j++;
		}

		$items = $tmpitems;

		foreach ($items as $i => &$item) {

			$item->params = new JRegistry();
			$item->flink = JRoute::_('index.php?option=com_k2&view=itemlist&layout=category&task=category&id=' . $item->id );
			$item->deeper = false;
			$item->shallower = false;
			$item->level_diff = 0;
			$item->level =  $item->level - $k2categoryrootitem->level;

			if (isset($items[$i-1])) {
				$items[$i-1]->deeper = ($item->level > $items[$i-1]->level);
				$items[$i-1]->shallower = ($item->level < $items[$i-1]->level);
				$items[$i-1]->level_diff = ($items[$i-1]->level - $item->level);
				if ($items[$i-1]->deeper AND $params->get('layout', 'default') != '_:flatlist') 
					$items[$i-1]->classe .= " parent";
			}

			// test if it is the last item
			$item->is_end = !isset($items[$i + 1]);

			// add some classes
			$item->classe = " item" . $item->id;
			if (isset($active_category_id) && $active_category_id == $item->id) {
				$item->classe .= " current active";
			}

            // search for parameters
			$patterns = "#{maximenu}(.*){/maximenu}#Uis";
			$result = preg_match($patterns, stripslashes($item->description), $results);

			$item->desc = '';
			$item->colwidth = '';
			$item->tagcoltitle = 'none';
			$item->tagclass = '';
			$item->leftmargin = '';
			$item->topmargin = '';
			$item->submenuwidth = '';

			if (isset($results[1])) {
				$k2params = explode('|', $results[1]);
				// $parmsnumb = count($k2params);
				for ($j = 0; $j < count($k2params); $j++) {
					$item->desc = stristr($k2params[$j], "desc=") ? str_replace('desc=', '', $k2params[$j]) : $item->desc;
					$item->colwidth = stristr($k2params[$j], "col=") ? str_replace('col=', '', $k2params[$j]) : $item->colwidth;
					$item->tagcoltitle = stristr($k2params[$j], "taghtml=") ? str_replace('taghtml=', '', $k2params[$j]) : $item->tagcoltitle;
					$item->tagclass = stristr($k2params[$j], "tagclass=") ? ' '.str_replace('tagclass=', '', $k2params[$j]) : $item->tagclass;
					$item->leftmargin = stristr($k2params[$j], "leftmargin=") ? str_replace('leftmargin=', '', $k2params[$j]) : $item->leftmargin;
					$item->topmargin = stristr($k2params[$j], "topmargin=") ? str_replace('topmargin=', '', $k2params[$j]) : $item->topmargin;
					$item->submenucontainerwidth = stristr($k2params[$j], "submenuwidth=") ? str_replace('submenuwidth=', '', $k2params[$j]) : $item->submenuwidth;
					$item->createnewrow = stristr($k2params[$j], "newrow") ? 1 : 0;
				}
			}

			$item->classe .= $item->tagclass;
			// variables definition
			$item->ftitle = stripslashes(htmlspecialchars($item->name));
			$item->content = "";
			$item->rel = "";

            // manage images
            if (!$usek2suffix) $k2imagesuffix = '';
            $item->menu_image = '';
            if ($usek2images) {
				$imageurl = $item->image ? explode(".",$item->image): '';
				$imagename = isset($imageurl[0]) ? $imageurl[0] : '';
				$imageext = isset($imageurl[1]) ? $imageurl[1] : '';
                if (JFile::exists(JPATH_ROOT . '/media/k2/categories/' . $imagename . $k2imagesuffix . '.' . $imageext)) {
					$item->menu_image = 'media/k2/categories/' . $imagename . $k2imagesuffix . '.' . $imageext;
				}
            }
			

			// manage columns
            if ($item->colwidth) {
				$item->colonne = true;
				$parentItem = self::getParentItem($item->parent, $items);

				if (isset($parentItem->submenuswidth)) {
					$parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
				} else {
					$parentItem->submenuswidth = strval($item->colwidth);
				}
				if (isset($items[$i-1]) AND $items[$i-1]->deeper) {
					$items[$i-1]->nextcolumnwidth = $item->colwidth;
				}
				$item->columnwidth = $item->colwidth;
			}
			if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth) 
				$parentItem->submenuswidth = $parentItem->submenucontainerwidth;

			$item->name = $item->ftitle;

			// pour compat avec default.php
			$item->anchor_css = '';
			$item->anchor_title = '';
			$item->type = '';
			
			// get plugin parameters that are used directly in the layout
			$item->liclass = $item->params->get('maximenu_liclass', '');
			$item->colbgcolor = $item->params->get('maximenu_colbgcolor', '');
		}

		// give the correct deep infos for the last item
		if (isset($items[$i])) {
			$items[$i]->level_diff	= ($items[$i]->level - 1);
		}

		return $items;
	}

	function getParentItem($id, $items) {
		foreach ($items as $item) {
			if ($item->id == $id)
				return $item;
		}
	}
}