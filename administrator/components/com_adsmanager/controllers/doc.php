<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class AdsmanagerControllerDoc extends TController 
{
	public function execute($task) {
		$rootfolder = JPATH_ROOT.'/administrator/components/com_adsmanager/doc/';
		$folderstmp = JFolder::listFolderTree($rootfolder,".*",50);
		//var_dump(JFolder::files($rootfolder,".*\.html"));exit();
		$folders = array();
		$files = array();
		foreach($folderstmp as $f) {
			if ($f['name'] == "images")
				continue;
			if (!isset($folders[$f['parent']])) {
				$folders[$f['parent']]= array();
			}
			$files[$f['id']] = JFolder::files($f['fullname'],".*\.html");
			foreach($files[$f['id']] as $key => $file) {
				$ff = array();
				$ff['name'] = $file;
				$ff['filepath'] = $f['fullname']."/".$file;
				$files[$f['id']][$key] = $ff;
			}
			$folders[$f['parent']][] = $f;
			
		}
		$files[0] = JFolder::files($rootfolder,".*\.html");
		//echo "<ul>";
		$this->urlbuilder(0,0,$files,$folders,"",$links,false);
		?>
		<div class="row-fluid">
			<div class="span3">
			<?php 
			$this->displayTOC($links);
			?>
			</div>
			<div class="span9">
			<?php 
			$page = JRequest::getVar('page','');
			$found = false;
			$currentlevel = null;
			$mode = null;
			$pagelinks = array();
			foreach($links as $key => $link) {
				if ($found == false) {
					if ($link['page'] == $page) {
						$found = true;
						//var_dump($link);
						if ($link['type'] == 'file') {
							echo $this->renderFile($link,1);
							break;
						} else if ($link['type'] == 'folder') {
							$mode = 'list';
						} else {
							$mode = 'flat';
						}
						$currentlevel = $link['level'];
						$pagelinks[] = $link;
					} else {
						continue;
					}	
				} else {
					if ($link['level'] > $currentlevel ) {
						$pagelinks[] = $link;
					} else {
						break;
					}
				}
			}
			if ($mode == 'list') {
				$this->displayTOC($pagelinks);
			} else if ($mode == 'flat') {
				$currentlevel = $pagelinks[0]['level'];
				foreach($pagelinks as $link) {
					if ($link['type'] == 'file') {
						echo $this->renderFile($link,$link['level']-$currentlevel+1);
					} else {
						echo $this->renderFolder($link,$link['level']-$currentlevel+1);
					}
				}
			}
			?>
			</div>
		</div>
		<?php 
	}
	
	public function renderFile($link,$level) {
		$content = JFile::read($link['filepath'])."<br>";
		if (strpos($content,"h1") !== false) {	
			$content = str_replace(array("<h1>","</h1>"),array("<h$level id='".$link['id']."'>","</h$level>"),$content);
		} else {
			echo "<h$level id='".$link['id']."'>".$link['name']."</h1><br>";
		}
		$dir = dirname($link['filepath']);
		$url = substr($dir,strpos($dir,'/administrator')+1);
		$content = str_replace("images/",JURI::root().$url.'/images/',$content);
		echo $content;
	}
	
	public function renderFolder($link,$level) {
		echo "<h$level id='".$link['id']."'>".$link['name']."</h1><br>";
	}
	
	public function urlbuilder($parent,$level,$files,$folders,$currentpath="",&$links,$hash=false) {
		if ($links == null)
			$links = array();
		$nodes = array();
		if (isset($files[$parent])) {
			foreach($files[$parent] as $f) {
				$f['type'] = 'file';
				$nodes[$f['name']] = $f;
				
			}
		}
		if (isset($folders[$parent])) {
			foreach($folders[$parent] as $f) {
				$f['type'] = 'folder';
				$nodes[$f['name']] = $f;
			}
		}
		ksort($nodes);
		foreach($nodes as $n) {
			if ($n['type'] == 'folder') {
				$name = substr($n['name'],strpos($n['name'],"_")+1);
				if (strpos($name,"_final") !== false) {
					$name = str_replace("_final","",$name);
					$nexthash = true;
				} else {
					$nexthash = false;
				}
				if ($hash == true)
					$nexthash = true;
				$label = str_replace("_"," ",$name);
				
				
				$link = array();
				
				if ($nexthash == true) {
					$link['type'] = "flatfolder";
				} else {
					$link['type'] = "folder";
				}
				
				$link['id'] = $name;
				
				if ($hash == false) {
					$nextpath = $currentpath."/".$name;
					$link['url'] = JRoute::_('index.php?option=com_adsmanager&c=doc&page='.$nextpath);
					$link['page'] = $nextpath;
				} else {
					$nextpath = $currentpath;
					$link['url'] = JRoute::_('index.php?option=com_adsmanager&c=doc&page='.$currentpath."#".$name);
					$link['page'] = $currentpath."#".$name;
					
				}
				$link['name'] = $label;
				$link['level'] = $level;
				$links[] = $link;
				echo $this->urlbuilder($n['id'],$level+1,$files,$folders,$nextpath,$links,$nexthash);
			} else {
				$name = str_replace(".html","",$n['name']);
				$name = substr($name,strpos($name,"_")+1);
				$label = str_replace("_"," ",$name);
				$link = array();
				$link['id'] = $name;
				
				if ($hash == false) {
					$link['url'] = JRoute::_('index.php?option=com_adsmanager&c=doc&page='.$currentpath."/".$name);
					$link['page'] = $currentpath."/".$name;
				} else {
					$link['url'] = JRoute::_('index.php?option=com_adsmanager&c=doc&page='.$currentpath."#".$name);
					$link['page'] = $currentpath."#".$name;
					
				}
				$link['type'] = "file";
				$link['filepath'] = $n['filepath'];
				$link['name'] = $label;
				$link['level'] = $level;
				$links[] = $link;
				//$link = JRoute::_('index.php?option=com_adsmanager&c=doc&page=XXXX');
			}
		}
	}
			
	
	public function displayTOC($links) {
		$currentlevel = 0;
		echo "<ul>";
		foreach($links as $link) {
			if ($link['level'] > $currentlevel) {
				echo "<ul>";
				$currentlevel = $link['level'];
			}
			else if ($link['level'] < $currentlevel) 
			{
				for($i=$currentlevel;$i>$link['level'];$i--) {
					echo "</ul>";
				}
				$currentlevel = $link['level'];
			}
			echo "<li><a href='".$link['url']."'>".$link['name']."</a></li>";
		}
		for($i=$currentlevel;$i>=0;$i--) {
			echo "</ul>";
		}
	}
}