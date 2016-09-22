<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Class to handle page and popup headers and footers
 * 
 * Also responisble for adding css files, javascript and other similar things
 * 
 **/
jimport( 'fsj_core.lib.utils.template');
jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");

class FSJ_Page
{
	static $styles_incl = false;
	static $styles_sub_incl = array();
	static $footer = array();
	
	static function StylesAndJS($css = array(), $js = array())
	{
		// THIS NEEDS ALTERING SO SITE AND ADMIN HAVE DIFFERENT DEFAULTS
		
		// force css and js to array incase we add stuff to em
		if (!is_array($css))
			$css = array($css);
		if (!is_array($js))
			$js = array($js);

		// add support for modal inclusion here too!
		$document = JFactory::getDocument();
		
		if (!self::$styles_incl)
		{
			// bootstrap is included with Joomal 3.x, so just use that version
			// jQuery is included in Joomla 3.x so just include it
			if (FSJ_Helper::IsJ3()) 
			{
				JHtml::_('jquery.framework');
			} else {
				self::jQuery_j25();
			}
			
			self::Bootstrap();
	
			self::Script('libraries/fsj_core/assets/js/jquery/jquery.fsj_tooltip.js');
			
			if (JFactory::getApplication()->isAdmin()) // if we are admin pages, add some extra stuff
			{
				self::Style('libraries/fsj_core/assets/css/fsj/fsj.admin.less');
				
				if (!FSJ_Helper::IsJ3()) // If we are admin for Joomla 2.5, add the style fixes for the bootstrap styles
				{
					self::Style('libraries/fsj_core/assets/css/fsj/fsj.admin.j25.less');
					self::Script('libraries/fsj_core/assets/js/form/form.admin.j25.js');
				}
				
				self::Script('libraries/fsj_core/assets/js/fsj/fsj.admin.js');
			}
			
			// add core javascript files for things like popups etc
			self::Script('libraries/fsj_core/assets/js/fsj/fsj.core.js');
			
			// get settings etc for current compoennt
			
			if (!JFactory::getApplication()->isAdmin())
			{
				$comp_css = FSJ_Settings::Get('comp_css', 'overrides');
				$core_css = FSJ_Settings::Get('core_css', 'overrides');
				
				if ($comp_css) $document->addStyleDeclaration($comp_css);
				if ($core_css) $document->addStyleDeclaration($core_css);

				$core_js = FSJ_Settings::Get('core_css', 'scripts');

				if ($core_js) $document->addScriptDeclaration($core_js);
		}
			
			self::$styles_incl = true;
		}
			
		self::Style($css);
		self::Script($js);
		
		$js = "\nvar fsj_base_url = '" . JURI::root() . "';\n";
		$document->addScriptDeclaration($js);


		if (!JFactory::getApplication()->isAdmin() && FSJ_Settings::Get('core_pageinclude', 'artisteerfixes'))
		{
			self::Style('libraries/fsj_core/assets/css/misc/artisteer.less');
			self::Script('libraries/fsj_core/assets/js/misc/artisteer.js');
		}

	}
	
	static $jquery_incl = false;
	static function jQuery_j25($force = false)
	{		
		if (self::$jquery_incl)
			return;
		
		self::$jquery_incl = true;
		
		$document = JFactory::getDocument();
		
		//$include = FSS_Settings::get('jquery_include');
		//if ($include == "")
			$include = "auto";
			
		$url = 'libraries/fsj_core/assets/js/jquery/jquery-1.11.0.min.js';
		$cpurl = 'libraries/fsj_core/assets/js/jquery/jquery-migrate-1.2.1.min.js';
		$ncurl = 'libraries/fsj_core/assets/js/jquery/jquery.noconflict.js';

		if ($force)
			$include = "yes";

		if ($include == "yes")
		{
			self::Script( $url );
			self::Script( $cpurl );
			self::Script( $ncurl );
			
		} else if ($include == "yesnonc") // yes, include it, but not with noconflict
		{
			self::Script( $url );
			self::Script( $cpurl );
		} else // auto detect mode
		{
			$found = false;
			
			foreach ($document->_scripts as $jsurl => $script)
			{
				if (strpos(strtolower($jsurl), "jquery") > 0)
				{
					$found = true;
					break;
				}
			}
			
			if (!$found)
			{
				self::Script( $url );
				self::Script( $cpurl );
				self::Script( $ncurl );
			}
		}
	}
	
	static function JQueryUI($addons = array())
	{
		/*if (FSJ_Helper::IsJ3()) 
		{
			JHtml::_('jquery.ui', $addons);
		} else {*/
			FSJ_Page::Script('libraries/fsj_core/assets/js/jquery/jquery.ui.core.js');
			if (in_array("sortable", $addons))
				FSJ_Page::Script('libraries/fsj_core/assets/js/jquery/jquery.ui.sortable.js');
		//}
	}
	
	static function Bootstrap()
	{
		if (FSJ_Helper::IsJ3()) 
		{
			JHtml::_('bootstrap.framework');
		} else {
			self::Script('libraries/fsj_core/assets/js/bootstrap/bootstrap.min.js');
		}
		
		if (JFactory::getApplication()->isAdmin())
		{
			if (FSJ_Helper::IsJ3())
			{
				$option = "partial";
			} else {
				$option = "fsj";
			}
		} else {
			$option = FSJ_Settings::Get('core_pageinclude', 'inc_bootstrap');
		}

		if ($option == "fsj")
		{
			self::Style('libraries/fsj_core/assets/css/bootstrap/bootstrap_fsjonly.less');
		} else if ($option == "partial")
		{		
			self::Style('libraries/fsj_core/assets/css/bootstrap/bootstrap_missing.parsed.less');
		} else if ($option == "yes")
		{
			self::Style('libraries/fsj_core/assets/css/bootstrap/bootstrap.less');
		} else
		{
			self::Style('libraries/fsj_core/assets/css/bootstrap/freestyle_only.less');
		}
		
		if (!FSJ_Helper::IsJ3() && !JFactory::getApplication()->isAdmin())
		{
			self::Style('libraries/fsj_core/assets/css/fsj/fsj.site.j25.less');
		}
	}
		
	static function Style($css)
	{
		if (!is_array($css))
			$css = array($css);
		
		$document = JFactory::getDocument();
		
		foreach ($css as $c)
		{
			if (array_key_exists($c, self::$styles_sub_incl))
				continue;
			
			$file = $c;
				
			$jpc = JPATH_CACHE;
			$jpc = str_ireplace("administrator", "", $jpc);
				
			if (!file_exists($jpc.DS.'fsj'.DS.'css'))
				JFolder::create($jpc.DS.'fsj'.DS.'css');
				
			$in_file = JPATH_ROOT.DS.$file;

			if (!is_file($in_file)) continue;
			
			$out_filename = str_replace(".less","",str_replace("/","_",str_replace("\\","_",$file)));
			$out_filename = str_replace(".", "_", $out_filename).".css";
		
			if (JFactory::getDocument()->direction == "rtl")
				$out_filename = "rtl_" . $out_filename;
				
			$out_file = $jpc.DS.'fsj'.DS.'css'.DS.$out_filename;
				
			if (!is_file($out_file) || filemtime($in_file) > filemtime($out_file)) {
				jimport('fsj_core.third.less.lessc');
				
				$extension = strtolower(pathinfo($c, PATHINFO_EXTENSION));
				if ($extension == "less")
				{
					$less = new fsj_lessc;
					$output = $less->compileFile($in_file);
				} else {
					$output = file_get_contents($in_file);	
				}
				if (JFactory::getDocument()->direction == "rtl")
					$output .= self::rtlCSS($output);
			
				JFile::write($out_file, $output);
			}

			$document = JFactory::getDocument();
					
			// need to check if we can use the cache folder.
			// if not we need to use alternate url
				
			if (!FSJ_Settings::$base_item) FSJ_Settings::LoadBaseSettings("com_fsj_main");
					
			if (FSJ_Settings::get('css_settings', 'cache_bypass'))
			{
				$document->addStyleSheet(JRoute::_("index.php?option=com_fsj_main&view=css&file=" . $out_filename, false));
			} else {
				$document->addStyleSheet(JURI::root() . "cache/fsj/css/" . $out_filename);	
			}

			
			self::$styles_sub_incl[$c] = 1;		
		}		
	}
		
	static function Script($js)
	{
		if (!is_array($js))
			$js = array($js);
		
		$document = JFactory::getDocument();
		
		foreach ($js as $j)
		{
			$j = (string)$j;
			if (array_key_exists($j, self::$styles_sub_incl))
				continue;
			
			if (substr($j, 0, 2) != "//")
			{
				$document->addScript(JURI::root(true)."/".$j); 
			} else {
				$document->addScript($j); 
			}
			
			self::$styles_sub_incl[$j] = 1;
		}	
	}
	
	static function ScriptDec($js)
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);
	}
	
	static function StyleDec($css)
	{
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);
	}
	
	static function StyleParsed($ident, $css, $updated)
	{
		$jpc = JPATH_CACHE;
		$jpc = str_ireplace("administrator", "", $jpc);

		if (!file_exists($jpc.DS.'fsj'.DS.'css'))
			mkdir($jpc.DS.'fsj'.DS.'css',0777,true);
		
		$out_filename = "$ident.css";
		$out_file = $jpc.DS.'fsj'.DS.'css'.DS.$out_filename;
		
		if (!is_file($out_file) || $updated > filemtime($out_file)) {
			jimport('fsj_core.third.less.lessc');
			$less = new fsj_lessc;
			$output = $less->compile($css);
			JFile::write($out_file, $output);
		}

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . "cache/fsj/css/" . $out_filename);
	}
	
	static $modal_done;
	static function IncludeModal()
	{
		if (empty(self::$modal_done))
		{
			require_once(JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'tmpl'.DS.'misc'.DS.'modal.php');
			self::$modal_done = true;
		}
	}

	static function Popup_End($buttons = null)
	{
		$html[] = '</div>';
		$html[] = '<div class="modal-footer fsj">';
		if ($buttons)
			$html[] = $buttons;
		else
			$html[] = '<a href="#" class="btn close_popup" data-dismiss="modal">' . JText::_('FSJ_MODAL_CLOSE') .'</a>';
		$html[] = '</div>';
		
		return implode($html);
	}

	static function Popup_Begin($title,$subtitle = "")
	{
		
		self::Script('libraries/fsj_core/assets/js/form/form.iframe.popup.js');
		
		$html[] = '<div class="modal-header fsj">';
		$html[] = '<button class="close" data-dismiss="modal">&times;</button>';
		$html[] = '<h3>' . JText::_($title);
		if ($subtitle)
			$html[] = " - " . JText::_($subtitle);
		$html[] = '</h3>';
		$html[] = '</div>';
		$html[] = '<div class="modal-body fsj">';
		
		return implode($html);
	}
	
	static $page_template;
	static $template_id;
	
	static function Page_Start($title = "", $subtitle = "", $item = null)
	{
		// start a page. This should use all info available from current menu item to
		// do things like meta data etc. Needs to be able to be passed an item so things
		// like metadata can be overridden	
		
		if (FSJ_Settings::$component == "")
			FSJ_Settings::LoadBaseSettings("com_fsj_main");
	
		// ensure that the menu item settings are loaded
		FSJ_Settings::AddMenuItemSettings();
		
		// page title
		$pageparams = JFactory::getApplication()->getPageParameters(JRequest::getVar('option'));

		$pageheading = $pageparams->get('page_title', '');
		$menutitle = $pageparams->get('page_heading', $pageheading);

		self::_page_browser_title($title, $subtitle, $pageheading);
		
		$page_title = self::_title_string($title, $subtitle, $menutitle, false);
		
		$page_class = $pageparams->get('pageclass_sfx', '');
		if ($page_class)
		{
			$page_class = "item-page-" . $page_class;
		} else {
			$page_class = "item-page";
		}
		
		self::$template_id = FSJ_Settings::Get("core_template","template") ;
		
		self::$page_template = new FSJ_Template();		
		self::$page_template->load_template("main", "page", self::$template_id);
		self::$page_template->OutputCSS("main", "page", self::$template_id);
		
		self::$page_template->assign("page_title", $page_title);
		self::$page_template->assign("title", $title);
		self::$page_template->assign("subtitle", $subtitle);
		self::$page_template->assign("menutitle", $menutitle);
		self::$page_template->assign("page_class", $page_class);
		
		$show_title = true;
		
		if (FSJ_Settings::Get("core_title","title_format") == 99)
			$show_title = false;
	
		if ($page_title == "")
			$show_title = false;
		
		if (FSJ_Settings::Get("core_title","use_joomla") && ! $pageparams->get('show_page_heading',1))
			$show_title = false;
		
		self::$page_template->assign("show_title", $show_title);
		
		
		$result = array();
		
		$result[] = "<div class='" . self::_page_classes() . "'>\n";
		$result[] = self::$page_template->fetch("fsjtpl:main.page.".self::$template_id.".page_header");
		
		return implode($result);
	}
	
	static function Page_End()
	{
		$result = array();
		
		$result[] = self::$page_template->fetch("fsjtpl:main.page.".self::$template_id.".page_footer");
		$result[] = implode("\n", self::$footer);
		$result[] = "</div>";
		
		return implode($result);
	}
	
	static function Page_SubTitle($subtitle)
	{
		$result = array();
		
		$result[] = self::$page_template->assign("sub_title", $subtitle);
		$result[] = self::$page_template->fetch("fsjtpl:main.page.".self::$template_id.".page_subtitle");
		
		return implode($result);
	}
	
	static function _page_browser_title($title, $subtitle, $heading)
	{
		$title_browser = self::_title_string($title, $subtitle, $heading, true);
		
		if (JFactory::getApplication()->getCfg('sitename_pagetitles', 0) == 1)
			$title_browser = JText::sprintf('JPAGETITLE', JFactory::getApplication()->getCfg('sitename'), $title_browser);

		if (JFactory::getApplication()->getCfg('sitename_pagetitles', 0) == 2)
			$title_browser = JText::sprintf('JPAGETITLE', $title_browser, JFactory::getApplication()->getCfg('sitename'));
		
		JFactory::getDocument()->setTitle($title_browser);
	}
	
	static function _page_classes()
	{
		$classes = array();
		$classes[] = "fsj";
		
		if (FSJ_Helper::IsJ3())
			$classes[] = "fsj_j3";
		
		$option = JRequest::getVar('option');
		if ($option)
			$classes[] = "fsj_" . $option;
		
		$view = JRequest::getVar('view');
		if ($view)
			$classes[] = "fsj_view_" . $view;
		
		$layout = JRequest::getVar('layout');
		if ($layout)
			$classes[] = "fsj_layout_" . $layout;
		
		$itemid = JRequest::getVar('Itemid');
		if ($itemid)
			$classes[] = "fsj_itemid_" . $itemid;
		
		return implode(" ", $classes);
	}
	
	static function _title_string($title,$subtitle,$menutitle,$isbrowser)
	{
		if ($isbrowser)
		{
			$setting = FSJ_Settings::Get("core_title","browser_title_format");		
			if ($setting == -1 || $setting == 100)
				$setting = FSJ_Settings::Get("core_title","title_format");		
		} else {
			$setting = FSJ_Settings::Get("core_title","title_format");		
		}
		
		$texts = array();
		
		switch ($setting)
		{
			case 0: // Title or Subtitle
				if ($subtitle)
					$texts[] = $subtitle;
				else
					$texts[] = $title;
				break;
			
			case 1:	// Title - Subtitle
				if ($subtitle)
				{
					$texts[] = $title;
					$texts[] = $subtitle;
				} else {
					$texts[] = $title;
				}
				break;
			
			case 2: // Title
				$texts[] = $title;
				break;
			
			case 3: // Menu Title
				$texts[] = $menutitle;
				break;
			
			case 4: // Menu Title - Title or Subtitle
				if ($subtitle)
				{
					$texts[] = $menutitle;
					$texts[] = $subtitle;
				} else {
					$texts[] = $menutitle;
					$texts[] = $title;
				}
				break;
			
			case 5: // Menu Title - Title - Subtitle
				if ($subtitle)
				{
					$texts[] = $menutitle;
					$texts[] = $title;
					$texts[] = $subtitle;
				} else {
					$texts[] = $menutitle;
					$texts[] = $title;
				}
				break;
			
			case 6:
				$texts[] = $menutitle;
				$texts[] = $title;
				break;	
			
			case 99:
				break;		
		}
		
		$actual = array();
		
		$lang = JFactory::getLanguage();
		
		foreach ($texts as $text)
		{
			if (trim($text == "")) continue;
			if ($lang->hasKey($text))
			{
				$actual[] = JText::_($text);
			} else {
				$actual[] = $text;
			}
		}
		
		if (count($actual) == 1)
			return $actual[0];
		
		if (count($actual) == 2)
			return JText::sprintf("FSJ_PAGE_HEAD_TWIN", $actual[0], $actual[1]);
		
		if (count($actual) == 3)
			return JText::sprintf("FSJ_PAGE_HEAD_TRIPLE", $actual[0], $actual[1], $actual[2]);
		
		return "";
	}
	
	static function Footer($code)
	{
		self::$footer[] = $code;	
	}
	
	static function AllowCache()
	{
		if (FSJ_Helper::IsJ3())
		{
			JFactory::getApplication()->allowCache(true);
		} else {
			JResponse::allowCache(true);
		}		
	}
	
	static function Powered_By()
	{
		FSJ_Settings::GetBaseItem();
		
		if (!FSJ_Settings::Get('general', 'hide_powered'))
		{
			if (JRequest::getInt('print') > 0)
			{
?>
<div align="center" style="text-align:center;padding-top:20px;padding-bottom:20px;">
	<div>Powered by <?php echo FSJ_Settings::$powered_com; ?></div>
	<div><?php echo FSJ_Settings::$powered_link; ?></div>
	<div><img style="padding-top:2px;" border="0" src="<?php echo JURI::root( true ); ?>/libraries/fsj_core/assets/images/logo_small.png"></div>
</div>
<?php
			} else {
?>
<div align="center" style="text-align:center;padding-top:20px;padding-bottom:20px;">
	<div><a href="<?php echo FSJ_Settings::$powered_link; ?>">Powered by <?php echo FSJ_Settings::$powered_com; ?></a></div>
	<div><a href="<?php echo FSJ_Settings::$powered_link; ?>"><img style="padding-top:2px;" border="0" src="<?php echo JURI::root( true ); ?>/libraries/fsj_core/assets/images/logo_small.png"></a></div>
</div>
<?php
			}
		}
	}
			
	static function errorBox($title, $content)
	{
		return "<div class='alert alert-danger'><h4>$title</h4>$content</div>";	
	}
	
	static function Chosen()
	{
		if (!FSJ_Helper::IsJ3())
		{
			// add chosen to the current doc
			FSJ_Page::Script('libraries/fsj_core/assets/js/chosen/chosen.jquery.js');
			FSJ_Page::Style('libraries/fsj_core/assets/css/chosen/chosen.css');
		}
	}
	
	static $sefBuffer = '';
	protected static function sefParseCheck(&$buffer)
	{
		if ($buffer === null)
		{
			// no buffer returned from regexp, so use the last good one we had
			$buffer = self::$sefBuffer;
		} else {
			// buffer was ok, store it for later
			self::$sefBuffer = $buffer;
		}
	}
	
	protected static function sefParseRoute(&$matches)
	{
		$url   = $matches[1];
		$url   = str_replace('&amp;', '&', $url);
		$route = JRoute::_('index.php?' . $url);

		return 'href="' . $route;
	}
	
	static function sefParse($buffer)
	{
		self::$sefBuffer = $buffer;
		
		$app = JFactory::getApplication();

		if ($app->getName() != 'site' || $app->getCfg('sef') == '0')
		{
			return $buffer;
		}

		// Replace src links.
		$base   = JUri::base(true) . '/';

		$regex  = '#href="index.php\?([^"]*)#m';
		$buffer = preg_replace_callback($regex, array('FSJ_Page', 'sefParseRoute'), $buffer);
		self::sefParseCheck($buffer);

		// Check for all unknown protocals (a protocol must contain at least one alpahnumeric character followed by a ":").
		$protocols = '[a-zA-Z0-9]+:';
		$regex     = '#(src|href|poster)="(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
		$buffer    = preg_replace($regex, "$1=\"$base\$2\"", $buffer);
		self::sefParseCheck($buffer);

		$regex  = '#(onclick="window.open\(\')(?!/|' . $protocols . '|\#)([^/]+[^\']*?\')#m';
		$buffer = preg_replace($regex, '$1' . $base . '$2', $buffer);
		self::sefParseCheck($buffer);

		// ONMOUSEOVER / ONMOUSEOUT
		$regex  = '#(onmouseover|onmouseout)="this.src=([\']+)(?!/|' . $protocols . '|\#|\')([^"]+)"#m';
		$buffer = preg_replace($regex, '$1="this.src=$2' . $base . '$3$4"', $buffer);
		self::sefParseCheck($buffer);

		// Background image.
		$regex  = '#style\s*=\s*[\'\"](.*):\s*url\s*\([\'\"]?(?!/|' . $protocols . '|\#)([^\)\'\"]+)[\'\"]?\)#m';
		$buffer = preg_replace($regex, 'style="$1: url(\'' . $base . '$2$3\')', $buffer);
		self::sefParseCheck($buffer);

		// OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag.
		$regex  = '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
		$buffer = preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $buffer);
		self::sefParseCheck($buffer);

		// OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag.
		$regex  = '#(<param\s+[^>]*)value\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
		$buffer = preg_replace($regex, '<param value="' . $base . '$2" name="$3"', $buffer);
		self::sefParseCheck($buffer);

		// OBJECT data="xx" attribute -- fix it only in the object tag.
		$regex  = '#(<object\s+[^>]*)data\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
		$buffer = preg_replace($regex, '$1data="' . $base . '$2"$3', $buffer);
		self::sefParseCheck($buffer);

		return $buffer;
	}
	
	static function rtlCSS($css_data, $escaped=array('.no-convert')) {
		
		$dir='RTL';
		
		//$css_data = file_get_contents($css_file);
		//remove comments 
		$css_data = preg_replace('/\/\*(.*)?\*\//Usi','' ,$css_data);
		//rewrite padding,margin,border
		$css_data = preg_replace('/(\h*)(padding|margin|border):(\d+.+)\h+(\d+.+)\h+(\d+.+)\h+(\d+.+)\h*;/Ui',"\\1\\2-right:\\4;\\1\\2-left:\\5;" ,$css_data);
		//rewrite border-radius 
		$css_data = preg_replace('/(\h*|)border-radius:(.+)\h+(.+)\h+(.+)\h+(.+)\h*;/Ui',"\\1border-top-left-radius:\\2;\\1border-top-".
			"right-radius:\\3;\\1border-bottom-right-radius:\\4;\\1border-bottom-left-radius:\\5;", $css_data);
		//start parsing css file
		$css_data = preg_replace('/(@media .+){(.+)}\s*}/Uis', '\1$$$\2}$$$', $css_data);
		preg_match_all('/(.+){(.+)(}\$\$\$|})/Uis', $css_data, $css_arr);
		$css_flipped    = "/* Created by flipcss.php 0.7 by daif alotaibi (http://daif.net) */\n\n";
		foreach($css_arr[0] as $key=>$val) {
			//ignore escaped classes
			if(!preg_match('/('.implode('|', array_map('preg_quote', $escaped)).')/i', $css_arr[1][$key])) {
				if(preg_match('/left|right/i', $css_arr[2][$key])) {
					if($rules = self::rtlCSSRule($css_arr[2][$key])) {
						$css_flipped .= trim(str_replace('$$$','{',$css_arr[1][$key]));
						$css_flipped .= " {\n\t".trim($rules)."\n";
						$css_flipped .= str_replace('$$$',"\n}",$css_arr[3][$key])."\n\n";
					}
				}
			}
		}
		
		return $css_flipped;
	}
	
	static function rtlCSSRule($rules) {
		$return         = '';
		$rules_arr      = explode(";", $rules);
		foreach($rules_arr as $rule) {
			//ignore rules that doesn't need flipping
			if(preg_match('/(left|right)/i', $rule)) {
				//flip float
				if(preg_match('/float\h*:\h*(.+)/i', $rule, $rule_arr)) {
					$rule = 'float: '.((trim($rule_arr[1])=='left')?'right':'left');
					$return .="\t".trim($rule).";\n";
					
					//flip text-align
				} elseif(preg_match('/text-align\h*:\h*(.+)/i', $rule, $rule_arr)) {
					$rule = 'text-align: '.((trim($rule_arr[1])=='left')?'right':'left');
					$return .="\t".trim($rule).";\n";
					
					//flip padding, margin, border
				} elseif(preg_match('/(\*|)(margin|padding|border)-(left|right)\h*:\h*(.+)/i', $rule, $rule_arr)) {
					$dir = ((trim($rule_arr[3])=='left')?'right':'left');
					//reset direction rule
					if((trim($rule_arr[3]) == 'left' && !preg_match('/'.trim($rule_arr[2]).'\-right/i', $rules)) || (trim($rule_arr[2]) == 'right' && !preg_match('/'.trim($rule_arr[2]).'\-left/i', $rules))) {
						$rule = trim($rule_arr[1]).trim($rule_arr[2]).'-'.$rule_arr[3].": 0;\n\t";
					} else {
						$rule = '';
					}
					$rule .= trim($rule_arr[1]).trim($rule_arr[2]).'-'.$dir.': '.$rule_arr[4];
					$return .="\t".trim($rule).";\n";
					
					//flip border-radius
				} elseif(preg_match('/border-(top|bottom)-(left|right)-radius\h*:\h*(.+)/i', $rule, $rule_arr)) {
					$dir = ((trim($rule_arr[2])=='left')?'right':'left');
					//reset direction rule
					if((trim($rule_arr[2]) == 'left' && !preg_match('/'.trim($rule_arr[1]).'\-right/i', $rules)) || (trim($rule_arr[2]) == 'right' && !preg_match('/'.trim($rule_arr[1]).'\-left/i', $rules))) {
						$rule = 'border-'.$rule_arr[1].'-'.$rule_arr[2].'-radius: 0;'."\n\t";
					} else {
						$rule = '';
					}
					//write new direction rule
					$rule .= 'border-'.$rule_arr[1].'-'.$dir.'-radius: '.$rule_arr[3];
					$return .="\t".trim($rule).";\n";
					
					//flip left, right
				} elseif(preg_match('/\h+(left|right)\h*:\h*(.+)/i', $rule, $rule_arr)) {
					$dir = ((trim($rule_arr[1])=='left')?'right':'left');
					//reset LTR rule
					if((trim($rule_arr[1]) == 'left' && !preg_match('/\h+right\h*:/i', $rules)) || (trim($rule_arr[1]) == 'right' && !preg_match('/\h+left\h*:/i', $rules))) {
						$rule = trim($rule_arr[1]).": auto;\n\t";
					} else {
						$rule = '';
					}
					$rule .= $dir.': '.$rule_arr[2];
					$return .="\t".trim($rule).";\n";
				}
			}
		}
		return($return);
	}	
	
}